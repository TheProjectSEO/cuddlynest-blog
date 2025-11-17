<?php
/**
 * Cross-Link CTA Component
 *
 * Displays a clickable card that links to the opposite page type:
 * - Interview Questions pages link to Job Description
 * - Job Description pages link to Interview Questions
 *
 * Expected variables from parent scope:
 * @var string $job_title - Job title (e.g., "Accountant")
 * @var string $page_type - Either "interview-questions" or "job-description"
 */

// Convert job title to URL slug (lowercase, spaces to hyphens)
$job_title_slug = strtolower(str_replace(' ', '-', $job_title));

// Determine content based on page type
if ($page_type === 'interview-questions') {
    // Interview Questions page links to Job Description
    $badge_text = 'Start Here';
    $title_text = 'Download Job Description Template';
    $description_text = 'Create a compelling ' . strtolower($job_title) . ' job posting before you start interviewing';
    $link_url = 'https://x0pa.com/hiring/' . $job_title_slug . '-job-description/';
} else {
    // Job Description page links to Interview Questions
    $badge_text = 'Next Step';
    $title_text = 'Get Interview Question Templates';
    $description_text = 'Expert-crafted questions to evaluate ' . strtolower($job_title) . ' candidates effectively';
    $link_url = 'https://x0pa.com/hiring/' . $job_title_slug . '-interview-questions/';
}
?>

<section> <a href="<?php echo $link_url; ?>" class="block bg-accent bg-opacity-10 border-2 border-accent rounded-lg p-6 my-2 hover:bg-opacity-20 hover:shadow-md transition-all duration-200 cursor-pointer group"> <div class="flex items-start justify-between gap-4"> <div class="flex-1"> <div class="text-xs text-accent font-bold uppercase mb-2 tracking-wide"><?php echo $badge_text; ?></div> <div class="text-lg font-semibold text-dark group-hover:text-primary transition-colors mb-1"> <?php echo $title_text; ?> </div> <div class="text-sm text-gray-600"><?php echo $description_text; ?></div> </div> <svg class="w-6 h-6 text-accent flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20"> <path d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" /> </svg> </div> </a> </section>