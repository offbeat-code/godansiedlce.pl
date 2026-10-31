<?php
/**
 * Plugin Name: Godan Hardening
 * Description: XML-RPC off, no public user enumeration, security headers, revisions limit.
 */

defined('ABSPATH') || exit;

// xmlrpc.php defines XMLRPC_REQUEST before loading WP, so it can be cut off here entirely
if (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) {
    status_header(403);
    exit;
}

add_filter('xmlrpc_enabled', '__return_false');

/**
 * Hide /wp/v2/users from anonymous visitors (it exposes login slugs).
 */
add_filter('rest_endpoints', function ($endpoints) {
    if (is_user_logged_in()) {
        return $endpoints;
    }
    unset($endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)']);
    return $endpoints;
});

/**
 * Block ?author=N scans that redirect to /author/<login>/.
 */
add_action('template_redirect', function () {
    if (!is_admin() && isset($_GET['author']) && !is_user_logged_in()) {
        wp_safe_redirect(home_url('/'), 301);
        exit;
    }
});

add_action('send_headers', function () {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    if (is_ssl()) {
        header('Strict-Transport-Security: max-age=31536000');
    }
});

add_filter('wp_revisions_to_keep', function () {
    return 5;
});
