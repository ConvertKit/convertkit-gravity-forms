<?php
namespace Helper\Acceptance;

/**
 * Helper methods and actions related to the Gravity Forms Plugin,
 * which are then available using $I->{yourFunctionName}.
 *
 * @since   1.2.1
 */
class GravityForms extends \Codeception\Module
{
	/**
	 * Enables HTML5 Output in Gravity Forms.
	 *
	 * @since   1.3.7
	 *
	 * @param   AcceptanceTester $I     AcceptanceTester.
	 */
	public function enableGravityFormsHTML5Output($I)
	{
		$I->haveOptionInDatabase('rg_gforms_enable_html5', '1');
	}

	/**
	 * Disables HTML5 Output in Gravity Forms.
	 *
	 * @since   1.3.7
	 *
	 * @param   AcceptanceTester $I     AcceptanceTester.
	 */
	public function disableGravityFormsHTML5Output($I)
	{
		$I->haveOptionInDatabase('rg_gforms_enable_html5', '0');
	}

	/**
	 * Check that the Form Settings screen for the given Gravity Form has a ConvertKit
	 * section registered, displaying the given message.
	 *
	 * @since   1.3.7
	 *
	 * @param   AcceptanceTester $I                 AcceptanceTester.
	 * @param   int              $gravityFormID     Gravity Forms Form ID.
	 * @param   string           $message           Message.
	 */
	public function seeGravityFormsSettingMessage($I, $gravityFormID, $message)
	{
		// Navigate to Form's settings.
		$I->amOnAdminPage('admin.php?page=gf_edit_forms&view=settings&subview=settings&id=' . $gravityFormID);

		// Confirm the ConvertKit settings section exists.
		$I->seeElementInDOM('#gform-settings-section-convertkit');

		// Confirm a message is displayed telling the user HTML5 output is required.
		$I->seeInSource($message);
	}

	/**
	 * Check that enabling the Creator Network Recommendations on the Form Settings screen
	 * for the given Gravity Form works.
	 *
	 * @since   1.3.7
	 *
	 * @param   AcceptanceTester $I                 AcceptanceTester.
	 * @param   int              $gravityFormID     Gravity Forms Form ID.
	 */
	public function enableGravityFormsSettingCreatorNetworkRecommendations($I, $gravityFormID)
	{
		// Navigate to Form's settings.
		$I->amOnAdminPage('admin.php?page=gf_edit_forms&view=settings&subview=settings&id=' . $gravityFormID);

		// Confirm the ConvertKit settings section exists.
		$I->seeElementInDOM('#gform-settings-section-convertkit');

		// Enable Creator Network Recommendations.
		$I->click('label[for="_gform_setting_ckgf_enable_creator_network_recommendations"]');

		// Save settings.
		$I->click('#gform-settings-save');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm settings saved.
		$I->seeCheckboxIsChecked('#_gform_setting_ckgf_enable_creator_network_recommendations');
	}

	/**
	 * Creates a Gravity Forms Form.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I     AcceptanceTester.
	 * @return  int                     Form ID.
	 */
	public function createGravityFormsForm($I)
	{
		// Navigate to Forms > New Form.
		$I->amOnAdminPage('admin.php?page=gf_new_form');

		// Select Blank Form.
		$I->waitForElementVisible('.gform-dialog__content');
		$I->click('Blank Form');

		// Define Title.
		$I->waitForElementVisible('#template-library-form-title-input');
		$I->fillField('#template-library-form-title-input', 'ConvertKit Form Test: ' . date('Y-m-d H:i:s') . ' on PHP ' . PHP_VERSION_ID);

		// Press enter key to create the form.
		$I->pressKey('#template-library-form-title-input', \Facebook\WebDriver\WebDriverKeys::ENTER);

		// Wait for the Form Edit screen to load.
		$I->waitForElementVisible('#no-fields');

		// Open Advanced Fields Panel.
		$I->click('button[aria-controls="add_advanced_fields"]');
		$I->wait(2);

		// Add Name Field.
		$I->click('#add_advanced_fields button[data-type="name"]');
		$I->wait(2);

		// Add Email Field.
		$I->click('#add_advanced_fields button[data-type="email"]');
		$I->wait(2);

		// Add Text Field.
		$I->click('#add_standard_fields button[data-type="text"]');
		$I->wait(2);

		// Add Textarea Field.
		$I->click('#add_standard_fields button[data-type="textarea"]');
		$I->wait(2);

		// Add Select Field.
		$I->click('#add_standard_fields button[data-type="select"]');
		$I->wait(2);

		// Click the Select Field.
		$I->click('#field_6');
		$I->wait(2);

		$I->fillField('#field_label', 'Tag');

		// Open Choices flyout.
		$I->click('button.choices-ui__trigger');
		$I->wait(2);

		// Show Values.
		$I->click('#choices-ui-flyout .gform-flyout__body label[for=field_choice_values_enabled]');

		// Define Tags.
		$I->fillField('#select_choice_text_0', 'Select Tag');
		$I->fillField('#select_choice_value_0', '');

		$I->fillField('#select_choice_text_1', 'Tag Label 1');
		$I->fillField('#select_choice_value_1', $_ENV['CONVERTKIT_API_ADDITIONAL_TAG_NAME']);

		$I->fillField('#select_choice_text_2', 'Tag Label 2');
		$I->fillField('#select_choice_value_2', 'fakeTagNotInConvertKit');

		// Close Choices flyout.
		$I->click('#choices-ui-flyout .gform-flyout__close');

		// Update.
		$I->click('button.update-form');

		// Return Form ID.
		return (int) $I->grabFromCurrentUrl('~(\d+)$~');
	}

	/**
	 * Creates a Gravity Forms ConvertKit Feed (a feed sends form entries to ConvertKit) for the given Gravity Form.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I                 AcceptanceTester.
	 * @param   int              $gravityFormID     Gravity Forms Form ID.
	 * @param   string           $formName          ConvertKit Form Name.
	 * @param   mixed            $tagName           ConvertKit Tag Name.
	 * @param   bool             $mapTagField       Whether to map the Tag field.
	 * @param   mixed            $emailFieldName    Gravity Forms Field Name to map to ConvertKit Email Address.
	 * @param   mixed            $conditionalLogic  Whether to configure conditional logic on the feed.
	 */
	public function createGravityFormsFeed($I, $gravityFormID, $formName, $tagName = false, $mapTagField = false, $emailFieldName = 'Email', $conditionalLogic = false)
	{
		// Navigate to Form's Settings > ConvertKit.
		$I->amOnAdminPage('admin.php?page=gf_edit_forms&view=settings&subview=ckgf&id=' . $gravityFormID);

		// Click Add New.
		$I->click('#gform-settings div.tablenav.top div.alignright a.button');

		// Complete Feed's Form Fields.
		$I->completeGravityFormsFeedFields($I, $formName, $tagName, $mapTagField, $emailFieldName, $conditionalLogic);

		// Click Save Settings.
		$I->click('#gform-settings-save');

		// Confirm Feed Settings saved successfully.
		$I->seeInSource('Settings updated.');

		// Confirm Feed Fields contain saved values.
		$I->seeInField('_gform_setting_feed_name', 'ConvertKit Feed');
		$I->seeOptionIsSelected('_gform_setting_form_id', $formName);
		if ($tagName) {
			$I->seeOptionIsSelected('_gform_setting_tag_id', $tagName);
		}
		$I->seeOptionIsSelected('#_gform_setting_field_map_e', $emailFieldName);
		$I->seeOptionIsSelected('#_gform_setting_field_map_n', 'Name (First)');
		$I->seeOptionIsSelected('#_gform_setting_convertkit_custom_fields_custom_key_0', 'Last Name');
		$I->seeOptionIsSelected('#_gform_setting_convertkit_custom_fields_custom_value_0', 'Name (Last)');
		if ($mapTagField) {
			$I->seeOptionIsSelected('#_gform_setting_field_map_tag', 'Tag');
		} else {
			$I->seeOptionIsSelected('#_gform_setting_field_map_tag', 'Select a Field');
		}
	}

	/**
	 * Completes form fields for a Gravity Forms ConvertKit Feed Settings.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I                 AcceptanceTester.
	 * @param   string           $formName          ConvertKit Form Name.
	 * @param   mixed            $tagName           ConvertKit Tag Name.
	 * @param   bool             $mapTagField       Whether to map the Tag field.
	 * @param   mixed            $emailFieldName    Gravity Forms Field Name to map to ConvertKit Email Address.
	 * @param   mixed            $conditionalLogic  Whether to configure conditional logic on the feed.
	 */
	public function completeGravityFormsFeedFields($I, $formName, $tagName = false, $mapTagField = false, $emailFieldName = 'Email', $conditionalLogic = false)
	{
		// Check ConvertKit Form option exists and is populated.
		$I->seeElementInDOM('select[name="_gform_setting_form_id"]');

		// Define Feed Name.
		$I->fillField('_gform_setting_feed_name', 'ConvertKit Feed');

		// Check Forms are displayed in alphabetical order.
		$I->checkSelectFormOptionOrder($I, '#form_id');

		// Define ConvertKit Form to send entries to.
		$I->selectOption('_gform_setting_form_id', $formName);

		// Check Tags are displayed in alphabetical order.
		$I->checkSelectTagOptionOrder(
			$I,
			'#tag_id',
			[
				'(No Tag)',
			]
		);

		// Define ConvertKit Tag to apply to subscribers.
		if ($tagName) {
			$I->selectOption('_gform_setting_tag_id', $tagName);
		}

		// Map Email Field.
		$I->selectOption('#_gform_setting_field_map_e', $emailFieldName);

		// Map Name Field.
		$I->selectOption('#_gform_setting_field_map_n', 'Name (First)');

		// Map Tag Field.
		if ($mapTagField) {
			$I->selectOption('#_gform_setting_field_map_tag', 'Tag');
		} else {
			$I->selectOption('#_gform_setting_field_map_tag', 'Select a Field');
		}

		// Check Custom Fields are displayed in alphabetical order.
		$I->checkSelectCustomFieldOptionOrder(
			$I,
			'#_gform_setting_convertkit_custom_fields_custom_key_0',
			[
				'Select a Field',
			]
		);

		// Map ConvertKit Account Custom Field 'Last Name'.
		$I->selectOption('#_gform_setting_convertkit_custom_fields_custom_key_0', 'Last Name');
		$I->selectOption('#_gform_setting_convertkit_custom_fields_custom_value_0', 'Name (Last)');

		// Configure conditional logic if enabled.
		if ($conditionalLogic) {
			$I->checkOption('#feed_condition_conditional_logic');
			$I->selectOption('#feed_condition_rule_field_0', $conditionalLogic['field']);
			$I->fillField('#feed_condition_rule_value_0', $conditionalLogic['value']);
		}
	}

	/**
	 * Disables the given feed for the given Gravity Form ID.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I                  AcceptanceTester.
	 * @param   int              $gravityFormID      Gravity Forms Form ID.
	 * @param   int              $gravityFormFeedID  Gravity Forms Feed ID.
	 */
	public function disableGravityFormsFeed($I, $gravityFormID, $gravityFormFeedID = 1)
	{
		// Load Form's ConvertKit Feed Settings.
		$I->amOnAdminPage('admin.php?page=gf_edit_forms&view=settings&subview=ckgf&id=' . $gravityFormID);

		// Deactivate the Feed.
		$I->click('table.feeds tbody tr:nth-child(' . $gravityFormFeedID . ') td.manage-column button.gform-status--active');
	}

	/**
	 * Creates a WordPress Page with the Gravity Form shortcode as the content
	 * to render the Gravity Form.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I              AcceptanceTester.
	 * @param   int              $gravityFormID  Gravity Forms Form ID.
	 * @param   bool             $ajax           Enable AJAX on Form Submission.
	 * @return  int                                 Page ID
	 */
	public function createPageWithGravityFormShortcode($I, $gravityFormID, $ajax = false)
	{
		return $I->havePostInDatabase(
			[
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_name'    => 'gravity-form-' . $gravityFormID,
				'post_title'   => 'Gravity Form #' . $gravityFormID,
				'post_content' => '[gravityform id="' . $gravityFormID . '" title="false" description="false" ajax="' . ( $ajax ? 'true' : 'false' ) . '"]',
			]
		);
	}

	/**
	 * Checks that a success or error note was added by the Plugin to the most recent Gravity Forms Entry.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I AcceptanceTester.
	 */
	public function checkGravityFormsNotesExist($I)
	{
		$I->loginAsAdmin();
		$I->amOnAdminPage('admin.php?page=gf_entries');
		$I->click('table.gf_entries tbody tr.entry_row:first-child a[aria-label="View this entry"]');
		$I->seeElementInDOM('#notes div[data-type="ckgf"]');

		// Confirm that the ConvertKit logo is displayed beside the note.
		$I->seeInSource('<img alt="ConvertKit" src="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-content/plugins/convertkit-gravity-forms/resources/backend/images/block-icon-form.png" class="avatar avatar-48" height="48" width="48">');
	}

	/**
	 * Checks that a success note was added by the Plugin to the most recent Gravity Forms Entry.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I AcceptanceTester.
	 */
	public function checkGravityFormsSuccessNotesExist($I)
	{
		$I->loginAsAdmin();
		$I->amOnAdminPage('admin.php?page=gf_entries');
		$I->click('table.gf_entries tbody tr.entry_row:first-child a[aria-label="View this entry"]');
		$I->seeElementInDOM('#notes div[data-type="ckgf"][data-sub-type="success"]');

		// Confirm that the ConvertKit logo is displayed beside the note.
		$I->seeInSource('<img alt="ConvertKit" src="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-content/plugins/convertkit-gravity-forms/resources/backend/images/block-icon-form.png" class="avatar avatar-48" height="48" width="48">');
	}

	/**
	 * Checks that an error note was added by the Plugin to the most recent Gravity Forms Entry.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I AcceptanceTester.
	 */
	public function checkGravityFormsErrorNotesExist($I)
	{
		$I->loginAsAdmin();
		$I->amOnAdminPage('admin.php?page=gf_entries');
		$I->click('table.gf_entries tbody tr.entry_row:first-child a[aria-label="View this entry"]');
		$I->seeElementInDOM('#notes div[data-type="ckgf"][data-sub-type="error"]');

		// Confirm that the ConvertKit logo is displayed beside the note.
		$I->seeInSource('<img alt="ConvertKit" src="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-content/plugins/convertkit-gravity-forms/resources/backend/images/block-icon-form.png" class="avatar avatar-48" height="48" width="48">');
	}

	/**
	 * Checks that no Notes were added by the Plugin to the most recent Gravity Forms Entry.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I AcceptanceTester.
	 */
	public function checkGravityFormsNotesDoNotExist($I)
	{
		$I->loginAsAdmin();
		$I->amOnAdminPage('admin.php?page=gf_entries');
		$I->click('table.gf_entries tbody tr.entry_row:first-child a[aria-label="View this entry"]');
		$I->dontSeeElementInDOM('#notes div[data-type="ckgf"]');
	}

	/**
	 * Creates a role called 'gravity_forms'.
	 *
	 * If the role exists, deletes it before creating.
	 *
	 * @since   1.2.5
	 *
	 * @param   AcceptanceTester $I          Tester.
	 * @param   bool             $settings   Role has access to Plugin's Settings.
	 * @param   bool             $form       Role has access to Plugin's Form Settings.
	 * @param   bool             $uninstall  Role has access to Plugin's Uninstallation action.
	 */
	public function createGravityFormsRole($I, $settings = true, $form = true, $uninstall = true)
	{
		$I->deleteRole($I, 'gravity_forms');
		$I->addRole(
			$I,
			'gravity_forms',
			[
				// General.
				'edit_dashboard'                => true,
				'read'                          => true,

				// Gravity Forms.
				'gravityforms_api_settings'     => true,
				'gravityforms_create_form'      => true,
				'gravityforms_delete_entries'   => true,
				'gravityforms_delete_forms'     => true,
				'gravityforms_edit_entries'     => true,
				'gravityforms_edit_entry_notes' => true,
				'gravityforms_edit_forms'       => true,
				'gravityforms_edit_settings'    => true,
				'gravityforms_export_entries'   => true,
				'gravityforms_logging'          => true,
				'gravityforms_preview_forms'    => true,
				'gravityforms_system_status'    => true,
				'gravityforms_uninstall'        => true,
				'gravityforms_view_addons'      => true,
				'gravityforms_view_entries'     => true,
				'gravityforms_view_entry_notes' => true,
				'gravityforms_view_settings'    => true,
				'gravityforms_view_updates'     => true,

				// Plugin.
				'ckgf_convertkit_settings_page' => $settings,
				'ckgf_convertkit_form_page'     => $form,
				'ckgf_convertkit_uninstall'     => $uninstall,
			]
		);
	}
}
