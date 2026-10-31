<?php
/**
 * Module: Blog
 *
 * @package Godan
 */

$id_module   = $module['id_module'] ?? '';
$tagline     = $module['tagline'] ?? '';
$headline    = $module['headline'] ?? '';
$description = $module['description'] ?? '';
$posts       = $module['posts'] ?? [];

$total_posts = wp_count_posts('post')->publish;
$blog_url    = get_permalink(get_option('page_for_posts'));
?>

<section <?= $id_module ? 'id="' . esc_attr($id_module) . '" ' : ''; ?>class="blog-section">
    <div class="container">
        <div class="blog-section__inner">
            <div class="blog-section__header">
                <?php if ($tagline): ?>
                    <span class="blog-section__tagline tagline"><?= esc_html($tagline); ?></span>
                <?php endif; ?>

                <?php if ($headline): ?>
                    <h2 class="blog-section__headline headline"><?= esc_html($headline); ?></h2>
                <?php endif; ?>

                <?php if ($description): ?>
                    <div class="blog-section__description description"><?= wp_kses_post($description); ?></div>
                <?php endif; ?>
            </div>

            <?php if ($posts): ?>
                <div class="blog-section__grid">
                    <?php foreach ($posts as $post):
                        setup_postdata($post);
                        $categories = get_the_category($post->ID);
                        $category   = !empty($categories) ? $categories[0]->name : '';
                        $thumb_id   = get_post_thumbnail_id($post->ID);
                        $thumb_url  = $thumb_id ? get_the_post_thumbnail_url($post->ID, 'thumbnail') : '';
                        $main_url   = $thumb_id ? get_the_post_thumbnail_url($post->ID, 'medium_large') : '';
                        $alt        = get_the_title($post->ID);
                    ?>
                        <a href="<?= get_permalink($post->ID); ?>" class="blog-section__card">
                            <div class="blog-section__card-image-wrapper">
                                <?php if ($thumb_url): ?>
                                    <img
                                        src="<?= esc_url($thumb_url); ?>"
                                        alt=""
                                        class="blog-section__card-placeholder"
                                        aria-hidden="true"
                                    >
                                    <img
                                        src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                        data-src="<?= esc_url($main_url); ?>"
                                        alt="<?= esc_attr($alt); ?>"
                                        class="blog-section__card-image"
                                    >
                                <?php endif; ?>
                                <?php if ($category): ?>
                                    <span class="blog-section__card-category"><?= esc_html($category); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="blog-section__card-content">
                                <span class="blog-section__card-date">
                                    <i data-lucide="calendar"></i>
                                    <?= get_the_date('d.m.Y', $post->ID); ?>
                                </span>
                                <h3 class="blog-section__card-title"><?= esc_html(get_the_title($post->ID)); ?></h3>
                                <p class="blog-section__card-excerpt"><?= esc_html(wp_trim_words(get_the_excerpt($post->ID), 15)); ?></p>
                                <span class="blog-section__card-link">
                                    Czytaj więcej
                                    <i data-lucide="arrow-right"></i>
                                </span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
            <?php endif; ?>

            <?php if ($total_posts > 3 && $blog_url): ?>
                <div class="blog-section__cta">
                    <a href="<?= esc_url($blog_url); ?>" class="btn btn--primary">
                        Zobacz więcej
                        <i data-lucide="arrow-right"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
(function() {
    const cards = document.querySelectorAll('.blog-section__card');
    if (!cards.length) return;

    const observer = new IntersectionObserver(function(entries, obs) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                const wrapper = entry.target.querySelector('.blog-section__card-image-wrapper');
                const img = wrapper.querySelector('.blog-section__card-image');
                const src = img ? img.dataset.src : null;

                if (src) {
                    img.src = src;
                    img.onload = function() {
                        wrapper.classList.add('is-loaded');
                    };
                }
                obs.unobserve(entry.target);
            }
        });
    }, { rootMargin: '100px' });

    cards.forEach(function(card) {
        observer.observe(card);
    });
})();
</script>
