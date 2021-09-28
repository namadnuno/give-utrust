<?php

namespace Nunoalexandre\Utrust\External;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * WC_UTRUST_API_Base class.
 *
 * Sets Interfaces to Communicates with Utrust API.
 */

class UtrustAPI
{
    private $callback_url = '';
    private $api_key = '';
    private $webhook_secret = '';

    public function __construct()
    {        

        $this->api_key = give_get_option( 'utrust_api_key');
        $this->webhook_secret = give_get_option( 'utrust_secret');
        $this->environment = give_get_option('utrust_environment');

        $this->callback_url = give_get_option('utrust_callback_url');
        $this->return_url = get_site_url() . '/wp-content/plugins/utrust-give/return.php';
        $this->cancel_url = get_site_url() . '/wp-content/plugins/utrust-give/cancel.php';
    }

    public function create_order($donation)
    {
        $order = [
            'reference' => strval($donation['id']),
            'amount' => [
                'total' => strval($donation['price']),
                'currency' => 'EUR',
                'details' => [
                    'subtotal' => strval($donation['price']),
                    'tax' => "0",
                    'shipping' => "0",
                    'discount' => "0",
                ],
            ],
            'line_items' => [
                [
                    "amount" => $donation['price'],
                    'sku' => 'donation_' . strval($donation['price']),
                    'name' => "doacao",
                    'price' => strval($donation['price']),
                    'currency' => 'EUR',
                    'quantity' => '1',
                ]
            ],
            'return_urls' => [
                'return_url' => $this->return_url,
                'cancel_url' => $this->cancel_url,
                'callback_url' => $this->callback_url,
            ]
        ];

        // Customer info
        $customer_data = [
            'first_name' => $donation['user_info']['first_name'],
            'last_name' => $donation['user_info']['last_name'],
            'email' => $donation['user_info']['email'],
            'country' => 'PT'
        ];

        $response = null;

        // Make the API request
        try {
            $utrustApi = new ApiClient($this->api_key, $this->environment);

            $response = $utrustApi->createOrder($order, $customer_data);

        } catch (\Exception $e) {
            error_log('Utrust: Something went wrong: ' . $e->getMessage());
            var_dump($e);
            die();
            throw new $e;
        }

        return $response;
    }
}
