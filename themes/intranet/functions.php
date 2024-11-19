<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', 'intranet_enqueue_scripts');
function intranet_enqueue_scripts(): void {
    wp_enqueue_style('intranet', get_stylesheet_directory_uri().'/assets/css/style.css', array(), wp_get_theme()->get('Version'), 'screen');
    wp_enqueue_style('intranet-extra', get_stylesheet_directory_uri().'/assets/css/extra.css', array(), wp_get_theme()->get('Version'), 'screen');
}

add_action('after_switch_theme', 'intranet_switch_theme');

function intranet_switch_theme(): void {
	update_option('posts_per_page', 12);

	update_option('timezone_string', 'Europe/Paris');
	update_option('date_format', 'j F Y');
	update_option('time_format', 'G\hi');

	global $wp_rewrite;
	$wp_rewrite->set_permalink_structure('/%postname%/');
	$wp_rewrite->flush_rules();

	create_and_set_default_pages();
	create_new_category('Actualités', 'Les actualités du site');
}

add_action('after_setup_theme', 'intranet_theme_setup');
function intranet_theme_setup(  ): void {
	add_theme_support("title-tag");
	add_theme_support("post-thumbnails");
	add_theme_support('html5', array( 'search-form', 'style', 'script') );
}

function create_new_category($name, $description=""): void {
	$category = array(
		'cat_name' => $name,                                // Nom affiché
		'category_nicename' => sanitize_title($name),       // Slug
		'category_description' => $description,             // Description
		'category_parent' => 0,                             // 0 = catégorie parente
		'taxonomy' => 'category'                            // Type de taxonomie
	);

	if (!term_exists($name, 'category')) {
		if (!function_exists('wp_insert_category')) {
			require_once(ABSPATH . 'wp-admin/includes/taxonomy.php');
		}
		wp_create_category( $category['cat_name'], 0 );
	}

}
function create_and_set_default_pages(): void {
	// Tableau des pages à créer
	$pages = array(
		'homepage' => array(
			'title' => 'Accueil',
			'content' => 'Contenu de la page d\'accueil',
			//'template' => 'templates/homepage.php', // Optionnel: template personnalisé
		),
		'actualites' => array(
			'title' => 'Actualités',
			'content' => 'Nos dernières actualités',
		)
	);

	foreach ($pages as $slug => $page_data) {
		// Vérifier si la page existe déjà
		$existing_page = get_page_by_path($slug);

		if (!$existing_page) {
			// Création de la nouvelle page
			$page_args = array(
				'post_title'    => $page_data['title'],
				'post_content'  => $page_data['content'],
				'post_status'   => 'publish',
				'post_author'   => 1,
				'post_type'     => 'page',
				'post_name'     => $slug
			);

			// Insérer la page
			$page_id = wp_insert_post($page_args);

			// Si un template est spécifié, l'attribuer à la page
			if (!empty($page_data['template'])) {
				update_post_meta($page_id, '_wp_page_template', $page_data['template']);
			}

			// Stocker l'ID de la page pour une utilisation ultérieure
			update_option($slug . '_page_id', $page_id);
		} else {
			// Stocker l'ID de la page existante
			update_option($slug . '_page_id', $existing_page->ID);
		}
	}

	// Définir la page d'accueil et la page des articles
	$homepage_id = get_option('homepage_page_id');
	$actualites_id = get_option('actualites_page_id');

	if ($homepage_id && $actualites_id) {
		// Définir la page d'accueil statique et la page des articles
		update_option('show_on_front', 'page');
		update_option('page_on_front', $homepage_id);
		update_option('page_for_posts', $actualites_id);
	}
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

add_action('wp_head', 'add_custom_meta');

//TODO:  add_custom_meta -> temporary function
function add_custom_meta():void
{
	$author  = "webmaster";
	$post = get_post();
	if ( $post ) {
		$author  = get_the_author_meta('login', get_post()->post_author);
		echo '<meta name="post-id" content="'. get_post()->ID .'">'.PHP_EOL;
	}
	if ( !empty( $author ) ) {
		echo '<meta name="author" content="'. $author .'">'.PHP_EOL;
	}


}