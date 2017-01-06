<?php

if(!defined('ABSPATH')) { exit; }

/**
 * Class CKGF_Components_GravityForms
 */
class CKGF_Components_GravityForms {

	/**
	 * Initialize the class
	 */
	public static function init() {
		self::add_actions();
	}

	/**
	 * Plugin Hooks
	 */
	private static function add_actions() {
		add_action('gform_loaded', array(__CLASS__, 'load'), 11);
	}

	/**
	 * Register the plugin's GFFeedAddOn to Gravity Forms
	 *
	 */
	public static function load() {
		if(!method_exists('GFForms', 'include_payment_addon_framework')) {
			return;
		}

	    require_once('class-gf-convertkit.php');

		GFAddOn::register('GFConvertKit');
	}

}

require_once('functions/gravityforms.php');

CKGF_Components_GravityForms::init();
