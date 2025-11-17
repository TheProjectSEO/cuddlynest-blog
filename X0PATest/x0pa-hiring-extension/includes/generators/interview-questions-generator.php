<?php
/**
 * Interview Questions List Generator
 *
 * Reusable PHP function for automatically generating a numbered list
 * of interview questions from content-item__title elements in the page.
 *
 * @package x0pa-hiring-templates
 */

/**
 * Generate interview questions overview section from HTML content
 *
 * Parses HTML content to find all elements with 'content-item__title' class,
 * extracts the question text, counts them, and generates a numbered list
 * section with the format: "{COUNT} {JOB_TITLE} Interview Questions"
 *
 * @param string $html_content The HTML content to parse
 * @param string $job_title The job title (e.g., "Accountant", "Manager")
 * @return string HTML string for the questions overview section
 */
function generate_interview_questions_section($html_content, $job_title = '') {
    // Return empty section if no content
    if (empty($html_content)) {
        return '<section id="questions-overview" class="content-section"><div class="content-section__title">0 Interview Questions</div><div class="content-item__box-list"><ol class="interview-questions-list"></ol></div></section>';
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

    // Initialize questions array
    $questions = array();

    // Use XPath to find all elements with 'content-item__title' class
    $xpath = new DOMXPath($dom);
    $question_nodes = $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " content-item__title ")]');

    // Extract question text from each node
    foreach ($question_nodes as $node) {
        $question_text = trim($node->textContent);
        if (!empty($question_text)) {
            $questions[] = $question_text;
        }
    }

    // Count total questions
    $count = count($questions);

    // Build the section title
    $title = $count . ' ' . $job_title . ' Interview Questions';

    // Start building HTML
    $html = '<section id="questions-overview" class="content-section">' . "\n";
    $html .= '    <h2 class="interview-questions-list__title">' . "\n";
    $html .= '        ' . htmlspecialchars($title) . "\n";
    $html .= '    </h2>' . "\n";
    $html .= '    <div class="content-item__box-list">' . "\n";
    $html .= '        <ol class="interview-questions-list">' . "\n";

    // Add each question as a list item
    foreach ($questions as $question) {
        $html .= '            <li class="interview-questions-list-item"><h2>' . htmlspecialchars($question) . '</h2></li>' . "\n";
    }

    $html .= '        </ol>' . "\n";
    $html .= '    </div>' . "\n";
    $html .= '</section>' . "\n";

    return $html;
}

/**
 * Get interview questions count from HTML content
 *
 * @param string $html_content The HTML content to parse
 * @return int Number of questions found
 */
function get_interview_questions_count($html_content) {
    if (empty($html_content)) {
        return 0;
    }

    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML(
        '<?xml encoding="utf-8" ?>' . $html_content,
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);
    $question_nodes = $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " content-item__title ")]');

    return $question_nodes->length;
}

/**
 * Extract questions array and job title from HTML content
 *
 * This function parses HTML content to extract interview questions
 * for use in PDF generation or other purposes.
 *
 * @param string $html_content The HTML content to parse
 * @return array Associative array with 'questions' (array) and 'job_title' (string)
 */
function extract_questions_from_page($html_content) {
    // Default return values
    $result = array(
        'questions' => array(),
        'job_title' => 'Professional'
    );

    if (empty($html_content)) {
        return $result;
    }

    // Suppress DOMDocument warnings
    libxml_use_internal_errors(true);

    // Create DOMDocument and load HTML
    $dom = new DOMDocument();
    $dom->loadHTML(
        '<?xml encoding="utf-8" ?>' . $html_content,
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );
    libxml_clear_errors();

    // Use XPath for queries
    $xpath = new DOMXPath($dom);

    // Extract questions from content-item__title elements
    $question_nodes = $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " content-item__title ")]');

    foreach ($question_nodes as $node) {
        $question_text = trim($node->textContent);
        // Normalize whitespace: replace all multiple spaces/newlines/tabs with single space
        $question_text = preg_replace('/\s+/', ' ', $question_text);
        if (!empty($question_text)) {
            $result['questions'][] = $question_text;
        }
    }

    // Extract job title from hero section h1
    $hero_title_nodes = $xpath->query('//h1[contains(concat(" ", normalize-space(@class), " "), " hiring__hero-title ")]');

    if ($hero_title_nodes->length > 0) {
        $title_text = trim($hero_title_nodes->item(0)->textContent);
        // Remove " Interview Questions" suffix if present
        $title_text = preg_replace('/\s+Interview\s+Questions$/i', '', $title_text);
        // Remove leading number (e.g., "29 Accountant" → "Accountant")
        $title_text = preg_replace('/^\d+\s+/', '', $title_text);
        $result['job_title'] = trim($title_text);
    }

    return $result;
}
?>
