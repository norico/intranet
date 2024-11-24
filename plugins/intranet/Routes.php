<?php
// Empêcher l'accès direct au fichier
if (!defined('ABSPATH')) {
	exit;
}
class Routes {

	public function __construct() {
		add_action('rest_api_init', [$this, 'register_routes']);
	}

	public function register_routes(): void {
		$route_namespace = strtolower(IntranetPlugin::getInstance()->get_theme_name())."/v1";
		register_rest_route($route_namespace, '/routes', array(
			'methods' => 'GET',
			'callback' => array($this, 'handle_request'),
			'permission_callback' => '__return_true'
		));
	}

	public function handle_request(WP_REST_Request $request): WP_REST_Response {
		if( is_multisite() ) {
			$sites_data = array();
			$sites = get_sites();
			foreach ($sites as $site) {
				switch_to_blog($site->blog_id);
				$posts_data = $this->get_posts_from_current_site($site->blog_id);

				$sites_data[] = array(
					'blog_id' => $site->blog_id,
					'site_name' => get_bloginfo('name'),
					'site_url' => get_bloginfo('url'),
					'admin_email' => get_bloginfo('admin_email'),
					'post_count' => wp_count_posts()->publish,
					'last_updated' => get_lastpostmodified('GMT'),
					'recent_posts' => $posts_data
				);
				restore_current_blog();
			}
		}
		else {
			$sites_data[] = array(
				'blog_id' => 1,
				'site_name' => get_bloginfo('name'),
				'site_url' => get_bloginfo('url'),
				'admin_email' => get_bloginfo('admin_email'),
				'post_count' => wp_count_posts()->publish,
				'last_updated' => get_lastpostmodified('GMT'),
				'recent_posts' => $posts_data
			);
		}

		return new WP_REST_Response($sites_data, 200);
	}

	private function get_posts_from_current_site( string $blog_id ): array {
		if ( is_multisite() ) {
			switch_to_blog($blog_id);
		}
		$posts_data = array();

		$recent_posts = new WP_Query(array(
			'post_type' => 'post',
			'post_status' => 'publish',
			'posts_per_page' => get_option('posts_per_page'),
			'orderby' => 'date',
			'order' => 'DESC'
		));

		if ($recent_posts->have_posts()) {
			while ($recent_posts->have_posts()) {
				$recent_posts->the_post();
				$posts_data[] = array(
					'ID' => get_the_ID(),
					'title' => get_the_title(),
					'permalink' => get_permalink(),
					'date' => get_the_date('Y-m-d H:i:s'),
					'excerpt' => get_the_excerpt(),
					'author' => get_the_author(),
					'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
					'categories' => wp_get_post_categories(get_the_ID(), array('fields' => 'names'))
				);
			}
			wp_reset_postdata();
		}
		return $posts_data;
	}
}