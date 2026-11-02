<?php
/**
 * Contact Form Handler
 *
 * Obsługa formularza kontaktowego - zapis do bazy danych
 *
 * @package Godan
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Nazwa tabeli w bazie danych
 */
function godan_get_contacts_table_name() {
    global $wpdb;
    return $wpdb->prefix . 'godan_contact_form_submissions';
}

/**
 * Tworzenie tabeli przy aktywacji motywu
 */
function godan_create_contacts_table() {
    global $wpdb;

    $table_name = godan_get_contacts_table_name();
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(50) DEFAULT '',
        message TEXT NOT NULL,
        is_read TINYINT(1) NOT NULL DEFAULT 1,
        is_archived TINYINT(1) NOT NULL DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY created_at (created_at)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Kolumna is_read doszła w 1.3 - istniejące zgłoszenia dostają DEFAULT 1 (przeczytane)
    $has_is_read = $wpdb->get_var($wpdb->prepare("SHOW COLUMNS FROM $table_name LIKE %s", 'is_read'));
    if (!$has_is_read) {
        $wpdb->query("ALTER TABLE $table_name ADD COLUMN is_read TINYINT(1) NOT NULL DEFAULT 1 AFTER message");
    }

    // Kolumna is_archived doszła w 1.4
    $has_is_archived = $wpdb->get_var($wpdb->prepare("SHOW COLUMNS FROM $table_name LIKE %s", 'is_archived'));
    if (!$has_is_archived) {
        $wpdb->query("ALTER TABLE $table_name ADD COLUMN is_archived TINYINT(1) NOT NULL DEFAULT 0 AFTER is_read");
    }

    // Zapisz wersję tabeli
    update_option('godan_contact_form_submissions_version', '1.4');
}
add_action('after_switch_theme', 'godan_create_contacts_table');

/**
 * Sprawdź i utwórz tabelę jeśli nie istnieje (dla bezpieczeństwa)
 */
function godan_maybe_create_contacts_table() {
    if (get_option('godan_contact_form_submissions_version') !== '1.4') {
        godan_create_contacts_table();
    }
}
add_action('init', 'godan_maybe_create_contacts_table');

/**
 * Rejestracja AJAX handlerów
 */
function godan_register_contact_ajax() {
    // Dla zalogowanych użytkowników
    add_action('wp_ajax_godan_submit_contact', 'godan_handle_contact_submission');
    // Dla niezalogowanych użytkowników
    add_action('wp_ajax_nopriv_godan_submit_contact', 'godan_handle_contact_submission');
    // Świeży nonce - strony siedzą w LiteSpeed Cache dłużej niż żyje nonce (12-24h)
    add_action('wp_ajax_godan_contact_nonce', 'godan_send_fresh_contact_nonce');
    add_action('wp_ajax_nopriv_godan_contact_nonce', 'godan_send_fresh_contact_nonce');
}
add_action('init', 'godan_register_contact_ajax');

/**
 * Zwróć aktualny nonce formularza (odpowiedź nie może trafić do cache)
 */
function godan_send_fresh_contact_nonce() {
    do_action('litespeed_control_set_nocache', 'godan contact nonce');
    nocache_headers();
    wp_send_json_success(['nonce' => wp_create_nonce('godan_contact_nonce')]);
}

/**
 * Obsługa wysłania formularza kontaktowego
 */
function godan_handle_contact_submission() {
    // Weryfikacja nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'godan_contact_nonce')) {
        wp_send_json_error([
            'message' => 'Błąd bezpieczeństwa. Odśwież stronę i spróbuj ponownie.'
        ], 403);
    }

    // Honeypot - jeśli wypełnione, to bot
    if (!empty($_POST['website'])) {
        // Udajemy sukces żeby bot myślał że działa
        wp_send_json_success([
            'message' => 'Dziękujemy za wiadomość! Odpowiemy najszybciej jak to możliwe.'
        ]);
    }

    // Pobierz i waliduj dane
    // wp_unslash - WP dodaje ukośniki do $_POST (inaczej " zapisuje się jako \")
    $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $phone = isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '';
    $message = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';

    // Walidacja wymaganych pól
    $errors = [];

    if (empty($name)) {
        $errors['name'] = 'Imię i nazwisko jest wymagane.';
    } elseif (strlen($name) < 3) {
        $errors['name'] = 'Imię i nazwisko musi mieć minimum 3 znaki.';
    }

    if (empty($email)) {
        $errors['email'] = 'Adres e-mail jest wymagany.';
    } elseif (!is_email($email)) {
        $errors['email'] = 'Podaj prawidłowy adres e-mail.';
    }

    if (empty($message)) {
        $errors['message'] = 'Wiadomość jest wymagana.';
    } elseif (strlen($message) < 10) {
        $errors['message'] = 'Wiadomość musi mieć minimum 10 znaków.';
    }

    // Walidacja zgody na politykę prywatności
    $privacy_consent = isset($_POST['privacy_consent']) && $_POST['privacy_consent'] === 'on';
    if (!$privacy_consent) {
        $errors['privacy_consent'] = 'Musisz zaakceptować politykę prywatności.';
    }

    // Jeśli są błędy, zwróć je
    if (!empty($errors)) {
        wp_send_json_error([
            'message' => 'Popraw błędy w formularzu.',
            'errors' => $errors
        ], 400);
    }

    // Zapisz do bazy danych
    global $wpdb;
    $table_name = godan_get_contacts_table_name();

    $result = $wpdb->insert(
        $table_name,
        [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'message' => $message,
            'is_read' => 0,
            'created_at' => current_time('mysql')
        ],
        ['%s', '%s', '%s', '%s', '%d', '%s']
    );

    if ($result === false) {
        wp_send_json_error([
            'message' => 'Wystąpił błąd podczas zapisywania wiadomości. Spróbuj ponownie.'
        ], 500);
    }

    // Powiadomienie mailowe - zgłoszenie jest już w bazie, więc błąd maila nie blokuje odpowiedzi
    godan_send_contact_notification([
        'name'       => $name,
        'email'      => $email,
        'phone'      => $phone,
        'message'    => $message,
        'created_at' => current_time('mysql'),
    ]);

    // Sukces
    wp_send_json_success([
        'message' => 'Dziękujemy za wiadomość! Odpowiemy najszybciej jak to możliwe.'
    ]);
}

/**
 * Wyślij maila z nowym zgłoszeniem.
 *
 * @param array $data name, email, phone, message, created_at
 * @return bool
 */
function godan_send_contact_notification($data) {
    $recipient = apply_filters('godan_contact_email_to', 'godansiedlce@gmail.com', $data);

    if (empty($recipient) || !is_email($recipient)) {
        return false;
    }

    $site_name = wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);
    $subject   = sprintf('Nowe zgłoszenie z formularza – %s', $data['name']);
    $body      = godan_build_contact_email_html($data, admin_url('admin.php?page=contact-submissions'));

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        sprintf('From: %s <wordpress@%s>', $site_name, wp_parse_url(home_url(), PHP_URL_HOST)),
        sprintf('Reply-To: %s <%s>', $data['name'], $data['email']),
    ];

    return wp_mail($recipient, $subject, $body, $headers);
}

/**
 * HTML maila w kolorach Godan (inline CSS - klienty pocztowe ignorują <style>).
 *
 * @param array  $data
 * @param string $admin_url
 * @return string
 */
function godan_build_contact_email_html($data, $admin_url) {
    $name    = esc_html($data['name']);
    $email   = esc_html($data['email']);
    $message = nl2br(esc_html($data['message']));
    $when    = esc_html(date_i18n('d.m.Y H:i', strtotime($data['created_at'])));
    $email_a = esc_url('mailto:' . $data['email']);
    $phone   = !empty($data['phone']) ? esc_html($data['phone']) : '';
    $phone_a = $phone ? esc_url('tel:' . preg_replace('/\s+/', '', $data['phone'])) : '';
    $label   = 'padding:12px 0;border-bottom:1px solid #e5e7eb;font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.04em;width:110px;vertical-align:top;';
    $value   = 'padding:12px 0;border-bottom:1px solid #e5e7eb;font-size:15px;color:#111827;';

    ob_start();
    ?>
    <!doctype html>
    <html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Nowe zgłoszenie</title>
    </head>
    <body style="margin:0;padding:0;background:#f3f4f6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:14px;overflow:hidden;">
                    <tr>
                        <td style="background-color:#672b83;background-image:linear-gradient(135deg,#581c6e 0%,#672b83 45%,#f18825 100%);padding:28px 32px;color:#ffffff;">
                            <div style="font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#fed7aa;">Godan Siedlce · Balony i dekoracje</div>
                            <div style="font-size:22px;font-weight:700;margin-top:6px;">Nowe zgłoszenie z formularza</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px;">
                            <p style="margin:0 0 20px;color:#4b5563;font-size:14px;line-height:1.6;">
                                Ktoś wypełnił formularz na stronie. Kliknij „Odpowiedz” — wiadomość trafi prosto do tej osoby.
                            </p>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                <tr>
                                    <td style="<?= $label ?>">Imię</td>
                                    <td style="<?= $value ?>font-weight:600;"><?= $name ?></td>
                                </tr>
                                <tr>
                                    <td style="<?= $label ?>">E-mail</td>
                                    <td style="<?= $value ?>"><a href="<?= $email_a ?>" style="color:#f18825;text-decoration:none;font-weight:600;"><?= $email ?></a></td>
                                </tr>
                                <tr>
                                    <td style="<?= $label ?>">Telefon</td>
                                    <td style="<?= $value ?>">
                                        <?php if ($phone_a): ?>
                                            <a href="<?= $phone_a ?>" style="color:#f18825;text-decoration:none;font-weight:600;"><?= $phone ?></a>
                                        <?php else: ?>
                                            <span style="color:#9ca3af;">— nie podano —</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="<?= $label ?>">Data</td>
                                    <td style="<?= $value ?>color:#374151;"><?= $when ?></td>
                                </tr>
                            </table>

                            <div style="margin-top:24px;padding:16px 18px;background:#fff7ed;border-left:4px solid #f18825;border-radius:6px;">
                                <div style="font-size:12px;font-weight:600;color:#9a3412;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:8px;">Wiadomość</div>
                                <div style="font-size:15px;color:#1f2937;line-height:1.65;"><?= $message ?></div>
                            </div>

                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:28px auto 0;">
                                <tr>
                                    <td style="padding-right:8px;">
                                        <a href="<?= $email_a ?>" style="display:inline-block;padding:12px 22px;background:#f18825;color:#ffffff;text-decoration:none;border-radius:999px;font-size:14px;font-weight:700;">Odpisz</a>
                                    </td>
                                    <?php if ($phone_a): ?>
                                    <td style="padding-right:8px;">
                                        <a href="<?= $phone_a ?>" style="display:inline-block;padding:12px 22px;background:#672b83;color:#ffffff;text-decoration:none;border-radius:999px;font-size:14px;font-weight:700;">Zadzwoń</a>
                                    </td>
                                    <?php endif; ?>
                                    <td>
                                        <a href="<?= esc_url($admin_url) ?>" style="display:inline-block;padding:11px 21px;border:1px solid #d1d5db;color:#374151;text-decoration:none;border-radius:999px;font-size:14px;font-weight:600;">Panel</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 32px;background:#f9fafb;border-top:1px solid #e5e7eb;font-size:12px;color:#9ca3af;text-align:center;">
                            Wiadomość wysłana automatycznie z formularza na <?= esc_html(wp_parse_url(home_url(), PHP_URL_HOST)) ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    </body>
    </html>
    <?php
    return ob_get_clean();
}

/**
 * Dodaj skrypt AJAX do frontendu
 */
function godan_enqueue_contact_scripts() {
    wp_localize_script('godan-navigation', 'godanContact', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('godan_contact_nonce')
    ]);
}
add_action('wp_enqueue_scripts', 'godan_enqueue_contact_scripts');
