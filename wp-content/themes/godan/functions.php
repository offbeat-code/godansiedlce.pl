<?php
/**
 * Godan functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Godan
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.5' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function godan_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on Godan, use a find and replace
		* to change 'godan' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'godan', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'primary'   => esc_html__( 'Primary', 'godan' ),
			'secondary' => esc_html__( 'Secondary', 'godan' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'godan_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'godan_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function godan_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'godan_content_width', 640 );
}
add_action( 'after_setup_theme', 'godan_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function godan_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'godan' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'godan' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'godan_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function godan_scripts() {
	// Google Fonts - Epilogue & Nunito
	wp_enqueue_style(
		'godan-google-fonts',
		'https://fonts.googleapis.com/css2?family=Epilogue:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap',
		array(),
		null
	);

	wp_enqueue_style( 'godan-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'godan-style', 'rtl', 'replace' );

	// Custom SCSS compiled CSS
	wp_enqueue_style( 'godan-main', get_template_directory_uri() . '/scss/godan.css', array(), _S_VERSION );

	// Swiper.js
	wp_enqueue_style( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.0' );
	wp_enqueue_script( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0.0', true );

	// Lucide Icons
	wp_enqueue_script( 'lucide', 'https://unpkg.com/lucide@0.469.0/dist/umd/lucide.min.js', array(), '0.469.0', true );

	wp_enqueue_script( 'godan-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'godan_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Contact form handler.
 */
require get_template_directory() . '/inc/contact-form.php';
require get_template_directory() . '/inc/contact-form-admin.php';

/**
 * Gallery order - Drag & Drop sortowanie galerii per kategoria.
 */
require get_template_directory() . '/inc/gallery-order.php';

/**
 * ACF Options Page - Zasoby strony
 */
function godan_register_acf_options_page() {
    if (!function_exists('acf_add_options_page')) {
        return;
    }

    acf_add_options_page(array(
        'page_title' => 'Zasoby strony',
        'menu_title' => 'Zasoby strony',
        'menu_slug' => 'site-resources',
        'capability' => 'edit_posts',
        'redirect' => false,
        'icon_url' => 'dashicons-open-folder',
        'position' => 30,
        'post_id' => 'global_content',
        'wpml_language_synchronization' => false
    ));
}
add_action('acf/init', 'godan_register_acf_options_page');

/**
 * Maintenance Mode
 *
 * Włącz/wyłącz tryb konserwacji zmieniając wartość GODAN_MAINTENANCE_MODE na true/false
 */
define('GODAN_MAINTENANCE_MODE', false);

function godan_maintenance_mode() {
    if (GODAN_MAINTENANCE_MODE && !current_user_can('edit_posts') && !is_admin()) {
        include get_template_directory() . '/maintenance.php';
        exit;
    }
}
add_action('template_redirect', 'godan_maintenance_mode');

/**
 * Custom Post Type: Dekoracje
 */
function godan_register_dekoracje_cpt() {
    register_post_type('dekoracje', array(
        'labels' => array(
            'name'                  => 'Dekoracje',
            'singular_name'         => 'Dekoracja',
            'menu_name'             => 'Dekoracje',
            'add_new'               => 'Dodaj nową',
            'add_new_item'          => 'Dodaj nową dekorację',
            'edit_item'             => 'Edytuj dekorację',
            'new_item'              => 'Nowa dekoracja',
            'search_items'          => 'Szukaj dekoracji',
            'not_found'             => 'Nie znaleziono dekoracji',
            'not_found_in_trash'    => 'Nie znaleziono w koszu',
            'featured_image'        => 'Zdjęcie dekoracji',
            'set_featured_image'    => 'Ustaw zdjęcie dekoracji',
            'remove_featured_image' => 'Usuń zdjęcie dekoracji',
            'use_featured_image'    => 'Użyj jako zdjęcie dekoracji',
        ),
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-images-alt2',
        'supports'           => array('title', 'thumbnail'),
        'has_archive'        => false,
        'rewrite'            => false,
    ));
}
add_action('init', 'godan_register_dekoracje_cpt');

/**
 * Custom Taxonomy: Kategoria dekoracji
 */
function godan_register_typ_dekoracji_taxonomy() {
    register_taxonomy('typ_dekoracji', 'dekoracje', array(
        'labels' => array(
            'name'              => 'Kategorie dekoracji',
            'singular_name'     => 'Kategoria dekoracji',
            'menu_name'         => 'Kategorie dekoracji',
            'all_items'         => 'Wszystkie kategorie',
            'edit_item'         => 'Edytuj kategorię',
            'view_item'         => 'Zobacz kategorię',
            'update_item'       => 'Zaktualizuj kategorię',
            'add_new_item'      => 'Dodaj nową kategorię',
            'new_item_name'     => 'Nazwa nowej kategorii',
            'search_items'      => 'Szukaj kategorii',
            'not_found'         => 'Nie znaleziono kategorii',
        ),
        'hierarchical'      => true,
        'public'            => true,
        // Dekoracje nie mają własnych stron, więc archiwa kategorii były puste ("Nothing Found") i trafiały do mapy witryny
        'publicly_queryable' => false,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => false,
        'rewrite'           => false,
    ));
}
add_action('init', 'godan_register_typ_dekoracji_taxonomy');

/**
 * Dekoracje - kolumna z miniaturką w tabeli
 */
function godan_dekoracje_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['thumbnail'] = 'Zdjęcie';
    foreach ($columns as $key => $value) {
        if ($key !== 'cb') {
            $new_columns[$key] = $value;
        }
    }
    return $new_columns;
}
add_filter('manage_dekoracje_posts_columns', 'godan_dekoracje_columns');

function godan_dekoracje_column_content($column, $post_id) {
    if ($column === 'thumbnail') {
        $thumbnail = get_the_post_thumbnail($post_id, array(60, 60));
        if ($thumbnail) {
            echo $thumbnail;
        } else {
            echo '<span style="color:#999;">Brak</span>';
        }
    }
}
add_action('manage_dekoracje_posts_custom_column', 'godan_dekoracje_column_content', 10, 2);

/**
 * Dekoracje - filtrowanie po kategoriach w panelu admina
 */
function godan_dekoracje_filter_by_taxonomy($post_type) {
    if ($post_type !== 'dekoracje') {
        return;
    }

    $taxonomy = 'typ_dekoracji';
    $selected = isset($_GET[$taxonomy]) ? sanitize_text_field($_GET[$taxonomy]) : '';

    wp_dropdown_categories(array(
        'show_option_all' => 'Wszystkie kategorie',
        'taxonomy'        => $taxonomy,
        'name'            => $taxonomy,
        'value_field'     => 'slug',
        'selected'        => $selected,
        'hierarchical'    => true,
        'show_count'      => true,
        'hide_empty'      => false,
    ));
}
add_action('restrict_manage_posts', 'godan_dekoracje_filter_by_taxonomy');

/**
 * Przekieruj archiwa kategorii bloga na stronę główną bloga
 */
function godan_redirect_category_archives() {
    if (is_category()) {
        wp_redirect(home_url('/blog/'), 301);
        exit;
    }
}
add_action('template_redirect', 'godan_redirect_category_archives');


/**
 * Zdjęcia w treści wpisów bez opisu alternatywnego dostają tytuł wpisu
 *
 * @param string $image   Tag <img>.
 * @param string $context Kontekst (the_content itp.).
 * @return string
 */
function godan_fill_empty_content_img_alt($image, $context) {
    if (!preg_match('/\salt=(""|\'\')/', $image) && preg_match('/\salt=/', $image)) {
        return $image;
    }
    $alt = esc_attr(wp_strip_all_tags(get_the_title()) . ' – Godan Siedlce');
    if (preg_match('/\salt=/', $image)) {
        return preg_replace('/\salt=(""|\'\')/', ' alt="' . $alt . '"', $image, 1);
    }
    return preg_replace('/<img\s/', '<img alt="' . $alt . '" ', $image, 1);
}
add_filter('wp_content_img_tag', 'godan_fill_empty_content_img_alt', 10, 2);

/**
 * Telefon i opis firmy w schema Organization (darmowy Yoast ich nie wypisuje, choć ma pola w ustawieniach)
 *
 * @param array $data Dane grafu Organization.
 * @return array
 */
function godan_schema_organization_contact($data) {
    $phone = function_exists('get_field') ? get_field('phone_header', 'global_content') : '';
    if ($phone && empty($data['telephone'])) {
        $data['telephone'] = $phone;
    }
    $description = class_exists('WPSEO_Options') ? WPSEO_Options::get('org-description') : '';
    if ($description && empty($data['description'])) {
        $data['description'] = $description;
    }

    // Sklep stacjonarny - dane jak w polityce prywatności i na mapie w stopce
    $data['@type']         = ['Organization', 'Store'];
    $data['alternateName'] = 'GoDan - Strefa Uśmiechu';
    $data['address']       = [
        '@type'           => 'PostalAddress',
        'streetAddress'   => 'ul. Fryderyka Chopina 3',
        'postalCode'      => '08-110',
        'addressLocality' => 'Siedlce',
        'addressCountry'  => 'PL',
    ];
    $data['geo'] = [
        '@type'     => 'GeoCoordinates',
        'latitude'  => 52.168738,
        'longitude' => 22.268154,
    ];

    $map = function_exists('get_field') ? get_field('link_map', 'global_content') : '';
    if ($map) {
        $data['hasMap'] = $map;
    }

    // Godziny z pola, które wyświetla stopka, np. "10:00–18:00"; "Zamknięte" pomijamy
    $hours = function_exists('get_field') ? (array) get_field('opening_hours', 'global_content') : [];
    $specs = [];
    foreach ($hours as $day => $value) {
        if (preg_match('/(\d{1,2}:\d{2})\s*[–-]\s*(\d{1,2}:\d{2})/u', (string) $value, $m)) {
            $specs[] = [
                '@type'     => 'OpeningHoursSpecification',
                'dayOfWeek' => 'https://schema.org/' . ucfirst($day),
                'opens'     => $m[1],
                'closes'    => $m[2],
            ];
        }
    }
    if ($specs) {
        $data['openingHoursSpecification'] = $specs;
    }

    return $data;
}
add_filter('wpseo_schema_organization', 'godan_schema_organization_contact');
