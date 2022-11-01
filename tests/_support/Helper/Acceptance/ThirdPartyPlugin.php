<?php
namespace Helper\Acceptance;

// Define any custom actions related to third party Plugins that
// would be used across multiple tests.
// These are then available in $I->{yourFunctionName}

class ThirdPartyPlugin extends \Codeception\Module
{
	/**
	 * Helper method to activate a third party Plugin, checking
	 * it activated and no errors were output.
	 *
	 * @since   1.9.6.7
	 *
	 * @param   string $name   Plugin Slug.
	 */
	public function activateThirdPartyPlugin($I, $name)
	{
		// Login as the Administrator
		$I->loginAsAdmin();

		// Go to the Plugins screen in the WordPress Administration interface.
		$I->amOnPluginsPage();

		// Activate the Plugin.
		$I->activatePlugin($name);

		// Some Plugins have a different slug when activated.
		switch ($name) {
			case 'gravity-forms':
				$I->seePluginActivated('gravityforms');
				break;

			default:
				$I->seePluginActivated($name);
				break;
		}

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Helper method to activate a third party Plugin, checking
	 * it activated and no errors were output.
	 *
	 * @since   1.9.6.7
	 *
	 * @param   string $name   Plugin Slug.
	 */
	public function deactivateThirdPartyPlugin($I, $name)
	{
		// Login as the Administrator
		$I->loginAsAdmin();

		// Go to the Plugins screen in the WordPress Administration interface.
		$I->amOnPluginsPage();

		// Some Plugins have a different slug when activated/deactivated.
		switch ($name) {
			case 'gravity-forms':
				$I->deactivatePlugin('gravityforms');
				$I->seePluginDeactivated('gravity-forms');
				break;

			default:
				$I->deactivatePlugin($name);
				$I->seePluginDeactivated($name);
				break;
		}

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}
}
