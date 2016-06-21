<?php

if(!defined('ABSPATH')) { exit; }

GFForms::include_feed_addon_framework();

class GFConvertKit extends GFFeedAddOn {

	#region Required Variables for GFFeedAddOn class

	protected $_full_path                = CKGF_PLUGIN_FILEPATH;
	protected $_min_gravityforms_version = CKGF_MIN_GF_VERSION;
	protected $_path                     = CKGF_PLUGIN_BASENAME;
	protected $_short_title              = CKGF_SHORT_TITLE;
	protected $_slug                     = CKGF_SLUG;
	protected $_title                    = CKGF_TITLE;
	protected $_version                  = CKGF_VERSION;

	#endregion Required Variables for GFFeedAddOn class

	public function init() {
		parent::init();
		$this->add_delayed_payment_support(
			array(
				'option_label' => esc_html__( 'Send to ConvertKit only when payment is received.' )
			)
		);
	}

	#region Singleton

	private static $instance = null;

	public static function get_instance() {
		if(is_null(self::$instance)) {
			self::$instance = new GFConvertKit();
		}

		return self::$instance;
	}

	#endregion Singleton

	#region Settings Fields

	public function plugin_settings_fields() {
		ob_start();
		include('views/section.php');
		$section_description = ob_get_clean();

		return array(
			array(
				'title'       => '',
				'description' => $section_description,
				'fields'      => array(
					array(
						'name'              => 'api_key',
						'label'             => __('ConvertKit API Key'),
						'type'              => 'text',
						'class'             => 'medium',
						'required'          => true,
						'feedback_callback' => array($this, 'validate_api_key'),
					),
				),
			),
		);
	}

	public function validate_api_key($api_key) {
		return !is_wp_error(ckgf_convertkit_api_get_forms($api_key));
	}

	#endregion Settings Fields

	#region Feed Settings Fields

	public function feed_settings_fields() {
		return array(
			array(
				'title'       => __('ConvertKit Feed Settings'),
				'description' => '',
				'fields'      => array(
					array(
						'name'     => 'feed_name',
						'label'    => __('Name'),
						'type'     => 'text',
						'class'    => 'medium',
						'required' => true,
						'tooltip'  => sprintf('<h6>%s</h6>%s', __('Name'), __('Enter a feed name to uniquely identify this setup.')),
					),

					array(
						'name'     => 'form_id',
						'label'    => __('ConvertKit Form'),
						'type'     => 'convertkit_form',
						'required' => true,
						'tooltip'  => sprintf('<h6>%s</h6>%s', __('ConvertKit Form'), __('Select the ConvertKit form that you would like to add your contacts to.')),
					),

					array(
						'name'      => 'field_map',
						'label'     => __('Map Fields'),
						'type'      => 'field_map',
						'field_map' => array(
							array(
								'name'       => 'e',
								'label'      => __('Email'),
								'required'   => true,
								'field_type' => '',
							),

							array(
								'name'       => 'n',
								'label'      => __('Name'),
								'required'   => true,
								'field_type' => '',
							),
						),
						'tooltip'   => sprintf('<h6>%s</h6>%s', __('Map Fields'), __('Associate email address and subscriber name with the appropriate Gravity Forms fields.')),
					),

					array(
						'name'    => 'conditions',
						'label'   => __('Conditional Logic'),
						'type'    => 'feed_condition',
						'tooltip' => sprintf('<h6>%s</h6>%s', __('Conditional Logic'), __('When conditional logic is enabled, form submissions will only be exported to ConvertKit when the conditions are met. When disabled all form submissions will be exported.')),
					),
				),
			),
		);
	}

	public function settings_convertkit_form($field, $echo = true) {
		$forms = ckgf_convertkit_api_get_forms();

		ckgf_debug($forms);

		if(is_wp_error($forms)) {
			$markup = sprintf('%s: %s', __('Error'), $forms->get_error_message());
		} else if(empty($forms)) {
			$markup = sprintf('%s: %s', __('Error'), __('Please configure some forms on ConvertKit'));
		} else {
			$options = array(
				array(
					'label' => __('Select a ConvertKit form'),
					'value' => '',
				),
			);

			foreach($forms as $form) {
				$options[] = array(
					'label' => esc_html($form['name']),
					'value' => esc_attr($form['id']),
				);
			}

			$markup = $this->settings_select(array_merge($field, array(
				'choices' => $options,
				'type'    => 'select',
			)), false);
		}

		if($echo) {
			echo $markup;
		}

		return $markup;
	}

	#endregion Feed Settings Fields

	#region Feed List

	public function feed_list_columns() {
		return array(
			'feed_name' => __('Name'),
			'form_id' => __('Form'),
		);
	}

	public function get_column_value_form_id($feed) {
		$forms   = ckgf_convertkit_api_get_forms();
		$form_id = rgars($feed, 'meta/form_id');

		if(is_array($forms) && isset($forms[$form_id])) {
			$form = $forms[$form_id];

			return sprintf('<a href="%s" target="_blank">%s</a>', esc_attr(esc_url($form['url'])), esc_html($forms[$form_id]['name']));
		} else {
			return __('N/A');
		}
	}

	#endregion Feed List

	#region Feed Processing

	public function process_feed($feed, $entry, $form) {
		$field_map_e = rgars($feed, 'meta/field_map_e');
		$field_map_n = rgars($feed, 'meta/field_map_n');
		$form_id     = rgars($feed, 'meta/form_id');

		ckgf_convertkit_api_add_email($form_id, $entry[$field_map_e], $entry[$field_map_n]);
	}

	#endregion Feed Processing

	#region Data

	public function get_api_key() {
		return $this->get_plugin_setting('api_key');
	}

	#endregion Data
}
