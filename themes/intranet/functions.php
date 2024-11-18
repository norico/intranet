<?php
// Empêcher l'accès direct au fichier
if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', 'intranet_enqueue_scripts');
function intranet_enqueue_scripts(): void {
    wp_enqueue_style('intranet', get_stylesheet_directory_uri().'/assets/css/style.css', array(), wp_get_theme()->get('Version'), 'screen');
    wp_enqueue_style('intranet-extra', get_stylesheet_directory_uri().'/assets/css/extra.css', array(), wp_get_theme()->get('Version'), 'screen');
}

add_action('after_setup_theme', 'intranet_theme_setup');
function intranet_theme_setup(  ): void {
	add_theme_support("title-tag");
	add_theme_support("post-thumbnails");
	add_theme_support("automatic-feed-links");
	add_theme_support("html5");
	add_theme_support("customize-selective-refresh");
	add_theme_support("custom-logo");
	add_theme_support("align-wide");
	add_theme_support("responsive-embeds");
}

add_filter( 'upload_mimes', 'allow_svg_upload' );
function allow_svg_upload( $mimes ) {
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}

add_filter( 'wp_check_filetype_and_ext', 'fix_svg_mime_type', 10, 4 );
function fix_svg_mime_type( $data, $file, $filename, $mimes ): array {
	$filetype = wp_check_filetype( $filename, $mimes );
	$ext = $filetype['ext'];
	$type = $filetype['type'];
	$proper_filename = $data['proper_filename'];
	return compact( 'ext', 'type', 'proper_filename' );
}