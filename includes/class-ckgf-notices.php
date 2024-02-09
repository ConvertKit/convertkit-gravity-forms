<?php
/**
 * Notices class.
 *
 * @package CKWC
 * @author ConvertKit
 */

/**
 * Notices class.
 *
 * @package CKWC
 * @author ConvertKit
 */
class CKGF_Notices {

	/**
	 * Constructor.
	 *
	 * @since   1.4.2
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'maybe_deactivate_plugin' ) );

	}

	/**
	 * Deactivates this Plugin if the official Gravity Forms ConvertKit Add-On is active.
	 *
	 * @since   1.4.2
	 */
	public function maybe_deactivate_plugin() {

		// If the official Gravity Forms ConvertKit Add-On is active, deactivate
		// our Plugin.
		if ( is_plugin_active( 'gravityformsconvertkit/convertkit.php' ) ) {
			deactivate_plugins( 'convertkit-gravity-forms/convertkit.php' );
			return;
		}

		// The official Gravity Forms ConvertKit Add-On is not installed.
		// Recommend the user install it by showing a notice.
		add_action( 'admin_notices', array( $this, 'output_notice' ) );

	}

	/**
	 * Output a persistent notice in the WordPress Administration
	 * telling users to migrate to the official Add-on.
	 *
	 * @since   1.4.2
	 */
	public function output_notice() {

		?>
		<div class="notice notice-warning">
			<p>
				<?php
				printf(
					'%s <a href="%s" target="_blank">%s</a>. %s',
					esc_html__( 'ConvertKit Gravity Forms Add-On: Please install the official', 'convertkit' ),
					esc_url( 'https://www.gravityforms.com/blog/convertkit-add-on/' ),
					esc_html__( 'Gravity Forms ConvertKit Add-On', 'convertkit' ),
					esc_html__( 'Your existing settings will automatically migrate once installed and active.', 'convertkit' )
				);
				?>
			</p>
		</div>
		<?php

	}

}

// Initialize class.
add_action(
	'convertkit_gravity_forms_initialize_admin',
	function () {

		new CKGF_Notices();

	}
);
