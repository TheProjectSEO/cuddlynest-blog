<?php
/**
 * SEO Meta Generator
 *
 * @package X0PA_Hiring_Extension
 * @subpackage Generators
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class X0PA_SEO_Meta
 *
 * Generates SEO metadata for hiring pages including titles, descriptions,
 * Open Graph tags, and reading time calculations
 */
class X0PA_SEO_Meta {

    /**
     * Average reading speed (words per minute)
     */
    const READING_SPEED = 200;

    /**
     * Generate complete SEO metadata for a post
     *
     * @param WP_Post $post The post object
     * @param string $content HTML content for word count
     * @return array SEO metadata array
     */
    public static function generate($post, $content = '') {
        if (!$post || !is_a($post, 'WP_Post')) {
            return array();
        }

        $job_title = get_post_meta($post->ID, '_x0pa_job_title', true);
        $page_type = get_post_meta($post->ID, '_x0pa_page_type', true);

        // Generate title and description
        $title = self::generate_title($job_title, $page_type);
        $description = self::generate_description($job_title, $page_type);

        // Calculate reading time
        $reading_time = self::calculate_reading_time($content);

        // Build metadata array
        $meta = array(
            // Basic SEO
            'title' => $title,
            'description' => $description,
            'canonical_url' => get_permalink($post),

            // Open Graph
            'og:title' => $title,
            'og:description' => $description,
            'og:url' => get_permalink($post),
            'og:type' => 'article',
            'og:site_name' => get_bloginfo('name'),
            'og:locale' => get_locale(),

            // Twitter Card
            'twitter:card' => 'summary_large_image',
            'twitter:title' => $title,
            'twitter:description' => $description,

            // Article metadata
            'article:published_time' => get_post_time('c', false, $post),
            'article:modified_time' => get_post_modified_time('c', false, $post),
            'article:author' => 'Nina Alag Suri',

            // Custom metadata
            'reading_time' => $reading_time,
            'word_count' => self::get_word_count($content),
            'job_title' => $job_title,
            'page_type' => $page_type
        );

        // Add image if available
        $image_url = self::get_featured_image_url($post);
        if ($image_url) {
            $meta['og:image'] = $image_url;
            $meta['twitter:image'] = $image_url;
        }

        return $meta;
    }

    /**
     * Generate SEO title
     *
     * @param string $job_title Job title
     * @param string $page_type Page type
     * @return string SEO title
     */
    private static function generate_title($job_title, $page_type) {
        $templates = array(
            'interview-questions' => '{job_title} Interview Questions & Answers | X0PA AI',
            'job-description' => '{job_title} Job Description Template | X0PA AI'
        );

        $template = isset($templates[$page_type]) ? $templates[$page_type] : '{job_title} | X0PA AI';

        return str_replace('{job_title}', $job_title, $template);
    }

    /**
     * Generate meta description
     *
     * @param string $job_title Job title
     * @param string $page_type Page type
     * @return string Meta description
     */
    private static function generate_description($job_title, $page_type) {
        $templates = array(
            'interview-questions' => 'Comprehensive {job_title} interview questions with expert answers. Prepare for your next hiring interview with proven strategies and insights.',
            'job-description' => 'Professional {job_title} job description template with responsibilities, requirements, and qualifications. Expert guidance for effective hiring.'
        );

        $template = isset($templates[$page_type])
            ? $templates[$page_type]
            : 'Professional {job_title} hiring resources from X0PA AI.';

        return str_replace('{job_title}', $job_title, $template);
    }

    /**
     * Calculate reading time in minutes
     *
     * @param string $content HTML content
     * @return int Reading time in minutes
     */
    public static function calculate_reading_time($content) {
        if (empty($content)) {
            return 0;
        }

        $word_count = self::get_word_count($content);

        // Calculate minutes (minimum 1 minute)
        $minutes = max(1, ceil($word_count / self::READING_SPEED));

        return $minutes;
    }

    /**
     * Get word count from content
     *
     * @param string $content HTML content
     * @return int Word count
     */
    public static function get_word_count($content) {
        if (empty($content)) {
            return 0;
        }

        // Strip HTML tags
        $text = wp_strip_all_tags($content);

        // Remove extra whitespace
        $text = preg_replace('/\s+/', ' ', $text);

        // Count words
        return str_word_count($text);
    }

    /**
     * Get featured image URL
     *
     * @param WP_Post $post Post object
     * @return string|null Image URL or null
     */
    private static function get_featured_image_url($post) {
        if (has_post_thumbnail($post)) {
            $image = wp_get_attachment_image_src(get_post_thumbnail_id($post), 'large');
            return $image ? $image[0] : null;
        }

        // Fallback to default X0PA image
        return 'https://x0pa.com/wp-content/uploads/2024/03/X0PA-AI-Logo.png';
    }

    /**
     * Output meta tags in HTML head
     *
     * @param array $meta Metadata array
     */
    public static function output_meta_tags($meta) {
        if (empty($meta)) {
            return;
        }

        // Title tag
        if (isset($meta['title'])) {
            echo '<title>' . esc_html($meta['title']) . '</title>' . "\n";
        }

        // Meta description
        if (isset($meta['description'])) {
            echo '<meta name="description" content="' . esc_attr($meta['description']) . '">' . "\n";
        }

        // Canonical URL
        if (isset($meta['canonical_url'])) {
            echo '<link rel="canonical" href="' . esc_url($meta['canonical_url']) . '">' . "\n";
        }

        // Open Graph tags
        $og_tags = array('og:title', 'og:description', 'og:url', 'og:type', 'og:image', 'og:site_name', 'og:locale');
        foreach ($og_tags as $tag) {
            if (isset($meta[$tag])) {
                echo '<meta property="' . esc_attr($tag) . '" content="' . esc_attr($meta[$tag]) . '">' . "\n";
            }
        }

        // Twitter Card tags
        $twitter_tags = array('twitter:card', 'twitter:title', 'twitter:description', 'twitter:image');
        foreach ($twitter_tags as $tag) {
            if (isset($meta[$tag])) {
                echo '<meta name="' . esc_attr($tag) . '" content="' . esc_attr($meta[$tag]) . '">' . "\n";
            }
        }

        // Article metadata
        $article_tags = array('article:published_time', 'article:modified_time', 'article:author');
        foreach ($article_tags as $tag) {
            if (isset($meta[$tag])) {
                echo '<meta property="' . esc_attr($tag) . '" content="' . esc_attr($meta[$tag]) . '">' . "\n";
            }
        }
    }

    /**
     * Generate breadcrumb structured data
     *
     * @param WP_Post $post Post object
     * @param string $job_title Job title
     * @return string JSON-LD breadcrumb schema
     */
    public static function generate_breadcrumb_schema($post, $job_title) {
        $breadcrumbs = array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array(
                array(
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Home',
                    'item' => home_url('/')
                ),
                array(
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'Hiring Resources',
                    'item' => home_url('/hiring/')
                ),
                array(
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $job_title,
                    'item' => get_permalink($post)
                )
            )
        );

        return json_encode($breadcrumbs, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Format reading time for display
     *
     * @param int $minutes Reading time in minutes
     * @return string Formatted reading time
     */
    public static function format_reading_time($minutes) {
        if ($minutes === 1) {
            return '1 min read';
        }

        return $minutes . ' min read';
    }

    /**
     * Get estimated engagement time (reading + interaction)
     *
     * @param string $content Content
     * @param int $interaction_time Additional time for forms, etc. (seconds)
     * @return int Total engagement time in seconds
     */
    public static function get_engagement_time($content, $interaction_time = 0) {
        $reading_minutes = self::calculate_reading_time($content);
        $reading_seconds = $reading_minutes * 60;

        return $reading_seconds + $interaction_time;
    }
}
