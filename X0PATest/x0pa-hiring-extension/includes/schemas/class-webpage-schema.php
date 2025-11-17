<?php
/**
 * WebPage Schema Generator
 *
 * @package X0PA_Hiring_Extension
 * @subpackage Schemas
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class X0PA_WebPage_Schema
 *
 * Generates Schema.org WebPage structured data for hiring pages
 */
class X0PA_WebPage_Schema {

    /**
     * Generate WebPage schema for a post
     *
     * @param WP_Post $post The post object
     * @return string JSON-LD schema markup
     */
    public static function generate($post) {
        if (!$post || !is_a($post, 'WP_Post')) {
            return '';
        }

        // Extract metadata
        $job_title = get_post_meta($post->ID, '_x0pa_job_title', true);
        $page_type = get_post_meta($post->ID, '_x0pa_page_type', true);
        $last_updated = get_post_meta($post->ID, '_x0pa_last_updated', true);

        // Fallback to post modified date if no custom last_updated
        if (empty($last_updated)) {
            $last_updated = get_post_modified_time('c', false, $post);
        }

        // Format page type for description
        $page_type_label = self::format_page_type($page_type);

        // Build schema
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => get_the_title($post),
            'url' => get_permalink($post),
            'datePublished' => get_post_time('c', false, $post),
            'dateModified' => $last_updated,
            'description' => "Professional {$job_title} {$page_type_label} template with expert guidance and best practices.",
            'inLanguage' => 'en-US',
            'breadcrumb' => self::generate_breadcrumb($post, $job_title, $page_type),
            'author' => self::get_author_reference()
        );

        // Add main entity (the actual content)
        if ($page_type === 'interview-questions') {
            $schema['mainEntity'] = array(
                '@type' => 'Article',
                'headline' => $job_title . ' Interview Questions',
                'author' => self::get_author_reference()
            );
        } elseif ($page_type === 'job-description') {
            $schema['mainEntity'] = array(
                '@type' => 'JobPosting',
                'title' => $job_title,
                'description' => "Comprehensive job description template for {$job_title} positions."
            );
        }

        return self::format_schema($schema);
    }

    /**
     * Generate BreadcrumbList schema
     *
     * @param WP_Post $post The post object
     * @param string $job_title The job title
     * @param string $page_type The page type
     * @return array Breadcrumb schema
     */
    private static function generate_breadcrumb($post, $job_title, $page_type) {
        $breadcrumb_items = array(
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
        );

        return array(
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumb_items
        );
    }

    /**
     * Get author reference for Nina Alag Suri
     *
     * @return array Author schema reference
     */
    private static function get_author_reference() {
        return array(
            '@type' => 'Person',
            'name' => 'Nina Alag Suri',
            'jobTitle' => 'Founder and CEO',
            'url' => 'https://www.linkedin.com/in/ninaalagsuri/'
        );
    }

    /**
     * Format page type for human-readable display
     *
     * @param string $page_type The page type slug
     * @return string Formatted page type
     */
    private static function format_page_type($page_type) {
        $types = array(
            'interview-questions' => 'Interview Questions',
            'job-description' => 'Job Description'
        );

        return isset($types[$page_type]) ? $types[$page_type] : ucwords(str_replace('-', ' ', $page_type));
    }

    /**
     * Format schema as JSON-LD
     *
     * @param array $schema The schema array
     * @return string JSON-LD formatted string
     */
    private static function format_schema($schema) {
        return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Output schema in page head
     *
     * @param WP_Post $post The post object
     */
    public static function output_schema($post) {
        $schema = self::generate($post);

        if (!empty($schema)) {
            echo '<script type="application/ld+json">' . "\n";
            echo $schema . "\n";
            echo '</script>' . "\n";
        }
    }
}
