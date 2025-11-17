<?php
/**
 * Interview Overview Generator
 *
 * @package X0PA_Hiring_Extension
 * @subpackage Generators
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class X0PA_Interview_Overview
 *
 * Generates the questions overview card for interview-questions pages
 * Shows categorized question count and key topics
 */
class X0PA_Interview_Overview {

    /**
     * Generate overview section from content JSON
     *
     * @param string $content_json JSON-encoded content sections
     * @param string $job_title Job title
     * @return string Overview HTML
     */
    public static function generate($content_json, $job_title) {
        if (empty($content_json)) {
            return '';
        }

        $sections = json_decode($content_json, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($sections)) {
            return '';
        }

        // Analyze content structure
        $analysis = self::analyze_content($sections);

        ob_start();
        ?>

        <section id="questions-overview" class="interview-overview-section">
            <div class="overview-container">
                <div class="overview-header">
                    <h2 class="overview-title">Interview Questions Overview</h2>
                    <p class="overview-description">
                        This comprehensive guide covers <?php echo esc_html($analysis['total_questions']); ?>
                        carefully curated interview questions for hiring exceptional
                        <?php echo esc_html($job_title); ?> candidates.
                    </p>
                </div>

                <div class="overview-grid">
                    <!-- Question Categories -->
                    <div class="overview-card categories-card">
                        <div class="card-icon">📋</div>
                        <h3 class="card-title">Question Categories</h3>
                        <ul class="category-list">
                            <?php foreach ($analysis['categories'] as $category): ?>
                                <li class="category-item">
                                    <a href="#<?php echo esc_attr($category['id']); ?>"
                                       class="category-link">
                                        <span class="category-name">
                                            <?php echo esc_html($category['name']); ?>
                                        </span>
                                        <span class="category-count">
                                            <?php echo esc_html($category['count']); ?> questions
                                        </span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Statistics -->
                    <div class="overview-card stats-card">
                        <div class="card-icon">📊</div>
                        <h3 class="card-title">At a Glance</h3>
                        <div class="stats-grid">
                            <div class="stat-box">
                                <div class="stat-value"><?php echo esc_html($analysis['total_questions']); ?></div>
                                <div class="stat-label">Total Questions</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-value"><?php echo esc_html($analysis['total_categories']); ?></div>
                                <div class="stat-label">Categories</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-value"><?php echo esc_html($analysis['difficulty_levels']); ?></div>
                                <div class="stat-label">Difficulty Levels</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-value"><?php echo esc_html($analysis['estimated_time']); ?> min</div>
                                <div class="stat-label">Interview Time</div>
                            </div>
                        </div>
                    </div>

                    <!-- Key Topics -->
                    <?php if (!empty($analysis['key_topics'])): ?>
                        <div class="overview-card topics-card">
                            <div class="card-icon">🎯</div>
                            <h3 class="card-title">Key Topics Covered</h3>
                            <div class="topics-tags">
                                <?php foreach ($analysis['key_topics'] as $topic): ?>
                                    <span class="topic-tag"><?php echo esc_html($topic); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Interview Tips -->
                    <div class="overview-card tips-card">
                        <div class="card-icon">💡</div>
                        <h3 class="card-title">How to Use This Guide</h3>
                        <ul class="tips-list">
                            <li>Review all question categories before the interview</li>
                            <li>Prepare follow-up questions based on candidate responses</li>
                            <li>Adapt questions to match your specific role requirements</li>
                            <li>Use the answer guidelines to evaluate candidate responses</li>
                            <li>Combine with our <?php echo esc_html($job_title); ?> job description template</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <?php
        return ob_get_clean();
    }

    /**
     * Analyze content structure and extract statistics
     *
     * @param array $sections Content sections
     * @return array Analysis data
     */
    private static function analyze_content($sections) {
        $analysis = array(
            'total_questions' => 0,
            'total_categories' => 0,
            'categories' => array(),
            'difficulty_levels' => 3, // Typically Easy, Medium, Hard
            'estimated_time' => 0,
            'key_topics' => array()
        );

        foreach ($sections as $section) {
            if (!isset($section['section_id']) || !isset($section['section_title'])) {
                continue;
            }

            // Count questions in this section
            $question_count = 0;

            if (isset($section['questions']) && is_array($section['questions'])) {
                $question_count = count($section['questions']);
            }

            if ($question_count > 0) {
                $analysis['categories'][] = array(
                    'id' => $section['section_id'],
                    'name' => $section['section_title'],
                    'count' => $question_count
                );

                $analysis['total_questions'] += $question_count;
                $analysis['total_categories']++;

                // Extract key topics from section titles
                $analysis['key_topics'][] = $section['section_title'];
            }

            // Handle subsections
            if (isset($section['subsections']) && is_array($section['subsections'])) {
                foreach ($section['subsections'] as $subsection) {
                    if (isset($subsection['questions']) && is_array($subsection['questions'])) {
                        $subsection_count = count($subsection['questions']);
                        $analysis['total_questions'] += $subsection_count;
                    }
                }
            }
        }

        // Estimate interview time (2-3 minutes per question)
        $analysis['estimated_time'] = ceil($analysis['total_questions'] * 2.5);

        // Limit key topics to top 8
        $analysis['key_topics'] = array_slice($analysis['key_topics'], 0, 8);

        return $analysis;
    }

    /**
     * Generate compact overview (for alternative layouts)
     *
     * @param string $content_json JSON-encoded content
     * @return string Compact overview HTML
     */
    public static function generate_compact($content_json) {
        $sections = json_decode($content_json, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($sections)) {
            return '';
        }

        $analysis = self::analyze_content($sections);

        ob_start();
        ?>

        <div class="overview-compact">
            <div class="compact-stat">
                <strong><?php echo esc_html($analysis['total_questions']); ?></strong> questions
            </div>
            <div class="compact-stat">
                <strong><?php echo esc_html($analysis['total_categories']); ?></strong> categories
            </div>
            <div class="compact-stat">
                <strong>~<?php echo esc_html($analysis['estimated_time']); ?></strong> min interview
            </div>
        </div>

        <?php
        return ob_get_clean();
    }

    /**
     * Generate category navigation menu
     *
     * @param string $content_json JSON-encoded content
     * @return string Category navigation HTML
     */
    public static function generate_category_nav($content_json) {
        $sections = json_decode($content_json, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($sections)) {
            return '';
        }

        ob_start();
        ?>

        <nav class="category-nav" aria-label="Question Categories">
            <h3 class="category-nav-title">Jump to Category</h3>
            <ul class="category-nav-list">
                <?php foreach ($sections as $section): ?>
                    <?php
                    if (!isset($section['section_id']) || !isset($section['section_title'])) {
                        continue;
                    }

                    $question_count = isset($section['questions']) ? count($section['questions']) : 0;

                    if ($question_count === 0) {
                        continue;
                    }
                    ?>

                    <li class="category-nav-item">
                        <a href="#<?php echo esc_attr($section['section_id']); ?>"
                           class="category-nav-link">
                            <?php echo esc_html($section['section_title']); ?>
                            <span class="nav-count">(<?php echo esc_html($question_count); ?>)</span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <?php
        return ob_get_clean();
    }

    /**
     * Extract difficulty distribution from content
     *
     * @param string $content_json JSON-encoded content
     * @return array Difficulty distribution
     */
    public static function get_difficulty_distribution($content_json) {
        $sections = json_decode($content_json, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($sections)) {
            return array(
                'easy' => 0,
                'medium' => 0,
                'hard' => 0
            );
        }

        $distribution = array(
            'easy' => 0,
            'medium' => 0,
            'hard' => 0
        );

        foreach ($sections as $section) {
            if (!isset($section['questions']) || !is_array($section['questions'])) {
                continue;
            }

            foreach ($section['questions'] as $question) {
                if (isset($question['difficulty'])) {
                    $difficulty = strtolower($question['difficulty']);

                    if (isset($distribution[$difficulty])) {
                        $distribution[$difficulty]++;
                    }
                }
            }
        }

        return $distribution;
    }

    /**
     * Generate difficulty breakdown chart
     *
     * @param string $content_json JSON-encoded content
     * @return string Difficulty chart HTML
     */
    public static function generate_difficulty_chart($content_json) {
        $distribution = self::get_difficulty_distribution($content_json);
        $total = array_sum($distribution);

        if ($total === 0) {
            return '';
        }

        ob_start();
        ?>

        <div class="difficulty-chart">
            <h4 class="chart-title">Question Difficulty</h4>
            <div class="chart-bars">
                <?php foreach ($distribution as $level => $count): ?>
                    <?php
                    $percentage = ($count / $total) * 100;
                    ?>
                    <div class="chart-bar-row">
                        <span class="bar-label"><?php echo ucfirst($level); ?></span>
                        <div class="bar-container">
                            <div class="bar-fill bar-<?php echo esc_attr($level); ?>"
                                 style="width: <?php echo esc_attr($percentage); ?>%;"
                                 role="progressbar"
                                 aria-valuenow="<?php echo esc_attr($percentage); ?>"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
                            </div>
                        </div>
                        <span class="bar-count"><?php echo esc_html($count); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php
        return ob_get_clean();
    }
}
