<?php
/**
 * Tests the ConvertKit Review Notification.
 *
 * @since   1.2.2
 */
class ReviewRequestCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   1.2.2
	 *
	 * @param   AcceptanceTester $I  Tester
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
	}

	/**
	 * Test that the review request is displayed when the options table entries
	 * have the required values to display the review request notification.
	 *
	 * @since   1.2.2
	 *
	 * @param   AcceptanceTester $I  Tester
	 */
	public function testReviewRequestNotificationDisplayed(AcceptanceTester $I)
	{
		// Set review request option with a timestamp in the past, to emulate
		// the Plugin having set this a few days ago.
		$I->haveOptionInDatabase('convertkit-gravity-forms-review-request', time() - 3600 );

		// Navigate to a screen in the WordPress Administration.
		$I->amOnAdminPage('index.php');

		// Confirm the review displays.
		$I->seeElementInDOM('div.review-convertkit-gravity-forms');

		// Confirm links are correct.
		$I->seeInSource('<a href="https://wordpress.org/support/plugin/convertkit-gravity-forms/reviews/?filter=5#new-post" class="button button-primary" rel="noopener" target="_blank">');
		$I->seeInSource('<a href="https://convertkit.com/support" class="button" rel="noopener" target="_blank">');
	}

	/**
	 * Test that the review request is dismissed and does not reappear
	 * on a subsequent page load.
	 *
	 * @since   1.2.2
	 *
	 * @param   AcceptanceTester $I  Tester
	 */
	public function testReviewRequestNotificationDismissed(AcceptanceTester $I)
	{
		// Set review request option with a timestamp in the past, to emulate
		// the Plugin having set this a few days ago.
		$I->haveOptionInDatabase('convertkit-gravity-forms-review-request', time() - 3600 );

		// Navigate to a screen in the WordPress Administration.
		$I->amOnAdminPage('index.php');

		// Confirm the review displays.
		$I->seeElementInDOM('div.review-convertkit-gravity-forms');

		// Dismiss the review request.
		$I->click('div.review-convertkit-gravity-forms button.notice-dismiss');

		// Navigate to a screen in the WordPress Administration.
		$I->amOnAdminPage('index.php');

		// Confirm the review notification no longer displays.
		$I->dontSeeElementInDOM('div.review-convertkit-gravity-forms');
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   1.2.2
	 *
	 * @param   AcceptanceTester $I  Tester
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
