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
 * Liczba nieprzeczytanych zgłoszeń
 */
function godan_count_unread_contacts() {
    global $wpdb;
    $table_name = godan_get_contacts_table_name();
    return (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE is_read = 0 AND is_archived = 0");
}

/**
 * Dodaj stronę do menu admina
 */
function godan_add_contact_submissions_menu() {
    $unread = godan_count_unread_contacts();
    $bubble = $unread ? sprintf(' <span class="awaiting-mod count-%1$d"><span class="pending-count">%1$d</span></span>', $unread) : '';

    $hook = add_menu_page(
        'Zgłoszenia z formularza kontaktowego', // Tytuł strony
        'Zgłoszenia' . $bubble,                  // Nazwa w menu
        'edit_posts',                       // Uprawnienia
        'contact-submissions',              // Slug
        'godan_render_contact_submissions_page', // Callback
        'dashicons-email-alt',              // Ikona
        31                                  // Pozycja (po "Zasoby strony")
    );

    add_action('load-' . $hook, 'godan_handle_contact_submissions_actions');
}
add_action('admin_menu', 'godan_add_contact_submissions_menu');

/**
 * Obsługa usuwania (przed wysłaniem HTML, żeby móc przekierować)
 */
function godan_handle_contact_submissions_actions() {
    if (!current_user_can('edit_posts')) {
        return;
    }

    global $wpdb;
    $table_name = godan_get_contacts_table_name();
    $base_url   = admin_url('admin.php?page=contact-submissions');
    $deleted    = null;
    $notice     = [];

    // Wracamy do zakładki, z której przyszło działanie
    $status = sanitize_key($_REQUEST['status'] ?? '');
    if (in_array($status, ['new', 'archived'], true)) {
        $base_url = add_query_arg('status', $status, $base_url);
    }

    // Pojedyncze działanie (przyciski w wierszu)
    if (isset($_GET['godan_action'], $_GET['id'])) {
        $id     = absint($_GET['id']);
        $action = sanitize_key($_GET['godan_action']);
        check_admin_referer('godan_contact_' . $action . '_' . $id);

        if ($action === 'delete') {
            $deleted = (int) $wpdb->delete($table_name, ['id' => $id], ['%d']);
        } elseif ($action === 'archive' || $action === 'restore') {
            $n = (int) $wpdb->update($table_name, ['is_archived' => $action === 'archive' ? 1 : 0], ['id' => $id], ['%d'], ['%d']);
            $notice = [$action === 'archive' ? 'archived' : 'restored' => $n];
        }
    }

    // Działania masowe
    $bulk = isset($_POST['godan_bulk_apply']) ? sanitize_key($_POST['bulk_action'] ?? '') : '';
    if (in_array($bulk, ['delete', 'archive', 'restore'], true)) {
        check_admin_referer('godan_contacts_bulk');
        $ids = array_filter(array_map('absint', (array) ($_POST['ids'] ?? [])));
        $n   = 0;
        if ($ids) {
            $placeholders = implode(',', array_fill(0, count($ids), '%d'));
            if ($bulk === 'delete') {
                $n = (int) $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id IN ($placeholders)", $ids));
            } else {
                $flag = $bulk === 'archive' ? 1 : 0;
                $n    = (int) $wpdb->query($wpdb->prepare("UPDATE $table_name SET is_archived = $flag WHERE id IN ($placeholders)", $ids));
            }
        }
        if ($bulk === 'delete') {
            $deleted = $n;
        } else {
            $notice = [$bulk === 'archive' ? 'archived' : 'restored' => $n];
        }
    }

    if ($notice) {
        wp_safe_redirect(add_query_arg($notice, $base_url));
        exit;
    }

    // Usunięcie starszych niż X miesięcy
    if (isset($_POST['godan_delete_older'])) {
        check_admin_referer('godan_contacts_bulk');
        $months = absint($_POST['months'] ?? 0);
        if (in_array($months, [3, 6, 12], true)) {
            $cutoff  = wp_date('Y-m-d H:i:s', strtotime("-$months months", current_time('timestamp', true)));
            $deleted = (int) $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE created_at < %s", $cutoff));
        }
    }

    if ($deleted !== null) {
        wp_safe_redirect(add_query_arg('deleted', $deleted, $base_url));
        exit;
    }
}

/**
 * Dodaj style dla strony zgłoszeń (tylko odstępy - reszta to domyślny wygląd WP)
 */
function godan_contact_submissions_admin_styles($hook) {
    if ($hook !== 'toplevel_page_contact-submissions') {
        return;
    }

    wp_add_inline_style('wp-admin', '
        .godan-contacts .wp-list-table {
            min-width: 960px;
        }
        .godan-contacts .table-wrap {
            overflow-x: auto;
            clear: both;
            width: 100%;
        }
        .godan-contacts .wp-list-table tbody td {
            padding-top: 12px;
            padding-bottom: 12px;
        }
        .godan-contacts .wp-list-table tbody th.check-column {
            padding-top: 14px;
        }
        .godan-contacts .column-date { width: 120px; }
        .godan-contacts .column-name { width: 18%; }
        .godan-contacts .column-contact { width: 22%; }
        .godan-contacts .column-actions { width: 230px; text-align: right; }
        .godan-contacts .godan-msg { white-space: pre-line; }
        .godan-contacts .godan-actions { display: flex; gap: 6px; justify-content: flex-end; }
        .godan-contacts .godan-actions .button-link-delete { color: #b32d2e; }
        .godan-contacts tr.godan-unread td,
        .godan-contacts tr.godan-unread th { background: #fef7f1; }
        .godan-contacts tr.godan-unread th.check-column { border-left: 4px solid #d63638; }
        .godan-contacts .tablenav .actions select { margin-right: 4px; }
    ');
}
add_action('admin_enqueue_scripts', 'godan_contact_submissions_admin_styles');

/**
 * Paginacja w stylu WP list table
 *
 * @param int $current_page
 * @param int $total_pages
 * @param int $total_items
 */
function godan_contact_submissions_pagination($current_page, $total_pages, $total_items) {
    ?>
    <div class="tablenav-pages<?= $total_pages <= 1 ? ' one-page' : '' ?>">
        <span class="displaying-num"><?= esc_html(sprintf(_n('%s element', '%s elementów', $total_items), number_format_i18n($total_items))) ?></span>
        <?php if ($total_pages > 1): ?>
            <span class="pagination-links">
                <?php if ($current_page > 1): ?>
                    <a class="first-page button" href="<?= esc_url(add_query_arg('paged', 1)) ?>"><span aria-hidden="true">«</span></a>
                    <a class="prev-page button" href="<?= esc_url(add_query_arg('paged', $current_page - 1)) ?>"><span aria-hidden="true">‹</span></a>
                <?php else: ?>
                    <span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
                    <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
                <?php endif; ?>
                <span class="paging-input">
                    <span class="tablenav-paging-text"><?= esc_html($current_page) ?> z <span class="total-pages"><?= esc_html($total_pages) ?></span></span>
                </span>
                <?php if ($current_page < $total_pages): ?>
                    <a class="next-page button" href="<?= esc_url(add_query_arg('paged', $current_page + 1)) ?>"><span aria-hidden="true">›</span></a>
                    <a class="last-page button" href="<?= esc_url(add_query_arg('paged', $total_pages)) ?>"><span aria-hidden="true">»</span></a>
                <?php else: ?>
                    <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
                    <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
                <?php endif; ?>
            </span>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Renderuj stronę ze zgłoszeniami
 */
function godan_render_contact_submissions_page() {
    global $wpdb;
    $table_name = godan_get_contacts_table_name();
    $base_url   = admin_url('admin.php?page=contact-submissions');

    // Filtr: wszystkie / nowe / archiwum
    $status = sanitize_key($_GET['status'] ?? '');
    if (!in_array($status, ['new', 'archived'], true)) {
        $status = 'all';
    }

    // Wyszukiwanie
    $search     = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
    $conditions = [$status === 'archived' ? 'is_archived = 1' : 'is_archived = 0'];
    if ($status === 'new') {
        $conditions[] = 'is_read = 0';
    }
    if ($search !== '') {
        $like         = '%' . $wpdb->esc_like($search) . '%';
        $conditions[] = $wpdb->prepare('(name LIKE %s OR email LIKE %s OR phone LIKE %s OR message LIKE %s)', $like, $like, $like, $like);
    }
    $where = ' WHERE ' . implode(' AND ', $conditions);

    // Paginacja
    $per_page = 20;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $per_page;

    $total_items = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name" . $where);
    $total_pages = (int) ceil($total_items / $per_page);
    $count_all      = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE is_archived = 0");
    $count_new      = godan_count_unread_contacts();
    $count_archived = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE is_archived = 1");
    $view_url       = $status === 'all' ? $base_url : add_query_arg('status', $status, $base_url);

    // Pobierz zgłoszenia (od najnowszych)
    $submissions = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name" . $where . " ORDER BY created_at DESC LIMIT %d OFFSET %d",
        $per_page,
        $offset
    ));

    ?>
    <div class="wrap godan-contacts">
        <h1 class="wp-heading-inline">Zgłoszenia z formularza kontaktowego</h1>
        <?php if ($search !== ''): ?>
            <span class="subtitle">Wyniki wyszukiwania dla: <strong><?= esc_html($search) ?></strong></span>
        <?php endif; ?>
        <hr class="wp-header-end">

        <?php
        $notices = [
            'deleted'  => ['Usunięto zgłoszeń: %d.', 'Nic nie zostało usunięte.'],
            'archived' => ['Przeniesiono do archiwum: %d.', 'Nic nie zostało zarchiwizowane.'],
            'restored' => ['Przywrócono z archiwum: %d.', 'Nic nie zostało przywrócone.'],
        ];
        foreach ($notices as $key => [$done, $none]):
            if (!isset($_GET[$key])) continue;
            $n = absint($_GET[$key]);
        ?>
            <div class="notice notice-success is-dismissible">
                <p><?= esc_html($n ? sprintf($done, $n) : $none) ?></p>
            </div>
        <?php endforeach; ?>

        <ul class="subsubsub">
            <li class="all">
                <a href="<?= esc_url($base_url) ?>"<?= $status === 'all' ? ' class="current" aria-current="page"' : '' ?>>Wszystkie <span class="count">(<?= esc_html($count_all) ?>)</span></a> |
            </li>
            <li class="new">
                <a href="<?= esc_url(add_query_arg('status', 'new', $base_url)) ?>"<?= $status === 'new' ? ' class="current" aria-current="page"' : '' ?>>Nowe <span class="count">(<?= esc_html($count_new) ?>)</span></a> |
            </li>
            <li class="archived">
                <a href="<?= esc_url(add_query_arg('status', 'archived', $base_url)) ?>"<?= $status === 'archived' ? ' class="current" aria-current="page"' : '' ?>>Archiwum <span class="count">(<?= esc_html($count_archived) ?>)</span></a>
            </li>
        </ul>

        <form method="get" action="<?= esc_url(admin_url('admin.php')) ?>">
            <input type="hidden" name="page" value="contact-submissions">
            <?php if ($status !== 'all'): ?><input type="hidden" name="status" value="<?= esc_attr($status) ?>"><?php endif; ?>
            <p class="search-box">
                <label class="screen-reader-text" for="godan-contact-search">Szukaj zgłoszeń:</label>
                <input type="search" id="godan-contact-search" name="s" value="<?= esc_attr($search) ?>">
                <input type="submit" class="button" value="Szukaj zgłoszeń">
            </p>
        </form>

        <form method="post">
            <?php wp_nonce_field('godan_contacts_bulk'); ?>
            <input type="hidden" name="status" value="<?= esc_attr($status) ?>">

            <div class="tablenav top">
                <div class="alignleft actions bulkactions">
                    <label for="godan-bulk-action" class="screen-reader-text">Wybierz działanie masowe</label>
                    <select name="bulk_action" id="godan-bulk-action">
                        <option value="">Działania masowe</option>
                        <?php if ($status === 'archived'): ?>
                            <option value="restore">Przywróć z archiwum</option>
                        <?php else: ?>
                            <option value="archive">Przenieś do archiwum</option>
                        <?php endif; ?>
                        <option value="delete">Usuń</option>
                    </select>
                    <input type="submit" name="godan_bulk_apply" class="button action" value="Zastosuj"
                           onclick="return document.getElementById('godan-bulk-action').value !== 'delete' || confirm('Usunąć zaznaczone zgłoszenia? Tego nie da się cofnąć.');">
                </div>
                <?php if ($count_all): ?>
                    <div class="alignleft actions">
                        <label for="godan-older" class="screen-reader-text">Usuń zgłoszenia starsze niż</label>
                        <select name="months" id="godan-older">
                            <option value="3">Starsze niż 3 miesiące</option>
                            <option value="6">Starsze niż 6 miesięcy</option>
                            <option value="12" selected>Starsze niż 12 miesięcy</option>
                        </select>
                        <input type="submit" name="godan_delete_older" class="button" value="Usuń starsze"
                               onclick="return confirm('Usunąć wszystkie zgłoszenia starsze niż wybrany okres? Tego nie da się cofnąć.');">
                    </div>
                <?php endif; ?>
                <?php godan_contact_submissions_pagination($current_page, $total_pages, $total_items); ?>
                <br class="clear">
            </div>

            <div class="table-wrap">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column">
                            <label class="screen-reader-text" for="godan-cb-all">Zaznacz wszystko</label>
                            <input type="checkbox" id="godan-cb-all">
                        </td>
                        <th scope="col" class="manage-column column-date">Data</th>
                        <th scope="col" class="manage-column column-name column-primary">Imię i nazwisko</th>
                        <th scope="col" class="manage-column column-contact">Kontakt</th>
                        <th scope="col" class="manage-column column-message">Wiadomość</th>
                        <th scope="col" class="manage-column column-actions">Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($submissions)): ?>
                        <tr class="no-items">
                            <td class="colspanchange" colspan="6">
                                <?php
                                if ($search !== '') {
                                    echo 'Brak zgłoszeń pasujących do wyszukiwania.';
                                } elseif ($status === 'new') {
                                    echo 'Brak nowych zgłoszeń.';
                                } elseif ($status === 'archived') {
                                    echo 'Archiwum jest puste.';
                                } else {
                                    echo 'Brak zgłoszeń.';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($submissions as $submission):
                        $is_new     = empty($submission->is_read);
                        $created    = strtotime($submission->created_at);
                        $row_url = function ($action) use ($submission, $view_url) {
                            return wp_nonce_url(
                                add_query_arg(['godan_action' => $action, 'id' => $submission->id], $view_url),
                                'godan_contact_' . $action . '_' . $submission->id
                            );
                        };
                        $archive_action = $submission->is_archived ? 'restore' : 'archive';
                    ?>
                        <tr class="<?= $is_new ? 'godan-unread' : '' ?>">
                            <th scope="row" class="check-column">
                                <label class="screen-reader-text" for="godan-cb-<?= esc_attr($submission->id) ?>">Zaznacz</label>
                                <input type="checkbox" id="godan-cb-<?= esc_attr($submission->id) ?>" name="ids[]" value="<?= esc_attr($submission->id) ?>">
                            </th>
                            <td class="column-date">
                                <?= esc_html(date_i18n('d.m.Y', $created)) ?><br>
                                <span class="description"><?= esc_html(date_i18n('H:i', $created)) ?></span>
                            </td>
                            <td class="column-name column-primary">
                                <strong><?= esc_html($submission->name) ?></strong><?php if ($is_new): ?> — <span class="post-state">Nowe</span><?php endif; ?>
                            </td>
                            <td class="column-contact">
                                <a href="mailto:<?= esc_attr($submission->email) ?>"><?= esc_html($submission->email) ?></a>
                                <?php if (!empty($submission->phone)): ?>
                                    <br><a href="tel:<?= esc_attr(preg_replace('/\s+/', '', $submission->phone)) ?>"><?= esc_html($submission->phone) ?></a>
                                <?php endif; ?>
                            </td>
                            <td class="column-message"><div class="godan-msg"><?= esc_html($submission->message) ?></div></td>
                            <td class="column-actions">
                                <div class="godan-actions">
                                    <a class="button button-small" href="mailto:<?= esc_attr($submission->email) ?>?subject=<?= rawurlencode('Odpowiedź – Godan Siedlce') ?>">Odpisz</a>
                                    <a class="button button-small" href="<?= esc_url($row_url($archive_action)) ?>"><?= $archive_action === 'archive' ? 'Archiwizuj' : 'Przywróć' ?></a>
                                    <a class="button button-small button-link-delete" href="<?= esc_url($row_url('delete')) ?>" onclick="return confirm('Usunąć zgłoszenie od <?= esc_js($submission->name) ?>?');">Usuń</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>

            <div class="tablenav bottom">
                <?php godan_contact_submissions_pagination($current_page, $total_pages, $total_items); ?>
                <br class="clear">
            </div>
        </form>

        <script>
            document.getElementById('godan-cb-all').addEventListener('change', function () {
                document.querySelectorAll('input[name="ids[]"]').forEach(function (cb) { cb.checked = this.checked; }, this);
            });
        </script>
    </div>
    <?php

    // Wyświetlone zgłoszenia uznajemy za przeczytane (oznaczenie „Nowe” zostaje do następnego wejścia)
    $shown_unread = wp_list_pluck(array_filter($submissions, function ($s) {
        return empty($s->is_read);
    }), 'id');
    if ($shown_unread) {
        $placeholders = implode(',', array_fill(0, count($shown_unread), '%d'));
        $wpdb->query($wpdb->prepare("UPDATE $table_name SET is_read = 1 WHERE id IN ($placeholders)", array_map('intval', $shown_unread)));
    }
}
