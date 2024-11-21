<?php
if (!defined('ABSPATH'))    exit;

require_once get_stylesheet_directory() . '/classes/Theme.php';
if ( !class_exists('\Intranet\Theme') ) wp_die('Class Intranet Theme not loaded');
$theme = Intranet\Theme::getInstance();

add_action('wp_enqueue_scripts', [$theme,'wp_enqueue_scripts']);
add_action('after_setup_theme', [$theme, 'after_setup_theme']);
add_action('wp_head', [$theme,'wp_head']);
add_action('after_switch_theme', [$theme,'after_switch_theme']);

add_filter( 'upload_mimes', [$theme, 'upload_mimes'] );
add_filter( 'wp_check_filetype_and_ext', [$theme, 'fix_svg_mime_type'], 10, 4 );

add_filter('template_include', [$theme, 'load_multisite_custom_template']);
