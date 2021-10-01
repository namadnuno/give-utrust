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
	 * Give_Utrust_Settings constructor.
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
					'title' => __( 'Title', 'give-utrust' ),
					'id'    => 'utrust_title',
					'type'  => 'text',
					'desc'  => __('This controls the title for the payment method the customer sees during checkout.', 'give-utrust'),
                    'default' => __('Utrust', 'give-utrust')
				),
                array(
					'title' => __( 'Title', 'give-utrust' ),
					'id'    => 'utrust_description',
					'type'  => 'text',
					'desc'  => __('Payment method instructions that the customer will see on your checkout.', 'give-utrust'),
                    'default' => __('You will be redirected to the Utrust payment widget compatible with any major crypto wallets. It will allow you to pay for your purchase in a safe and seamless way using Bitcoin, Ethereum, Tether or a number of other currencies.', 'give-utrust')
				),
                array(
					'title' => __( 'Environment', 'give-utrust' ),
					'id'    => 'utrust_environment',
					'type'  => 'radio_inline',
					'desc'  => __('Environment where the payment will run', 'give-utrust'),
                    'default' => 'test',
                    'options' => [
                        'test' => 'Sandbox',
                        'production' => 'Production'
                    ]
				),
				array(
					'title' => __( 'Callback URL', 'give-utrust' ),
					'id'    => 'utrust_callback_url',
					'type'  => 'text',
					'desc'  => __( 'Callback url that will be requested once an update is made on the order. (change to a published domain, do not use localhost)', 'give-utrust' ),
					'default' => get_site_url() . '/wp-content/plugins/utrust-give/callback.php'
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
