<?php
/*
** Zabbix
** Copyright (C) 2001-2020 Zabbix SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**/


/**
 * Main class containing methods for operations with dashboards.
 */
abstract class CDashboardGeneral extends CApiService {

	protected const MAX_X = 23; // DASHBOARD_MAX_COLUMNS - 1;
	protected const MAX_Y = 62; // DASHBOARD_MAX_ROWS - 2;

	protected $tableName = 'dashboard';
	protected $tableAlias = 'd';
	protected $sortColumns = ['dashboardid', 'name'];

	/**
	 * @param array $options
	 *
	 * @throws APIException if the input is invalid.
	 *
	 * @return array|int
	 */
	abstract public function get(array $options = []);

	/**
	 * @param array $dashboards
	 *
	 * @return array
	 */
	public function create(array $dashboards): array {
		$this->validateCreate($dashboards);

		$ins_dashboards = [];

		foreach ($dashboards as $dashboard) {
			unset($dashboard['users'], $dashboard['userGroups'], $dashboard['widgets']);
			$ins_dashboards[] = $dashboard;
		}

		$dashboardids = DB::insert('dashboard', $ins_dashboards);

		foreach ($dashboards as $index => &$dashboard) {
			$dashboard['dashboardid'] = $dashboardids[$index];
		}
		unset($dashboard);

		if ($this instanceof CDashboard) {
			$this->updateDashboardUser($dashboards, __FUNCTION__);
			$this->updateDashboardUsrgrp($dashboards, __FUNCTION__);
		}

		$this->updateWidget($dashboards, __FUNCTION__);

		$this->addAuditBulk(AUDIT_ACTION_ADD, static::AUDIT_RESOURCE, $dashboards);

		return ['dashboardids' => $dashboardids];
	}

	/**
	 * @param array $dashboards
	 *
	 * @throws APIException if the input is invalid
	 */
	abstract protected function validateCreate(array &$dashboards): void;

	/**
	 * @param array $dashboards
	 *
	 * @return array
	 */
	public function update(array $dashboards): array {
		$this->validateUpdate($dashboards, $db_dashboards);

		$upd_dashboards = [];

		foreach ($dashboards as $dashboard) {
			$db_dashboard = $db_dashboards[$dashboard['dashboardid']];

			$upd_dashboard = [];

			if (array_key_exists('name', $dashboard) && $dashboard['name'] !== $db_dashboard['name']) {
				$upd_dashboard['name'] = $dashboard['name'];
			}

			if ($this instanceof CDashboard) {
				if (array_key_exists('userid', $dashboard) && bccomp($dashboard['userid'], $db_dashboard['userid']) != 0) {
					$upd_dashboard['userid'] = $dashboard['userid'];
				}

				if (array_key_exists('private', $dashboard) && $dashboard['private'] != $db_dashboard['private']) {
					$upd_dashboard['private'] = $dashboard['private'];
				}
			}

			if ($this instanceof CTemplateDashboard) {
				if (array_key_exists('templateid', $dashboard)
						&& bccomp($dashboard['templateid'], $db_dashboard['templateid']) != 0) {
					$upd_dashboard['templateid'] = $dashboard['templateid'];
				}
			}

			if ($upd_dashboard) {
				$upd_dashboards[] = [
					'values' => $upd_dashboard,
					'where' => ['dashboardid' => $dashboard['dashboardid']]
				];
			}
		}

		if ($upd_dashboards) {
			DB::update('dashboard', $upd_dashboards);
		}

		if ($this instanceof CDashboard) {
			$this->updateDashboardUser($dashboards, __FUNCTION__);
			$this->updateDashboardUsrgrp($dashboards, __FUNCTION__);
		}

		$this->updateWidget($dashboards, __FUNCTION__, $db_dashboards);

		foreach ($db_dashboards as &$db_dashboard) {
			unset($db_dashboard['widgets']);
		}
		unset($db_dashboard);

		$this->addAuditBulk(AUDIT_ACTION_UPDATE, static::AUDIT_RESOURCE, $dashboards, $db_dashboards);

		return ['dashboardids' => array_column($dashboards, 'dashboardid')];
	}

	/**
	 * @param array      $dashboards
	 * @param array|null $db_dashboards
	 *
	 * @throws APIException if the input is invalid
	 */
	abstract protected function validateUpdate(array &$dashboards, ?array &$db_dashboards = null): void;

	/**
	 * Check for duplicated dashboards.
	 *
	 * @param array  $names
	 *
	 * @throws APIException  if dashboard already exists.
	 */
	abstract protected function checkDuplicates(array $names): void;

	/**
	 * Check duplicates widgets in one cell.
	 *
	 * @param array  $dashboards
	 * @param string $dashboards[]['name']
	 * @param array  $dashboards[]['widgets']              (optional)
	 * @param int    $dashboards[]['widgets'][]['x']
	 * @param int    $dashboards[]['widgets'][]['y']
	 * @param int    $dashboards[]['widgets'][]['width']
	 * @param int    $dashboards[]['widgets'][]['height']
	 *
	 * @throws APIException if input is invalid.
	 */
	protected function checkWidgets(array $dashboards): void {
		foreach ($dashboards as $dashboard) {
			if (array_key_exists('widgets', $dashboard)) {
				$filled = [];

				foreach ($dashboard['widgets'] as $widget) {
					for ($x = $widget['x']; $x < $widget['x'] + $widget['width']; $x++) {
						for ($y = $widget['y']; $y < $widget['y'] + $widget['height']; $y++) {
							if (array_key_exists($x, $filled) && array_key_exists($y, $filled[$x])) {
								self::exception(ZBX_API_ERROR_PARAMETERS,
									_s('Dashboard "%1$s" cell X - %2$s Y - %3$s is already taken.',
										$dashboard['name'], $widget['x'], $widget['y']
									)
								);
							}

							$filled[$x][$y] = true;
						}
					}

					if ($widget['x'] + $widget['width'] > DASHBOARD_MAX_COLUMNS
							|| $widget['y'] + $widget['height'] > DASHBOARD_MAX_ROWS) {
						self::exception(ZBX_API_ERROR_PARAMETERS,
							_s('Dashboard "%1$s" widget in cell X - %2$s Y - %3$s is out of bounds.',
								$dashboard['name'], $widget['x'], $widget['y']
							)
						);
					}
				}
			}
		}
	}

	/**
	 * Returns widget field name by field type.
	 *
	 * @return array
	 */
	abstract protected static function getFieldNamesByType(): array;

	/**
	 * Check widget fields.
	 *
	 * @param array  $dashboards
	 * @param string $dashboards[]['name']
	 * @param array  $dashboards[]['widgets']
	 * @param string $dashboards[]['widgets'][]['widgetid']  (optional)
	 * @param array  $dashboards[]['widgets'][]['fields']
	 * @param int    $dashboards[]['widgets'][]['type']
	 * @param mixed  $dashboards[]['widgets'][]['value']
	 * @param string $method
	 * @param array  $types
	 *
	 * @throws APIException if input is invalid.
	 */
	protected function checkWidgetFields(array $dashboards, string $method, array $types): void {
		$widget_fields = [];

		if ($method === 'validateUpdate') {
			$widgetids = [];

			foreach ($dashboards as $dashboard) {
				if (array_key_exists('widgets', $dashboard)) {
					foreach ($dashboard['widgets'] as $widget) {
						if (array_key_exists('widgetid', $widget)) {
							$widgetids[] = $widget['widgetid'];
						}
					}
				}
			}

			if ($widgetids) {
				$db_widget_fields = DB::select('widget_field', [
					'output' => ['widgetid', 'type', 'value_groupid', 'value_hostid', 'value_itemid', 'value_graphid',
						'value_sysmapid'
					],
					'filter' => [
						'widgetid' => $widgetids,
						'type' => [ZBX_WIDGET_FIELD_TYPE_GROUP, ZBX_WIDGET_FIELD_TYPE_HOST, ZBX_WIDGET_FIELD_TYPE_ITEM,
							ZBX_WIDGET_FIELD_TYPE_ITEM_PROTOTYPE, ZBX_WIDGET_FIELD_TYPE_GRAPH,
							ZBX_WIDGET_FIELD_TYPE_GRAPH_PROTOTYPE, ZBX_WIDGET_FIELD_TYPE_MAP
						]
					]
				]);

				$field_names_by_type = static::getFieldNamesByType();

				foreach ($db_widget_fields as $db_widget_field) {
					$widgetid = $db_widget_field['widgetid'];
					$type = $db_widget_field['type'];
					$value = $db_widget_field[$field_names_by_type[$db_widget_field['type']]];

					$widget_fields[$widgetid][$type][$value] = true;
				}
			}
		}

		$ids = [];
		foreach ($types as $type) {
			$ids[$type] = [];
		}

		foreach ($dashboards as $dashboard) {
			if (array_key_exists('widgets', $dashboard)) {
				foreach ($dashboard['widgets'] as $widget) {
					$widgetid = array_key_exists('widgetid', $widget) ? $widget['widgetid'] : 0;

					if (array_key_exists('fields', $widget)) {
						foreach ($widget['fields'] as $field) {
							if ($widgetid == 0 || !array_key_exists($widgetid, $widget_fields)
									|| !array_key_exists($field['type'], $widget_fields[$widgetid])
									|| !array_key_exists($field['value'], $widget_fields[$widgetid][$field['type']])) {
								$ids[$field['type']][$field['value']] = true;
							}
						}
					}
				}
			}
		}

		if (array_key_exists(ZBX_WIDGET_FIELD_TYPE_GROUP, $ids) && $ids[ZBX_WIDGET_FIELD_TYPE_GROUP]) {
			$groupids = array_keys($ids[ZBX_WIDGET_FIELD_TYPE_GROUP]);

			$db_groups = API::HostGroup()->get([
				'output' => [],
				'groupids' => $groupids,
				'preservekeys' => true
			]);

			foreach ($groupids as $groupid) {
				if (!array_key_exists($groupid, $db_groups)) {
					self::exception(ZBX_API_ERROR_PARAMETERS,
						_s('Host group with ID "%1$s" is not available.', $groupid)
					);
				}
			}
		}

		if (array_key_exists(ZBX_WIDGET_FIELD_TYPE_HOST, $ids) && $ids[ZBX_WIDGET_FIELD_TYPE_HOST]) {
			$hostids = array_keys($ids[ZBX_WIDGET_FIELD_TYPE_HOST]);

			$db_hosts = API::Host()->get([
				'output' => [],
				'hostids' => $hostids,
				'preservekeys' => true
			]);

			foreach ($hostids as $hostid) {
				if (!array_key_exists($hostid, $db_hosts)) {
					self::exception(ZBX_API_ERROR_PARAMETERS, _s('Host with ID "%1$s" is not available.', $hostid));
				}
			}
		}

		if (array_key_exists(ZBX_WIDGET_FIELD_TYPE_ITEM, $ids) && $ids[ZBX_WIDGET_FIELD_TYPE_ITEM]) {
			$itemids = array_keys($ids[ZBX_WIDGET_FIELD_TYPE_ITEM]);

			$db_items = API::Item()->get([
				'output' => [],
				'itemids' => $itemids,
				'webitems' => true,
				'preservekeys' => true
			]);

			foreach ($itemids as $itemid) {
				if (!array_key_exists($itemid, $db_items)) {
					self::exception(ZBX_API_ERROR_PARAMETERS, _s('Item with ID "%1$s" is not available.', $itemid));
				}
			}
		}

		if (array_key_exists(ZBX_WIDGET_FIELD_TYPE_ITEM_PROTOTYPE, $ids)
				&& $ids[ZBX_WIDGET_FIELD_TYPE_ITEM_PROTOTYPE]) {
			$item_prototypeids = array_keys($ids[ZBX_WIDGET_FIELD_TYPE_ITEM_PROTOTYPE]);

			$db_item_prototypes = API::ItemPrototype()->get([
				'output' => [],
				'itemids' => $item_prototypeids,
				'preservekeys' => true
			]);

			foreach ($item_prototypeids as $item_prototypeid) {
				if (!array_key_exists($item_prototypeid, $db_item_prototypes)) {
					self::exception(ZBX_API_ERROR_PARAMETERS,
						_s('Item prototype with ID "%1$s" is not available.', $item_prototypeid)
					);
				}
			}
		}

		if (array_key_exists(ZBX_WIDGET_FIELD_TYPE_GRAPH, $ids) && $ids[ZBX_WIDGET_FIELD_TYPE_GRAPH]) {
			$graphids = array_keys($ids[ZBX_WIDGET_FIELD_TYPE_GRAPH]);

			$db_graphs = API::Graph()->get([
				'output' => [],
				'graphids' => $graphids,
				'preservekeys' => true
			]);

			foreach ($graphids as $graphid) {
				if (!array_key_exists($graphid, $db_graphs)) {
					self::exception(ZBX_API_ERROR_PARAMETERS, _s('Graph with ID "%1$s" is not available.', $graphid));
				}
			}
		}

		if (array_key_exists(ZBX_WIDGET_FIELD_TYPE_GRAPH_PROTOTYPE, $ids)
				&& $ids[ZBX_WIDGET_FIELD_TYPE_GRAPH_PROTOTYPE]) {
			$graph_prototypeids = array_keys($ids[ZBX_WIDGET_FIELD_TYPE_GRAPH_PROTOTYPE]);

			$db_graph_prototypes = API::GraphPrototype()->get([
				'output' => [],
				'graphids' => $graph_prototypeids,
				'preservekeys' => true
			]);

			foreach ($graph_prototypeids as $graph_prototypeid) {
				if (!array_key_exists($graph_prototypeid, $db_graph_prototypes)) {
					self::exception(ZBX_API_ERROR_PARAMETERS,
						_s('Graph prototype with ID "%1$s" is not available.', $graph_prototypeid)
					);
				}
			}
		}

		if (array_key_exists(ZBX_WIDGET_FIELD_TYPE_MAP, $ids) && $ids[ZBX_WIDGET_FIELD_TYPE_MAP]) {
			$sysmapids = array_keys($ids[ZBX_WIDGET_FIELD_TYPE_MAP]);

			$db_sysmaps = API::Map()->get([
				'output' => [],
				'sysmapids' => $sysmapids,
				'preservekeys' => true
			]);

			foreach ($sysmapids as $sysmapid) {
				if (!array_key_exists($sysmapid, $db_sysmaps)) {
					self::exception(ZBX_API_ERROR_PARAMETERS, _s('Map with ID "%1$s" is not available.', $sysmapid));
				}
			}
		}
	}

	/**
	 * Update table "widget".
	 *
	 * @param array  $dashboards
	 * @param string $method
	 * @param array  $db_dashboards
	 */
	protected function updateWidget(array $dashboards, string $method, array $db_dashboards = null): void {
		$db_widgets = [];

		if ($db_dashboards !== null) {
			foreach ($dashboards as $dashboard) {
				if (array_key_exists('widgets', $dashboard)) {
					$db_widgets += zbx_toHash($db_dashboards[$dashboard['dashboardid']]['widgets'], 'widgetid');
				}
			}
		}

		$ins_widgets = [];
		$upd_widgets = [];

		$field_names = [
			'str' => ['type', 'name'],
			'int' => ['view_mode', 'x', 'y', 'width', 'height']
		];

		foreach ($dashboards as $dashboard) {
			if (array_key_exists('widgets', $dashboard)) {
				foreach ($dashboard['widgets'] as $widget) {
					if (array_key_exists('widgetid', $widget)) {
						$db_widget = $db_widgets[$widget['widgetid']];
						unset($db_widgets[$widget['widgetid']]);

						$upd_widget = [];

						foreach ($field_names['str'] as $field_name) {
							if (array_key_exists($field_name, $widget)) {
								if ($widget[$field_name] !== $db_widget[$field_name]) {
									$upd_widget[$field_name] = $widget[$field_name];
								}
							}
						}
						foreach ($field_names['int'] as $field_name) {
							if (array_key_exists($field_name, $widget)) {
								if ($widget[$field_name] != $db_widget[$field_name]) {
									$upd_widget[$field_name] = $widget[$field_name];
								}
							}
						}

						if ($upd_widget) {
							$upd_widgets[] = [
								'values' => $upd_widget,
								'where' => ['widgetid' => $db_widget['widgetid']]
							];
						}
					}
					else {
						$ins_widgets[] = ['dashboardid' => $dashboard['dashboardid']] + $widget;
					}
				}
			}
		}

		if ($ins_widgets) {
			$widgetids = DB::insert('widget', $ins_widgets);
			$index = 0;

			foreach ($dashboards as &$dashboard) {
				if (array_key_exists('widgets', $dashboard)) {
					foreach ($dashboard['widgets'] as &$widget) {
						if (!array_key_exists('widgetid', $widget)) {
							$widget['widgetid'] = $widgetids[$index++];
						}
					}
					unset($widget);
				}
			}
			unset($dashboard);
		}

		if ($upd_widgets) {
			DB::update('widget', $upd_widgets);
		}

		if ($db_widgets) {
			$this->deleteWidgets(array_keys($db_widgets));
		}

		$this->updateWidgetField($dashboards, $method);
	}

	/**
	 * Delete widgets.
	 *
	 * @param array  $widgetids
	 */
	protected function deleteWidgets(array $widgetids): void {
		DB::delete('profiles', [
			'idx' => 'web.dashbrd.widget.rf_rate',
			'idx2' => $widgetids
		]);

		DB::delete('widget', ['widgetid' => $widgetids]);
	}

	/**
	 * Update table "widget_field".
	 *
	 * @param array  $dashboards
	 * @param array  $dashboards[]['widgets']              (optional)
	 * @param array  $dashboards[]['widgets'][]['fields']  (optional)
	 * @param string $method
	 */
	protected function updateWidgetField(array $dashboards, string $method): void {
		$widgets_fields = [];
		$field_names_by_type = static::getFieldNamesByType();
		$def_values = [];
		foreach ($field_names_by_type as $field_name) {
			$def_values[$field_name] = DB::getDefault('widget_field', $field_name);
		}

		foreach ($dashboards as $dashboard) {
			if (array_key_exists('widgets', $dashboard)) {
				foreach ($dashboard['widgets'] as $widget) {
					if (array_key_exists('fields', $widget)) {
						CArrayHelper::sort($widget['fields'], ['type', 'name']);
						$widgets_fields[$widget['widgetid']] = $widget['fields'];
					}
				}
			}
		}

		foreach ($widgets_fields as &$widget_fields) {
			foreach ($widget_fields as &$widget_field) {
				$widget_field[$field_names_by_type[$widget_field['type']]] = $widget_field['value'];
				unset($widget_field['value']);
				$widget_field += $def_values;
			}
			unset($widget_field);
		}
		unset($widget_fields);

		$db_widget_fields = ($method === 'update')
			? DB::select('widget_field', [
				'output' => ['widget_fieldid', 'widgetid', 'type', 'name', 'value_int', 'value_str', 'value_groupid',
					'value_hostid', 'value_itemid', 'value_graphid', 'value_sysmapid'
				],
				'filter' => ['widgetid' => array_keys($widgets_fields)],
				'sortfield' => ['widgetid', 'type', 'name']
			])
			: [];

		$ins_widget_fields = [];
		$upd_widget_fields = [];
		$del_widget_fieldids = [];

		$field_names = [
			'str' => ['name', 'value_str'],
			'int' => ['type', 'value_int'],
			'ids' => ['value_groupid', 'value_hostid', 'value_itemid', 'value_graphid', 'value_sysmapid']
		];

		foreach ($db_widget_fields as $db_widget_field) {
			if ($widgets_fields[$db_widget_field['widgetid']]) {
				$widget_field = array_shift($widgets_fields[$db_widget_field['widgetid']]);

				$upd_widget_field = [];

				foreach ($field_names['str'] as $field_name) {
					if (array_key_exists($field_name, $widget_field)) {
						if ($widget_field[$field_name] !== $db_widget_field[$field_name]) {
							$upd_widget_field[$field_name] = $widget_field[$field_name];
						}
					}
				}
				foreach ($field_names['int'] as $field_name) {
					if (array_key_exists($field_name, $widget_field)) {
						if ($widget_field[$field_name] != $db_widget_field[$field_name]) {
							$upd_widget_field[$field_name] = $widget_field[$field_name];
						}
					}
				}
				foreach ($field_names['ids'] as $field_name) {
					if (array_key_exists($field_name, $widget_field)) {
						if (bccomp($widget_field[$field_name], $db_widget_field[$field_name]) != 0) {
							$upd_widget_field[$field_name] = $widget_field[$field_name];
						}
					}
				}

				if ($upd_widget_field) {
					$upd_widget_fields[] = [
						'values' => $upd_widget_field,
						'where' => ['widget_fieldid' => $db_widget_field['widget_fieldid']]
					];
				}
			}
			else {
				$del_widget_fieldids[] = $db_widget_field['widget_fieldid'];
			}
		}

		foreach ($widgets_fields as $widgetid => $widget_fields) {
			foreach ($widget_fields as $widget_field) {
				$ins_widget_fields[] = ['widgetid' => $widgetid] + $widget_field;
			}
		}

		if ($ins_widget_fields) {
			DB::insert('widget_field', $ins_widget_fields);
		}

		if ($upd_widget_fields) {
			DB::update('widget_field', $upd_widget_fields);
		}

		if ($del_widget_fieldids) {
			DB::delete('widget_field', ['widget_fieldid' => $del_widget_fieldids]);
		}
	}

	/**
	 * @param array $dashboardids
	 *
	 * @return array
	 */
	public function delete(array $dashboardids): array {
		$api_input_rules = ['type' => API_IDS, 'flags' => API_NOT_EMPTY, 'uniq' => true];
		if (!CApiInputValidator::validate($api_input_rules, $dashboardids, '/', $error)) {
			self::exception(ZBX_API_ERROR_PARAMETERS, $error);
		}

		$db_dashboards = $this->get([
			'output' => ['dashboardid', 'name'],
			'selectWidgets' => ['widgetid'],
			'dashboardids' => $dashboardids,
			'editable' => true,
			'preservekeys' => true
		]);

		$widgetids = [];

		foreach ($dashboardids as $dashboardid) {
			if (!array_key_exists($dashboardid, $db_dashboards)) {
				self::exception(ZBX_API_ERROR_PERMISSIONS,
					_('No permissions to referred object or it does not exist!')
				);
			}

			$widgetids = array_merge($widgetids, array_column($db_dashboards[$dashboardid]['widgets'], 'widgetid'));
		}

		if ($widgetids) {
			$this->deleteWidgets($widgetids);
		}

		DB::delete('dashboard', ['dashboardid' => $dashboardids]);

		$this->addAuditBulk(AUDIT_ACTION_DELETE, static::AUDIT_RESOURCE, $db_dashboards);

		return ['dashboardids' => $dashboardids];
	}

	protected function addRelatedObjects(array $options, array $result) {
		$result = parent::addRelatedObjects($options, $result);

		$dashboardids = array_keys($result);

		if ($this instanceof CDashboard) {
			// Adding user shares.
			if ($options['selectUsers'] !== null) {
				$relation_map = $this->createRelationMap($result, 'dashboardid', 'userid', 'dashboard_user');
				// Get all allowed users.
				$db_users = API::User()->get([
					'output' => [],
					'userids' => $relation_map->getRelatedIds(),
					'preservekeys' => true
				]);

				if ($db_users) {
					$db_dashboard_users = API::getApiService()->select('dashboard_user', [
						'output' => $this->outputExtend($options['selectUsers'], ['dashboardid', 'userid']),
						'filter' => ['dashboardid' => $dashboardids, 'userid' => array_keys($db_users)],
						'preservekeys' => true
					]);

					$relation_map = $this->createRelationMap($db_dashboard_users, 'dashboardid', 'dashboard_userid');

					$db_dashboard_users = $this->unsetExtraFields($db_dashboard_users, ['userid'], $options['selectUsers']);

					foreach ($db_dashboard_users as &$db_dashboard_user) {
						unset($db_dashboard_user['dashboard_userid'], $db_dashboard_user['dashboardid']);
					}
					unset($db_dashboard_user);

					$result = $relation_map->mapMany($result, $db_dashboard_users, 'users');
				}
				else {
					foreach ($result as &$row) {
						$row['users'] = [];
					}
					unset($row);
				}
			}

			// Adding user group shares.
			if ($options['selectUserGroups'] !== null) {
				$relation_map = $this->createRelationMap($result, 'dashboardid', 'usrgrpid', 'dashboard_usrgrp');
				// Get all allowed groups.
				$db_usrgrps = API::UserGroup()->get([
					'output' => [],
					'usrgrpids' => $relation_map->getRelatedIds(),
					'preservekeys' => true
				]);

				if ($db_usrgrps) {
					$db_dashboard_usrgrps = API::getApiService()->select('dashboard_usrgrp', [
						'output' => $this->outputExtend($options['selectUserGroups'], ['dashboardid', 'usrgrpid']),
						'filter' => ['dashboardid' => $dashboardids, 'usrgrpid' => array_keys($db_usrgrps)],
						'preservekeys' => true
					]);

					$relation_map = $this->createRelationMap($db_dashboard_usrgrps, 'dashboardid', 'dashboard_usrgrpid');

					$db_dashboard_usrgrps =
						$this->unsetExtraFields($db_dashboard_usrgrps, ['usrgrpid'], $options['selectUserGroups']);

					foreach ($db_dashboard_usrgrps as &$db_dashboard_usrgrp) {
						unset($db_dashboard_usrgrp['dashboard_usrgrpid'], $db_dashboard_usrgrp['dashboardid']);
					}
					unset($db_dashboard_usrgrp);

					$result = $relation_map->mapMany($result, $db_dashboard_usrgrps, 'userGroups');
				}
				else {
					foreach ($result as &$row) {
						$row['userGroups'] = [];
					}
					unset($row);
				}
			}
		}

		// Adding widgets.
		if ($options['selectWidgets'] !== null) {
			$fields_requested = $this->outputIsRequested('fields', $options['selectWidgets']);
			if ($fields_requested && is_array($options['selectWidgets'])) {
				$key = array_search('fields', $options['selectWidgets']);
				unset($options['selectWidgets'][$key]);
			}

			$db_widgets = API::getApiService()->select('widget', [
				'output' => $this->outputExtend($options['selectWidgets'], ['widgetid', 'dashboardid']),
				'filter' => ['dashboardid' => $dashboardids],
				'preservekeys' => true
			]);

			if ($db_widgets && $fields_requested) {
				foreach ($db_widgets as &$db_widget) {
					$db_widget['fields'] = [];
				}
				unset($db_widget);

				$db_widget_fields = DB::select('widget_field', [
					'output' => ['widgetid', 'type', 'name', 'value_int', 'value_str', 'value_groupid', 'value_hostid',
						'value_itemid', 'value_graphid', 'value_sysmapid'
					],
					'filter' => ['widgetid' => array_keys($db_widgets)]
				]);

				$field_names_by_type = self::getFieldNamesByType();

				foreach ($db_widget_fields as $db_widget_field) {
					$db_widgets[$db_widget_field['widgetid']]['fields'][] = [
						'type' => $db_widget_field['type'],
						'name' => $db_widget_field['name'],
						'value' => $db_widget_field[$field_names_by_type[$db_widget_field['type']]]

					];
				}
			}

			foreach ($result as &$row) {
				$row['widgets'] = [];
			}
			unset($row);

			$db_widgets = $this->unsetExtraFields($db_widgets, ['widgetid'], $options['selectWidgets']);

			foreach ($db_widgets as $db_widget) {
				$dashboardid = $db_widget['dashboardid'];
				unset($db_widget['dashboardid']);

				$result[$dashboardid]['widgets'][] = $db_widget;
			}
		}

		return $result;
	}
}
