<?php
/**
 * Tests that Feeds work with a Gravity Form and that subscriber data
 * is correctly sent to the API.
 * 
 * @since 	1.2.1
 */
class FormCest
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
		$I->activateGravityFormsAndConvertKitPlugins($I);
		$I->setupConvertKitPlugin($I);
	}

	/**
	 * Test that the Plugin works when:
	 * - Creating a Gravity Form's Form, and
	 * - Adding a Feed to send entries to ConvertKit, and
	 * - Submitting the Form on the frontend web site results in the email address subscribing to the ConvertKit Form.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testCreateFormAndFeed(AcceptanceTester $I)
	{
		// Create Form.
		$gravityFormID = $I->createGravityFormsForm($I);

		// Create ConvertKit Feed for Form.
		$feedID = $I->createGravityFormsFeed($I, $gravityFormID, $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Create a Page with the Gravity Forms shortcode as its content.
		$pageID = $I->createPageWithGravityFormShortcode($I, $gravityFormID);

		// Define Name, Email Address and Custom Field Data for this Test.
		$firstName = 'First';
		$lastName = 'Last';
		$emailAddress = 'wordpress-gravityforms-' . date('YmdHis') . '@n7studios.com';
		$customFields = [
			'last_name' => $lastName,
		];

		// Logout as the WordPress Administrator.
		$I->logOut();

		// Load the Page on the frontend site.
		$I->amOnPage('/?p=' . $pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Complete Form Fields.
		$I->fillField('.name_first input[type=text]', $firstName);
		$I->fillField('.name_last input[type=text]', $lastName);
		$I->fillField('.ginput_container_email input[type=text]', $emailAddress);

		// Submit Form.
		$I->click('Submit');

		// Confirm submission was successful.
		$I->seeInSource('Thanks for contacting us! We will get in touch with you shortly.');

		// Check API to confirm subscriber was sent and data mapped to fields correctly.
		$I->apiCheckSubscriberExists($I, $emailAddress, $firstName, $customFields);
	}

	/**
	 * Test that the Plugin shows an error when:
	 * - Creating a Gravity Form's Form, and
	 * - Adding a Feed to send entries to ConvertKit, with no ConvertKit Form selected.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testCreateFormAndFeedWithNoConvertKitFormSelected(AcceptanceTester $I)
	{
		// Create Form.
		$gravityFormID = $I->createGravityFormsForm($I);

		// Navigate to Form's Settings > ConvertKit.
		$I->amOnAdminPage('admin.php?page=gf_edit_forms&view=settings&subview=ckgf&id=' . $gravityFormID);

		// Click Add New.
		$I->click('#gform-settings div.tablenav.top div.alignright a.button');

		// Complete Feed's Form Fields.
		$I->completeGravityFormsFeedFields($I, 'Select a ConvertKit form');

		// Click Save Settings.
		$I->click('#gform-settings-save');

		// Confirm an error message is displayed.
		$I->seeInSource('There was an error while saving your settings.');

		// Create a Page with the Gravity Forms shortcode as its content.
		$pageID = $I->createPageWithGravityFormShortcode($I, $gravityFormID);

		// Define Name, Email Address and Custom Field Data for this Test.
		$firstName = 'First';
		$lastName = 'Last';
		$emailAddress = 'wordpress-gravityforms-' . date('YmdHis') . '@n7studios.com';

		// Logout as the WordPress Administrator.
		$I->logOut();

		// Load the Page on the frontend site.
		$I->amOnPage('/?p=' . $pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Complete Form Fields.
		$I->fillField('.name_first input[type=text]', $firstName);
		$I->fillField('.name_last input[type=text]', $lastName);
		$I->fillField('.ginput_container_email input[type=text]', $emailAddress);

		// Submit Form.
		$I->click('Submit');

		// Confirm submission was successful.
		$I->seeInSource('Thanks for contacting us! We will get in touch with you shortly.');

		// Check API to confirm email address was not sent to ConvertKit.
		$I->apiCheckSubscriberDoesNotExist($I, $emailAddress);
	}

	/**
	 * Test that the Plugin works when:
	 * - Creating a Gravity Form's Form, and
	 * - Adding a Feed to send entries to ConvertKit, with a Tag selected, and
	 * - Submitting the Form on the frontend web site, with a Tag selected from the <select> field, results in the email address subscribing to the ConvertKit Form, and
	 * - The subscribed email address has the expected ConvertKit Tag assigned to it.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testCreateFormAndFeedWithTagAndFieldTagSelected(AcceptanceTester $I)
	{
		// Create Form.
		$gravityFormID = $I->createGravityFormsForm($I);

		// Create ConvertKit Feed for Form.
		$feedID = $I->createGravityFormsFeed(
			$I,
			$gravityFormID,
			$_ENV['CONVERTKIT_API_FORM_NAME'],
			$_ENV['CONVERTKIT_API_TAG_NAME']
		);

		// Create a Page with the Gravity Forms shortcode as its content.
		$pageID = $I->createPageWithGravityFormShortcode($I, $gravityFormID);

		// Define Name, Email Address and Custom Field Data for this Test.
		$firstName = 'First';
		$lastName = 'Last';
		$emailAddress = 'wordpress-gravityforms-' . date('YmdHis') . '@n7studios.com';
		$customFields = [
			'last_name' => $lastName,
		];

		// Logout as the WordPress Administrator.
		$I->logOut();

		// Load the Page on the frontend site.
		$I->amOnPage('/?p=' . $pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Complete Form Fields.
		$I->fillField('.name_first input[type=text]', $firstName);
		$I->fillField('.name_last input[type=text]', $lastName);
		$I->fillField('.ginput_container_email input[type=text]', $emailAddress);
		$I->selectOption( 'select.gfield_select', $_ENV['CONVERTKIT_API_ADDITIONAL_TAG_NAME'] );

		// Submit Form.
		$I->click('Submit');

		// Confirm submission was successful.
		$I->seeInSource('Thanks for contacting us! We will get in touch with you shortly.');

		// Check API to confirm subscriber was sent and data mapped to fields correctly.
		$I->apiCheckSubscriberExists($I, $emailAddress, $firstName, $customFields);

		// Check API to confirm subscriber has expected tags.
		// @TODO
	}

	/**
	 * Test that the Plugin works when:
	 * - Creating a Gravity Form's Form, and
	 * - Adding a Feed to send entries to ConvertKit, and
	 * - Submitting the Form on the frontend web site, with a Tag selected from the <select> field, results in the email address subscribing to the ConvertKit Form, and
	 * - The subscribed email address has the expected ConvertKit Tag assigned to it.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testCreateFormAndFeedWithFieldTagSelected(AcceptanceTester $I)
	{
		// Create Form.
		$gravityFormID = $I->createGravityFormsForm($I);

		// Create ConvertKit Feed for Form.
		$feedID = $I->createGravityFormsFeed(
			$I,
			$gravityFormID,
			$_ENV['CONVERTKIT_API_FORM_NAME']
		);

		// Create a Page with the Gravity Forms shortcode as its content.
		$pageID = $I->createPageWithGravityFormShortcode($I, $gravityFormID);

		// Define Name, Email Address and Custom Field Data for this Test.
		$firstName = 'First';
		$lastName = 'Last';
		$emailAddress = 'wordpress-gravityforms-' . date('YmdHis') . '@n7studios.com';
		$customFields = [
			'last_name' => $lastName,
		];

		// Logout as the WordPress Administrator.
		$I->logOut();

		// Load the Page on the frontend site.
		$I->amOnPage('/?p=' . $pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Complete Form Fields.
		$I->fillField('.name_first input[type=text]', $firstName);
		$I->fillField('.name_last input[type=text]', $lastName);
		$I->fillField('.ginput_container_email input[type=text]', $emailAddress);
		$I->selectOption( 'select.gfield_select', $_ENV['CONVERTKIT_API_ADDITIONAL_TAG_NAME'] );

		// Submit Form.
		$I->click('Submit');

		// Confirm submission was successful.
		$I->seeInSource('Thanks for contacting us! We will get in touch with you shortly.');

		// Check API to confirm subscriber was sent and data mapped to fields correctly.
		$I->apiCheckSubscriberExists($I, $emailAddress, $firstName, $customFields);

		// Check API to confirm subscriber has expected tags.
		// @TODO
	}

	/**
	 * Test that the Plugin works when:
	 * - Creating a Gravity Form's Form, and
	 * - Adding a Feed to send entries to ConvertKit, with a Tag selected, and
	 * - Submitting the Form on the frontend web site results in the email address subscribing to the ConvertKit Form, and
	 * - The subscribed email address has the expected ConvertKit Tag assigned to it.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testCreateFormAndFeedWithTagSelected(AcceptanceTester $I)
	{
		// Create Form.
		$gravityFormID = $I->createGravityFormsForm($I);

		// Create ConvertKit Feed for Form.
		$feedID = $I->createGravityFormsFeed(
			$I,
			$gravityFormID,
			$_ENV['CONVERTKIT_API_FORM_NAME'],
			$_ENV['CONVERTKIT_API_TAG_NAME']
		);

		// Create a Page with the Gravity Forms shortcode as its content.
		$pageID = $I->createPageWithGravityFormShortcode($I, $gravityFormID);

		// Define Name, Email Address and Custom Field Data for this Test.
		$firstName = 'First';
		$lastName = 'Last';
		$emailAddress = 'wordpress-gravityforms-' . date('YmdHis') . '@n7studios.com';
		$customFields = [
			'last_name' => $lastName,
		];

		// Logout as the WordPress Administrator.
		$I->logOut();

		// Load the Page on the frontend site.
		$I->amOnPage('/?p=' . $pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Complete Form Fields.
		$I->fillField('.name_first input[type=text]', $firstName);
		$I->fillField('.name_last input[type=text]', $lastName);
		$I->fillField('.ginput_container_email input[type=text]', $emailAddress);

		// Submit Form.
		$I->click('Submit');

		// Confirm submission was successful.
		$I->seeInSource('Thanks for contacting us! We will get in touch with you shortly.');

		// Check API to confirm subscriber was sent and data mapped to fields correctly.
		$I->apiCheckSubscriberExists($I, $emailAddress, $firstName, $customFields);

		// Check API to confirm subscriber has expected tags.
		// @TODO
	}

	/**
	 * Test that the Plugin works when:
	 * - Creating a Gravity Form's Form, and
	 * - Adding a Feed to send entries to ConvertKit, and
	 * - Disabling the Feed (meaning no entries should be sent to ConvertKit), and
	 * - Submitting the Form on the frontend web site results in the email address not being subscribed to the ConvertKit Form.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testCreateFormAndFeedDisabled(AcceptanceTester $I)
	{
		// Create Form.
		$gravityFormID = $I->createGravityFormsForm($I);

		// Create ConvertKit Feed for Form.
		$feedID = $I->createGravityFormsFeed(
			$I,
			$gravityFormID,
			$_ENV['CONVERTKIT_API_FORM_NAME'],
			$_ENV['CONVERTKIT_API_TAG_NAME']
		);

		// Disable the Feed.
		$I->disableGravityFormsFeed($I, $gravityFormID);

		// Create a Page with the Gravity Forms shortcode as its content.
		$pageID = $I->createPageWithGravityFormShortcode($I, $gravityFormID);

		// Define Name, Email Address and Custom Field Data for this Test.
		$firstName = 'First';
		$lastName = 'Last';
		$emailAddress = 'wordpress-gravityforms-' . date('YmdHis') . '@n7studios.com';

		// Logout as the WordPress Administrator.
		$I->logOut();

		// Load the Page on the frontend site.
		$I->amOnPage('/?p=' . $pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Complete Form Fields.
		$I->fillField('.name_first input[type=text]', $firstName);
		$I->fillField('.name_last input[type=text]', $lastName);
		$I->fillField('.ginput_container_email input[type=text]', $emailAddress);

		// Submit Form.
		$I->click('Submit');

		// Confirm submission was successful.
		$I->seeInSource('Thanks for contacting us! We will get in touch with you shortly.');

		// Check API to confirm email address was not sent to ConvertKit.
		$I->apiCheckSubscriberDoesNotExist($I, $emailAddress);
	}
	
}
