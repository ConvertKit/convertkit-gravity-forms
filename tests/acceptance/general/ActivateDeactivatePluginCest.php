<?php

class ActivateDeactivatePluginCest
{
	/**
	 * Test that activating the Plugin and the Gravity Forms Plugins works
	 * with no errors.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I  Tester
	 */
	public function testPluginActivationDeactivation(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
		$I->activateThirdPartyPlugin($I, 'gravity-forms');
		$I->deactivateConvertKitPlugin($I);
		$I->deactivateThirdPartyPlugin($I, 'gravity-forms');
	}

	/**
	 * Test that activating the Plugin, without activating the Gravity Forms Plugin, works
	 * with no errors.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I  Tester
	 */
	public function testPluginActivationDeactivationWithoutGravityForms(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
		$I->deactivateConvertKitPlugin($I);

	}
}
