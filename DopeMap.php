<?php
/**
 * Plugin Name: DopeMap for Elementor
 * Description: Interactive world map widget for Elementor with country markers and custom popups.
 * Version: 1.0.1
 * Author: Aminul Islam
 * Text Domain: dope-map
 * Requires Plugins: elementor
 * Requires at least: 6.0
 * Requires PHP: 7.4
 *
 * @package DopeMap
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class DopeMap_Plugin {
	/**
	 * Plugin version.
	 */
	const VERSION = '1.0.1';

	/**
	 * Minimum Elementor version.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '3.0.0';

	/**
	 * Minimum PHP version.
	 */
	const MINIMUM_PHP_VERSION = '7.4';

	/**
	 * Singleton instance.
	 *
	 * @var DopeMap_Plugin|null
	 */
	private static $_instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return DopeMap_Plugin
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ) );
	}

	/**
	 * Bootstrap plugin hooks once compatibility checks pass.
	 *
	 * @return void
	 */
	public function on_plugins_loaded() {
		if ( ! $this->is_compatible() ) {
			return;
		}

		add_action(
			'elementor/elements/categories_registered',
			array( $this, 'register_categories' )
		);

		add_action(
			'elementor/widgets/register',
			array( $this, 'register_widgets' )
		);

		add_action(
			'wp_enqueue_scripts',
			array( $this, 'register_assets' )
		);
	}

	/**
	 * Compatibility checks.
	 *
	 * @return bool
	 */
	private function is_compatible() {
		if ( ! did_action( 'elementor/loaded' ) ) {
			return false;
		}

		if ( ! defined( 'ELEMENTOR_VERSION' ) || ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			return false;
		}

		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Register Elementor category for Dope widgets.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Elements manager.
	 * @return void
	 */
	public function register_categories( $elements_manager ) {
		$categories = method_exists( $elements_manager, 'get_categories' ) ? $elements_manager->get_categories() : array();

		if ( ! is_array( $categories ) || ! isset( $categories['dope-category'] ) ) {
			$elements_manager->add_category(
				'dope-category',
				array(
					'title' => esc_html__( 'Dope Plugins', 'dope-map' ),
					'icon'  => 'fa fa-plug',
				)
			);
		}
	}

	/**
	 * Register frontend assets used by the widget.
	 *
	 * @return void
	 */
	public function register_assets() {
		wp_register_style(
			'dopemap-jvectormap-style',
			plugin_dir_url( __FILE__ ) . 'assets/vendor/jvectormap/jquery-jvectormap.css',
			array(),
			self::VERSION
		);

		wp_register_style(
			'dopemap-style',
			plugin_dir_url( __FILE__ ) . 'assets/css/style.css',
			array( 'dopemap-jvectormap-style' ),
			self::VERSION
		);

		wp_register_script(
			'dopemap-jvectormap-script',
			plugin_dir_url( __FILE__ ) . 'assets/vendor/jvectormap/jquery-jvectormap.min.js',
			array( 'jquery' ),
			self::VERSION,
			true
		);

		wp_register_script(
			'dopemap-jvectormap-world-script',
			plugin_dir_url( __FILE__ ) . 'assets/vendor/jvectormap/jquery-jvectormap-world-mill.js',
			array( 'dopemap-jvectormap-script' ),
			self::VERSION,
			true
		);

		wp_register_script(
			'dopemap-script',
			plugin_dir_url( __FILE__ ) . 'assets/js/scripts.js',
			array( 'dopemap-jvectormap-world-script' ),
			self::VERSION,
			true
		);
	}

	/**
	 * Register Elementor widget.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Widgets manager.
	 * @return void
	 */
	public function register_widgets( $widgets_manager ) {
		$widget_file = __DIR__ . '/includes/widgets/world-map-widget.php';

		if ( file_exists( $widget_file ) ) {
			require_once $widget_file;
		}

		if ( class_exists( 'DopeMap_World_Map_Widget' ) ) {
			$widgets_manager->register( new \DopeMap_World_Map_Widget() );
		}
	}
}

DopeMap_Plugin::instance();
