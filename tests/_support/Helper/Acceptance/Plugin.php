<?php
namespace Helper\Acceptance;

/**
 * Helper methods and actions related to the ConvertKit Plugin,
 * which are then available using $I->{yourFunctionName}.
 *
 * @since   1.2.1
 */
class Plugin extends \Codeception\Module
{
	/**
	 * Helper method to activate the ConvertKit Plugin, checking
	 * it activated and no errors were output.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I AcceptanceTester.
	 */
	public function activateConvertKitPlugin($I)
	{
		$I->activateThirdPartyPlugin($I, 'convertkit-gravity-forms');
	}

	/**
	 * Helper method to deactivate the ConvertKit Plugin, checking
	 * it activated and no errors were output.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I AcceptanceTester.
	 */
	public function deactivateConvertKitPlugin($I)
	{
		$I->deactivateThirdPartyPlugin($I, 'convertkit-gravity-forms');
	}

	/**
	 * Helper method to setup the Plugin's API Key and Secret, and enable the integration.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I  AcceptanceTester.
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
	 * Helper method to load the Gravity Forms > Settings > ConvertKit screen.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I  AcceptanceTester.
	 */
	public function loadConvertKitSettingsScreen($I)
	{
		$I->amOnAdminPage('admin.php?page=gf_settings&subview=ckgf');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Helper method to determine that the order of the Form resources in the given
	 * select element are in the expected alphabetical order.
	 *
	 * @since   1.3.1
	 *
	 * @param   AcceptanceTester $I                 AcceptanceTester.
	 * @param   string           $selectElement     <select> element.
	 * @param   bool|array       $prependOptions    Option elements that should appear before the resources.
	 */
	public function checkSelectFormOptionOrder($I, $selectElement, $prependOptions = false)
	{
		// Define options.
		$options = [
			'AAA Test', // First item.
			'WooCommerce Product Form', // Last item.
		];

		// Prepend options, such as 'Default' and 'None' to the options, if required.
		if ( $prependOptions ) {
			$options = array_merge( $prependOptions, $options );
		}

		// Check order.
		$I->checkSelectOptionOrder($I, $selectElement, $options);
	}

	/**
	 * Helper method to determine that the order of the Tag resources in the given
	 * select element are in the expected alphabetical order.
	 *
	 * @since   1.3.1
	 *
	 * @param   AcceptanceTester $I                 AcceptanceTester.
	 * @param   string           $selectElement     <select> element.
	 * @param   bool|array       $prependOptions    Option elements that should appear before the resources.
	 */
	public function checkSelectTagOptionOrder($I, $selectElement, $prependOptions = false)
	{
		// Define options.
		$options = [
			'gravityforms-tag-1', // First item.
			'wpforms', // Last item.
		];

		// Prepend options, such as 'Default' and 'None' to the options, if required.
		if ( $prependOptions ) {
			$options = array_merge( $prependOptions, $options );
		}

		// Check order.
		$I->checkSelectOptionOrder($I, $selectElement, $options);
	}

	/**
	 * Helper method to determine that the order of the Custom Field resources in the given
	 * select element are in the expected alphabetical order.
	 *
	 * @since   1.3.1
	 *
	 * @param   AcceptanceTester $I                 AcceptanceTester.
	 * @param   string           $selectElement     <select> element.
	 * @param   bool|array       $prependOptions    Option elements that should appear before the resources.
	 */
	public function checkSelectCustomFieldOptionOrder($I, $selectElement, $prependOptions = false)
	{
		// Define options.
		$options = [
			'Billing Address', // First item.
			'Last Name', // Second item.
			'Add Custom Key', // Last item.
		];

		// Prepend options, such as 'Default' and 'None' to the options, if required.
		if ( $prependOptions ) {
			$options = array_merge( $prependOptions, $options );
		}

		// Check order.
		$I->checkSelectOptionOrder($I, $selectElement, $options);
	}

	/**
	 * Helper method to determine the order of <option> values for the given select element
	 * and values.
	 *
	 * @since   1.3.1
	 *
	 * @param   AcceptanceTester $I             AcceptanceTester.
	 * @param   string           $selectElement <select> element.
	 * @param   array            $values        <option> values.
	 */
	public function checkSelectOptionOrder($I, $selectElement, $values)
	{
		foreach ( $values as $i => $value ) {
			// Define the applicable CSS selector.
			if ( $i === 0 ) {
				$nth = 'first-child';
			} elseif ( $i + 1 === count( $values ) ) {
				$nth = 'last-child';
			} else {
				$nth = 'nth-child(' . ( $i + 1 ) . ')';
			}

			$I->assertEquals(
				$I->grabTextFrom('select' . $selectElement . ' option:' . $nth),
				$value
			);
		}
	}

	/**
	 * Helper method to reset the ConvertKit Plugin settings, as if it's a clean installation.
	 *
	 * @since   1.2.2
	 *
	 * @param   AcceptanceTester $I AcceptanceTester.
	 */
	public function resetConvertKitPlugin($I)
	{
		// Plugin Settings.
		$I->dontHaveOptionInDatabase('gravityformsaddon_ckgf_settings');
		$I->dontHaveOptionInDatabase('gravityformsaddon_ckgf_version');

		// Review Request.
		$I->dontHaveOptionInDatabase('convertkit-gravity-forms-review-request');
		$I->dontHaveOptionInDatabase('convertkit-gravity-forms-review-dismissed');

		// Users.
		$I->dontHaveUserInDatabase('gravity_forms_user');
	}
}
