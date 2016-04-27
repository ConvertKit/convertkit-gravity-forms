<?php

if(!defined('ABSPATH')) { exit; }

function ckgf_convertkit_api_request($path, $query_args = array(), $request_body = null, $request_args = array()) {
	$path        = ltrim($path, '/');
	$request_url = "https://api.convertkit.com/v3/{$path}";

	$request_args = array_merge(array(
		'body'    => $request_body,
		'headers' => array(
			'Accept' => 'application/json',
		),
		'method'  => 'GET',
		'timeout' => 5,
	), $request_args);

	if(!isset($query_args['api_key'])) {
		$query_args['api_key'] = ckgf_instance()->get_api_key();
	}

	$request_url = add_query_arg($query_args, $request_url);
	$response    = wp_remote_request($request_url, $request_args);

	if(is_wp_error($response)) {
		return $response;
	} else {
		$response_body = wp_remote_retrieve_body($response);
		$response_data = json_decode($response_body, true);

		if(is_null($response_data)) {
			return new WP_Error('parse_failed', __('Could not parse response from ConvertKit'));
		} else if(isset($response_data['error']) && isset($response_data['message'])) {
			return new WP_Error($response_data['error'], $response_data['message']);
		} else {
			return $response_data;
		}
	}
}

function ckgf_convertkit_api_get_forms($api_key = null) {
	$query_args = is_null($api_key) ? array() : array('api_key' => $api_key);
	$response   = ckgf_convertkit_api_request('forms', $query_args, null);

	return is_wp_error($response) ? $response : (isset($response['forms']) ? array_combine(wp_list_pluck($response['forms'], 'id'), $response['forms']) : array());
}

function ckgf_convertkit_api_add_email($form, $email, $name, $api_key = null) {
	$query_args = is_null($api_key) ? array() : array('api_key' => $api_key);

	return ckgf_convertkit_api_request(sprintf('forms/%d/subscribe', $form), $query_args, array(
		'name'  => $name,
		'email' => $email,
	), array(
		'method' => 'POST',
	));
}
