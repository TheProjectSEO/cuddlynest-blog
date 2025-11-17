<?php
/**
 * Smart Internal Linking Engine
 *
 * @package X0PA_Hiring_Extension
 * @subpackage Core
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class X0PA_Internal_Linking
 *
 * Implements smart relevance-based internal linking algorithm
 * Links pages based on job title similarity using string matching and keyword overlap
 */
class X0PA_Internal_Linking {

    /**
     * Cache group for transients
     */
    const CACHE_GROUP = 'x0pa_internal_links';

    /**
     * Cache expiration (12 hours)
     */
    const CACHE_EXPIRATION = 12 * HOUR_IN_SECONDS;

    /**
     * Get related pages based on job title similarity
     *
     * @param string $current_job_title Current page job title
     * @param string $page_type Page type (interview-questions, job-description)
     * @param int $current_post_id Current post ID to exclude
     * @param int $limit Number of related pages to return (default: 4)
     * @return array Array of related pages with scores
     */
    public static function get_related_pages($current_job_title, $page_type, $current_post_id, $limit = 4) {
        // Validate inputs
        if (empty($current_job_title) || empty($page_type) || empty($current_post_id)) {
            return array();
        }

        // Try cache first
        $cache_key = self::get_cache_key($current_post_id, $page_type, $limit);
        $cached = get_transient($cache_key);

        if ($cached !== false) {
            return $cached;
        }

        // Query all pages of same type
        $args = array(
            'post_type' => 'x0pa_hiring_page',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => '_x0pa_page_type',
                    'value' => $page_type,
                    'compare' => '='
                )
            ),
            'post__not_in' => array($current_post_id), // CRITICAL: Exclude self
            'fields' => 'ids' // Only get IDs for performance
        );

        $page_ids = get_posts($args);

        if (empty($page_ids)) {
            return array();
        }

        // Calculate similarity scores
        $scored_pages = array();

        foreach ($page_ids as $page_id) {
            $page_job_title = get_post_meta($page_id, '_x0pa_job_title', true);

            if (empty($page_job_title)) {
                continue;
            }

            $score = self::calculate_similarity($current_job_title, $page_job_title);

            $scored_pages[] = array(
                'post_id' => $page_id,
                'job_title' => $page_job_title,
                'url' => get_permalink($page_id),
                'score' => $score,
                'page_type' => $page_type
            );
        }

        // Sort by score descending
        usort($scored_pages, function($a, $b) {
            if ($b['score'] === $a['score']) {
                // If scores are equal, sort alphabetically by job title
                return strcmp($a['job_title'], $b['job_title']);
            }
            return $b['score'] - $a['score'];
        });

        // Get top N results
        $related_pages = array_slice($scored_pages, 0, $limit);

        // Cache results
        set_transient($cache_key, $related_pages, self::CACHE_EXPIRATION);

        return $related_pages;
    }

    /**
     * Calculate similarity score between two job titles
     *
     * Uses weighted combination of:
     * - String similarity (60%)
     * - Keyword overlap (40%)
     *
     * @param string $title1 First job title
     * @param string $title2 Second job title
     * @return float Similarity score (0-100)
     */
    private static function calculate_similarity($title1, $title2) {
        // Normalize titles
        $title1 = self::normalize_title($title1);
        $title2 = self::normalize_title($title2);

        // Calculate string similarity using similar_text
        similar_text($title1, $title2, $string_percent);

        // Calculate keyword overlap
        $overlap_percent = self::calculate_keyword_overlap($title1, $title2);

        // Weighted average (60% string similarity, 40% keyword overlap)
        $final_score = ($string_percent * 0.6) + ($overlap_percent * 0.4);

        return round($final_score, 2);
    }

    /**
     * Normalize job title for comparison
     *
     * @param string $title Job title
     * @return string Normalized title
     */
    private static function normalize_title($title) {
        // Convert to lowercase
        $title = strtolower($title);

        // Remove common suffixes/prefixes
        $remove_terms = array(
            'senior', 'junior', 'sr', 'jr', 'mid-level', 'entry-level',
            'lead', 'chief', 'head of', 'director of', 'manager',
            'i', 'ii', 'iii', 'iv', 'v'
        );

        foreach ($remove_terms as $term) {
            $title = str_replace($term, '', $title);
        }

        // Remove extra spaces
        $title = preg_replace('/\s+/', ' ', $title);

        return trim($title);
    }

    /**
     * Calculate keyword overlap percentage
     *
     * @param string $title1 First title (normalized)
     * @param string $title2 Second title (normalized)
     * @return float Overlap percentage (0-100)
     */
    private static function calculate_keyword_overlap($title1, $title2) {
        // Split into words
        $words1 = self::extract_keywords($title1);
        $words2 = self::extract_keywords($title2);

        if (empty($words1) || empty($words2)) {
            return 0;
        }

        // Find common keywords
        $common = array_intersect($words1, $words2);

        // Calculate overlap as percentage of larger set
        $max_words = max(count($words1), count($words2));

        return (count($common) / $max_words) * 100;
    }

    /**
     * Extract meaningful keywords from title
     *
     * @param string $title Normalized title
     * @return array Array of keywords
     */
    private static function extract_keywords($title) {
        // Split into words
        $words = explode(' ', $title);

        // Remove stop words
        $stop_words = array('and', 'or', 'the', 'a', 'an', 'of', 'for', 'in', 'on', 'at', 'to', 'with');

        $keywords = array_filter($words, function($word) use ($stop_words) {
            // Remove empty, too short, or stop words
            return !empty($word) && strlen($word) > 2 && !in_array($word, $stop_words);
        });

        return array_values($keywords);
    }

    /**
     * Get cache key for related pages
     *
     * @param int $post_id Post ID
     * @param string $page_type Page type
     * @param int $limit Result limit
     * @return string Cache key
     */
    private static function get_cache_key($post_id, $page_type, $limit) {
        return self::CACHE_GROUP . '_' . $post_id . '_' . $page_type . '_' . $limit;
    }

    /**
     * Clear cache for a specific post
     *
     * @param int $post_id Post ID
     */
    public static function clear_post_cache($post_id) {
        $page_type = get_post_meta($post_id, '_x0pa_page_type', true);

        if (empty($page_type)) {
            return;
        }

        // Clear for different limits
        for ($limit = 1; $limit <= 10; $limit++) {
            $cache_key = self::get_cache_key($post_id, $page_type, $limit);
            delete_transient($cache_key);
        }
    }

    /**
     * Clear all internal linking caches
     */
    public static function clear_all_caches() {
        global $wpdb;

        // Delete all transients with our prefix
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options}
                WHERE option_name LIKE %s",
                '_transient_' . self::CACHE_GROUP . '_%'
            )
        );

        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options}
                WHERE option_name LIKE %s",
                '_transient_timeout_' . self::CACHE_GROUP . '_%'
            )
        );
    }

    /**
     * Get related pages for both page types (complementary linking)
     *
     * @param string $job_title Job title
     * @param int $current_post_id Current post ID
     * @param int $limit Number of results per type
     * @return array Array with 'interview-questions' and 'job-description' keys
     */
    public static function get_complementary_pages($job_title, $current_post_id, $limit = 2) {
        $results = array(
            'interview-questions' => array(),
            'job-description' => array()
        );

        foreach (array('interview-questions', 'job-description') as $page_type) {
            $results[$page_type] = self::get_related_pages(
                $job_title,
                $page_type,
                $current_post_id,
                $limit
            );
        }

        return $results;
    }

    /**
     * Render related pages HTML
     *
     * @param array $related_pages Array of related pages
     * @param string $title Section title
     * @return string HTML output
     */
    public static function render_related_pages($related_pages, $title = 'Related Resources') {
        if (empty($related_pages)) {
            return '';
        }

        $html = '<div class="related-pages-section">';
        $html .= '<h3 class="related-pages-title">' . esc_html($title) . '</h3>';
        $html .= '<ul class="related-pages-list">';

        foreach ($related_pages as $page) {
            $html .= '<li class="related-page-item">';
            $html .= '<a href="' . esc_url($page['url']) . '" class="related-page-link">';
            $html .= esc_html($page['job_title']);
            $html .= '</a>';

            // Optionally show similarity score (for debugging)
            if (defined('WP_DEBUG') && WP_DEBUG) {
                $html .= ' <span class="similarity-score">(' . $page['score'] . '% match)</span>';
            }

            $html .= '</li>';
        }

        $html .= '</ul>';
        $html .= '</div>';

        return $html;
    }
}
