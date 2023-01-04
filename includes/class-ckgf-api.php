<?php
/**
 * ConvertKit API class for Gravity Forms.
 *
 * @package CKWC
 * @author ConvertKit
 */

/**
 * ConvertKit API class for Gravity Forms.
 *
 * @package CKWC
 * @author ConvertKit
 */
class CKGF_API extends ConvertKit_API {

	/**
	 * Sets up the API with the required credentials.
	 *
	 * @since   1.2.1
	 *
	 * @param   bool|string $api_key        ConvertKit API Key.
	 * @param   bool|string $api_secret     ConvertKit API Secret.
	 * @param   bool|object $debug          Save data to log.
	 */
	public function __construct( $api_key = false, $api_secret = false, $debug = false ) {

		// Set API credentials, debugging and logging class.
		$this->api_key        = $api_key;
		$this->api_secret     = $api_secret;
		$this->debug          = $debug;
		$this->plugin_name    = ( defined( 'CKGF_PLUGIN_NAME' ) ? CKGF_PLUGIN_NAME : false );
		$this->plugin_path    = ( defined( 'CKGF_PLUGIN_PATH' ) ? CKGF_PLUGIN_PATH : false );
		$this->plugin_url     = ( defined( 'CKGF_PLUGIN_URL' ) ? CKGF_PLUGIN_URL : false );
		$this->plugin_version = ( defined( 'CKGF_PLUGIN_VERSION' ) ? CKGF_PLUGIN_VERSION : false );

		// Setup logging class if the required parameters exist.
		if ( $this->debug && $this->plugin_path !== false ) {
			$this->log = new ConvertKit_Log( $this->plugin_path );
		}

		// Define translatable / localized error strings.
		// WordPress requires that the text domain be a string (e.g. 'convertkit') and not a variable,
		// otherwise localization won't work.
		$this->error_messages = array(
			'form_subscribe_form_id_empty'                => __( 'form_subscribe(): the form_id parameter is empty.', 'convertkit' ),
			'form_subscribe_email_empty'                  => __( 'form_subscribe(): the email parameter is empty.', 'convertkit' ),

			'sequence_subscribe_sequence_id_empty'        => __( 'sequence_subscribe(): the sequence_id parameter is empty.', 'convertkit' ),
			'sequence_subscribe_email_empty'              => __( 'sequence_subscribe(): the email parameter is empty.', 'convertkit' ),

			'tag_subscribe_tag_id_empty'                  => __( 'tag_subscribe(): the tag_id parameter is empty.', 'convertkit' ),
			'tag_subscribe_email_empty'                   => __( 'tag_subscribe(): the email parameter is empty.', 'convertkit' ),

			'get_subscriber_by_email_email_empty'         => __( 'get_subscriber_by_email(): the email parameter is empty.', 'convertkit' ),
			/* translators: Email Address */
			'get_subscriber_by_email_none'                => __( 'No subscriber(s) exist in ConvertKit matching the email address %s.', 'convertkit' ),

			'get_subscriber_by_id_subscriber_id_empty'    => __( 'get_subscriber_by_id(): the subscriber_id parameter is empty.', 'convertkit' ),

			'get_subscriber_tags_subscriber_id_empty'     => __( 'get_subscriber_tags(): the subscriber_id parameter is empty.', 'convertkit' ),

			'unsubscribe_email_empty'                     => __( 'unsubscribe(): the email parameter is empty.', 'convertkit' ),

			'get_all_posts_posts_per_request_bound_too_low' => __( 'get_all_posts(): the posts_per_request parameter must be equal to or greater than 1.', 'convertkit' ),
			'get_all_posts_posts_per_request_bound_too_high' => __( 'get_all_posts(): the posts_per_request parameter must be equal to or less than 50.', 'convertkit' ),

			'get_posts_page_parameter_bound_too_low'      => __( 'get_posts(): the page parameter must be equal to or greater than 1.', 'convertkit' ),
			'get_posts_per_page_parameter_bound_too_low'  => __( 'get_posts(): the per_page parameter must be equal to or greater than 1.', 'convertkit' ),
			'get_posts_per_page_parameter_bound_too_high' => __( 'get_posts(): the per_page parameter must be equal to or less than 50.', 'convertkit' ),

			/* translators: HTTP method */
			'request_method_unsupported'                  => __( 'API request method %s is not supported in ConvertKit_API class.', 'convertkit' ),
			'request_rate_limit_exceeded'                 => __( 'ConvertKit API Error: Rate limit hit.', 'convertkit' ),
			'request_internal_server_error'               => __( 'ConvertKit API Error: Internal server error.', 'convertkit' ),
			'request_bad_gateway'                         => __( 'ConvertKit API Error: Bad gateway.', 'convertkit' ),
			'response_type_unexpected'                    => __( 'ConvertKit API Error: The response is not of the expected type array.', 'convertkit' ),
		);

	}

}
