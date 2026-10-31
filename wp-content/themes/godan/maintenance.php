<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Strona w budowie - Pralnia Godan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@400;500;600;700&family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= get_template_directory_uri() ?>/scss/godan.css">
</head>
<body class="maintenance-page">
    <main class="maintenance">
        <div class="maintenance__particles" aria-hidden="true">
            <div class="maintenance__particle maintenance__particle--1"></div>
            <div class="maintenance__particle maintenance__particle--2"></div>
            <div class="maintenance__particle maintenance__particle--3"></div>
            <div class="maintenance__particle maintenance__particle--4"></div>
        </div>

        <div class="maintenance__balloon maintenance__balloon--1" aria-hidden="true">
            <svg viewBox="0 0 100 140" fill="none" xmlns="http://www.w3.org/2000/svg">
                <ellipse cx="50" cy="45" rx="40" ry="45" fill="url(#maintGrad1)"/>
                <ellipse cx="50" cy="45" rx="40" ry="45" fill="url(#maintShine1)" opacity="0.6"/>
                <path d="M50 90 L46 100 L54 100 Z" fill="url(#maintGrad1)"/>
                <path d="M50 100 Q45 110 50 120 Q55 130 50 140" stroke="rgba(255,255,255,0.3)" stroke-width="2" fill="none"/>
                <ellipse cx="35" cy="30" rx="8" ry="12" fill="white" opacity="0.3" transform="rotate(-20 35 30)"/>
                <defs>
                    <linearGradient id="maintGrad1" x1="10" y1="0" x2="90" y2="90" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#FDBA74"/>
                        <stop offset="100%" stop-color="#F97316"/>
                    </linearGradient>
                    <radialGradient id="maintShine1" cx="0.3" cy="0.3" r="0.7">
                        <stop offset="0%" stop-color="white" stop-opacity="0.4"/>
                        <stop offset="100%" stop-color="white" stop-opacity="0"/>
                    </radialGradient>
                </defs>
            </svg>
        </div>

        <div class="maintenance__balloon maintenance__balloon--2" aria-hidden="true">
            <svg viewBox="0 0 100 140" fill="none" xmlns="http://www.w3.org/2000/svg">
                <ellipse cx="50" cy="45" rx="40" ry="45" fill="url(#maintGrad2)"/>
                <ellipse cx="50" cy="45" rx="40" ry="45" fill="url(#maintShine2)" opacity="0.6"/>
                <path d="M50 90 L46 100 L54 100 Z" fill="url(#maintGrad2)"/>
                <path d="M50 100 Q45 110 50 120 Q55 130 50 140" stroke="rgba(255,255,255,0.3)" stroke-width="2" fill="none"/>
                <ellipse cx="35" cy="30" rx="8" ry="12" fill="white" opacity="0.3" transform="rotate(-20 35 30)"/>
                <defs>
                    <linearGradient id="maintGrad2" x1="10" y1="0" x2="90" y2="90" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#E9D5FF"/>
                        <stop offset="100%" stop-color="#A855F7"/>
                    </linearGradient>
                    <radialGradient id="maintShine2" cx="0.3" cy="0.3" r="0.7">
                        <stop offset="0%" stop-color="white" stop-opacity="0.4"/>
                        <stop offset="100%" stop-color="white" stop-opacity="0"/>
                    </radialGradient>
                </defs>
            </svg>
        </div>

        <div class="maintenance__balloon maintenance__balloon--3" aria-hidden="true">
            <svg viewBox="0 0 100 140" fill="none" xmlns="http://www.w3.org/2000/svg">
                <ellipse cx="50" cy="45" rx="40" ry="45" fill="url(#maintGrad3)"/>
                <ellipse cx="50" cy="45" rx="40" ry="45" fill="url(#maintShine3)" opacity="0.6"/>
                <path d="M50 90 L46 100 L54 100 Z" fill="url(#maintGrad3)"/>
                <path d="M50 100 Q45 110 50 120 Q55 130 50 140" stroke="rgba(255,255,255,0.3)" stroke-width="2" fill="none"/>
                <ellipse cx="35" cy="30" rx="8" ry="12" fill="white" opacity="0.3" transform="rotate(-20 35 30)"/>
                <defs>
                    <linearGradient id="maintGrad3" x1="10" y1="0" x2="90" y2="90" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#FDE68A"/>
                        <stop offset="100%" stop-color="#F59E0B"/>
                    </linearGradient>
                    <radialGradient id="maintShine3" cx="0.3" cy="0.3" r="0.7">
                        <stop offset="0%" stop-color="white" stop-opacity="0.4"/>
                        <stop offset="100%" stop-color="white" stop-opacity="0"/>
                    </radialGradient>
                </defs>
            </svg>
        </div>

        <div class="maintenance__content">
            <?php
            $logo = get_field('logo_header', 'global_content');
            if ($logo):
            ?>
            <div class="maintenance__logo">
                <img src="<?= esc_url($logo['url']) ?>" alt="<?= esc_attr($logo['alt'] ?: 'Pralnia Godan') ?>">
            </div>
            <?php endif; ?>

            <h1 class="maintenance__title">Już prawie gotowe!</h1>

            <p class="maintenance__description">
                Pracujemy nad nowym wyglądem strony, który lepiej odda magię naszych dekoracji balonowych.
                Wkrótce będziecie mogli zobaczyć efekt! W międzyczasie zapraszamy do kontaktu.
            </p>

            <?php
            $phone_header = get_field('phone_header', 'global_content');
            if ($phone_header):
                $phone_clean = preg_replace('/\s+/', '', $phone_header);
            ?>
            <a href="tel:<?= esc_attr($phone_clean) ?>" class="maintenance__button">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                </svg>
                Zadzwoń: <?= esc_html($phone_header) ?>
            </a>
            <?php else: ?>
            <a href="tel:+48786332987" class="maintenance__button">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                </svg>
                Zadzwoń: +48 786 332 987
            </a>
            <?php endif; ?>
        </div>

        <div class="maintenance__wave" aria-hidden="true">
            <svg class="maintenance__wave-layer maintenance__wave-layer--1" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z"></path>
            </svg>
            <svg class="maintenance__wave-layer maintenance__wave-layer--2" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z"></path>
            </svg>
            <svg class="maintenance__wave-layer maintenance__wave-layer--3" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z"></path>
            </svg>
        </div>
    </main>
</body>
</html>
