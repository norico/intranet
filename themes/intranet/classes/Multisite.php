<?php

namespace Intranet;

class Multisite {

	private static ?Multisite $instance = null;
	public static function getInstance(): ?Multisite {
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __clone() {
		return false;
	}

	private function __construct() {

	}

	public function Query($args=null): array {
		$sites = $this->get_available_sites($args['exclude_primary']);
		foreach ( $sites as $blog_id ) {
			switch_to_blog( $blog_id );
			$query = new \WP_Query( $args );
			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					$posts[] = array('blog_id' =>$blog_id, 'post_id' => get_the_id(), 'date' => get_the_date('c') );
				}
			}
			restore_current_blog();
		}
		usort($posts, function($a, $b) {
			return strtotime($b['date']) - strtotime($a['date']);
		});
		return $posts;
	}

	private function get_available_sites($exclude_primary=false): array {
		$sub_sites = get_sites([
			'public' => 1,
			'archived' => 0,
			'mature' => 0,
			'spam' => 0,
			'deleted' => 0
		]);
		foreach ($sub_sites as $sub_site) {
			if (!$exclude_primary || get_main_site_id() !== intval($sub_site->blog_id)) {
				$sub_site_ids[] = intval($sub_site->blog_id);
			}
		}
		return $sub_site_ids ?? [];
	}
}