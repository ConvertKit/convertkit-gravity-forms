<?php
/**
 * Tests that ConvertKit Settings for a Gravity Form save correctly.
 * 
 * @since 	1.2.1
 */
class FormSettingsCest
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
	 * Test that creating a Gravity Form's Form, adding a Feed to send entries to ConvertKit
	 * and submitting the Form on the frontend web site works.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testFormWithFeed(AcceptanceTester $I)
	{
		// Create Form.
		$formID = $I->createForm($I);

		// Navigate to Form's Settings > ConvertKit.
		$I->amOnAdminPage('admin.php?page=gf_edit_forms&view=settings&subview=ckgf&id=' . $formID);

		// Click Add New.
		$I->click('#form_settings a.add-new-h2');

		// Check ConvertKit Form option exists and is populated.
		$I->seeElementInDOM('select[name="_gaddon_setting_form_id"]');

		// Define Feed Name.
		$I->fillField('_gaddon_setting_feed_name', 'ConvertKit Feed');

		// Define ConvertKit Form to send entries to.
		$I->selectOption('_gaddon_setting_form_id', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Map Email Field.
		$I->selectOption('_gaddon_setting_field_map_e', 'Email');

		// Map Name Field.
		$I->selectOption('_gaddon_setting_field_map_n', 'Name (First)');

		// Map ConvertKit Account Custom Field 'Last Name'.
		$I->selectOption('_gaddon_setting_convertkit_custom_fields_key', 'Last Name');
		$I->selectOption('_gaddon_setting_convertkit_custom_fields_custom_value', 'Name (Last)');

		// Click Update Settings.
		$I->click('Update Settings');

		// Confirm Feed Settings saved successfully.
		$I->seeInSource('Feed updated successfully.');

		// Confirm Feed Fields contain saved values.
		$I->seeInField('_gaddon_setting_feed_name', 'ConvertKit Feed');
		$I->seeOptionIsSelected('_gaddon_setting_form_id', $_ENV['CONVERTKIT_API_FORM_NAME']);
		$I->seeOptionIsSelected('_gaddon_setting_field_map_e', 'Email');
		$I->seeOptionIsSelected('_gaddon_setting_field_map_n', 'Name (First)');
		$I->seeOptionIsSelected('_gaddon_setting_convertkit_custom_fields_key', 'Last Name');
		$I->seeOptionIsSelected('_gaddon_setting_convertkit_custom_fields_custom_value', 'Name (Last)');

		// Create a Page with the Gravity Forms shortcode as its content.
		$pageID = $I->createPageWithGravityFormShortcode($I, $formID);

		// Define Name, Email Address and Custom Field Data for this Test.
		$firstName = 'First';
		$lastName = 'Last';
		$emailAddress = 'wordpress-gravityforms-' . date( 'YmdHis' ) . '@convertkit.com';
		$customFields = [
			'last_name' => $lastName,
		];

		// Unsubscribe the email address, so we restore the account back to its previous state.
		// $I->apiUnsubscribe($emailAddress);

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
		$I->apiCheckSubscriberExists($emailAddress, $firstName, $customFields);
	}
}
