<?php
/**
 * Module: CTA
 *
 * @package Godan
 */

$id_module   = $module['id_module'] ?? '';
$headline    = $module['headline'] ?? '';
$description = $module['description'] ?? '';
$phone       = $module['phone'] ?? '';
$button      = $module['button'] ?? [];

$phone_clean = preg_replace('/[^0-9+]/', '', $phone);
?>

<section <?= $id_module ? 'id="' . esc_attr($id_module) . '" ' : ''; ?>class="cta">
    <div class="container">
        <div class="cta__content">
            <?php if ($headline): ?>
                <h2 class="cta__headline headline"><?= esc_html($headline); ?></h2>
            <?php endif; ?>

            <?php if ($description): ?>
                <div class="cta__description description"><?= wp_kses_post($description); ?></div>
            <?php endif; ?>
        </div>

        <div class="cta__actions">
            <?php if ($phone): ?>
                <a href="tel:<?= esc_attr($phone_clean); ?>" class="cta__phone">
                    <i data-lucide="phone"></i>
                    <?= esc_html($phone); ?>
                </a>
            <?php endif; ?>

            <?php if ($button): ?>
                <a href="<?= esc_url($button['url']); ?>" class="btn btn--secondary"<?= !empty($button['target']) ? ' target="' . esc_attr($button['target']) . '"' : ''; ?>>
                    <?= esc_html($button['title']); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>
