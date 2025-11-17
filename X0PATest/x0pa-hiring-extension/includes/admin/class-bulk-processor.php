<?php
/**
 * Bulk CSV Processor
 *
 * @package X0PA_Hiring_Extension
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * X0PA Hiring Bulk Processor Class
 */
class X0PA_Hiring_Bulk_Processor {

    /**
     * Success count
     *
     * @var int
     */
    private $success_count = 0;

    /**
     * Error count
     *
     * @var int
     */
    private $error_count = 0;

    /**
     * Error messages
     *
     * @var array
     */
    private $errors = array();

    /**
     * Process CSV data and create posts
     *
     * @param array  $csv_data  CSV data array
     * @param string $page_type Page type (interview-questions or job-description)
     * @return array Processing result
     */
    public function process_csv($csv_data, $page_type) {
        if (empty($csv_data)) {
            return array(
                'success' => false,
                'message' => __('CSV file is empty', 'x0pa-hiring'),
            );
        }

        // Reset counters
        $this->success_count = 0;
        $this->error_count = 0;
        $this->errors = array();

        // Process each row
        foreach ($csv_data as $index => $row) {
            $row_number = $index + 2; // Account for header row and 0-index

            if ($page_type === 'interview-questions') {
                $this->process_interview_questions_row($row, $row_number);
            } elseif ($page_type === 'job-description') {
                $this->process_job_description_row($row, $row_number);
            }
        }

        // Trigger hub page regeneration
        $this->regenerate_hub_page();

        // Compile result message
        $message = $this->get_result_message($page_type);

        return array(
            'success'       => $this->error_count === 0,
            'message'       => $message,
            'success_count' => $this->success_count,
            'error_count'   => $this->error_count,
            'errors'        => $this->errors,
        );
    }

    /**
     * Process interview questions row
     *
     * @param array $row        CSV row data
     * @param int   $row_number Row number for error reporting
     */
    private function process_interview_questions_row($row, $row_number) {
        // Validate required fields
        if (empty($row['job_title'])) {
            $this->add_error($row_number, __('Missing job_title', 'x0pa-hiring'));
            return;
        }

        $job_title = sanitize_text_field($row['job_title']);
        $last_updated = isset($row['last_updated']) ? sanitize_text_field($row['last_updated']) : current_time('Y-m-d');

        // DEBUG: Log CSV row processing
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("X0PA CSV Import - Processing row {$row_number}: {$job_title}");
        }

        // Build content JSON from sections
        $content_data = array();
        $section_num = 1;

        while (isset($row["section_{$section_num}_id"])) {
            $section = array(
                'id'    => sanitize_text_field($row["section_{$section_num}_id"]),
                'title' => sanitize_text_field($row["section_{$section_num}_title"]),
            );

            // Parse questions JSON if present
            if (isset($row["section_{$section_num}_questions_json"])) {
                $questions_json = $row["section_{$section_num}_questions_json"];

                // DEBUG: Log raw JSON before processing
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log("X0PA CSV Import - Section {$section_num} raw JSON (first 200 chars): " . substr($questions_json, 0, 200));
                }

                // Remove any BOM or leading/trailing whitespace
                $questions_json = trim($questions_json);
                $questions_json = str_replace("\xEF\xBB\xBF", '', $questions_json); // Remove UTF-8 BOM

                // Try to decode JSON
                $questions = json_decode($questions_json, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($questions)) {
                    $section['questions'] = $questions;

                    if (defined('WP_DEBUG') && WP_DEBUG) {
                        error_log("X0PA CSV Import - Section {$section_num} successfully parsed " . count($questions) . " questions");
                    }
                } else {
                    $json_error = json_last_error_msg();
                    $section['questions'] = array();

                    if (defined('WP_DEBUG') && WP_DEBUG) {
                        error_log("X0PA CSV Import - Section {$section_num} JSON parse error: {$json_error}");
                    }

                    $this->add_error($row_number, sprintf(
                        __('Section %d JSON parse error: %s', 'x0pa-hiring'),
                        $section_num,
                        $json_error
                    ));
                }
            } else {
                $section['questions'] = array();

                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log("X0PA CSV Import - Section {$section_num} has no questions_json field");
                }
            }

            $content_data["section_{$section_num}"] = $section;
            $section_num++;
        }

        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("X0PA CSV Import - Created content data with " . ($section_num - 1) . " sections");
        }

        // Create post
        $this->create_hiring_post($job_title, 'interview-questions', $last_updated, $content_data, $row_number);
    }

    /**
     * Process job description row
     *
     * @param array $row        CSV row data
     * @param int   $row_number Row number for error reporting
     */
    private function process_job_description_row($row, $row_number) {
        // Validate required fields
        if (empty($row['job_title'])) {
            $this->add_error($row_number, __('Missing job_title', 'x0pa-hiring'));
            return;
        }

        $job_title = sanitize_text_field($row['job_title']);
        $last_updated = isset($row['last_updated']) ? sanitize_text_field($row['last_updated']) : current_time('Y-m-d');

        // Build content JSON
        $content_data = array(
            'objectives'        => isset($row['objectives']) ? sanitize_textarea_field($row['objectives']) : '',
            'responsibilities'  => isset($row['responsibilities']) ? sanitize_textarea_field($row['responsibilities']) : '',
            'required_skills'   => isset($row['required_skills']) ? sanitize_textarea_field($row['required_skills']) : '',
            'preferred_skills'  => isset($row['preferred_skills']) ? sanitize_textarea_field($row['preferred_skills']) : '',
            'what_role_does'    => isset($row['what_role_does']) ? sanitize_textarea_field($row['what_role_does']) : '',
            'skills_to_look_for' => isset($row['skills_to_look_for']) ? sanitize_textarea_field($row['skills_to_look_for']) : '',
        );

        // Create post
        $this->create_hiring_post($job_title, 'job-description', $last_updated, $content_data, $row_number);
    }

    /**
     * Create hiring post
     *
     * @param string $job_title    Job title
     * @param string $page_type    Page type
     * @param string $last_updated Last updated date
     * @param array  $content_data Content data
     * @param int    $row_number   Row number for error reporting
     */
    private function create_hiring_post($job_title, $page_type, $last_updated, $content_data, $row_number) {
        // Generate post title
        $page_type_label = $page_type === 'interview-questions' ? 'Interview Questions' : 'Job Description';
        $post_title = sprintf('%s %s', $job_title, $page_type_label);

        // Generate post slug
        $job_slug = sanitize_title($job_title);
        $post_slug = sprintf('%s-%s', $job_slug, $page_type);

        // Check if post already exists
        $existing_post = get_page_by_path($post_slug, OBJECT, 'x0pa_hiring_page');

        $post_data = array(
            'post_title'  => $post_title,
            'post_name'   => $post_slug,
            'post_status' => 'publish',
            'post_type'   => 'x0pa_hiring_page',
            'post_author' => get_current_user_id(),
        );

        if ($existing_post) {
            // Update existing post
            $post_data['ID'] = $existing_post->ID;
            $post_id = wp_update_post($post_data, true);
        } else {
            // Create new post
            $post_id = wp_insert_post($post_data, true);
        }

        if (is_wp_error($post_id)) {
            $this->add_error($row_number, sprintf(
                __('Failed to create post for %s: %s', 'x0pa-hiring'),
                $job_title,
                $post_id->get_error_message()
            ));
            return;
        }

        // Encode content data to JSON
        $content_json = wp_json_encode($content_data);

        // DEBUG: Log what's being stored
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("X0PA CSV Import - Created/updated post ID {$post_id} for {$job_title}");
            error_log("X0PA CSV Import - Content JSON length: " . strlen($content_json));
            error_log("X0PA CSV Import - Content JSON (first 500 chars): " . substr($content_json, 0, 500));
        }

        // Update post meta
        update_post_meta($post_id, '_x0pa_page_type', $page_type);
        update_post_meta($post_id, '_x0pa_job_title', $job_title);
        update_post_meta($post_id, '_x0pa_last_updated', $last_updated);
        update_post_meta($post_id, '_x0pa_content_json', $content_json);

        // DEBUG: Verify what was stored
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $stored_json = get_post_meta($post_id, '_x0pa_content_json', true);
            error_log("X0PA CSV Import - Verified stored JSON length: " . strlen($stored_json));

            // Test decode
            $test_decode = json_decode($stored_json, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                error_log("X0PA CSV Import - Stored JSON decodes successfully, keys: " . print_r(array_keys($test_decode ?: []), true));
            } else {
                error_log("X0PA CSV Import - ERROR: Stored JSON does not decode! Error: " . json_last_error_msg());
            }
        }

        $this->success_count++;
    }

    /**
     * Add error message
     *
     * @param int    $row_number Row number
     * @param string $message    Error message
     */
    private function add_error($row_number, $message) {
        $this->error_count++;
        $this->errors[] = sprintf(__('Row %d: %s', 'x0pa-hiring'), $row_number, $message);
    }

    /**
     * Get result message
     *
     * @param string $page_type Page type
     * @return string Result message
     */
    private function get_result_message($page_type) {
        $page_type_label = $page_type === 'interview-questions' ? 'Interview Questions' : 'Job Description';

        $message = sprintf(
            __('%s: Successfully processed %d page(s)', 'x0pa-hiring'),
            $page_type_label,
            $this->success_count
        );

        if ($this->error_count > 0) {
            $message .= sprintf(
                __(', %d error(s) encountered', 'x0pa-hiring'),
                $this->error_count
            );

            if (!empty($this->errors)) {
                $message .= '<br><strong>' . __('Errors:', 'x0pa-hiring') . '</strong><ul>';
                foreach ($this->errors as $error) {
                    $message .= '<li>' . esc_html($error) . '</li>';
                }
                $message .= '</ul>';
            }
        }

        return $message;
    }

    /**
     * Regenerate hub page content
     */
    private function regenerate_hub_page() {
        $hub_page_id = get_option('x0pa_hiring_hub_page_id');

        if (!$hub_page_id) {
            return;
        }

        // Get all hiring pages
        $args = array(
            'post_type'      => 'x0pa_hiring_page',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'meta_value',
            'meta_key'       => '_x0pa_job_title',
            'order'          => 'ASC',
        );

        $hiring_pages = get_posts($args);

        // Group pages by job title
        $grouped_pages = array();
        foreach ($hiring_pages as $page) {
            $job_title = get_post_meta($page->ID, '_x0pa_job_title', true);
            if (!isset($grouped_pages[$job_title])) {
                $grouped_pages[$job_title] = array();
            }
            $grouped_pages[$job_title][] = $page;
        }

        // Build hub page content
        $content = '<!-- wp:heading --><h2>Hiring Resources</h2><!-- /wp:heading -->' . "\n\n";
        $content .= '<!-- wp:paragraph --><p>Explore our comprehensive collection of hiring resources including interview questions and job descriptions.</p><!-- /wp:paragraph -->' . "\n\n";

        foreach ($grouped_pages as $job_title => $pages) {
            $content .= sprintf('<!-- wp:heading {"level":3} --><h3>%s</h3><!-- /wp:heading -->' . "\n", esc_html($job_title));
            $content .= '<!-- wp:list --><ul>' . "\n";

            foreach ($pages as $page) {
                $page_type = get_post_meta($page->ID, '_x0pa_page_type', true);
                $page_type_label = $page_type === 'interview-questions' ? 'Interview Questions' : 'Job Description';
                $content .= sprintf(
                    '<li><a href="%s">%s</a></li>' . "\n",
                    get_permalink($page->ID),
                    esc_html($page_type_label)
                );
            }

            $content .= '</ul><!-- /wp:list -->' . "\n\n";
        }

        // Update hub page
        wp_update_post(array(
            'ID'           => $hub_page_id,
            'post_content' => $content,
        ));
    }
}
