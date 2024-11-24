<?php

namespace Intranet;

class Theme {

	private static ?Theme $instance = null;
	private string $name;
	private string $version;

	public static function getInstance(): ?Theme {
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __clone() {
		return false;
	}

	private function __construct() {
		$this->name = wp_get_theme()->get( 'Name' );
		$this->version = wp_get_theme()->get( 'Version' );
	}

	public function get_theme_name(): string {
		return $this->name;
	}

	public function get_theme_version(): string {
		return $this->version;
	}

	public function wp_enqueue_scripts(): void {
		wp_enqueue_style($this->get_theme_name().'-theme', get_stylesheet_directory_uri().'/assets/css/style.css', array(), $this->get_theme_version(), 'screen');
		wp_enqueue_style($this->get_theme_name().'-extra', get_stylesheet_directory_uri().'/assets/css/extra.css', array(), $this->get_theme_version(), 'screen');
	}

	public function after_setup_theme(): void {
		add_theme_support("title-tag");
		add_theme_support("post-thumbnails");
		add_theme_support('html5', array( 'search-form', 'style', 'script') );
	}

	public function wp_head(): void {
		$this->add_custom_meta();
	}

	private function add_custom_meta(): void {
		$author  = "webmaster";
		$post = get_post();
		if ( $post ) {
			$author  = get_the_author_meta('login', get_post()->post_author);
			echo '<meta name="post-id" content="'. get_post()->ID .'">'.PHP_EOL;
		}
		if ( !empty( $author ) ) {
			echo '<meta name="author" content="'. $author .'">'.PHP_EOL;
		}

		/*
		 *  <meta property="og:type" content="website" />
		 *  <meta property="og:site_name" content=" xxx " />
		 *  <meta property="og:title" value=" post title or site title " />
		 *  <meta property="og:description" value=" site description " />
		 *  <meta property="og:url" value=" blog url " />
		 *  <meta property="og:locale" content="en_GB" />
		 *  <meta property="og:image" value="post thumbnail " />
		 *  <meta property="og:image:alt" value="caption thumbnail" />
		 *  <meta property="og:image:width" value="xxx px" />
		 *  <meta property="og:image:height" value="xxx px" />
		 */



	}

	public function after_switch_theme(): void {
		update_option('posts_per_page', 12);

		update_option('timezone_string', 'Europe/Paris');
		update_option('date_format', 'j F Y');
		update_option('time_format', 'G\hi');
		update_option('rss_use_excerpt', '1');

		$this->set_permalink_structure('/%postname%/');
		$this->create_new_category('Actualités', 'Les actualités du site');

		$page_definitions = array(
			['type' => 'homepage',
			 'slug' => 'homepage',
			 'title' => 'Page d’accueil',
			 'content' => 'Contenu de la page d\'accueil'
			],
			['type' => 'blogpage',
			 'slug' => 'actualites',
			 'title' => 'Page des articles',
			 'content' => 'Dernières nouvelles'
			]
		);

		$this->create_and_set_default_pages($page_definitions);

	}

	private function set_permalink_structure($permalink_structure=""): void {
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure($permalink_structure);
		$wp_rewrite->flush_rules();
	}

	private function create_new_category($name=null, $description=""): void {
		if ($name !== null) {
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
	}

	public function upload_mimes( $mimes ) {
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}

	public function fix_svg_mime_type( $data, $file, $filename, $mimes ): array {
		$filetype = wp_check_filetype( $filename, $mimes );
		$ext = $filetype['ext'];
		$type = $filetype['type'];
		$proper_filename = $data['proper_filename'];
		return compact( 'ext', 'type', 'proper_filename' );
	}

	private function create_and_set_default_pages($page_definitions = null): void {
		if ($page_definitions === null) {
			return;
		}
		foreach ($page_definitions as $page_data) {
			$page_data = wp_parse_args($page_data, array(
				'type' => 'page',
				'slug' => '',
				'title' => '',
				'content' => '',
				'template' => '',
				'status' => 'publish',
				'author' => 1
			));

			if (empty($page_data['slug'])) {
				continue; // Sauter les entrées sans slug
			}

			// Vérifier si la page existe déjà
			$existing_page = get_page_by_path($page_data['slug']);

			if (!$existing_page) {
				$page_args = array(
					'post_title'    => $page_data['title'],
					'post_content'  => $page_data['content'],
					'post_status'   => $page_data['status'],
					'post_author'   => $page_data['author'],
					'post_type'     => 'page',
					'post_name'     => $page_data['slug']
				);

				$page_id = wp_insert_post($page_args);

				if (!empty($page_data['template'])) {
					update_post_meta($page_id, '_wp_page_template', $page_data['template']);
				}

				update_option($page_data['slug'] . '_page_id', $page_id);
			} else {
				update_option($page_data['slug'] . '_page_id', $existing_page->ID);
			}
		}

		$homepage = null;
		$blogpage = null;

		foreach ($page_definitions as $page_data) {
			if ($page_data['type'] === 'homepage') {
				$homepage = get_option($page_data['slug'] . '_page_id');
			}
			if ($page_data['type'] === 'blogpage') {
				$blogpage = get_option($page_data['slug'] . '_page_id');
			}
		}

		if ($homepage && $blogpage) {
			update_option('show_on_front', 'page');
			update_option('page_on_front', $homepage);
			update_option('page_for_posts', $blogpage);
		}
	}

	/**
	 * redirect front page and home (blog) for single and multisite
	 * @param $template
	 *
	 * @return string
	 */
	public function load_multisite_custom_template($template): string {
		if ( is_multisite() ) {
			if (is_main_site() && is_front_page()) {
				return locate_template('pages/multisite/front-page.php');
			}
			if (is_main_site() && is_home()) {
				return locate_template('pages/multisite/home.php');
			}
		}
		return $template;
	}

	public function set_default_thumbnail($html, $post_id, $post_thumbnail_id, $size, $attr) {
		if (!empty($html)) {
			return $html;
		}
		// Chemin vers l'image par défaut (à modifier selon vos besoins)
		$default_image_path = get_template_directory_uri() . '/assets/images/default-thumbnail.png';
		$default_html = sprintf(
			'<img src="%s" class="aspect-video object-cover bg-slate-200" alt="%s" />',
			esc_url($default_image_path),
			esc_attr(get_the_title($post_id))
		);

		return $default_html;


	}


}