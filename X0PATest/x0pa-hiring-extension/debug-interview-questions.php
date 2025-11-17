<?php
/**
 * Debug Script for Interview Questions Pages
 *
 * Add this to your WordPress site to debug the interview questions issue.
 *
 * USAGE:
 * 1. Upload this file to your WordPress root directory
 * 2. Visit: https://yoursite.com/debug-interview-questions.php
 * 3. Look for any errors or issues in the output
 * 4. DELETE this file after debugging
 *
 * @package X0PA_Hiring_Extension
 */

// Load WordPress
require_once(__DIR__ . '/wp-load.php');

// Security check
if (!current_user_can('manage_options')) {
    wp_die('Unauthorized access');
}

// Set content type
header('Content-Type: text/plain; charset=utf-8');

echo "=== X0PA HIRING EXTENSION DEBUG REPORT ===\n\n";

// Check if plugin is active
echo "1. PLUGIN STATUS\n";
echo "   Active: " . (is_plugin_active('x0pa-hiring-extension/x0pa-hiring-extension.php') ? 'YES' : 'NO') . "\n\n";

// Get all hiring pages
echo "2. HIRING PAGES\n";
$args = array(
    'post_type'      => 'x0pa_hiring_page',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
);
$pages = get_posts($args);

echo "   Total pages found: " . count($pages) . "\n\n";

// Analyze each page
foreach ($pages as $page) {
    echo "   --- PAGE: " . $page->post_title . " ---\n";
    echo "   Post ID: " . $page->ID . "\n";
    echo "   Slug: " . $page->post_name . "\n";
    echo "   URL: " . get_permalink($page->ID) . "\n";

    // Get meta data
    $page_type = get_post_meta($page->ID, '_x0pa_page_type', true);
    $job_title = get_post_meta($page->ID, '_x0pa_job_title', true);
    $last_updated = get_post_meta($page->ID, '_x0pa_last_updated', true);
    $content_json = get_post_meta($page->ID, '_x0pa_content_json', true);

    echo "   Page Type: " . ($page_type ?: 'NOT SET') . "\n";
    echo "   Job Title: " . ($job_title ?: 'NOT SET') . "\n";
    echo "   Last Updated: " . ($last_updated ?: 'NOT SET') . "\n";
    echo "   Content JSON Length: " . strlen($content_json) . " bytes\n";

    // Try to decode JSON
    $content_data = json_decode($content_json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "   ❌ JSON DECODE ERROR: " . json_last_error_msg() . "\n";
        echo "   Raw JSON (first 500 chars):\n";
        echo "   " . substr($content_json, 0, 500) . "\n";
    } else {
        echo "   ✓ JSON decodes successfully\n";

        if (empty($content_data)) {
            echo "   ❌ Content data is EMPTY\n";
        } else {
            echo "   Content structure keys: " . implode(', ', array_keys($content_data)) . "\n";

            // Check sections for interview questions
            if ($page_type === 'interview-questions') {
                $total_questions = 0;

                for ($i = 1; $i <= 3; $i++) {
                    $section_key = "section_{$i}";

                    if (isset($content_data[$section_key])) {
                        $section = $content_data[$section_key];
                        echo "   \n";
                        echo "   Section {$i}:\n";
                        echo "     ID: " . ($section['id'] ?? 'NOT SET') . "\n";
                        echo "     Title: " . ($section['title'] ?? 'NOT SET') . "\n";

                        if (isset($section['questions'])) {
                            if (is_array($section['questions'])) {
                                $count = count($section['questions']);
                                $total_questions += $count;
                                echo "     Questions: {$count}\n";

                                // Show first question as sample
                                if ($count > 0) {
                                    $first_q = $section['questions'][0];
                                    echo "     Sample question: " . (isset($first_q['question']) ? substr($first_q['question'], 0, 80) . '...' : 'NO QUESTION TEXT') . "\n";
                                }
                            } else {
                                echo "     ❌ Questions field is NOT an array: " . gettype($section['questions']) . "\n";
                            }
                        } else {
                            echo "     ❌ NO questions field found\n";
                        }
                    }
                }

                echo "   \n";
                echo "   Total Questions Across All Sections: {$total_questions}\n";

                if ($total_questions === 0) {
                    echo "   ❌ PROBLEM: Zero questions found!\n";
                    echo "   \n";
                    echo "   DEBUGGING INFO:\n";
                    echo "   Full content_data structure:\n";
                    echo "   " . print_r($content_data, true) . "\n";
                }
            }
        }
    }

    echo "\n";
}

echo "\n3. TEMPLATE FILE CHECK\n";
$template_file = WP_PLUGIN_DIR . '/x0pa-hiring-extension/includes/templates/wordpress-interview-questions.php';
echo "   Template exists: " . (file_exists($template_file) ? 'YES' : 'NO') . "\n";
if (file_exists($template_file)) {
    echo "   Template size: " . filesize($template_file) . " bytes\n";
    echo "   Template modified: " . date('Y-m-d H:i:s', filemtime($template_file)) . "\n";
}

echo "\n4. GENERATOR FILE CHECK\n";
$generator_file = WP_PLUGIN_DIR . '/x0pa-hiring-extension/includes/generators/interview-questions-generator.php';
echo "   Generator exists: " . (file_exists($generator_file) ? 'YES' : 'NO') . "\n";
if (file_exists($generator_file)) {
    echo "   Generator size: " . filesize($generator_file) . " bytes\n";
    echo "   Generator modified: " . date('Y-m-d H:i:s', filemtime($generator_file)) . "\n";
}

echo "\n5. WORDPRESS DEBUG MODE\n";
echo "   WP_DEBUG: " . (defined('WP_DEBUG') && WP_DEBUG ? 'ENABLED' : 'DISABLED') . "\n";
echo "   WP_DEBUG_LOG: " . (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG ? 'ENABLED' : 'DISABLED') . "\n";

if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
    $log_file = WP_CONTENT_DIR . '/debug.log';
    if (file_exists($log_file)) {
        echo "   Debug log exists: YES\n";
        echo "   Debug log size: " . filesize($log_file) . " bytes\n";
        echo "   \n";
        echo "   Last 20 lines of debug.log:\n";
        echo "   " . str_repeat('-', 70) . "\n";

        $lines = file($log_file);
        $last_lines = array_slice($lines, -20);
        foreach ($last_lines as $line) {
            echo "   " . trim($line) . "\n";
        }
    } else {
        echo "   Debug log: NOT FOUND\n";
    }
}

echo "\n=== END DEBUG REPORT ===\n";
echo "\nREMEMBER TO DELETE THIS FILE AFTER DEBUGGING!\n";
