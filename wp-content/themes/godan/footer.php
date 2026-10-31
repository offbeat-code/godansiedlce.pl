<?php
/**
 * Footer
 *
 * @package Godan
 */

$brand         = get_field('brand', 'global_content') ?? [];
$logo          = $brand['logo'] ?? null;
$description   = $brand['description'] ?? '';
$social_links  = $brand['social_links'] ?? [];

$opening_hours = get_field('opening_hours', 'global_content') ?? [];
$iframe_map    = get_field('iframe_map', 'global_content') ?? '';
$map_link      = get_field('link_map', 'global_content') ?? '';
$copyright     = get_field('copyright', 'global_content') ?? '';

$days = [
    'monday'    => 'Poniedziałek',
    'tuesday'   => 'Wtorek',
    'wednesday' => 'Środa',
    'thursday'  => 'Czwartek',
    'friday'    => 'Piątek',
    'saturday'  => 'Sobota',
    'sunday'    => 'Niedziela',
];

$social_icons = [
    'facebook'  => '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
    'instagram' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>',
];
?>

<footer id="colophon" class="footer">
    <div class="footer__wave">
        <svg viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z"></path>
        </svg>
    </div>

    <div class="footer__particles">
        <div class="footer__particle footer__particle--1"></div>
        <div class="footer__particle footer__particle--2"></div>
        <div class="footer__particle footer__particle--3"></div>
        <div class="footer__particle footer__particle--4"></div>
    </div>

    <div class="footer__content">
        <div class="container">
            <div class="footer__grid">
                <div class="footer__about">
                    <?php if ($logo): ?>
                        <img src="<?= esc_url($logo['url']); ?>" alt="<?= esc_attr($logo['alt'] ?: 'Logo'); ?>" class="footer__logo">
                    <?php endif; ?>

                    <?php if ($description): ?>
                        <p class="footer__description"><?= esc_html($description); ?></p>
                    <?php endif; ?>

                    <?php if ($social_links): ?>
                        <div class="footer__social">
                            <?php foreach ($social_links as $social):
                                $type = $social['type'] ?? '';
                                $url  = $social['url'] ?? '';
                                $icon = $social_icons[$type] ?? '';

                                if (!$url || !$icon) continue;
                                ?>
                                <a href="<?= esc_url($url); ?>" target="_blank" rel="noopener noreferrer" class="footer__social-link" aria-label="<?= esc_attr(ucfirst($type)); ?>">
                                    <?= $icon; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="footer__column footer__column--menu">
                    <h3 class="footer__heading">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
                        Menu
                    </h3>
                    <nav class="footer__nav">
                        <?php
                        wp_nav_menu([
                            'theme_location' => 'secondary',
                            'container'      => '',
                            'menu_class'     => 'footer__list',
                            'menu_id'        => '',
                            'fallback_cb'    => false,
                        ]);
                        ?>
                    </nav>
                </div>

                <div class="footer__column footer__column--hours">
                    <h3 class="footer__heading">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Godziny
                    </h3>
                    <div class="footer__hours">
                        <?php foreach ($days as $key => $label):
                            $hours = $opening_hours[$key] ?? '';
                            if (!$hours) continue;
                            $is_closed_day = stripos($hours, 'zamknięte') !== false || stripos($hours, 'zamkniete') !== false;

                            $classes = 'footer__hours-item';
                            if ($is_closed_day) $classes .= ' footer__hours-item--closed';
                            ?>
                            <div class="<?= $classes; ?>" data-day="<?= esc_attr($key); ?>">
                                <span class="footer__hours-day"><?= esc_html($label); ?></span>
                                <span class="footer__hours-time"><?= esc_html($hours); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if ($iframe_map): ?>
                    <div class="footer__column footer__column--map">
                        <h3 class="footer__heading">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            Lokalizacja
                        </h3>
                        <div class="footer__map-wrapper">
                            <?php
                            $iframe_clean = strip_tags($iframe_map, '<iframe>');
                            $iframe_with_class = str_replace('<iframe', '<iframe class="footer__map-iframe"', $iframe_clean);
                            echo $iframe_with_class;

                            if ($map_link): ?>
                                <a href="<?= esc_url($map_link); ?>" target="_blank" rel="noopener noreferrer" class="footer__map-overlay" aria-label="Otwórz w Google Maps">
                                    <span class="footer__map-overlay-text">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        Otwórz w Google Maps
                                    </span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($copyright): ?>
        <div class="footer__bottom">
            <div class="container">
                <p class="footer__copyright"><?= esc_html($copyright); ?></p>
            </div>
        </div>
    <?php endif; ?>
</footer>
</div>

<?php
include get_template_directory() . '/inc/messenger.php';
wp_footer();
?>

<script data-no-optimize="1">
    (function() {
        var dayMap = {0:'sunday',1:'monday',2:'tuesday',3:'wednesday',4:'thursday',5:'friday',6:'saturday'};
        var now = new Date(new Date().toLocaleString('en-US', {timeZone: 'Europe/Warsaw'}));
        var today = dayMap[now.getDay()];
        var currentTime = ('0'+now.getHours()).slice(-2) + ':' + ('0'+now.getMinutes()).slice(-2);

        var items = document.querySelectorAll('.footer__hours-item[data-day]');
        items.forEach(function(item) {
            if (item.getAttribute('data-day') !== today) return;

            item.classList.add('footer__hours-item--today');

            var timeEl = item.querySelector('.footer__hours-time');
            if (!timeEl) return;
            var text = timeEl.textContent || '';

            if (/zamkni[eę]te/i.test(text)) {
                item.classList.add('footer__hours-item--closed-now');
                return;
            }

            var match = text.match(/(\d{1,2})[.:](\d{2})[^\d]+(\d{1,2})[.:](\d{2})/);
            if (match) {
                var open  = ('0'+parseInt(match[1],10)).slice(-2) + ':' + match[2];
                var close = ('0'+parseInt(match[3],10)).slice(-2) + ':' + match[4];
                if (currentTime >= open && currentTime < close) {
                    item.classList.add('footer__hours-item--open');
                } else {
                    item.classList.add('footer__hours-item--closed-now');
                }
            }
        });
    })();
</script>

</body>
</html>