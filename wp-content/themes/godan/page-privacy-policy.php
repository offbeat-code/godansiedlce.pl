<?php
/**
 * Template Name: Polityka prywatności
 *
 * Szablon dla stron tekstowych (polityka prywatności, regulamin, itp.)
 * Wyświetla tytuł strony i treść z edytora WordPress.
 *
 * @package Godan
 */

get_header();

get_template_part('template-parts/section-hero', null, ['title' => get_the_title()]);
?>

<main id="primary" class="site-main">
    <article class="page-privacy-policy">
        <div class="container">
            <div class="page-privacy-policy__content">
                <?php
                while (have_posts()) :
                    the_post();
                    the_content();
                endwhile;
                ?>
            </div>
        </div>
    </article>
</main>

<?php
get_footer();
