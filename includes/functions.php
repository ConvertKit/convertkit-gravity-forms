<?php
/**
 * ConvertKit for Gravity Forms general plugin functions.
 *
 * @package CKGF
 * @author ConvertKit
 */

/**
 * Helper method to return the Plugin Settings Link
 *
 * @since   1.2.1
 *
 * @param   array $query_args     Optional Query Args.
 * @return  string                  Settings Link
 */
function ckgf_get_settings_link( $query_args = array() ) {

	$query_args = array_merge(
		$query_args,
		array(
			'page'    => 'gf_settings',
			'subview' => 'ckgf',
		)
	);

	return add_query_arg( $query_args, admin_url( 'admin.php' ) );

}

/**
 * Helper method to return the URL the user needs to visit to sign in to their ConvertKit account.
 *
 * @since   1.2.1
 *
 * @return  string  ConvertKit Login URL.
 */
function ckgf_get_sign_in_url() {

	return 'https://app.convertkit.com/?utm_source=wordpress&utm_content=convertkit-gravity-forms';

}

/**
 * Helper method to return the URL the user needs to visit to sign up for a ConvertKit account.
 *
 * @since   1.2.1
 *
 * @return  string  ConvertKit Signup URL.
 */
function ckgf_get_signup_url() {

	return 'https://app.convertkit.com/users/signup?utm_source=wordpress&utm_content=convertkit-gravity-forms';

}

/**
 * Helper method to return the URL the user needs to visit on the ConvertKit app to obtain their API Key and Secret.
 *
 * @since   1.2.1
 *
 * @return  string  ConvertKit App URL.
 */
function ckgf_get_api_key_url() {

	return 'https://app.convertkit.com/account_settings/advanced_settings/?utm_source=wordpress&utm_content=convertkit-gravity-forms';

}

/**
 * Helper method to return the URL the user needs to visit on the ConvertKit app to upgrade their account.
 *
 * @since   1.3.7
 *
 * @return  string  ConvertKit App URL.
 */
function ckgf_get_settings_billing_url() {

	return 'https://app.convertkit.com/account_settings/billing/?utm_source=wordpress&utm_content=convertkit-gravity-forms';

}
