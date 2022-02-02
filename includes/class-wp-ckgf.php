<?php
/**
 * ConvertKit Gravity Forms class.
 *
 * @package CKGF
 * @author ConvertKit
 */

/**
 * Class ConvertKit Gravity Forms
 *
 * @package CKGF
 * @author ConvertKit
 */
class WP_CKGF {

	/**
	 * Holds the class object.
	 *
	 * @since   1.2.1
	 *
	 * @var     object
	 */
	public static $instance;

	/**
	 * Constructor. Acts as a bootstrap to load the rest of the plugin
	 *
	 * @since   1.2.1
	 */
	public function __construct() {

		// Register integration.
		add_action( 'gform_loaded', array( $this, 'gravity_forms_integrations_register' ), 5 );

		// Load language files.
		add_action( 'init', array( $this, 'load_language_files' ) );

	}

	/**
	 * Register this Plugin's Gravity Forms integration class as a Gravity Forms Integration.
	 *
	 * @since   1.2.1
	 */
	public function gravity_forms_integrations_register() {

		// Don't register integration if Gravity Forms Feed Framework doesn't exist.
		if ( ! method_exists( 'GFForms', 'include_payment_addon_framework' ) ) {
			return;
		}

		// Load integration.
		require_once CKGF_PLUGIN_PATH . '/includes/class-gfconvertkit.php';

		// Register integration.
		GFAddOn::register( 'GFConvertKit' );

	}

	/**
	 * Loads plugin textdomain
	 *
	 * @since   1.0.0
	 */
	public function load_language_files() {

		load_plugin_textdomain( 'convertkit', false, basename( dirname( CKGF_PLUGIN_BASENAME ) ) . '/languages/' );

	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @since   1.2.1
	 *
	 * @return  object Class.
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

}
