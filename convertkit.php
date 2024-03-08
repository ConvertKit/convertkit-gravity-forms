<?php
/**
 * ConvertKit for Gravity Forms Plugin.
 *
 * @package CKGF
 * @author ConvertKit
 *
 * @wordpress-plugin
 * Plugin Name: ConvertKit for Gravity Forms
 * Description: Integrates Gravity Forms with ConvertKit allowing form submissions to be automatically sent to your ConvertKit account.
 * Version: 1.4.3
 * Author: ConvertKit
 * Author URI: https://convertkit.com/
 * Text Domain: convertkit
 */

// Define ConverKit Plugin paths and version number.
define( 'CKGF_PLUGIN_NAME', 'ConvertKitGravityForms' ); // Used for user-agent in API class.
define( 'CKGF_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'CKGF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CKGF_PLUGIN_PATH', __DIR__ );
define( 'CKGF_PLUGIN_FILEPATH', __FILE__ );
define( 'CKGF_PLUGIN_VERSION', '1.4.3' );
define( 'CKGF_MIN_GF_VERSION', '1.9.3' );
define( 'CKGF_SLUG', 'ckgf' );
define( 'CKGF_TITLE', __( 'ConvertKit Gravity Forms Add-On', 'convertkit' ) );
define( 'CKGF_SHORT_TITLE', __( 'ConvertKit', 'convertkit' ) );

// Load shared classes, if they have not been included by another ConvertKit Plugin.
if ( ! class_exists( 'ConvertKit_API' ) ) {
	require_once CKGF_PLUGIN_PATH . '/vendor/convertkit/convertkit-wordpress-libraries/src/class-convertkit-api.php';
}
if ( ! class_exists( 'ConvertKit_Log' ) ) {
	require_once CKGF_PLUGIN_PATH . '/vendor/convertkit/convertkit-wordpress-libraries/src/class-convertkit-log.php';
}
if ( ! class_exists( 'ConvertKit_Review_Request' ) ) {
	require_once CKGF_PLUGIN_PATH . '/vendor/convertkit/convertkit-wordpress-libraries/src/class-convertkit-review-request.php';
}

// Load files that are always used.
require_once CKGF_PLUGIN_PATH . '/includes/functions.php';
require_once CKGF_PLUGIN_PATH . '/includes/class-ckgf-api.php';
require_once CKGF_PLUGIN_PATH . '/includes/class-ckgf-notices.php';
require_once CKGF_PLUGIN_PATH . '/includes/class-wp-ckgf.php';

/**
 * Main function to return Plugin instance.
 *
 * @since   1.2.1
 */
function WP_CKGF() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName

	return WP_CKGF::get_instance();

}

/**
 * Main function to return the Gravity Forms Integration class.
 *
 * @since   1.2.1
 */
function WP_CKGF_Integration() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName

	return GFConvertKit::get_instance();

}

// Finally, initialize the Plugin.
WP_CKGF();
