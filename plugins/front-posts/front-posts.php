<?php
/**
 * FRONT POSTS
 *
 * @package           intranet
 * @author            norico
 * @copyright         2024 Company Name
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Front Posts
 * Plugin URI:        https://wordpress.org/plugins/front-posts
 * Description:       Highlighting of posts from other sites.
 * Version:           1.1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            norico
 * Author URI:        https://wordpress.org/profile/norico
 * Text Domain:       front-post
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Update URI:        https://
 * Requires Plugins:  //intranet
 */

if (!defined('ABSPATH')) {
    exit;
}

const OPTION_NAME = 'front_posts_external_links';
const TEMPLATE_NAME = 'front-posts';

/**
 * Prints scripts or data in the head tag on the front end.
 */
add_action('wp_head', 'insert_open_graph_meta');
add_shortcode('front-posts', 'render_front_posts');

function insert_open_graph_meta(): void {
    echo '<!-- OpenGraph -->' .PHP_EOL;
    echo get_open_graph("og:type");
    echo get_open_graph("og:site_name");
    echo get_open_graph("og:title");
    echo get_open_graph("og:url");
    echo get_open_graph("og:description");
    echo get_open_graph("og:image");
    echo get_open_graph("og:locale");
    echo '<!-- /OpenGraph -->' .PHP_EOL;
}
/**
 * print opengraph data.
 * @param $key
 *
 * @return string|null
 */
function get_open_graph($key): ?string {
    $post_id = get_the_ID();
    switch ($key) {
        case 'og:type':
            $page_type = is_front_page() ? 'Homepage' : (is_home() ? 'Blog' : get_post_type() );
            $value = sprintf('<meta property="og:type" content="%s"/>'.PHP_EOL, $page_type );
            break;
        case 'og:site_name':
            $value = sprintf('<meta property="og:site_name" content="%s"/>'.PHP_EOL, get_bloginfo('Name') );
            break;
        case 'og:title':
            $value = sprintf('<meta property="og:title" content="%s"/>'.PHP_EOL, get_the_title() );
            break;
        case 'og:url':
            $value = sprintf('<meta property="og:url" content="%s"/>'.PHP_EOL, get_the_permalink() );
            break;
        case 'og:description':
            $description = match(true) {
	            is_home() => "Blog",
	            is_front_page() => "Homepage",
                is_page() || is_single() => html_entity_decode(get_the_excerpt()),
                default => __('no description')
            };
	        $description = strlen($description) > 200 ? trim(substr($description, 0, strrpos(substr($description, 0, 200), ' '))) . '...' : $description;
	        $value = sprintf('<meta property="og:description" content="%s"/>' . PHP_EOL, htmlspecialchars($description, ENT_QUOTES, 'UTF-8'));
            break;
        case 'og:image':
            $image = match(true) {
                is_home() || is_front_page() => null,
                default => get_the_post_thumbnail_url($post_id, 'medium')
            };
            $value = $image ? sprintf('<meta property="og:image" content="%s"/>%s', $image, PHP_EOL) : null;
            break;
        case 'og:locale':
            $value = sprintf('<meta property="og:locale" content="%s"/>'.PHP_EOL, get_bloginfo( 'language' ) );
            break;
        default:
            $value = null;
            break;
    }
    return $value;

}


function render_front_posts($atts = null) {

    foreach (get_posts_url() as $url) {
        $data[] =fetch_item_data($url);
    }
    if (!empty($data)) {
	    $template = locate_template( 'template-parts/front-posts-2.php' );
	    if ( ! $template ) {
		    $template = plugin_dir_path( __FILE__ ) . 'templates/front-posts-2.php';
	    }
	    if ( file_exists( $template ) ) {
		    load_template($template, false, ['data' => $data] );
	    }
    }
}

function get_item($item,$item_id): ?string {
	$data = fetch_item_data($item);
	$template = locate_template( 'template-parts/'.TEMPLATE_NAME.'.php' );
	ob_start();
	if ( ! $template ) {
		$template = plugin_dir_path( __FILE__ ) . 'templates/'.TEMPLATE_NAME.'.php';
	}
	if ( file_exists( $template ) ) {
		load_template($template, false, array('item_id' => $item_id, 'data' => $data));
	}
	return ob_get_clean();
}


function fetch_item_data($item): ?array {
	// Générer une clé unique pour le transient basée sur l'URL
	$transient_key = 'front_posts_item_' . md5($item);

	// Essayer de récupérer les données du transient
	$cached_data = get_transient($transient_key);

	// Si les données existent dans le transient, les retourner
	if ($cached_data !== false) {
		return $cached_data;
	}

	// Si pas de données en cache, récupérer les données
	$args = array(
		'method' => 'GET',
		'timeout' => 10,
		'user-agent' => 'WordPress Front-posts plugin',
		'sslverify' => true,
		'X-Api-Key' => AUTH_KEY
	);
	$response = wp_remote_get($item, $args);

	if (is_wp_error($response)) {
		error_log('Front Posts - Erreur de récupération : ' . $response->get_error_message());
		return false;
	}
	$body = wp_remote_retrieve_body($response);

	$title = '';
	$description = '';
	$thumbnail = '';

	// Recherche du titre
	if (preg_match('/<title[^>]*>(.*?)<\/title>/isu', $body, $title_match)) {
		$title = trim(strip_tags($title_match[1]));
	}
	// Recherche de la description
	// Utilise la balise meta description du post (il faut qu'elle soit définie)
	if (preg_match('/<meta\s+name="description"\s+content="([^"]+)"/isu', $body, $desc_match)) {
		$description = trim(strip_tags($desc_match[1]));
	}
	// Utilise la balise meta property og:description du post (il faut qu'elle soit définie)
	if (empty($description) && preg_match('/<meta\s+property="og:description"\s+content="([^"]+)"/isu', $body, $desc_match)) {
		$description = trim(strip_tags($desc_match[1]));
	}
	// Recherche de la miniature
	if (preg_match('/<meta\s+property="og:image"\s+content="([^"]+)"/isu', $body, $desc_match)) {
		$thumbnail = trim(strip_tags($desc_match[1]));
	}

	// Fallback si les regex ne fonctionnent pas
	$title = !empty($title) ? $title : 'Titre non disponible';
	$description = !empty($description) ? $description : 'Description non disponible';
	$thumbnail = !empty($thumbnail) ? $thumbnail : 'Thumbnail non disponible';

	$result = [
		'link' => $item,
		'title' => sanitize_text_field($title),
		'excerpt' => sanitize_text_field($description),
		'thumbnail' => sanitize_text_field($thumbnail)
	];

	// Enregistrer les données dans un transient pendant 60 secondes
	set_transient($transient_key, $result, 60);

	// Ajouter un script pour afficher un message dans la console
	add_action('wp_footer', function() use ($item) {
		printf('<script>');
		printf('console.log("Données récupérées en direct sur l\'article  %s")', $item);
		printf('</script>');
	});

	return $result;
}

// Hook pour créer la page d'administration
add_action('admin_menu', 'front_posts_add_admin_page');

// Hook pour enregistrer les scripts et styles
add_action('admin_enqueue_scripts', 'front_posts_admin_scripts');

function front_posts_add_admin_page() {
	add_menu_page(
		'Front Posts URLs',
		'Front Posts',
		'manage_options',
		'front-posts-urls',
		'front_posts_admin_page',
        'dashicons-slides',
        2

	);
}

function front_posts_admin_scripts($hook) {
	// Charger uniquement sur notre page

	if ($hook !== 'toplevel_page_front-posts-urls') {
		return;
	}

	// Charger jQuery UI Sortable
	wp_enqueue_script('jquery-ui-sortable');

	// Script personnalisé
	wp_enqueue_script('front-posts-admin', plugin_dir_url(__FILE__) . 'assets/front-posts-admin.js', ['jquery-ui-sortable'], '1.0', true);

	// Style personnalisé
	wp_enqueue_style('front-posts-admin', plugin_dir_url(__FILE__) . 'assets/front-posts-admin.css');
}

function get_posts_url(): array {
	// Récupérer les URLs enregistrées dans les options
	$saved_urls = get_option(OPTION_NAME, array());

	// Filtrer pour ne garder que les URLs valides
	return array_filter($saved_urls, 'filter_var', FILTER_VALIDATE_URL);
}

function front_posts_admin_page() {
	// Vérifier les permissions
	if (!current_user_can('manage_options')) {
		wp_die(__('Vous n\'avez pas les autorisations suffisantes pour accéder à cette page.'));
	}

	// Récupérer les URLs existantes
	$urls = get_option(OPTION_NAME, array());
	?>
    <div class="wrap front-posts-urls-admin">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <form method="post" action="options.php" id="front-posts-urls-form">
			<?php
			// Ajouter des champs de sécurité
			settings_fields('front_posts_urls_group');
			wp_nonce_field('front_posts_urls_nonce', 'front_posts_urls_nonce');
			?>

            <div id="urls-container">
				<?php
				// Afficher les URLs existantes
				if (!empty($urls)) {
					foreach ($urls as $index => $url) {
						front_posts_render_url_input($index, $url);
					}
				} else {
					// Au moins un champ vide par défaut
					front_posts_render_url_input(0);
				}
				?>
            </div>

            <div class="front-posts-actions">
                <button type="button" id="add-url-button" class="button button-secondary">
                    Ajouter une URL
                </button>
				<?php submit_button('Enregistrer les URLs', 'primary', 'submit', false); ?>
            </div>
        </form>
    </div>

    <script id="url-input-template" type="text/html">
		<?php front_posts_render_url_input('{{index}}'); ?>
    </script>
	<?php
}

function front_posts_render_url_input($index, $url = '') {
	?>
    <div class="url-input-row" data-index="<?php echo esc_attr($index); ?>">
        <span class="dashicons dashicons-media-default sort-handle"></span>
        <input
                type="url"
                name="<?php echo OPTION_NAME; ?>[<?php echo esc_attr($index); ?>]"
                value="<?php echo esc_url($url); ?>"
                placeholder="Saisir une URL"
                class="large-text"
        />
        <button type="button" class="button button-link-delete remove-url-button">
            Supprimer
        </button>
    </div>
	<?php
}

// Enregistrer les paramètres
add_action('admin_init', 'front_posts_register_settings');

function front_posts_register_settings() {
	register_setting(
		'front_posts_urls_group',
		OPTION_NAME,
		[
			'type' => 'array',
			'sanitize_callback' => 'front_posts_sanitize_urls'
		]
	);
}

function front_posts_sanitize_urls($input) {
	// Si l'input est null ou vide, retourner un tableau vide
	if (!is_array($input)) {
		return array();
	}

	// Filtrer et nettoyer chaque URL
	$clean_urls = array_filter($input, function($url) {
		// Trim et vérifier si l'URL est valide
		$url = trim($url);
		return !empty($url) && filter_var($url, FILTER_VALIDATE_URL);
	});

	// Si aucune URL valide, retourner le tableau original
	// Cela permet de conserver les champs même s'ils sont temporairement vides
	return empty($clean_urls) ? $input : array_values($clean_urls);
}
