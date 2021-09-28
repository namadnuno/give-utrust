<?php
/**
 * Process Utrust Payments.
 */

use Nunoalexandre\Utrust\External\UtrustAPI;

// Bailout, if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Process the Razorpay payment.
 *
 * @param $donation_data
 */
function give_utrust_process_payment( $donation_data ) {

	 // Make sure we don't have any left over errors present.
     give_clear_errors();

     // Any errors?
     $errors = give_get_errors();

     // No errors, proceed.
     if ( ! $errors ) {

        $form_id         = intval( $donation_data['post_data']['give-form-id'] );

        // Setup payment details for one-time donations.
        $payment_data = array(
            'price'           => $donation_data['price'],
            'give_form_title' => $donation_data['post_data']['give-form-title'],
            'give_form_id'    => $form_id,
            'give_price_id'   => isset( $donation_data['post_data']['give-price-id'] ) ? $donation_data['post_data']['give-price-id'] : '',
            'date'            => $donation_data['date'],
            'user_email'      => $donation_data['user_email'],
            'purchase_key'    => $donation_data['purchase_key'],
            'currency'        => give_get_currency(),
            'user_info'       => $donation_data['user_info'],
            'status'          => 'pending',
            'gateway'         => $donation_data['gateway'],
        );

        // Record the pending payment
        $payment = give_insert_payment( $payment_data );

        // Verify donation payment.
        if ( ! $payment ) {
            // Record the error.
            give_record_gateway_error(
                esc_html__( 'Payment Error', 'give-utrust' ),
                /* translators: %s: payment data */
                sprintf(
                    esc_html__( 'The payment creation failed before processing the Razorpay gateway request. Payment data: %s', 'give-utrust' ),
                    print_r( $donation_data, true )
                ),
                $payment
            );

            give_set_error( 'give-utrust', __( 'An error occurred while processing your payment. Please try again.', 'give-utrust' ) );

            // Problems? Send back.
            give_send_back_to_checkout();
        }

        $donation_data['id'] = $payment;
        $api = new UtrustAPI();
        $result = $api->create_order($donation_data);
        
        give_insert_payment_note( $payment, sprintf( __( 'Transação criada com sucesso: %s', 'give-utrust' ), $result['data']['id'] ) );
        give_set_payment_transaction_id( $payment, $result['id'] );
        update_post_meta( $payment, 'utrust_donation_response', $result );
        give_update_payment_status( $payment, 'pending' );

        wp_redirect(
            $result['attributes']['redirect_url']
        );

        give_send_to_success_page();
	} else {

		// An error occurred.
		give_record_gateway_error(
			__( 'Utrust Error', 'give-utrust' ),
			__( 'Transaction Failed.', 'give-utrust' ) . '<br><br>' . sprintf( esc_attr__( 'Details: %s', 'give-utrust' ), '<br>' . print_r( $razorpay_response, true ) )
		);

		give_set_error( 'give-utrust', sprintf( __( 'The transaction failed. Details: %s', 'give-utrust' ), $razorpay_response ) );

		// Problems? Send back.
		give_send_back_to_checkout();
	}
}

add_action( 'give_gateway_utrust', 'give_utrust_process_payment' );