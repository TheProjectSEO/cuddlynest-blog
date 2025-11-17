<?php
/**
 * Plugin Name: X0PA Hiring Extension
 * Plugin URI: https://x0pa.com
 * Description: Create and manage hiring pages (interview questions & job descriptions) with automated schema, internal linking, and hub page
 * Version: 1.0.0
 * Author: X0PA
 * Author URI: https://x0pa.com
 * Text Domain: x0pa-hiring
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('X0PA_HIRING_VERSION', '1.0.0');
define('X0PA_HIRING_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('X0PA_HIRING_PLUGIN_URL', plugin_dir_url(__FILE__));
define('X0PA_HIRING_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main X0PA Hiring Extension Class
 */
class X0PA_Hiring_Extension {

    /**
     * Single instance of the class
     *
     * @var X0PA_Hiring_Extension
     */
    private static $instance = null;

    /**
     * Get single instance of the class
     *
     * @return X0PA_Hiring_Extension
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }

    /**
     * Load required dependencies
     */
    private function load_dependencies() {
        // Core classes
        require_once X0PA_HIRING_PLUGIN_DIR . 'includes/core/class-custom-post-type.php';
        require_once X0PA_HIRING_PLUGIN_DIR . 'includes/core/class-url-rewrite.php';
        require_once X0PA_HIRING_PLUGIN_DIR . 'includes/core/class-template-loader.php';

        // Admin classes
        if (is_admin()) {
            require_once X0PA_HIRING_PLUGIN_DIR . 'includes/admin/class-admin-menu.php';
            require_once X0PA_HIRING_PLUGIN_DIR . 'includes/admin/class-csv-uploader.php';
            require_once X0PA_HIRING_PLUGIN_DIR . 'includes/admin/class-bulk-processor.php';
        }
    }

    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        // Initialize custom post type
        add_action('init', array('X0PA_Hiring_CPT', 'register'));

        // Initialize URL rewrites
        add_action('init', array('X0PA_Hiring_URL_Rewrite', 'register_rules'));

        // Initialize template loader
        add_action('init', array('X0PA_Hiring_Template_Loader', 'init'));

        // Initialize admin menu
        if (is_admin()) {
            add_action('admin_menu', array('X0PA_Hiring_Admin_Menu', 'register'));
        }

        // Plugin activation/deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }

    /**
     * Plugin activation callback
     */
    public function activate() {
        // Register custom post type
        X0PA_Hiring_CPT::register();

        // Register URL rewrite rules
        X0PA_Hiring_URL_Rewrite::register_rules();

        // Flush rewrite rules
        flush_rewrite_rules();

        // Create hub page if it doesn't exist
        $this->create_hub_page();

        // Set activation flag
        update_option('x0pa_hiring_activated', true);
        update_option('x0pa_hiring_version', X0PA_HIRING_VERSION);
    }

    /**
     * Plugin deactivation callback
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();

        // Remove activation flag
        delete_option('x0pa_hiring_activated');
    }

    /**
     * Create hub page for /hiring/
     */
    private function create_hub_page() {
        // Check if hub page already exists
        $existing_page = get_page_by_path('hiring', OBJECT, 'page');

        if (!$existing_page) {
            $hub_page = array(
                'post_title'   => 'Hiring Resources',
                'post_name'    => 'hiring',
                'post_content' => '<!-- Hub page content will be dynamically generated -->',
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_author'  => get_current_user_id(),
            );

            $page_id = wp_insert_post($hub_page);

            if ($page_id && !is_wp_error($page_id)) {
                // Store hub page ID for future reference
                update_option('x0pa_hiring_hub_page_id', $page_id);
            }
        } else {
            // Store existing hub page ID
            update_option('x0pa_hiring_hub_page_id', $existing_page->ID);
        }
    }
}

/**
 * Initialize the plugin
 */
function x0pa_hiring_init() {
    return X0PA_Hiring_Extension::get_instance();
}

// Start the plugin
x0pa_hiring_init();
