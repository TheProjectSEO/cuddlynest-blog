<?php
/**
 * SEO Meta Tags Generator
 *
 * Dynamically generates comprehensive SEO meta tags including Open Graph,
 * Twitter Cards, and Structured Data (JSON-LD) for hiring page templates.
 *
 * @package x0pa-hiring-templates
 */

/**
 * Generate complete SEO meta tags for head section
 *
 * Creates comprehensive SEO metadata including title, description, Open Graph,
 * Twitter Cards, and JSON-LD structured data based on page type and content.
 *
 * @param array $config Configuration array with:
 *   - job_title (string, required): Job title (e.g., "Accountant")
 *   - page_type (string, required): 'interview-questions' or 'job-description'
 *   - publish_date (string, optional): Date string, defaults to current date
 *   - canonical_url (string, optional): Full canonical URL
 *   - og_image (string, optional): Open Graph image URL
 * @param string $content Full page HTML content for reading time and question count
 * @return string Complete HTML meta tags for <head> section
 */
function generate_seo_meta($config, $content = '') {
    // Extract config values
    $job_title = isset($config['job_title']) ? $config['job_title'] : 'Professional';
    $page_type = isset($config['page_type']) ? $config['page_type'] : 'interview-questions';
    $publish_date = isset($config['publish_date']) ? $config['publish_date'] : date('F j, Y');

    // Hardcoded values
    $author_name = 'Nina Alag Suri';
    $author_linkedin = 'https://www.linkedin.com/in/ninaalagsuri/';
    $site_name = 'x0pa';
    $current_year = date('Y');

    // Calculate reading time
    $reading_time = calculate_reading_time($content);

    // Calculate question count (only for interview-questions pages)
    $question_count = 0;
    if ($page_type === 'interview-questions' && function_exists('get_interview_questions_count')) {
        $question_count = get_interview_questions_count($content);
    }

    // Generate URLs (these should be passed in config for production)
    $job_slug = strtolower(str_replace(' ', '-', $job_title));
    $page_slug = $page_type === 'job-description' ? 'job-description' : 'interview-questions';
    $canonical_url = isset($config['canonical_url']) ? $config['canonical_url'] : "https://x0pa.com/hiring/{$job_slug}-{$page_slug}/";

    // Default OG image
    $og_image = isset($config['og_image']) ? $config['og_image'] : "https://x0pa.com/images/hiring/{$job_slug}-og.jpg";

    // Convert date to ISO 8601 for structured data
    $date_obj = date_create($publish_date);
    $iso_date = $date_obj ? $date_obj->format('Y-m-d') : date('Y-m-d');
    $iso_datetime = $iso_date . 'T00:00:00Z';

    // Generate dynamic content based on page type
    if ($page_type === 'job-description') {
        $title = "{$job_title} Job Description Template [{$current_year}] - Key Requirements | {$site_name}";
        $meta_description = "Complete {$job_title} job description template with key requirements, duties, responsibilities, and skills. {$reading_time} min read.";
        $og_title = "{$job_title} Job Description Template - Hire the Right Talent";
        $og_description = "Comprehensive {$job_title} job description covering all essential requirements, duties, and skills for successful hiring.";
        $keywords = "{$job_slug} job description, {$job_slug} job requirements, hiring {$job_slug}, job template, {$job_slug} duties, {$job_slug} responsibilities";
        $article_section = "Job Descriptions";
        $breadcrumb_name = "{$job_title} Job Description";
    } else {
        // Default to interview-questions
        $title = "{$question_count} {$job_title} Interview Questions & Answers [{$current_year}] | {$site_name}";
        $meta_description = "Expert {$job_title} interview questions covering technical skills, analysis, and soft skills. Includes evaluation criteria. {$reading_time} min read.";
        $og_title = "{$question_count} {$job_title} Interview Questions Every Recruiter Needs";
        $og_description = "Expert-crafted interview questions with evaluation criteria to help you hire the best {$job_title} candidates.";
        $keywords = "{$job_slug} interview questions, {$job_slug} interview, hiring {$job_slug}, recruitment questions, {$job_slug} skills, interview tips";
        $article_section = "Interview Questions";
        $breadcrumb_name = "{$job_title} Interview Questions";
    }

    // Twitter card variations (shorter for Twitter)
    $twitter_title = $page_type === 'job-description'
        ? "{$job_title} Job Description Template [{$current_year}]"
        : "{$question_count} {$job_title} Interview Questions & Answers";
    $twitter_description = $page_type === 'job-description'
        ? "Complete job description template with requirements, duties, and skills. {$reading_time} min read."
        : "Expert interview questions covering technical skills, analysis, and soft skills. {$reading_time} min read.";

    // Build the HTML
    $html = '';

    // Critical Meta Tags
    $html .= "    <meta charset=\"UTF-8\">\n";
    $html .= "    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
    $html .= "    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n\n";

    // Primary SEO Tags
    $html .= "    <!-- Primary SEO Tags -->\n";
    $html .= "    <title>" . htmlspecialchars($title) . "</title>\n";
    $html .= "    <meta name=\"description\" content=\"" . htmlspecialchars($meta_description) . "\">\n";
    $html .= "    <meta name=\"keywords\" content=\"" . htmlspecialchars($keywords) . "\">\n";
    $html .= "    <link rel=\"canonical\" href=\"" . htmlspecialchars($canonical_url) . "\">\n";
    $html .= "    <meta name=\"author\" content=\"" . htmlspecialchars($author_name) . "\">\n";
    $html .= "    <link rel=\"author\" href=\"" . htmlspecialchars($author_linkedin) . "\">\n\n";

    // Open Graph Tags
    $html .= "    <!-- Open Graph / Facebook -->\n";
    $html .= "    <meta property=\"og:type\" content=\"article\">\n";
    $html .= "    <meta property=\"og:site_name\" content=\"" . htmlspecialchars($site_name) . "\">\n";
    $html .= "    <meta property=\"og:title\" content=\"" . htmlspecialchars($og_title) . "\">\n";
    $html .= "    <meta property=\"og:description\" content=\"" . htmlspecialchars($og_description) . "\">\n";
    $html .= "    <meta property=\"og:url\" content=\"" . htmlspecialchars($canonical_url) . "\">\n";
    $html .= "    <meta property=\"og:image\" content=\"" . htmlspecialchars($og_image) . "\">\n";
    $html .= "    <meta property=\"article:author\" content=\"" . htmlspecialchars($author_linkedin) . "\">\n";
    $html .= "    <meta property=\"article:published_time\" content=\"" . htmlspecialchars($iso_datetime) . "\">\n";
    $html .= "    <meta property=\"article:modified_time\" content=\"" . htmlspecialchars($iso_datetime) . "\">\n";
    $html .= "    <meta property=\"article:section\" content=\"" . htmlspecialchars($article_section) . "\">\n\n";

    // Twitter Card Tags
    $html .= "    <!-- Twitter Card -->\n";
    $html .= "    <meta name=\"twitter:card\" content=\"summary_large_image\">\n";
    $html .= "    <meta name=\"twitter:title\" content=\"" . htmlspecialchars($twitter_title) . "\">\n";
    $html .= "    <meta name=\"twitter:description\" content=\"" . htmlspecialchars($twitter_description) . "\">\n";
    $html .= "    <meta name=\"twitter:image\" content=\"" . htmlspecialchars($og_image) . "\">\n\n";

    // Performance Optimization Tags
    $html .= "    <!-- Performance Optimization -->\n";
    $html .= "    <link rel=\"preconnect\" href=\"https://cdn.tailwindcss.com\">\n";
    $html .= "    <link rel=\"preconnect\" href=\"https://www.googletagmanager.com\">\n";
    $html .= "    <link rel=\"dns-prefetch\" href=\"https://cdn.tailwindcss.com\">\n";
    $html .= "    <link rel=\"dns-prefetch\" href=\"https://www.googletagmanager.com\">\n\n";

    // Structured Data - Article Schema
    $html .= "    <!-- Structured Data: Article -->\n";
    $html .= "    <script type=\"application/ld+json\">\n";
    $html .= "    {\n";
    $html .= "      \"@context\": \"https://schema.org\",\n";
    $html .= "      \"@type\": \"Article\",\n";
    $html .= "      \"headline\": \"" . addslashes($og_title) . "\",\n";
    $html .= "      \"description\": \"" . addslashes($meta_description) . "\",\n";
    $html .= "      \"author\": {\n";
    $html .= "        \"@type\": \"Person\",\n";
    $html .= "        \"name\": \"" . addslashes($author_name) . "\",\n";
    $html .= "        \"url\": \"" . addslashes($author_linkedin) . "\"\n";
    $html .= "      },\n";
    $html .= "      \"publisher\": {\n";
    $html .= "        \"@type\": \"Organization\",\n";
    $html .= "        \"name\": \"" . addslashes($site_name) . "\",\n";
    $html .= "        \"logo\": {\n";
    $html .= "          \"@type\": \"ImageObject\",\n";
    $html .= "          \"url\": \"https://x0pa.com/logo.png\"\n";
    $html .= "        }\n";
    $html .= "      },\n";
    $html .= "      \"datePublished\": \"" . $iso_date . "\",\n";
    $html .= "      \"dateModified\": \"" . $iso_date . "\",\n";
    $html .= "      \"image\": \"" . addslashes($og_image) . "\",\n";
    $html .= "      \"url\": \"" . addslashes($canonical_url) . "\"\n";
    $html .= "    }\n";
    $html .= "    </script>\n\n";

    // Structured Data - Breadcrumb Schema
    $html .= "    <!-- Structured Data: Breadcrumb -->\n";
    $html .= "    <script type=\"application/ld+json\">\n";
    $html .= "    {\n";
    $html .= "      \"@context\": \"https://schema.org\",\n";
    $html .= "      \"@type\": \"BreadcrumbList\",\n";
    $html .= "      \"itemListElement\": [\n";
    $html .= "        {\n";
    $html .= "          \"@type\": \"ListItem\",\n";
    $html .= "          \"position\": 1,\n";
    $html .= "          \"name\": \"Home\",\n";
    $html .= "          \"item\": \"https://x0pa.com\"\n";
    $html .= "        },\n";
    $html .= "        {\n";
    $html .= "          \"@type\": \"ListItem\",\n";
    $html .= "          \"position\": 2,\n";
    $html .= "          \"name\": \"Hiring\",\n";
    $html .= "          \"item\": \"https://x0pa.com/hiring\"\n";
    $html .= "        },\n";
    $html .= "        {\n";
    $html .= "          \"@type\": \"ListItem\",\n";
    $html .= "          \"position\": 3,\n";
    $html .= "          \"name\": \"" . addslashes($breadcrumb_name) . "\",\n";
    $html .= "          \"item\": \"" . addslashes($canonical_url) . "\"\n";
    $html .= "        }\n";
    $html .= "      ]\n";
    $html .= "    }\n";
    $html .= "    </script>\n";

    return $html;
}

/**
 * Get SEO configuration for a specific page
 *
 * Helper function to generate standard config array for SEO meta generation.
 *
 * @param string $job_title Job title (e.g., "Accountant")
 * @param string $page_type 'interview-questions' or 'job-description'
 * @param string $publish_date Optional publish date, defaults to current date
 * @param string $canonical_url Optional canonical URL
 * @param string $og_image Optional Open Graph image URL
 * @return array Configuration array for generate_seo_meta()
 */
function get_seo_config($job_title, $page_type, $publish_date = null, $canonical_url = null, $og_image = null) {
    $config = array(
        'job_title' => $job_title,
        'page_type' => $page_type
    );

    if ($publish_date !== null) {
        $config['publish_date'] = $publish_date;
    }

    if ($canonical_url !== null) {
        $config['canonical_url'] = $canonical_url;
    }

    if ($og_image !== null) {
        $config['og_image'] = $og_image;
    }

    return $config;
}
?>
