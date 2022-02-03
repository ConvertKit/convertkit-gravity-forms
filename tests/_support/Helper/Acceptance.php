<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Acceptance extends \Codeception\Module
{
	/**
	 * Helper method to assert that there are non PHP errors, warnings or notices output
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I 	AcceptanceTester.
	 */
	public function checkNoWarningsAndNoticesOnScreen($I)
	{
		// Check that the <body> class does not have a php-error class, which indicates a suppressed PHP function call error.
		$I->dontSeeElement('.php-error');

		// Check that no Xdebug errors exist.
		$I->dontSeeElement('.xdebug-error');
		$I->dontSeeElement('.xe-notice');
	}

	/**
	 * Helper method to assert that the field's value contains the given value.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I 	AcceptanceTester.
	 */
	public function seeFieldContains($I, $element, $value)
	{
		$this->assertNotFalse(strpos($I->grabValueFrom($element), $value));
	}

	/**
	 * Helper method to enter text into a jQuery Select2 Field, selecting the option that appears.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I 			AcceptanceTester.
	 * @param 	string 				$container 	Field CSS Class / ID.
	 * @param 	string 				$value 		Field Value.
	 * @param 	string 				$ariaAttributeName 	Aria Attribute Name (aria-controls|aria-owns).
	 */
	public function fillSelect2Field($I, $container, $value, $ariaAttributeName = 'aria-controls')
	{
		$fieldID = $I->grabAttributeFrom($container, 'id');
		$fieldName = str_replace('-container', '', str_replace('select2-', '', $fieldID));
		$I->click('#'.$fieldID);
		$I->waitForElementVisible('.select2-search__field[' . $ariaAttributeName . '="select2-' . $fieldName . '-results"]');
		$I->fillField('.select2-search__field[' . $ariaAttributeName . '="select2-' . $fieldName . '-results"]', $value);
		$I->waitForElementVisible('ul#select2-' . $fieldName . '-results li.select2-results__option--highlighted');
		$I->pressKey('.select2-search__field[' . $ariaAttributeName . '="select2-' . $fieldName . '-results"]', \Facebook\WebDriver\WebDriverKeys::ENTER);
	}

	/**
	 * Helper method to close the Gutenberg "Welcome to the block editor" dialog, which
	 * might show for each Page/Post test performed due to there being no persistence
	 * remembering that the user dismissed the dialog.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I AcceptanceTester.
	 */
	public function maybeCloseGutenbergWelcomeModal($I)
	{
		try {
			$I->performOn('.components-modal__screen-overlay', [
				'click' => '.components-modal__screen-overlay .components-modal__header button.components-button'
			], 3);
		} catch ( \Facebook\WebDriver\Exception\TimeoutException $e ) {
		}
	}

	/**
	 * Helper method to activate the Plugin.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I AcceptanceTester.
	 */
	public function activateConvertKitPlugin($I)
	{
		// Login as the Administrator
		$I->loginAsAdmin();

		// Go to the Plugins screen in the WordPress Administration interface.
		$I->amOnPluginsPage();

		// Activate the Plugin.
		$I->activatePlugin('convertkit-gravity-forms');

		// Check that the Plugin activated successfully.
		$I->seePluginActivated('convertkit-gravity-forms');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Helper method to activate the Gravity Forms Plugin and the ConvertKit for Gravity Forms Plugin.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I AcceptanceTester.
	 */
	public function activateGravityFormsAndConvertKitPlugins($I)
	{
		// Login as the Administrator
		$I->loginAsAdmin();

		// Go to the Plugins screen in the WordPress Administration interface.
		$I->amOnPluginsPage();

		// Activate the Gravity Forms Plugin.
		$I->activatePlugin('gravity-forms');

		// Check that the Plugin activated successfully.
		$I->seePluginActivated('gravityforms');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Activate the Plugin.
		$I->activatePlugin('convertkit-gravity-forms');

		// Check that the Plugin activated successfully.
		$I->seePluginActivated('convertkit-gravity-forms');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Helper method to deactivate the Plugin.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I AcceptanceTester.
	 */
	public function deactivateConvertKitPlugin($I)
	{
		// Login as the Administrator
		$I->loginAsAdmin();

		// Go to the Plugins screen in the WordPress Administration interface.
		$I->amOnPluginsPage();

		// Deactivate the Plugin.
		$I->deactivatePlugin('convertkit-gravity-forms');

		// Check that the Plugin deactivated successfully.
		$I->seePluginDeactivated('convertkit-gravity-forms');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Helper method to load the Gravity Forms > Settings > ConvertKit screen.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I 	AcceptanceTester.
	 */
	public function loadConvertKitSettingsScreen($I)
	{
		$I->amOnAdminPage('admin.php?page=gf_settings&subview=ckgf');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Helper method to setup the Plugin's API Key and Secret, and enable the integration.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I 	AcceptanceTester.
	 */
	public function setupConvertKitPlugin($I)
	{
		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsScreen($I);

		// Complete API Fields.
		$I->fillField('_gform_setting_api_key', $_ENV['CONVERTKIT_API_KEY']);
		
		// Click the Save Settings button.
		$I->click('#gform-settings-save');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that a message is displayed confirming settings saved.
		$I->seeInSource('Settings updated.');

		// Check the value of the fields match the inputs provided.
		$I->seeInField('_gform_setting_api_key', $_ENV['CONVERTKIT_API_KEY']);
	}

	/**
	 * Creates a Gravity Forms Form.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I  				AcceptanceTester.
	 * @return 	int 					Form ID.
	 */
	public function createGravityFormsForm($I)
	{
		// Navigate to Forms > New Form.
		$I->amOnAdminPage('admin.php?page=gf_new_form');

		// Define Title.
		$I->fillField('#new_form_title', 'ConvertKit Form Test');

		// Click Create Form button.
		$I->click('Create Form');

		// Wait for the Form Edit screen to load.
		$I->waitForElementVisible('#no-fields');

		// Open Advanced Fields Panel.
		$I->click('button[aria-controls="add_advanced_fields"]');
		$I->wait(2);

		// Add Name Field.
		$I->click('#add_advanced_fields button[data-type="name"]');
		$I->wait(2);

		// Add Email Field.
		$I->click('#add_advanced_fields button[data-type="email"]');
		$I->wait(2);

		// Add Text Field.
		$I->click('#add_standard_fields button[data-type="text"]');
		$I->wait(2);

		// Add Textarea Field.
		$I->click('#add_standard_fields button[data-type="textarea"]');
		$I->wait(2);

		// Add Select Field, with values matching ConvertKit Tags.
		$I->click('#add_standard_fields button[data-type="select"]');
		$I->wait(2);

		$I->click('#field_5');
		$I->wait(2);

		$I->fillField('#field_label', 'Tag');
		$I->click('label[for=field_choice_values_enabled]');
		
		$I->fillField('#select_choice_text_0', 'Select Tag');
		$I->fillField('#select_choice_value_0', '');
		
		$I->fillField('#select_choice_text_1', 'Tag Label 1');
		$I->fillField('#select_choice_value_1', $_ENV['CONVERTKIT_API_ADDITIONAL_TAG_NAME']);

		$I->fillField('#select_choice_text_2', 'Tag Label 2');
		$I->fillField('#select_choice_value_2', 'fakeTagNotInConvertKit');
	
		// Update.
		$I->click('button.update-form');

		// Return Form ID.
		return (int) $I->grabFromCurrentUrl('~(\d+)$~');
	}

	/**
	 * Creates a Gravity Forms ConvertKit Feed (a feed sends form entries to ConvertKit) for the given Gravity Form.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I 				AcceptanceTester.
	 * @param 	int 				$gravityFormID 	Gravity Forms Form ID.
	 * @param 	string 				$formName 		ConvertKit Form Name.
	 * @param 	string 				$tagName 		ConvertKit Tag Name.
	 */
	public function createGravityFormsFeed($I, $gravityFormID, $formName, $tagName = false, $mapTagField = false)
	{
		// Navigate to Form's Settings > ConvertKit.
		$I->amOnAdminPage('admin.php?page=gf_edit_forms&view=settings&subview=ckgf&id=' . $gravityFormID);

		// Click Add New.
		$I->click('#gform-settings div.tablenav.top div.alignright a.button');

		// Complete Feed's Form Fields.
		$I->completeGravityFormsFeedFields($I, $formName, $tagName, $mapTagField);

		// Click Save Settings.
		$I->click('#gform-settings-save');

		// Confirm Feed Settings saved successfully.
		$I->seeInSource('Settings updated.');

		// Confirm Feed Fields contain saved values.
		$I->seeInField('_gform_setting_feed_name', 'ConvertKit Feed');
		$I->seeOptionIsSelected('_gform_setting_form_id', $formName);
		if ($tagName) {
			$I->seeOptionIsSelected('_gform_setting_tag_id', $tagName);
		}
		$I->seeOptionIsSelected('#_gform_setting_field_map_e', 'Email');
		$I->seeOptionIsSelected('#_gform_setting_field_map_n', 'Name (First)');
		$I->seeOptionIsSelected('#_gform_setting_convertkit_custom_fields_custom_key_0', 'Last Name');
		$I->seeOptionIsSelected('#_gform_setting_convertkit_custom_fields_custom_value_0', 'Name (Last)');
		if ($mapTagField) {
			$I->seeOptionIsSelected('#_gform_setting_field_map_tag', 'Tag');
		} else {
			$I->seeOptionIsSelected('#_gform_setting_field_map_tag', 'Select a Field');
		}
	}

	/**
	 * Completes form fields for a Gravity Forms ConvertKit Feed Settings.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I 				AcceptanceTester.
	 * @param 	string 				$formName 		ConvertKit Form Name.
	 * @param 	string 				$tagName 		ConvertKit Tag Name.
	 */
	public function completeGravityFormsFeedFields($I, $formName, $tagName = false, $mapTagField = false)
	{
		// Check ConvertKit Form option exists and is populated.
		$I->seeElementInDOM('select[name="_gform_setting_form_id"]');

		// Define Feed Name.
		$I->fillField('_gform_setting_feed_name', 'ConvertKit Feed');

		// Define ConvertKit Form to send entries to.
		$I->selectOption('_gform_setting_form_id', $formName);

		// Define ConvertKit Tag to apply to subscribers.
		if ($tagName) {
			$I->selectOption('_gform_setting_tag_id', $tagName);
		}

		// Map Email Field.
		$I->selectOption('#_gform_setting_field_map_e', 'Email');

		// Map Name Field.
		$I->selectOption('#_gform_setting_field_map_n', 'Name (First)');

		// Map Tag Field.
		if ($mapTagField) {
			$I->selectOption('#_gform_setting_field_map_tag', 'Tag');
		} else {
			$I->selectOption('#_gform_setting_field_map_tag', 'Select a Field');
		}

		// Map ConvertKit Account Custom Field 'Last Name'.
		$I->selectOption('#_gform_setting_convertkit_custom_fields_custom_key_0', 'Last Name');
		$I->selectOption('#_gform_setting_convertkit_custom_fields_custom_value_0', 'Name (Last)');
	}

	/**
	 * Disables the given feed for the given Gravity Form ID.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I 					AcceptanceTester.
	 * @param 	int 				$gravityFormID 		Gravity Forms Form ID.
	 * @param 	int 				$gravityFormFeedID 	Gravity Forms Feed ID.
	 */
	public function disableGravityFormsFeed($I, $gravityFormID, $gravityFormFeedID = 1)
	{
		// Load Form's ConvertKit Feed Settings.
		$I->amOnAdminPage('admin.php?page=gf_edit_forms&view=settings&subview=ckgf&id=' . $gravityFormID);

		// Deactivate the Feed
		$I->click('table.feeds tbody tr:nth-child(' . $gravityFormFeedID . ') th.manage-column button.gform-status--active');
	}

	/**
	 * Creates a WordPress Page with the Gravity Form shortcode as the content
	 * to render the Gravity Form.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester 	$I 				AcceptanceTester.
	 * @param 	int 				$gravityFormID 	Gravity Forms Form ID.
	 * @return 	int 								Page ID
	 */
	public function createPageWithGravityFormShortcode($I, $gravityFormID)
	{
		return $I->havePostInDatabase([
			'post_type'		=> 'page',
			'post_status'	=> 'publish',
			'post_name' 	=> 'gravity-form-' . $gravityFormID,
			'post_title'	=> 'Gravity Form #' . $gravityFormID,
			'post_content'	=> '[gravityform id="' . $gravityFormID . '" title="false" description="false"]',
		]);
	}

	/**
	 * Check the given email address and name exists as a subscriber on ConvertKit.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester $I 			AcceptanceTester.
	 * @param 	string 			$emailAddress 	Email Address.
	 * @param 	mixed 			$firstName 		First Name (false = don't test).
	 * @param 	mixed 			$customFields 	Custom Field Key/Value pairs (false = don't test).
	 */ 	
	public function apiCheckSubscriberExists($I, $emailAddress, $firstName = false, $customFields = false)
	{
		// Run request.
		$results = $this->apiRequest('subscribers', 'GET', [
			'email_address' => $emailAddress,
		]);

		// Check at least one subscriber was returned.
		$I->assertGreaterThan(0, $results['total_subscribers']);
		$subscriber = $results['subscribers'][0];

		// Check the subscriber's email address matches.
		$I->assertEquals($emailAddress, $subscriber['email_address']);

		// Check that the first name matches.
		if ($firstName) {
			$I->assertEquals($firstName, $results['subscribers'][0]['first_name']);	
		}

		// Check custom field key/value pairs.
		if ($customFields) {
			foreach ($customFields as $key => $value) {
				$I->assertArrayHasKey($key, $subscriber['fields']);
				$I->assertEquals($value, $subscriber['fields'][$key]);
			}
		}
	}

	/**
	 * Checks if the given email address has the given tag.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	$I
	 * @param 	$emailAddress 	Email Address.
	 * @param 	$tagID 			Tag ID.
	 */
	public function apiCheckSubscriberHasTag($I, $emailAddress, $tagID)
	{
		// Get Subscribers.
		$subscribers = $this->apiGetSubscribersByTagID($tagID);
			
		$subscriberTagged = false;
		foreach ($subscribers as $subscriber) {
			if ($subscriber['subscriber']['email_address'] == $emailAddress) {
				$subscriberTagged = true;
				break;
			}
		}

		// Check that the Subscriber is tagged.
		$I->assertTrue($subscriberTagged);
	}

	/**
	 * Checks if the given email address does not have the given tag.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	$I
	 * @param 	$emailAddress 	Email Address.
	 * @param 	$tagID 			Tag ID.
	 */
	public function apiCheckSubscriberDoesNotHaveTag($I, $emailAddress, $tagID)
	{
		// Get Subscribers.
		$subscribers = $this->apiGetSubscribersByTagID($tagID);
			
		$subscriberTagged = false;
		foreach ($subscribers as $subscriber) {
			if ($subscriber['subscriber']['email_address'] == $emailAddress) {
				$subscriberTagged = true;
				break;
			}
		}

		// Check that the Subscriber is not tagged.
		$I->assertFalse($subscriberTagged);
	}

	/**
	 * Check the given email address does not exists as a subscriber on ConvertKit.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	AcceptanceTester $I 			AcceptanceTester
	 * @param 	string 			$emailAddress 	Email Address
	 */ 	
	public function apiCheckSubscriberDoesNotExist($I, $emailAddress)
	{
		// Run request.
		$results = $this->apiRequest('subscribers', 'GET', [
			'email_address' => $emailAddress,
		]);

		// Check no subscribers are returned by this request.
		$I->assertEquals(0, $results['total_subscribers']);
	}

	/**
	 * Unsubscribes the given email address. Useful for clearing the API
	 * between tests.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	string 			$emailAddress 	Email Address
	 */ 	
	public function apiUnsubscribe($emailAddress)
	{
		// Run request.
		$this->apiRequest('unsubscribe', 'PUT', [
			'email' => $emailAddress,
		]);
	}

	/**
	 * Returns all subscribers to the given Tag ID from the API.
	 * 
	 * @param 	int 	$tagID 	Tag ID.
	 * @return 	array
	 */
	public function apiGetSubscribersByTagID($tagID)
	{
		// Get first page of subscribers.
		$subscribers = $this->apiRequest('tags/'.$tagID.'/subscriptions', 'GET');
		$data = $subscribers['subscriptions'];
		$totalPages = $subscribers['total_pages'];

		if ($totalPages == 1) {
			return $data;
		}

		// Get additional pages of purchases.
		for ($page = 2; $page <= $totalPages; $page++) {
			$subscribers = $this->apiRequest('tags/'.$tagID.'/subscriptions', 'GET', [
				'page' => $page,
			]);

			$data = array_merge($data, $subscribers['subscriptions']);
		}

		return $data;
	}

	/**
	 * Sends a request to the ConvertKit API, typically used to read an endpoint to confirm
	 * that data in an Acceptance Test was added/edited/deleted successfully.
	 * 
	 * @since 	1.2.1
	 * 
	 * @param 	string 	$endpoint 	Endpoint
	 * @param 	string 	$method 	Method (GET|POST|PUT)
	 * @param 	array 	$params 	Endpoint Parameters
	 * @return 	array
	 */
	public function apiRequest($endpoint, $method = 'GET', $params = array())
	{
		// Build query parameters.
		$params = array_merge($params, [
			'api_key' => $_ENV['CONVERTKIT_API_KEY'],
			'api_secret' => $_ENV['CONVERTKIT_API_SECRET'],
		]);

		// Send request.
		try {
			$client = new \GuzzleHttp\Client();
			$result = $client->request($method, 'https://api.convertkit.com/v3/' . $endpoint . '?' . http_build_query($params), [
				'headers' => [
					'Accept-Encoding' => 'gzip',
					'timeout'         => 5,
				],
			]);

			// Return JSON decoded response.
			return json_decode($result->getBody()->getContents(), true);
		} catch(\GuzzleHttp\Exception\ClientException $e) {
			return [];
		}
	}
}
