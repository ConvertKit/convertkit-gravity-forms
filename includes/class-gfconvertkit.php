<?php
/**
 * ConvertKit Gravity Forms Integration class.
 *
 * @package CKGF
 * @author ConvertKit
 */

GFForms::include_feed_addon_framework();

/**
 * Registers ConvertKit as a Feed Addon for Gravity Forms Feeds.
 *
 * @package CKGF
 * @author ConvertKit
 */
class GFConvertKit extends GFFeedAddOn {

	/**
	 * Holds the Plugin version number.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $_version = CKGF_PLUGIN_VERSION; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	/**
	 * Holds the minimum required Gravity Forms version for this integration
	 * to be registered.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $_min_gravityforms_version = CKGF_MIN_GF_VERSION; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	/**
	 * Holds the slug that stores settings and is used for referencing
	 * this integration.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $_slug = CKGF_SLUG; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	/**
	 * Holds the path and filename to the Plugin.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $_path = CKGF_PLUGIN_BASENAME; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	/**
	 * Holds the full path to this Plugin.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $_full_path = CKGF_PLUGIN_FILEPATH; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	/**
	 * Holds the full title of this Plugin.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $_title = CKGF_TITLE; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	/**
	 * Holds the short title of this Plugin.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $_short_title = CKGF_SHORT_TITLE; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	/**
	 * Holds a list of capabilities to add to roles for Members plugin (https://wordpress.org/plugins/members/) integration.
	 *
	 * @since   1.2.1
	 *
	 * @var     array
	 */
	protected $_capabilities = array( 'ckgf_convertkit_settings_page', 'ckgf_convertkit_form_page', 'ckgf_convertkit_uninstall' ); // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	/**
	 * Holds capabilities or roles that have access to this Plugin's Settings page.
	 *
	 * @since   1.2.1
	 *
	 * @var     string
	 */
	protected $_capabilities_settings_page = 'ckgf_convertkit_settings_page'; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	/**
	 * Holds capabilities or roles that have access to this Plugin's Form Settings page.
	 *
	 * @since   1.2.5
	 *
	 * @var     string
	 */
	protected $_capabilities_form_settings = 'ckgf_convertkit_form_page'; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	/**
	 * Holds capabilities or roles that can install this Plugin.
	 *
	 * @since   1.2.1
	 *
	 * @var     string
	 */
	protected $_capabilities_uninstall = 'ckgf_convertkit_uninstall'; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	/**
	 * Holds the class object.
	 *
	 * @since   1.0.0
	 *
	 * @var     object
	 */
	private static $_instance = null; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	/**
	 * Holds the key to store the Creator Network Recommendations JS URL in.
	 *
	 * @since   1.3.7
	 *
	 * @var     string
	 */
	private $creator_network_recommendations_script_key = 'ckgf_creator_network_recommendations_script';

	/**
	 * Holds the API instance.
	 *
	 * @since   1.2.1
	 *
	 * @var     CKGF_API
	 */
	private $api;

	/**
	 * Called when the integration is initialized by Gravity Forms.
	 *
	 * @since   1.0.0
	 */
	public function init() {

		// Initialize parent class.
		parent::init();

		// Register support for sending data to ConvertKit once payment is received from a payment gateway,
		// such as PayPal.
		$this->add_delayed_payment_support(
			array(
				'option_label' => esc_html__( 'Send to ConvertKit only when payment is received.', 'convertkit' ),
			)
		);

		// Register fields on the Form Settings screen.
		add_filter( 'gform_form_settings_fields', array( $this, 'add_form_settings_fields' ), 10, 1 );

		// Output Creator Network Recommendations script, if enabled on the Form.
		add_filter( 'gform_enqueue_scripts', array( $this, 'maybe_enqueue_creator_network_recommendations_script' ), 10, 2 );

	}

	/**
	 * Registers a section in each Gravity Forms' "Form Settings" screen, displaying
	 * an option to enable the Creator Network recommendations script if available on the ConvertKit account.
	 *
	 * @since   1.3.7
	 *
	 * @param   array $fields     Settings Fields.
	 * @return  array               Settings Fields
	 */
	public function add_form_settings_fields( $fields ) {

		// If "Output HTML5" is disabled at Forms > Settings, don't show an option.
		// This would render an email field as input[type=text], which the Creator
		// Network Recommendations script does not recognize.
		if ( ! (bool) get_option( 'rg_gforms_enable_html5' ) ) {
			return array_merge(
				$fields,
				$this->get_creator_network_form_setting_field(
					false,
					sprintf(
						'%s <a href="%s">%s</a>',
						esc_html__( 'HTML5 output is required for proper function. Please enable this in ', 'convertkit' ),
						esc_url( admin_url( 'admin.php?page=gf_settings' ) ),
						esc_html__( 'Gravity Forms settings.', 'convertkit' )
					)
				)
			);
		}

		// If no API Key and Secret is specified, don't show an option.
		if ( ! $this->has_api_key_and_secret() ) {
			return array_merge(
				$fields,
				$this->get_creator_network_form_setting_field(
					false,
					sprintf(
						'%s <a href="%s">%s</a>',
						esc_html__( 'Please enter your API Key and Secret on the', 'convertkit' ),
						esc_url( ckgf_get_settings_link() ),
						esc_html__( 'settings screen', 'convertkit' )
					)
				)
			);
		}

		// Query API to fetch Creator Network Recommendations script.
		$result = $this->get_creator_network_recommendations_script( true );

		// If an error occured, don't show an option.
		if ( is_wp_error( $result ) ) {
			return array_merge(
				$fields,
				$this->get_creator_network_form_setting_field(
					false,
					sprintf(
						'%s. <a href="%s">%s</a>',
						$result->get_error_message(),
						esc_url( ckgf_get_settings_link() ),
						esc_html__( 'Fix settings', 'convertkit' )
					)
				)
			);
		}

		// If the result is false, the Creator Network is disabled - don't show an option.
		if ( ! $result ) {
			return array_merge(
				$fields,
				$this->get_creator_network_form_setting_field(
					false,
					sprintf(
						'%s <a href="%s">%s</a>',
						esc_html__( 'Creator Network Recommendations requires a', 'convertkit' ),
						esc_url( ckgf_get_settings_billing_url() ),
						esc_html__( 'paid ConvertKit Plan', 'convertkit' )
					)
				)
			);
		}

		// Creator Network is enabled.
		return array_merge(
			$fields,
			$this->get_creator_network_form_setting_field(
				true,
				__( 'If enabled, displays the Creator Network Recommendations modal when this form is submitted.', 'convertkit' )
			)
		);

	}

	/**
	 * Returns a section and settings field for a Gravity Form when editing its Form Settings,
	 * to enable/disable the Creator Network Recommendations modal.
	 *
	 * @since   1.3.7
	 *
	 * @param   bool   $enabled_on_account     Creator Network is available. If false, only the description is returned.
	 * @param   string $description            Description.
	 * @return  array                           Settings Fields
	 */
	public function get_creator_network_form_setting_field( $enabled_on_account, $description ) {

		// If the Creator Network feature isn't enabled on the ConvertKit account,
		// just show the description.
		if ( ! $enabled_on_account ) {
			return array(
				'ckgf' => array(
					'title'  => CKGF_SHORT_TITLE,
					'fields' => array(
						array(
							'name' => 'ckgf_enable_creator_network_recommendations_description',
							'type' => 'html',
							'html' => $description,
						),

						// Ensure that the setting is set to disabled if the form settings are saved.
						array(
							'name'  => 'ckgf_enable_creator_network_recommendations',
							'type'  => 'hidden',
							'value' => false,
						),
					),
				),
			);
		}

		// Return field to toggle the setting.
		return array(
			'ckgf' => array(
				'title'  => CKGF_SHORT_TITLE,
				'fields' => array(
					array(
						'name'    => 'ckgf_enable_creator_network_recommendations',
						'type'    => 'toggle',
						'label'   => esc_html__( 'Enable Creator Network Recommendations', 'convertkit' ),
						'tooltip' => $description,
					),
				),
			),
		);

	}

	/**
	 * Enqueues the Creator Network Recommendations script, if the Gravity Forms form
	 * has the 'Enable Creator Network Recommendations' setting enabled.
	 *
	 * @since   1.3.7
	 *
	 * @param   array $form       Gravity Forms Form.
	 * @param   bool  $is_ajax    If AJAX is enabled for form submission.
	 */
	public function maybe_enqueue_creator_network_recommendations_script( $form, $is_ajax ) {

		// Bail if AJAX submission is disabled; we can't show the Creator Network Recommendations
		// if the page reloads on form submission.
		if ( ! $is_ajax ) {
			return;
		}

		// Bail if Creator Network Recommendations are disabled.
		if ( ! array_key_exists( 'ckgf_enable_creator_network_recommendations', $form ) ) {
			return;
		}
		if ( ! $form['ckgf_enable_creator_network_recommendations'] ) {
			return;
		}

		// Fetch Creator Network Recommendations script URL.
		$script_url = $this->get_creator_network_recommendations_script();

		// Bail if an error occured fetching the script, or no script exists,
		// because Creator Network Recommendations are not enabled on the
		// ConvertKit account.
		if ( is_wp_error( $script_url ) ) {
			return;
		}
		if ( ! $script_url ) {
			return;
		}

		// Enqueue script.
		wp_enqueue_script( 'ckgf-creator-network-recommendations', $script_url, array(), CKGF_PLUGIN_VERSION, true );

	}

	/**
	 * Register CSS to load for this Integration on Gravity Form screens.
	 *
	 * @since   1.2.1
	 *
	 * @return  array
	 */
	public function styles() {

		// Initialize parent styles.
		parent::styles();

		return array(
			array(
				'handle'  => 'ckgf_form_settings_css',
				'src'     => CKGF_PLUGIN_URL . 'resources/backend/css/form-settings.css',
				'version' => CKGF_PLUGIN_VERSION,
				'enqueue' => array(
					array(
						'admin_page' => array(
							'form_editor',
							'form_list',
							'form_settings',
							'plugin_settings',
							'app_settings',
							'plugin_page',
						),
					),
				),
			),
		);

	}

	/**
	 * Return the CSS class for the Plugin's icon, used on Form Settings Menus.
	 *
	 * @since   1.2.1
	 *
	 * @return  string
	 */
	public function get_menu_icon() {

		// Must be prefixed with gform-icon--, otherwise no CSS class is applied.
		return 'gform-icon--convertkit';

	}

	/**
	 * Define the ConvertKit settings in the Gravity Forms General Settings page.
	 *
	 * @since   1.0.0
	 *
	 * @return  array
	 */
	public function plugin_settings_fields() {

		return array(
			array(
				'title'       => __( 'Settings', 'convertkit' ),
				'description' => '<p>' . esc_html__( 'ConvertKit makes it easy to send drip email courses to your customers. Use Gravity Forms to collect customer information and automatically add them to your ConvertKit forms.', 'convertkit' ) . '</p>',
				'fields'      => array(
					array(
						'name'                => 'api_key',
						'label'               => esc_html__( 'API Key', 'convertkit' ),
						'type'                => 'text',
						'class'               => 'medium',
						'required'            => true,
						'description'         => sprintf(
							/* translators: %1$s: Link to ConvertKit Account, %2$s: <br>, %3$s Link to ConvertKit Signup */
							esc_html__( '%1$s Required for proper plugin function. %2$s Don\'t have a ConvertKit account? %3$s', 'convertkit' ),
							'<p><a href="' . esc_url( ckgf_get_api_key_url() ) . '" target="_blank">' . esc_html__( 'Get your ConvertKit API Key.', 'convertkit' ) . '</a>',
							'<br />',
							'<a href="' . esc_url( ckgf_get_signup_url() ) . '" target="_blank">' . esc_html__( 'Sign up here.', 'convertkit' ) . '</a></p>'
						),
						'feedback_callback'   => array( $this, 'plugin_settings_fields_feedback_callback_api_key' ),
						'validation_callback' => array( $this, 'plugin_settings_fields_validation_callback_api_key' ),
					),
					array(
						'name'                => 'api_secret',
						'label'               => esc_html__( 'API Secret', 'convertkit' ),
						'type'                => 'text',
						'class'               => 'medium',
						'required'            => true,
						'description'         => sprintf(
							/* translators: %1$s: Link to ConvertKit Account, %2$s: <br>, %3$s Link to ConvertKit Signup */
							esc_html__( '%1$s Required for proper plugin function. %2$s Don\'t have a ConvertKit account? %3$s', 'convertkit' ),
							'<p><a href="' . esc_url( ckgf_get_api_key_url() ) . '" target="_blank">' . esc_html__( 'Get your ConvertKit API Secret.', 'convertkit' ) . '</a>',
							'<br />',
							'<a href="' . esc_url( ckgf_get_signup_url() ) . '" target="_blank">' . esc_html__( 'Sign up here.', 'convertkit' ) . '</a></p>'
						),
						'feedback_callback'   => array( $this, 'plugin_settings_fields_feedback_callback_api_secret' ),
						'validation_callback' => array( $this, 'plugin_settings_fields_validation_callback_api_secret' ),
					),
					array(
						'name'    => 'debug',
						'label'   => esc_html__( 'Debug', 'convertkit' ),
						'type'    => 'checkbox',
						'choices' => array(
							array(
								'name'    => 'debug',
								'label'   => esc_html__( 'Log requests to file.', 'convertkit' ),
								'tooltip' => '',
							),
						),
					),
				),
			),
		);

	}

	/**
	 * Validate that the API Key is valid when loading the settings screen, showing a
	 * tick or a cross.
	 *
	 * @since   1.0.0
	 *
	 * @param   string $api_key    API Key.
	 * @return  bool                API Key valid
	 */
	public function plugin_settings_fields_feedback_callback_api_key( $api_key ) {

		// Validation fails if the API Key is empty.
		if ( empty( $api_key ) ) {
			return false;
		}

		// Get Forms to test that the API Key is valid.
		$api   = new CKGF_API(
			$api_key,
			$this->api_secret(),
			$this->debug_enabled()
		);
		$forms = $api->get_forms();

		if ( is_wp_error( $forms ) ) {
			return false;
		}

		return true;

	}

	/**
	 * Validate that the API Key is valid when saving settings, showing a tooltip
	 * with a contextual message containing the error if the API Key is invalid.
	 *
	 * @since   1.2.1
	 *
	 * @param   array  $field      Settings Field.
	 * @param   string $api_key     API Key.
	 * @return  bool                API Key valid
	 */
	public function plugin_settings_fields_validation_callback_api_key( $field, $api_key ) {

		// Get Forms to test that the API Key is valid.
		$api   = new CKGF_API(
			$api_key,
			$this->api_secret(),
			$this->debug_enabled()
		);
		$forms = $api->get_forms();

		// If an error occured, set the field's error so that an exclamation point with a tooltip is displayed
		// by Gravity Forms.
		if ( is_wp_error( $forms ) ) {
			$this->set_field_error( $field, $forms->get_error_message() );
			return false;
		}

		return true;

	}

	/**
	 * Validate that the API Secret is valid when loading the settings screen, showing a
	 * tick or a cross.
	 *
	 * @since   1.3.7
	 *
	 * @param   string $api_secret    API Secret.
	 * @return  bool                  API Secret valid
	 */
	public function plugin_settings_fields_feedback_callback_api_secret( $api_secret ) {

		// Validation fails if the API Secret is empty.
		if ( empty( $api_secret ) ) {
			return false;
		}

		// Get Account to test that the API Secret is valid.
		$api     = new CKGF_API(
			$this->api_key(),
			$api_secret,
			$this->debug_enabled()
		);
		$account = $api->account();

		if ( is_wp_error( $account ) ) {
			return false;
		}

		return true;

	}

	/**
	 * Validate that the API Secret is valid when saving settings, showing a tooltip
	 * with a contextual message containing the error if the API Secret is invalid.
	 *
	 * @since   1.3.7
	 *
	 * @param   array  $field       Settings Field.
	 * @param   string $api_secret  API Secret.
	 * @return  bool                API Secret valid
	 */
	public function plugin_settings_fields_validation_callback_api_secret( $field, $api_secret ) {

		// Get Account to test that the API Key is valid.
		$api   = new CKGF_API(
			$this->api_key(),
			$api_secret,
			$this->debug_enabled()
		);
		$forms = $api->account();

		// If an error occured, set the field's error so that an exclamation point with a tooltip is displayed
		// by Gravity Forms.
		if ( is_wp_error( $forms ) ) {
			$this->set_field_error( $field, $forms->get_error_message() );
			return false;
		}

		return true;

	}

	/**
	 * Define the table columns to display when editing ConvertKit Feeds at Gravity Form > Settings > ConvertKit.
	 *
	 * @since   1.0.0
	 *
	 * @return  array
	 */
	public function feed_list_columns() {

		return array(
			'feed_name' => __( 'Name', 'convertkit' ),
			'form_id'   => __( 'Form', 'convertkit' ),
		);

	}

	/**
	 * Define the table row output for the Form column when editing ConvertKit Feeds at Gravity Form > Settings > ConvertKit.
	 *
	 * @since   1.0.0
	 *
	 * @param   array $feed   ConvertKit Feed.
	 * @return  string
	 */
	public function get_column_value_form_id( $feed ) {

		// Get ConvertKit Form ID.
		$form_id = rgars( $feed, 'meta/form_id' );

		// Get Forms to test that the API Key is valid.
		$api   = new CKGF_API(
			$this->api_key(),
			$this->api_secret(),
			$this->debug_enabled()
		);
		$forms = $api->get_forms();

		// If an error occured, bail.
		if ( is_wp_error( $forms ) ) {
			return __( 'N/A', 'convertkit' );
		}

		// Return Form Name, linked to ConvertKit.
		return sprintf(
			'<a href="%1$s" target="_blank">%2$s</a>',
			esc_attr(
				esc_url(
					sprintf(
						'https://app.convertkit.com/forms/designers/%s/edit',
						$form_id
					)
				)
			),
			esc_html( $forms[ $form_id ]['name'] )
		);

	}

	/**
	 * Define the ConvertKit Feed settings when editing a Gravity Form > Settings > ConvertKit > Add/Edit Feed.
	 *
	 * @since   1.0.0
	 *
	 * @return  array
	 */
	public function feed_settings_fields() {

		// Define Feed Settings and Name Field.
		$base_fields = array(
			'title'       => __( 'ConvertKit Feed Settings', 'convertkit' ),
			'description' => '',
			'fields'      => array(
				array(
					'name'     => 'feed_name',
					'label'    => __( 'Name', 'convertkit' ),
					'type'     => 'text',
					'class'    => 'medium',
					'required' => true,
					'tooltip'  => sprintf( '<h6>%s</h6>%s', __( 'Name', 'convertkit' ), __( 'Enter a feed name to uniquely identify this setup.', 'convertkit' ) ),
				),
			),
		);

		// Add Form selection.
		$form_fields = $this->get_forms();
		if ( ! is_wp_error( $form_fields ) ) {
			$base_fields['fields'][] = array(
				'name'     => 'form_id',
				'label'    => __( 'ConvertKit Form', 'convertkit' ),
				'type'     => 'select',
				'required' => true,
				'choices'  => $form_fields,
				'tooltip'  => sprintf( '<h6>%s</h6>%s', __( 'ConvertKit Form', 'convertkit' ), __( 'Select the ConvertKit form that you would like to add your contacts to.', 'convertkit' ) ),
			);
		}

		// Add Tag selection.
		$tag_fields = $this->get_tags();
		if ( ! is_wp_error( $tag_fields ) ) {
			$base_fields['fields'][] = array(
				'name'     => 'tag_id',
				'label'    => __( 'ConvertKit Tag', 'convertkit' ),
				'type'     => 'select',
				'required' => false,
				'choices'  => $tag_fields,
				'tooltip'  => sprintf( '<h6>%s</h6>%s', __( 'ConvertKit Tag', 'convertkit' ), __( 'Select the ConvertKit tag that you would like to assign your contacts to.', 'convertkit' ) ),
			);
		}

		// Add Field Mapping.
		$base_fields['fields'][] = array(
			'name'      => 'field_map',
			'label'     => __( 'Map Fields', 'convertkit' ),
			'type'      => 'field_map',
			'field_map' => array(
				array(
					'name'       => 'e',
					'label'      => __( 'Email', 'convertkit' ),
					'required'   => true,
					'field_type' => '',
				),
				array(
					'name'       => 'n',
					'label'      => __( 'Name', 'convertkit' ),
					'required'   => false,
					'field_type' => '',
				),
				array(
					'name'       => 'tag',
					'label'      => __( 'Tag', 'convertkit' ),
					'required'   => false,
					'field_type' => '',
				),
			),
			'tooltip'   => sprintf( '<h6>%s</h6>%s', __( 'Map Fields', 'convertkit' ), __( 'Associate email address and subscriber name with the appropriate Gravity Forms fields.', 'convertkit' ) ),
		);

		// Add Conditional Logic.
		$base_fields['fields'][] = array(
			'name'    => 'conditions',
			'label'   => __( 'Conditional Logic', 'convertkit' ),
			'type'    => 'feed_condition',
			'tooltip' => sprintf( '<h6>%s</h6>%s', __( 'Conditional Logic', 'convertkit' ), __( 'When conditional logic is enabled, form submissions will only be exported to ConvertKit when the conditions are met. When disabled all form submissions will be exported.', 'convertkit' ) ),
		);

		// Add Custom Fields.
		$base_fields = $this->feed_settings_custom_fields( $base_fields );

		// Return.
		return array( $base_fields );

	}

	/**
	 * Defines the image to use for notes added to entries by this addon.
	 *
	 * @since   1.2.3
	 *
	 * @return  string
	 */
	public function note_avatar() {

		return CKGF_PLUGIN_URL . 'resources/backend/images/block-icon-form.png';

	}

	/**
	 * Adds Custom Fields to the Feed Settings Fields, if Custom Fields exist in the ConvertKit account.
	 *
	 * @since   1.0.0
	 *
	 * @param   array $base_fields  Feed Settings Fields.
	 * @return  array               Feed Settings Fields
	 */
	public function feed_settings_custom_fields( $base_fields ) {

		// Get Custom Fields from ConvertKit API.
		$custom_fields = $this->get_custom_fields();

		// If an error occured, just return the base fields.
		if ( is_wp_error( $custom_fields ) ) {
			return $base_fields;
		}

		// Inject Custom Fields after Email + Name, but before Conditions.
		array_splice(
			$base_fields['fields'],
			4,
			0,
			array(
				array(
					'name'           => 'convertkit_custom_fields',
					'label'          => '',
					'type'           => 'dynamic_field_map',
					'field_map'      => $custom_fields,
					'disable_custom' => true,
				),
			)
		);

		// Return.
		return $base_fields;

	}

	/**
	 * Returns an array of Forms registered in ConvertKit, in an array compatible with
	 * Gravity Form's Feed Settings.
	 *
	 * @since   1.2.1
	 *
	 * @return  mixed   WP_Error | array
	 */
	private function get_forms() {

		// Get Custom Fields.
		$api   = new CKGF_API(
			$this->api_key(),
			$this->api_secret(),
			$this->debug_enabled()
		);
		$forms = $api->get_forms();

		// Bail if an error occured.
		if ( is_wp_error( $forms ) ) {
			return $forms;
		}

		// Map to Gravity Forms Feed compatible array.
		$fields = array(
			array(
				'label' => __( 'Select a ConvertKit form', 'convertkit' ),
				'value' => '',
			),
		);
		foreach ( $forms as $form ) {
			$fields[] = array(
				'value' => esc_attr( $form['id'] ),
				'label' => esc_html( $form['name'] ),
			);
		}

		// Sort Forms in ascending order by label.
		$fields = $this->sort_fields( $fields );

		return $fields;

	}

	/**
	 * Returns an array of Custom Fields registered in ConvertKit, in an array compatible with
	 * Gravity Form's Feed Settings.
	 *
	 * @since   1.0.0
	 *
	 * @return  mixed   WP_Error | array
	 */
	private function get_custom_fields() {

		// Get Custom Fields.
		$api           = new CKGF_API(
			$this->api_key(),
			$this->api_secret(),
			$this->debug_enabled()
		);
		$custom_fields = $api->get_custom_fields();

		// Bail if an error occured.
		if ( is_wp_error( $custom_fields ) ) {
			return $custom_fields;
		}

		// Map to Gravity Forms Feed compatible array.
		$fields = array();
		foreach ( $custom_fields as $custom_field ) {
			$fields[] = array(
				'value' => esc_attr( $custom_field['key'] ),
				'label' => esc_html( $custom_field['label'] ),
			);
		}

		// Sort Custom Fields in ascending order by label.
		$fields = $this->sort_fields( $fields );

		return $fields;

	}

	/**
	 * Returns an array of Custom Fields registered in ConvertKit, in an array compatible with
	 * Gravity Form's Feed Settings.
	 *
	 * @since   1.0.0
	 *
	 * @return  mixed   WP_Error | array
	 */
	private function get_tags() {

		// Get Tags.
		$api  = new CKGF_API(
			$this->api_key(),
			$this->api_secret(),
			$this->debug_enabled()
		);
		$tags = $api->get_tags();

		// Bail if an error occured.
		if ( is_wp_error( $tags ) ) {
			return $tags;
		}

		// Map to Gravity Forms Feed compatible array.
		$fields = array(
			array(
				'label' => __( '(No Tag)', 'convertkit' ),
				'value' => '',
			),
		);
		foreach ( $tags as $tag ) {
			$fields[] = array(
				'value' => esc_attr( $tag['id'] ),
				'label' => esc_html( $tag['name'] ),
			);
		}

		// Sort Tags in ascending order by label.
		$fields = $this->sort_fields( $fields );

		return $fields;

	}

	/**
	 * Returns the given array of fields (Forms, Tags or Custom Fields)
	 * in alphabetical ascending order by label.
	 *
	 * @since   1.3.1
	 *
	 * @param   array $resources  Resources.
	 * @return  array               Sorted Resources by label
	 */
	private function sort_fields( $resources ) {

		// Sort resources ascending by the label property.
		uasort(
			$resources,
			function ( $a, $b ) {
				return strcmp( $a['label'], $b['label'] );
			}
		);

		return $resources;

	}

	/**
	 * Sends the given Gravity Forms Entry to ConvertKit, based on the integration and feed
	 * configuration.
	 *
	 * @since   1.0.0
	 *
	 * @param   array $feed   ConvertKit Feed.
	 * @param   array $entry  Gravity Forms Entry / Submission.
	 * @param   array $form   Gravity Forms Form.
	 * @return  array|null    Returns a modified entry object or null.
	 */
	public function process_feed( $feed, $entry, $form ) {

		// Get ConvertKit Feed Settings.
		$form_id                  = rgars( $feed, 'meta/form_id' );
		$tag_id                   = rgars( $feed, 'meta/tag_id' );
		$field_map_e              = rgars( $feed, 'meta/field_map_e' );
		$field_map_n              = rgars( $feed, 'meta/field_map_n' );
		$field_map_tag            = rgars( $feed, 'meta/field_map_tag' );
		$convertkit_custom_fields = $this->get_dynamic_field_map_fields( $feed, 'convertkit_custom_fields' );

		// Get Entry Values.
		$email        = $this->get_field_value( $form, $entry, $field_map_e );
		$name         = $this->get_field_value( $form, $entry, $field_map_n );
		$tag          = $this->get_field_value( $form, $entry, $field_map_tag );
		$fields       = array(); // Populated later in this function.
		$entry_tag_id = false; // Populated later in this function.

		// If no Email Address is specified, bail.
		if ( empty( $email ) ) {
			$this->add_note(
				$entry['id'],
				__( 'Error Subscribing: The field mapped to the email address contains no value.', 'convertkit' ),
				'error'
			);
			return null;
		}

		// If the Email Address isn't actually an email address, bail.
		if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			$this->add_note(
				$entry['id'],
				sprintf(
					/* translators: Field Value */
					__( 'Error Subscribing: The field mapped to the email address contains the invalid email value %s.', 'convertkit' ),
					$email
				),
				'error'
			);
			return null;
		}

		// Initialize API class.
		$this->api = new CKGF_API(
			$this->api_key(),
			$this->api_secret(),
			$this->debug_enabled()
		);

		// Get Custom Fields.
		$fields = $this->process_feed_custom_fields( $form, $entry, $convertkit_custom_fields );

		// If an error occured, log it as a note in the Gravity Forms Entry,
		// and set fields to false so we can still attempt to subscribe the user.
		if ( is_wp_error( $fields ) ) {
			$this->add_note(
				$entry['id'],
				sprintf(
					/* translators: Error message */
					__( 'Error processing Custom Field Mappings: %s', 'convertkit' ),
					$fields->get_error_message()
				),
				'error'
			);

			$fields = false;
		}

		// Get Entry's Tag ID Mapping.
		if ( ! empty( $tag ) ) {
			$entry_tag_id = $this->process_feed_tag( $tag );

			// If an error occured, log it as a note in the Gravity Forms Entry.
			if ( is_wp_error( $entry_tag_id ) ) {
				$this->add_note(
					$entry['id'],
					sprintf(
						/* translators: Error message */
						__( 'Error processing Tag Field Mapping: %s', 'convertkit' ),
						$entry_tag_id->get_error_message()
					),
					'error'
				);
			}
		}

		// Build array of Tag IDs, which might comprise of the Feed's Tag, Entry's Tag Field value, both or neither.
		$tag_ids = $this->build_tag_ids_array( array( $tag_id, $entry_tag_id ) );

		// Call API to subscribe the email address to the given Form.
		$result = $this->api->form_subscribe( $form_id, $email, $name, $fields, $tag_ids );

		// If an error occured, log it as a note in the Gravity Forms Entry.
		if ( is_wp_error( $result ) ) {
			$this->add_note(
				$entry['id'],
				sprintf(
					/* translators: Error message */
					__( 'Error Subscribing: %s', 'convertkit' ),
					$result->get_error_message()
				),
				'error'
			);
			return null;
		}

		// Add success note to Gravity Forms Entry.
		$this->add_note(
			$entry['id'],
			__( 'Subscribed to ConvertKit successfully', 'convertkit' ),
			'success'
		);

		// Request a review for the Plugin, now that the email address was successfully
		// subscribed to ConvertKit.
		// This can safely be called multiple times, as the review request
		// class will ensure once a review request is dismissed by the user,
		// it is never displayed again.
		WP_CKGF()->get_class( 'review_request' )->request_review();

		return null;

	}

	/**
	 * Map Gravity Form Entry values to Custom Fields.
	 *
	 * @since   1.2.1
	 *
	 * @param   array $form                         Gravity Forms Form.
	 * @param   array $entry                        Gravity Forms Entry / Submission.
	 * @param   array $convertkit_custom_fields     Gravity Forms Custom Fields.
	 * @return  mixed                               WP_Error | array
	 */
	private function process_feed_custom_fields( $form, $entry, $convertkit_custom_fields ) {

		// Get Custom Fields from the API.
		$custom_fields = $this->api->get_custom_fields();

		// If Custom Fields could not be fetched, bail.
		if ( is_wp_error( $custom_fields ) ) {
			return $custom_fields;
		}

		$fields = array();
		foreach ( $custom_fields as $custom_field ) {
			// If this Custom Field isn't mapped in the Feed, skip it.
			if ( ! isset( $convertkit_custom_fields[ $custom_field['key'] ] ) ) {
				continue;
			}

			// Get Entry Value for this Custom Field.
			$fields[ $custom_field['key'] ] = $this->get_field_value( $form, $entry, $convertkit_custom_fields[ $custom_field['key'] ] );
		}

		return $fields;

	}

	/**
	 * Returns the Tag ID for the given Tag Name.
	 *
	 * @since   1.2.1
	 *
	 * @param   string $tag_name    Tag Name.
	 * @return  WP_Error|bool|int   Tag ID
	 */
	private function process_feed_tag( $tag_name ) {

		// Get Tags from the API.
		$tags = $this->api->get_tags();

		// If Tags could not be fetched, bail.
		if ( is_wp_error( $tags ) ) {
			return $tags;
		}

		foreach ( $tags as $tag ) {
			// If the tag's name matches the $tag_name, return its ID.
			if ( $tag['name'] === $tag_name ) {
				return $tag['id'];
			}
		}

		// No matching tag was found in ConvertKit.
		return false;

	}

	/**
	 * Iterates through the supplied array of possible Tag IDs, checking that
	 * they are not WP_Error instances, empty or false, returning an array
	 * of Tag IDs or false if no Tag IDs are present.
	 *
	 * @since   1.2.1
	 *
	 * @param   array $possible_tag_ids   Possible Tag IDs.
	 * @return  mixed                       false | array
	 */
	private function build_tag_ids_array( $possible_tag_ids ) {

		$tag_ids = array();

		// Iterate through array of possible Tag IDs, adding to the array if they're
		// valid IDs.
		foreach ( $possible_tag_ids as $possible_tag_id ) {
			// Skip if a WP_Error.
			if ( is_wp_error( $possible_tag_id ) ) {
				continue;
			}

			// Skip if empty or false.
			if ( empty( $possible_tag_id ) || ! $possible_tag_id ) {
				continue;
			}

			// Add to array.
			$tag_ids[] = $possible_tag_id;
		}

		// If Tag IDs array is now empty, set it to boolean false.
		if ( ! count( $tag_ids ) ) {
			return false;
		}

		// Return a zero based index array.
		return array_values( $tag_ids );

	}

	/**
	 * Returns the API Key defined in the integration's settings.
	 *
	 * @since   1.0.0
	 *
	 * @return  string
	 */
	private function api_key() {

		return $this->get_plugin_setting( 'api_key' );

	}

	/**
	 * Returns the API Secret defined in the integration's settings.
	 *
	 * @since   1.3.7
	 *
	 * @return  string
	 */
	private function api_secret() {

		return $this->get_plugin_setting( 'api_secret' );

	}

	/**
	 * Returns whether the API Key has been set in the integration's settings.
	 *
	 * @since   1.3.7
	 *
	 * @return  bool
	 */
	private function has_api_key() {

		return ( ! empty( $this->api_key() ) ? true : false );

	}

	/**
	 * Returns whether the API Secret has been set in the integration's settings.
	 *
	 * @since   1.3.7
	 *
	 * @return  bool
	 */
	private function has_api_secret() {

		return ( ! empty( $this->api_secret() ) ? true : false );

	}

	/**
	 * Returns whether the API Key and Secret have been set in the integration's settings.
	 *
	 * @since   1.3.7
	 *
	 * @return  bool
	 */
	private function has_api_key_and_secret() {

		return $this->has_api_key() && $this->has_api_secret();

	}

	/**
	 * Returns whether debug logging is enabled in the integration's settings.
	 *
	 * @since   1.2.1
	 *
	 * @return  bool
	 */
	private function debug_enabled() {

		return (bool) $this->get_plugin_setting( 'debug' );

	}

	/**
	 * Fetches the Creator Network Recommendations script from the database, falling
	 * back to an API query if the database doesn't have a copy of it stored.
	 *
	 * @since   1.3.7
	 *
	 * @param   bool $force  If enabled, queries the API instead of checking the cached data.
	 *
	 * @return  WP_Error|bool|string
	 */
	private function get_creator_network_recommendations_script( $force = true ) {

		// Get Creator Network Recommendations script URL.
		if ( ! $force ) {
			$script_url = get_option( $this->creator_network_recommendations_script_key );
			if ( $script_url ) {
				return $script_url;
			}
		}

		// No cached script, or we're forcing an API query; fetch from the API.
		$api = new CKGF_API(
			$this->api_key(),
			$this->api_secret(),
			$this->debug_enabled()
		);

		// Sanity check that we're using the ConvertKit WordPress Libraries 1.3.7 or higher.
		// If another ConvertKit Plugin is active and out of date, its libraries might
		// be loaded that don't have this method.
		if ( ! method_exists( $api, 'recommendations_script' ) ) {
			delete_option( $this->creator_network_recommendations_script_key );
			return false;
		}

		// Get script from API.
		$result = $api->recommendations_script();

		// Bail if an error occured.
		if ( is_wp_error( $result ) ) {
			delete_option( $this->creator_network_recommendations_script_key );
			return $result;
		}

		// Bail if not enabled.
		if ( ! $result['enabled'] ) {
			delete_option( $this->creator_network_recommendations_script_key );
			return false;
		}

		// Store script URL, as Creator Network Recommendations are available on this account.
		update_option( $this->creator_network_recommendations_script_key, $result['embed_js'] );

		// Return.
		return $result['embed_js'];

	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @since   1.0.0
	 *
	 * @return  object Class.
	 */
	public static function get_instance() {

		if ( self::$_instance === null ) {
			self::$_instance = new GFConvertKit();
		}

		return self::$_instance;

	}

}
