<?php
/**
 * Table of Contents Generator
 *
 * @package X0PA_Hiring_Extension
 * @subpackage Generators
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class X0PA_TOC_Generator
 *
 * Generates dynamic table of contents with jump links and ScrollSpy support
 */
class X0PA_TOC_Generator {

    /**
     * Generate TOC structure from content JSON
     *
     * @param string $content_json JSON-encoded content sections
     * @return array TOC structure
     */
    public static function generate_from_content($content_json) {
        if (empty($content_json)) {
            return array();
        }

        $sections = json_decode($content_json, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($sections)) {
            return array();
        }

        $toc = array();

        foreach ($sections as $section) {
            if (!isset($section['section_id']) || !isset($section['section_title'])) {
                continue;
            }

            $toc_item = array(
                'id' => $section['section_id'],
                'title' => $section['section_title'],
                'count' => 0,
                'subsections' => array()
            );

            // Count questions if present
            if (isset($section['questions']) && is_array($section['questions'])) {
                $toc_item['count'] = count($section['questions']);
            }

            // Handle subsections
            if (isset($section['subsections']) && is_array($section['subsections'])) {
                foreach ($section['subsections'] as $subsection) {
                    $toc_item['subsections'][] = array(
                        'id' => $subsection['id'] ?? '',
                        'title' => $subsection['title'] ?? '',
                        'count' => isset($subsection['questions']) ? count($subsection['questions']) : 0
                    );
                }
            }

            $toc[] = $toc_item;
        }

        return $toc;
    }

    /**
     * Render jump links sidebar HTML
     *
     * @param array $sections TOC sections
     * @param string $first_section_id ID of first section to highlight (default: 'questions-overview')
     * @param bool $sticky Whether sidebar should be sticky (default: true)
     * @return string HTML output
     */
    public static function render_jump_links($sections, $first_section_id = 'questions-overview', $sticky = true) {
        if (empty($sections)) {
            return '';
        }

        $sticky_class = $sticky ? 'toc-sticky' : '';

        ob_start();
        ?>

        <nav class="toc-sidebar <?php echo esc_attr($sticky_class); ?>"
             aria-label="Table of Contents"
             data-spy="scroll"
             data-target="#main-content">

            <div class="toc-header">
                <h3 class="toc-title">Table of Contents</h3>
            </div>

            <ul class="toc-list" id="toc-navigation">
                <?php foreach ($sections as $index => $section): ?>
                    <li class="toc-item <?php echo $index === 0 ? 'active' : ''; ?>">
                        <a href="#<?php echo esc_attr($section['id']); ?>"
                           class="toc-link"
                           data-section="<?php echo esc_attr($section['id']); ?>">
                            <span class="toc-text"><?php echo esc_html($section['title']); ?></span>

                            <?php if (!empty($section['count'])): ?>
                                <span class="toc-count"><?php echo esc_html($section['count']); ?></span>
                            <?php endif; ?>
                        </a>

                        <?php if (!empty($section['subsections'])): ?>
                            <ul class="toc-subsections">
                                <?php foreach ($section['subsections'] as $subsection): ?>
                                    <li class="toc-subitem">
                                        <a href="#<?php echo esc_attr($subsection['id']); ?>"
                                           class="toc-sublink"
                                           data-section="<?php echo esc_attr($subsection['id']); ?>">
                                            <?php echo esc_html($subsection['title']); ?>
                                            <?php if (!empty($subsection['count'])): ?>
                                                <span class="toc-count"><?php echo esc_html($subsection['count']); ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Progress indicator -->
            <div class="toc-progress">
                <div class="toc-progress-bar" id="reading-progress" style="width: 0%;"></div>
            </div>
        </nav>

        <?php
        return ob_get_clean();
    }

    /**
     * Render mobile-friendly TOC dropdown
     *
     * @param array $sections TOC sections
     * @return string HTML output
     */
    public static function render_mobile_toc($sections) {
        if (empty($sections)) {
            return '';
        }

        ob_start();
        ?>

        <div class="toc-mobile">
            <button class="toc-mobile-toggle"
                    aria-expanded="false"
                    aria-controls="mobile-toc-menu">
                <span class="toc-mobile-icon">☰</span>
                <span class="toc-mobile-text">Jump to Section</span>
            </button>

            <div class="toc-mobile-menu" id="mobile-toc-menu" hidden>
                <ul class="toc-mobile-list">
                    <?php foreach ($sections as $section): ?>
                        <li class="toc-mobile-item">
                            <a href="#<?php echo esc_attr($section['id']); ?>"
                               class="toc-mobile-link">
                                <?php echo esc_html($section['title']); ?>
                                <?php if (!empty($section['count'])): ?>
                                    <span class="toc-count">(<?php echo esc_html($section['count']); ?>)</span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <script>
        // Mobile TOC toggle
        (function() {
            const toggle = document.querySelector('.toc-mobile-toggle');
            const menu = document.querySelector('.toc-mobile-menu');

            if (toggle && menu) {
                toggle.addEventListener('click', function() {
                    const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
                    toggle.setAttribute('aria-expanded', !isExpanded);
                    menu.hidden = isExpanded;
                });

                // Close when clicking a link
                const links = menu.querySelectorAll('.toc-mobile-link');
                links.forEach(link => {
                    link.addEventListener('click', function() {
                        toggle.setAttribute('aria-expanded', 'false');
                        menu.hidden = true;
                    });
                });
            }
        })();
        </script>

        <?php
        return ob_get_clean();
    }

    /**
     * Generate ScrollSpy JavaScript
     *
     * @param array $sections TOC sections
     * @return string JavaScript code
     */
    public static function generate_scrollspy_script($sections) {
        if (empty($sections)) {
            return '';
        }

        ob_start();
        ?>

        <script>
        /**
         * ScrollSpy implementation for TOC
         * Updates active state based on scroll position
         */
        (function() {
            const tocLinks = document.querySelectorAll('.toc-link, .toc-sublink');
            const sections = document.querySelectorAll('[data-section]');
            const progressBar = document.getElementById('reading-progress');

            let ticking = false;

            // Section IDs for tracking
            const sectionIds = <?php echo json_encode(array_column($sections, 'id')); ?>;

            /**
             * Update active TOC link based on scroll position
             */
            function updateActiveTOC() {
                const scrollPosition = window.scrollY + 100; // Offset for header

                let currentSection = null;

                // Find current section
                sectionIds.forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        const offsetTop = element.offsetTop;
                        if (scrollPosition >= offsetTop) {
                            currentSection = id;
                        }
                    }
                });

                // Update active states
                tocLinks.forEach(link => {
                    link.parentElement.classList.remove('active');

                    const linkSection = link.getAttribute('data-section');
                    if (linkSection === currentSection) {
                        link.parentElement.classList.add('active');
                    }
                });

                // Update progress bar
                updateProgressBar();

                ticking = false;
            }

            /**
             * Update reading progress bar
             */
            function updateProgressBar() {
                if (!progressBar) return;

                const windowHeight = window.innerHeight;
                const documentHeight = document.documentElement.scrollHeight;
                const scrollTop = window.scrollY;

                const progress = (scrollTop / (documentHeight - windowHeight)) * 100;

                progressBar.style.width = Math.min(progress, 100) + '%';
            }

            /**
             * Request animation frame for scroll events
             */
            function onScroll() {
                if (!ticking) {
                    window.requestAnimationFrame(updateActiveTOC);
                    ticking = true;
                }
            }

            // Attach scroll listener
            window.addEventListener('scroll', onScroll, { passive: true });

            // Smooth scroll on TOC link click
            tocLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    const targetId = this.getAttribute('href').substring(1);
                    const targetElement = document.getElementById(targetId);

                    if (targetElement) {
                        const offsetTop = targetElement.offsetTop - 80; // Account for header

                        window.scrollTo({
                            top: offsetTop,
                            behavior: 'smooth'
                        });

                        // Update URL hash without jump
                        history.pushState(null, null, '#' + targetId);
                    }
                });
            });

            // Initialize on load
            updateActiveTOC();

            // Handle deep links on page load
            if (window.location.hash) {
                setTimeout(() => {
                    const targetId = window.location.hash.substring(1);
                    const targetElement = document.getElementById(targetId);

                    if (targetElement) {
                        const offsetTop = targetElement.offsetTop - 80;
                        window.scrollTo({
                            top: offsetTop,
                            behavior: 'smooth'
                        });
                    }
                }, 100);
            }
        })();
        </script>

        <?php
        return ob_get_clean();
    }

    /**
     * Extract sections from HTML content
     *
     * @param string $html_content HTML content
     * @return array Sections with IDs and titles
     */
    public static function extract_sections_from_html($html_content) {
        if (empty($html_content)) {
            return array();
        }

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        @$dom->loadHTML(mb_convert_encoding($html_content, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $headings = $xpath->query('//h2[@id] | //h3[@id]');

        $sections = array();

        foreach ($headings as $heading) {
            $id = $heading->getAttribute('id');
            $title = trim($heading->textContent);

            if (!empty($id) && !empty($title)) {
                $sections[] = array(
                    'id' => $id,
                    'title' => $title,
                    'level' => $heading->tagName === 'h2' ? 2 : 3,
                    'count' => 0
                );
            }
        }

        return $sections;
    }

    /**
     * Add IDs to headings in HTML content
     *
     * @param string $html_content HTML content
     * @return string Modified HTML with IDs added
     */
    public static function add_heading_ids($html_content) {
        if (empty($html_content)) {
            return $html_content;
        }

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        @$dom->loadHTML(mb_convert_encoding($html_content, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $headings = $xpath->query('//h2 | //h3');

        $used_ids = array();

        foreach ($headings as $heading) {
            // Skip if already has ID
            if ($heading->hasAttribute('id')) {
                continue;
            }

            // Generate ID from text
            $text = trim($heading->textContent);
            $id = self::generate_id_from_text($text, $used_ids);

            $heading->setAttribute('id', $id);
            $used_ids[] = $id;
        }

        return $dom->saveHTML();
    }

    /**
     * Generate unique ID from text
     *
     * @param string $text Text to convert
     * @param array $used_ids Already used IDs
     * @return string Unique ID
     */
    private static function generate_id_from_text($text, $used_ids = array()) {
        // Convert to lowercase and replace spaces with hyphens
        $id = strtolower($text);
        $id = preg_replace('/[^a-z0-9\s-]/', '', $id);
        $id = preg_replace('/\s+/', '-', $id);
        $id = trim($id, '-');

        // Ensure uniqueness
        $original_id = $id;
        $counter = 1;

        while (in_array($id, $used_ids)) {
            $id = $original_id . '-' . $counter;
            $counter++;
        }

        return $id;
    }
}
