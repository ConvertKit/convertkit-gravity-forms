<?php
/**
 * Tests the notices output recommending the official add-on.
 *
 * @since   1.4.2
 */
class NoticesCest
{
	/**
	 * Holds the expected notice text.
	 *
	 * @since   1.4.2
	 *
	 * @var     string
	 */
	public $expectedNoticeText = 'ConvertKit Gravity Forms Add-On: Please install the official Gravity Forms ConvertKit Add-On. Your existing settings will automatically migrate once installed and active.';

	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   1.4.2
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
	}

	/**
	 * Test that the persistent notice displays with expected wording when the Plugin is active.
	 *
	 * @since   1.4.2
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testPersistentNoticeDisplays(AcceptanceTester $I)
	{
		// Confirm notice displays with expected text.
		$I->seeElement('.notice-warning');
		$I->see($this->expectedNoticeText);

		// Confirm the link is valid.
		$I->assertEquals(
			$I->grabAttributeFrom('.notice-warning a', 'href'),
			'https://www.gravityforms.com/blog/convertkit-add-on/'
		);

		// Deactivate Plugin.
		$I->deactivateConvertKitPlugin($I);

		// Confirm no notice from this Plugin displays.
		$I->dontSee($this->expectedNoticeText);
	}

	/**
	 * Test that the persistent notice does not display when the official add-on is active.
	 *
	 * @since   1.4.2
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testPersistentNoticeNotDisplayedWhenOfficialAddonActive(AcceptanceTester $I)
	{
		// Activate Gravity Forms and official add-on.
		$I->activateThirdPartyPlugin($I, 'gravity-forms');
		$I->activateThirdPartyPlugin($I, 'gravity-forms-convertkit-add-on');

		// Confirm no notice from this Plugin displays.
		$I->dontSee($this->expectedNoticeText);

		// Confirm the official add-on displays its notice that it deactivated this Plugin.
		$I->see('In order to prevent conflicts, we disabled the existing ConvertKit for Gravity Forms plugin.');
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   1.2.2
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->resetConvertKitPlugin($I);
	}
}
