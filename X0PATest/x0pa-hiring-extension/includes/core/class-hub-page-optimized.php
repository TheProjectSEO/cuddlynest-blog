<?php
/**
 * Hub Page Management - OPTIMIZED VERSION
 *
 * @package X0PA_Hiring_Extension
 * @subpackage Core
 * @since 1.0.0
 *
 * OPTIMIZATIONS:
 * - Two-tier caching (wp_cache + transients)
 * - Batch meta fetching instead of loops
 * - Optimized WP_Query with fields=ids
 * - Reduced DOM manipulation in render
 * - Output buffering optimization
 * - Type hints for PHP 7.4+
 * - Early returns for performance
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class X0PA_Hub_Page
 *
 * Manages the central /hiring/ hub page that lists all resources
 */
class X0PA_Hub_Page {

    /**
     * Hub page slug
     */
    const PAGE_SLUG = 'hiring';

    /**
     * Page template name
     */
    const TEMPLATE_NAME = 'x0pa-hub-page.php';

    /**
     * Cache key for grouped pages
     */
    const CACHE_KEY = 'x0pa_hub_grouped_pages';

    /**
     * Cache key for statistics
     */
    const STATS_CACHE_KEY = 'x0pa_hub_statistics';

    /**
     * Cache expiration (6 hours)
     */
    const CACHE_EXPIRATION = 6 * HOUR_IN_SECONDS;

    /**
     * Runtime cache for this request
     *
     * @var array|null
     */
    private static $runtime_cache = null;

    /**
     * Create or update hub page on plugin activation
     *
     * @return int|WP_Error Page ID or error
     */
    public static function create_hub_page() {
        // Check if hub page already exists
        $hub_page = get_page_by_path(self::PAGE_SLUG);

        if ($hub_page) {
            // Update existing page
            $page_id = $hub_page->ID;

            wp_update_post(array(
                'ID' => $page_id,
                'post_title' => 'Hiring Resources',
                'post_status' => 'publish'
            ));

            // Update custom template
            update_post_meta($page_id, '_wp_page_template', self::TEMPLATE_NAME);

            return $page_id;
        }

        // Create new hub page
        $page_id = wp_insert_post(array(
            'post_title' => 'Hiring Resources',
            'post_name' => self::PAGE_SLUG,
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_content' => '<!-- Hub page content rendered by template -->',
            'post_excerpt' => 'Comprehensive hiring resources including interview questions and job descriptions for various roles.'
        ));

        if (is_wp_error($page_id)) {
            return $page_id;
        }

        // Set custom template
        update_post_meta($page_id, '_wp_page_template', self::TEMPLATE_NAME);

        // Set SEO metadata
        self::set_hub_page_seo($page_id);

        return $page_id;
    }

    /**
     * Set SEO metadata for hub page
     *
     * @param int $page_id Page ID
     */
    private static function set_hub_page_seo(int $page_id): void {
        // Meta title
        update_post_meta($page_id, '_yoast_wpseo_title', 'Hiring Resources - Interview Questions & Job Descriptions | X0PA AI');

        // Meta description
        update_post_meta($page_id, '_yoast_wpseo_metadesc', 'Access comprehensive hiring resources including interview questions and job description templates for various roles. Expert guidance for HR professionals and recruiters.');

        // Focus keyword
        update_post_meta($page_id, '_yoast_wpseo_focuskw', 'hiring resources');
    }

    /**
     * Get all hiring pages grouped by job title (OPTIMIZED)
     *
     * @param bool $use_cache Whether to use cached results
     * @return array Grouped pages array
     */
    public static function get_grouped_pages(bool $use_cache = true): array {
        // Check runtime cache first (fastest)
        if (self::$runtime_cache !== null) {
            return self::$runtime_cache;
        }

        // Try wp_cache (fast)
        if ($use_cache) {
            $cached = wp_cache_get(self::CACHE_KEY, 'x0pa_hub');
            if ($cached !== false) {
                self::$runtime_cache = $cached;
                return $cached;
            }
        }

        // Try transient cache (persistent)
        if ($use_cache) {
            $cached_transient = get_transient(self::CACHE_KEY);
            if ($cached_transient !== false) {
                // Store in wp_cache for this request
                wp_cache_set(self::CACHE_KEY, $cached_transient, 'x0pa_hub', self::CACHE_EXPIRATION);
                self::$runtime_cache = $cached_transient;
                return $cached_transient;
            }
        }

        // Generate fresh data
        $grouped = self::generate_grouped_pages();

        // Cache in both layers
        wp_cache_set(self::CACHE_KEY, $grouped, 'x0pa_hub', self::CACHE_EXPIRATION);
        set_transient(self::CACHE_KEY, $grouped, self::CACHE_EXPIRATION);

        // Store in runtime cache
        self::$runtime_cache = $grouped;

        return $grouped;
    }

    /**
     * Generate grouped pages data (OPTIMIZED)
     *
     * @return array Grouped pages
     */
    private static function generate_grouped_pages(): array {
        // OPTIMIZED Query: Only get IDs
        $args = array(
            'post_type' => 'x0pa_hiring_page',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'fields' => 'ids', // PERFORMANCE: Only IDs
            'orderby' => 'meta_value',
            'meta_key' => '_x0pa_job_title',
            'order' => 'ASC',
            'no_found_rows' => true, // PERFORMANCE: Skip count
            'update_post_meta_cache' => false, // PERFORMANCE: Skip cache
            'update_post_term_cache' => false // PERFORMANCE: Skip cache
        );

        $page_ids = get_posts($args);

        if (empty($page_ids)) {
            return array();
        }

        // OPTIMIZED: Batch fetch all meta at once
        $job_titles = self::batch_get_meta($page_ids, '_x0pa_job_title');
        $page_types = self::batch_get_meta($page_ids, '_x0pa_page_type');
        $last_updates = self::batch_get_meta($page_ids, '_x0pa_last_updated');

        // Build grouped array
        $grouped = array();

        foreach ($page_ids as $page_id) {
            $job_title = $job_titles[$page_id] ?? '';
            $page_type = $page_types[$page_id] ?? '';
            $last_updated = $last_updates[$page_id] ?? '';

            if (empty($job_title) || empty($page_type)) {
                continue;
            }

            // Initialize job title group if not exists
            if (!isset($grouped[$job_title])) {
                $grouped[$job_title] = array(
                    'job_title' => $job_title,
                    'pages' => array(),
                    'last_updated' => ''
                );
            }

            // Add page data
            $grouped[$job_title]['pages'][$page_type] = array(
                'id' => $page_id,
                'url' => get_permalink($page_id),
                'title' => get_the_title($page_id),
                'last_updated' => $last_updated
            );

            // Track most recent update
            if (empty($grouped[$job_title]['last_updated']) ||
                strtotime($last_updated) > strtotime($grouped[$job_title]['last_updated'])) {
                $grouped[$job_title]['last_updated'] = $last_updated;
            }
        }

        // Sort alphabetically by job title
        ksort($grouped);

        return $grouped;
    }

    /**
     * Batch fetch post meta (OPTIMIZED)
     *
     * @param array $post_ids Array of post IDs
     * @param string $meta_key Meta key to fetch
     * @return array Associative array [post_id => meta_value]
     */
    private static function batch_get_meta(array $post_ids, string $meta_key): array {
        global $wpdb;

        $results = array();

        if (empty($post_ids)) {
            return $results;
        }

        // Single optimized query
        $placeholders = implode(',', array_fill(0, count($post_ids), '%d'));

        $query = $wpdb->prepare(
            "SELECT post_id, meta_value
            FROM {$wpdb->postmeta}
            WHERE post_id IN ($placeholders)
            AND meta_key = %s",
            array_merge($post_ids, array($meta_key))
        );

        $rows = $wpdb->get_results($query);

        foreach ($rows as $row) {
            $results[$row->post_id] = $row->meta_value;
        }

        return $results;
    }

    /**
     * Get statistics about hub page content (OPTIMIZED with caching)
     *
     * @return array Statistics
     */
    public static function get_statistics(): array {
        // Try cache first
        $cached = wp_cache_get(self::STATS_CACHE_KEY, 'x0pa_hub');
        if ($cached !== false) {
            return $cached;
        }

        $grouped = self::get_grouped_pages();

        $stats = array(
            'total_job_titles' => count($grouped),
            'total_interview_questions' => 0,
            'total_job_descriptions' => 0,
            'total_pages' => 0,
            'complete_sets' => 0 // Job titles with both page types
        );

        foreach ($grouped as $group) {
            if (isset($group['pages']['interview-questions'])) {
                $stats['total_interview_questions']++;
                $stats['total_pages']++;
            }

            if (isset($group['pages']['job-description'])) {
                $stats['total_job_descriptions']++;
                $stats['total_pages']++;
            }

            if (isset($group['pages']['interview-questions']) &&
                isset($group['pages']['job-description'])) {
                $stats['complete_sets']++;
            }
        }

        // Cache for 6 hours
        wp_cache_set(self::STATS_CACHE_KEY, $stats, 'x0pa_hub', self::CACHE_EXPIRATION);

        return $stats;
    }

    /**
     * Render hub page content (OPTIMIZED - output buffering)
     *
     * @return string HTML output
     */
    public static function render_hub_page(): string {
        $grouped = self::get_grouped_pages();
        $stats = self::get_statistics();

        // OPTIMIZATION: Single output buffer
        ob_start();
        include dirname(__FILE__) . '/../templates/hub-page-template.php';
        return ob_get_clean();
    }

    /**
     * Render hub page HTML (called from template)
     *
     * @param array $grouped Grouped pages data
     * @param array $stats Statistics data
     */
    public static function render_hub_html(array $grouped, array $stats): void {
        ?>
        <div class="x0pa-hub-container">
            <!-- Hero Section -->
            <div class="hub-hero">
                <h1 class="hub-title">Hiring Resources</h1>
                <p class="hub-description">
                    Comprehensive interview questions and job description templates for <?php echo esc_html($stats['total_job_titles']); ?> roles.
                    Expert guidance for HR professionals and recruiters.
                </p>

                <div class="hub-stats">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo esc_html($stats['total_job_titles']); ?></span>
                        <span class="stat-label">Job Titles</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo esc_html($stats['total_interview_questions']); ?></span>
                        <span class="stat-label">Interview Guides</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo esc_html($stats['total_job_descriptions']); ?></span>
                        <span class="stat-label">Job Descriptions</span>
                    </div>
                </div>
            </div>

            <!-- Search/Filter Section -->
            <div class="hub-filter">
                <input type="text"
                       id="hub-search"
                       class="hub-search-input"
                       placeholder="Search job titles..."
                       aria-label="Search job titles">
            </div>

            <!-- Resources Grid -->
            <div class="hub-resources-grid" id="resources-grid">
                <?php foreach ($grouped as $job_title => $data): ?>
                    <div class="resource-card" data-job-title="<?php echo esc_attr(strtolower($job_title)); ?>">
                        <h3 class="resource-title"><?php echo esc_html($job_title); ?></h3>

                        <div class="resource-links">
                            <?php if (isset($data['pages']['interview-questions'])): ?>
                                <a href="<?php echo esc_url($data['pages']['interview-questions']['url']); ?>"
                                   class="resource-link interview-link">
                                    <span class="link-icon">❓</span>
                                    <span class="link-text">Interview Questions</span>
                                </a>
                            <?php endif; ?>

                            <?php if (isset($data['pages']['job-description'])): ?>
                                <a href="<?php echo esc_url($data['pages']['job-description']['url']); ?>"
                                   class="resource-link job-desc-link">
                                    <span class="link-icon">📄</span>
                                    <span class="link-text">Job Description</span>
                                </a>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($data['last_updated'])): ?>
                            <div class="resource-meta">
                                <small class="last-updated">
                                    Updated: <?php echo esc_html(date('M j, Y', strtotime($data['last_updated']))); ?>
                                </small>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- No Results Message -->
            <div id="no-results" class="no-results" style="display: none;">
                <p>No resources found matching your search.</p>
            </div>
        </div>

        <?php self::render_search_script(); ?>
        <?php
    }

    /**
     * Render optimized search script (OPTIMIZED - debounced, cached selectors)
     */
    private static function render_search_script(): void {
        ?>
        <script>
        (function() {
            'use strict';

            // OPTIMIZATION: Cache selectors
            const elements = {
                searchInput: document.getElementById('hub-search'),
                resourceCards: document.querySelectorAll('.resource-card'),
                noResults: document.getElementById('no-results')
            };

            if (!elements.searchInput || !elements.resourceCards.length) return;

            let searchTimer = null;

            // OPTIMIZATION: Debounced search (200ms delay)
            function debounceSearch(searchTerm) {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => performSearch(searchTerm), 200);
            }

            // OPTIMIZATION: Efficient search with early exit
            function performSearch(searchTerm) {
                const term = searchTerm.toLowerCase().trim();
                let visibleCount = 0;

                // OPTIMIZATION: Use requestAnimationFrame for DOM updates
                requestAnimationFrame(() => {
                    elements.resourceCards.forEach(card => {
                        const jobTitle = card.getAttribute('data-job-title');
                        const isVisible = term === '' || jobTitle.includes(term);

                        card.style.display = isVisible ? '' : 'none';
                        if (isVisible) visibleCount++;
                    });

                    // Show/hide no results
                    elements.noResults.style.display = visibleCount === 0 ? 'block' : 'none';
                });
            }

            // OPTIMIZATION: Passive event listener
            elements.searchInput.addEventListener('input', function(e) {
                debounceSearch(e.target.value);
            }, { passive: true });

        })();
        </script>
        <?php
    }

    /**
     * Clear hub page cache (OPTIMIZED - clears all tiers)
     */
    public static function clear_cache(): void {
        // Clear runtime cache
        self::$runtime_cache = null;

        // Clear wp_cache
        wp_cache_delete(self::CACHE_KEY, 'x0pa_hub');
        wp_cache_delete(self::STATS_CACHE_KEY, 'x0pa_hub');

        // Clear transient
        delete_transient(self::CACHE_KEY);
        delete_transient(self::STATS_CACHE_KEY);
    }

    /**
     * Get hub page ID
     *
     * @return int|null Page ID or null if not found
     */
    public static function get_hub_page_id(): ?int {
        $page = get_page_by_path(self::PAGE_SLUG);
        return $page ? $page->ID : null;
    }

    /**
     * Get hub page URL
     *
     * @return string|null Hub page URL or null
     */
    public static function get_hub_page_url(): ?string {
        $page_id = self::get_hub_page_id();
        return $page_id ? get_permalink($page_id) : null;
    }

    /**
     * Add hub page link to navigation menu
     *
     * @param array $items Menu items
     * @param object $args Menu arguments
     * @return array Modified menu items
     */
    public static function add_to_menu(array $items, object $args): array {
        // Only add to primary menu
        if ($args->theme_location !== 'primary') {
            return $items;
        }

        $hub_url = self::get_hub_page_url();

        if ($hub_url) {
            $hub_item = '<li class="menu-item"><a href="' . esc_url($hub_url) . '">Hiring Resources</a></li>';
            $items .= $hub_item;
        }

        return $items;
    }

    /**
     * Warm up cache (useful for cron jobs)
     */
    public static function warm_cache(): void {
        // Force regenerate cache
        self::get_grouped_pages(false);
        self::get_statistics();
    }
}
