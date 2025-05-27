<?php

namespace Elgg\PayPalMarketplace;

class PayPalClient {
    private $client_id;
    private $client_secret;
    private $is_sandbox;
    private $access_token;
    
    public function __construct() {
        $this->client_id = elgg_get_plugin_setting('paypal_client_id', 'paypal_marketplace');
        $this->client_secret = elgg_get_plugin_setting('paypal_client_secret', 'paypal_marketplace');
        $this->is_sandbox = elgg_get_plugin_setting('paypal_sandbox', 'paypal_marketplace') === 'yes';
    }
    
    /**
     * Get PayPal API base URL based on environment
     */
    private function getBaseUrl() {
        return $this->is_sandbox 
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }
    
    /**
     * Get access token for PayPal API
     */
    private function getAccessToken() {
        if ($this->access_token) {
            return $this->access_token;
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getBaseUrl() . '/v1/oauth2/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
        curl_setopt($ch, CURLOPT_USERPWD, $this->client_id . ":" . $this->client_secret);
        
        $headers = array();
        $headers[] = 'Accept: application/json';
        $headers[] = 'Accept-Language: en_US';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception('PayPal API Error: ' . curl_error($ch));
        }
        curl_close($ch);
        
        $response = json_decode($result, true);
        $this->access_token = $response['access_token'];
        
        return $this->access_token;
    }
    
    /**
     * Create a PayPal order
     */
    public function createOrder($amount, $currency = 'USD', $description = '') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getBaseUrl() . '/v2/checkout/orders');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        
        $data = [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'amount' => [
                    'currency_code' => $currency,
                    'value' => $amount
                ],
                'description' => $description
            ]]
        ];
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer ' . $this->getAccessToken();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception('PayPal API Error: ' . curl_error($ch));
        }
        curl_close($ch);
        
        return json_decode($result, true);
    }
    
    /**
     * Capture a PayPal order
     */
    public function captureOrder($order_id) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getBaseUrl() . '/v2/checkout/orders/' . $order_id . '/capture');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer ' . $this->getAccessToken();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception('PayPal API Error: ' . curl_error($ch));
        }
        curl_close($ch);
        
        return json_decode($result, true);
    }
    
    /**
     * Create a PayPal payout
     */
    public function createPayout($receiver_email, $amount, $currency = 'USD', $note = '') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getBaseUrl() . '/v1/payments/payouts');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        
        $data = [
            'sender_batch_header' => [
                'sender_batch_id' => uniqid(),
                'email_subject' => 'You have a payout!'
            ],
            'items' => [[
                'recipient_type' => 'EMAIL',
                'amount' => [
                    'value' => $amount,
                    'currency' => $currency
                ],
                'note' => $note,
                'receiver' => $receiver_email
            ]]
        ];
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: Bearer ' . $this->getAccessToken();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception('PayPal API Error: ' . curl_error($ch));
        }
        curl_close($ch);
        
        return json_decode($result, true);
    }
} 