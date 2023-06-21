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
require_once dirname(__FILE__).'/../behaviors/CMessageBehavior.php';

/**
 * Base class for "Hosts and Problems filter save" function tests.
 */
class testFormFilter extends CWebTest {

	// URL to page with filters: Hosts or Problems.
	public $url;

	/**
	 * Attach MessageBehavior to the test.
	 *
	 * @return array
	 */
	public function getBehaviors() {
		return [CMessageBehavior::class];
	}

	/**
	 * Check created filter.
	 *
	 * @param array $data  given data provider
	 */
	public function checkFilters($data, $table_selector) {
		$filter_container = $this->query('xpath://ul[@class="ui-sortable-container ui-sortable"]')->asFilterTab()->one();

		switch ($data['expected']) {
			case TEST_GOOD:
				$table = $this->query($table_selector)->asTable()->waitUntilReady()->one();
				$rows = $table->getRows();
				$filtered_rows_count = ($rows->count() === 1 && $rows->asText() === ['No data found.'])
					? 0
					: $rows->count();

				// Checking that data exists after saving filter.
				if (array_key_exists('filter_form', $data)) {
					$form = $this->query('id:tabfilter_'.$data['tab_id'])->asForm()->waitUntilVisible()->one();
					$form->checkValue($data['filter_form']);
				}

				// Filter default name is Untitled.
				if (!array_key_exists('Name', $data['filter'])) {
					$data['filter']['Name'] = 'Untitled';
				}

				$this->checkName($data['filter']['Name']);

				// Checking that hosts/problems amount displayed near name in filter tab.
				if (array_key_exists('Show number of records', $data['filter'])) {
					$this->query('xpath://a[@class="zi-filter tabfilter-item-link"]')->one()->click();
					$this->assertEquals($filtered_rows_count, $this->query('xpath://li[@data-target="tabfilter_'.
							$data['tab_id'].'"]/a')->one()->getAttribute('data-counter'));
				}

				// Checking that dropdown/popup tab works.
				$dropdown = $this->query('class:zi-chevron-down')->asPopupButton()->waitUntilClickable()->one();
				$dropdown->fill($data['filter']['Name']);
				$this->assertEquals($data['filter']['Name'], $filter_container->getSelectedTabName());
				break;

			case TEST_BAD:
				$this->assertMessage(TEST_BAD, null, $data['error_message']);
				$this->page->refresh()->waitUntilReady();
				$this->assertEquals($this->query('xpath://li/ul[@class="ui-sortable-container ui-sortable"]/li')->count(), 1);
				break;
		}
	}

	/**
	 * Create, remember and check filter.
	 *
	 * @param array  $data				given data provider
	 * @param string $table_selector	selector of a table with filtered data
	 */
	public function checkRememberedFilters($data, $table_selector = 'class:list-table') {
		$this->page->login()->open($this->url.'&filter_reset=1')->waitUntilReady();

		// Checking if home tab is selected.
		$xpath = 'xpath://li[@data-target="tabfilter_0"]';
		if ($this->query($xpath)->one()->getAttribute('class') === 'tabfilter-item-label') {
			$this->query($xpath.'/a')->waitUntilClickable()->one()->click();
			$this->page->waitUntilReady();
		}

		$home_form = $this->query('xpath://div[@id="tabfilter_0"]/form')->asForm()->one();
		$home_form->fill($data);

		$result_table = $this->query($table_selector)->asTable()->waitUntilPresent()->one();
		$this->query('name:filter_apply')->waitUntilClickable()->one()->click();
		$result_table->waitUntilReloaded();
		$filter_result = $result_table->getRows()->asText();

		// Go to another page, to check saved filter after.
		$this->page->open('zabbix.php?action=dashboard.view')->waitUntilReady();

		// Open filter page again.
		$this->page->open($this->url)->waitUntilReady();

		// Check that filter form fields and table result match.
		$home_form->invalidate()->checkValue($data);
		$this->assertEquals($filter_result, $result_table->getRows()->asText());

		// Reset filter not to interfere next tests.
		$this->query('name:filter_reset')->waitUntilClickable()->one()->click();
		$this->page->waitUntilReady();
	}

	/**
	 * Change data in filter form.
	 *
	 * @param string $user              test user with saved filters
	 * @param string $password          password for user with saved filters
	 * @param string $table_selector    selector of a table with filtered data
	 */
	public function updateFilterForm($user, $password, $table_selector) {
		$this->page->userLogin($user, $password);
		$this->page->open($this->url)->waitUntilReady();
		$table = $this->query($table_selector)->asTable()->waitUntilPresent()->one();

		// Changing filter data.
		$filter_container = $this->query('xpath://ul[@class="ui-sortable-container ui-sortable"]')->asFilterTab()->one();
		$filter_container->selectTab('update_tab');
		$form = $this->query('id:tabfilter_1')->asForm()->waitUntilReady()->one();
		$result_before = $this->getTableResults($table_selector);

		for ($i = 0; $i < 2; ++$i) {
			$form->fill(['Host groups' => ['Group to check Overview', 'Another group to check Overview']]);

			if ($i === 0) {
				$this->query('name:filter_apply')->one()->click();
				$this->assertFalse($result_before === $this->getTableResults($table_selector));
			}

			$this->query('xpath://a[@aria-label="Home"]')->one()->click();
			$table->waitUntilReloaded();
			$this->assertFalse($this->query('xpath://li[@data-target="tabfilter_1"]//span')->one()->hasClass('display-none'));

			$filter_container->selectTab('update_tab');
			$table->waitUntilReloaded();

			if ($i === 0) {
				$this->query('button:Reset')->one()->click();
				$this->page->waitUntilReady();
				$table->waitUntilReloaded();
			}
			else {
				$this->assertTrue($result_before === $this->getTableResults($table_selector));
				$this->query('button:Update')->one()->click();
				$table->waitUntilReloaded();
			}
		}

		// Getting changed host/problem result and then comparing it with displayed result from dropdown.
		$result = $this->getTableResults($table_selector);
		$this->query('xpath://a[@aria-label="Home"]')->waitUntilClickable()->one()->click();
		$table->waitUntilReloaded();
		$this->query('xpath://button[@data-action="toggleTabsList"]')->one()->click();
		$popup_item = CPopupMenuElement::find()->waitUntilVisible()->one()->getItem('update_tab');
		$this->assertEquals($result, $popup_item->getAttribute('data-counter'));

		// Checking that hosts/problems amount in filter displayed near name at the tab changed.
		$this->assertEquals($result, $this->query('xpath://li[@data-target="tabfilter_1"]/a')
				->one()->getAttribute('data-counter')
		);
	}

	/**
	 * Update filter properties.
	 *
	 * @param string $user        test user with saved filters
	 * @param string $password    password for user with saved filters
	 */
	public function updateFilterProperties($user, $password) {
		$this->page->userLogin($user, $password);
		$this->page->open($this->url)->waitUntilReady();
		$filter_container = $this->query('xpath://ul[@class="ui-sortable-container ui-sortable"]')->asFilterTab()->one();

		// Checking that filter result amount displayed.
		$this->assertTrue($this->query('xpath://li[@data-target="tabfilter_1"]/a[@data-counter]')->exists());
		$filter_container->selectTab('update_tab');

		// Changing filter name to empty space.
		$filter_container->editProperties();
		$this->page->waitUntilReady();
		$dialog = COverlayDialogElement::find()->asForm()->all()->last()->waitUntilReady();
		$dialog->fill(['Name' => '']);
		$dialog->submit();
		$this->assertMessage(TEST_BAD, null, 'Incorrect value for field "filter_name": cannot be empty.');

		// Changing filter name and disabling Show number of records.
		$dialog->fill(['Name' => 'updated_filter_name', 'Show number of records' => false]);
		$dialog->submit();
		$this->page->waitUntilReady();

		// Checking that filter name changed, and result amount not displayed.
		$this->checkName('updated_filter_name');
		$this->query('xpath://li[@data-target="tabfilter_0"]/a')->one()->click();
		$this->assertFalse($this->query('xpath://li[@data-target="tabfilter_1"]/a[@data-counter]')->exists());
	}

	/**
	 * Delete existing filters.
	 *
	 * @param string $user        test user with saved filters
	 * @param string $password    password for user with saved filters
	 */
	public function deleteFilter($user, $password) {
		$this->page->userLogin($user, $password);
		$this->page->open($this->url)->waitUntilReady();
		$filter_container = $this->query('xpath://ul[@class="ui-sortable-container ui-sortable"]')->asFilterTab()->one();

		$filters = $filter_container->getTitles();
		foreach ($filters as $filter) {
			$filter_container->editProperties($filter);
			$dialog = COverlayDialogElement::find()->all()->last()->waitUntilReady();
			$dialog->query('button:Delete')->one()->click();
			$this->page->acceptAlert();
			$this->page->waitUntilReady();
			array_shift($filters);

			// Checking that deleted filter doesn't exist in filters tab list.
			if ($filters !== []) {
				$this->assertEquals($filters, $filter_container->getTitles());
			}
			else {
				$this->assertEquals(null, $filter_container->getTitles());
			}

			// Checking that deleted filter doesn't exist in filters dropdown list.
			$this->assertEquals($filters, $this->getDropdownFilterNames());
		}
	}

	/**
	 * Create filter.
	 *
	 * @param array  $data        given data provider
	 * @param string $user        test user with saved filters
	 * @param string $password    password for user with saved filters
	 */
	public function createFilter($data, $user, $password, $table_selector = 'class:list-table') {
		$this->page->userLogin($user, $password);
		$this->page->open($this->url)->waitUntilReady();

		// Checking if home tab is selected.
		$xpath = 'xpath://li[@data-target="tabfilter_0"]';

		if ($this->query($xpath)->one()->getAttribute('class') === 'tabfilter-item-label') {
			$this->query($xpath.'/a')->waitUntilClickable()->one()->click();
			$this->page->waitUntilReady();
		}

		if (array_key_exists('filter_form', $data)) {
			$home_form = $this->query('xpath://div[@id="tabfilter_0"]/form')->asForm()->one();
			$home_form->fill($data['filter_form']);
		}

		$result_table = $this->query($table_selector)->one();
		$this->query('button:Save as')->one()->click();
		$dialog = COverlayDialogElement::find()->asForm()->all()->last()->waitUntilReady();
		$dialog->fill($data['filter']);
		$dialog->submit();

		if (CTestArrayHelper::get($data, 'expected', TEST_GOOD) === TEST_GOOD) {
			COverlayDialogElement::ensureNotPresent();
			$result_table->waitUntilReloaded();
			$this->page->waitUntilReady();
		}
	}

	/**
	 * Return result amount from table.
	 *
	 * @param string $table_selector    selector of a table with filtered data
	 *
	 * @return int
	 */
	public function getTableResults($table_selector) {
		$table = $this->query($table_selector)->asTable()->waitUntilReady()->one();
		$text = $table->query('xpath:.//tbody/tr/td')->one()->getText();
		$result = ($text === 'No data found.') ? 0 : $table->getRows()->count();

		return $result;
	}

	/**
	 * Return filter names from droplist.
	 *
	 * @return array
	 */
	public function getDropdownFilterNames() {
		$this->query('xpath://button[@data-action="toggleTabsList"]')->one()->click();
		$dropdown_filters = CPopupMenuElement::find()->waitUntilVisible()->one()->getItems()->asText();
		array_shift($dropdown_filters);

		return $dropdown_filters;
	}

	/**
	 * Checking filters name in 3 different places: tab list, droplist, options.
	 *
	 * @param string $filter_name	filter name, that need to be checked in properties, droplist and tab list
	 */
	public function checkName($filter_name) {
		$filter_container = $this->query('xpath://ul[@class="ui-sortable-container ui-sortable"]')->asFilterTab()->waitUntilVisible()->one();

		// Checking that name of filter displayed on the tab.
		$this->assertEquals($filter_name, $filter_container->getSelectedTabName());

		// Checking that names displayed on the filter tabs same as in drop down list.
		$filters = $filter_container->getTitles();
		$this->assertEquals($filters, $this->getDropdownFilterNames());

		// Checking that name displayed in filter properties.
		$filter_container->editProperties();
		$dialog = COverlayDialogElement::find()->asForm()->all()->last()->waitUntilReady();
		$dialog->checkValue(['Name' => $filter_name]);
		$this->query('button:Cancel')->one()->click();
		COverlayDialogElement::ensureNotPresent();
	}
}
