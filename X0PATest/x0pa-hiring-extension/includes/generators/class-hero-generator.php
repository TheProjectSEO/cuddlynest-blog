<?php
/**
 * Hero Section Generator
 *
 * @package X0PA_Hiring_Extension
 * @subpackage Generators
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class X0PA_Hero_Generator
 *
 * Generates dynamic hero sections for hiring pages
 */
class X0PA_Hero_Generator {

    /**
     * Generate hero section HTML
     *
     * @param WP_Post $post Post object
     * @param array $meta_data SEO metadata array
     * @return string Hero HTML
     */
    public static function generate($post, $meta_data = array()) {
        if (!$post || !is_a($post, 'WP_Post')) {
            return '';
        }

        $job_title = get_post_meta($post->ID, '_x0pa_job_title', true);
        $page_type = get_post_meta($post->ID, '_x0pa_page_type', true);
        $last_updated = get_post_meta($post->ID, '_x0pa_last_updated', true);

        // Get reading time from meta data
        $reading_time = isset($meta_data['reading_time']) ? $meta_data['reading_time'] : 0;

        // Generate hero based on page type
        if ($page_type === 'interview-questions') {
            return self::generate_interview_hero($job_title, $last_updated, $reading_time);
        } elseif ($page_type === 'job-description') {
            return self::generate_job_desc_hero($job_title, $last_updated, $reading_time);
        }

        return '';
    }

    /**
     * Generate interview questions hero
     *
     * @param string $job_title Job title
     * @param string $last_updated Last updated date
     * @param int $reading_time Reading time in minutes
     * @return string Hero HTML
     */
    private static function generate_interview_hero($job_title, $last_updated, $reading_time) {
        ob_start();
        ?>

        <section class="hero-section hero-interview">
            <div class="hero-container">
                <!-- Breadcrumb -->
                <nav class="hero-breadcrumb" aria-label="Breadcrumb">
                    <ol class="breadcrumb-list" itemscope itemtype="https://schema.org/BreadcrumbList">
                        <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <a href="<?php echo esc_url(home_url('/')); ?>" itemprop="item">
                                <span itemprop="name">Home</span>
                            </a>
                            <meta itemprop="position" content="1" />
                        </li>
                        <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <a href="<?php echo esc_url(home_url('/hiring/')); ?>" itemprop="item">
                                <span itemprop="name">Hiring Resources</span>
                            </a>
                            <meta itemprop="position" content="2" />
                        </li>
                        <li class="breadcrumb-item active" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <span itemprop="name"><?php echo esc_html($job_title); ?></span>
                            <meta itemprop="position" content="3" />
                        </li>
                    </ol>
                </nav>

                <!-- Main Hero Content -->
                <div class="hero-content">
                    <div class="hero-badge">
                        <span class="badge-icon">❓</span>
                        <span class="badge-text">Interview Questions</span>
                    </div>

                    <h1 class="hero-title">
                        <?php echo esc_html($job_title); ?> Interview Questions
                    </h1>

                    <p class="hero-description">
                        Comprehensive guide with expert-curated interview questions and answers for hiring
                        top <?php echo esc_html($job_title); ?> candidates. Prepare your interview process with
                        proven strategies and insights.
                    </p>

                    <!-- Meta Information -->
                    <div class="hero-meta">
                        <?php if (!empty($last_updated)): ?>
                            <div class="meta-item">
                                <span class="meta-icon">📅</span>
                                <span class="meta-text">
                                    Updated: <?php echo date('F j, Y', strtotime($last_updated)); ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <?php if ($reading_time > 0): ?>
                            <div class="meta-item">
                                <span class="meta-icon">⏱️</span>
                                <span class="meta-text">
                                    <?php echo X0PA_SEO_Meta::format_reading_time($reading_time); ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <div class="meta-item">
                            <span class="meta-icon">✍️</span>
                            <span class="meta-text">By Nina Alag Suri</span>
                        </div>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="hero-actions">
                        <a href="#questions-overview"
                           class="btn btn-primary"
                           data-scroll-to="questions-overview">
                            View Questions
                        </a>

                        <?php
                        // Link to complementary job description page
                        $job_desc_link = self::get_complementary_page_url($job_title, 'job-description');
                        if ($job_desc_link):
                        ?>
                            <a href="<?php echo esc_url($job_desc_link); ?>"
                               class="btn btn-secondary">
                                View Job Description
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Hero Visual (Optional) -->
                <div class="hero-visual">
                    <div class="hero-icon">
                        <!-- SVG Icon or Image -->
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" fill="currentColor"/>
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        <?php
        return ob_get_clean();
    }

    /**
     * Generate job description hero
     *
     * @param string $job_title Job title
     * @param string $last_updated Last updated date
     * @param int $reading_time Reading time in minutes
     * @return string Hero HTML
     */
    private static function generate_job_desc_hero($job_title, $last_updated, $reading_time) {
        ob_start();
        ?>

        <section class="hero-section hero-job-desc">
            <div class="hero-container">
                <!-- Breadcrumb -->
                <nav class="hero-breadcrumb" aria-label="Breadcrumb">
                    <ol class="breadcrumb-list" itemscope itemtype="https://schema.org/BreadcrumbList">
                        <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <a href="<?php echo esc_url(home_url('/')); ?>" itemprop="item">
                                <span itemprop="name">Home</span>
                            </a>
                            <meta itemprop="position" content="1" />
                        </li>
                        <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <a href="<?php echo esc_url(home_url('/hiring/')); ?>" itemprop="item">
                                <span itemprop="name">Hiring Resources</span>
                            </a>
                            <meta itemprop="position" content="2" />
                        </li>
                        <li class="breadcrumb-item active" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <span itemprop="name"><?php echo esc_html($job_title); ?></span>
                            <meta itemprop="position" content="3" />
                        </li>
                    </ol>
                </nav>

                <!-- Main Hero Content -->
                <div class="hero-content">
                    <div class="hero-badge">
                        <span class="badge-icon">📄</span>
                        <span class="badge-text">Job Description Template</span>
                    </div>

                    <h1 class="hero-title">
                        <?php echo esc_html($job_title); ?> Job Description
                    </h1>

                    <p class="hero-description">
                        Professional job description template for <?php echo esc_html($job_title); ?> positions.
                        Includes responsibilities, requirements, qualifications, and best practices for
                        attracting top talent.
                    </p>

                    <!-- Meta Information -->
                    <div class="hero-meta">
                        <?php if (!empty($last_updated)): ?>
                            <div class="meta-item">
                                <span class="meta-icon">📅</span>
                                <span class="meta-text">
                                    Updated: <?php echo date('F j, Y', strtotime($last_updated)); ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <?php if ($reading_time > 0): ?>
                            <div class="meta-item">
                                <span class="meta-icon">⏱️</span>
                                <span class="meta-text">
                                    <?php echo X0PA_SEO_Meta::format_reading_time($reading_time); ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <div class="meta-item">
                            <span class="meta-icon">✍️</span>
                            <span class="meta-text">By Nina Alag Suri</span>
                        </div>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="hero-actions">
                        <a href="#job-overview"
                           class="btn btn-primary"
                           data-scroll-to="job-overview">
                            View Template
                        </a>

                        <?php
                        // Link to complementary interview questions page
                        $interview_link = self::get_complementary_page_url($job_title, 'interview-questions');
                        if ($interview_link):
                        ?>
                            <a href="<?php echo esc_url($interview_link); ?>"
                               class="btn btn-secondary">
                                View Interview Questions
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Hero Visual (Optional) -->
                <div class="hero-visual">
                    <div class="hero-icon">
                        <!-- SVG Icon or Image -->
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" fill="currentColor"/>
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        <?php
        return ob_get_clean();
    }

    /**
     * Get URL of complementary page (opposite page type for same job)
     *
     * @param string $job_title Job title
     * @param string $target_page_type Target page type
     * @return string|null URL or null if not found
     */
    private static function get_complementary_page_url($job_title, $target_page_type) {
        $args = array(
            'post_type' => 'x0pa_hiring_page',
            'posts_per_page' => 1,
            'post_status' => 'publish',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => '_x0pa_job_title',
                    'value' => $job_title,
                    'compare' => '='
                ),
                array(
                    'key' => '_x0pa_page_type',
                    'value' => $target_page_type,
                    'compare' => '='
                )
            )
        );

        $pages = get_posts($args);

        return !empty($pages) ? get_permalink($pages[0]->ID) : null;
    }

    /**
     * Generate minimal hero (for alternative layouts)
     *
     * @param string $job_title Job title
     * @param string $page_type Page type
     * @return string Minimal hero HTML
     */
    public static function generate_minimal_hero($job_title, $page_type) {
        $type_label = $page_type === 'interview-questions' ? 'Interview Questions' : 'Job Description';

        ob_start();
        ?>

        <div class="hero-minimal">
            <h1 class="hero-minimal-title"><?php echo esc_html($job_title . ' ' . $type_label); ?></h1>
        </div>

        <?php
        return ob_get_clean();
    }
}
