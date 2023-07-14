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
	 * Helper method to programmatically setup the Plugin's API Key and Secret,
	 * enabling debug logging.
	 *
	 * @since   1.2.1
	 *
	 * @param   AcceptanceTester $I              AcceptanceTester.
	 * @param   bool|string      $apiKey         API Key (if specified, used instead of CONVERTKIT_API_KEY).
	 * @param   bool|string      $apiSecret      API Secret (if specified, used instead of CONVERTKIT_API_SECRET).
	 * @param   bool|string      $debug          Debug log enabled.
	 */
	public function setupConvertKitPlugin($I, $apiKey = false, $apiSecret = false, $debug = false)
	{
		$I->haveOptionInDatabase(
			'gravityformsaddon_ckgf_settings',
			[
				'api_key'    => ( $apiKey !== false ? $apiKey : $_ENV['CONVERTKIT_API_KEY'] ),
				'api_secret' => ( $apiSecret !== false ? $apiSecret : $_ENV['CONVERTKIT_API_SECRET'] ),
				'debug'      => ( $debug !== false ? '1' : '0' ),
			]
		);
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
