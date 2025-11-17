<?php
/**
 * WordPress-Compatible Job Description Template
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
require_once X0PA_HIRING_PLUGIN_DIR . 'includes/generators/hero-generator.php';
require_once X0PA_HIRING_PLUGIN_DIR . 'includes/generators/seo-meta-generator.php';

// Get WordPress post data
global $post;
$job_title = get_post_meta($post->ID, '_x0pa_job_title', true);
$last_updated = get_post_meta($post->ID, '_x0pa_last_updated', true);
$content_json = get_post_meta($post->ID, '_x0pa_content_json', true);

// Parse JSON content
$content_data = json_decode($content_json, true);

// Page configuration
$hero_config = get_hero_config($job_title, 'job-description', $last_updated);
$seo_config = get_seo_config($job_title, 'job-description', $last_updated);

// Define sections and their display titles
$sections = array(
    'objectives' => 'Role Objectives',
    'responsibilities' => 'Key Responsibilities',
    'required_skills' => 'Required Skills',
    'preferred_skills' => 'Preferred Skills',
    'what_role_does' => 'What This Role Does',
    'skills_to_look_for' => 'Skills to Look For'
);

// Start output buffering to capture main content for SEO meta, TOC, and reading time
ob_start();
?>

<!-- Product Card CTA -->
<?php include X0PA_HIRING_PLUGIN_DIR . 'includes/templates/partials/cta__product-card-job-description.php'; ?>

<?php
// Loop through sections
foreach ($sections as $section_id => $section_title) {
    $content = $content_data[$section_id] ?? '';

    if (empty($content)) {
        continue;
    }

    // Convert pipe-delimited content to array
    $items = explode('|', $content);
    ?>

<!-- Section: <?php echo esc_html($section_title); ?> -->
<section id="<?php echo esc_attr($section_id); ?>" class="content-section">
    <div class="content-section__title">
        <?php echo esc_html($section_title); ?>
    </div>

    <div class="content-section__body">
        <?php if (count($items) > 1): ?>
            <ul>
                <?php foreach ($items as $item):
                    $item = trim($item);
                    if ($item): ?>
                        <li><?php echo esc_html($item); ?></li>
                    <?php endif;
                endforeach; ?>
            </ul>
        <?php else: ?>
            <p><?php echo esc_html($content); ?></p>
        <?php endif; ?>
    </div>
</section>

<?php
}
?>

<?php $page_type = 'job-description'; include X0PA_HIRING_PLUGIN_DIR . 'includes/templates/partials/cta__cross-link.php'; ?>

<?php
// Get buffered content
$body_content = ob_get_clean();

// Now output the complete HTML with dynamically generated head
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    // Generate SEO meta tags
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
        $toc_sections = generate_toc_from_content($body_content);

        // Start layout
        echo '<div class="hiring__layout">';

        // Output the sidebar with generated jump links
        echo '<aside class="hiring__layout-sidebar-left">';
        echo render_jump_links($toc_sections);
        echo '</aside>';

        // Generate and output the main content
        echo '<main class="hiring__layout-content">';

        // Output the complete content
        echo $body_content;
        echo '</main>';
        ?>

        <!-- Author Section - Right Sidebar -->
        <aside class="hiring__layout-sidebar-right">
            <?php include X0PA_HIRING_PLUGIN_DIR . 'includes/templates/partials/sidebar-author.php'; ?>
            <?php include X0PA_HIRING_PLUGIN_DIR . 'includes/templates/partials/sidebar-newsletter.php'; ?>

            <!-- Sticky Resources Section: Links + CTA -->
            <?php
            $page_type = 'job-description';
            include X0PA_HIRING_PLUGIN_DIR . 'includes/templates/partials/sidebar-resources-sticky.php';
            ?>

        </aside>

        <?php echo '</div>'; // Close hiring__layout ?>

    </div> <!-- Close hiring -->

    <?php include X0PA_HIRING_PLUGIN_DIR . 'includes/templates/partials/cta__footer-how-x0pa-helps.php'; ?>

    <!-- Scripts -->
    <script src="<?php echo X0PA_HIRING_PLUGIN_URL; ?>includes/assets/js/hiring-script.js"></script>
    <script>
        initScrollSpy();
    </script>
    <script src="<?php echo X0PA_HIRING_PLUGIN_URL; ?>includes/assets/js/hubspot-newsletter.js"></script>

    <!-- PRESERVE:START -->
    <?php include X0PA_HIRING_PLUGIN_DIR . 'includes/tracking/tracking-codes-body-end.php'; ?>
    <!-- PRESERVE:END -->

</body>
</html>
