<?php
if (!defined('ABSPATH')) {
    exit;
}
do_action('woocommerce_email_header', $email_heading, $email);
?>

<p><?php printf('Hallo %s,', esc_html($order->get_billing_first_name())); ?></p>
<p>Ihr Mailkonto wurde erfolgreich erstellt. Hier sind Ihre Zugangsdaten:</p>

<ul>
    <li><strong>E-Mail-Adresse:</strong> <?php echo esc_html($email->placeholders['{username}'] . '@macbay.de'); ?></li>
    <li><strong>Passwort:</strong> <?php echo esc_html($email->placeholders['{password}']); ?></li>
    <li><strong>Webmail:</strong> <a href="<?php echo esc_url($email->placeholders['{webmail_url}']); ?>"><?php echo esc_url($email->placeholders['{webmail_url}']); ?></a></li>
</ul>

<p><strong>IMAP/SMTP-Einstellungen:</strong></p>
<ul>
    <li>Server: mail.macbay.eu</li>
    <li>IMAP-Port: 993 (SSL/TLS)</li>
    <li>SMTP-Port: 587 (STARTTLS)</li>
</ul>

<p>Wir empfehlen Ihnen, Ihr Passwort bei der ersten Anmeldung zu ändern.</p>
<p>Falls Sie Fragen haben, kontaktieren Sie uns gerne!</p>

<?php
do_action('woocommerce_email_footer', $email);
