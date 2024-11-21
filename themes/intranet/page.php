<?php
get_header();
echo '<div class="container mx-auto">';
echo '<h1 class="titre"><?php get_the_title();?></h1>';
the_content();
echo '</div>';
get_footer();
