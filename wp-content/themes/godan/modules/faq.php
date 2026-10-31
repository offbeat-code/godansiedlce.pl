<?php
/**
 * Module: FAQ
 *
 * @package Godan
 */

$id_module = $module['id_module'] ?? '';
$tagline   = $module['tagline'] ?? '';
$headline  = $module['headline'] ?? '';
$faq_items = $module['faq_items'] ?? [];
?>

<section <?= $id_module ? 'id="' . esc_attr($id_module) . '" ' : ''; ?>class="faq">
    <div class="faq__balloon faq__balloon--left">
        <svg viewBox="0 0 100 140" fill="none" xmlns="http://www.w3.org/2000/svg">
            <ellipse cx="50" cy="45" rx="40" ry="45" fill="url(#faqBalloonGradLeft)"/>
            <ellipse cx="50" cy="45" rx="40" ry="45" fill="url(#faqBalloonShineLeft)" opacity="0.6"/>
            <path d="M50 90 L46 100 L54 100 Z" fill="url(#faqBalloonGradLeft)"/>
            <path d="M50 100 Q45 110 50 120 Q55 130 50 140" stroke="#CBD5E1" stroke-width="2" fill="none"/>
            <ellipse cx="35" cy="30" rx="8" ry="12" fill="white" opacity="0.3" transform="rotate(-20 35 30)"/>
            <defs>
                <linearGradient id="faqBalloonGradLeft" x1="10" y1="0" x2="90" y2="90" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#FDBA74"/>
                    <stop offset="100%" stop-color="#F97316"/>
                </linearGradient>
                <radialGradient id="faqBalloonShineLeft" cx="0.3" cy="0.3" r="0.7">
                    <stop offset="0%" stop-color="white" stop-opacity="0.4"/>
                    <stop offset="100%" stop-color="white" stop-opacity="0"/>
                </radialGradient>
            </defs>
        </svg>
    </div>

    <div class="faq__balloon faq__balloon--right">
        <svg viewBox="0 0 100 140" fill="none" xmlns="http://www.w3.org/2000/svg">
            <ellipse cx="50" cy="45" rx="40" ry="45" fill="url(#faqBalloonGradRight)"/>
            <ellipse cx="50" cy="45" rx="40" ry="45" fill="url(#faqBalloonShineRight)" opacity="0.6"/>
            <path d="M50 90 L46 100 L54 100 Z" fill="url(#faqBalloonGradRight)"/>
            <path d="M50 100 Q45 110 50 120 Q55 130 50 140" stroke="#CBD5E1" stroke-width="2" fill="none"/>
            <ellipse cx="35" cy="30" rx="8" ry="12" fill="white" opacity="0.3" transform="rotate(-20 35 30)"/>
            <defs>
                <linearGradient id="faqBalloonGradRight" x1="10" y1="0" x2="90" y2="90" gradientUnits="userSpaceOnUse">
                    <stop offset="0%" stop-color="#C4B5FD"/>
                    <stop offset="100%" stop-color="#8B5CF6"/>
                </linearGradient>
                <radialGradient id="faqBalloonShineRight" cx="0.3" cy="0.3" r="0.7">
                    <stop offset="0%" stop-color="white" stop-opacity="0.4"/>
                    <stop offset="100%" stop-color="white" stop-opacity="0"/>
                </radialGradient>
            </defs>
        </svg>
    </div>

    <div class="container">
        <div class="faq__header">
            <?php if ($tagline): ?>
                <span class="faq__tagline tagline"><?= esc_html($tagline); ?></span>
            <?php endif; ?>

            <?php if ($headline): ?>
                <h2 class="faq__headline headline"><?= esc_html($headline); ?></h2>
            <?php endif; ?>
        </div>

        <?php if ($faq_items): ?>
            <div class="faq__grid">
                <?php foreach ($faq_items as $item):
                    $question = $item['question'] ?? '';
                    $answer   = $item['answer'] ?? '';

                    if (!$question || !$answer) continue;
                ?>
                    <details class="faq__item">
                        <summary class="faq__question">
                            <?= esc_html($question); ?>
                            <i class="faq__icon" data-lucide="chevron-down"></i>
                        </summary>
                        <div class="faq__answer-wrapper">
                            <div class="faq__answer">
                                <p><?= esc_html($answer); ?></p>
                            </div>
                        </div>
                    </details>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
(function() {
    document.querySelectorAll('.faq__item').forEach(function(details) {
        var summary = details.querySelector('.faq__question');
        var content = details.querySelector('.faq__answer-wrapper');

        if (!summary || !content) return;

        if (!details.open) {
            content.style.gridTemplateRows = '0fr';
        } else {
            content.style.gridTemplateRows = '1fr';
            summary.classList.add('is-open');
        }

        summary.addEventListener('click', function(e) {
            e.preventDefault();

            if (details.open) {
                summary.classList.remove('is-open');
                content.style.gridTemplateRows = '0fr';
                content.addEventListener('transitionend', function handler() {
                    details.open = false;
                    content.removeEventListener('transitionend', handler);
                }, { once: true });
            } else {
                details.open = true;
                summary.classList.add('is-open');
                content.offsetHeight;
                content.style.gridTemplateRows = '1fr';
            }
        });
    });
})();
</script>
