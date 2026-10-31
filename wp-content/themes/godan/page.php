<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Godan
 */

get_header();
?>

	<main id="primary" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();

			// ACF Flexible Content Modules
			$modules = get_field('modules');
			if ($modules) {
				foreach ($modules as $index => $module) {
					if (isset($module['module_is_disabled']) && $module['module_is_disabled']) continue;

					$path = get_template_directory() . '/modules/' . $module['acf_fc_layout'] . '.php';
					if (file_exists($path)) {
						include $path;
					} else {
						error_log(sprintf(
							'Undefined module `%s` in Flexible Content Field: %s',
							$module['acf_fc_layout'],
							PHP_EOL . __FILE__
						));
					}
				}
			}

		endwhile; // End of the loop.
		?>

	</main><!-- #main -->

<?php
get_footer();
