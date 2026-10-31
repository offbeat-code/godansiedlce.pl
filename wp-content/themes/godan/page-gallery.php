<?php
/**
 * Template Name: Galeria
 *
 * Szablon strony z galerią dekoracji.
 * Automatycznie pobiera dekoracje z kategorii o tym samym slug co strona.
 *
 * @package Godan
 */

get_header();

$page_slug   = get_post_field('post_name', get_the_ID());
$title       = get_the_title();
$description = get_field('gallery_description');

// Pobierz kolejność z term_meta (drag & drop)
$term = get_term_by('slug', $page_slug, 'typ_dekoracji');
$ordered_ids = $term ? get_term_meta($term->term_id, 'decoration_order', true) : [];

if (!empty($ordered_ids) && is_array($ordered_ids)) {
    // Posortowane dekoracje (w kolejności z drag & drop)
    $dekoracje_ordered = get_posts([
        'post_type'      => 'dekoracje',
        'post__in'       => $ordered_ids,
        'orderby'        => 'post__in',
        'posts_per_page' => -1,
    ]);

    // Nowe dekoracje (jeszcze nieposortowane) — dopisz na końcu
    $dekoracje_new = get_posts([
        'post_type'      => 'dekoracje',
        'post__not_in'   => $ordered_ids,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'posts_per_page' => -1,
        'tax_query'      => [[
            'taxonomy' => 'typ_dekoracji',
            'field'    => 'slug',
            'terms'    => $page_slug,
        ]],
    ]);

    $dekoracje = array_merge($dekoracje_ordered, $dekoracje_new);
} else {
    // Fallback: brak zapisanej kolejności — po dacie
    $dekoracje = get_posts([
        'post_type'      => 'dekoracje',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'tax_query'      => [[
            'taxonomy' => 'typ_dekoracji',
            'field'    => 'slug',
            'terms'    => $page_slug,
        ]],
    ]);
}
?>

<main id="primary" class="site-main">
    <!-- Hero Section -->
    <section class="gallery-hero">
        <div class="gallery-hero__container container">
            <h1 class="gallery-hero__title"><?= esc_html($title); ?></h1>
            <?php if ($description): ?>
                <div class="gallery-hero__description description">
                    <?= wp_kses_post($description); ?>
                </div>
            <?php endif; ?>

            <?php if ($dekoracje || !get_field('seo_content')): ?>
            <a href="#galeria" class="gallery-hero__button btn btn--secondary">
                Zobacz galerię
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <polyline points="19 12 12 19 5 12"/>
                </svg>
            </a>
            <?php endif; ?>
        </div>

        <div class="gallery-hero__wave" aria-hidden="true">
            <svg class="gallery-hero__wave-layer gallery-hero__wave-layer--1" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z"></path>
            </svg>
            <svg class="gallery-hero__wave-layer gallery-hero__wave-layer--2" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z"></path>
            </svg>
            <svg class="gallery-hero__wave-layer gallery-hero__wave-layer--3" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z"></path>
            </svg>
        </div>
    </section>

    <?php
    // Pusta galeria z napisem "Brak dekoracji" psuje stronę, która ma już opis usługi - wtedy ją pomijamy
    $hide_empty_gallery = !$dekoracje && get_field('seo_content');
    ?>
    <?php if (!$hide_empty_gallery): ?>
    <!-- Gallery Section -->
    <section id="galeria" class="gallery">
        <div class="container">
            <?php if ($dekoracje): ?>
                <div class="gallery__grid">
                    <?php foreach ($dekoracje as $index => $dekoracja):
                        $thumbnail_id = get_post_thumbnail_id($dekoracja->ID);
                        if (!$thumbnail_id) continue;

                        // Pobierz różne rozmiary
                        $thumb_url  = wp_get_attachment_image_url($thumbnail_id, 'thumbnail');
                        $medium_url = wp_get_attachment_image_url($thumbnail_id, 'medium_large');
                        $full_url   = wp_get_attachment_image_url($thumbnail_id, 'full');
                        $alt        = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true) ?: $dekoracja->post_title;
                        // Tytuły dekoracji to nazwy plików (godan-decoration-0501, IMG_1234, hash) - nic nie mówią Google
                        if (preg_match('/^(godan-decoration|img|dsc|[0-9a-f]{8}-)[\w-]*$/i', $alt)) {
                            $alt = sprintf('%s – dekoracje balonowe Godan Siedlce, realizacja %d', $title, $index + 1);
                        }
                    ?>
                        <div class="gallery__item" data-index="<?= $index; ?>">
                            <div class="gallery__item-inner">
                                <!-- Placeholder blur -->
                                <img
                                    src="<?= esc_url($thumb_url); ?>"
                                    alt=""
                                    class="gallery__placeholder"
                                    aria-hidden="true"
                                >
                                <!-- Main image (lazy loaded) -->
                                <img
                                    src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                    data-src="<?= esc_url($medium_url); ?>"
                                    data-full="<?= esc_url($full_url); ?>"
                                    alt="<?= esc_attr($alt); ?>"
                                    class="gallery__image"
                                    loading="lazy"
                                >
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="gallery__empty">
                    <p>Brak dekoracji w tej kategorii.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php
    // Opis usługi i FAQ pod galerią - sekcje pojawiają się tylko gdy pola są wypełnione
    $seo_headline = get_field('seo_headline');
    $seo_content  = get_field('seo_content');
    $seo_faq      = get_field('seo_faq');
    ?>
    <?php if ($seo_content): ?>
        <section class="gallery-content<?= $hide_empty_gallery ? ' gallery-content--standalone' : ''; ?>">
            <div class="gallery-content__container container">
                <?php if ($seo_headline): ?>
                    <h2 class="gallery-content__headline headline"><?= esc_html($seo_headline); ?></h2>
                <?php endif; ?>
                <div class="gallery-content__body">
                    <?= wp_kses_post($seo_content); ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($seo_faq):
        $module = [
            'tagline'   => 'FAQ',
            'headline'  => 'Najczęstsze pytania',
            'faq_items' => $seo_faq,
        ];
        include get_template_directory() . '/modules/faq.php';
    endif; ?>

    <!-- Lightbox -->
    <div class="gallery__lightbox" aria-hidden="true">
        <button class="gallery__lightbox-close" aria-label="Zamknij" type="button">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
        <button class="gallery__lightbox-prev" aria-label="Poprzednie" type="button">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </button>
        <button class="gallery__lightbox-next" aria-label="Następne" type="button">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </button>
        <div class="gallery__lightbox-content">
            <!-- Placeholder (medium) - pokazuje się od razu -->
            <img src="" alt="" class="gallery__lightbox-placeholder">
            <!-- Full quality - ładuje się w tle -->
            <img src="" alt="" class="gallery__lightbox-image">
        </div>
        <div class="gallery__lightbox-counter"></div>
    </div>
</main>

<script>
(function() {
    // Progressive Image Loading
    const galleryItems = document.querySelectorAll('.gallery__item');

    if (!galleryItems.length) return;

    const imageObserver = new IntersectionObserver(function(entries, observer) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                const item = entry.target;
                const image = item.querySelector('.gallery__image');
                const src = image.dataset.src;

                if (src) {
                    image.src = src;
                    image.onload = function() {
                        item.classList.add('is-loaded');
                    };
                }

                observer.unobserve(item);
            }
        });
    }, {
        rootMargin: '100px'
    });

    galleryItems.forEach(function(item) {
        imageObserver.observe(item);
    });

    // Lightbox
    const lightbox = document.querySelector('.gallery__lightbox');
    const lightboxContent = lightbox.querySelector('.gallery__lightbox-content');
    const lightboxPlaceholder = lightbox.querySelector('.gallery__lightbox-placeholder');
    const lightboxImage = lightbox.querySelector('.gallery__lightbox-image');
    const lightboxCounter = lightbox.querySelector('.gallery__lightbox-counter');
    const closeBtn = lightbox.querySelector('.gallery__lightbox-close');
    const prevBtn = lightbox.querySelector('.gallery__lightbox-prev');
    const nextBtn = lightbox.querySelector('.gallery__lightbox-next');

    let currentIndex = 0;
    let updateTimeout = null;
    const images = [];
    const loadedFullImages = {};

    // Zbierz wszystkie obrazki (medium + full)
    galleryItems.forEach(function(item, index) {
        const img = item.querySelector('.gallery__image');
        images.push({
            medium: img.dataset.src,
            full: img.dataset.full,
            alt: img.alt
        });

        item.addEventListener('click', function() {
            openLightbox(index);
        });
    });

    function openLightbox(index) {
        currentIndex = index;
        updateLightbox();
        lightbox.classList.add('is-active');
        document.body.style.overflow = 'hidden';
        lightbox.setAttribute('aria-hidden', 'false');
    }

    function closeLightbox() {
        lightbox.classList.remove('is-active');
        document.body.style.overflow = '';
        lightbox.setAttribute('aria-hidden', 'true');
    }

    function updateLightbox() {
        const image = images[currentIndex];
        const targetIndex = currentIndex;

        // Anuluj poprzedni timeout
        if (updateTimeout) {
            clearTimeout(updateTimeout);
        }

        // Reset stanu - ukryj pełny obrazek
        lightboxContent.classList.remove('is-loaded');
        lightboxPlaceholder.classList.add('is-changing');

        // Preload medium image ZANIM zmienimy src
        const preloadMedium = new Image();
        preloadMedium.onload = function() {
            // Sprawdź czy użytkownik nie zmienił zdjęcia
            if (currentIndex !== targetIndex) return;

            // Dopiero teraz ustaw src (obraz jest już w cache przeglądarki)
            lightboxPlaceholder.src = image.medium;
            lightboxPlaceholder.alt = image.alt;
            lightboxPlaceholder.classList.remove('is-changing');

            // Teraz załaduj full
            if (loadedFullImages[targetIndex]) {
                lightboxImage.src = image.full;
                lightboxImage.alt = image.alt;
                lightboxContent.classList.add('is-loaded');
            } else {
                const fullImg = new Image();
                fullImg.onload = function() {
                    if (currentIndex === targetIndex) {
                        lightboxImage.src = image.full;
                        lightboxImage.alt = image.alt;
                        lightboxContent.classList.add('is-loaded');
                    }
                    loadedFullImages[targetIndex] = true;
                };
                fullImg.src = image.full;
            }
        };
        preloadMedium.src = image.medium;

        lightboxCounter.textContent = (currentIndex + 1) + ' / ' + images.length;

        prevBtn.style.display = images.length > 1 ? '' : 'none';
        nextBtn.style.display = images.length > 1 ? '' : 'none';
    }

    function showPrev() {
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        updateLightbox();
    }

    function showNext() {
        currentIndex = (currentIndex + 1) % images.length;
        updateLightbox();
    }

    closeBtn.addEventListener('click', closeLightbox);
    prevBtn.addEventListener('click', showPrev);
    nextBtn.addEventListener('click', showNext);

    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) {
            closeLightbox();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (!lightbox.classList.contains('is-active')) return;

        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') showPrev();
        if (e.key === 'ArrowRight') showNext();
    });

    // Swipe usunięty - używamy tylko przycisków prev/next
    // Pinch-to-zoom działa natywnie dzięki touch-action: pinch-zoom na obrazku
})();
</script>

<?php get_footer(); ?>
