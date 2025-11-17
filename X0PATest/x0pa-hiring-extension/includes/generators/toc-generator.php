<?php
/**
 * Table of Contents Generator
 *
 * Reusable PHP functions for automatically generating table of contents
 * from HTML content sections. Designed for hiring page templates and
 * WordPress plugin integration.
 *
 * @package x0pa-hiring-templates
 */

/**
 * Generate table of contents data from HTML content
 *
 * Parses HTML content to find all <section> elements with an ID and
 * the 'content-section' class, then extracts their IDs and titles.
 *
 * @param string $html_content The HTML content to parse
 * @return array Array of sections with 'id' and 'title' keys
 */
function generate_toc_from_content($html_content) {
    // Return empty array if no content
    if (empty($html_content)) {
        return array();
    }

    // Suppress DOMDocument warnings for HTML5 tags
    libxml_use_internal_errors(true);

    // Create DOMDocument and load HTML
    $dom = new DOMDocument();
    $dom->loadHTML(
        '<?xml encoding="utf-8" ?>' . $html_content,
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );

    // Clear any libxml errors
    libxml_clear_errors();

    // Initialize sections array
    $sections = array();

    // Find all section elements
    $section_elements = $dom->getElementsByTagName('section');

    foreach ($section_elements as $section) {
        // Check if section has ID attribute
        if (!$section->hasAttribute('id')) {
            continue;
        }

        // Check if section has 'content-section' class
        $class_attr = $section->getAttribute('class');
        if (strpos($class_attr, 'content-section') === false) {
            continue;
        }

        // Get the section ID
        $section_id = $section->getAttribute('id');

        // Find the title div within this section
        $title_text = '';
        $xpath = new DOMXPath($dom);
        $title_nodes = $xpath->query('.//div[contains(@class, "content-section__title")]', $section);

        if ($title_nodes->length > 0) {
            $title_text = trim($title_nodes->item(0)->textContent);
        }

        // If no title found, try to get first heading (h2, h3, etc.)
        if (empty($title_text)) {
            $heading_nodes = $xpath->query('.//h2 | .//h3', $section);
            if ($heading_nodes->length > 0) {
                $title_text = trim($heading_nodes->item(0)->textContent);
            }
        }

        // Only add section if we found both ID and title
        if (!empty($section_id) && !empty($title_text)) {
            $sections[] = array(
                'id' => $section_id,
                'title' => $title_text
            );
        }
    }

    return $sections;
}

/**
 * Render jump links HTML from sections array
 *
 * Generates the complete jump links navigation HTML with proper
 * BEM class structure for styling and JavaScript interaction.
 *
 * @param array $sections Array of sections with 'id' and 'title' keys
 * @param string $active_id Optional ID of the section to mark as active initially
 * @return string HTML string for jump links navigation
 */
function render_jump_links($sections, $active_id = '') {
    // Return empty nav if no sections
    if (empty($sections)) {
        return '<nav class="jump-links"><div class="jump-links__title">Contents</div><ul class="jump-links__list"></ul></nav>';
    }

    // If no active ID specified, use first section
    if (empty($active_id) && count($sections) > 0) {
        $active_id = $sections[0]['id'];
    }

    // Start building HTML
    $html = '<nav class="jump-links">' . "\n";
    $html .= '    <button class="jump-links__toggle" aria-expanded="false" aria-label="Toggle table of contents">' . "\n";
    $html .= '        <span class="jump-links__title">Contents</span>' . "\n";
    $html .= '        <svg class="jump-links__toggle-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">' . "\n";
    $html .= '            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>' . "\n";
    $html .= '        </svg>' . "\n";
    $html .= '    </button>' . "\n";
    $html .= '    <ul class="jump-links__list">' . "\n";

    // Add each section as a jump link
    foreach ($sections as $section) {
        $is_active = ($section['id'] === $active_id);
        $active_class = $is_active ? ' jump-links__link--active' : '';

        $html .= '        <li class="jump-links__item">' . "\n";
        $html .= '            <a href="#' . htmlspecialchars($section['id']) . '" class="jump-links__link' . $active_class . '">' . "\n";
        $html .= '                ' . htmlspecialchars($section['title']) . "\n";
        $html .= '            </a>' . "\n";
        $html .= '        </li>' . "\n";
    }

    $html .= '    </ul>' . "\n";
    $html .= '</nav>';

    return $html;
}

/**
 * Get section count for display
 *
 * @param array $sections Array of sections
 * @return int Number of sections
 */
function get_section_count($sections) {
    return count($sections);
}
?>
