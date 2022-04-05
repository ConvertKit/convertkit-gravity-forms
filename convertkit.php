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
 * Version: 1.2.2
 * Author: ConvertKit
 * Author URI: https://convertkit.com/
 * Text Domain: convertkit
 */

// Define ConverKit Plugin paths and version number.
define( 'CKGF_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'CKGF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CKGF_PLUGIN_PATH', __DIR__ );
define( 'CKGF_PLUGIN_FILEPATH', __FILE__ );
define( 'CKGF_PLUGIN_VERSION', '1.2.2' );
define( 'CKGF_MIN_GF_VERSION', '1.9.3' );
define( 'CKGF_SLUG', 'ckgf' );
define( 'CKGF_TITLE', __( 'ConvertKit Gravity Forms Add-On', 'convertkit' ) );
define( 'CKGF_SHORT_TITLE', __( 'ConvertKit', 'convertkit' ) );

// Load files that are always used.
require_once CKGF_PLUGIN_PATH . '/includes/functions.php';
require_once CKGF_PLUGIN_PATH . '/includes/class-ckgf-api.php';
require_once CKGF_PLUGIN_PATH . '/includes/class-ckgf-log.php';
require_once CKGF_PLUGIN_PATH . '/includes/class-ckgf-review-request.php';
require_once CKGF_PLUGIN_PATH . '/includes/class-wp-ckgf.php';

/**
 * Main function to return Plugin instance.
 *
 * @since   1.2.1
 */
function WP_CKGF() { // phpcs:ignore

	return WP_CKGF::get_instance();

}

/**
 * Main function to return the Gravity Forms Integration class.
 *
 * @since   1.2.1
 */
function WP_CKGF_Integration() { // phpcs:ignore

	return GFConvertKit::get_instance();

}

// Finally, initialize the Plugin.
WP_CKGF();
