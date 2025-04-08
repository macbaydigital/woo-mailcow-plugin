<?php
if (!defined('ABSPATH')) {
    exit;
}

class Woo_MailCow_API {
    public function request($endpoint, $method = 'GET', $data = array()) {
        $api_key = get_option('woo_mailcow_api_key');
        $api_url = get_option('woo_mailcow_api_url', 'https://mail.macbay.eu/api/v1/');

        $args = array(
            'method' => $method,
            'headers' => array(
                'X-API-Key' => $api_key,
                'Content-Type' => 'application/json'
            ),
            'timeout' => 10,
        );

        if (!empty($data)) {
            $args['body'] = json_encode($data);
        }

        $response = wp_remote_request($api_url . $endpoint, $args);

        if (is_wp_error($response)) {
            error_log('MailCow API Fehler: ' . $response->get_error_message());
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        return json_decode($body, true);
    }
}
