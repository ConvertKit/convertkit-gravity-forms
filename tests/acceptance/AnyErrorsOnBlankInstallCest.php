<?php

class AnyErrorsOnBlankInstallCest
{
	/**
	 * Check that no PHP errors or notices are displayed at Gravity Forms > Settings > ConvertKit, when the Plugin is activated
	 * and not configured.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testSettingsScreen(AcceptanceTester $I)
	{
		// Activate Plugins.
		$I->activateConvertKitPlugin($I);
		$I->activateThirdPartyPlugin($I, 'gravity-forms');

		// Go to the Plugin's Settings > General Screen.
		$I->loadConvertKitSettingsScreen($I);
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 * 
	 * @since 	1.2.2
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}