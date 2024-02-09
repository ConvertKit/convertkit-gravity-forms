<?php
/**
 * Tests that Feeds work with a Gravity Form and that subscriber data
 * is correctly sent to the API.
 *
 * @since   1.2.1
 */
class FormCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   1.2.1
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
	 * Test that the Plugin works when:
	 * - Creating a Gravity Form's Form, and
	 * - Adding a Feed to send entries to ConvertKit, and
	 * - Submitting the Form on the frontend web site results in the email address subscribing to the ConvertKit Form.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I  Tester.
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
		$firstName    = 'First';
		$lastName     = 'Last';
		$emailAddress = $I->generateEmailAddress();
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
		$I->fillField('.ginput_container_email input[type=email]', $emailAddress);

		// Submit Form.
		$I->click('Submit');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm submission was successful.
		$I->seeInSource('Thanks for contacting us! We will get in touch with you shortly.');

		// Check API to confirm subscriber was sent and data mapped to fields correctly.
		$I->apiCheckSubscriberExists($I, $emailAddress, $firstName, $customFields);

		// Check ConvertKit Notes were added to the Entry.
		$I->checkGravityFormsSuccessNotesExist($I);

		// Check that the options table does have a review request set.
		$I->seeOptionInDatabase('convertkit-gravity-forms-review-request');

		// Check that the option table does not yet have a review dismissed set.
		$I->dontSeeOptionInDatabase('convertkit-gravity-forms-review-dismissed');
	}

	/**
	 * Test that the Plugin shows an error when:
	 * - Creating a Gravity Form's Form, and
	 * - Adding a Feed to send entries to ConvertKit, with no ConvertKit Form selected.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I  Tester.
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
		$firstName    = 'First';
		$lastName     = 'Last';
		$emailAddress = $I->generateEmailAddress();

		// Logout as the WordPress Administrator.
		$I->logOut();

		// Load the Page on the frontend site.
		$I->amOnPage('/?p=' . $pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Complete Form Fields.
		$I->fillField('.name_first input[type=text]', $firstName);
		$I->fillField('.name_last input[type=text]', $lastName);
		$I->fillField('.ginput_container_email input[type=email]', $emailAddress);

		// Submit Form.
		$I->click('Submit');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm submission was successful.
		$I->seeInSource('Thanks for contacting us! We will get in touch with you shortly.');

		// Check API to confirm email address was not sent to ConvertKit.
		$I->apiCheckSubscriberDoesNotExist($I, $emailAddress);

		// Check ConvertKit Notes were not added to the Entry.
		$I->checkGravityFormsNotesDoNotExist($I);

		// Check that the options table doesn't have a review request set.
		$I->dontSeeOptionInDatabase('convertkit-gravity-forms-review-request');
		$I->dontSeeOptionInDatabase('convertkit-gravity-forms-review-dismissed');
	}

	/**
	 * Test that the Plugin works when:
	 * - Creating a Gravity Form's Form, and
	 * - Adding a Feed to send entries to ConvertKit,
	 * - Submitting the Form on the frontend web site, without an email address, results in no attempt to send data to ConvertKit.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testCreateFormAndFeedWithNoEmailAddressSpecified(AcceptanceTester $I)
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

		// Define Name.
		$firstName = 'First';
		$lastName  = 'Last';

		// Logout as the WordPress Administrator.
		$I->logOut();

		// Load the Page on the frontend site.
		$I->amOnPage('/?p=' . $pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Complete Form Fields.
		$I->fillField('.name_first input[type=text]', $firstName);
		$I->fillField('.name_last input[type=text]', $lastName);

		// Submit Form.
		$I->click('Submit');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check a ConvertKit Error Note were added to the Entry.
		$I->checkGravityFormsErrorNotesExist($I);

		// Check that the options table doesn't have a review request set.
		$I->dontSeeOptionInDatabase('convertkit-gravity-forms-review-request');
		$I->dontSeeOptionInDatabase('convertkit-gravity-forms-review-dismissed');
	}

	/**
	 * Test that the Plugin works when:
	 * - Creating a Gravity Form's Form, and
	 * - Adding a Feed to send entries to ConvertKit, with the Email mapped to a non-Email field
	 * - Submitting the Form on the frontend web site, with an invalid formatted email address, results in no attempt to send data to ConvertKit.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testCreateFormAndFeedWithInvalidEmailAddressSpecified(AcceptanceTester $I)
	{
		// Create Form.
		$gravityFormID = $I->createGravityFormsForm($I);

		// Create ConvertKit Feed for Form.
		$feedID = $I->createGravityFormsFeed(
			$I,
			$gravityFormID,
			$_ENV['CONVERTKIT_API_FORM_NAME'],
			false,
			false,
			'Name (First)'
		);

		// Create a Page with the Gravity Forms shortcode as its content.
		$pageID = $I->createPageWithGravityFormShortcode($I, $gravityFormID);

		// Define Name.
		$firstName = 'First';
		$lastName  = 'Last';

		// Logout as the WordPress Administrator.
		$I->logOut();

		// Load the Page on the frontend site.
		$I->amOnPage('/?p=' . $pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Complete Form Fields.
		$I->fillField('.name_first input[type=text]', $firstName);
		$I->fillField('.name_last input[type=text]', $lastName);

		// Submit Form.
		$I->click('Submit');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check a ConvertKit Error Note were added to the Entry.
		$I->checkGravityFormsErrorNotesExist($I);

		// Check that the options table doesn't have a review request set.
		$I->dontSeeOptionInDatabase('convertkit-gravity-forms-review-request');
		$I->dontSeeOptionInDatabase('convertkit-gravity-forms-review-dismissed');
	}

	/**
	 * Test that the Plugin works when:
	 * - Creating a Gravity Form's Form, and
	 * - Adding a Feed to send entries to ConvertKit, with a Tag selected, and
	 * - Submitting the Form on the frontend web site, with a Tag selected from the <select> field, results in the email address subscribing to the ConvertKit Form, and
	 * - The subscribed email address has the expected ConvertKit Tag assigned to it.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testCreateFormAndFeedWithTagAndFieldTagSelected(AcceptanceTester $I)
	{
		// Create Form.
		$gravityFormID = $I->createGravityFormsForm($I);

		// Create ConvertKit Feed for Form.
		$feedID = $I->createGravityFormsFeed(
			$I,
			$gravityFormID,
			$_ENV['CONVERTKIT_API_FORM_NAME'], // Define Feed's Form.
			$_ENV['CONVERTKIT_API_TAG_NAME'], // Define Feed's Tag.
			true // Map Feed's Tag Field to Form's Tag Field.
		);

		// Create a Page with the Gravity Forms shortcode as its content.
		$pageID = $I->createPageWithGravityFormShortcode($I, $gravityFormID);

		// Define Name, Email Address and Custom Field Data for this Test.
		$firstName    = 'First';
		$lastName     = 'Last';
		$emailAddress = $I->generateEmailAddress();
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
		$I->fillField('.ginput_container_email input[type=email]', $emailAddress);
		$I->selectOption('select.gfield_select', $_ENV['CONVERTKIT_API_ADDITIONAL_TAG_NAME']);

		// Submit Form.
		$I->click('Submit');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm submission was successful.
		$I->seeInSource('Thanks for contacting us! We will get in touch with you shortly.');

		// Check API to confirm subscriber was sent and data mapped to fields correctly.
		$I->apiCheckSubscriberExists($I, $emailAddress, $firstName, $customFields);

		// Check API to confirm subscriber has Tag set in Form's Feed.
		$I->apiCheckSubscriberHasTag($I, $emailAddress, $_ENV['CONVERTKIT_API_TAG_ID']);

		// Check API to confirm subscriber has Tag set in Form's <select> field.
		$I->apiCheckSubscriberHasTag($I, $emailAddress, $_ENV['CONVERTKIT_API_ADDITIONAL_TAG_ID']);

		// Check ConvertKit Notes were added to the Entry.
		$I->checkGravityFormsSuccessNotesExist($I);

		// Check that the options table does have a review request set.
		$I->seeOptionInDatabase('convertkit-gravity-forms-review-request');

		// Check that the option table does not yet have a review dismissed set.
		$I->dontSeeOptionInDatabase('convertkit-gravity-forms-review-dismissed');
	}

	/**
	 * Test that the Plugin works when:
	 * - Creating a Gravity Form's Form, and
	 * - Adding a Feed to send entries to ConvertKit, and
	 * - Submitting the Form on the frontend web site, with a Tag selected from the <select> field, results in the email address subscribing to the ConvertKit Form, and
	 * - The subscribed email address has the expected ConvertKit Tag assigned to it.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testCreateFormAndFeedWithFieldTagSelected(AcceptanceTester $I)
	{
		// Create Form.
		$gravityFormID = $I->createGravityFormsForm($I);

		// Create ConvertKit Feed for Form.
		$feedID = $I->createGravityFormsFeed(
			$I,
			$gravityFormID,
			$_ENV['CONVERTKIT_API_FORM_NAME'], // Define Feed's Form.
			false, // Don't define Feed's Tag.
			true // Map Feed's Tag Field to Form's Tag Field.
		);

		// Create a Page with the Gravity Forms shortcode as its content.
		$pageID = $I->createPageWithGravityFormShortcode($I, $gravityFormID);

		// Define Name, Email Address and Custom Field Data for this Test.
		$firstName    = 'First';
		$lastName     = 'Last';
		$emailAddress = $I->generateEmailAddress();
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
		$I->fillField('.ginput_container_email input[type=email]', $emailAddress);
		$I->selectOption('select.gfield_select', $_ENV['CONVERTKIT_API_ADDITIONAL_TAG_NAME']);

		// Submit Form.
		$I->click('Submit');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm submission was successful.
		$I->seeInSource('Thanks for contacting us! We will get in touch with you shortly.');

		// Check API to confirm subscriber was sent and data mapped to fields correctly.
		$I->apiCheckSubscriberExists($I, $emailAddress, $firstName, $customFields);

		// Check API to confirm subscriber does not have Tag, as none was defined in the Form's Feed.
		$I->apiCheckSubscriberDoesNotHaveTag($I, $emailAddress, $_ENV['CONVERTKIT_API_TAG_ID']);

		// Check API to confirm subscriber has Tag set in Form's <select> field.
		$I->apiCheckSubscriberHasTag($I, $emailAddress, $_ENV['CONVERTKIT_API_ADDITIONAL_TAG_ID']);

		// Check ConvertKit Notes were added to the Entry.
		$I->checkGravityFormsSuccessNotesExist($I);

		// Check that the options table does have a review request set.
		$I->seeOptionInDatabase('convertkit-gravity-forms-review-request');

		// Check that the option table does not yet have a review dismissed set.
		$I->dontSeeOptionInDatabase('convertkit-gravity-forms-review-dismissed');
	}

	/**
	 * Test that the Plugin works when:
	 * - Creating a Gravity Form's Form, and
	 * - Adding a Feed to send entries to ConvertKit, and
	 * - Submitting the Form on the frontend web site, with an Invalid Tag selected from the <select> field, results in the email address subscribing to the ConvertKit Form, and
	 * - The subscribed email address has no ConvertKit Tag assigned to it, as the Tag selected does not exist.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testCreateFormAndFeedWithInvalidFieldTagSelected(AcceptanceTester $I)
	{
		// Create Form.
		$gravityFormID = $I->createGravityFormsForm($I);

		// Create ConvertKit Feed for Form.
		$feedID = $I->createGravityFormsFeed(
			$I,
			$gravityFormID,
			$_ENV['CONVERTKIT_API_FORM_NAME'], // Define Feed's Form.
			false, // Don't define Feed's Tag.
			true // Map Feed's Tag Field to Form's Tag Field.
		);

		// Create a Page with the Gravity Forms shortcode as its content.
		$pageID = $I->createPageWithGravityFormShortcode($I, $gravityFormID);

		// Define Name, Email Address and Custom Field Data for this Test.
		$firstName    = 'First';
		$lastName     = 'Last';
		$emailAddress = $I->generateEmailAddress();
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
		$I->fillField('.ginput_container_email input[type=email]', $emailAddress);
		$I->selectOption( 'select.gfield_select', 'fakeTagNotInConvertKit' );

		// Submit Form.
		$I->click('Submit');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm submission was successful.
		$I->seeInSource('Thanks for contacting us! We will get in touch with you shortly.');

		// Check API to confirm subscriber was sent and data mapped to fields correctly.
		$I->apiCheckSubscriberExists($I, $emailAddress, $firstName, $customFields);

		// Check API to confirm subscriber does not have Tag, as none was defined in the Form's Feed.
		$I->apiCheckSubscriberDoesNotHaveTag($I, $emailAddress, $_ENV['CONVERTKIT_API_TAG_ID']);

		// Check API to confirm subscriber does not have Tag set in Form's <select> field, as it's an invalid tag.
		$I->apiCheckSubscriberDoesNotHaveTag($I, $emailAddress, $_ENV['CONVERTKIT_API_ADDITIONAL_TAG_ID']);

		// Check ConvertKit Notes were added to the Entry.
		$I->checkGravityFormsSuccessNotesExist($I);

		// Check that the options table does have a review request set.
		$I->seeOptionInDatabase('convertkit-gravity-forms-review-request');

		// Check that the option table does not yet have a review dismissed set.
		$I->dontSeeOptionInDatabase('convertkit-gravity-forms-review-dismissed');
	}

	/**
	 * Test that the Plugin works when:
	 * - Creating a Gravity Form's Form, and
	 * - Adding a Feed to send entries to ConvertKit, with a Tag selected, and
	 * - Submitting the Form on the frontend web site results in the email address subscribing to the ConvertKit Form, and
	 * - The subscribed email address has the expected ConvertKit Tag assigned to it.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testCreateFormAndFeedWithTagSelected(AcceptanceTester $I)
	{
		// Create Form.
		$gravityFormID = $I->createGravityFormsForm($I);

		// Create ConvertKit Feed for Form.
		$feedID = $I->createGravityFormsFeed(
			$I,
			$gravityFormID,
			$_ENV['CONVERTKIT_API_FORM_NAME'], // Define Feed's Form.
			$_ENV['CONVERTKIT_API_TAG_NAME'], // Define Feed's Tag.
			false // Don't map Feed's Tag Field.
		);

		// Create a Page with the Gravity Forms shortcode as its content.
		$pageID = $I->createPageWithGravityFormShortcode($I, $gravityFormID);

		// Define Name, Email Address and Custom Field Data for this Test.
		$firstName    = 'First';
		$lastName     = 'Last';
		$emailAddress = $I->generateEmailAddress();
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
		$I->fillField('.ginput_container_email input[type=email]', $emailAddress);

		// Submit Form.
		$I->click('Submit');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm submission was successful.
		$I->seeInSource('Thanks for contacting us! We will get in touch with you shortly.');

		// Check API to confirm subscriber was sent and data mapped to fields correctly.
		$I->apiCheckSubscriberExists($I, $emailAddress, $firstName, $customFields);

		// Check API to confirm subscriber has Tag set in Form's Feed.
		$I->apiCheckSubscriberHasTag($I, $emailAddress, $_ENV['CONVERTKIT_API_TAG_ID']);

		// Check API to confirm subscriber does not have Tag set in Form's <select> field, as no value was chosen.
		$I->apiCheckSubscriberDoesNotHaveTag($I, $emailAddress, $_ENV['CONVERTKIT_API_ADDITIONAL_TAG_ID']);

		// Check ConvertKit Notes were added to the Entry.
		$I->checkGravityFormsSuccessNotesExist($I);

		// Check that the options table does have a review request set.
		$I->seeOptionInDatabase('convertkit-gravity-forms-review-request');

		// Check that the option table does not yet have a review dismissed set.
		$I->dontSeeOptionInDatabase('convertkit-gravity-forms-review-dismissed');
	}

	/**
	 * Test that the Plugin works when:
	 * - Creating a Gravity Form's Form, and
	 * - Adding a Feed to send entries to ConvertKit, and
	 * - Configuring the Feed's conditional logic to send data to ConvertKit on the first name matching a value, and
	 * - Submitting the Form on the frontend web site results in the email address subscribing to the ConvertKit Form.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testCreateFormAndFeedWithConditionalLogicMatching(AcceptanceTester $I)
	{
		// Define Name, Email Address and Custom Field Data for this Test.
		$firstName    = 'First';
		$lastName     = 'Last';
		$emailAddress = $I->generateEmailAddress();
		$customFields = [
			'last_name' => $lastName,
		];

		// Create Form.
		$gravityFormID = $I->createGravityFormsForm($I);

		// Create ConvertKit Feed for Form, configuring the data to only send to ConvertKit
		// when the Gravity Form's first name field's value = 'First'.
		$feedID = $I->createGravityFormsFeed(
			$I,
			$gravityFormID,
			$_ENV['CONVERTKIT_API_FORM_NAME'],
			false, // Don't define the ConvertKit Tag setting.
			false, // Don't map a Gravity Forms field to a Tag.
			'Email',
			[
				'field' => 'Name (First)',
				'value' => $firstName,
			]
		);

		// Create a Page with the Gravity Forms shortcode as its content.
		$pageID = $I->createPageWithGravityFormShortcode($I, $gravityFormID);

		// Logout as the WordPress Administrator.
		$I->logOut();

		// Load the Page on the frontend site.
		$I->amOnPage('/?p=' . $pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Complete Form Fields.
		$I->fillField('.name_first input[type=text]', $firstName);
		$I->fillField('.name_last input[type=text]', $lastName);
		$I->fillField('.ginput_container_email input[type=email]', $emailAddress);

		// Submit Form.
		$I->click('Submit');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm submission was successful.
		$I->seeInSource('Thanks for contacting us! We will get in touch with you shortly.');

		// Check API to confirm subscriber was sent and data mapped to fields correctly, because
		// the conditional logic on the Gravity Forms Feed passed.
		$I->apiCheckSubscriberExists($I, $emailAddress, $firstName, $customFields);

		// Check ConvertKit Notes were added to the Entry.
		$I->checkGravityFormsSuccessNotesExist($I);

		// Check that the options table does have a review request set.
		$I->seeOptionInDatabase('convertkit-gravity-forms-review-request');

		// Check that the option table does not yet have a review dismissed set.
		$I->dontSeeOptionInDatabase('convertkit-gravity-forms-review-dismissed');
	}

	/**
	 * Test that the Plugin works when:
	 * - Creating a Gravity Form's Form, and
	 * - Adding a Feed to send entries to ConvertKit, and
	 * - Configuring the Feed's conditional logic to send data to ConvertKit on the first name matching a value, and
	 * - Submitting the Form on the frontend web site with a different first name results in the email address not subscribing.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testCreateFormAndFeedWithConditionalLogicDoesNotMatch(AcceptanceTester $I)
	{
		// Define Name, Email Address and Custom Field Data for this Test.
		$firstName    = 'First';
		$lastName     = 'Last';
		$emailAddress = $I->generateEmailAddress();
		$customFields = [
			'last_name' => $lastName,
		];

		// Create Form.
		$gravityFormID = $I->createGravityFormsForm($I);

		// Create ConvertKit Feed for Form, configuring the data to only send to ConvertKit
		// when the Gravity Form's first name field's value = 'random'.
		$feedID = $I->createGravityFormsFeed(
			$I,
			$gravityFormID,
			$_ENV['CONVERTKIT_API_FORM_NAME'],
			false, // Don't define the ConvertKit Tag setting.
			false, // Don't map a Gravity Forms field to a Tag.
			'Email',
			[
				'field' => 'Name (First)',
				'value' => 'random',
			]
		);

		// Create a Page with the Gravity Forms shortcode as its content.
		$pageID = $I->createPageWithGravityFormShortcode($I, $gravityFormID);

		// Logout as the WordPress Administrator.
		$I->logOut();

		// Load the Page on the frontend site.
		$I->amOnPage('/?p=' . $pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Complete Form Fields.
		$I->fillField('.name_first input[type=text]', $firstName);
		$I->fillField('.name_last input[type=text]', $lastName);
		$I->fillField('.ginput_container_email input[type=email]', $emailAddress);

		// Submit Form.
		$I->click('Submit');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm submission was successful.
		$I->seeInSource('Thanks for contacting us! We will get in touch with you shortly.');

		// Check API to confirm subscriber was not sent, because the conditional logic on the
		// Gravity Forms Feed will have failed due to a first name mismatch.
		$I->apiCheckSubscriberDoesNotExist($I, $emailAddress);

		// Check ConvertKit Notes were not added to the Entry.
		$I->checkGravityFormsNotesDoNotExist($I);

		// Check that the options table doesn't have a review request set.
		$I->dontSeeOptionInDatabase('convertkit-gravity-forms-review-request');
		$I->dontSeeOptionInDatabase('convertkit-gravity-forms-review-dismissed');
	}

	/**
	 * Test that the Plugin works when:
	 * - Creating a Gravity Form's Form, and
	 * - Adding a Feed to send entries to ConvertKit, and
	 * - Disabling the Feed (meaning no entries should be sent to ConvertKit), and
	 * - Submitting the Form on the frontend web site results in the email address not being subscribed to the ConvertKit Form.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I  Tester.
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
		$firstName    = 'First';
		$lastName     = 'Last';
		$emailAddress = $I->generateEmailAddress();

		// Logout as the WordPress Administrator.
		$I->logOut();

		// Load the Page on the frontend site.
		$I->amOnPage('/?p=' . $pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Complete Form Fields.
		$I->fillField('.name_first input[type=text]', $firstName);
		$I->fillField('.name_last input[type=text]', $lastName);
		$I->fillField('.ginput_container_email input[type=email]', $emailAddress);

		// Submit Form.
		$I->click('Submit');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm submission was successful.
		$I->seeInSource('Thanks for contacting us! We will get in touch with you shortly.');

		// Check API to confirm email address was not sent to ConvertKit.
		$I->apiCheckSubscriberDoesNotExist($I, $emailAddress);

		// Check API to confirm subscriber does not have tags.
		$I->apiCheckSubscriberDoesNotHaveTag($I, $emailAddress, $_ENV['CONVERTKIT_API_TAG_ID']);
		$I->apiCheckSubscriberDoesNotHaveTag($I, $emailAddress, $_ENV['CONVERTKIT_API_ADDITIONAL_TAG_ID']);

		// Check no ConvertKit Notes were added to the Entry.
		$I->checkGravityFormsNotesDoNotExist($I);

		// Check that the options table doesn't have a review request set.
		$I->dontSeeOptionInDatabase('convertkit-gravity-forms-review-request');
		$I->dontSeeOptionInDatabase('convertkit-gravity-forms-review-dismissed');
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
		$I->deactivateConvertKitPlugin($I);
		$I->deactivateThirdPartyPlugin($I, 'gravity-forms');
		$I->resetConvertKitPlugin($I);
	}
}
