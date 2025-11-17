<?php
/**
 * Hub Page Management
 *
 * @package X0PA_Hiring_Extension
 * @subpackage Core
 * @since 1.0.0
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
     * Cache expiration (6 hours)
     */
    const CACHE_EXPIRATION = 6 * HOUR_IN_SECONDS;

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
    private static function set_hub_page_seo($page_id) {
        // Meta title
        update_post_meta($page_id, '_yoast_wpseo_title', 'Hiring Resources - Interview Questions & Job Descriptions | X0PA AI');

        // Meta description
        update_post_meta($page_id, '_yoast_wpseo_metadesc', 'Access comprehensive hiring resources including interview questions and job description templates for various roles. Expert guidance for HR professionals and recruiters.');

        // Focus keyword
        update_post_meta($page_id, '_yoast_wpseo_focuskw', 'hiring resources');
    }

    /**
     * Get all hiring pages grouped by job title
     *
     * @param bool $use_cache Whether to use cached results
     * @return array Grouped pages array
     */
    public static function get_grouped_pages($use_cache = true) {
        // Try cache first
        if ($use_cache) {
            $cached = get_transient(self::CACHE_KEY);
            if ($cached !== false) {
                return $cached;
            }
        }

        // Query all hiring pages
        $args = array(
            'post_type' => 'x0pa_hiring_page',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'meta_value',
            'meta_key' => '_x0pa_job_title',
            'order' => 'ASC'
        );

        $pages = get_posts($args);

        $grouped = array();

        foreach ($pages as $page) {
            $job_title = get_post_meta($page->ID, '_x0pa_job_title', true);
            $page_type = get_post_meta($page->ID, '_x0pa_page_type', true);
            $last_updated = get_post_meta($page->ID, '_x0pa_last_updated', true);

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
                'id' => $page->ID,
                'url' => get_permalink($page->ID),
                'title' => get_the_title($page->ID),
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

        // Cache results
        set_transient(self::CACHE_KEY, $grouped, self::CACHE_EXPIRATION);

        return $grouped;
    }

    /**
     * Get statistics about hub page content
     *
     * @return array Statistics
     */
    public static function get_statistics() {
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

        return $stats;
    }

    /**
     * Render hub page content
     *
     * @return string HTML output
     */
    public static function render_hub_page() {
        $grouped = self::get_grouped_pages();
        $stats = self::get_statistics();

        ob_start();
        ?>

        <div class="x0pa-hub-container">
            <!-- Hero Section -->
            <div class="hub-hero">
                <h1 class="hub-title">Hiring Resources</h1>
                <p class="hub-description">
                    Comprehensive interview questions and job description templates for <?php echo $stats['total_job_titles']; ?> roles.
                    Expert guidance for HR professionals and recruiters.
                </p>

                <div class="hub-stats">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $stats['total_job_titles']; ?></span>
                        <span class="stat-label">Job Titles</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $stats['total_interview_questions']; ?></span>
                        <span class="stat-label">Interview Guides</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $stats['total_job_descriptions']; ?></span>
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
                                    Updated: <?php echo date('M j, Y', strtotime($data['last_updated'])); ?>
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

        <script>
        // Client-side search functionality
        (function() {
            const searchInput = document.getElementById('hub-search');
            const resourceCards = document.querySelectorAll('.resource-card');
            const noResults = document.getElementById('no-results');

            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase().trim();
                    let visibleCount = 0;

                    resourceCards.forEach(card => {
                        const jobTitle = card.getAttribute('data-job-title');

                        if (searchTerm === '' || jobTitle.includes(searchTerm)) {
                            card.style.display = '';
                            visibleCount++;
                        } else {
                            card.style.display = 'none';
                        }
                    });

                    // Show/hide no results message
                    noResults.style.display = visibleCount === 0 ? 'block' : 'none';
                });
            }
        })();
        </script>

        <?php
        return ob_get_clean();
    }

    /**
     * Clear hub page cache
     */
    public static function clear_cache() {
        delete_transient(self::CACHE_KEY);
    }

    /**
     * Get hub page ID
     *
     * @return int|null Page ID or null if not found
     */
    public static function get_hub_page_id() {
        $page = get_page_by_path(self::PAGE_SLUG);
        return $page ? $page->ID : null;
    }

    /**
     * Get hub page URL
     *
     * @return string|null Hub page URL or null
     */
    public static function get_hub_page_url() {
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
    public static function add_to_menu($items, $args) {
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
}
