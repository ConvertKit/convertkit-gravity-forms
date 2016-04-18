<?php

if(!defined('ABSPATH')) { exit; }

class CKGF_Components_GravityForms {
	public static function init() {
		self::add_actions();
		self::add_filters();
	}

	private static function add_actions() {
		if(is_admin()) {

		} else {

		}

		add_action('gform_loaded', array(__CLASS__, 'load'), 11);
	}

	private static function add_filters() {
		if(is_admin()) {

		} else {

		}

	}

	#region Load Add-On

	public static function load() {
		if(!method_exists('GFForms', 'include_payment_addon_framework')) {
			return;
		}

	    require_once('class-gf-convertkit.php');

		GFAddOn::register('GFConvertKit');
	}

	#endregion Load Add-On
}

require_once('functions/gravityforms.php');

CKGF_Components_GravityForms::init();
