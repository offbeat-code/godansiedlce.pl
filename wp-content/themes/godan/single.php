<?php
/**
 * Template: Single Post
 *
 * @package Godan
 */

get_header();

$blog_page_id = get_option('page_for_posts');
$headline     = get_the_title($blog_page_id);

get_template_part('template-parts/section-hero', null, ['title' => $headline, 'tag' => 'p']);

while (have_posts()) : the_post();
    $categories = get_the_category();
    $category   = !empty($categories) ? $categories[0]->name : '';
    $date       = get_the_date('d.m.Y');
?>

<main id="primary" class="site-main">
    <article class="single-post">
        <header class="single-post__header">
            <div class="container container--narrow">
                <div class="single-post__meta">
                    <?php if ($category): ?>
                        <span class="single-post__category"><?= esc_html($category); ?></span>
                    <?php endif; ?>
                    <span class="single-post__date">
                        <i data-lucide="calendar"></i>
                        <?= esc_html($date); ?>
                    </span>
                </div>

                <h1 class="single-post__title"><?= esc_html(get_the_title()); ?></h1>

                <?php if (has_excerpt()): ?>
                    <p class="single-post__excerpt"><?= esc_html(get_the_excerpt()); ?></p>
                <?php endif; ?>
            </div>
        </header>

        <div class="single-post__content">
            <div class="container container--narrow">
                <?php the_content(); ?>
            </div>
        </div>

        <footer class="single-post__footer">
            <div class="container container--narrow">
                <?php
                $tags = get_the_tags();
                if ($tags):
                ?>
                    <div class="single-post__tags">
                        <?php foreach ($tags as $tag): ?>
                            <a href="<?= get_tag_link($tag->term_id); ?>" class="single-post__tag"><?= esc_html($tag->name); ?></a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </footer>

        <nav class="single-post__nav">
            <div class="container">
                <div class="single-post__nav-inner">
                    <?php
                    $prev_post = get_previous_post();
                    $next_post = get_next_post();
                    ?>

                    <?php if ($prev_post): ?>
                        <a href="<?= get_permalink($prev_post); ?>" class="single-post__nav-link single-post__nav-link--prev">
                            <span class="single-post__nav-label">
                                <i data-lucide="arrow-left"></i>
                                Poprzedni wpis
                            </span>
                            <span class="single-post__nav-title"><?= esc_html($prev_post->post_title); ?></span>
                        </a>
                    <?php else: ?>
                        <div></div>
                    <?php endif; ?>

                    <?php if ($next_post): ?>
                        <a href="<?= get_permalink($next_post); ?>" class="single-post__nav-link single-post__nav-link--next">
                            <span class="single-post__nav-label">
                                Następny wpis
                                <i data-lucide="arrow-right"></i>
                            </span>
                            <span class="single-post__nav-title"><?= esc_html($next_post->post_title); ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </article>
</main>

<?php
endwhile;
get_footer();
?>
