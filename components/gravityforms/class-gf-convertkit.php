<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

GFForms::include_feed_addon_framework();

/**
 * Class GFConvertKit
 */
class GFConvertKit extends GFFeedAddOn {

	protected $_full_path                = CKGF_PLUGIN_FILEPATH;
	protected $_min_gravityforms_version = CKGF_MIN_GF_VERSION;
	protected $_path                     = CKGF_PLUGIN_BASENAME;
	protected $_short_title              = CKGF_SHORT_TITLE;
	protected $_slug                     = CKGF_SLUG;
	protected $_title                    = CKGF_TITLE;
	protected $_version                  = CKGF_VERSION;

	/** @var null  */
	private static $instance = null;

	/**
	 * Initialize this class
	 */
	public function init() {
		parent::init();
		$this->add_delayed_payment_support(
			array(
				'option_label' => esc_html__( 'Send to ConvertKit only when payment is received.', 'convertkit' ),
			)
		);
	}

	/**
	 * Get singleton
	 *
	 * @return GFConvertKit|null
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new GFConvertKit();
		}

		return self::$instance;
	}

	/**
	 * Output the ConvertKit settings in the Gravity Forms General Settings page
	 *
	 * @return array
	 */
	public function plugin_settings_fields() {
		ob_start();
		include( 'views/section.php' );
		$section_description = ob_get_clean();

		return array(
			array(
				'title'       => '',
				'description' => $section_description,
				'fields'      => array(
					array(
						'name'              => 'api_key',
						'label'             => __( 'ConvertKit API Key' ),
						'type'              => 'text',
						'class'             => 'medium',
						'required'          => true,
						'feedback_callback' => array( $this, 'validate_api_key' ),
					),
				),
			),
		);
	}

	/**
	 * Check API key by using it in an API call
	 *
	 * @param $api_key
	 *
	 * @return bool
	 */
	public function validate_api_key( $api_key ) {
		return ! is_wp_error( ckgf_convertkit_api_get_forms( $api_key ) );
	}

	/**
	 * Add settings to the form specific Settings tab
	 *
	 * @return array
	 */
	public function feed_settings_fields() {
		$base_fields = array(
			array(
				'title'       => __( 'ConvertKit Feed Settings' ),
				'fields'      => array(
					array(
						'name'     => 'feed_name',
						'label'    => __( 'Name' ),
						'type'     => 'text',
						'class'    => 'medium',
						'required' => true,
						'tooltip'  => sprintf( '<h6>%s</h6>%s', __( 'Name' ), __( 'Enter a feed name to uniquely identify this setup.', 'convertkit' ) ),
					),
					array(
						'name'     => 'form_id',
						'label'    => __( 'ConvertKit Form' ),
						'type'     => 'convertkit_form',
						'required' => true,
						'tooltip'  => sprintf( '<h6>%s</h6>%s', __( 'ConvertKit Form', 'convertkit' ), __( 'Select the ConvertKit form that you would like to add your contacts to.', 'convertkit' ) ),
					),
				),
			),
			array(
				'dependency' => 'form_id',
				'fields' => array(

					array(
						'name'      => 'field_map',
						'label'     => __( 'Map Fields' ),
						'type'      => 'field_map',
						'field_map' => array(
							array(
								'name'       => 'e',
								'label'      => __( 'Email' ),
								'required'   => true,
								'field_type' => '',
							),

							array(
								'name'       => 'n',
								'label'      => __( 'Name' ),
								'required'   => true,
								'field_type' => '',
							),
						),
						'tooltip'   => sprintf( '<h6>%s</h6>%s', __( 'Map Fields', 'convertkit' ), __( 'Associate email address and subscriber name with the appropriate Gravity Forms fields.', 'convertkit' ) ),
					),
					array(
						'name' => 'convertkit_custom_fields',
						'label' => '',
						'type' => 'dynamic_field_map',
						'field_map' => $this->get_custom_fields(),
						'disable_custom' => true,
					),
					array(
						'name'       => 'tags',
						'label'      => esc_html__( 'Tags', 'convertkit' ),
						'dependency' => array( $this, 'has_tags' ),
						'type'       => 'convertkit_tags',
						'tooltip'    => sprintf(
							'<h6>%s</h6>%s',
							esc_html__( 'Tags', 'convertkit' ),
							esc_html__( 'When one or more tags are enabled, users will have tags added.. When disabled, users will not be tagged.', 'convertkit' )
						),
					),
					array(
						'name'    => 'conditions',
						'label'   => __( 'Conditional Logic' ),
						'type'    => 'feed_condition',
						'tooltip' => sprintf( '<h6>%s</h6>%s', __( 'Conditional Logic', 'convertkit' ), __( 'When conditional logic is enabled, form submissions will only be exported to ConvertKit when the conditions are met. When disabled all form submissions will be exported.', 'convertkit' ) ),
					),
				),
			),
		);

		return $base_fields;
	}

	/**
	 * Get ConvertKit Custom Fields from the API to be used for the dynamic field map
	 *
	 * @return array
	 */
	public function get_custom_fields() {

		$path = 'custom_fields';
		$query_args = array();
		$request_body = null;
		$request_args = array();
		$fields = array();

		$response = ckgf_convertkit_api_request( $path, $query_args, $request_body, $request_args );
		if ( ! is_wp_error( $response ) ) {


			$custom_fields = $response['custom_fields'];

			if ( $custom_fields && ! is_wp_error( $custom_fields ) ) {

				$fields[] = array(
					'label' => __( 'Choose a ConvertKit Field', 'convertkit' ),
				);

				foreach ( $custom_fields as $field ) {

					$fields[] = array(
						'value' => $field['key'],
						'label' => $field['label'],
					);
				}
			}
		} else {
			$fields[] = array(
				'label' => __( 'There was an error connecting to ConvertKit', 'convertkit' ),
			);
		}

		return $fields;
	}

	/**
	 * Checks for tags in the ConvertKit
	 *
	 * @return bool
	 */
	public function has_tags(){

		$path = 'tags';
		$query_args = array();
		$request_body = null;
		$request_args = array();

		$response = ckgf_convertkit_api_request( $path, $query_args, $request_body, $request_args );

		return ! empty( $response );
	}

	/**
	 * GF Settings callback
	 *
	 * Build a SELECT to be shown in Feed Settings to select ConvertKit form
	 * form data will be posted to.
	 *
	 * @param array $field
	 * @param bool|true $echo
	 *
	 * @return string
	 */
	public function settings_convertkit_form( $field, $echo = true ) {
		$forms = ckgf_convertkit_api_get_forms();

		ckgf_debug( $forms );

		if ( is_wp_error( $forms ) ) {
			$markup = sprintf( '%s: %s', __( 'Error', 'convertkit' ), $forms->get_error_message() );
		} elseif ( empty( $forms ) ) {
			$markup = sprintf( '%s: %s', __( 'Error', 'convertkit' ), __( 'Please configure some forms on ConvertKit', 'convertkit' ) );
		} else {
			$options = array(
				array(
					'label' => __( 'Select a ConvertKit form', 'convertkit' ),
					'value' => '',
				),
			);

			foreach ( $forms as $form ) {
				$options[] = array(
					'label' => esc_html( $form['name'] ),
					'value' => esc_attr( $form['id'] ),
				);
			}

			$markup = $this->settings_select(array_merge($field, array(
				'choices'  => $options,
				'type'     => 'select',
				'onchange' => 'jQuery(this).parents("form").submit();',
			)), false);
		}

		if ( $echo ) {
			echo $markup;
		}

		return $markup;
	}

	/**
	 *
	 */
	public function settings_convertkit_tags( $field, $echo = true ) {

		// Get tags
		$path = 'tags';
		$query_args = array();
		$request_body = null;
		$request_args = array();

		$tags = ckgf_convertkit_api_request( $path, $query_args, $request_body, $request_args );

		// If no tags are found, return.
		if ( empty( $tags['tags'] ) ) {
			$this->log_debug( __METHOD__ . '(): No tags found.' );
			return '<p>No tags found</p>';
		}
		// Start field markup.
		$html = "<div id='gaddon-convertkit_tags'>";

		// Open container.
		$html .= '<div class="gaddon-convertkit-category">';

		// Define label.
		$label = '';

		// Display tag label.
		$html .= '<div class="gaddon-convertkit-categoryname">' . esc_html( $label ) . '</div><div class="gf_animate_sub_settings">';

		// Loop through tags.
		foreach ( $tags['tags'] as $tag ) {

			// Define tag key.
			$tag_key = 'convetkittag_' . $tag['id'];

			// Define enabled checkbox key.
			$enabled_key = $tag_key . '_enabled';

			// Get tag checkbox markup.
			$html .= $this->settings_checkbox(
				array(
					'name'    => esc_html( $tag['name'] ),
					'type'    => 'checkbox',
					'onclick' => "if(this.checked){jQuery('#{$tag_key}_condition_container').slideDown();} else{jQuery('#{$tag_key}_condition_container').slideUp();}",
					'choices' => array(
						array(
							'name'  => $enabled_key,
							'label' => esc_html( $tag['name'] ),
						),
					),
				),
				false
			);

			$html .= $this->tag_category_condition( $tag_key );

		}

		$html .= '</div></div>';


		$html .= '</div>';

		if ( $echo ) {
			echo $html;
		}

		return $html;
	}

	/**
	 * Define the markup for the tag conditional logic.
	 *
	 * @since  4.0
	 * @access public
	 *
	 * @param string $setting_name_root The category setting key.
	 *
	 * @return string
	 */
	public function tag_category_condition( $setting_name_root ) {

		$condition_enabled_setting = "{$setting_name_root}_enabled";
		$is_enabled                = $this->get_setting( $condition_enabled_setting ) == '1';
		$container_style           = ! $is_enabled ? "style='display:none;'" : '';

		$str = "<div id='{$setting_name_root}_condition_container' {$container_style} class='condition_container'>" .
		       esc_html__( 'Assign to group:', 'convertkit' ) . ' ';

		$str .= $this->settings_select(
			array(
				'name'     => "{$setting_name_root}_decision",
				'type'     => 'select',
				'choices'  => array(
					array(
						'value' => 'always',
						'label' => esc_html__( 'Always', 'convertkit' )
					),
					array(
						'value' => 'if',
						'label' => esc_html__( 'If', 'convertkit' )
					),
				),
				'onchange' => "if(jQuery(this).val() == 'if'){jQuery('#{$setting_name_root}_decision_container').show();}else{jQuery('#{$setting_name_root}_decision_container').hide();}",
			), false
		);

		$decision = $this->get_setting( "{$setting_name_root}_decision" );
		if ( empty( $decision ) ) {
			$decision = 'always';
		}

		$conditional_style = $decision == 'always' ? "style='display:none;'" : '';

		$str .= '   <span id="' . $setting_name_root . '_decision_container" ' . $conditional_style . '><br />' .
		        $this->simple_condition( $setting_name_root, $is_enabled ) .
		        '   </span>' .

		        '</div>';

		return $str;

	}

	/**
	 * Define which field types can be used for the tag conditional logic.
	 *
	 * @since  3.0
	 * @access public
	 *
	 * @uses GFAddOn::get_current_form()
	 * @uses GFCommon::get_label()
	 * @uses GF_Field::get_entry_inputs()
	 * @uses GF_Field::get_input_type()
	 * @uses GF_Field::is_conditional_logic_supported()
	 *
	 * @return array
	 */
	public function get_conditional_logic_fields() {

		// Initialize conditional logic fields array.
		$fields = array();

		// Get the current form.
		$form = $this->get_current_form();

		// Loop through the form fields.
		foreach ( $form['fields'] as $field ) {

			// If this field does not support conditional logic, skip it.
			if ( ! $field->is_conditional_logic_supported() ) {
				continue;
			}

			// Get field inputs.
			$inputs = $field->get_entry_inputs();

			// If field has multiple inputs, add them as individual field options.
			if ( $inputs && 'checkbox' !== $field->get_input_type() ) {

				// Loop through the inputs.
				foreach ( $inputs as $input ) {

					// If this is a hidden input, skip it.
					if ( rgar( $input, 'isHidden' ) ) {
						continue;
					}

					// Add input to conditional logic fields array.
					$fields[] = array(
						'value' => $input['id'],
						'label' => GFCommon::get_label( $field, $input['id'] ),
					);

				}

			} else {

				// Add field to conditional logic fields array.
				$fields[] = array(
					'value' => $field->id,
					'label' => GFCommon::get_label( $field ),
				);

			}

		}

		return $fields;

	}

	/**
	 * @return array
	 */
	public function feed_list_columns() {
		return array(
			'feed_name' => __( 'Name' ),
			'form_id' => __( 'Form' ),
		);
	}

	/**
	 * @param $feed
	 *
	 * @return string|void
	 */
	public function get_column_value_form_id( $feed ) {
		$forms   = ckgf_convertkit_api_get_forms();
		$form_id = rgars( $feed, 'meta/form_id' );

		if ( is_array( $forms ) && isset( $forms[ $form_id ] ) ) {
			$form = $forms[ $form_id ];

			return sprintf( '<a href="%s" target="_blank">%s</a>', esc_attr( esc_url( $form['url'] ) ), esc_html( $forms[ $form_id ]['name'] ) );
		} else {
			return __( 'N/A' );
		}
	}

	/**
	 * Process the submitted Gravity Form
	 *
	 * Email and Name are required by the API. If custom fields are mapped to form fields
	 * they will be added to the API post.
	 *
	 * @param array $feed
	 * @param array $entry
	 * @param array $form
	 */
	public function process_feed( $feed, $entry, $form ) {

		$field_map_e = rgars( $feed, 'meta/field_map_e' );
		$field_map_n = rgars( $feed, 'meta/field_map_n' );
		$form_id     = rgars( $feed, 'meta/form_id' );

		$convertkit_custom_fields = $this->get_dynamic_field_map_fields( $feed, 'convertkit_custom_fields' );

		$fields = array();

		$custom_fields = $this->get_custom_fields();
		// do we have custom fields in the feed?  add them to fields
		foreach ( $custom_fields as $field ) {
			if ( isset( $field['value'] ) ) {
				if ( isset( $convertkit_custom_fields[ $field['value'] ] ) ) {
					$fields[ $field['value'] ] = $this->get_field_value( $form, $entry, $convertkit_custom_fields[ $field['value'] ] );
				}
			}
		}

		ckgf_convertkit_api_add_email( $form_id, $entry[ $field_map_e ], $entry[ $field_map_n ], null, $fields );

		// add tags

		$categories = $this->get_feed_tags( $feed );
		foreach ( $categories as $category_id => $category_meta ) {

			// Log that we are evaluating the category conditions.
			$this->log_debug( __METHOD__ . '(): Evaluating condition for tag "' . $category_id . '": ' . print_r( $category_meta, true ) );

			// Get condition evaluation.
			$condition_evaluation = $this->is_category_condition_met( $category_meta, $form, $entry );

			// Set tag based on evaluation.
			$tags[ $category_id ] = $condition_evaluation;

		}

		foreach ( $tags as $tag_id => $value ) {
			$path = 'tags/' . $tag_id . '/subscribe';
			$query_args = array();
			$request_body = array (
				'email' => $entry[ $field_map_e ],
			);
			$request_args = array(
				'method' => 'POST',
			);

			// add the tag
			ckgf_convertkit_api_request( $path, $query_args, $request_body, $request_args);

		}

	}

	public function get_feed_tags ( $feed, $enabled = true ) {

		// Initialize tags array.
		$categories = array();

		// Loop through feed meta.
		foreach ( $feed['meta'] as $key => $value ) {

			// If this is not an tag, skip it.
			if ( 0 !== strpos( $key, 'convetkittag_' ) ) {
				continue;
			}

			// Explode the meta key.
			$key = explode( '_', $key );

			// Add value to categories array.
			$categories[ $key[1] ][ $key[2] ] = $value;

		}

		// If we are only returning enabled categories, remove disabled categories.
		if ( $enabled ) {

			// Loop through categories.
			foreach ( $categories as $category_id => $category_meta ) {

				// If category is enabled, skip it.
				if ( '1' == $category_meta['enabled'] ) {
					continue;
				}

				// Remove category.
				unset( $categories[ $category_id ] );

			}

		}

		return $categories;

	}

	public function is_category_condition_met( $category, $form, $entry ) {

		if ( ! $category['enabled'] ) {

			$this->log_debug( __METHOD__ . '(): Tag not enabled. Returning false.' );

			return false;

		} else if ( $category['decision'] == 'always' ) {

			$this->log_debug( __METHOD__ . '(): Tag decision is always. Returning true.' );

			return true;

		}

		$field = GFFormsModel::get_field( $form, $category['field'] );

		if ( ! is_object( $field ) ) {

			$this->log_debug( __METHOD__ . "(): Field #{$category['field']} not found. Returning true." );

			return true;

		} else {

			$field_value    = GFFormsModel::get_lead_field_value( $entry, $field );
			$is_value_match = GFFormsModel::is_value_match( $field_value, $category['value'], $category['operator'] );

			$this->log_debug( __METHOD__ . "(): Add to tag if field #{$category['field']} value {$category['operator']} '{$category['value']}'. Is value match? " . var_export( $is_value_match, 1 ) );

			return $is_value_match;

		}

	}

	/**
	 * @return mixed
	 */
	public function get_api_key() {
		return $this->get_plugin_setting( 'api_key' );
	}

}
