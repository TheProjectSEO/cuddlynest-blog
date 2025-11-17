<?php
/**
 * WordPress-Compatible Interview Questions Template
 * Based on original X0PA template design
 *
 * @package X0PA_Hiring_Extension
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include all generators
require_once X0PA_HIRING_PLUGIN_DIR . 'includes/generators/toc-generator.php';
require_once X0PA_HIRING_PLUGIN_DIR . 'includes/generators/interview-questions-generator.php';
require_once X0PA_HIRING_PLUGIN_DIR . 'includes/generators/hero-generator.php';
require_once X0PA_HIRING_PLUGIN_DIR . 'includes/generators/seo-meta-generator.php';

// Get WordPress post data
global $post;
$job_title = get_post_meta($post->ID, '_x0pa_job_title', true);
$last_updated = get_post_meta($post->ID, '_x0pa_last_updated', true);
$content_json = get_post_meta($post->ID, '_x0pa_content_json', true);

// DEBUG: Log raw data (remove after debugging)
if (defined('WP_DEBUG') && WP_DEBUG) {
    error_log('X0PA Debug - Post ID: ' . $post->ID);
    error_log('X0PA Debug - Job Title: ' . $job_title);
    error_log('X0PA Debug - Content JSON: ' . substr($content_json, 0, 500));
}

// Parse JSON content
$content_data = json_decode($content_json, true);

// DEBUG: Log parsed data
if (defined('WP_DEBUG') && WP_DEBUG) {
    error_log('X0PA Debug - JSON Decode Error: ' . json_last_error_msg());
    error_log('X0PA Debug - Content Data Keys: ' . print_r(array_keys($content_data ?: []), true));
}

// Page configuration
$hero_config = get_hero_config($job_title, 'interview-questions', $last_updated);
$seo_config = get_seo_config($job_title, 'interview-questions', $last_updated);

// Start output buffering to capture main content for SEO meta, TOC, and reading time
ob_start();
?>

<!-- Questions Overview Section - Dynamically Generated (will be inserted at top) -->

<?php
// Loop through sections (up to 3 sections)
for ($i = 1; $i <= 3; $i++) {
    // FIXED: Access nested section data structure correctly
    $section_key = "section_{$i}";

    if (!isset($content_data[$section_key]) || !is_array($content_data[$section_key])) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("X0PA Debug - Section {$i} not found or not an array");
        }
        continue;
    }

    $section = $content_data[$section_key];
    $section_id = $section['id'] ?? '';
    $section_title = $section['title'] ?? '';
    $questions = $section['questions'] ?? [];

    if (empty($section_id) || empty($section_title) || empty($questions)) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("X0PA Debug - Section {$i} missing required fields: id=" . ($section_id ?: 'empty') . ", title=" . ($section_title ?: 'empty') . ", questions=" . (empty($questions) ? 'empty' : count($questions)));
        }
        continue;
    }

    if (!is_array($questions)) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log("X0PA Debug - Section {$i} questions is not an array: " . gettype($questions));
        }
        continue;
    }

    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log("X0PA Debug - Rendering section {$i} with " . count($questions) . " questions");
    }
    ?>

<!-- Section <?php echo $i; ?>: <?php echo esc_html($section_title); ?> -->
<section id="<?php echo esc_attr($section_id); ?>" class="content-section">
    <div class="content-section__title"> <?php echo esc_html($section_title); ?> </div>
    <div class="content-section__body">

        <?php foreach ($questions as $q): ?>
            <!-- Question -->
            <article class="content-item">
                <h3 class="content-item__title"> <?php echo esc_html($q['question']); ?> </h3>

                <?php if (!empty($q['what_to_listen_for'])): ?>
                    <div class="content-item__box">
                        <h4 class="content-item__box-title"> What to Listen For: </h4>
                        <ul class="content-item__box-list">
                            <?php foreach ($q['what_to_listen_for'] as $point): ?>
                                <li class="content-item__box-list-item"><?php echo esc_html($point); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>

    </div>
</section>

<?php
}
?>

<?php $page_type = 'interview-questions'; include X0PA_HIRING_PLUGIN_DIR . 'includes/templates/partials/cta__cross-link.php'; ?>

<?php
// Get TEMPORARY buffered content (question sections only, no overview yet)
$questions_content = ob_get_clean();

// NOW start FINAL buffer and build complete content in correct order
ob_start();

// FIRST: Generate questions overview section from question content
echo generate_interview_questions_section($questions_content, $job_title);

// SECOND: Output all the question sections after the overview
echo $questions_content;

// Get FINAL buffered content (overview + questions = complete body)
$body_content = ob_get_clean();

// Now output the complete HTML with dynamically generated head
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    // Generate SEO meta tags (now that we have content for reading time/question count)
    echo generate_seo_meta($seo_config, $body_content);
    ?>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <?php include X0PA_HIRING_PLUGIN_DIR . 'includes/config/tailwind-config.php'; ?>
    <?php include X0PA_HIRING_PLUGIN_DIR . 'includes/assets/css/styles.php'; ?>

    <!-- PRESERVE:START -->
    <?php include X0PA_HIRING_PLUGIN_DIR . 'includes/tracking/tracking-codes-head.php'; ?>
    <!-- PRESERVE:END -->
</head>

<body class="bg-white">

    <!-- PRESERVE:START -->
    <?php include X0PA_HIRING_PLUGIN_DIR . 'includes/tracking/tracking-codes-body-start.php'; ?>
    <!-- PRESERVE:END -->

    <!-- Page Container -->
    <div class="hiring">
        <?php
        // Generate hero section (needs content for reading time calculation)
        echo generate_hero_section($hero_config, $body_content);

        // Generate TOC from content
        $sections = generate_toc_from_content($body_content);

        // Start layout
        echo '<div class="hiring__layout">';

        // Output the sidebar with generated jump links
        echo '<aside class="hiring__layout-sidebar-left">';
        echo render_jump_links($sections, 'questions-overview');
        echo '</aside>';

        // Generate and output the main content
        echo '<main class="hiring__layout-content">';

        // Output the complete content (overview already included in $body_content)
        echo $body_content;
        echo '</main>';
        ?>

        <!-- Author Section - Right Sidebar -->
        <aside class="hiring__layout-sidebar-right">
            <?php include X0PA_HIRING_PLUGIN_DIR . 'includes/templates/partials/sidebar-author.php'; ?>
            <?php include X0PA_HIRING_PLUGIN_DIR . 'includes/templates/partials/sidebar-newsletter.php'; ?>

            <!-- Sticky Resources Section: Links + CTA -->
            <?php
            $page_type = 'interview-questions';
            include X0PA_HIRING_PLUGIN_DIR . 'includes/templates/partials/sidebar-resources-sticky.php';
            ?>

        </aside>

        <?php echo '</div>'; // Close hiring__layout ?>

    </div> <!-- Close hiring -->

    <?php include X0PA_HIRING_PLUGIN_DIR . 'includes/templates/partials/cta__footer-how-x0pa-helps.php'; ?>

    <!-- PDF Download Gate Modal -->
    <!-- PRESERVE:START -->
    <?php include X0PA_HIRING_PLUGIN_DIR . 'includes/templates/partials/pdf-download-modal.php'; ?>
    <!-- PRESERVE:END -->

    <!-- Scripts (inside body, before optimizer ends, so modal exists when scripts run) -->
    <script src="<?php echo X0PA_HIRING_PLUGIN_URL; ?>includes/assets/js/hiring-script.js"></script>
    <script>
        initScrollSpy('questions-overview');
    </script>
    <script src="<?php echo X0PA_HIRING_PLUGIN_URL; ?>includes/assets/js/hubspot-newsletter.js"></script>
    <script src="<?php echo X0PA_HIRING_PLUGIN_URL; ?>includes/assets/js/hubspot-pdf-gate.js"></script>

    <!-- PRESERVE:START -->
    <?php include X0PA_HIRING_PLUGIN_DIR . 'includes/tracking/tracking-codes-body-end.php'; ?>
    <!-- PRESERVE:END -->

</body>
</html>
