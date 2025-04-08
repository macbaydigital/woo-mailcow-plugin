<?php
if (!defined('ABSPATH')) {
    exit;
}

class Woo_MailCow_Customer_Completed_Order extends WC_Email {
    public function __construct() {
        $this->id = 'woo_mailcow_customer_completed_order';
        $this->customer_email = true;
        $this->title = 'MailCow Konto erstellt';
        $this->description = 'E-Mail mit Zugangsdaten nach abgeschlossener Bestellung.';
        $this->template_html = 'emails/customer-completed-order.php';
        $this->template_plain = 'emails/plain/customer-completed-order.php';
        $this->placeholders = array(
            '{username}' => '',
            '{password}' => '',
            '{webmail_url}' => 'https://mail.macbay.eu/SOGo/'
        );

        // Trigger bei abgeschlossener Bestellung
        add_action('woocommerce_order_status_completed_notification', array($this, 'trigger'), 10, 2);

        parent::__construct();
    }

    public function trigger($order_id, $order = false) {
        if ($order_id && !is_a($order, 'WC_Order')) {
            $order = wc_get_order($order_id);
        }

        if (is_a($order, 'WC_Order') && get_post_meta($order_id, '_mailcow_account_created', true)) {
            $this->object = $order;
            $this->recipient = $order->get_billing_email();

            $this->placeholders['{username}'] = strtolower(get_post_meta($order_id, '_billing_username', true));
            $user = get_user_by('email', $this->recipient);
            $this->placeholders['{password}'] = $user && $user->user_login === $this->placeholders['{username}'] ? 'Ihr bestehendes Passwort' : wp_generate_password(12, true);

            if (!$this->is_enabled() || !$this->get_recipient()) {
                return;
            }

            $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
        }
    }

    public function get_default_subject() {
        return 'Ihr Macbay Mailkonto ist bereit!';
    }

    public function get_default_heading() {
        return 'Willkommen bei Macbay Mail';
    }
}
