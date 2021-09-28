<?php

function log_m($message)
{
    file_put_contents("test.txt", $message . PHP_EOL, FILE_APPEND);
}

function mapStatus($status)
{
    if ($status === 'detected') {
        return 'publish';
    }

    return $status;
}


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once 'vendor/autoload.php';
require_once "../../../wp-load.php";

use Nunoalexandre\Utrust\External\Event;

$webhooksSecret = give_get_option('utrust_secret');


// The payload should come from something like:
$payload = file_get_contents( 'php://input' );
// But for demo purposes, we will hardcode an example payload that Utrust can send
//$payload = "{ \"event_type\": \"ORDER.PAYMENT.CANCELLED\", \"resource\": { \"amount\": \"0.99\", \"currency\": \"EUR\", \"reference\": \"214\" }, \"signature\": \"469ad54f2574d3f6124f91ba2711d90050029df88b58a0bd7ac17a31c05d230f\", \"state\": \"cancelled\" }";

try {
    log_m("Received: " . json_encode($payload));
    $event = new Event($payload);
    $event->validateSignature($webhooksSecret);
    log_m("Valid!");
    // update status
    $donation = json_decode($payload, true);
    give_update_payment_status( intval($donation['resource']['reference']), mapStatus($donation['state'] ));
    log_m("Donation: " . json_encode(give_get_payment_meta(intval($donation['resource']['reference']))));
    log_m("Update Status (" . $donation['resource']['reference'] . "): " . mapStatus($donation['state']));
    // Here you can change your Order status
    http_response_code(200); // Don't delete this
} catch (\Exception $exception) {
    http_response_code(500); // Don't delete this

    // Handle webhook error
    echo 'Error: ' . $exception->getMessage();
}