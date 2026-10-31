<?php
/**
 * Template: 404 Error Page
 *
 * @package Godan
 */

get_header();

get_template_part('template-parts/section-hero', null, ['title' => 'Strona nie znaleziona']);
?>

<main id="primary" class="site-main">
    <section class="error-404">
        <div class="error-404__balloon error-404__balloon--1">
            <svg viewBox="0 0 100 140" fill="none" xmlns="http://www.w3.org/2000/svg">
                <ellipse cx="50" cy="45" rx="40" ry="45" fill="url(#balloon404Grad1)"/>
                <ellipse cx="50" cy="45" rx="40" ry="45" fill="url(#balloon404Shine1)" opacity="0.6"/>
                <path d="M50 90 L46 100 L54 100 Z" fill="url(#balloon404Grad1)"/>
                <path d="M50 100 Q45 110 50 120 Q55 130 50 140" stroke="#CBD5E1" stroke-width="2" fill="none"/>
                <ellipse cx="35" cy="30" rx="8" ry="12" fill="white" opacity="0.3" transform="rotate(-20 35 30)"/>
                <defs>
                    <linearGradient id="balloon404Grad1" x1="10" y1="0" x2="90" y2="90" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#FDBA74"/>
                        <stop offset="100%" stop-color="#F97316"/>
                    </linearGradient>
                    <radialGradient id="balloon404Shine1" cx="0.3" cy="0.3" r="0.7">
                        <stop offset="0%" stop-color="white" stop-opacity="0.4"/>
                        <stop offset="100%" stop-color="white" stop-opacity="0"/>
                    </radialGradient>
                </defs>
            </svg>
        </div>

        <div class="error-404__balloon error-404__balloon--2">
            <svg viewBox="0 0 100 140" fill="none" xmlns="http://www.w3.org/2000/svg">
                <ellipse cx="50" cy="45" rx="40" ry="45" fill="url(#balloon404Grad2)"/>
                <ellipse cx="50" cy="45" rx="40" ry="45" fill="url(#balloon404Shine2)" opacity="0.6"/>
                <path d="M50 90 L46 100 L54 100 Z" fill="url(#balloon404Grad2)"/>
                <path d="M50 100 Q45 110 50 120 Q55 130 50 140" stroke="#CBD5E1" stroke-width="2" fill="none"/>
                <ellipse cx="35" cy="30" rx="8" ry="12" fill="white" opacity="0.3" transform="rotate(-20 35 30)"/>
                <defs>
                    <linearGradient id="balloon404Grad2" x1="10" y1="0" x2="90" y2="90" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#C4B5FD"/>
                        <stop offset="100%" stop-color="#8B5CF6"/>
                    </linearGradient>
                    <radialGradient id="balloon404Shine2" cx="0.3" cy="0.3" r="0.7">
                        <stop offset="0%" stop-color="white" stop-opacity="0.4"/>
                        <stop offset="100%" stop-color="white" stop-opacity="0"/>
                    </radialGradient>
                </defs>
            </svg>
        </div>

        <div class="error-404__balloon error-404__balloon--3">
            <svg viewBox="0 0 100 140" fill="none" xmlns="http://www.w3.org/2000/svg">
                <ellipse cx="50" cy="45" rx="40" ry="45" fill="url(#balloon404Grad3)"/>
                <ellipse cx="50" cy="45" rx="40" ry="45" fill="url(#balloon404Shine3)" opacity="0.6"/>
                <path d="M50 90 L46 100 L54 100 Z" fill="url(#balloon404Grad3)"/>
                <path d="M50 100 Q45 110 50 120 Q55 130 50 140" stroke="#CBD5E1" stroke-width="2" fill="none"/>
                <ellipse cx="35" cy="30" rx="8" ry="12" fill="white" opacity="0.3" transform="rotate(-20 35 30)"/>
                <defs>
                    <linearGradient id="balloon404Grad3" x1="10" y1="0" x2="90" y2="90" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#FDA4AF"/>
                        <stop offset="100%" stop-color="#F43F5E"/>
                    </linearGradient>
                    <radialGradient id="balloon404Shine3" cx="0.3" cy="0.3" r="0.7">
                        <stop offset="0%" stop-color="white" stop-opacity="0.4"/>
                        <stop offset="100%" stop-color="white" stop-opacity="0"/>
                    </radialGradient>
                </defs>
            </svg>
        </div>

        <div class="container">
            <div class="error-404__content">
                <span class="error-404__code">404</span>
                <h2 class="error-404__title">Ups! Ta strona odleciała...</h2>
                <p class="error-404__description">
                    Wygląda na to, że strona której szukasz pofrunęła jak balon.<br>
                    Spokojnie - wróć na stronę główną i znajdź to czego szukasz!
                </p>
                <a href="<?= esc_url(home_url('/')) ?>" class="error-404__button">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    Wróć na stronę główną
                </a>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();
