<?php
namespace ElementorMultistepFree;
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://teklovers.com
 * @since             1.0.0
 * @package           Multi_Step_Forms_Free
 *
 * @wordpress-plugin
 * Plugin Name:       Multi-step Forms FREE (for Elementor)
 * Plugin URI:        multi-step-forms-free
 * Description:       Multistep form to beautiful the users experience when completing forms.
 * Version:           1.2.4
 * Author:            KryisCodes
 * Author URI:        https://teklovers.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       multi-step-forms-free
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MULTI_STEP_FORMS_FREE_VERSION', '1.2.4' );
define("multistep_plugin_url_free", plugin_dir_url( __FILE__ ));
define("multistep_plugin_url_free_dir", __DIR__);
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-multi-step-forms-free-activator.php
 */
function activate_multi_step_forms_free() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-multi-step-forms-free-activator.php';
	Multi_Step_Forms_Free_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-multi-step-forms-free-deactivator.php
 */
function deactivate_multi_step_forms_free() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-multi-step-forms-free-deactivator.php';
	Multi_Step_Forms_Free_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_multi_step_forms_free' );
register_deactivation_hook( __FILE__, 'deactivate_multi_step_forms_free' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-multi-step-forms-free.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_multi_step_forms_free() {



	$plugin = new Multi_Step_Forms_Free();
	$plugin->run();


	add_action( 'rest_api_init', function ($request) {
		register_rest_route( 'corona_monitor/v1', '/update', array(
			'methods'  => 'GET', //POST
			'callback' => function($request){
					$request->get_body();

					require_once( corona_monitor_plugin_url_dir . '/corona_monitor.php' );

					$html = file_get_html('https://www.youtube.com/feed/trending?gl=GB');
					$return = array("html" => $html);
					echo $return;
					$response = new WP_REST_Response( $return );

					// Add a custom status code
					$response->set_status( 201 );

					// Add a custom header
					$response->header( 'Location', 'http://example.com/' );
					return $response;
				},
		) );
	} );


	add_action( 'elementor/widgets/widgets_registered', [ $plugin, 'register_widgets' ] );
	add_action( 'elementor/editor/before_enqueue_scripts', function() {
		wp_register_script( 'elementor-multistep-editor-js', multistep_plugin_url_free . 'assets/js/editor.js', '1.0.0', true );
    wp_enqueue_script( 'elementor-multistep-editor-js');

	} );



}
run_multi_step_forms_free();
