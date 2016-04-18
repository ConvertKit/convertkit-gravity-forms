<?php

if(!defined('ABSPATH')) { exit; }

if(!function_exists('ckgf_instance')) {
	function ckgf_instance() {
		return GFConvertKit::get_instance();
	}
}
