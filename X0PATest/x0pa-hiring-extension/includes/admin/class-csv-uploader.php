<?php
/**
 * CSV Upload Handler
 *
 * @package X0PA_Hiring_Extension
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * X0PA Hiring CSV Uploader Class
 */
class X0PA_Hiring_CSV_Uploader {

    /**
     * Allowed MIME types for CSV upload
     *
     * @var array
     */
    private static $allowed_mime_types = array(
        'text/csv',
        'text/plain',
        'application/csv',
        'text/comma-separated-values',
        'application/excel',
        'application/vnd.ms-excel',
        'application/vnd.msexcel',
    );

    /**
     * Maximum file size in bytes (10MB)
     *
     * @var int
     */
    private static $max_file_size = 10485760;

    /**
     * Handle CSV file upload
     *
     * @return array Result array with success status and message
     */
    public static function handle_upload() {
        // Verify nonce
        if (!isset($_POST['x0pa_upload_nonce']) ||
            !wp_verify_nonce($_POST['x0pa_upload_nonce'], 'x0pa_upload_csv')) {
            return array(
                'success' => false,
                'message' => __('Security check failed. Please try again.', 'x0pa-hiring'),
            );
        }

        // Check user capabilities
        if (!current_user_can('manage_options')) {
            return array(
                'success' => false,
                'message' => __('You do not have permission to upload files.', 'x0pa-hiring'),
            );
        }

        $results = array();
        $overall_success = true;

        // Process Interview Questions CSV
        if (isset($_FILES['interview_questions_csv']) && $_FILES['interview_questions_csv']['error'] !== UPLOAD_ERR_NO_FILE) {
            $iq_result = self::process_file($_FILES['interview_questions_csv'], 'interview-questions');
            $results['interview_questions'] = $iq_result;
            if (!$iq_result['success']) {
                $overall_success = false;
            }
        }

        // Process Job Description CSV
        if (isset($_FILES['job_description_csv']) && $_FILES['job_description_csv']['error'] !== UPLOAD_ERR_NO_FILE) {
            $jd_result = self::process_file($_FILES['job_description_csv'], 'job-description');
            $results['job_description'] = $jd_result;
            if (!$jd_result['success']) {
                $overall_success = false;
            }
        }

        // Check if at least one file was uploaded
        if (empty($results)) {
            return array(
                'success' => false,
                'message' => __('Please select at least one CSV file to upload.', 'x0pa-hiring'),
            );
        }

        // Compile overall message
        $messages = array();
        foreach ($results as $type => $result) {
            if (isset($result['message'])) {
                $messages[] = $result['message'];
            }
        }

        return array(
            'success' => $overall_success,
            'message' => implode('<br>', $messages),
            'results' => $results,
        );
    }

    /**
     * Process individual CSV file
     *
     * @param array  $file      Uploaded file data
     * @param string $page_type Page type (interview-questions or job-description)
     * @return array Result array
     */
    private static function process_file($file, $page_type) {
        // Validate file upload
        $validation = self::validate_file($file);
        if (!$validation['valid']) {
            return array(
                'success' => false,
                'message' => sprintf(
                    __('Error uploading %s: %s', 'x0pa-hiring'),
                    $page_type === 'interview-questions' ? 'Interview Questions' : 'Job Description',
                    $validation['error']
                ),
            );
        }

        // Read CSV file
        $csv_data = self::read_csv($file['tmp_name']);
        if ($csv_data === false) {
            return array(
                'success' => false,
                'message' => sprintf(
                    __('Error reading CSV file for %s', 'x0pa-hiring'),
                    $page_type === 'interview-questions' ? 'Interview Questions' : 'Job Description'
                ),
            );
        }

        // Process CSV data with bulk processor
        require_once X0PA_HIRING_PLUGIN_DIR . 'includes/admin/class-bulk-processor.php';
        $processor = new X0PA_Hiring_Bulk_Processor();
        $result = $processor->process_csv($csv_data, $page_type);

        return $result;
    }

    /**
     * Validate uploaded file
     *
     * @param array $file Uploaded file data
     * @return array Validation result
     */
    private static function validate_file($file) {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error_messages = array(
                UPLOAD_ERR_INI_SIZE   => __('File exceeds upload_max_filesize directive in php.ini', 'x0pa-hiring'),
                UPLOAD_ERR_FORM_SIZE  => __('File exceeds MAX_FILE_SIZE directive in HTML form', 'x0pa-hiring'),
                UPLOAD_ERR_PARTIAL    => __('File was only partially uploaded', 'x0pa-hiring'),
                UPLOAD_ERR_NO_FILE    => __('No file was uploaded', 'x0pa-hiring'),
                UPLOAD_ERR_NO_TMP_DIR => __('Missing temporary folder', 'x0pa-hiring'),
                UPLOAD_ERR_CANT_WRITE => __('Failed to write file to disk', 'x0pa-hiring'),
                UPLOAD_ERR_EXTENSION  => __('File upload stopped by extension', 'x0pa-hiring'),
            );

            return array(
                'valid' => false,
                'error' => $error_messages[$file['error']] ?? __('Unknown upload error', 'x0pa-hiring'),
            );
        }

        // Check file size
        if ($file['size'] > self::$max_file_size) {
            return array(
                'valid' => false,
                'error' => sprintf(
                    __('File size exceeds maximum allowed size of %s MB', 'x0pa-hiring'),
                    self::$max_file_size / 1048576
                ),
            );
        }

        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime_type, self::$allowed_mime_types, true)) {
            // Also check file extension as fallback
            $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($file_extension !== 'csv') {
                return array(
                    'valid' => false,
                    'error' => __('Invalid file type. Please upload a CSV file.', 'x0pa-hiring'),
                );
            }
        }

        return array('valid' => true);
    }

    /**
     * Read CSV file and return data
     *
     * @param string $file_path Path to CSV file
     * @return array|false CSV data or false on failure
     */
    private static function read_csv($file_path) {
        if (!file_exists($file_path) || !is_readable($file_path)) {
            return false;
        }

        $csv_data = array();
        $handle = fopen($file_path, 'r');

        if ($handle === false) {
            return false;
        }

        // Read header row
        $headers = fgetcsv($handle);
        if ($headers === false) {
            fclose($handle);
            return false;
        }

        // Trim and normalize headers
        $headers = array_map('trim', $headers);

        // Read data rows
        while (($row = fgetcsv($handle)) !== false) {
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Combine headers with row data
            $row_data = array_combine($headers, $row);
            if ($row_data !== false) {
                $csv_data[] = $row_data;
            }
        }

        fclose($handle);

        return $csv_data;
    }

    /**
     * Get upload status message
     *
     * @param array $results Upload results
     * @return string Formatted message
     */
    public static function get_status_message($results) {
        if (!isset($results['success'])) {
            return '';
        }

        $class = $results['success'] ? 'success' : 'error';
        $message = $results['message'] ?? '';

        return sprintf(
            '<div class="x0pa-message %s">%s</div>',
            esc_attr($class),
            wp_kses_post($message)
        );
    }
}
