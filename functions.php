<?php
global $more;    // Declare global $more (before the loop).
$more = 0;       // Set (inside the loop) to display content above the more tag.
	
define('TMPLT_DIR', get_template_directory_uri());

if (!function_exists('theme_name_setup')) {
	function theme_name_setup() {
		// Post format support
		add_theme_support('post-formats', array(
			// 'aside', // similar to FB note update
			// 'gallery',
			// 'image', // a single image
			// 'quote',
			// 'status', // Twitter-like update
			'video',
			'audio',
			// 'chat' // chat transcript
		));

		// Post thumbnail support
		add_theme_support('post-thumbnails');
		
		// nav menus
		register_nav_menus(
			array(
				'primary-menu' => __('Main Menu','text-domain'),
				// 'footer-menu' => __('Footer Menu''text-domain')
			)
		);

		// sidebar
		register_sidebar(array(
			'name'          => __('Default Sidebar', 'text-domain'),
			'id'            => 'sidebar',
			'description'   => 'Sidebar on all other pages.',
			'class'         => 'default-sidebar',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="title">',
			'after_title'   => '</h3>'
		));
	}
}
add_action('after_setup_theme', 'theme_name_setup');

function add_toolbar_items($admin_bar){
	$admin_bar->add_menu(array(
			'id'    => 'mytheme-options',
			'title' => 'mytheme Theme Options',
			'href'  => admin_url('admin.php?page=mytheme-options'),
			'parent' => 'site-name',
			'meta'  => array(
					'title' => __('My Theme Options','text-domain'),
			),
	));
}
add_action('admin_bar_menu', 'add_toolbar_items', 100);

function add_first_and_last($output) {
  $output = preg_replace('/class="menu-item/', 'class="first-menu-item menu-item', $output, 1);
  $output = substr_replace($output, 'class="last-menu-item menu-item', strripos($output, 'class="menu-item'), strlen('class="menu-item'));
  return $output;
}
add_filter('wp_nav_menu', 'add_first_and_last');

function custom_excerpt($text = '') {
	$raw_excerpt = $text;
	if ('' == $text) {
		$text = get_the_content('');
		// $text = strip_shortcodes( $text );
		$text = do_shortcode( $text );
		$text = apply_filters('the_content', $text);
		$text = str_replace(']]>', ']]>', $text);
		$excerpt_length = apply_filters('excerpt_length', 55);
		$excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
		$text = wp_trim_words( $text, $excerpt_length, $excerpt_more );
	}
	return apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
}
remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'custom_excerpt');

if (!function_exists('theme_name_css')) {
	function theme_name_css() {
		// wp_enqueue_style($handle, TMPLT_DIR.'/css/.css', array(), $ver, $media);
	}
}
add_action('wp_enqueue_scripts','theme_name_css');

if (!function_exists('theme_name_js')) {
	function theme_name_js() {
		// wp_enqueue_script($handle, TMPLT_DIR.'js/.js', array('jquery'), $ver, true);
	}
}
add_action('wp_enqueue_scripts', 'theme_name_js');

// Register Custom Navigation Walker
require_once('wp_bootstrap_navwalker.php');

require_once 'class.themeoptions.php';
?>