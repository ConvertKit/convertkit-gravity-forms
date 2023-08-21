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
	private static $instance;

	/**
	 * Holds singleton initialized classes that include
	 * action and filter hooks.
	 *
	 * @since   1.2.2
	 *
	 * @var     array
	 */
	private $classes = array();

	/**
	 * Constructor. Acts as a bootstrap to load the rest of the plugin
	 *
	 * @since   1.2.1
	 */
	public function __construct() {

		// Register integration.
		add_action( 'gform_loaded', array( $this, 'gravity_forms_integrations_register' ), 5 );

		// Initialize.
		add_action( 'init', array( $this, 'init' ) );

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
	 * Initialize admin, frontend and global Plugin classes.
	 *
	 * @since   1.2.2
	 */
	public function init() {

		// Initialize class(es) to register hooks.
		$this->initialize_admin();
		$this->initialize_frontend();
		$this->initialize_global();

	}

	/**
	 * Initialize classes for the WordPress Administration interface
	 *
	 * @since   1.2.2
	 */
	private function initialize_admin() {

		// Bail if this request isn't for the WordPress Administration interface.
		if ( ! is_admin() ) {
			return;
		}

		/**
		 * Initialize integration classes for the WordPress Administration interface.
		 *
		 * @since   1.2.2
		 */
		do_action( 'convertkit_gravity_forms_initialize_admin' );

	}

	/**
	 * Initialize classes for the frontend web site
	 *
	 * @since   1.2.2
	 */
	private function initialize_frontend() {

		// Bail if this request isn't for the frontend web site.
		if ( is_admin() ) {
			return;
		}

		/**
		 * Initialize integration classes for the frontend web site.
		 *
		 * @since   1.2.2
		 */
		do_action( 'convertkit_gravity_forms_initialize_frontend' );

	}

	/**
	 * Initialize classes required globally, across the WordPress Administration, CLI, Cron and Frontend
	 * web site.
	 *
	 * @since   1.2.2
	 */
	private function initialize_global() {

		$this->classes['review_request'] = new ConvertKit_Review_Request( 'ConvertKit for Gravity Forms', 'convertkit-gravity-forms', CKGF_PLUGIN_PATH );

		/**
		 * Initialize integration classes for the frontend web site.
		 *
		 * @since   1.2.2
		 */
		do_action( 'convertkit_gravity_forms_initialize_global' );

	}

	/**
	 * Loads the plugin's translated strings, if available.
	 *
	 * @since   1.0.0
	 */
	public function load_language_files() {

		// If the .mo file for a given language is available in WP_LANG_DIR/convertkit
		// i.e. it's available as a translation at https://translate.wordpress.org/projects/wp-plugins/convertkit-gravoty-forms/,
		// it will be used instead of the .mo file in convertkit-gravoty-forms/languages.
		load_plugin_textdomain( 'convertkit', false, 'convertkit-gravity-forms/languages' );

	}

	/**
	 * Returns the given class
	 *
	 * @since   1.2.2
	 *
	 * @param   string $name   Class Name.
	 * @return  object          Class Object
	 */
	public function get_class( $name ) {

		// If the class hasn't been loaded, throw a WordPress die screen
		// to avoid a PHP fatal error.
		if ( ! isset( $this->classes[ $name ] ) ) {
			// Define the error.
			$error = new WP_Error(
				'convertkit_gravity_forms_get_class',
				sprintf(
					/* translators: %1$s: PHP class name */
					__( 'ConvertKit for Gravity Forms Error: Could not load Plugin class <strong>%1$s</strong>', 'convertkit' ),
					$name
				)
			);

			// Depending on the request, return or display an error.
			// Admin UI.
			if ( is_admin() ) {
				wp_die(
					esc_attr( $error->get_error_message() ),
					esc_html__( 'ConvertKit for Gravity Forms Error', 'convertkit' ),
					array(
						'back_link' => true,
					)
				);
			}

			// Cron / CLI.
			return $error;
		}

		// Return the class object.
		return $this->classes[ $name ];

	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @since   1.2.1
	 *
	 * @return  object Class.
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

}
