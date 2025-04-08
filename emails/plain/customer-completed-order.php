<?php
if (!defined('ABSPATH')) {
    exit;
}
echo $email_heading . "\n\n";

printf('Hallo %s,' . "\n\n", $order->get_billing_first_name());
echo "Ihr Mailkonto wurde erfolgreich erstellt. Hier sind Ihre Zugangsdaten:\n\n";

echo "E-Mail-Adresse: " . $email->placeholders['{username}'] . "@macbay.de\n";
echo "Passwort: " . $email->placeholders['{password}'] . "\n";
echo "Webmail: " . $email->placeholders['{webmail_url}'] . "\n\n";

echo "IMAP/SMTP-Einstellungen:\n";
echo "Server: mail.macbay.eu\n";
echo "IMAP-Port: 993 (SSL/TLS)\n";
echo "SMTP-Port: 587 (STARTTLS)\n\n";

echo "Wir empfehlen Ihnen, Ihr Passwort bei der ersten Anmeldung zu ändern.\n";
echo "Falls Sie Fragen haben, kontaktieren Sie uns gerne!\n\n";

echo apply_filters('woocommerce_email_footer_text', get_option('woocommerce_email_footer_text'));
