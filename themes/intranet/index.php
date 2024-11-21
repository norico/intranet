<?php get_header();?>

<div class="container mx-auto">
	<?php
	$args = array(
		'post_type' => 'post',                                              // Uniquement les articles
		'post_status' => 'publish',                                         // Uniquement les articles publiés
		'orderby' => 'date',                                                // Trier par date
		'order' => 'DESC',                                                  // Du plus récent au plus ancien
		'category__not_in' => intval(get_option('default_category')) // La catégorie n'est pas celle par défaut
	);

	// Création de la nouvelle requête
	$query = new WP_Query($args);
	// Boucle d'affichage
	if ($query->have_posts()) :
		echo '<ul>'; // Bonne pratique : wrapper la liste
		while ($query->have_posts()) : $query->the_post();
			echo '<li><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></li>';
		endwhile;
		echo '</ul>';

		// Restaurer les données de post originales
		wp_reset_postdata();
	else :
		_e('Sorry, no posts were found.', 'textdomain');
	endif;
	?>
</div>



<?php get_footer();?>