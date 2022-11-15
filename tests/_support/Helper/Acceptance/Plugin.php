<?php
namespace Helper\Acceptance;

/**
 * Helper methods and actions related to the ConvertKit Plugin,
 * which are then available using $I->{yourFunctionName}.
 *
 * @since   1.2.1
 */
class Plugin extends \Codeception\Module
{
	/**
	 * Helper method to activate the ConvertKit Plugin, checking
	 * it activated and no errors were output.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I AcceptanceTester.
	 */
	public function activateConvertKitPlugin($I)
	{
		$I->activateThirdPartyPlugin($I, 'convertkit-gravity-forms');
	}

	/**
	 * Helper method to deactivate the ConvertKit Plugin, checking
	 * it activated and no errors were output.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I AcceptanceTester.
	 */
	public function deactivateConvertKitPlugin($I)
	{
		$I->deactivateThirdPartyPlugin($I, 'convertkit-gravity-forms');
	}

	/**
	 * Helper method to setup the Plugin's API Key and Secret, and enable the integration.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I  AcceptanceTester.
	 */
	public function setupConvertKitPlugin($I)
	{
		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsScreen($I);

		// Complete API Fields.
		$I->fillField('_gform_setting_api_key', $_ENV['CONVERTKIT_API_KEY']);

		// Click the Save Settings button.
		$I->click('#gform-settings-save');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that a message is displayed confirming settings saved.
		$I->seeInSource('Settings updated.');

		// Check the value of the fields match the inputs provided.
		$I->seeInField('_gform_setting_api_key', $_ENV['CONVERTKIT_API_KEY']);
	}

	/**
	 * Helper method to load the Gravity Forms > Settings > ConvertKit screen.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I  AcceptanceTester.
	 */
	public function loadConvertKitSettingsScreen($I)
	{
		$I->amOnAdminPage('admin.php?page=gf_settings&subview=ckgf');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Helper method to reset the ConvertKit Plugin settings, as if it's a clean installation.
	 *
	 * @since   1.2.2
	 *
	 * @param   AcceptanceTester $I AcceptanceTester.
	 */
	public function resetConvertKitPlugin($I)
	{
		// Plugin Settings.
		$I->dontHaveOptionInDatabase('gravityformsaddon_ckgf_settings');
		$I->dontHaveOptionInDatabase('gravityformsaddon_ckgf_version');

		// Review Request.
		$I->dontHaveOptionInDatabase('convertkit-gravity-forms-review-request');
		$I->dontHaveOptionInDatabase('convertkit-gravity-forms-review-dismissed');

		// Users.
		$I->dontHaveUserInDatabase('gravity_forms_user');
	}
}
