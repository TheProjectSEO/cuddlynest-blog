<?php
/**
 * Admin Menu Handler
 *
 * @package X0PA_Hiring_Extension
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * X0PA Hiring Admin Menu Class
 */
class X0PA_Hiring_Admin_Menu {

    /**
     * Register admin menu pages
     */
    public static function register() {
        // Add submenu page under Hiring Pages CPT
        add_submenu_page(
            'edit.php?post_type=x0pa_hiring_page',
            __('Upload Pages', 'x0pa-hiring'),
            __('Upload Pages', 'x0pa-hiring'),
            'manage_options',
            'x0pa-upload-pages',
            array(__CLASS__, 'render_upload_page')
        );

        // Add submenu page for managing pages
        add_submenu_page(
            'edit.php?post_type=x0pa_hiring_page',
            __('Manage Pages', 'x0pa-hiring'),
            __('Manage Pages', 'x0pa-hiring'),
            'manage_options',
            'x0pa-manage-pages',
            array(__CLASS__, 'render_manage_page')
        );

        // Enqueue admin styles and scripts
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_assets'));
    }

    /**
     * Enqueue admin styles and scripts
     *
     * @param string $hook The current admin page hook
     */
    public static function enqueue_admin_assets($hook) {
        // Only load on our plugin pages
        if (strpos($hook, 'x0pa-') === false) {
            return;
        }

        // Enqueue WordPress media uploader
        wp_enqueue_media();

        // Add custom admin styles
        wp_add_inline_style('wp-admin', '
            .x0pa-upload-form {
                max-width: 800px;
                background: #fff;
                padding: 20px;
                border: 1px solid #ccd0d4;
                box-shadow: 0 1px 1px rgba(0,0,0,.04);
            }
            .x0pa-upload-form h2 {
                margin-top: 0;
            }
            .x0pa-upload-section {
                margin-bottom: 30px;
                padding-bottom: 20px;
                border-bottom: 1px solid #ddd;
            }
            .x0pa-upload-section:last-child {
                border-bottom: none;
            }
            .x0pa-file-input {
                margin: 10px 0;
            }
            .x0pa-instructions {
                background: #f0f6fc;
                border-left: 4px solid #0073aa;
                padding: 15px;
                margin: 20px 0;
            }
            .x0pa-instructions h3 {
                margin-top: 0;
            }
            .x0pa-instructions ul {
                margin-bottom: 0;
            }
            .x0pa-message {
                padding: 12px;
                margin: 20px 0;
                border-left: 4px solid;
            }
            .x0pa-message.success {
                background: #ecf7ed;
                border-color: #46b450;
                color: #155724;
            }
            .x0pa-message.error {
                background: #fef7f1;
                border-color: #dc3232;
                color: #721c24;
            }
        ');
    }

    /**
     * Render upload page
     */
    public static function render_upload_page() {
        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'x0pa-hiring'));
        }

        // Include the upload form view
        include X0PA_HIRING_PLUGIN_DIR . 'includes/admin/views/upload-form.php';
    }

    /**
     * Render manage page
     */
    public static function render_manage_page() {
        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'x0pa-hiring'));
        }

        // Include the page list view
        include X0PA_HIRING_PLUGIN_DIR . 'includes/admin/views/page-list.php';
    }
}
