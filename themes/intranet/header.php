<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class("flex flex-col min-h-[calc(100vh-var(--wp-admin--admin-bar--height,0px))]"); ?>>

<?php wp_body_open(); ?>

<div id="page" class="grow">
	<a href="#content" class="sr-only"><?php esc_html_e( 'Skip to content', 'intranet' ); ?></a>