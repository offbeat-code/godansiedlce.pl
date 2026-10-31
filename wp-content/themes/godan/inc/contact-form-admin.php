<?php
/**
 * Contact Form Admin
 *
 * Strona w panelu admina z listą zgłoszeń kontaktowych
 *
 * @package Godan
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Dodaj stronę do menu admina
 */
function godan_add_contact_submissions_menu() {
    add_menu_page(
        'Zgłoszenia z formularza kontaktowego', // Tytuł strony
        'Zgłoszenia',                            // Nazwa w menu
        'edit_posts',                       // Uprawnienia
        'contact-submissions',              // Slug
        'godan_render_contact_submissions_page', // Callback
        'dashicons-email-alt',              // Ikona
        31                                  // Pozycja (po "Zasoby strony")
    );
}
add_action('admin_menu', 'godan_add_contact_submissions_menu');

/**
 * Dodaj style dla strony zgłoszeń
 */
function godan_contact_submissions_admin_styles($hook) {
    if ($hook !== 'toplevel_page_contact-submissions') {
        return;
    }

    wp_add_inline_style('wp-admin', '
        .godan-table-wrap {
            overflow-x: auto;
            margin: 1rem 0;
        }
        .godan-table-wrap table {
            min-width: 1200px;
        }
        @media screen and (max-width: 782px) {
            .godan-table-wrap {
                margin: 0 -10px;
                padding: 0 10px;
            }
        }
    ');
}
add_action('admin_enqueue_scripts', 'godan_contact_submissions_admin_styles');

/**
 * Renderuj stronę ze zgłoszeniami
 */
function godan_render_contact_submissions_page() {
    global $wpdb;
    $table_name = godan_get_contacts_table_name();

    // Paginacja
    $per_page = 10;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $per_page;

    // Pobierz całkowitą liczbę zgłoszeń
    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $total_pages = ceil($total_items / $per_page);

    // Pobierz zgłoszenia (od najnowszych)
    $submissions = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT %d OFFSET %d",
        $per_page,
        $offset
    ));

    ?>
    <div class="wrap">
        <h1>Zgłoszenia z formularza kontaktowego</h1>

        <?php if (empty($submissions)): ?>
            <div class="notice notice-info">
                <p>Brak zgłoszeń do wyświetlenia.</p>
            </div>
        <?php else: ?>
            <p class="description">Łącznie zgłoszeń: <strong><?= esc_html($total_items) ?></strong></p>

            <div class="godan-table-wrap">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th scope="col" style="width: 50px;">ID</th>
                        <th scope="col" style="width: 150px;">Imię i nazwisko</th>
                        <th scope="col" style="width: 180px;">E-mail</th>
                        <th scope="col" style="width: 120px;">Telefon</th>
                        <th scope="col">Wiadomość</th>
                        <th scope="col" style="width: 140px;">Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($submissions as $submission): ?>
                        <tr>
                            <td><?= esc_html($submission->id) ?></td>
                            <td><strong><?= esc_html($submission->name) ?></strong></td>
                            <td>
                                <a href="mailto:<?= esc_attr($submission->email) ?>">
                                    <?= esc_html($submission->email) ?>
                                </a>
                            </td>
                            <td>
                                <?php if (!empty($submission->phone)): ?>
                                    <a href="tel:<?= esc_attr(preg_replace('/\s+/', '', $submission->phone)) ?>">
                                        <?= esc_html($submission->phone) ?>
                                    </a>
                                <?php else: ?>
                                    <span style="color: #999;">—</span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc_html($submission->message) ?></td>
                            <td><?= esc_html(date_i18n('d.m.Y H:i', strtotime($submission->created_at))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="tablenav bottom">
                    <div class="tablenav-pages">
                        <span class="displaying-num"><?= esc_html($total_items) ?> elementów</span>
                        <span class="pagination-links">
                            <?php if ($current_page > 1): ?>
                                <a class="first-page button" href="<?= esc_url(add_query_arg('paged', 1)) ?>">
                                    <span aria-hidden="true">«</span>
                                </a>
                                <a class="prev-page button" href="<?= esc_url(add_query_arg('paged', $current_page - 1)) ?>">
                                    <span aria-hidden="true">‹</span>
                                </a>
                            <?php else: ?>
                                <span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
                                <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
                            <?php endif; ?>

                            <span class="paging-input">
                                <span class="tablenav-paging-text">
                                    <?= esc_html($current_page) ?> z <span class="total-pages"><?= esc_html($total_pages) ?></span>
                                </span>
                            </span>

                            <?php if ($current_page < $total_pages): ?>
                                <a class="next-page button" href="<?= esc_url(add_query_arg('paged', $current_page + 1)) ?>">
                                    <span aria-hidden="true">›</span>
                                </a>
                                <a class="last-page button" href="<?= esc_url(add_query_arg('paged', $total_pages)) ?>">
                                    <span aria-hidden="true">»</span>
                                </a>
                            <?php else: ?>
                                <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
                                <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php
}
