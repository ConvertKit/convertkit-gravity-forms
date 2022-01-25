<?php

class AnyErrorsOnBlankInstallCest
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
	}

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
		// Activate Plugin.
		$I->activateGravityFormsAndConvertKitPlugins($I);

		// Go to the Plugin's Settings > General Screen.
		$I->loadConvertKitSettingsScreen($I);
	}
}