<?php
namespace ElementorMultistepFree;
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://teklovers.com
 * @since      1.0.0
 *
 * @package    Multi_Step_Forms_Free
 * @subpackage Multi_Step_Forms_Free/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Multi_Step_Forms_Free
 * @subpackage Multi_Step_Forms_Free/includes
 * @author     Kryis <krystal@tafsite.com>
 */
class Multi_Step_Forms_Free_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'multi-step-forms-free',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
