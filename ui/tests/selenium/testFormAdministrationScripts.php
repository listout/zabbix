<?php
/*
** Zabbix
** Copyright (C) 2001-2021 Zabbix SIA
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

require_once dirname(__FILE__).'/../include/CWebTest.php';
require_once dirname(__FILE__).'/behaviors/CMessageBehavior.php';

/**
 * @backup scripts
 */
class testFormAdministrationScripts extends CWebTest {

	private const ID_UPDATE = 200;	// Script for Update.

	private const ID_CLONE = 201; // Script for Clone.
	private const NAME_CLONE = 'Cloned Script for Clone';

	private const ID_DELETE = 202;
	private const NAME_DELETE = 'Script for Delete';

	/**
	 * Attach MessageBehavior to the test.
	 *
	 * @return array
	 */
	public function getBehaviors() {
		return [
			CMessageBehavior::class
		];
	}

	/**
	 * Test data for Scripts form.
	 */
	public function getScriptsData() {
		return [
			// Webhook.
			[
				[
					'fields' =>  [
						'Name' => 'Minimal script',
						'Script' => 'java script'
					]
				]
			],
			// Remove trailing spaces.
			// Uncomment this after ZBX-18271 is fixed.
//			[
//				[
//					'trim' => true,
//					'fields' =>  [
//						'Name' => 'Test trailing spaces',
//						'Type' => 'Webhook',
//						'Script' => 'Webhook Script'
//					],
//					'Parameters' => [
//						[
//							'action' => USER_ACTION_UPDATE,
//							'index' => 1,
//							'Name' => 'name',
//							'Value' => '   trimmed    value    '
//						],
//						[
//							'action' => USER_ACTION_UPDATE,
//							'index' => 0,
//							'Name' => '   trimmed     name    ',
//							'Value' => 'value'
//						]
//					]
//				]
//			],
			[
				[
					'fields' =>  [
						'Name' => 'Max webhook',
						'Scope' => 'Manual host action',
						'Type' => 'Webhook',
						'Script' => 'Webhook Script',
						'Timeout' => '60s',
						'Description' => 'Test description',
						'User group' => 'Selenium user group',
						'Host group' => 'Selected',
						'xpath://div[@id="groupid"]/..' => 'Templates',
						'Required host permissions' => 'Write',
						'Enable confirmation' => true,
						'Confirmation text' => 'Execute script?'
					],
					'Parameters' => [
						[
							'action' => USER_ACTION_UPDATE,
							'index' => 0,
							'Name' => 'host',
							'Value' => '{HOST.HOST}'
						],
						[
							'action' => USER_ACTION_UPDATE,
							'index' => 1,
							'Name' => 'var',
							'Value' => 'Value'
						]
					]
				]
			],
			[
				[
					'fields' =>  [
						'Name' => 'Max webhook 2',
						'Scope' => 'Action operation',
						'Type' => 'Webhook',
						'Script' => 'Webhook Script',
						'Timeout' => '60s',
						'Description' => 'Test description',
						'Host group' => 'Selected',
						'xpath://div[@id="groupid"]/..' => 'Templates'
					],
					'Parameters' => [
						[
							'action' => USER_ACTION_UPDATE,
							'index' => 0,
							'Name' => 'host',
							'Value' => '{HOST.HOST}'
						],
						[
							'action' => USER_ACTION_UPDATE,
							'index' => 1,
							'Name' => 'var',
							'Value' => 'Value'
						]
					]
				]
			],
			[
				[
					'fields' =>  [
						'Name' => 'Max webhook 3',
						'Scope' => 'Manual event action',
						'Type' => 'Webhook',
						'Script' => 'Webhook Script',
						'Timeout' => '60s',
						'Description' => 'Test description',
						'User group' => 'Selenium user group',
						'Host group' => 'Selected',
						'xpath://div[@id="groupid"]/..' => 'Templates',
						'Required host permissions' => 'Write',
						'Enable confirmation' => true,
						'Confirmation text' => 'Execute script?'
					],
					'Parameters' => [
						[
							'action' => USER_ACTION_UPDATE,
							'index' => 0,
							'Name' => 'host',
							'Value' => '{HOST.HOST}'
						],
						[
							'action' => USER_ACTION_UPDATE,
							'index' => 1,
							'Name' => 'var',
							'Value' => 'Value'
						]
					]
				]
			],
			// Uncomment this after ZBX-18271 is fixed.
//			[
//				[
//					'fields' =>  [
//						'Name' => 'Test parameters',
//						'Type' => 'Webhook',
//						'Script' => 'Webhook Script',
//						'Timeout' => '1s',
//					],
//					'Parameters' => [
//						[
//							'action' => USER_ACTION_UPDATE,
//							'index' => 0,
//							'Name' => '!@#$%^&*()_+<>,.\/',
//							'Value' => '!@#$%^&*()_+<>,.\/'
//						],
//						[
//							'action' => USER_ACTION_UPDATE,
//							'index' => 1,
//							'Name' => str_repeat('n', 255),
//							'Value' => str_repeat('v', 2048)
//						],
//						[
//							'Name' => '{$MACRO:A}',
//							'Value' => '{$MACRO:A}'
//						],
//						[
//							'Name' => '{$USERMACRO}',
//							'Value' => ''
//						],
//						[
//							'Name' => '{HOST.HOST}'
//						],
//						[
//							'Name' => 'Имя',
//							'Value' => 'Значение'
//						]
//					]
//				]
//			],
			[
				[
					'fields' =>  [
						'Name' => 'Webhook false confirmation',
						'Script' => 'webhook',
						'Script' => 'java script',
						'Enable confirmation' => false
					]
				]
			],
			[
				[
					'fields' =>  [
						'Name' => 'Timeout test 1',
						'Script' => 'java script',
						'Timeout' => '1'
					]
				]
			],
			[
				[
					'fields' =>  [
						'Name' => 'Timeout test 60',
						'Script' => 'java script',
						'Timeout' => '60'
					]
				]
			],
			[
				[
					'expected' => TEST_BAD,
					'details' => 'Incorrect value for field "timeout": value must be one of 1-60.',
					'fields' =>  [
						'Name' => 'Timeout test 0',
						'Script' => 'java script',
						'Timeout' => '0'
					]
				]
			],
			[
				[
					'fields' =>  [
						'Name' => 'Timeout test 1m',
						'Script' => 'java script',
						'Timeout' => '1m'
					]
				]
			],
			[
				[
					'expected' => TEST_BAD,
					'details' => 'Incorrect value for field "timeout": value must be one of 1-60.',
					'fields' =>  [
						'Name' => 'Timeout test 1h',
						'Script' => 'java script',
						'Timeout' => '1h'
					]
				]
			],
			[
				[
					'expected' => TEST_BAD,
					'details' => 'Incorrect value for field "timeout": value must be one of 1-60.',
					'fields' =>  [
						'Name' => 'Timeout test 70',
						'Script' => 'java script',
						'Timeout' => '70s'
					]
				]
			],
			[
				[
					'expected' => TEST_BAD,
					'details' => 'Incorrect value for field "timeout": a time unit is expected.',
					'fields' =>  [
						'Name' => 'Timeout test -1',
						'Script' => 'java script',
						'Timeout' => '-1'
					]
				]
			],
			[
				[
					'expected' => TEST_BAD,
					'details' => 'Incorrect value for field "timeout": a time unit is expected.',
					'fields' =>  [
						'Name' => 'Timeout test character',
						'Script' => 'java script',
						'Timeout' => 'char'
					]
				]
			],
			[
				[
					'expected' => TEST_BAD,
					'details' => 'Invalid parameter "/1/parameters/1/name": cannot be empty.',
					'fields' =>  [
						'Name' => 'Test empty parameters',
						'Type' => 'Webhook',
						'Script' => 'Webhook Script'
					],
					'Parameters' => [
						[
							'action' => USER_ACTION_UPDATE,
							'index' => 0,
							'Name' => '',
							'Value' => ''
						],
						[
							'action' => USER_ACTION_UPDATE,
							'index' => 1,
							'Name' => '',
							'Value' => ''
						]
					]
				]
			],
			[
				[
					'expected' => TEST_BAD,
					'details' => 'Invalid parameter "/1/parameters/2": value (name)=(Param1) already exists.',
					'fields' =>  [
						'Name' => 'Test empty parameter names',
						'Type' => 'Webhook',
						'Script' => 'Webhook Script'
					],
					'Parameters' => [
						[
							'action' => USER_ACTION_UPDATE,
							'index' => 0,
							'Name' => 'Param1',
							'Value' => 'Value1'
						],
						[
							'action' => USER_ACTION_UPDATE,
							'index' => 1,
							'Name' => 'Param1',
							'Value' => 'Value'
						]
					]
				]
			],
			[
				[
					'expected' => TEST_BAD,
					'details' => 'Invalid parameter "/1/parameters/1/name": cannot be empty.',
					'fields' =>  [
						'Name' => 'Test trailing spaces',
						'Type' => 'Webhook',
						'Script' => 'Webhook Script'
					],
					'Parameters' => [
						[
							'action' => USER_ACTION_UPDATE,
							'index' => 0,
							'Name' => '   ',
							'Value' => '   '
						]
					]
				]
			],
			[
				[
					'expected' => TEST_BAD,
					'details' => 'Invalid parameter "/1/command": cannot be empty.',
					'fields' =>  [
						'Name' => 'Webhook Empty script',
						'Script' => ''
					]
				]
			],
			[
				[
					'expected' => TEST_BAD,
					'details' => 'Incorrect value for field "name": cannot be empty.',
					'fields' =>  [
						'Name' => '',
						'Script' => 'Webhook: empty name'
					]
				]
			],
			// Script.
			[
				[
					'fields' =>  [
						'Name' => 'Max script',
						'Scope' => 'Manual host action',
						'Type' => 'Script',
						'Execute on' => 'Zabbix server (proxy)',
						'Commands' => 'Script command',
						'Description' => 'Test description',
						'User group' => 'Selenium user group',
						'Host group' => 'Selected',
						'xpath://div[@id="groupid"]/..' => 'Hypervisors',
						'Required host permissions' => 'Write',
						'Enable confirmation' => false
					]
				]
			],
			[
				[
					'fields' =>  [
						'Name' => 'Max script 2',
						'Scope' => 'Action operation',
						'Type' => 'Script',
						'Execute on' => 'Zabbix server (proxy)',
						'Commands' => 'Script command',
						'Description' => 'Test description',
						'Host group' => 'Selected',
						'xpath://div[@id="groupid"]/..' => 'Hypervisors'
					]
				]
			],
			[
				[
					'fields' =>  [
						'Name' => 'Max script 3',
						'Scope' => 'Manual event action',
						'Type' => 'Script',
						'Execute on' => 'Zabbix server (proxy)',
						'Commands' => 'Script command',
						'Description' => 'Test description',
						'User group' => 'Selenium user group',
						'Host group' => 'Selected',
						'xpath://div[@id="groupid"]/..' => 'Hypervisors',
						'Required host permissions' => 'Write',
						'Enable confirmation' => false
					]
				]
			],
			[
				[
					'expected' => TEST_BAD,
					'details' => 'Incorrect value for field "name": cannot be empty.',
					'fields' =>  [
						'Name' => '',
						'Type' => 'Script',
						'Commands' => 'Script empty name'
					]
				]
			],
			[
				[
					'expected' => TEST_BAD,
					'details' => 'Invalid parameter "/1/command": cannot be empty.',
					'fields' =>  [
						'Name' => 'Script empty command',
						'Type' => 'Script',
						'Commands' => ''
					]
				]
			],
			// IPMI.
			[
				[
					'fields' =>  [
						'Name' => 'Max IPMI',
						'Scope' => 'Manual host action',
						'Type' => 'IPMI',
						'Command' => 'IPMI command',
						'Description' => 'Test description',
						'User group' => 'Selenium user group',
						'Host group' => 'Selected',
						'xpath://div[@id="groupid"]/..' => 'Discovered hosts',
						'Required host permissions' => 'Write',
						'Enable confirmation' => true,
						'Confirmation text' => 'Execute script?'
					]
				]
			],
			[
				[
					'fields' =>  [
						'Name' => 'Max IPMI 2',
						'Scope' => 'Action operation',
						'Type' => 'IPMI',
						'Command' => 'IPMI command',
						'Description' => 'Test description',
						'Host group' => 'Selected',
						'xpath://div[@id="groupid"]/..' => 'Discovered hosts'
					]
				]
			],
			[
				[
					'fields' =>  [
						'Name' => 'Max IPMI 3',
						'Scope' => 'Manual event action',
						'Type' => 'IPMI',
						'Command' => 'IPMI command',
						'Description' => 'Test description',
						'User group' => 'Selenium user group',
						'Host group' => 'Selected',
						'xpath://div[@id="groupid"]/..' => 'Discovered hosts',
						'Required host permissions' => 'Write',
						'Enable confirmation' => true,
						'Confirmation text' => 'Execute script?'
					]
				]
			],
			[
				[
					'expected' => TEST_BAD,
					'details' => 'Incorrect value for field "name": cannot be empty.',
					'fields' =>  [
						'Name' => '',
						'Type' => 'IPMI',
						'Command' => 'IPMI empty name'
					]
				]
			],
			[
				[
					'expected' => TEST_BAD,
					'details' => 'Invalid parameter "/1/command": cannot be empty.',
					'fields' =>  [
						'Name' => 'IPMI empty command',
						'Type' => 'IPMI',
						'Command' => ''
					]
				]
			],
			// SSH.
			[
				[
					'fields' =>  [
						'Name' => 'Max SSH',
						'Scope' => 'Manual host action',
						'Type' => 'SSH',
						'Username' => 'test',
						'Password' => 'test_password',
						'Port' => '81',
						'Commands' => 'Script command',
						'Description' => 'Test description',
						'User group' => 'Selenium user group',
						'Host group' => 'Selected',
						'xpath://div[@id="groupid"]/..' => 'Hypervisors',
						'Required host permissions' => 'Write',
						'Enable confirmation' => false
					]
				]
			],
			[
				[
					'fields' =>  [
						'Name' => 'Max SSH 2',
						'Scope' => 'Action operation',
						'Type' => 'SSH',
						'Username' => 'test',
						'Password' => 'test_password',
						'Port' => '81',
						'Commands' => 'Script command',
						'Description' => 'Test description',
						'Host group' => 'Selected',
						'xpath://div[@id="groupid"]/..' => 'Hypervisors'
					]
				]
			],
			[
				[
					'fields' =>  [
						'Name' => 'Max SSH 3',
						'Scope' => 'Manual event action',
						'Type' => 'SSH',
						'Username' => 'test',
						'Password' => 'test_password',
						'Port' => '81',
						'Commands' => 'Script command',
						'Description' => 'Test description',
						'User group' => 'Selenium user group',
						'Host group' => 'Selected',
						'xpath://div[@id="groupid"]/..' => 'Hypervisors',
						'Required host permissions' => 'Write',
						'Enable confirmation' => false
					]
				]
			],
			[
				[
					'expected' => TEST_BAD,
					'details' => 'Incorrect value for field "name": cannot be empty.',
					'fields' =>  [
						'Name' => '',
						'Type' => 'SSH',
						'Commands' => 'SSH empty name'
					]
				]
			],
			[
				[
					'expected' => TEST_BAD,
					'details' => 'Invalid parameter "/1/command": cannot be empty.',
					'fields' =>  [
						'Name' => 'SSH empty command',
						'Type' => 'SSH',
						'Commands' => ''
					]
				]
			],
			// Telnet
			[
				[
					'fields' =>  [
						'Name' => 'Max Telnet',
						'Scope' => 'Manual host action',
						'Type' => 'Telnet',
						'Username' => 'test',
						'Password' => 'test_password',
						'Port' => '81',
						'Commands' => 'Script command',
						'Description' => 'Test description',
						'User group' => 'Selenium user group',
						'Host group' => 'Selected',
						'xpath://div[@id="groupid"]/..' => 'Hypervisors',
						'Required host permissions' => 'Write',
						'Enable confirmation' => false
					]
				]
			],
			[
				[
					'fields' =>  [
						'Name' => 'Max Telnet 2',
						'Scope' => 'Action operation',
						'Type' => 'Telnet',
						'Username' => 'test',
						'Password' => 'test_password',
						'Port' => '81',
						'Commands' => 'Script command',
						'Description' => 'Test description',
						'Host group' => 'Selected',
						'xpath://div[@id="groupid"]/..' => 'Hypervisors'
					]
				]
			],
			[
				[
					'fields' =>  [
						'Name' => 'Max Telnet 3',
						'Scope' => 'Manual event action',
						'Type' => 'Telnet',
						'Username' => 'test',
						'Password' => 'test_password',
						'Port' => '81',
						'Commands' => 'Script command',
						'Description' => 'Test description',
						'User group' => 'Selenium user group',
						'Host group' => 'Selected',
						'xpath://div[@id="groupid"]/..' => 'Hypervisors',
						'Required host permissions' => 'Write',
						'Enable confirmation' => false
					]
				]
			],
			[
				[
					'expected' => TEST_BAD,
					'details' => 'Incorrect value for field "name": cannot be empty.',
					'fields' =>  [
						'Name' => '',
						'Type' => 'Telnet',
						'Commands' => 'Telnet empty name'
					]
				]
			],
			[
				[
					'expected' => TEST_BAD,
					'details' => 'Invalid parameter "/1/command": cannot be empty.',
					'fields' =>  [
						'Name' => 'Telnet empty command',
						'Type' => 'Telnet',
						'Commands' => ''
					]
				]
			]
		];
	}

	/**
	 * @dataProvider getScriptsData
	 * @backup-once scripts
	 */
	public function testFormAdministrationScripts_Create($data) {
		$this->checkScripts($data, false, 'zabbix.php?action=script.edit');
	}

	/**
	 * @dataProvider getScriptsData
	 */
	public function testFormAdministrationScripts_Update($data) {
		$this->checkScripts($data, true, 'zabbix.php?action=script.edit&scriptid='.self::ID_UPDATE);
	}

	/**
	 * Function for checking script configuration form.
	 *
	 * @param arary     $data     data provider
	 * @param boolean   $update   is it update case, or not
	 * @param string    $link     link to script form
	 */
	private function checkScripts($data, $update, $link) {
		if (CTestArrayHelper::get($data, 'expected', TEST_GOOD) === TEST_BAD) {
			$sql = 'SELECT * FROM scripts ORDER BY scriptid';
			$old_hash = CDBHelper::getHash($sql);
		}

		$this->page->login()->open($link);
		$form = $this->query('id:script-form')->waitUntilReady()->asForm()->one();
		$form->fill($data['fields']);

		if (CTestArrayHelper::get($data, 'Parameters')) {

			// Remove action and index fields for create case.
			if ($update === false) {
				foreach ($data['Parameters'] as &$parameter) {
					unset($parameter['action'], $parameter['index']);
				}
				unset($parameter);
			}

			$this->query('id:parameters-table')->asMultifieldTable()->one()->fill($data['Parameters']);
		}

		// Check testing confirmation while configuring.
		if (array_key_exists('Enable confirmation', $data['fields'])) {
			$this->checkConfirmation($data, $form);
		}

		$form->submit();
		$this->page->waitUntilReady();

		if (CTestArrayHelper::get($data, 'expected', TEST_GOOD) === TEST_BAD) {
			$title = ($update) ? 'Cannot update script' : 'Cannot add script';
			$this->assertMessage(TEST_BAD, $title, $data['details']);
			// Check that DB hash is not changed.
			$this->assertEquals($old_hash, CDBHelper::getHash($sql));
		}
		else {
			$title = ($update) ? 'Script updated' : 'Script added';
			$this->assertMessage(TEST_GOOD, $title);
			$this->assertEquals(1, CDBHelper::getCount('SELECT NULL FROM scripts WHERE name='.zbx_dbstr($data['fields']['Name'])));
			// Check the results in form.
			$id = CDBHelper::getValue('SELECT scriptid FROM scripts WHERE name='.zbx_dbstr($data['fields']['Name']));
			$this->page->open('zabbix.php?action=script.edit&scriptid='.$id);

			$form->invalidate();
			$form->checkValue($data['fields']);

			// Check testing confirmation in saved form.
			if (array_key_exists('Enable confirmation', $data['fields'])) {
				$this->checkConfirmation($data, $form);
			}

			if (CTestArrayHelper::get($data, 'Parameters')) {

				if (CTestArrayHelper::get($data, 'trim', false) === true) {
					// Remove trailing spaces from name and value.
					foreach ($data['Parameters'] as $i => &$fields) {
						foreach (['Name', 'Value'] as $parameter) {
							if (array_key_exists($parameter, $fields)) {
								$fields[$parameter] = trim($fields[$parameter]);
							}
						}
					}
					unset($fields);
				}

				// Remove action and index fields for asserting.
				if ($update === true) {
					foreach ($data['Parameters'] as &$parameter) {
						unset($parameter['action'], $parameter['index']);
					}
					unset($parameter);
				}

				$this->query('id:parameters-table')->asMultifieldTable()->one()->checkValue($data['Parameters']);
			}
		}
	}

	/**
	 * Function for checking execution confirmation popup.
	 *
	 * @param arary     $data    data provider
	 * @param element   $form    script configuration form
	 */
	private function checkConfirmation($data, $form) {
		if (CTestArrayHelper::get($data['fields'], 'Enable confirmation') === false) {
			$this->assertFalse($form->query('id:confirmation')->one()->isEnabled());
			$this->assertFalse($form->query('id:test-confirmation')->one()->isEnabled());
		}

		if (CTestArrayHelper::get($data['fields'], 'Confirmation text')) {
			$this->query('button:Test confirmation')->waitUntilClickable()->one()->click();
			$dialog = COverlayDialogElement::find()->one();
			$this->assertEquals('Execution confirmation', $dialog->getTitle());
			$this->assertEquals($data['fields']['Confirmation text'],
					$dialog->query('xpath://span[@class="confirmation-msg"]')->waitUntilReady()->one()->getText());
			$this->assertFalse($dialog->query('button:Execute')->one()->isEnabled());
			$dialog->query('button:Cancel')->one()->click();
		}
	}

	/**
	 * Function for checking script form update cancelling.
	 */
	public function testFormAdministrationScripts_CancelUpdate() {
		$sql = 'SELECT * FROM scripts ORDER BY scriptid';
		$old_hash = CDBHelper::getHash($sql);
		$this->page->login()->open('zabbix.php?action=script.edit&scriptid='.self::ID_UPDATE);
		$form = $this->query('id:script-form')->waitUntilReady()->asForm()->one();
		$form->fill([
			'Name' => 'Cancelled cript',
			'Type' => 'Script',
			'Execute on' => 'Zabbix server',
			'Commands' => 'Script command',
			'Description' => 'Cancelled description',
			'User group' => 'Disabled',
			'Host group' => 'Selected',
			'xpath://div[@id="groupid"]/..' => 'Hypervisors',
			'Required host permissions' => 'Write',
			'Enable confirmation' => true
		]);
		$form->query('button:Cancel')->waitUntilClickable()->one()->click();
		$this->page->waitUntilReady();
		$this->page->assertHeader('Scripts');
		$this->assertTrue($this->query('button:Create script')->waitUntilVisible()->one()->isReady());
		$this->assertEquals($old_hash, CDBHelper::getHash($sql));
	}

	/**
	 * Function for checking script form update without any changes.
	 */
	public function testFormAdministrationScripts_SimpleUpdate() {
		$sql = 'SELECT * FROM scripts ORDER BY scriptid';
		$old_hash = CDBHelper::getHash($sql);
		$this->page->login()->open('zabbix.php?action=script.edit&scriptid='.self::ID_UPDATE);
		$this->query('id:script-form')->waitUntilReady()->asForm()->one()->submit();
		$this->page->waitUntilReady();
		$this->assertMessage(TEST_GOOD, 'Script updated');
		$this->assertEquals($old_hash, CDBHelper::getHash($sql));
	}

	/**
	 * Function for checking script cloning with only changed name.
	 */
	public function testFormAdministrationScripts_Clone() {
		$this->page->login()->open('zabbix.php?action=script.edit&scriptid='.self::ID_CLONE);
		$form = $this->query('id:script-form')->waitUntilReady()->asForm()->one();
		$values = $form->getFields()->asValues();
		$values['Name'] = self::NAME_CLONE;
		$this->query('button:Clone')->waitUntilReady()->one()->click();
		$this->page->waitUntilReady();

		$form->invalidate();
		$form->fill(['Name' => self::NAME_CLONE]);
		$form->submit();

		$this->assertMessage(TEST_GOOD, 'Script added');
		$this->assertEquals(1, CDBHelper::getCount('SELECT NULL FROM scripts WHERE name='.zbx_dbstr(self::NAME_CLONE)));
		$this->assertEquals(1, CDBHelper::getCount('SELECT NULL FROM scripts WHERE name='.zbx_dbstr('Script for Clone')));

		$id = CDBHelper::getValue('SELECT scriptid FROM scripts WHERE name='.zbx_dbstr(self::NAME_CLONE));
		$this->page->open('zabbix.php?action=script.edit&scriptid='.$id);
		$cloned_values = $form->getFields()->asValues();
		$this->assertEquals($values, $cloned_values);
	}

	/**
	 * Function for testing script delete from configuration form.
	 */
	public function testFormAdministrationScripts_Delete() {
		$this->page->login()->open('zabbix.php?action=script.edit&scriptid='.self::ID_DELETE);
		$this->query('button:Delete')->waitUntilReady()->one()->click();
		$this->page->acceptAlert();
		$this->page->waitUntilReady();
		$this->assertMessage(TEST_GOOD, 'Script deleted');
		$this->assertEquals(0, CDBHelper::getCount('SELECT NULL FROM scripts WHERE name='.zbx_dbstr(self::NAME_DELETE)));
	}
}
