<?php
/**
 * Floating Messenger Button
 *
 * @package Godan
 */

$messenger_url = get_field('messenger_url', 'global_content') ?? '';

if (!$messenger_url) return;
?>

<a href="<?= esc_url($messenger_url); ?>" target="_blank" rel="noopener noreferrer" class="fab" aria-label="Napisz na Messengerze">
    <svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2C6.36 2 2 6.13 2 11.7c0 2.91 1.19 5.44 3.14 7.17.16.13.26.35.27.57l.05 1.78c.04.57.61.94 1.13.71l1.98-.87c.17-.08.36-.1.55-.06.9.25 1.85.38 2.88.38 5.64 0 10-4.13 10-9.7C22 6.13 17.64 2 12 2zm6 7.46l-2.93 4.67c-.47.75-1.47.92-2.17.37l-2.33-1.75a.6.6 0 0 0-.72 0l-3.15 2.4c-.42.32-.97-.18-.68-.62l2.93-4.67c.47-.75 1.47-.92 2.17-.37l2.33 1.75a.6.6 0 0 0 .72 0l3.15-2.4c.42-.32.97.18.68.62z"/>
    </svg>
</a>
