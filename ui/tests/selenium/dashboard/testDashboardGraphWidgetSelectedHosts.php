<?php
/*
** Zabbix
** Copyright (C) 2001-2023 Zabbix SIA
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


require_once dirname(__FILE__).'/../../include/CWebTest.php';

use Facebook\WebDriver\WebDriverKeys;

/**
 * @backup hosts, dashboard
 *
 * @onBefore prepareSelectedHostdata
 */

class testDashboardGraphWidgetSelectedHosts extends CWebTest {

	/**
	 * Id of the dashboard with widgets.
	 *
	 * @var integer
	 */
	protected static $dashboardid;

	public static function prepareSelectedHostdata() {
		$hostgroupid = CDataHelper::call('hostgroup.create', [['name' => 'Suggestion list group']])['groupids'][0];

		CDataHelper::createHosts([
			[
				'host' => 'Host for widget 1',
				'groups' => [
					'groupid' => $hostgroupid
				],
				'status' => HOST_STATUS_MONITORED,
				'items' => [
					[
						'name' => 'Item for Graph 1_1',
						'key_' => 'trap1',
						'type' => ITEM_TYPE_TRAPPER,
						'value_type' => ITEM_VALUE_TYPE_FLOAT
					],
					[
						'name' => 'Item for Graph 1_2',
						'key_' => 'trap2',
						'type' => ITEM_TYPE_TRAPPER,
						'value_type' => ITEM_VALUE_TYPE_FLOAT
					],
					[
						'name' => 'Item for Graph 1_3',
						'key_' => 'trap3',
						'type' => ITEM_TYPE_TRAPPER,
						'value_type' => ITEM_VALUE_TYPE_FLOAT
					],
					[
						'name' => 'Item for Graph 1_4',
						'key_' => 'trap4',
						'type' => ITEM_TYPE_TRAPPER,
						'value_type' => ITEM_VALUE_TYPE_FLOAT
					],
					[
						'name' => 'Item for Graph 1_5',
						'key_' => 'trap5',
						'type' => ITEM_TYPE_TRAPPER,
						'value_type' => ITEM_VALUE_TYPE_FLOAT
					]
				]
			],
			[
				'host' => 'Host for widget 2',
				'groups' => [
					'groupid' => $hostgroupid
				],
				'status' => HOST_STATUS_MONITORED,
				'items' => [
					[
						'name' => 'Item for Graph 2_1',
						'key_' => 'trap1',
						'type' => ITEM_TYPE_TRAPPER,
						'value_type' => ITEM_VALUE_TYPE_FLOAT
					],
					[
						'name' => 'Item for Graph 2_2',
						'key_' => 'trap2',
						'type' => ITEM_TYPE_TRAPPER,
						'value_type' => ITEM_VALUE_TYPE_FLOAT
					],
					[
						'name' => 'Item for Graph 2_3',
						'key_' => 'trap3',
						'type' => ITEM_TYPE_TRAPPER,
						'value_type' => ITEM_VALUE_TYPE_FLOAT
					],
					[
						'name' => 'Item for Graph 2_4',
						'key_' => 'trap4',
						'type' => ITEM_TYPE_TRAPPER,
						'value_type' => ITEM_VALUE_TYPE_FLOAT
					],
					[
						'name' => 'Item for Graph 2_5',
						'key_' => 'trap5',
						'type' => ITEM_TYPE_TRAPPER,
						'value_type' => ITEM_VALUE_TYPE_FLOAT
					]
				]
			],
			[
				'host' => 'Host for widget 3',
				'groups' => [
					'groupid' => $hostgroupid
				],
				'status' => HOST_STATUS_MONITORED,
				'items' => [
					[
						'name' => 'Item for Graph 3_1',
						'key_' => 'trap1',
						'type' => ITEM_TYPE_TRAPPER,
						'value_type' => ITEM_VALUE_TYPE_FLOAT
					],
					[
						'name' => 'Item for Graph 3_2',
						'key_' => 'trap2',
						'type' => ITEM_TYPE_TRAPPER,
						'value_type' => ITEM_VALUE_TYPE_FLOAT
					],
					[
						'name' => 'Item for Graph 3_3',
						'key_' => 'trap3',
						'type' => ITEM_TYPE_TRAPPER,
						'value_type' => ITEM_VALUE_TYPE_FLOAT
					],
					[
						'name' => 'Item for Graph 3_4',
						'key_' => 'trap4',
						'type' => ITEM_TYPE_TRAPPER,
						'value_type' => ITEM_VALUE_TYPE_FLOAT
					],
					[
						'name' => 'Item for Graph 3_5',
						'key_' => 'trap5',
						'type' => ITEM_TYPE_TRAPPER,
						'value_type' => ITEM_VALUE_TYPE_FLOAT
					]
				]
			],
			[
				'host' => 'Host for widget 4',
				'groups' => [
					'groupid' => $hostgroupid
				],
				'status' => HOST_STATUS_MONITORED,
				'items' => [
					[
						'name' => 'Item for Graph 4_1',
						'key_' => 'trap1',
						'type' => ITEM_TYPE_TRAPPER,
						'value_type' => ITEM_VALUE_TYPE_FLOAT
					],
					[
						'name' => 'Item for Graph 4_2',
						'key_' => 'trap2',
						'type' => ITEM_TYPE_TRAPPER,
						'value_type' => ITEM_VALUE_TYPE_FLOAT
					],
					[
						'name' => 'Item for Graph 4_3',
						'key_' => 'trap3',
						'type' => ITEM_TYPE_TRAPPER,
						'value_type' => ITEM_VALUE_TYPE_FLOAT
					],
					[
						'name' => 'Item for Graph 4_4',
						'key_' => 'trap4',
						'type' => ITEM_TYPE_TRAPPER,
						'value_type' => ITEM_VALUE_TYPE_FLOAT
					],
					[
						'name' => 'Item for Graph 4_5',
						'key_' => 'trap5',
						'type' => ITEM_TYPE_TRAPPER,
						'value_type' => ITEM_VALUE_TYPE_FLOAT
					]
				]
			],
			[
				'host' => 'Host for widget 5',
				'groups' => [
					'groupid' => $hostgroupid
				],
				'status' => HOST_STATUS_MONITORED,
				'items' => [
					[
						'name' => 'Item for Graph 5_1',
						'key_' => 'trap1',
						'type' => ITEM_TYPE_TRAPPER,
						'value_type' => ITEM_VALUE_TYPE_FLOAT
					],
					[
						'name' => 'Item for Graph 5_2',
						'key_' => 'trap2',
						'type' => ITEM_TYPE_TRAPPER,
						'value_type' => ITEM_VALUE_TYPE_FLOAT
					],
					[
						'name' => 'Item for Graph 5_3',
						'key_' => 'trap3',
						'type' => ITEM_TYPE_TRAPPER,
						'value_type' => ITEM_VALUE_TYPE_FLOAT
					],
					[
						'name' => 'Item for Graph 5_4',
						'key_' => 'trap4',
						'type' => ITEM_TYPE_TRAPPER,
						'value_type' => ITEM_VALUE_TYPE_FLOAT
					],
					[
						'name' => 'Item for Graph 5_5',
						'key_' => 'trap5',
						'type' => ITEM_TYPE_TRAPPER,
						'value_type' => ITEM_VALUE_TYPE_FLOAT
					]
				]
			]
		]);

		CDataHelper::call('dashboard.create', [
			[
				'name' => 'Dashboard for creating Graph widgets',
				'display_period' => 60,
				'auto_start' => 0,
				'pages' => [
					[
						'name' => 'First page'
					]
				]
			]
		]);
		self::$dashboardid = array_values(CDataHelper::getIds('name'))[0];
	}

	public static function getDatasetData() {
		return [
			[
				[
					'Data set' => [
						'host' => 'Host for widget'
					],
					'expected' => [
						'Host for widget 1',
						'Host for widget 2',
						'Host for widget 4',
						'Host for widget 5'
					],
					'select' => 'Host for widget 3',
					'keyboard_navigation' => true,
				]
			],
			[
				[
					'Data set' => [
						'host' => 'Host for widget 1',
						'item' => 'Item for'
					],
					'expected' => [
						'Item for Graph 1_1',
						'Item for Graph 1_2',
						'Item for Graph 1_4',
						'Item for Graph 1_5'
					],
					'select' => 'Item for Graph 1_3'
				]
			],
			[
				[
					'Data set' => [
						'host' => 'Host for widget*',
						'item' => 'Item'
					],
					'expected' => [
						'Item for Graph 1_1',
						'Item for Graph 1_2',
						'Item for Graph 1_3',
						'Item for Graph 1_4',
						'Item for Graph 1_5',
						'Item for Graph 2_1',
						'Item for Graph 2_2',
						'Item for Graph 2_3',
						'Item for Graph 2_4',
						'Item for Graph 2_5',
						'Item for Graph 3_1',
						'Item for Graph 3_2',
						'Item for Graph 3_3',
						'Item for Graph 3_4',
						'Item for Graph 3_5',
						'Item for Graph 4_1',
						'Item for Graph 4_2',
						'Item for Graph 4_3',
						'Item for Graph 4_4'
					]
				]
			],
			[
				[
					'Data set' => [
						'host' => [
							'Host for widget 1',
							'Host for widget 2'
						],
						'item' => 'Item'
					],
					'expected' => [
						'Item for Graph 1_1',
						'Item for Graph 1_2',
						'Item for Graph 1_3',
						'Item for Graph 1_4',
						'Item for Graph 1_5',
						'Item for Graph 2_1',
						'Item for Graph 2_2',
						'Item for Graph 2_3',
						'Item for Graph 2_4',
						'Item for Graph 2_5'
					],
					'keyboard_navigation' => true,
				]
			],
			[
				[
					'Data set' => [
						'host' => [
							'Host for widget 1',
							'Host for widget 4'
						],
						'item' => 'Item'
					],
					'expected' => [
						'Item for Graph 1_1',
						'Item for Graph 1_2',
						'Item for Graph 1_3',
						'Item for Graph 1_4',
						'Item for Graph 1_5',
						'Item for Graph 4_1',
						'Item for Graph 4_2',
						'Item for Graph 4_3',
						'Item for Graph 4_4',
						'Item for Graph 4_5'
					]
				]
			]
		];
	}

	/**
	 * Function checks using keyboard navigation and elements if Graph Widget is correctly selecting and displaying hosts,
	 * their items in suggestion list.
	 *
	 * @dataProvider getDatasetData
	 */
	public function testDashboardGraphWidgetSelectedHosts_CheckSuggestionList($data) {
		$this->page->login()->open('zabbix.php?action=dashboard.view&dashboardid='.self::$dashboardid);
		$form = CDashboardElement::find()->one()->edit()->addWidget()->asForm();
		$form->fill(['Type' => 'Graph']);

		// Change mapping of associative arrays from data set.
		if (array_key_exists('item', $data['Data set'])) {
			$form->fill(['xpath:.//div[@id="ds_0_hosts_"]/..' => $data['Data set']['host']]);

			if (CTestArrayHelper::get($data, 'select')) {
				$form->fill(['xpath:.//div[@id="ds_0_items_"]/..' => $data['select']]);
			}

			$field_data = ['xpath:.//input[@placeholder="item pattern"]' => $data['Data set']['item']];
		}
		else {
			if (CTestArrayHelper::get($data, 'select')) {
				$form->fill(['xpath:.//div[@id="ds_0_hosts_"]/..' => $data['select']]);
			}

			$field_data = ['xpath:.//input[@placeholder="host pattern"]' => $data['Data set']['host']];
		}

		$form->fill($field_data);
		$this->checkSuggestionListCommon($data['expected'], $form);

		if (CTestArrayHelper::get($data, 'keyboard_navigation')) {
			$this->checkSuggestionListWithKeyboardNavigation($data, $form);
		}
	}

	/**
	 * Check contents of the suggestions list.
	 *
	 * @param array			$data		data provider
	 * @param CFormElement 	$form		form element of dashboard share
	 */
	protected function checkSuggestionListCommon ($data, $form) {
		$this->query('class', 'multiselect-suggest')->waitUntilVisible();
		$this->assertEquals($data, $form->getField('xpath:.//div[@id="ds_0_hosts_"]/..')->getSuggestions());
	}

	/**
	 * Suggestion list check using keyboard navigation.
	 *
	 * @param array			$data		data provider
	 * @param CFormElement 	$form		form element of dashboard share
	 */
	protected function checkSuggestionListWithKeyboardNavigation($data, $form) {
		$actual_suggestions = [];
		$id = (CTestArrayHelper::get($data['Data set'], 'item')) ? 'items' : 'hosts';

		// Go through the whole suggestion list using keyboard navigation and collect values that were in focus.
		for ($x = 0; $x < (count($data['expected'])); $x++) {
			$this->page->pressKey(WebDriverKeys::ARROW_DOWN);
			$option = ($id === 'items') ? 'Graph' : 'widget';
			$suggestion_text = $this->query('xpath://div[@id="ds_0_'.$id.'_"]//div[@aria-live="assertive"]')
					->one()->waitUntilTextPresent($option)->getText();
			array_push($actual_suggestions, $suggestion_text);
		}

		// Check that using keyboard navigation all suggestions were reachable.
		$this->assertEquals($data['expected'], $actual_suggestions);

		// Submit the last entry in the array and check that it was selected.
		$this->page->pressKey(WebDriverKeys::ENTER);

		// Check that the last value is selected.
		$selected = end($data['expected']);
		$this->assertTrue($this->query('xpath://li[@data-id='.CXPathHelper::escapeQuotes($selected).']')->one()->isValid());

		// Check that selected value is not in the list of suggestions.
		if ($id === 'items') {
			$form->fill(['xpath:.//input[@placeholder="item pattern"]' => 'Graph']);
		}
		else {
			$form->fill(['xpath:.//input[@placeholder="host pattern"]' => 'Host for widget']);
		}
		$this->checkSuggestionListCommon(array_diff($data['expected'], [$selected]), $form);
	}
}
