<?php
/*
Plugin Name: Intranet
Plugin URI: https://wordpress.org/plugins/intranet
Description: Add and remove features for an intranet (without external network)
Author: norico
Version: 1.0.0
Author URI: https://profiles.wordpress.org/norico/
Text Domain: intranet
Domain Path: /languages
*/

// Empêcher l'accès direct au fichier
if (!defined('ABSPATH')) {
    exit;
}
require_once('Routes.php');

// Initialisation du plugin
$plugin = IntranetPlugin::getInstance();

class IntranetPlugin {
    /**
     * Instance unique de la classe (pattern Singleton)
     *
     * @var IntranetPlugin|null
     */
    private static ?IntranetPlugin $instance = null;

    /**
     * Prefix du plugin
     *
     * @var string
     */
    private string $prefix = 'plugin_';

    /**
     * Plugin name
     *
     * @var string
     */
    private string $name;

    /**
     * Plugin version
     *
     * @var string
     */
    private string $version;

	private object $routes;

    /**
     * Classe Instance
     *
     * @return IntranetPlugin|null
     */
    public static function getInstance(): ?IntranetPlugin
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    protected function __construct()
    {
        // Utiliser les données du plugin plutôt que du thème
        $this->name = wp_get_theme()->get('Name');
        $this->version = wp_get_theme()->get('Version');
		$this->routes = new Routes();
        $this->init();
    }

    /**
     * ! Clone
     */
    private function __clone()
    {
    }

    /**
     * Plugin Init
     */
    private function init(): void
    {
        // Charger les traductions
        add_action('plugins_loaded', [$this, 'loadTextDomain']);
        add_action('admin_enqueue_scripts', [$this, 'admin_register_styles'],10 );
        add_action('get_avatar', '__return_false');

        add_action( 'wp_login', [$this, 'save_timestamp'],10,2);
        add_filter( 'manage_users_columns', [ $this, 'admin_users_column' ], 10, 3 );
        add_filter( 'manage_users_custom_column', [ $this, 'admin_users_column_data' ], 10, 3 );

	    remove_action('wp_head', 'wp_generator');


    }

    /**
     * Load translation
     */
    public function loadTextDomain(): void
    {
        load_plugin_textdomain(
            $this->name,
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
    }

    /**
     * Register stylesheet
     */
    public function admin_register_styles(): void
    {
        wp_register_style( $this->prefix.$this->get_plugin_slug() , plugins_url( trailingslashit($this->get_plugin_slug()). $this->get_plugin_slug() .'.css' ) );
        wp_enqueue_style( $this->prefix.$this->get_plugin_slug() );
    }


    /**
     * Getter for theme name
     *
     * @return string
     */
    public function get_theme_name(): string
    {
        return $this->name;
    }

    /**
     * Getter for theme slug
     *
     * @return string
     */
    public function get_plugin_slug(): string
    {
        return strtolower($this->name);
    }

    /**
     * Getter for theme version
     *
     * @return string
     */
    public function get_theme_version()
    {
        return $this->version;
    }

    /**
     * Store user last login
     */
    public function save_timestamp($user_login, $user): void
    {
        update_user_meta( $user->ID, 'last_login', current_time( 'timestamp' ) );
    }

	/**
	 * Add column to users page
	 *
	 * @param $columns
	 *
	 * @return array
	 */
    public function admin_users_column( $columns ) : array {
        $columns['last_login'] = __( 'Last Login' );
        return $columns;
    }

    /**
     * Put data in column last_login on users page
     * @param $output
     * @param $column_id
     * @param $user_id
     * @return string
     */
    public function admin_users_column_data($output, $column_id, $user_id ): string {
        if ( $column_id == 'last_login' ) {
            $last_login  = get_user_meta( $user_id, 'last_login', true );
            $date_format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
            $output = $last_login ? date_i18n( $date_format, $last_login ) : __( 'No items.' );
        }
        return $output;
    }
}
