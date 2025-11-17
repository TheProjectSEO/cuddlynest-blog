<?php
/**
 * Template Loader Class
 *
 * Loads custom templates for X0PA Hiring pages
 * Overrides WordPress default template hierarchy
 *
 * @package X0PA_Hiring_Extension
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * X0PA_Hiring_Template_Loader class
 */
class X0PA_Hiring_Template_Loader {

    /**
     * Initialize template loader
     */
    public static function init() {
        // Override single post template
        add_filter('single_template', array(__CLASS__, 'load_single_template'), 999);

        // Override page template (for hub page)
        add_filter('page_template', array(__CLASS__, 'load_page_template'), 999);
    }

    /**
     * Load custom template for single hiring posts
     *
     * @param string $template Current template path
     * @return string Modified template path
     */
    public static function load_single_template($template) {
        global $post;

        // Only for our custom post type
        if (!$post || $post->post_type !== 'x0pa_hiring_page') {
            return $template;
        }

        // Get page type from meta
        $page_type = get_post_meta($post->ID, '_x0pa_page_type', true);

        if (!$page_type) {
            return $template;
        }

        // Determine which template to load
        $custom_template = '';

        if ($page_type === 'interview-questions') {
            $custom_template = X0PA_HIRING_PLUGIN_DIR . 'includes/templates/wordpress-interview-questions.php';
        } elseif ($page_type === 'job-description') {
            $custom_template = X0PA_HIRING_PLUGIN_DIR . 'includes/templates/wordpress-job-description.php';
        }

        // Check if custom template exists
        if ($custom_template && file_exists($custom_template)) {
            return $custom_template;
        }

        return $template;
    }

    /**
     * Load custom template for hub page
     *
     * @param string $template Current template path
     * @return string Modified template path
     */
    public static function load_page_template($template) {
        global $post;

        // Only for hub page
        $hub_page_id = get_option('x0pa_hiring_hub_page_id');

        if (!$post || !$hub_page_id || $post->ID != $hub_page_id) {
            return $template;
        }

        // Load hub page template
        $custom_template = X0PA_HIRING_PLUGIN_DIR . 'includes/templates/template-hub-page.php';

        if (file_exists($custom_template)) {
            return $custom_template;
        }

        return $template;
    }
}
