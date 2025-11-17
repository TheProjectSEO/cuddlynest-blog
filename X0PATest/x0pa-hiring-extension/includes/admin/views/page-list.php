<?php
/**
 * Page List View
 *
 * @package X0PA_Hiring_Extension
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Handle bulk delete action
if (isset($_POST['action']) && $_POST['action'] === 'bulk_delete' && isset($_POST['bulk_delete_nonce'])) {
    if (wp_verify_nonce($_POST['bulk_delete_nonce'], 'bulk_delete_pages') && current_user_can('delete_posts')) {
        if (isset($_POST['post_ids']) && is_array($_POST['post_ids'])) {
            $deleted_count = 0;
            foreach ($_POST['post_ids'] as $post_id) {
                if (wp_delete_post($post_id, true)) {
                    $deleted_count++;
                }
            }
            echo '<div class="x0pa-message success">';
            printf(__('Successfully deleted %d page(s)', 'x0pa-hiring'), $deleted_count);
            echo '</div>';
        }
    }
}

// Get filter parameters
$filter_page_type = isset($_GET['filter_page_type']) ? sanitize_text_field($_GET['filter_page_type']) : '';

// Build query args
$args = array(
    'post_type'      => 'x0pa_hiring_page',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'meta_value',
    'meta_key'       => '_x0pa_job_title',
    'order'          => 'ASC',
);

// Apply filter if set
if (!empty($filter_page_type)) {
    $args['meta_query'] = array(
        array(
            'key'     => '_x0pa_page_type',
            'value'   => $filter_page_type,
            'compare' => '=',
        ),
    );
}

// Get posts
$hiring_pages = get_posts($args);
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <!-- Filter Form -->
    <form method="get" action="" style="margin: 20px 0;">
        <input type="hidden" name="post_type" value="x0pa_hiring_page">
        <input type="hidden" name="page" value="x0pa-manage-pages">

        <label for="filter_page_type"><?php _e('Filter by Page Type:', 'x0pa-hiring'); ?></label>
        <select name="filter_page_type" id="filter_page_type">
            <option value=""><?php _e('All Types', 'x0pa-hiring'); ?></option>
            <option value="interview-questions" <?php selected($filter_page_type, 'interview-questions'); ?>>
                <?php _e('Interview Questions', 'x0pa-hiring'); ?>
            </option>
            <option value="job-description" <?php selected($filter_page_type, 'job-description'); ?>>
                <?php _e('Job Description', 'x0pa-hiring'); ?>
            </option>
        </select>

        <?php submit_button(__('Filter', 'x0pa-hiring'), 'secondary', 'submit_filter', false); ?>

        <?php if (!empty($filter_page_type)) : ?>
            <a href="<?php echo admin_url('edit.php?post_type=x0pa_hiring_page&page=x0pa-manage-pages'); ?>"
               class="button">
                <?php _e('Clear Filter', 'x0pa-hiring'); ?>
            </a>
        <?php endif; ?>
    </form>

    <!-- Statistics -->
    <div style="background: #fff; padding: 15px; border: 1px solid #ccd0d4; margin-bottom: 20px;">
        <?php
        $total_count = count($hiring_pages);
        $iq_count = 0;
        $jd_count = 0;

        foreach ($hiring_pages as $page) {
            $page_type = get_post_meta($page->ID, '_x0pa_page_type', true);
            if ($page_type === 'interview-questions') {
                $iq_count++;
            } elseif ($page_type === 'job-description') {
                $jd_count++;
            }
        }
        ?>
        <strong><?php _e('Statistics:', 'x0pa-hiring'); ?></strong>
        <span style="margin-left: 15px;">
            <?php printf(__('Total Pages: %d', 'x0pa-hiring'), $total_count); ?>
        </span>
        <span style="margin-left: 15px;">
            <?php printf(__('Interview Questions: %d', 'x0pa-hiring'), $iq_count); ?>
        </span>
        <span style="margin-left: 15px;">
            <?php printf(__('Job Descriptions: %d', 'x0pa-hiring'), $jd_count); ?>
        </span>
    </div>

    <?php if (empty($hiring_pages)) : ?>
        <div class="x0pa-message error">
            <?php _e('No hiring pages found. Upload CSV files to create pages.', 'x0pa-hiring'); ?>
        </div>
    <?php else : ?>
        <form method="post" action="">
            <?php wp_nonce_field('bulk_delete_pages', 'bulk_delete_nonce'); ?>
            <input type="hidden" name="action" value="bulk_delete">

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <td class="manage-column column-cb check-column">
                            <input type="checkbox" id="cb-select-all">
                        </td>
                        <th class="manage-column"><?php _e('Job Title', 'x0pa-hiring'); ?></th>
                        <th class="manage-column"><?php _e('Page Type', 'x0pa-hiring'); ?></th>
                        <th class="manage-column"><?php _e('Last Updated', 'x0pa-hiring'); ?></th>
                        <th class="manage-column"><?php _e('URL', 'x0pa-hiring'); ?></th>
                        <th class="manage-column"><?php _e('Actions', 'x0pa-hiring'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($hiring_pages as $page) :
                        $job_title = get_post_meta($page->ID, '_x0pa_job_title', true);
                        $page_type = get_post_meta($page->ID, '_x0pa_page_type', true);
                        $last_updated = get_post_meta($page->ID, '_x0pa_last_updated', true);
                        $page_url = get_permalink($page->ID);

                        $page_type_labels = array(
                            'interview-questions' => __('Interview Questions', 'x0pa-hiring'),
                            'job-description'     => __('Job Description', 'x0pa-hiring'),
                        );
                        $page_type_label = $page_type_labels[$page_type] ?? $page_type;
                    ?>
                        <tr>
                            <th scope="row" class="check-column">
                                <input type="checkbox" name="post_ids[]" value="<?php echo esc_attr($page->ID); ?>">
                            </th>
                            <td>
                                <strong><?php echo esc_html($job_title); ?></strong>
                            </td>
                            <td>
                                <?php
                                $type_badge_color = $page_type === 'interview-questions' ? '#2271b1' : '#50575e';
                                ?>
                                <span style="display: inline-block; padding: 3px 8px; background: <?php echo esc_attr($type_badge_color); ?>; color: #fff; border-radius: 3px; font-size: 12px;">
                                    <?php echo esc_html($page_type_label); ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                if ($last_updated) {
                                    echo esc_html(date_i18n(get_option('date_format'), strtotime($last_updated)));
                                } else {
                                    echo '—';
                                }
                                ?>
                            </td>
                            <td>
                                <a href="<?php echo esc_url($page_url); ?>" target="_blank" rel="noopener">
                                    <?php _e('View Page', 'x0pa-hiring'); ?>
                                    <span class="dashicons dashicons-external" style="font-size: 14px; vertical-align: middle;"></span>
                                </a>
                            </td>
                            <td>
                                <a href="<?php echo get_edit_post_link($page->ID); ?>" class="button button-small">
                                    <?php _e('Edit', 'x0pa-hiring'); ?>
                                </a>
                                <a href="<?php echo get_delete_post_link($page->ID); ?>"
                                   class="button button-small"
                                   onclick="return confirm('<?php esc_attr_e('Are you sure you want to delete this page?', 'x0pa-hiring'); ?>');">
                                    <?php _e('Delete', 'x0pa-hiring'); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p class="submit">
                <?php submit_button(__('Delete Selected', 'x0pa-hiring'), 'delete', 'submit_bulk_delete', false); ?>
            </p>
        </form>
    <?php endif; ?>

    <!-- Hub Page Info -->
    <div style="background: #f0f6fc; border-left: 4px solid #0073aa; padding: 15px; margin-top: 20px;">
        <h3 style="margin-top: 0;"><?php _e('Hub Page Information', 'x0pa-hiring'); ?></h3>
        <?php
        $hub_page_id = get_option('x0pa_hiring_hub_page_id');
        if ($hub_page_id) {
            $hub_page_url = get_permalink($hub_page_id);
            ?>
            <p>
                <?php _e('The hiring hub page is automatically maintained and lists all hiring pages:', 'x0pa-hiring'); ?>
                <br>
                <a href="<?php echo esc_url($hub_page_url); ?>" target="_blank" rel="noopener">
                    <?php echo esc_url($hub_page_url); ?>
                    <span class="dashicons dashicons-external" style="font-size: 14px; vertical-align: middle;"></span>
                </a>
            </p>
            <p>
                <a href="<?php echo get_edit_post_link($hub_page_id); ?>" class="button">
                    <?php _e('Edit Hub Page', 'x0pa-hiring'); ?>
                </a>
            </p>
        <?php } else { ?>
            <p><?php _e('Hub page not found. Please reactivate the plugin to create it.', 'x0pa-hiring'); ?></p>
        <?php } ?>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Select all checkbox functionality
    $('#cb-select-all').on('change', function() {
        $('input[name="post_ids[]"]').prop('checked', $(this).prop('checked'));
    });

    // Bulk delete confirmation
    $('input[name="submit_bulk_delete"]').on('click', function(e) {
        var checked = $('input[name="post_ids[]"]:checked').length;
        if (checked === 0) {
            e.preventDefault();
            alert('<?php echo esc_js(__('Please select at least one page to delete.', 'x0pa-hiring')); ?>');
            return false;
        }

        if (!confirm('<?php echo esc_js(__('Are you sure you want to delete the selected pages? This action cannot be undone.', 'x0pa-hiring')); ?>')) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
