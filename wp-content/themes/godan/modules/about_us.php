<?php
/**
 * Module: About Us
 *
 * @package Godan
 */

$id_module   = $module['id_module'] ?? '';
$tagline     = $module['tagline'] ?? '';
$headline    = $module['headline'] ?? '';
$description = $module['description'] ?? '';
$image       = $module['image'] ?? [];
$cards       = $module['cards'] ?? [];
?>

<section <?= $id_module ? 'id="' . esc_attr($id_module) . '" ' : ''; ?>class="about-us">
    <div class="container">
        <div class="about-us__inner">
            <div class="about-us__top">
                <div class="about-us__content">
                    <?php if ($tagline): ?>
                        <span class="about-us__tagline tagline"><?= esc_html($tagline); ?></span>
                    <?php endif; ?>

                    <?php if ($headline): ?>
                        <h2 class="about-us__headline headline"><?= esc_html($headline); ?></h2>
                    <?php endif; ?>

                    <?php if ($description): ?>
                        <div class="about-us__description description">
                            <?= wp_kses_post($description); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($image):
                    $thumb_url  = $image['sizes']['thumbnail'] ?? $image['url'];
                    $medium_url = $image['sizes']['medium_large'] ?? $image['url'];
                    $full_url   = $image['url'];
                    $alt        = $image['alt'] ?: $headline;
                ?>
                    <div class="about-us__image-wrapper">
                        <img
                            src="<?= esc_url($thumb_url); ?>"
                            alt=""
                            class="about-us__placeholder"
                            aria-hidden="true"
                        >
                        <img
                            src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                            data-src="<?= esc_url($medium_url); ?>"
                            data-full="<?= esc_url($full_url); ?>"
                            alt="<?= esc_attr($alt); ?>"
                            class="about-us__image"
                        >
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($cards): ?>
                <div class="about-us__cards">
                    <?php foreach ($cards as $card):
                        $icon        = $card['icon'] ?? '';
                        $title       = $card['title'] ?? '';
                        $card_desc   = $card['description'] ?? '';

                        if (!$title) continue;
                    ?>
                        <div class="about-us__card">
                            <?php if ($icon): ?>
                                <div class="about-us__card-icon">
                                    <i data-lucide="<?= esc_attr($icon); ?>"></i>
                                </div>
                            <?php endif; ?>

                            <h3 class="about-us__card-title"><?= esc_html($title); ?></h3>

                            <?php if ($card_desc): ?>
                                <p class="about-us__card-text"><?= esc_html($card_desc); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
(function() {
    const wrapper = document.querySelector('.about-us__image-wrapper');
    if (!wrapper) return;

    const img = wrapper.querySelector('.about-us__image');
    const mediumSrc = img.dataset.src;
    const fullSrc = img.dataset.full;

    if (mediumSrc) {
        const observer = new IntersectionObserver(function(entries, obs) {
            if (entries[0].isIntersecting) {
                // Najpierw załaduj medium
                img.src = mediumSrc;
                img.onload = function() {
                    wrapper.classList.add('is-loaded');

                    // Po załadowaniu medium, załaduj full w tle
                    if (fullSrc && fullSrc !== mediumSrc) {
                        const fullImg = new Image();
                        fullImg.onload = function() {
                            img.src = fullSrc;
                        };
                        fullImg.src = fullSrc;
                    }
                };
                obs.unobserve(wrapper);
            }
        }, { rootMargin: '100px' });

        observer.observe(wrapper);
    }
})();
</script>
