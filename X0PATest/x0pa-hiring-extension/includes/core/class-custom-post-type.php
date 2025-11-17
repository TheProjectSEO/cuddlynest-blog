<?php
/**
 * Custom Post Type Registration
 *
 * @package X0PA_Hiring_Extension
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * X0PA Hiring Custom Post Type Class
 */
class X0PA_Hiring_CPT {

    /**
     * Post type slug
     *
     * @var string
     */
    const POST_TYPE = 'x0pa_hiring_page';

    /**
     * Meta key for page type
     *
     * @var string
     */
    const META_PAGE_TYPE = '_x0pa_page_type';

    /**
     * Meta key for job title
     *
     * @var string
     */
    const META_JOB_TITLE = '_x0pa_job_title';

    /**
     * Meta key for last updated
     *
     * @var string
     */
    const META_LAST_UPDATED = '_x0pa_last_updated';

    /**
     * Meta key for content JSON
     *
     * @var string
     */
    const META_CONTENT_JSON = '_x0pa_content_json';

    /**
     * Register custom post type
     */
    public static function register() {
        $labels = array(
            'name'               => _x('Hiring Pages', 'post type general name', 'x0pa-hiring'),
            'singular_name'      => _x('Hiring Page', 'post type singular name', 'x0pa-hiring'),
            'menu_name'          => _x('Hiring Pages', 'admin menu', 'x0pa-hiring'),
            'name_admin_bar'     => _x('Hiring Page', 'add new on admin bar', 'x0pa-hiring'),
            'add_new'            => _x('Add New', 'hiring page', 'x0pa-hiring'),
            'add_new_item'       => __('Add New Hiring Page', 'x0pa-hiring'),
            'new_item'           => __('New Hiring Page', 'x0pa-hiring'),
            'edit_item'          => __('Edit Hiring Page', 'x0pa-hiring'),
            'view_item'          => __('View Hiring Page', 'x0pa-hiring'),
            'all_items'          => __('All Hiring Pages', 'x0pa-hiring'),
            'search_items'       => __('Search Hiring Pages', 'x0pa-hiring'),
            'parent_item_colon'  => __('Parent Hiring Pages:', 'x0pa-hiring'),
            'not_found'          => __('No hiring pages found.', 'x0pa-hiring'),
            'not_found_in_trash' => __('No hiring pages found in Trash.', 'x0pa-hiring'),
        );

        $args = array(
            'labels'              => $labels,
            'description'         => __('Hiring pages for interview questions and job descriptions', 'x0pa-hiring'),
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => true,
            'rewrite'             => array(
                'slug'       => 'hiring',
                'with_front' => false,
            ),
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'menu_position'       => 20,
            'menu_icon'           => 'dashicons-portfolio',
            'supports'            => array('title', 'custom-fields'),
            'show_in_rest'        => true, // Enable Gutenberg editor
        );

        register_post_type(self::POST_TYPE, $args);

        // Register meta boxes
        add_action('add_meta_boxes', array(__CLASS__, 'add_meta_boxes'));

        // Save meta data
        add_action('save_post_' . self::POST_TYPE, array(__CLASS__, 'save_meta_data'));

        // Add custom columns
        add_filter('manage_' . self::POST_TYPE . '_posts_columns', array(__CLASS__, 'add_custom_columns'));
        add_action('manage_' . self::POST_TYPE . '_posts_custom_column', array(__CLASS__, 'render_custom_columns'), 10, 2);
    }

    /**
     * Add meta boxes to the editor
     */
    public static function add_meta_boxes() {
        add_meta_box(
            'x0pa_hiring_details',
            __('Hiring Page Details', 'x0pa-hiring'),
            array(__CLASS__, 'render_meta_box'),
            self::POST_TYPE,
            'normal',
            'high'
        );
    }

    /**
     * Render meta box content
     *
     * @param WP_Post $post The post object
     */
    public static function render_meta_box($post) {
        // Add nonce for security
        wp_nonce_field('x0pa_hiring_meta_box', 'x0pa_hiring_meta_box_nonce');

        // Retrieve current values
        $page_type = get_post_meta($post->ID, self::META_PAGE_TYPE, true);
        $job_title = get_post_meta($post->ID, self::META_JOB_TITLE, true);
        $last_updated = get_post_meta($post->ID, self::META_LAST_UPDATED, true);
        $content_json = get_post_meta($post->ID, self::META_CONTENT_JSON, true);

        ?>
        <table class="form-table">
            <tr>
                <th><label for="x0pa_page_type"><?php _e('Page Type', 'x0pa-hiring'); ?></label></th>
                <td>
                    <select name="x0pa_page_type" id="x0pa_page_type" class="regular-text">
                        <option value=""><?php _e('Select Page Type', 'x0pa-hiring'); ?></option>
                        <option value="interview-questions" <?php selected($page_type, 'interview-questions'); ?>>
                            <?php _e('Interview Questions', 'x0pa-hiring'); ?>
                        </option>
                        <option value="job-description" <?php selected($page_type, 'job-description'); ?>>
                            <?php _e('Job Description', 'x0pa-hiring'); ?>
                        </option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="x0pa_job_title"><?php _e('Job Title', 'x0pa-hiring'); ?></label></th>
                <td>
                    <input type="text" name="x0pa_job_title" id="x0pa_job_title"
                           value="<?php echo esc_attr($job_title); ?>" class="regular-text">
                    <p class="description"><?php _e('e.g., Accountant, Software Engineer', 'x0pa-hiring'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="x0pa_last_updated"><?php _e('Last Updated', 'x0pa-hiring'); ?></label></th>
                <td>
                    <input type="date" name="x0pa_last_updated" id="x0pa_last_updated"
                           value="<?php echo esc_attr($last_updated); ?>" class="regular-text">
                </td>
            </tr>
            <tr>
                <th><label for="x0pa_content_json"><?php _e('Content (JSON)', 'x0pa-hiring'); ?></label></th>
                <td>
                    <textarea name="x0pa_content_json" id="x0pa_content_json"
                              rows="10" class="large-text code"><?php echo esc_textarea($content_json); ?></textarea>
                    <p class="description"><?php _e('Enter content in JSON format', 'x0pa-hiring'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Save meta data when post is saved
     *
     * @param int $post_id The post ID
     */
    public static function save_meta_data($post_id) {
        // Check if nonce is set
        if (!isset($_POST['x0pa_hiring_meta_box_nonce'])) {
            return;
        }

        // Verify nonce
        if (!wp_verify_nonce($_POST['x0pa_hiring_meta_box_nonce'], 'x0pa_hiring_meta_box')) {
            return;
        }

        // Check if autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check user permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save page type
        if (isset($_POST['x0pa_page_type'])) {
            update_post_meta(
                $post_id,
                self::META_PAGE_TYPE,
                sanitize_text_field($_POST['x0pa_page_type'])
            );
        }

        // Save job title
        if (isset($_POST['x0pa_job_title'])) {
            update_post_meta(
                $post_id,
                self::META_JOB_TITLE,
                sanitize_text_field($_POST['x0pa_job_title'])
            );
        }

        // Save last updated
        if (isset($_POST['x0pa_last_updated'])) {
            update_post_meta(
                $post_id,
                self::META_LAST_UPDATED,
                sanitize_text_field($_POST['x0pa_last_updated'])
            );
        }

        // Save content JSON
        if (isset($_POST['x0pa_content_json'])) {
            // Validate JSON
            $json_content = stripslashes($_POST['x0pa_content_json']);
            $decoded = json_decode($json_content, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                update_post_meta($post_id, self::META_CONTENT_JSON, $json_content);
            }
        }
    }

    /**
     * Add custom columns to the admin list
     *
     * @param array $columns Existing columns
     * @return array Modified columns
     */
    public static function add_custom_columns($columns) {
        $new_columns = array();

        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;

            if ($key === 'title') {
                $new_columns['page_type'] = __('Page Type', 'x0pa-hiring');
                $new_columns['job_title'] = __('Job Title', 'x0pa-hiring');
                $new_columns['last_updated'] = __('Last Updated', 'x0pa-hiring');
            }
        }

        return $new_columns;
    }

    /**
     * Render custom column content
     *
     * @param string $column  Column name
     * @param int    $post_id Post ID
     */
    public static function render_custom_columns($column, $post_id) {
        switch ($column) {
            case 'page_type':
                $page_type = get_post_meta($post_id, self::META_PAGE_TYPE, true);
                if ($page_type) {
                    $type_labels = array(
                        'interview-questions' => __('Interview Questions', 'x0pa-hiring'),
                        'job-description'     => __('Job Description', 'x0pa-hiring'),
                    );
                    echo esc_html($type_labels[$page_type] ?? $page_type);
                } else {
                    echo '—';
                }
                break;

            case 'job_title':
                $job_title = get_post_meta($post_id, self::META_JOB_TITLE, true);
                echo $job_title ? esc_html($job_title) : '—';
                break;

            case 'last_updated':
                $last_updated = get_post_meta($post_id, self::META_LAST_UPDATED, true);
                if ($last_updated) {
                    echo esc_html(date_i18n(get_option('date_format'), strtotime($last_updated)));
                } else {
                    echo '—';
                }
                break;
        }
    }
}
