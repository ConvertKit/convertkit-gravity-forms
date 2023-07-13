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
		$I->setupConvertKitPlugin($I);
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

	}

	/**
	 * Tests that the 'Enable Creator Network Recommendations' option on a Form's settings
	 * is not displayed when no API Credentials are specified at Forms > Settings > ConvertKit.
	 *
	 * @since   1.3.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testCreatorNetworkRecommendationsOptionWhenNoAPICredentials(AcceptanceTester $I)
	{

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
