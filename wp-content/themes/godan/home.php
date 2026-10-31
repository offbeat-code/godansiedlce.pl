<?php
/**
 * Template: Blog Archive
 *
 * @package Godan
 */

get_header();

$blog_page_id = get_option('page_for_posts');
$headline     = get_the_title($blog_page_id);

get_template_part('template-parts/section-hero', null, ['title' => $headline]);
?>

<main id="primary" class="site-main">
    <section class="blog-archive">
        <div class="container">
            <div class="blog-archive__inner">

                <?php if (have_posts()): ?>
                    <div class="blog-archive__grid">
                        <?php while (have_posts()): the_post();
                            $categories   = get_the_category();
                            $category     = !empty($categories) ? $categories[0]->name : '';
                            $thumbnail_id = get_post_thumbnail_id();
                            $thumb_url    = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'thumbnail') : '';
                            $main_url     = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'medium_large') : '';
                        ?>
                            <a href="<?= get_permalink(); ?>" class="blog-archive__card">
                                <div class="blog-archive__card-image-wrapper">
                                    <?php if ($main_url): ?>
                                        <img
                                            src="<?= esc_url($thumb_url); ?>"
                                            alt=""
                                            class="blog-archive__card-placeholder"
                                            aria-hidden="true"
                                        >
                                        <img
                                            src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"
                                            data-src="<?= esc_url($main_url); ?>"
                                            alt="<?= esc_attr(get_the_title()); ?>"
                                            class="blog-archive__card-image"
                                        >
                                    <?php endif; ?>
                                    <?php if ($category): ?>
                                        <span class="blog-archive__card-category"><?= esc_html($category); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="blog-archive__card-content">
                                    <span class="blog-archive__card-date">
                                        <i data-lucide="calendar"></i>
                                        <?= get_the_date('d.m.Y'); ?>
                                    </span>
                                    <h3 class="blog-archive__card-title"><?= esc_html(get_the_title()); ?></h3>
                                    <p class="blog-archive__card-excerpt"><?= esc_html(wp_trim_words(get_the_excerpt(), 20)); ?></p>
                                    <span class="blog-archive__card-link">
                                        Czytaj więcej
                                        <i data-lucide="arrow-right"></i>
                                    </span>
                                </div>
                            </a>
                        <?php endwhile; ?>
                    </div>

                    <?php
                    $total_pages = $GLOBALS['wp_query']->max_num_pages;
                    if ($total_pages > 1):
                    ?>
                        <nav class="blog-archive__pagination">
                            <?php
                            echo paginate_links(array(
                                'total'     => $total_pages,
                                'current'   => max(1, get_query_var('paged')),
                                'prev_text' => '<i data-lucide="chevron-left"></i>',
                                'next_text' => '<i data-lucide="arrow-right"></i>',
                            ));
                            ?>
                        </nav>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="blog-archive__empty">
                        <p>Brak wpisów do wyświetlenia.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<script>
(function() {
    const cards = document.querySelectorAll('.blog-archive__card');
    if (!cards.length) return;

    const observer = new IntersectionObserver(function(entries, obs) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                const card = entry.target;
                const img = card.querySelector('.blog-archive__card-image');
                if (!img) return;

                const src = img.dataset.src;
                if (src) {
                    img.src = src;
                    img.onload = function() {
                        card.classList.add('is-loaded');
                    };
                }
                obs.unobserve(card);
            }
        });
    }, { rootMargin: '100px' });

    cards.forEach(function(card) {
        observer.observe(card);
    });
})();
</script>

<?php get_footer(); ?>
