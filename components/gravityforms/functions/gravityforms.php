<?php

if(!defined('ABSPATH')) { exit; }

if(!function_exists('ckgf_instance')) {
	/**
	 * Get the instance of the GFFeedAddOn
	 *
	 * @return GFConvertKit|null
	 */
	function ckgf_instance() {
		return GFConvertKit::get_instance();
	}
}
