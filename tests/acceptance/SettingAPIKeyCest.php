<?php
/**
 * Tests the API Key Setting.
 * 
 * @since 	1.2.1
 */
class SettingAPIKeyCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateGravityFormsAndConvertKitPlugins($I);
	}

	/**
	 * Test that no PHP errors or notices are displayed on the Plugin's Setting screen when the Update Settings
	 * button is pressed and no settings are specified at Gravity Forms > Settings > ConvertKit.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testSaveBlankSettings(AcceptanceTester $I)
	{
		// Load Settings screen.
		$I->loadConvertKitSettingsScreen($I);

		// Click the Update Settings button.
		$I->click('Update Settings');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that a message is displayed telling the user that the API Credentials are invalid.
		$I->seeInSource('Your ConvertKit API Key appears to be invalid. Please double check the value.');
	}

	/**
	 * Test that no PHP errors or notices are displayed on the Plugin's Setting screen,
	 * and no warning is displayed that the supplied API credentials are invalid, when
	 * saving valid API credentials at WooCommerce > Settings > Integration > ConvertKit.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testSaveValidAPICredentials(AcceptanceTester $I)
	{
		// Enable Integration and define its API Keys.
		$I->setupConvertKitPlugin($I);

		// Confirm that a message is not displayed telling the user that the API Credentials are invalid.
		$I->dontSeeInSource('Your ConvertKit API Key appears to be invalid. Please double check the value.');
	}

	/**
	 * Test that no PHP errors or notices are displayed on the Plugin's Setting screen,
	 * and a warning is displayed that the supplied API credentials are invalid, when
	 * saving invalid API credentials at WooCommerce > Settings > Integration > ConvertKit.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testSaveInvalidAPICredentials(AcceptanceTester $I)
	{
		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsScreen($I);

		// Complete API Fields.
		$I->fillField('_gaddon_setting_api_key', 'invalidApiKey');
		
		// Click the Update Settings button.
		$I->click('Update Settings');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the value of the fields match the inputs provided.
		$I->seeInField('_gaddon_setting_api_key', 'invalidApiKey');

		// Confirm that a message is displayed telling the user that the API Credentials are invalid.
		$I->seeInSource('Your ConvertKit API Key appears to be invalid. Please double check the value.');
	}
}
