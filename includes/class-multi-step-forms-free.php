<?php
namespace ElementorMultistepFree;
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://teklovers.com
 * @since      1.0.0
 *
 * @package    Multi_Step_Forms_Free
 * @subpackage Multi_Step_Forms_Free/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Multi_Step_Forms_Free
 * @subpackage Multi_Step_Forms_Free/includes
 * @author     Kryis <krystal@tafsite.com>
 */

class Multi_Step_Forms_Free {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Multi_Step_Forms_Free_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'MULTI_STEP_FORMS_FREE_VERSION' ) ) {
			$this->version = MULTI_STEP_FORMS_FREE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'multi-step-forms-free';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();






	}
	public function register_widgets() {
		require_once( multistep_plugin_url_free_dir . '/widgets/multistep_free.php' );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\multistep_free() );
	}
	public function widget_scripts() {
		wp_register_script( 'elementor-multistep_free', multistep_plugin_url_free . 'assets/js/multistep_free.js', ["elementor-frontend-modules", "elementor-pro-frontend"], '1.1.6', true ); #'elementor-frontend', , "ElementorProFrontendConfig" // BEST "elementor-pro-frontend", "elementor-sticky"
    global $wp;
    $is_edit = \Elementor\Plugin::instance()->preview->is_preview_mode();
    $locale_settings = array(
      'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'elementor-pro-frontend' ),
      "is_admin" => $is_edit,
    );
    wp_localize_script( 'elementor-multistep_free', 'php_vars', $locale_settings );
	}
	public function widget_styles() {
    wp_register_style( 'elementor-multistep-css_free', multistep_plugin_url_free . 'assets/css/multistep.css', [], '1.0.0' );
    wp_enqueue_style( 'elementor-multistep-css_free' );
  }
	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Multi_Step_Forms_Free_Loader. Orchestrates the hooks of the plugin.
	 * - Multi_Step_Forms_Free_i18n. Defines internationalization functionality.
	 * - Multi_Step_Forms_Free_Admin. Defines all hooks for the admin area.
	 * - Multi_Step_Forms_Free_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-multi-step-forms-free-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-multi-step-forms-free-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-multi-step-forms-free-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-multi-step-forms-free-public.php';

		$this->loader = new Multi_Step_Forms_Free_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Multi_Step_Forms_Free_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Multi_Step_Forms_Free_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Multi_Step_Forms_Free_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Multi_Step_Forms_Free_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Multi_Step_Forms_Free_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
