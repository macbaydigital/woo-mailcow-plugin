<?php
if (!defined('ABSPATH')) {
    exit;
}

class Woo_MailCow_Settings {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_settings_page() {
        add_options_page(
            'Woo MailCow Einstellungen',
            'Woo MailCow',
            'manage_options',
            'woo-mailcow-settings',
            array($this, 'render_settings_page')
        );
    }

    public function register_settings() {
        register_setting('woo_mailcow_settings', 'woo_mailcow_api_key', array(
            'sanitize_callback' => 'sanitize_text_field'
        ));
        register_setting('woo_mailcow_settings', 'woo_mailcow_api_url', array(
            'sanitize_callback' => 'esc_url_raw',
            'default' => 'https://mail.macbay.eu/api/v1/'
        ));

        add_settings_section('woo_mailcow_main', 'API Einstellungen', null, 'woo-mailcow-settings');
        add_settings_field('api_key', 'API-Schlüssel', array($this, 'render_api_key_field'), 'woo-mailcow-settings', 'woo_mailcow_main');
        add_settings_field('api_url', 'API-URL', array($this, 'render_api_url_field'), 'woo-mailcow-settings', 'woo_mailcow_main');
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>Woo MailCow Einstellungen</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('woo_mailcow_settings');
                do_settings_sections('woo-mailcow-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function render_api_key_field() {
        $api_key = get_option('woo_mailcow_api_key', '');
        echo '<input type="text" name="woo_mailcow_api_key" value="' . esc_attr($api_key) . '" size="50">';
    }

    public function render_api_url_field() {
        $api_url = get_option('woo_mailcow_api_url', 'https://mail.macbay.eu/api/v1/');
        echo '<input type="url" name="woo_mailcow_api_url" value="' . esc_attr($api_url) . '" size="50">';
    }
}
