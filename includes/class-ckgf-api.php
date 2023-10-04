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

	}

}
