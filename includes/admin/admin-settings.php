<?php
// Bailout, if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Give_Utrust_Gateway_Settings
 *
 * @since 1.0
 */
class Give_Utrust_Gateway_Settings {
	/**
	 * @since  1.0
	 * @access static
	 * @var Give_RUtrustGateway_Settings $instance
	 */
	static private $instance;

	/**
	 * @since  1.0
	 * @access private
	 * @var string $section_id
	 */
	private $section_id;

	/**
	 * @since  1.0
	 * @access private
	 * @var string $section_label
	 */
	private $section_label;

	/**
	 * Give_RazorpUtrustay_Settings constructor.
	 */
	private function __construct() {
	}

	/**
	 * get class object.
	 *
	 * @since 1.0
	 * @return GivUtrustay_Gateway_Settings
	 */
	static function get_instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Setup hooks.
	 *
	 * @since 1.0
	 */
	public function setup_hooks() {
		$this->section_id    = 'utrust';
		$this->section_label = __( 'Utrust', 'give-utrust' );

		// Add payment gateway to payment gateways list.
		add_filter( 'give_payment_gateways', array( $this, 'add_gateways' ) );

		if ( is_admin() ) {
			add_filter( 'give_get_sections_gateways', array( $this, 'add_section' ) );
			add_filter( 'give_get_settings_gateways', array( $this, 'add_settings' ) );
		}
	}

	/**
	 * Add payment gateways to gateways list.
	 *
	 * @since 1.0
	 *
	 * @param array $gateways array of payment gateways.
	 *
	 * @return array
	 */
	public function add_gateways( $gateways ) {
		$gateways[ $this->section_id ] = array(
			'admin_label'    => $this->section_label,
			'checkout_label' => 'Utrust',
		);

		return $gateways;
	}

	/**
	 * Add setting section.
	 *
	 * @since 1.0
	 *
	 * @param array $sections Array of section.
	 *
	 * @return array
	 */
	public function add_section( $sections ) {
		$sections[ $this->section_id ] = $this->section_label;

		return $sections;
	}

	/**
	 * Add plugin settings.
	 *
	 * @since 1.0
	 *
	 * @param array $settings Array of setting fields.
	 *
	 * @return array
	 */
	public function add_settings( $settings ) {
		$current_section = give_get_current_setting_section();

		if ( $this->section_id == $current_section ) {
			$settings = array(
				array(
					'id'   => 'give_utrust_payments_setting',
					'type' => 'title',
				),
				array(
					'title' => __( 'Api Key', 'give-utrust' ),
					'id'    => 'utrust_api_key',
					'type'  => 'api_key',
					'desc'  => __( 'Required api key provided by utrust.', 'give-utrust' ),
				),
				array(
					'title' => __( 'Webhook Secret', 'give-utrust' ),
					'id'    => 'utrust_secret',
					'type'  => 'api_key',
					'desc'  => __( 'Required api secret provided by utrust.', 'give-utrust' ),
				),
				array(
					'id'   => 'give_utrust_payments_setting',
					'type' => 'sectionend',
				),
			);
		}// End if().

		return $settings;
	}
}

Give_Utrust_Gateway_Settings::get_instance()->setup_hooks();
