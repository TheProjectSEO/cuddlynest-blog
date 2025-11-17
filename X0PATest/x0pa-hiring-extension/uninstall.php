<?php
/**
 * Uninstall Script
 *
 * Fired when the plugin is uninstalled.
 *
 * @package X0PA_Hiring_Extension
 */

// Exit if accessed directly or not uninstalling
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/**
 * Delete all hiring pages
 */
function x0pa_hiring_delete_all_pages() {
    $args = array(
        'post_type'      => 'x0pa_hiring_page',
        'posts_per_page' => -1,
        'post_status'    => 'any',
    );

    $pages = get_posts($args);

    foreach ($pages as $page) {
        // Delete all post meta
        delete_post_meta($page->ID, '_x0pa_page_type');
        delete_post_meta($page->ID, '_x0pa_job_title');
        delete_post_meta($page->ID, '_x0pa_last_updated');
        delete_post_meta($page->ID, '_x0pa_content_json');

        // Delete the post permanently
        wp_delete_post($page->ID, true);
    }
}

/**
 * Delete hub page
 */
function x0pa_hiring_delete_hub_page() {
    $hub_page_id = get_option('x0pa_hiring_hub_page_id');

    if ($hub_page_id) {
        wp_delete_post($hub_page_id, true);
    }
}

/**
 * Delete plugin options
 */
function x0pa_hiring_delete_options() {
    delete_option('x0pa_hiring_activated');
    delete_option('x0pa_hiring_version');
    delete_option('x0pa_hiring_hub_page_id');
}

// Execute cleanup
x0pa_hiring_delete_all_pages();
x0pa_hiring_delete_hub_page();
x0pa_hiring_delete_options();

// Flush rewrite rules
flush_rewrite_rules();
