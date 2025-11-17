<?php
/**
 * Simple Job Description Template
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
    'job-description',
    $post->ID,
    4
);

// Define sections to display
$sections = array(
    'objectives' => 'Role Objectives',
    'responsibilities' => 'Key Responsibilities',
    'required_skills' => 'Required Skills',
    'preferred_skills' => 'Preferred Skills',
    'what_role_does' => 'What This Role Does',
    'skills_to_look_for' => 'Skills to Look For'
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

.content-section__body {
    background: #f7fafc;
    padding: 1.5rem;
    border-radius: 0.5rem;
    line-height: 1.6;
    color: #2d3748;
}

.content-section__body ul {
    list-style: disc;
    padding-left: 1.5rem;
    margin: 1rem 0;
}

.content-section__body li {
    margin-bottom: 0.5rem;
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
        <h1 class="hiring-title"><?php echo esc_html($job_title . ' Job Description'); ?></h1>
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
                        <?php foreach ($sections as $key => $title):
                            if (!empty($content_data[$key])): ?>
                                <li><a href="#<?php echo esc_attr($key); ?>"><?php echo esc_html($title); ?></a></li>
                            <?php endif;
                        endforeach; ?>
                    </ul>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main>
            <?php foreach ($sections as $key => $title):
                $content = $content_data[$key] ?? '';
                if (empty($content)) {
                    continue;
                }

                // Convert pipe-delimited content to list items
                $items = explode('|', $content);
                ?>

                <section id="<?php echo esc_attr($key); ?>" class="content-section">
                    <h2 class="content-section__title"><?php echo esc_html($title); ?></h2>

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

            <?php endforeach; ?>
        </main>

        <!-- Right Sidebar: Related Resources -->
        <aside>
            <div class="sidebar-section">
                <h3 class="sidebar-title">More Job Description Templates</h3>

                <?php if (!empty($related_pages)): ?>
                    <ul class="sidebar-links">
                        <?php foreach ($related_pages as $page): ?>
                            <li>
                                <a href="<?php echo esc_url($page['url']); ?>">
                                    <?php echo esc_html($page['job_title'] . ' Job Description'); ?>
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
