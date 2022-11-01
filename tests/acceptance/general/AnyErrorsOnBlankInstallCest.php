<?php

class AnyErrorsOnBlankInstallCest
{
	/**
	 * Check that no PHP errors or notices are displayed at Gravity Forms > Settings > ConvertKit, when the Plugin is activated
	 * and not configured.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I  Tester
	 */
	public function testSettingsScreen(AcceptanceTester $I)
	{
		// Activate Plugins.
		$I->activateConvertKitPlugin($I);
		$I->activateThirdPartyPlugin($I, 'gravity-forms');

		// Go to the Plugin's Settings > General Screen.
		$I->loadConvertKitSettingsScreen($I);

		// Deactivate Plugins.
		$I->deactivateConvertKitPlugin($I);
		$I->deactivateThirdPartyPlugin($I, 'gravity-forms');
		$I->resetConvertKitPlugin($I);
	}
}
