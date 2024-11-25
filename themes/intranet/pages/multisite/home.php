<?php
get_header();
?>
<div class="container mx-auto">
	<div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-4 mb-10">
		<?php
		$multisite = Intranet\Multisite::getInstance();
		$main_site = is_main_site();
		$args = array(
			'posts_per_page'    => 12,
			'post_type'         => 'post',
			'post_status'       => 'publish',
			'category__not_in'  => intval(get_option('default_category')),
            'exclude_primary'   => true,
		);
		$posts = $multisite->Query($args);

		foreach ( $posts as $post ) {
            switch_to_blog($post['blog_id']);
            $post = get_post($post['post_id']);
			get_template_part('template-parts/content', 'card', ['post' => $post, 'main' => $main_site]);
            restore_current_blog();
        }
        ?>
	</div>
</div>
<?php
get_footer();