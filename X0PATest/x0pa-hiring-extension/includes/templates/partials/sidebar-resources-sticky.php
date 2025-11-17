<?php
/**
 * Sticky Sidebar Resources Section
 *
 * Dynamically displays related pages using smart internal linking algorithm
 *
 * @param string $page_type - Either 'interview-questions' or 'job-description'
 */

// Load internal linking class
require_once X0PA_HIRING_PLUGIN_DIR . 'includes/core/class-internal-linking.php';

// Set default values if not provided
$page_type = $page_type ?? 'interview-questions';

// Get current post data
global $post;
$current_job_title = get_post_meta($post->ID, '_x0pa_job_title', true);

// Get related pages using smart algorithm
$related_pages = X0PA_Internal_Linking::get_related_pages(
    $current_job_title,
    $page_type,
    $post->ID,
    4
);

// Configure title and link suffix based on page type
if ($page_type === 'interview-questions') {
    $section_title = 'Interview Templates for Other Roles';
    $link_suffix = 'Interview Questions';
} else {
    $section_title = 'More Job Description Templates';
    $link_suffix = 'Job Description';
}
?>

<!-- Single sticky container with natural gap-based spacing -->
<div class="lg:sticky top-8 mt-6 z-10 flex flex-col gap-6">

    <!-- Resource Links Card -->
    <div class="bg-white border border-gray rounded-lg p-6">
        <h3 class="text-lg font-bold text-dark mb-4"><?php echo htmlspecialchars($section_title); ?></h3>

        <?php if (!empty($related_pages) && is_array($related_pages)): ?>
            <ul class="flex flex-col gap-2 mb-4">
                <?php foreach ($related_pages as $page): ?>
                    <li class="text-sm text-gray hover:text-primary transition-colors duration-200">
                        <a href="<?php echo esc_url($page['url']); ?>" class="text-gray hover:text-primary">
                            <?php echo htmlspecialchars($page['job_title'] . ' ' . $link_suffix); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-sm text-gray mb-4">No related templates available at this time.</p>
        <?php endif; ?>

        <a href="https://x0pa.com/hiring/" class="text-primary hover:text-navy font-semibold text-sm flex items-center gap-1">
            <span>Browse All Templates</span>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"></path>
            </svg>
        </a>
    </div>

    <!-- CTA Button - Only show for interview questions pages -->
    <?php if ($page_type === 'interview-questions'): ?>
        <button id="pdf-download-btn-sidebar" class="bg-primary text-white px-4 py-2.5 rounded-lg font-semibold transition-all duration-200 hover:bg-navy flex gap-2 cursor-pointer w-full">
            <span class="text-sm">Download Interview Questions PDF</span>
        </button>
    <?php endif; ?>

</div>
