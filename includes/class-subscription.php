<?php
if (!defined('ABSPATH')) {
    exit;
}

class Woo_MailCow_Subscription {
    private $api;

    public function __construct() {
        $this->api = new Woo_MailCow_API();
        add_action('woocommerce_order_status_completed', array($this, 'create_mailcow_account'), 10, 1);
        add_filter('woocommerce_email_classes', array($this, 'register_email_class'));
    }

    public function create_mailcow_account($order_id) {
        $order = wc_get_order($order_id);
        $username = strtolower(get_post_meta($order_id, '_billing_username', true));
        $email = $order->get_billing_email(); // Alternative E-Mail-Adresse
        $user = wp_get_current_user();

        // Passwort: Für Neukunden vom WP-User, für Bestandskunden bestehendes Passwort
        $password = ($user->ID && $user->user_email === $email) ? wp_get_current_user()->user_pass : wp_generate_password(12, true);

        // Speicherplatz aus Produktvariation holen (Standard: 3 GB)
        $quota = 3 * 1024; // 3 GB in MB
        foreach ($order->get_items() as $item) {
            $product = $item->get_product();
            if ($product->is_type('variable-subscription')) {
                $variation = wc_get_product($item->get_variation_id());
                $quota_attr = $variation->get_attribute('speicherplatz');
                if ($quota_attr) {
                    $quota = intval($quota_attr) * 1024; // In MB umrechnen
                }
            }
        }

        // MailCow API-Aufruf
        $data = array(
            'local_part' => $username,
            'domain' => 'macbay.de',
            'name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
            'quota' => $quota,
            'password' => $password,
            'password2' => $password,
            'active' => 1,
            'force_pw_update' => 1, // Empfehlung zur Änderung
            'mailbox_reset' => $email // Alternative E-Mail für Passwortwiederherstellung
        );

        $response = $this->api->request('add/mailbox', 'POST', $data);

        if ($response && isset($response[0]['type']) && $response[0]['type'] === 'success') {
            // WP-User anlegen, falls nicht vorhanden
            if (!username_exists($username)) {
                $user_id = wp_create_user($username, $password, $email);
                if (!is_wp_error($user_id)) {
                    wp_update_user(array('ID' => $user_id, 'nickname' => $username));
                }
            }
            update_post_meta($order_id, '_mailcow_account_created', true);
        } else {
            error_log('Fehler beim Erstellen des MailCow-Kontos für Order ' . $order_id . ': ' . print_r($response, true));
            $order->add_order_note('Fehler beim Erstellen des MailCow-Kontos.');
        }
    }

    public function register_email_class($email_classes) {
        require_once plugin_dir_path(__FILE__) . 'class-email-customer-completed-order.php';
        $email_classes['Woo_MailCow_Customer_Completed_Order'] = new Woo_MailCow_Customer_Completed_Order();
        return $email_classes;
    }
}
