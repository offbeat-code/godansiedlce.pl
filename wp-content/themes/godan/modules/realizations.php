<?php
/**
 * Module: Realizations
 *
 * @package Godan
 */

$id_module = $module['id_module'] ?? '';
$tagline   = $module['tagline'] ?? '';
$headline  = $module['headline'] ?? '';
$items     = $module['items'] ?? [];

$size_variants = [
    0 => 'xlarge',
    1 => '',
    2 => 'tall',
    3 => '',
    4 => 'wide',
];
?>

<section <?= $id_module ? 'id="' . esc_attr($id_module) . '" ' : ''; ?>class="realizations">
    <div class="realizations__container container">
        <div class="realizations__header">
            <?php if ($tagline): ?>
                <span class="realizations__tagline tagline"><?= esc_html($tagline); ?></span>
            <?php endif; ?>

            <?php if ($headline): ?>
                <h2 class="realizations__headline headline"><?= esc_html($headline); ?></h2>
            <?php endif; ?>
        </div>

        <?php if ($items): ?>
            <div class="realizations__grid">
                <?php foreach ($items as $index => $item):
                    $image = $item['image'] ?? [];
                    $link = $item['link'] ?? [];

                    if (!$image || !$link) continue;

                    $variant = $size_variants[$index] ?? '';
                    $item_class = 'realizations__item';
                    if ($variant) {
                        $item_class .= ' realizations__item--' . $variant;
                    }

                    $target = !empty($link['target']) ? ' target="' . esc_attr($link['target']) . '"' : '';

                    $thumb_url  = $image['sizes']['thumbnail'] ?? $image['url'];
                    $medium_url = $image['sizes']['medium_large'] ?? $image['url'];
                    $full_url   = $image['url'];
                    $alt        = $image['alt'] ?: $link['title'];
                ?>
                    <a href="<?= esc_url($link['url']); ?>" class="<?= $item_class; ?>"<?= $target; ?>>
                        <img
                            src="<?= esc_url($thumb_url); ?>"
                            alt=""
                            class="realizations__placeholder"
                            aria-hidden="true"
                        >
                        <img
                            src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                            data-src="<?= esc_url($medium_url); ?>"
                            data-full="<?= esc_url($full_url); ?>"
                            alt="<?= esc_attr($alt); ?>"
                            class="realizations__img"
                        >
                        <div class="realizations__overlay">
                            <span class="realizations__title"><?= esc_html($link['title']); ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
(function() {
    var items = document.querySelectorAll('.realizations__item');
    if (!items.length) return;

    function loadImage(item) {
        var img = item.querySelector('.realizations__img');
        if (!img || item.classList.contains('is-loaded')) return;

        var mediumSrc = img.getAttribute('data-src');
        var fullSrc = img.getAttribute('data-full');

        if (!mediumSrc) {
            item.classList.add('is-loaded');
            return;
        }

        var mediumImg = new Image();

        mediumImg.onload = function() {
            img.src = mediumSrc;
            item.classList.add('is-loaded');

            // Załaduj full w tle (opcjonalnie)
            if (fullSrc && fullSrc !== mediumSrc) {
                var fullImg = new Image();
                fullImg.onload = function() {
                    img.src = fullSrc;
                };
                fullImg.src = fullSrc;
            }
        };

        mediumImg.onerror = function() {
            // W razie błędu i tak pokaż element
            item.classList.add('is-loaded');
        };

        mediumImg.src = mediumSrc;
    }

    // Użyj IntersectionObserver jeśli dostępny
    if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function(entries) {
            for (var i = 0; i < entries.length; i++) {
                if (entries[i].isIntersecting) {
                    loadImage(entries[i].target);
                    observer.unobserve(entries[i].target);
                }
            }
        }, { rootMargin: '100px' });

        for (var i = 0; i < items.length; i++) {
            observer.observe(items[i]);
        }
    } else {
        // Fallback - załaduj wszystko od razu
        for (var i = 0; i < items.length; i++) {
            loadImage(items[i]);
        }
    }
})();
</script>
