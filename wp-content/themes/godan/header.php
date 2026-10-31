<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Godan
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'godan' ); ?></a>

    <?php
    // Get header data from ACF
    $logo = get_field('logo_header', 'global_content');
    $logo_url = $logo['url'] ?? '';
    $alt_logo = $logo['alt'] ?? 'Logo';
    $cta_link = get_field('reference_header', 'global_content');
    $phone_header = get_field('phone_header', 'global_content');
    ?>

    <header id="masthead" class="site-header">
        <nav class="nav-desktop" aria-label="<?php esc_attr_e( 'Nawigacja główna', 'godan' ); ?>">
            <div class="nav-desktop__inner container">

                <!-- LOGO -->
                <?php if ($logo_url): ?>
                    <div class="nav-desktop__logo">
                        <a href="<?= esc_url( home_url('/#') ); ?>" class="nav-desktop__logo-link">
                            <img src="<?= esc_url($logo_url); ?>"
                                 alt="<?= esc_attr($alt_logo); ?>"
                                 class="nav-desktop__logo-img">
                        </a>
                    </div>
                <?php endif; ?>

                <!-- MENU -->
                <nav class="nav-desktop__menu" aria-label="<?php esc_attr_e('Menu główne', 'godan'); ?>">
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'primary',
                        'container'      => '',
                        'menu_class'     => 'nav-desktop__menu-list',
                        'menu_id'        => '',
                        'fallback_cb'    => false,
                    ]);
                    ?>
                </nav>

                <!-- AKCJE (telefon + przycisk) -->
                <div class="nav-desktop__actions">
                    <?php if ($phone_header): ?>
                        <a href="tel:<?= esc_attr(preg_replace('/\s+/', '', $phone_header)); ?>" class="nav-desktop__phone">
                            <i data-lucide="phone"></i>
                            <?= esc_html($phone_header); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($cta_link): ?>
                        <a href="<?= esc_url($cta_link['url']); ?>"
                           class="nav-desktop__button nav-desktop__button--primary"
                           <?= !empty($cta_link['target']) ? 'target="' . esc_attr($cta_link['target']) . '"' : ''; ?>>
                            <?= esc_html($cta_link['title']); ?>
                        </a>
                    <?php endif; ?>
                </div>

            </div><!-- /nav-desktop__inner -->
        </nav>

        <!-- MOBILE NAVIGATION -->
        <nav class="nav-mobile" aria-label="<?php esc_attr_e( 'Nawigacja mobilna', 'godan' ); ?>">
            <!-- Top bar -->
            <div class="nav-mobile__bar">
                <?php if ($logo_url): ?>
                    <a href="<?= esc_url( home_url('/') ); ?>" class="nav-mobile__logo">
                        <img src="<?= esc_url($logo_url); ?>"
                             alt="<?= esc_attr($alt_logo); ?>"
                             class="nav-mobile__logo-img">
                    </a>
                <?php endif; ?>

                <button class="nav-mobile__burger" aria-label="<?php esc_attr_e('Otwórz menu', 'godan'); ?>" aria-expanded="false">
                    <svg width="24" height="24" viewBox="0 0 640 640" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M96 160C96 142.3 110.3 128 128 128L512 128C529.7 128 544 142.3 544 160C544 177.7 529.7 192 512 192L128 192C110.3 192 96 177.7 96 160zM96 320C96 302.3 110.3 288 128 288L512 288C529.7 288 544 302.3 544 320C544 337.7 529.7 352 512 352L128 352C110.3 352 96 337.7 96 320zM544 480C544 497.7 529.7 512 512 512L128 512C110.3 512 96 497.7 96 480C96 462.3 110.3 448 128 448L512 448C529.7 448 544 462.3 544 480z"/>
                    </svg>
                </button>
            </div>

            <!-- Slide-out menu -->
            <div class="nav-mobile__overlay"></div>
            <div class="nav-mobile__drawer">
                <div class="nav-mobile__drawer-header">
                    <?php if ($logo_url): ?>
                        <a href="<?= esc_url( home_url('/') ); ?>" class="nav-mobile__drawer-logo">
                            <img src="<?= esc_url($logo_url); ?>" alt="<?= esc_attr($alt_logo); ?>">
                        </a>
                    <?php endif; ?>
                    <button class="nav-mobile__close" aria-label="<?php esc_attr_e('Zamknij menu', 'godan'); ?>">
                        <svg width="24" height="24" viewBox="0 0 640 640" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M183.1 137.4C170.6 124.9 150.3 124.9 137.8 137.4C125.3 149.9 125.3 170.2 137.8 182.7L275.2 320L137.9 457.4C125.4 469.9 125.4 490.2 137.9 502.7C150.4 515.2 170.7 515.2 183.2 502.7L320.5 365.3L457.9 502.6C470.4 515.1 490.7 515.1 503.2 502.6C515.7 490.1 515.7 469.8 503.2 457.3L365.8 320L503.1 182.6C515.6 170.1 515.6 149.8 503.1 137.3C490.6 124.8 470.3 124.8 457.8 137.3L320.5 274.7L183.1 137.4z"/>
                        </svg>
                    </button>
                </div>

                <div class="nav-mobile__content">
                    <div class="nav-mobile__menu">
                        <?php
                        wp_nav_menu([
                            'theme_location' => 'primary',
                            'container'      => '',
                            'menu_class'     => 'nav-mobile__menu-list',
                            'menu_id'        => '',
                            'fallback_cb'    => false,
                        ]);
                        ?>
                    </div>

                    <?php if ($phone_header): ?>
                        <a href="tel:<?= esc_attr(preg_replace('/\s+/', '', $phone_header)); ?>" class="nav-mobile__phone">
                            <i data-lucide="phone"></i>
                            <?= esc_html($phone_header); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($cta_link): ?>
                        <a href="<?= esc_url($cta_link['url']); ?>"
                           class="nav-mobile__cta"
                           <?= !empty($cta_link['target']) ? 'target="' . esc_attr($cta_link['target']) . '"' : ''; ?>>
                            <?= esc_html($cta_link['title']); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>
