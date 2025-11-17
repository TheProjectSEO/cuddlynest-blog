<?php
/**
 * Simple Interview Questions Template
 * Works with WordPress theme integration
 *
 * @package X0PA_Hiring_Extension
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Get WordPress header
get_header();

// Get post data
global $post;
$job_title = get_post_meta($post->ID, '_x0pa_job_title', true);
$last_updated = get_post_meta($post->ID, '_x0pa_last_updated', true);
$content_json = get_post_meta($post->ID, '_x0pa_content_json', true);

// Parse JSON content
$content_data = json_decode($content_json, true);

// Include internal linking class
require_once X0PA_HIRING_PLUGIN_DIR . 'includes/core/class-internal-linking.php';

// Get related pages
$related_pages = X0PA_Internal_Linking::get_related_pages(
    $job_title,
    'interview-questions',
    $post->ID,
    4
);
?>

<style>
/* Tailwind-inspired styles */
.hiring-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

.hiring-header {
    margin-bottom: 3rem;
}

.hiring-title {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
    color: #1a202c;
}

.hiring-meta {
    color: #718096;
    font-size: 0.875rem;
}

.hiring-layout {
    display: grid;
    grid-template-columns: 1fr;
    gap: 2rem;
}

@media (min-width: 1024px) {
    .hiring-layout {
        grid-template-columns: 250px 1fr 300px;
    }
}

.content-section {
    margin-bottom: 3rem;
}

.content-section__title {
    font-size: 1.875rem;
    font-weight: bold;
    color: #2d3748;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e2e8f0;
}

.content-item {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #f7fafc;
    border-radius: 0.5rem;
}

.content-item__title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 1rem;
}

.content-item__box {
    background: white;
    padding: 1rem;
    border-radius: 0.375rem;
    border: 1px solid #e2e8f0;
}

.content-item__box-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #4a5568;
    margin-bottom: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.content-item__box-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.content-item__box-list-item {
    padding: 0.5rem 0;
    padding-left: 1.5rem;
    position: relative;
    color: #2d3748;
}

.content-item__box-list-item:before {
    content: "✓";
    position: absolute;
    left: 0;
    color: #48bb78;
    font-weight: bold;
}

.sidebar-section {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.sidebar-title {
    font-size: 1.125rem;
    font-weight: bold;
    color: #2d3748;
    margin-bottom: 1rem;
}

.sidebar-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar-links li {
    margin-bottom: 0.5rem;
}

.sidebar-links a {
    color: #4299e1;
    text-decoration: none;
    font-size: 0.875rem;
}

.sidebar-links a:hover {
    color: #2b6cb0;
    text-decoration: underline;
}

.toc {
    position: sticky;
    top: 2rem;
}

.toc-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.toc-list li {
    margin-bottom: 0.5rem;
}

.toc-list a {
    color: #4a5568;
    text-decoration: none;
    font-size: 0.875rem;
}

.toc-list a:hover {
    color: #2d3748;
}
</style>

<div class="hiring-container">

    <!-- Header -->
    <div class="hiring-header">
        <h1 class="hiring-title"><?php echo esc_html($job_title . ' Interview Questions'); ?></h1>
        <?php if ($last_updated): ?>
            <div class="hiring-meta">Last Updated: <?php echo esc_html($last_updated); ?></div>
        <?php endif; ?>
    </div>

    <!-- Main Layout -->
    <div class="hiring-layout">

        <!-- Left Sidebar: Table of Contents -->
        <aside>
            <div class="toc">
                <div class="sidebar-section">
                    <h3 class="sidebar-title">On This Page</h3>
                    <ul class="toc-list">
                        <?php
                        // Generate TOC from sections
                        for ($i = 1; $i <= 3; $i++) {
                            $section_id = $content_data["section_{$i}_id"] ?? '';
                            $section_title = $content_data["section_{$i}_title"] ?? '';
                            if ($section_id && $section_title) {
                                echo '<li><a href="#' . esc_attr($section_id) . '">' . esc_html($section_title) . '</a></li>';
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main>
            <?php
            // Loop through sections
            for ($i = 1; $i <= 3; $i++) {
                $section_id = $content_data["section_{$i}_id"] ?? '';
                $section_title = $content_data["section_{$i}_title"] ?? '';
                $questions_json = $content_data["section_{$i}_questions_json"] ?? '';

                if (!$section_id || !$section_title || !$questions_json) {
                    continue;
                }

                $questions = json_decode($questions_json, true);

                if (!is_array($questions) || empty($questions)) {
                    continue;
                }
                ?>

                <section id="<?php echo esc_attr($section_id); ?>" class="content-section">
                    <h2 class="content-section__title"><?php echo esc_html($section_title); ?></h2>

                    <?php foreach ($questions as $q): ?>
                        <article class="content-item">
                            <h3 class="content-item__title"><?php echo esc_html($q['question']); ?></h3>

                            <?php if (!empty($q['what_to_listen_for'])): ?>
                                <div class="content-item__box">
                                    <h4 class="content-item__box-title">What to Listen For:</h4>
                                    <ul class="content-item__box-list">
                                        <?php foreach ($q['what_to_listen_for'] as $point): ?>
                                            <li class="content-item__box-list-item"><?php echo esc_html($point); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </section>

            <?php } ?>
        </main>

        <!-- Right Sidebar: Related Resources -->
        <aside>
            <div class="sidebar-section">
                <h3 class="sidebar-title">Interview Templates for Other Roles</h3>

                <?php if (!empty($related_pages)): ?>
                    <ul class="sidebar-links">
                        <?php foreach ($related_pages as $page): ?>
                            <li>
                                <a href="<?php echo esc_url($page['url']); ?>">
                                    <?php echo esc_html($page['job_title'] . ' Interview Questions'); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <a href="<?php echo esc_url(home_url('/hiring/')); ?>" style="color: #4299e1; font-weight: 600; font-size: 0.875rem; margin-top: 1rem; display: inline-block;">
                    Browse All Templates →
                </a>
            </div>
        </aside>

    </div>

</div>

<?php get_footer(); ?>
