<?php
/**
 * Plugin Name: Give - Utrust
 * Description: Process online donations via the Utrust payment gateway.
 * Author: Nuno Alexandre
 * Version: 0.0.1
 * Text Domain: give-utrust
 */

// Bailout, if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Give_Utrust_Gateway
 *
 * @since 1.0
 */
final class Give_Utrust_Gateway {

	/**
	 * @since  0.0.1
	 * @access static
	 * @var Give_Utrust_Gateway $instance
	 */
	static private $instance;

	/**
	 * Notices (array)
	 *
	 * @since 1.2.1
	 *
	 * @var array
	 */
	public $notices = array();

	/**
	 * Singleton pattern.
	 *
	 * Give_Utrust_Gateway constructor.
	 */
	private function __construct() {
	}


	/**
	 * Get instance
	 *
	 * @since  1.0
	 * @access static
	 * @return Give_Utrust_Gateway|static
	 */
	static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->setup();
		}

		return self::$instance;
	}

	/**
	 * Setup Give Mollie.
	 *
	 * @since  1.2.1
	 * @access private
	 */
	private function setup() {
		$this->setup_constants();
		add_action( 'plugins_loaded', array( $this, 'init' ), 10 );
	}

	/**
	 * Init the plugin in give_init so environment variables are set.
	 *
	 * @since 1.2.1
	 */
	public function init() {
		$this->load_files();
		$this->load_textdomain();
	}

	/**
	 * Setup constants.
	 *
	 * @since  1.0
	 * @access public
	 */
	public function setup_constants() {

		// Global Params.
		if ( ! defined( 'GIVE_UTRUST_VERSION' ) ) {
			define( 'GIVE_UTRUST_VERSION', '0.0.1' );
		}

		if ( ! defined( 'GIVE_UTRUST_FILE' ) ) {
			define( 'GIVE_UTRUST_FILE', __FILE__ );
		}

		if ( ! defined( 'GIVE_UTRUST_BASENAME' ) ) {
			define( 'GIVE_UTRUST_BASENAME', plugin_basename( GIVE_UTRUST_FILE ) );
		}

		if ( ! defined( 'GIVE_UTRUST_URL' ) ) {
			define( 'GIVE_UTRUST_URL', plugins_url( '/', GIVE_UTRUST_FILE ) );
		}

		if ( ! defined( 'GIVE_UTRUST_DIR' ) ) {
			define( 'GIVE_UTRUST_DIR', plugin_dir_path( GIVE_UTRUST_FILE ) );
		}
	}

	/**
	 * Load files.
	 *
	 * @since  1.0
	 * @access public
	 * @return Give_Utrust_Gateway
	 */
	public function load_files() {

		// Load Razorpay SDK for PHP.
		require_once GIVE_UTRUST_DIR . 'vendor/autoload.php';

		// Load helper functions.
		//require_once GIVE_UTRUST_DIR . 'includes/functions.php';

		// Load plugin settings.
		require_once GIVE_UTRUST_DIR . 'includes/admin/admin-settings.php';

		// Load frontend actions.
		require_once GIVE_UTRUST_DIR . 'includes/actions.php';

		// Process payment
		require_once GIVE_UTRUST_DIR . 'includes/process-payment.php';
		//require_once GIVE_UTRUST_DIR . 'includes/class-utrust-customers.php';
		//require_once GIVE_UTRUST_DIR . 'includes/class-utrust-webhooks.php';

		return self::$instance;
	}

	/**
	 * Load the text domain.
	 *
	 * @access private
	 * @since  1.0
	 *
	 * @return void
	 */
	public function load_textdomain() {

		// Set filter for plugin's languages directory.
		$give_utrust_lang_dir = dirname( plugin_basename( GIVE_UTRUST_FILE ) ) . '/languages/';
		$give_utrust_lang_dir = apply_filters( 'give_utrust_languages_directory', $give_utrust_lang_dir );

		// Traditional WordPress plugin locale filter
		$locale = apply_filters( 'plugin_locale', get_locale(), 'give-utrust' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'give-utrust', $locale );

		// Setup paths to current locale file
		$mofile_local  = $give_utrust_lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/give-utrust/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/give-utrust folder
			load_textdomain( 'give-utrust', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/give-utrust/languages/ folder
			load_textdomain( 'give-utrust', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'give-utrust', false, $give_utrust_lang_dir );
		}

	}
}

if ( ! function_exists( 'Give_Utrust_Gateway' ) ) {
	function Give_Utrust_Gateway() {
		return Give_Utrust_Gateway::get_instance();
	}

	Give_Utrust_Gateway();
}
