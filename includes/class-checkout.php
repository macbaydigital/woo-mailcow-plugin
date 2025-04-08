<?php
if (!defined('ABSPATH')) {
    exit;
}

class Woo_MailCow_Checkout {
    private $api;

    public function __construct() {
        $this->api = new Woo_MailCow_API();
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_check_username', array($this, 'check_username_callback'));
        add_action('wp_ajax_nopriv_check_username', array($this, 'check_username_callback'));
        add_filter('woocommerce_checkout_fields', array($this, 'add_username_field'));
    }

    public function enqueue_scripts() {
        if (is_checkout()) {
            wp_enqueue_script('woo-mailcow-checkout', plugin_dir_url(__DIR__) . 'js/checkout.js', array('jquery'), '1.0.0', true);
            wp_localize_script('woo-mailcow-checkout', 'woo_mailcow_vars', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('check_username')
            ));
        }
    }

    public function add_username_field($fields) {
        $fields['billing']['billing_username'] = array(
            'label' => 'Gewünschter E-Mail-Username',
            'required' => true,
            'class' => array('form-row-wide'),
            'priority' => 25,
        );
        return $fields;
    }

    public function check_username_callback() {
        check_ajax_referer('check_username', 'nonce');

        $username = sanitize_text_field($_POST['username']);
        $username = strtolower($username); // Großbuchstaben konvertieren

        // Validierung: Kleinbuchstaben, Zahlen, max. ein Punkt
        if (!preg_match('/^[a-z0-9]+(\.[a-z0-9]+)?$/', $username)) {
            wp_send_json_error('Ungültiger Username. Nur Kleinbuchstaben, Zahlen und maximal ein Punkt erlaubt.');
            wp_die();
        }

        // Verfügbarkeitsprüfung
        $mailboxes = $this->api->request('get/mailbox/all');
        if ($mailboxes && in_array($username . '@macbay.de', array_column($mailboxes, 'username'))) {
            wp_send_json_error('Username ist bereits vergeben.');
        } else {
            wp_send_json_success('Username ist verfügbar.');
        }
        wp_die();
    }
}
