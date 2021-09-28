<?php

require_once './vendor/autoload.php';
require_once "../../../wp-load.php";

$sessionDonation = give_get_purchase_session();

if ($sessionDonation) {
    give_update_payment_status( intval($sessionDonation['donation_id']), 'cancelled');
	//give_set_error( 'give-utrust', 'A doação foi cancelada.' );
    give_send_to_success_page();
}

wp_redirect('/');
