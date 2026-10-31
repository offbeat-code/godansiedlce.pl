<?php
/**
 * Module: Testimonials
 *
 * @package Godan
 */

$id_module = $module['id_module'] ?? '';
$tagline   = $module['tagline'] ?? '';
$headline  = $module['headline'] ?? '';
$opinions  = $module['opinions'] ?? [];
?>

<section <?= $id_module ? 'id="' . esc_attr($id_module) . '" ' : ''; ?>class="testimonials">
    <div class="container">
        <div class="testimonials__header">
            <?php if ($tagline): ?>
                <span class="testimonials__tagline tagline"><?= esc_html($tagline); ?></span>
            <?php endif; ?>

            <?php if ($headline): ?>
                <h2 class="testimonials__headline headline"><?= esc_html($headline); ?></h2>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($opinions): ?>
        <div class="testimonials__slider swiper">
            <div class="testimonials__track swiper-wrapper">
                <?php foreach ($opinions as $item):
                    $author_info = $item['author_info'] ?? [];
                    $opinion     = $item['opinion'] ?? '';
                    $name        = $author_info['name'] ?? '';
                    $initials    = $author_info['initials'] ?? '';
                    $category    = $author_info['category'] ?? '';

                    if (!$opinion) continue;
                ?>
                    <div class="testimonials__card swiper-slide">
                        <div class="testimonials__content">
                            <div class="testimonials__stars" aria-label="5 gwiazdek">★★★★★</div>
                            <p class="testimonials__text"><?= esc_html($opinion); ?></p>
                        </div>
                        <div class="testimonials__author">
                            <?php if ($initials): ?>
                                <div class="testimonials__avatar"><?= esc_html($initials); ?></div>
                            <?php endif; ?>
                            <div>
                                <?php if ($name): ?>
                                    <div class="testimonials__name"><?= esc_html($name); ?></div>
                                <?php endif; ?>
                                <?php if ($category): ?>
                                    <div class="testimonials__role"><?= esc_html($category); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</section>
