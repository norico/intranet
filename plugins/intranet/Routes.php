<?php
// Empêcher l'accès direct au fichier
if (!defined('ABSPATH')) {
	exit;
}
class Routes {

	private int $route_version  = 1;
	private string $route_name= 'recent-posts';


	public function __construct() {
		add_action('rest_api_init', [$this, 'register_routes']);
	}

	public function register_routes(): void {
		$route_namespace = strtolower(IntranetPlugin::getInstance()->get_theme_name())."/v".$this->route_version;
		register_rest_route($route_namespace, '/'.$this->route_name, array(
			'methods' => 'GET',
			'callback' => array($this, 'handle_request'),
			'permission_callback' => '__return_true'
		));
	}

	public function handle_request(WP_REST_Request $request): WP_REST_Response
	{
		$sites_data = [];

		// Si multisite, récupérer les données de tous les sites
		if (is_multisite()) {
			foreach (get_sites() as $site) {
				switch_to_blog($site->blog_id);
				$sites_data[] = $this->get_site_data($site->blog_id);
				restore_current_blog();
			}
		}
		// Sinon récupérer les données du site unique
		else {
			$sites_data[] = $this->get_site_data(1);
		}

		return new WP_REST_Response($sites_data, 200);
	}

	private function get_site_data(int $blog_id): array
	{
		return [
			'blog_id' => $blog_id,
			'site_name' => get_bloginfo('name'),
			'site_url' => get_bloginfo('url'),
			'admin_email' => get_bloginfo('admin_email'),
			'post_count' => wp_count_posts()->publish,
			'last_updated' => get_lastpostmodified('GMT'),
			'recent_posts' => $this->get_posts_from_current_site($blog_id)
		];
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