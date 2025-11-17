<?php
/**
 * Hero Section Generator
 *
 * Dynamically generates the hero section for hiring page templates based on
 * page type (interview questions or job description) and calculates reading time.
 *
 * @package x0pa-hiring-templates
 */

/**
 * Generate complete hero section HTML
 *
 * Creates the full hero section with dynamic title, description, and metadata
 * based on page type. Calculates reading time from content word count.
 *
 * @param array $config Configuration array with:
 *   - job_title (string, required): Job title (e.g., "Accountant")
 *   - page_type (string, required): 'interview-questions' or 'job-description'
 *   - publish_date (string, optional): Date string, defaults to current date
 * @param string $content Full page HTML content for reading time calculation
 * @return string Complete HTML for hero section wrapped in <header> tag
 */
function generate_hero_section($config, $content = '') {
    // Extract config values
    $job_title = isset($config['job_title']) ? $config['job_title'] : 'Professional';
    $page_type = isset($config['page_type']) ? $config['page_type'] : 'interview-questions';
    $publish_date = isset($config['publish_date']) ? $config['publish_date'] : date('F j, Y');

    // Hardcoded values
    $badge_text = 'Hiring guide';
    $author_name = 'Nina Alag Suri';
    $author_linkedin = 'https://www.linkedin.com/in/ninaalagsuri/';

    // Generate dynamic title and description based on page type
    if ($page_type === 'job-description') {
        $title = $job_title . ' Job Description';
        $description = 'Learn about the key requirements, duties, responsibilities, and skills that should be in an ' . $job_title . ' job description.';
    } else {
        // Default to interview-questions
        $title = $job_title . ' Interview Questions';
        $description = 'These ' . $job_title . ' interview questions will guide your interview process to help you find trusted candidates with the right skills you are looking for.';
    }

    // Calculate reading time from content
    $reading_time = calculate_reading_time($content);

    // Build the HTML
    $html = '<header class="hiring__header">' . "\n";
    $html .= '    <!-- Hero Section with Dark Background -->' . "\n";
    $html .= '    <div class="hiring__hero">' . "\n";
    $html .= '        <div class="hiring__hero-wrapper">' . "\n";
    $html .= '            <div class="hiring__hero-content">' . "\n";

    // Badge
    $html .= '                <!-- Tag/Badge -->' . "\n";
    $html .= '                <div class="hiring__hero-tag">' . "\n";
    $html .= '                    <span class="hiring__hero-tag-text">' . htmlspecialchars($badge_text) . '</span>' . "\n";
    $html .= '                </div>' . "\n\n";

    // Title
    $html .= '                <!-- Main Heading -->' . "\n";
    $html .= '                <h1 class="hiring__hero-title">' . "\n";
    $html .= '                    ' . htmlspecialchars($title) . "\n";
    $html .= '                </h1>' . "\n\n";

    // Author Metadata
    $html .= '                <!-- Author Metadata -->' . "\n";
    $html .= '                <div class="hiring__meta">' . "\n";
    $html .= '                    <div class="hiring__meta-item">' . "\n";
    $html .= '                        <span>By</span>' . "\n";
    $html .= '                        <a href="' . htmlspecialchars($author_linkedin) . '" class="hiring__meta-link">' . htmlspecialchars($author_name) . '</a>' . "\n";
    $html .= '                    </div>' . "\n";
    $html .= '                    <span class="hiring__meta-divider">■</span>' . "\n";
    $html .= '                    <div class="hiring__meta-item">' . "\n";
    $html .= '                        <span>' . htmlspecialchars($publish_date) . '</span>' . "\n";
    $html .= '                    </div>' . "\n";
    $html .= '                    <span class="hiring__meta-divider">■</span>' . "\n";
    $html .= '                    <div class="hiring__meta-item">' . "\n";
    $html .= '                        <span>' . $reading_time . ' min read</span>' . "\n";
    $html .= '                    </div>' . "\n";
    $html .= '                </div>' . "\n\n";

    // Description
    $html .= '                <!-- Description Text -->' . "\n";
    $html .= '                <p class="hiring__hero-description">' . "\n";
    $html .= '                    ' . htmlspecialchars($description) . "\n";
    $html .= '                </p>' . "\n\n";

    $html .= '            </div>' . "\n\n";
    $html .= '        </div>' . "\n";
    $html .= '    </div>' . "\n";
    $html .= '</header>';

    return $html;
}

/**
 * Calculate reading time from HTML content
 *
 * Strips HTML tags, counts words, and calculates reading time based on
 * industry standard of 200 words per minute. Rounds up to nearest minute.
 *
 * @param string $html_content HTML content to analyze
 * @return int Reading time in minutes (rounded up)
 */
function calculate_reading_time($html_content) {
    // Return default if no content
    if (empty($html_content)) {
        return 5; // Default 5 min read
    }

    // Strip HTML tags and count words
    $text_only = strip_tags($html_content);
    $word_count = str_word_count($text_only);

    // Calculate reading time (200 words per minute is industry standard)
    // Round up to nearest minute
    $reading_time = ceil($word_count / 200);

    // Ensure minimum of 1 minute
    return max(1, $reading_time);
}

/**
 * Get hero configuration for a specific page
 *
 * Helper function to generate standard config array for hero section.
 *
 * @param string $job_title Job title (e.g., "Accountant")
 * @param string $page_type 'interview-questions' or 'job-description'
 * @param string $publish_date Optional publish date, defaults to current date
 * @return array Configuration array for generate_hero_section()
 */
function get_hero_config($job_title, $page_type, $publish_date = null) {
    $config = array(
        'job_title' => $job_title,
        'page_type' => $page_type
    );

    if ($publish_date !== null) {
        $config['publish_date'] = $publish_date;
    }

    return $config;
}
?>
