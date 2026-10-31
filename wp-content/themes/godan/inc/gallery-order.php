<?php
/**
 * Gallery Order - Drag & Drop sortowanie dekoracji per kategoria
 *
 * @package Godan
 */

/**
 * Rejestracja submenu pod CPT Dekoracje
 */
function godan_add_gallery_order_menu() {
    add_submenu_page(
        'edit.php?post_type=dekoracje',
        'Kolejność galerii',
        'Kolejność galerii',
        'edit_posts',
        'gallery-order',
        'godan_render_gallery_order_page'
    );
}
add_action('admin_menu', 'godan_add_gallery_order_menu');

/**
 * Enqueue skryptów i stylów na stronie kolejności
 */
function godan_gallery_order_scripts($hook) {
    if ($hook !== 'dekoracje_page_gallery-order') {
        return;
    }

    wp_enqueue_script('jquery-ui-sortable');

    wp_add_inline_style('wp-admin', '
        .gallery-order-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 12px;
            margin: 20px 0;
        }
        .gallery-order-item {
            position: relative;
            aspect-ratio: 1;
            border-radius: 8px;
            overflow: hidden;
            cursor: grab;
            border: 2px solid #ddd;
            background: #f0f0f0;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .gallery-order-item:hover {
            border-color: #2271b1;
        }
        .gallery-order-item.ui-sortable-helper {
            cursor: grabbing;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            border-color: #2271b1;
            z-index: 100;
        }
        .gallery-order-item.ui-sortable-placeholder {
            visibility: visible !important;
            background: #e8f0fe;
            border: 2px dashed #2271b1;
        }
        .gallery-order-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .gallery-order-item__title {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 4px 8px;
            background: rgba(0,0,0,0.6);
            color: #fff;
            font-size: 11px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .gallery-order-item__number {
            position: absolute;
            top: 6px;
            left: 6px;
            width: 24px;
            height: 24px;
            background: rgba(0,0,0,0.6);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .gallery-order-empty {
            padding: 40px;
            text-align: center;
            color: #666;
            background: #f9f9f9;
            border-radius: 8px;
            margin: 20px 0;
        }
        .gallery-order-notice {
            display: none;
            padding: 10px 14px;
            margin: 15px 0 5px;
            background: #fff;
            border-left: 4px solid #00a32a;
            box-shadow: 0 1px 1px rgba(0,0,0,0.04);
            font-size: 13px;
        }
        .gallery-order-notice--success {
            border-left-color: #00a32a;
            color: #1d2327;
        }
        .gallery-order-notice--error {
            border-left-color: #d63638;
            color: #1d2327;
        }
        .gallery-order-category-select {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0 0;
        }
        .gallery-order-category-select select {
            min-width: 250px;
        }
    ');
}
add_action('admin_enqueue_scripts', 'godan_gallery_order_scripts');

/**
 * Render strony kolejności galerii
 */
function godan_render_gallery_order_page() {
    $terms = get_terms([
        'taxonomy'   => 'typ_dekoracji',
        'hide_empty' => false,
    ]);

    $selected_term_id = isset($_GET['category']) ? (int) $_GET['category'] : 0;

    // Domyślnie wybierz pierwszą kategorię
    if (!$selected_term_id && !empty($terms)) {
        $selected_term_id = $terms[0]->term_id;
    }

    ?>
    <div class="wrap">
        <h1>Kolejność galerii</h1>
        <div class="gallery-order-notice"></div>

        <?php if (empty($terms)): ?>
            <div class="gallery-order-empty">
                <p>Brak kategorii dekoracji. Dodaj kategorie w <a href="<?= admin_url('edit-tags.php?taxonomy=typ_dekoracji&post_type=dekoracje'); ?>">Kategorie dekoracji</a>.</p>
            </div>
            <?php return; ?>
        <?php endif; ?>

        <div class="gallery-order-category-select">
            <label for="gallery-order-category"><strong>Kategoria:</strong></label>
            <select id="gallery-order-category" onchange="window.location.href='<?= admin_url('edit.php?post_type=dekoracje&page=gallery-order&category='); ?>' + this.value">
                <?php foreach ($terms as $term): ?>
                    <option value="<?= esc_attr($term->term_id); ?>" <?php selected($selected_term_id, $term->term_id); ?>>
                        <?= esc_html($term->name); ?>
                        (<?= esc_html($term->count); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="button" id="gallery-order-save" class="button button-primary">Zapisz kolejność</button>
        </div>

        <?php
        if ($selected_term_id) {
            godan_render_gallery_order_grid($selected_term_id);
        }
        ?>
    </div>

    <script>
    jQuery(function($) {
        var $grid = $('#gallery-order-sorted');
        var $notice = $('.gallery-order-notice');
        var $saveBtn = $('#gallery-order-save');

        if (!$grid.length) return;

        $grid.sortable({
            items: '.gallery-order-item',
            placeholder: 'gallery-order-item ui-sortable-placeholder',
            tolerance: 'pointer',
            update: function() {
                $grid.find('.gallery-order-item__number').each(function(i) {
                    $(this).text(i + 1);
                });
            }
        });

        $saveBtn.on('click', function() {
            var $btn = $(this);
            $btn.prop('disabled', true).text('Zapisywanie...');
            $notice.hide();

            var allIds = [];
            $grid.find('.gallery-order-item').each(function() {
                allIds.push($(this).data('post-id'));
            });

            $.post(ajaxurl, {
                action: 'godan_save_gallery_order',
                nonce: '<?= wp_create_nonce('godan_gallery_order'); ?>',
                term_id: <?= (int) $selected_term_id; ?>,
                order: allIds
            }, function(response) {
                $btn.prop('disabled', false).text('Zapisz kolejność');

                if (response.success) {
                    $notice
                        .removeClass('gallery-order-notice--error')
                        .addClass('gallery-order-notice--success')
                        .text(response.data)
                        .fadeIn();
                } else {
                    $notice
                        .removeClass('gallery-order-notice--success')
                        .addClass('gallery-order-notice--error')
                        .text(response.data || 'Wystąpił błąd.')
                        .fadeIn();
                }

                setTimeout(function() { $notice.fadeOut(); }, 3000);
            }).fail(function() {
                $btn.prop('disabled', false).text('Zapisz kolejność');
                $notice
                    .removeClass('gallery-order-notice--success')
                    .addClass('gallery-order-notice--error')
                    .text('Błąd połączenia.')
                    .fadeIn();
            });
        });
    });
    </script>
    <?php
}

/**
 * Render grida z miniaturkami dla danej kategorii
 */
function godan_render_gallery_order_grid($term_id) {
    $saved_order = get_term_meta($term_id, 'decoration_order', true);
    if (!is_array($saved_order)) {
        $saved_order = [];
    }

    // Wszystkie dekoracje w tej kategorii — pełne obiekty (1 query)
    $all_posts = get_posts([
        'post_type'      => 'dekoracje',
        'posts_per_page' => -1,
        'tax_query'      => [[
            'taxonomy' => 'typ_dekoracji',
            'field'    => 'term_id',
            'terms'    => $term_id,
        ]],
    ]);

    if (empty($all_posts)) {
        echo '<div class="gallery-order-empty"><p>Brak dekoracji w tej kategorii.</p></div>';
        return;
    }

    // Zbuduj listę ID i załaduj miniaturki zbiorczo (1 query zamiast N)
    $all_ids = wp_list_pluck($all_posts, 'ID');
    $thumbnail_ids = array_filter(array_map('get_post_thumbnail_id', $all_posts));
    if ($thumbnail_ids) {
        get_posts([
            'post_type'      => 'attachment',
            'post__in'       => $thumbnail_ids,
            'posts_per_page' => count($thumbnail_ids),
        ]);
    }

    // Posortowane najpierw, nowe na końcu — jedna lista
    $valid_ordered = array_filter($saved_order, function($id) use ($all_ids) {
        return in_array($id, $all_ids);
    });
    $new_ids = array_diff($all_ids, $valid_ordered);
    $all_sorted = array_merge(array_values($valid_ordered), array_values($new_ids));

    ?>
    <div id="gallery-order-sorted" class="gallery-order-grid">
        <?php
        $position = 1;
        foreach ($all_sorted as $post_id):
            godan_render_gallery_order_item($post_id, $position);
            $position++;
        endforeach;
        ?>
    </div>
    <?php
}

/**
 * Render pojedynczego elementu miniaturki
 */
function godan_render_gallery_order_item($post_id, $position) {
    $thumbnail = get_the_post_thumbnail_url($post_id, 'thumbnail');
    $title = get_the_title($post_id);

    if (!$thumbnail) {
        $thumbnail = '';
    }
    ?>
    <div class="gallery-order-item" data-post-id="<?= esc_attr($post_id); ?>">
        <span class="gallery-order-item__number"><?= esc_html($position); ?></span>
        <?php if ($thumbnail): ?>
            <img src="<?= esc_url($thumbnail); ?>" alt="<?= esc_attr($title); ?>">
        <?php endif; ?>
        <span class="gallery-order-item__title"><?= esc_html($title); ?></span>
    </div>
    <?php
}

/**
 * AJAX: Zapisz kolejność galerii
 */
function godan_save_gallery_order() {
    check_ajax_referer('godan_gallery_order', 'nonce');

    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Brak uprawnień.');
    }

    $term_id = isset($_POST['term_id']) ? (int) $_POST['term_id'] : 0;
    $order   = isset($_POST['order']) ? array_map('intval', $_POST['order']) : [];

    if (!$term_id) {
        wp_send_json_error('Nie wybrano kategorii.');
    }

    $term = get_term($term_id, 'typ_dekoracji');
    if (!$term || is_wp_error($term)) {
        wp_send_json_error('Nieprawidłowa kategoria.');
    }

    // Filtruj — zachowaj tylko istniejące ID
    $order = array_filter($order, function($id) {
        return $id > 0;
    });
    $order = array_values($order);

    update_term_meta($term_id, 'decoration_order', $order);

    wp_send_json_success('Kolejność zapisana.');
}
add_action('wp_ajax_godan_save_gallery_order', 'godan_save_gallery_order');
