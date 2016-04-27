<?php

if(!defined('ABSPATH')) { exit; }

if(!function_exists('ckgf_debug')) {
	/**
	 * If logging is enabled using the `CKGF_DEBUG` constant, then dump the values passed into this
	 * function into the error log.
	 *
	 * @param mixed $variable,... unlimited optional number of additional variables to debug
	 * @return void
	 */
	function ckgf_debug($variable) {
		if(defined('CKGF_DEBUG') && CKGF_DEBUG && is_file(CKGF_DEBUG) && is_writable(CKGF_DEBUG)) {
			$variables = func_get_args();
			$backtrace = debug_backtrace();

			$tracefile = str_replace(CKGF_PLUGIN_DIRPATH, '', $backtrace[0]['file']);
			$traceline = $backtrace[0]['line'];

			foreach($variables as $variable) {
				$fileline = "{$tracefile}::{$traceline}";

				if(is_scalar($variable)) {
					file_put_contents(CKGF_DEBUG, "{$fileline} - {$variable}\n", FILE_APPEND);
				} else {
					file_put_contents(CKGF_DEBUG, "{$fileline} - complex\n", FILE_APPEND);
					file_put_contents(CKGF_DEBUG, print_r($variable, true), FILE_APPEND);
				}
			}
		}
	}
}

if(!function_exists('ckgf_redirect')) {
	/**
	 * Wrapper function for `wp_redirect` so a developer doesn't have to remember
	 * to call `exit` after `wp_redirect`.
	 *
	 * @param string $url The url to redirect the requestor to
	 * @param int $code The HTTP status code to send when redirecting
	 * @return void
	 */
	function ckgf_redirect($url, $code = 302) {
		wp_redirect($url, $code);
		exit;
	}
}

if(!function_exists('ckgf_requirements_met')) {
	/**
	 * Returns a value indicating whether the "ConvertKit: Gravity Forms" plugin's requirements are met. This means that the
	 * plugins that "ConvertKit: Gravity Forms" uses data or code from are present and of the appropriate minimum version.
	 *
	 * @return bool `true` if all "ConvertKit: Gravity Forms" requirements are met and `false` otherwise
	 */
	function ckgf_requirements_met() {
		return true;
	}
}

