<?php
/**
 * Plugin Name: Gravity Forms ConvertKit Add-On
 * Description: Integrates Gravity Forms with ConvertKit allowing form submissions to be automatically sent to your ConvertKit account.
 * Version: 1.0.3
 * Author: ConvertKit
 * Author URI: https://convertkit.com/
 * Text Domain: convertkit
 */

if(!defined('ABSPATH')) { exit; }

if(!defined('CKGF_CACHE_PERIOD')) {
	define('CKGF_CACHE_PERIOD', 6 * HOUR_IN_SECONDS);
}

if(!defined('CKGF_PLUGIN_BASENAME')) {
	define('CKGF_PLUGIN_BASENAME', plugin_basename(__FILE__));
}

if(!defined('CKGF_PLUGIN_DIRPATH')) {
	define('CKGF_PLUGIN_DIRPATH', trailingslashit(dirname(__FILE__)));
}

if(!defined('CKGF_PLUGIN_FILEPATH')) {
	define('CKGF_PLUGIN_FILEPATH', __FILE__);
}

if(!defined('CKGF_VERSION')) {
	define('CKGF_VERSION', '1.0.3');
}

if(!defined('CKGF_MIN_GF_VERSION')) {
	define('CKGF_MIN_GF_VERSION', '1.9.3');
}

if(!defined('CKGF_SLUG')) {
	define('CKGF_SLUG', 'ckgf');
}

if(!defined('CKGF_TITLE')) {
	define('CKGF_TITLE', 'Gravity Forms ConvertKit Add-On');
}

if(!defined('CKGF_SHORT_TITLE')) {
	define('CKGF_SHORT_TITLE', 'ConvertKit');
}

// Require the plugin's function definitions
// These files provide generic functions that don't really belong as part of a component
require_once(path_join(CKGF_PLUGIN_DIRPATH, 'functions/convertkit.php'));
require_once(path_join(CKGF_PLUGIN_DIRPATH, 'functions/utility.php'));

// Gravity Forms Integration
require_once(path_join(CKGF_PLUGIN_DIRPATH, 'components/gravityforms/gravityforms.php'));
