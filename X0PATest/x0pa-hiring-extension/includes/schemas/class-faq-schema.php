<?php
/**
 * FAQ Schema Generator
 *
 * @package X0PA_Hiring_Extension
 * @subpackage Schemas
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class X0PA_FAQ_Schema
 *
 * Scans HTML content and generates Schema.org FAQPage structured data
 * Looks for .content-item elements with questions and answers
 */
class X0PA_FAQ_Schema {

    /**
     * Generate FAQ schema from page content
     *
     * @param string $content HTML content to scan
     * @param int $min_questions Minimum number of questions required (default: 3)
     * @return string JSON-LD schema markup or empty string
     */
    public static function generate($content, $min_questions = 3) {
        if (empty($content)) {
            return '';
        }

        // Parse HTML content
        $questions = self::extract_questions($content);

        // Only generate schema if we have enough questions
        if (count($questions) < $min_questions) {
            return '';
        }

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $questions
        );

        return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Extract questions and answers from HTML content
     *
     * @param string $content HTML content
     * @return array Array of Question schema objects
     */
    private static function extract_questions($content) {
        $questions = array();

        // Load HTML into DOMDocument
        $dom = new DOMDocument();
        libxml_use_internal_errors(true); // Suppress warnings for malformed HTML

        // Use UTF-8 encoding
        $content = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');
        @$dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        // Find all .content-item elements
        $items = $xpath->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' content-item ')]");

        foreach ($items as $item) {
            $question_data = self::extract_question_from_item($xpath, $item);

            if ($question_data) {
                $questions[] = $question_data;
            }
        }

        return $questions;
    }

    /**
     * Extract question and answer from a content-item element
     *
     * @param DOMXPath $xpath XPath object
     * @param DOMElement $item The content-item element
     * @return array|null Question schema or null
     */
    private static function extract_question_from_item($xpath, $item) {
        // Get question (h3.content-item__title)
        $question_nodes = $xpath->query(".//*[contains(concat(' ', normalize-space(@class), ' '), ' content-item__title ')]", $item);

        if ($question_nodes->length === 0) {
            return null;
        }

        $question_text = trim($question_nodes->item(0)->textContent);

        if (empty($question_text)) {
            return null;
        }

        // Get answer points (.content-item__box-list-item)
        $answer_nodes = $xpath->query(".//*[contains(concat(' ', normalize-space(@class), ' '), ' content-item__box-list-item ')]", $item);

        $answer_points = array();
        foreach ($answer_nodes as $node) {
            $text = trim($node->textContent);
            if (!empty($text)) {
                $answer_points[] = $text;
            }
        }

        // If no list items, try to get the entire content box
        if (empty($answer_points)) {
            $content_box = $xpath->query(".//*[contains(concat(' ', normalize-space(@class), ' '), ' content-item__box ')]", $item);

            if ($content_box->length > 0) {
                $text = trim($content_box->item(0)->textContent);
                if (!empty($text)) {
                    $answer_points[] = $text;
                }
            }
        }

        // Need at least one answer point
        if (empty($answer_points)) {
            return null;
        }

        // Format answer text
        $answer_text = self::format_answer_text($answer_points);

        return array(
            '@type' => 'Question',
            'name' => $question_text,
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text' => $answer_text
            )
        );
    }

    /**
     * Format answer points into readable text
     *
     * @param array $answer_points Array of answer text points
     * @return string Formatted answer text
     */
    private static function format_answer_text($answer_points) {
        // If single point, return as is
        if (count($answer_points) === 1) {
            return $answer_points[0];
        }

        // Multiple points - join with proper punctuation
        $formatted_points = array();

        foreach ($answer_points as $point) {
            // Ensure point ends with period
            $point = rtrim($point);
            if (!preg_match('/[.!?]$/', $point)) {
                $point .= '.';
            }
            $formatted_points[] = $point;
        }

        return implode(' ', $formatted_points);
    }

    /**
     * Output FAQ schema in page head
     *
     * @param string $content HTML content to scan
     * @param int $min_questions Minimum number of questions required
     */
    public static function output_schema($content, $min_questions = 3) {
        $schema = self::generate($content, $min_questions);

        if (!empty($schema)) {
            echo '<script type="application/ld+json">' . "\n";
            echo $schema . "\n";
            echo '</script>' . "\n";
        }
    }

    /**
     * Check if content has enough FAQ items to warrant schema
     *
     * @param string $content HTML content
     * @param int $min_questions Minimum questions required
     * @return bool True if has enough questions
     */
    public static function has_sufficient_questions($content, $min_questions = 3) {
        $questions = self::extract_questions($content);
        return count($questions) >= $min_questions;
    }

    /**
     * Get count of FAQ items in content
     *
     * @param string $content HTML content
     * @return int Number of FAQ items found
     */
    public static function get_question_count($content) {
        $questions = self::extract_questions($content);
        return count($questions);
    }
}
