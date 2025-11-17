<?php
/**
 * Data Structure Verification Script
 *
 * Place this file in the plugin root and access via:
 * https://yoursite.com/wp-content/plugins/x0pa-hiring-extension/verify-data-structure.php
 *
 * ⚠️ DELETE THIS FILE AFTER USE - SECURITY RISK
 */

// Load WordPress
require_once('../../../wp-load.php');

// Security check
if (!current_user_can('manage_options')) {
    die('Access denied. You must be an administrator.');
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>X0PA Data Structure Verification</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .section { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { color: #22c55e; font-weight: bold; }
        .error { color: #ef4444; font-weight: bold; }
        .warning { color: #f59e0b; font-weight: bold; }
        pre { background: #1e293b; color: #e2e8f0; padding: 15px; border-radius: 5px; overflow-x: auto; }
        h1 { color: #1e293b; }
        h2 { color: #475569; border-bottom: 2px solid #cbd5e1; padding-bottom: 10px; }
        .status { display: inline-block; padding: 5px 10px; border-radius: 3px; margin-left: 10px; }
        .status.ok { background: #22c55e; color: white; }
        .status.fail { background: #ef4444; color: white; }
    </style>
</head>
<body>

<h1>🔍 X0PA Data Structure Verification</h1>

<?php
// Get all interview question pages
$args = array(
    'post_type'      => 'x0pa_hiring_page',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'meta_key'       => '_x0pa_page_type',
    'meta_value'     => 'interview-questions',
);

$posts = get_posts($args);

if (empty($posts)) {
    echo '<div class="section"><p class="error">❌ No interview question pages found!</p>';
    echo '<p>Please upload the CSV file via <strong>WordPress Admin → X0PA Hiring → Upload Pages</strong></p></div>';
} else {
    echo '<div class="section">';
    echo '<h2>Found ' . count($posts) . ' Interview Question Pages</h2>';
    echo '</div>';

    foreach ($posts as $post) {
        $job_title = get_post_meta($post->ID, '_x0pa_job_title', true);
        $content_json = get_post_meta($post->ID, '_x0pa_content_json', true);
        $content_data = json_decode($content_json, true);

        echo '<div class="section">';
        echo '<h2>' . esc_html($job_title) . ' <small>(Post ID: ' . $post->ID . ')</small></h2>';

        // Check if content_data decoded successfully
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo '<p class="error">❌ JSON Decode Error: ' . json_last_error_msg() . '</p>';
            echo '<h3>Raw JSON (first 500 characters):</h3>';
            echo '<pre>' . esc_html(substr($content_json, 0, 500)) . '</pre>';
            continue;
        }

        if (empty($content_data)) {
            echo '<p class="error">❌ Content data is empty</p>';
            continue;
        }

        // Check data structure
        $total_questions = 0;
        $sections_found = 0;

        for ($i = 1; $i <= 3; $i++) {
            $section_key = "section_{$i}";

            if (isset($content_data[$section_key])) {
                $sections_found++;
                $section = $content_data[$section_key];

                echo '<h3>Section ' . $i . ': ';

                if (isset($section['title'])) {
                    echo '<span class="success">' . esc_html($section['title']) . '</span>';
                } else {
                    echo '<span class="error">Missing Title</span>';
                }

                echo '</h3>';

                // Check structure
                $structure_ok = true;
                $structure_issues = [];

                if (!isset($section['id'])) {
                    $structure_ok = false;
                    $structure_issues[] = 'Missing "id" key';
                }

                if (!isset($section['title'])) {
                    $structure_ok = false;
                    $structure_issues[] = 'Missing "title" key';
                }

                if (!isset($section['questions'])) {
                    $structure_ok = false;
                    $structure_issues[] = 'Missing "questions" key';
                } else if (!is_array($section['questions'])) {
                    $structure_ok = false;
                    $structure_issues[] = '"questions" is not an array (type: ' . gettype($section['questions']) . ')';
                } else {
                    $question_count = count($section['questions']);
                    $total_questions += $question_count;

                    echo '<p>✅ <strong>' . $question_count . ' questions</strong></p>';

                    // Show first question as sample
                    if ($question_count > 0) {
                        $first_q = $section['questions'][0];
                        echo '<p><strong>Sample Question:</strong></p>';
                        echo '<pre>' . esc_html(print_r($first_q, true)) . '</pre>';
                    }
                }

                if (!$structure_ok) {
                    echo '<p class="error">❌ Structure Issues:</p><ul>';
                    foreach ($structure_issues as $issue) {
                        echo '<li class="error">' . esc_html($issue) . '</li>';
                    }
                    echo '</ul>';
                }

            } else {
                // Section doesn't exist - this is ok, not all pages have 3 sections
                if ($i === 1) {
                    echo '<p class="error">❌ Section 1 is missing - this is required!</p>';
                }
            }
        }

        // Summary
        echo '<hr>';
        echo '<p><strong>Summary:</strong></p>';
        echo '<ul>';
        echo '<li>Sections found: <strong>' . $sections_found . '</strong></li>';
        echo '<li>Total questions: <strong>' . $total_questions . '</strong> ';

        if ($total_questions > 0) {
            echo '<span class="status ok">✅ OK</span>';
        } else {
            echo '<span class="status fail">❌ NO QUESTIONS</span>';
        }

        echo '</li>';
        echo '</ul>';

        // Show expected structure
        if ($total_questions === 0) {
            echo '<h3>❗ Expected Data Structure:</h3>';
            echo '<pre>';
echo htmlspecialchars('[
    "section_1" => [
        "id" => "strategic-marketing",
        "title" => "Strategic Marketing & Planning",
        "questions" => [
            [
                "question" => "How do you develop...",
                "what_to_listen_for" => ["point 1", "point 2"]
            ]
        ]
    ]
]');
            echo '</pre>';

            echo '<h3>📌 Actual Structure Found:</h3>';
            echo '<pre>' . esc_html(print_r($content_data, true)) . '</pre>';
        }

        echo '</div>';
    }
}
?>

<div class="section">
    <h2>🔧 Troubleshooting Steps</h2>

    <?php if (empty($posts)): ?>
        <ol>
            <li>Go to <strong>WordPress Admin → X0PA Hiring → Upload Pages</strong></li>
            <li>Select <strong>"Interview Questions"</strong> from dropdown</li>
            <li>Upload the <code>comprehensive-interview-questions.csv</code> file</li>
            <li>Wait for success message</li>
            <li>Refresh this page to verify</li>
        </ol>
    <?php else: ?>
        <?php
        // Check if any pages have 0 questions
        $has_zero_questions = false;
        foreach ($posts as $post) {
            $content_json = get_post_meta($post->ID, '_x0pa_content_json', true);
            $content_data = json_decode($content_json, true);
            $total = 0;
            for ($i = 1; $i <= 3; $i++) {
                if (isset($content_data["section_{$i}"]['questions'])) {
                    $total += count($content_data["section_{$i}"]['questions']);
                }
            }
            if ($total === 0) {
                $has_zero_questions = true;
                break;
            }
        }

        if ($has_zero_questions):
        ?>
            <p class="error"><strong>Some pages have 0 questions. This means the CSV wasn't processed correctly.</strong></p>
            <ol>
                <li><strong>Enable WordPress Debug Mode</strong> (edit <code>wp-config.php</code>):
                    <pre>define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);</pre>
                </li>
                <li><strong>Delete all existing pages</strong>: Go to <strong>X0PA Hiring → All Pages</strong> and delete them</li>
                <li><strong>Re-upload the CSV</strong>: <strong>X0PA Hiring → Upload Pages</strong></li>
                <li><strong>Check the debug log</strong>: <code>/wp-content/debug.log</code> for errors</li>
                <li>Refresh this page to verify</li>
            </ol>

            <h3>Common Issues:</h3>
            <ul>
                <li><strong>CSV encoding:</strong> Make sure the CSV is UTF-8 encoded</li>
                <li><strong>JSON in CSV:</strong> The <code>section_1_questions_json</code> column must contain valid JSON</li>
                <li><strong>Quotes:</strong> JSON uses double quotes, and CSV may escape them as <code>""</code></li>
            </ul>
        <?php else: ?>
            <p class="success"><strong>✅ All pages have questions! Data structure is correct.</strong></p>
            <p>If the frontend still shows "0 questions", the issue is in the template rendering, not the data.</p>
            <ol>
                <li>Clear all caches (browser, WordPress, CDN)</li>
                <li>Visit the interview questions page</li>
                <li>Check browser console for JavaScript errors</li>
                <li>Check <code>/wp-content/debug.log</code> for template errors</li>
            </ol>
        <?php endif; ?>
    <?php endif; ?>

    <h3>⚠️ Security Warning</h3>
    <p class="warning"><strong>DELETE THIS FILE</strong> (<code>verify-data-structure.php</code>) after use. It exposes internal data.</p>
</div>

<div class="section">
    <p><small>Generated: <?php echo current_time('Y-m-d H:i:s'); ?></small></p>
</div>

</body>
</html>
