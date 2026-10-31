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
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY created_at (created_at)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Zapisz wersję tabeli
    update_option('godan_contact_form_submissions_version', '1.2');
}
add_action('after_switch_theme', 'godan_create_contacts_table');

/**
 * Sprawdź i utwórz tabelę jeśli nie istnieje (dla bezpieczeństwa)
 */
function godan_maybe_create_contacts_table() {
    if (get_option('godan_contact_form_submissions_version') !== '1.2') {
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
}
add_action('init', 'godan_register_contact_ajax');

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
    $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';

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
            'created_at' => current_time('mysql')
        ],
        ['%s', '%s', '%s', '%s', '%s']
    );

    if ($result === false) {
        wp_send_json_error([
            'message' => 'Wystąpił błąd podczas zapisywania wiadomości. Spróbuj ponownie.'
        ], 500);
    }

    // Sukces
    wp_send_json_success([
        'message' => 'Dziękujemy za wiadomość! Odpowiemy najszybciej jak to możliwe.'
    ]);
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
