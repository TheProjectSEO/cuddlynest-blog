<?php
/**
 * Author Schema Generator
 *
 * @package X0PA_Hiring_Extension
 * @subpackage Schemas
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class X0PA_Author_Schema
 *
 * Generates Schema.org Person structured data for Nina Alag Suri
 */
class X0PA_Author_Schema {

    /**
     * Generate Person schema for Nina Alag Suri
     *
     * @param bool $as_reference If true, returns minimal reference. If false, returns full schema.
     * @return array Person schema
     */
    public static function generate($as_reference = false) {
        $base_schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => 'Nina Alag Suri',
            'jobTitle' => 'Founder and CEO',
            'url' => 'https://www.linkedin.com/in/ninaalagsuri/'
        );

        if ($as_reference) {
            // Return minimal reference for embedding in other schemas
            unset($base_schema['@context']);
            return $base_schema;
        }

        // Full schema with additional details
        $full_schema = array_merge($base_schema, array(
            'description' => 'Trailblazer in HR Technology & AI-driven recruitment solutions. Leading X0PA AI in revolutionizing talent acquisition through advanced analytics and machine learning.',
            'image' => 'https://x0pa.com/wp-content/uploads/2024/03/Nina-X0PA-1024x1024.png',
            'sameAs' => array(
                'https://www.linkedin.com/in/ninaalagsuri/',
                'https://x0pa.com/about-us/'
            ),
            'worksFor' => array(
                '@type' => 'Organization',
                'name' => 'X0PA AI',
                'url' => 'https://x0pa.com',
                'description' => 'AI-powered talent intelligence platform'
            ),
            'knowsAbout' => array(
                'HR Technology',
                'Artificial Intelligence',
                'Recruitment',
                'Talent Acquisition',
                'People Analytics',
                'Machine Learning'
            )
        ));

        return $full_schema;
    }

    /**
     * Get formatted JSON-LD string
     *
     * @param bool $as_reference If true, returns minimal reference
     * @return string JSON-LD formatted string
     */
    public static function get_json_ld($as_reference = false) {
        $schema = self::generate($as_reference);
        return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Output author schema in page head
     */
    public static function output_schema() {
        echo '<script type="application/ld+json">' . "\n";
        echo self::get_json_ld(false) . "\n";
        echo '</script>' . "\n";
    }

    /**
     * Get author byline HTML
     *
     * @param bool $include_image Whether to include author image
     * @return string Author byline HTML
     */
    public static function get_author_byline($include_image = false) {
        $html = '<div class="author-byline" itemscope itemtype="https://schema.org/Person">';

        if ($include_image) {
            $html .= '<img src="https://x0pa.com/wp-content/uploads/2024/03/Nina-X0PA-1024x1024.png"
                          alt="Nina Alag Suri"
                          class="author-image"
                          itemprop="image"
                          width="48"
                          height="48" />';
        }

        $html .= '<div class="author-info">';
        $html .= '<span class="author-label">Written by</span> ';
        $html .= '<a href="https://www.linkedin.com/in/ninaalagsuri/"
                     class="author-name"
                     itemprop="name"
                     target="_blank"
                     rel="noopener noreferrer">';
        $html .= 'Nina Alag Suri';
        $html .= '</a>';
        $html .= '<span class="author-title" itemprop="jobTitle"> - Founder & CEO, X0PA AI</span>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Add author schema to article schema
     *
     * @param array $article_schema Existing article schema
     * @return array Article schema with author added
     */
    public static function add_to_article_schema($article_schema) {
        $article_schema['author'] = self::generate(true);
        return $article_schema;
    }
}
