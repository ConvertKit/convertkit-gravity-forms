<?php

class ActivateDeactivatePluginCest
{
	/**
	 * Activate the Plugin and confirm a success notification
	 * is displayed with no errors.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testPluginActivation(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
		$I->activateThirdPartyPlugin($I, 'gravity-forms');
	}

	/**
	 * Activate the Plugin without the Gravity Forms Plugin and confirm a success notification
	 * is displayed with no errors.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testPluginActivationWithoutGravityForms(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
	}

	/**
	 * Deactivate the Plugin and confirm a success notification
	 * is displayed with no errors.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testPluginDeactivation(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
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