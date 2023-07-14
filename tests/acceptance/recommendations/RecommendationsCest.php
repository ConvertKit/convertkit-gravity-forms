<?php
/**
 * Tests that the Creator Network Recommendations settings work with a Gravity Form
 *
 * @since   1.3.7
 */
class RecommendationsCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   1.3.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
		$I->activateThirdPartyPlugin($I, 'gravity-forms');
	}

	/**
	 * Tests that the 'Enable Creator Network Recommendations' option on a Form's settings
	 * is not displayed when Gravity Forms 'Output HTML5' is disabled.
	 *
	 * @since   1.3.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testCreatorNetworkRecommendationsOptionWhenOutputHTML5Disabled(AcceptanceTester $I)
	{
		// Disable Output HTML5 option.
		$I->disableGravityFormsHTML5Output($I);

		// Create Form.
		$gravityFormID = $I->createGravityFormsForm($I);

		// Confirm that the Form Settings display the expected error message.
		$I->seeGravityFormsSettingMessage(
			$I,
			$gravityFormID,
			'HTML5 output is required for proper function. Please enable this in  <a href="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-admin/admin.php?page=gf_settings">Gravity Forms settings.</a>'
		);

		// Create a Page with the Gravity Forms shortcode as its content.
		$pageID = $I->createPageWithGravityFormShortcode($I, $gravityFormID);

		// Confirm the recommendations script was not loaded.
		$I->dontSeeCreatorNetworkRecommendationsScript($I, $pageID);
	}

	/**
	 * Tests that the 'Enable Creator Network Recommendations' option on a Form's settings
	 * is not displayed when no API Key and Secret are specified at Forms > Settings > ConvertKit.
	 *
	 * @since   1.3.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testCreatorNetworkRecommendationsOptionWhenNoAPIKeyOrSecret(AcceptanceTester $I)
	{
		// Enable Output HTML5 option.
		$I->enableGravityFormsHTML5Output($I);

		// Create Form.
		$gravityFormID = $I->createGravityFormsForm($I);

		// Confirm that the Form Settings display the expected error message.
		$I->seeGravityFormsSettingMessage(
			$I,
			$gravityFormID,
			'Please enter your API Key and Secret on the <a href="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-admin/admin.php?page=gf_settings&amp;subview=ckgf">settings screen</a>'
		);

		// Create a Page with the Gravity Forms shortcode as its content.
		$pageID = $I->createPageWithGravityFormShortcode($I, $gravityFormID);

		// Confirm the recommendations script was not loaded.
		$I->dontSeeCreatorNetworkRecommendationsScript($I, $pageID);
	}

	/**
	 * Tests that the 'Enable Creator Network Recommendations' option on a Form's settings
	 * is not displayed when no API Secret is specified at Forms > Settings > ConvertKit.
	 *
	 * This handles users upgrading from < 1.3.7, where no API Secret setting was provided.
	 *
	 * @since   1.3.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testCreatorNetworkRecommendationsOptionWhenNoAPISecret(AcceptanceTester $I)
	{
		// Setup Plugin with just the API Key, as if we're upgrading from < 1.3.7.
		$I->setupConvertKitPlugin($I, $_ENV['CONVERTKIT_API_KEY'], '', false);

		// Enable Output HTML5 option.
		$I->enableGravityFormsHTML5Output($I);

		// Create Form.
		$gravityFormID = $I->createGravityFormsForm($I);

		// Confirm that the Form Settings display the expected error message.
		$I->seeGravityFormsSettingMessage(
			$I,
			$gravityFormID,
			'Please enter your API Key and Secret on the <a href="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-admin/admin.php?page=gf_settings&amp;subview=ckgf">settings screen</a>'
		);

		// Create a Page with the Gravity Forms shortcode as its content.
		$pageID = $I->createPageWithGravityFormShortcode($I, $gravityFormID);

		// Confirm the recommendations script was not loaded.
		$I->dontSeeCreatorNetworkRecommendationsScript($I, $pageID);
	}

	/**
	 * Tests that the 'Enable Creator Network Recommendations' option on a Form's settings
	 * is not displayed when invalid API Key and Secret are specified at Forms > Settings > ConvertKit.
	 *
	 * @since   1.3.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testCreatorNetworkRecommendationsOptionWhenInvalidAPIKeyAndSecret(AcceptanceTester $I)
	{
		// Setup Plugin with invalid API Key and Secret.
		$I->setupConvertKitPlugin($I, 'fakeApiKey', 'fakeApiSecret');

		// Enable Output HTML5 option.
		$I->enableGravityFormsHTML5Output($I);

		// Create Form.
		$gravityFormID = $I->createGravityFormsForm($I);

		// Confirm that the Form Settings display the expected error message.
		$I->seeGravityFormsSettingMessage(
			$I,
			$gravityFormID,
			'Authorization Failed: API Secret not valid. <a href="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-admin/admin.php?page=gf_settings&amp;subview=ckgf">Fix settings</a>'
		);

		// Create a Page with the Gravity Forms shortcode as its content.
		$pageID = $I->createPageWithGravityFormShortcode($I, $gravityFormID);

		// Confirm the recommendations script was not loaded.
		$I->dontSeeCreatorNetworkRecommendationsScript($I, $pageID);
	}

	/**
	 * Tests that the 'Enable Creator Network Recommendations' option on a Form's settings
	 * is not displayed when valid API Key and Secret are specified at Forms > Settings > ConvertKit,
	 * but the ConvertKit account does not have the Creator Network enabled.
	 *
	 * @since   1.3.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testCreatorNetworkRecommendationsOptionWhenDisabledOnConvertKitAccount(AcceptanceTester $I)
	{
		// Setup Plugin with API Key and Secret for ConvertKit Account that does not have the Creator Network enabled.
		$I->setupConvertKitPlugin($I, $_ENV['CONVERTKIT_API_KEY_NO_DATA'], $_ENV['CONVERTKIT_API_SECRET_NO_DATA']);

		// Enable Output HTML5 option.
		$I->enableGravityFormsHTML5Output($I);

		// Create Form.
		$gravityFormID = $I->createGravityFormsForm($I);

		// Confirm that the Form Settings display the expected error message.
		$I->seeGravityFormsSettingMessage(
			$I,
			$gravityFormID,
			'Creator Network Recommendations requires a <a href="https://app.convertkit.com/account_settings/billing/?utm_source=wordpress&amp;utm_content=convertkit-gravity-forms">paid ConvertKit Plan</a>'
		);

		// Create a Page with the Gravity Forms shortcode as its content.
		$pageID = $I->createPageWithGravityFormShortcode($I, $gravityFormID);

		// Confirm the recommendations script was not loaded.
		$I->dontSeeCreatorNetworkRecommendationsScript($I, $pageID);
	}

	/**
	 * Tests that the 'Enable Creator Network Recommendations' option on a Form's settings
	 * is displayed and saves correctly when valid API Key and Secret are specified at Forms > Settings > ConvertKit,
	 * and the ConvertKit account has the Creator Network enabled.  Viewing and submitting the Form does not
	 * display the Creator Network Recommendations modal, because the form submission will reload the page,
	 * which isn't supported right now.
	 *
	 * @since   1.3.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testCreatorNetworkRecommendationsWithAJAXDisabled(AcceptanceTester $I)
	{
		// Setup Plugin.
		$I->setupConvertKitPlugin($I);

		// Enable Output HTML5 option.
		$I->enableGravityFormsHTML5Output($I);

		// Create Form.
		$gravityFormID = $I->createGravityFormsForm($I);

		// Enable Creator Network Recommendations on the form's settings.
		$I->enableGravityFormsSettingCreatorNetworkRecommendations($I, $gravityFormID);

		// Create a Page with the Gravity Forms shortcode as its content.
		$pageID = $I->createPageWithGravityFormShortcode($I, $gravityFormID);

		// Confirm the recommendations script was not loaded, because AJAX was disabled on the form.
		$I->dontSeeCreatorNetworkRecommendationsScript($I, $pageID);
	}

	/**
	 * Tests that the 'Enable Creator Network Recommendations' option on a Form's settings
	 * is displayed and saves correctly when valid API Key and Secret are specified at Forms > Settings > ConvertKit,
	 * and the ConvertKit account has the Creator Network enabled.  Viewing and submitting the Form then correctly
	 * displays the Creator Network Recommendations modal.
	 *
	 * @since   1.3.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testCreatorNetworkRecommendationsWithAJAXEnabled(AcceptanceTester $I)
	{
		// Setup Plugin.
		$I->setupConvertKitPlugin($I);

		// Enable Output HTML5 option.
		$I->enableGravityFormsHTML5Output($I);

		// Create Form.
		$gravityFormID = $I->createGravityFormsForm($I);

		// Enable Creator Network Recommendations on the form's settings.
		$I->enableGravityFormsSettingCreatorNetworkRecommendations($I, $gravityFormID);

		// Create a Page with the Gravity Forms shortcode as its content.
		$pageID = $I->createPageWithGravityFormShortcode($I, $gravityFormID, true);

		// Confirm the recommendations script was loaded.
		$I->seeCreatorNetworkRecommendationsScript($I, $pageID);

		// Complete Form Fields.
		$I->fillField('.name_first input[type=text]', 'First');
		$I->fillField('.name_last input[type=text]', 'Last');
		$I->fillField('.ginput_container_email input[type=email]', $I->generateEmailAddress());

		// Submit Form.
		$I->click('Submit');

		// Wait for Creator Network Recommendations modal to display.
		$I->waitForElementVisible('.formkit-modal');
		$I->switchToIFrame('.formkit-modal iframe');
		$I->waitForElementVisible('div[data-component="Page"]');
		$I->switchToIFrame();

		// Close the modal.
		$I->click('.formkit-modal button.formkit-close');

		// Confirm that the underlying Gravity Form submitted successfully.
		$I->waitForElementNotVisible('.formkit-modal');
		$I->seeElementInDOM('.gform_confirmation_message');
		$I->see('Thanks for contacting us! We will get in touch with you shortly.');
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   1.3.7
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
