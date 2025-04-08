<?php
/*
 * Plugin Name: Woo MailCow Plugin
 * Description: Integriert WooCommerce Subscriptions mit MailCow zur Verwaltung von Mailkonten.
 * Version: 1.0.1
 * Author: Macbay Digital
 * GitHub Plugin URI: https://github.com/macbaydigital/woo-mailcow-plugin/
 */

if (!defined('ABSPATH')) {
    exit; // Verhindert direkten Zugriff
}

class Woo_MailCow_Plugin {
    public function __construct() {
        add_action('plugins_loaded', array($this, 'init'));
    }

    public function init() {
        if (class_exists('WooCommerce') && class_exists('WC_Subscriptions')) {
            $this->load_dependencies();
            $this->register_components();
        } else {
            add_action('admin_notices', array($this, 'missing_dependencies_notice'));
        }
    }

    private function load_dependencies() {
        require_once plugin_dir_path(__FILE__) . 'includes/class-api.php';
        require_once plugin_dir_path(__FILE__) . 'includes/class-settings.php';
        require_once plugin_dir_path(__FILE__) . 'includes/class-checkout.php';
        require_once plugin_dir_path(__FILE__) . 'includes/class-subscription.php';
    }

    private function register_components() {
        $api = new Woo_MailCow_API();
        $settings = new Woo_MailCow_Settings();
        $checkout = new Woo_MailCow_Checkout();
        $subscription = new Woo_MailCow_Subscription();
    }

    public function missing_dependencies_notice() {
        echo '<div class="error"><p>Woo MailCow Plugin benötigt WooCommerce und WooCommerce Subscriptions, um zu funktionieren.</p></div>';
    }
}

new Woo_MailCow_Plugin();
