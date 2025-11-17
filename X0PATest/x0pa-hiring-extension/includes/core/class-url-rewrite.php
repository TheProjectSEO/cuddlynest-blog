<?php
/**
 * URL Rewrite Rules Handler
 *
 * @package X0PA_Hiring_Extension
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * X0PA Hiring URL Rewrite Class
 */
class X0PA_Hiring_URL_Rewrite {

    /**
     * Register custom URL rewrite rules
     */
    public static function register_rules() {
        // Add custom rewrite rules for hiring pages
        // Pattern: /hiring/{job-title-slug}-{page-type}/

        add_rewrite_rule(
            '^hiring/([^/]+)-(interview-questions|job-description)/?$',
            'index.php?post_type=x0pa_hiring_page&name=$matches[1]-$matches[2]',
            'top'
        );

        // Hub page rule - /hiring/
        add_rewrite_rule(
            '^hiring/?$',
            'index.php?pagename=hiring',
            'top'
        );

        // Add query vars
        add_filter('query_vars', array(__CLASS__, 'add_query_vars'));

        // Modify permalink structure
        add_filter('post_type_link', array(__CLASS__, 'custom_permalink'), 10, 2);
    }

    /**
     * Add custom query variables
     *
     * @param array $vars Existing query vars
     * @return array Modified query vars
     */
    public static function add_query_vars($vars) {
        $vars[] = 'hiring_page_type';
        $vars[] = 'hiring_job_title';
        return $vars;
    }

    /**
     * Custom permalink structure for hiring pages
     *
     * @param string  $post_link The post's permalink
     * @param WP_Post $post      The post object
     * @return string Modified permalink
     */
    public static function custom_permalink($post_link, $post) {
        if ($post->post_type !== 'x0pa_hiring_page') {
            return $post_link;
        }

        // Get page type from meta
        $page_type = get_post_meta($post->ID, '_x0pa_page_type', true);
        $job_title = get_post_meta($post->ID, '_x0pa_job_title', true);

        if (!$page_type || !$job_title) {
            return $post_link;
        }

        // Create slug from job title
        $job_slug = sanitize_title($job_title);

        // Build custom URL: /hiring/{job-title-slug}-{page-type}/
        $custom_link = home_url("/hiring/{$job_slug}-{$page_type}/");

        return $custom_link;
    }

    /**
     * Get hiring hub page URL
     *
     * @return string Hub page URL
     */
    public static function get_hub_url() {
        return home_url('/hiring/');
    }

    /**
     * Get hiring page URL
     *
     * @param string $job_title Job title
     * @param string $page_type Page type (interview-questions or job-description)
     * @return string Page URL
     */
    public static function get_page_url($job_title, $page_type) {
        $job_slug = sanitize_title($job_title);
        return home_url("/hiring/{$job_slug}-{$page_type}/");
    }

    /**
     * Parse hiring URL to get components
     *
     * @param string $url The URL to parse
     * @return array|false Array with job_title and page_type, or false on failure
     */
    public static function parse_hiring_url($url) {
        // Extract path from URL
        $path = parse_url($url, PHP_URL_PATH);

        // Remove leading/trailing slashes
        $path = trim($path, '/');

        // Check if it matches hiring pattern
        if (!preg_match('#^hiring/([^/]+)-(interview-questions|job-description)$#', $path, $matches)) {
            return false;
        }

        return array(
            'job_slug'  => $matches[1],
            'page_type' => $matches[2],
        );
    }
}
