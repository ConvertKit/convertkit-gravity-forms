<?php
/**
 * Tests the Settings at Forms > Settings > ConvertKit.
 *
 * @since   1.2.1
 */
class SettingCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
		$I->activateThirdPartyPlugin($I, 'gravity-forms');
	}

	/**
	 * Test that no PHP errors or notices are displayed on the Plugin's Setting screen when the Update Settings
	 * button is pressed and no settings are specified at Gravity Forms > Settings > ConvertKit.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSaveBlankSettings(AcceptanceTester $I)
	{
		// Load Settings screen.
		$I->loadConvertKitSettingsScreen($I);

		// Click the Save Settings button.
		$I->click('#gform-settings-save');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that a message is displayed telling the their API key is not present.
		$I->seeInSource('Authorization Failed: API Key not present');
	}

	/**
	 * Test that no PHP errors or notices are displayed on the Plugin's Setting screen,
	 * and no warning is displayed that the supplied API credentials are invalid, when
	 * saving valid API credentials at Forms > Settings > ConvertKit.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSaveValidAPIKeyAndSecret(AcceptanceTester $I)
	{
		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsScreen($I);

		// Complete API Fields.
		$I->fillField('_gform_setting_api_key', $_ENV['CONVERTKIT_API_KEY']);
		$I->fillField('_gform_setting_api_secret', $_ENV['CONVERTKIT_API_SECRET']);

		// Click the Save Settings button.
		$I->click('#gform-settings-save');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that a message is displayed confirming settings saved.
		$I->seeInSource('Settings updated.');

		// Check the value of the fields match the inputs provided.
		$I->seeInField('_gform_setting_api_key', $_ENV['CONVERTKIT_API_KEY']);
		$I->seeInField('_gform_setting_api_secret', $_ENV['CONVERTKIT_API_SECRET']);
	}

	/**
	 * Test that no PHP errors or notices are displayed on the Plugin's Setting screen,
	 * and a warning is displayed that the supplied API Key is invalid, when
	 * saving an invalid API Key at Forms > Settings > ConvertKit.
	 *
	 * @since   1.3.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSaveInvalidAPIKey(AcceptanceTester $I)
	{
		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsScreen($I);

		// Complete API Fields.
		$I->fillField('_gform_setting_api_key', 'invalidApiKey');

		// Click the Save Settings button.
		$I->click('#gform-settings-save');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the value of the fields match the inputs provided.
		$I->seeInField('_gform_setting_api_key', 'invalidApiKey');

		// Confirm that a message is displayed telling the user that an error occured whilst saving.
		$I->seeInSource('There was an error while saving your settings.');

		// Confirm that a tooltip notification exists with the precise API error.
		$I->seeInSource('Authorization Failed: API Key not valid');
	}

	/**
	 * Test that no PHP errors or notices are displayed on the Plugin's Setting screen,
	 * and a warning is displayed that the supplied API Secret is invalid, when
	 * saving an invalid API Secret at Forms > Settings > ConvertKit.
	 *
	 * @since   1.3.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSaveInvalidAPISecret(AcceptanceTester $I)
	{
		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsScreen($I);

		// Complete API Fields.
		$I->fillField('_gform_setting_api_secret', 'invalidApiSecret');

		// Click the Save Settings button.
		$I->click('#gform-settings-save');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the value of the fields match the inputs provided.
		$I->seeInField('_gform_setting_api_secret', 'invalidApiSecret');

		// Confirm that a message is displayed telling the user that an error occured whilst saving.
		$I->seeInSource('There was an error while saving your settings.');

		// Confirm that a tooltip notification exists with the precise API error.
		$I->seeInSource('Authorization Failed: API Key not valid');
	}

	/**
	 * Test that no PHP errors or notices are displayed on the Plugin's Setting screen when the Debug option
	 * is enabled and disabled, and that the setting is honored.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testEnableAndDisableDebug(AcceptanceTester $I)
	{
		// Enable Integration and define its API Keys.
		$I->setupConvertKitPlugin($I);

		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsScreen($I);

		// Check Debug option.
		$I->checkOption('#debug');

		// Click the Save Settings button.
		$I->click('#gform-settings-save');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the checkbox is checked.
		$I->seeCheckboxIsChecked('#debug');

		// Untick field.
		$I->uncheckOption('#debug');

		// Click the Save Settings button.
		$I->click('#gform-settings-save');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the field remains unticked.
		$I->dontSeeCheckboxIsChecked('#debug');
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   1.2.2
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->deactivateThirdPartyPlugin($I, 'gravity-forms');
		$I->resetConvertKitPlugin($I);
	}
}
