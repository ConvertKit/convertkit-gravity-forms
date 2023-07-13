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
		$I->haveOptionInDatabase('rg_gforms_enable_html5', '0');

		// Create Form.
		$gravityFormID = $I->createGravityFormsForm($I);

		// Navigate to Form's settings.
		$I->amOnAdminPage('admin.php?page=gf_edit_forms&view=settings&subview=settings&id=' . $gravityFormID);

		// Confirm the ConvertKit settings section exists.
		$I->seeElementInDOM('#gform-settings-section-convertkit');

		// Confirm a message is displayed telling the user HTML5 output is required.
		$I->seeInSource('HTML5 output is required for proper function. Please enable this in  <a href="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-admin/admin.php?page=gf_settings">Gravity Forms settings.</a>');

		// Create a Page with the Gravity Forms shortcode as its content.
		$pageID = $I->createPageWithGravityFormShortcode($I, $gravityFormID);

		// Load the Page on the frontend site.
		$I->amOnPage('/?p=' . $pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm the recommendations script was not loaded.
		$I->dontSeeInSource('recommendations.js');
	}

	/**
	 * Tests that the 'Enable Creator Network Recommendations' option on a Form's settings
	 * is not displayed when no API Credentials are specified at Forms > Settings > ConvertKit.
	 *
	 * @since   1.3.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testCreatorNetworkRecommendationsOptionWhenNoAPIKeyOrSecret(AcceptanceTester $I)
	{
		// Enable Output HTML5 option.
		$I->haveOptionInDatabase('rg_gforms_enable_html5', '1');

		// Create Form.
		$gravityFormID = $I->createGravityFormsForm($I);

		// Navigate to Form's settings.
		$I->amOnAdminPage('admin.php?page=gf_edit_forms&view=settings&subview=settings&id=' . $gravityFormID);

		// Confirm the ConvertKit settings section exists.
		$I->seeElementInDOM('#gform-settings-section-convertkit');

		// Confirm a message is displayed telling the user to enter their API Key and Secret.
		$I->seeInSource('Please enter your API Key and Secret on the <a href="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-admin/admin.php?page=gf_settings&amp;subview=ckgf">settings screen</a>');

		// Create a Page with the Gravity Forms shortcode as its content.
		$pageID = $I->createPageWithGravityFormShortcode($I, $gravityFormID);

		// Load the Page on the frontend site.
		$I->amOnPage('/?p=' . $pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm the recommendations script was not loaded.
		$I->dontSeeInSource('recommendations.js');
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
		// Enable Output HTML5 option.
		$I->haveOptionInDatabase('rg_gforms_enable_html5', '1');

		// Create Form.
		$gravityFormID = $I->createGravityFormsForm($I);

		// Navigate to Form's settings.
		$I->amOnAdminPage('admin.php?page=gf_edit_forms&view=settings&subview=settings&id=' . $gravityFormID);

		// Confirm the ConvertKit settings section exists.
		$I->seeElementInDOM('#gform-settings-section-convertkit');

		// Confirm a message is displayed telling the user to enter their API Key and Secret.
		$I->seeInSource('Please enter your API Key and Secret on the <a href="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-admin/admin.php?page=gf_settings&amp;subview=ckgf">settings screen</a>');

		// Create a Page with the Gravity Forms shortcode as its content.
		$pageID = $I->createPageWithGravityFormShortcode($I, $gravityFormID);

		// Load the Page on the frontend site.
		$I->amOnPage('/?p=' . $pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm the recommendations script was not loaded.
		$I->dontSeeInSource('recommendations.js');
	}

	/**
	 * Tests that the 'Enable Creator Network Recommendations' option on a Form's settings
	 * is not displayed when invalid API Credentials are specified at Forms > Settings > ConvertKit.
	 *
	 * @since   1.3.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testCreatorNetworkRecommendationsOptionWhenInvalidAPICredentials(AcceptanceTester $I)
	{
		// Enable Output HTML5 option.
		$I->haveOptionInDatabase('rg_gforms_enable_html5', '1');

		// Define invalid API Credentials.


		// Create Form.
		$gravityFormID = $I->createGravityFormsForm($I);

		// Navigate to Form's settings.
		$I->amOnAdminPage('admin.php?page=gf_edit_forms&view=settings&subview=settings&id=' . $gravityFormID);

		// Confirm the ConvertKit settings section exists.
		$I->seeElementInDOM('#gform-settings-section-convertkit');

		// Confirm a message is displayed telling the user to enter their API Key and Secret.
		$I->seeInSource('Please enter your API Key and Secret on the <a href="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-admin/admin.php?page=gf_settings&amp;subview=ckgf">settings screen</a>');

		// Create a Page with the Gravity Forms shortcode as its content.
		$pageID = $I->createPageWithGravityFormShortcode($I, $gravityFormID);

		// Load the Page on the frontend site.
		$I->amOnPage('/?p=' . $pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm the recommendations script was not loaded.
		$I->dontSeeInSource('recommendations.js');
	}

	/**
	 * Tests that the 'Enable Creator Network Recommendations' option on a Form's settings
	 * is not displayed when valid API Credentials are specified at Forms > Settings > ConvertKit,
	 * but the ConvertKit account does not have the Creator Network enabled.
	 *
	 * @since   1.3.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testCreatorNetworkRecommendationsOptionWhenDisabledOnConvertKitAccount(AcceptanceTester $I)
	{

	}

	/**
	 * Tests that the 'Enable Creator Network Recommendations' option on a Form's settings
	 * is displayed and saves correctly when valid API Credentials are specified at Forms > Settings > ConvertKit,
	 * and the ConvertKit account has the Creator Network enabled.
	 *
	 * @since   1.3.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testCreatorNetworkRecommendations(AcceptanceTester $I)
	{

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
