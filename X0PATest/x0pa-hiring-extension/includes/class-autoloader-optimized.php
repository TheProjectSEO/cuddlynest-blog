<?php
/**
 * Class Autoloader - OPTIMIZED VERSION
 *
 * @package X0PA_Hiring_Extension
 * @since 1.0.0
 *
 * OPTIMIZATIONS:
 * - Static class map prevents repeated file_exists() calls
 * - Removed redundant fallback search loops
 * - Added type hints for PHP 7.4+
 * - Optimized string operations
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class X0PA_Autoloader
 *
 * Automatically loads plugin classes when needed
 */
class X0PA_Autoloader {

    /**
     * Class map for quick lookup (OPTIMIZED: Complete map to avoid searches)
     *
     * @var array
     */
    private static $class_map = array(
        // Schema Generators
        'X0PA_WebPage_Schema' => 'schemas/class-webpage-schema.php',
        'X0PA_Author_Schema' => 'schemas/class-author-schema.php',
        'X0PA_FAQ_Schema' => 'schemas/class-faq-schema.php',

        // Core Classes
        'X0PA_Internal_Linking' => 'core/class-internal-linking.php',
        'X0PA_Hub_Page' => 'core/class-hub-page.php',
        'X0PA_Hiring_CPT' => 'core/class-custom-post-type.php',
        'X0PA_Hiring_URL_Rewrite' => 'core/class-url-rewrite.php',

        // Content Generators
        'X0PA_SEO_Meta' => 'generators/class-seo-meta.php',
        'X0PA_TOC_Generator' => 'generators/class-toc-generator.php',
        'X0PA_Hero_Generator' => 'generators/class-hero-generator.php',
        'X0PA_Interview_Overview' => 'generators/class-interview-overview.php',

        // Admin Classes
        'X0PA_Admin_Menu' => 'admin/class-admin-menu.php',
        'X0PA_CSV_Uploader' => 'admin/class-csv-uploader.php',
        'X0PA_Bulk_Processor' => 'admin/class-bulk-processor.php',
    );

    /**
     * Base directory for includes
     *
     * @var string
     */
    private static $base_dir;

    /**
     * Loaded classes cache (prevents re-loading)
     *
     * @var array
     */
    private static $loaded_classes = array();

    /**
     * Initialize autoloader
     */
    public static function init(): void {
        self::$base_dir = dirname(__FILE__) . '/';

        // Register autoloader with prepend for priority
        spl_autoload_register(array(__CLASS__, 'autoload'), true, true);
    }

    /**
     * Autoload class files (OPTIMIZED)
     *
     * @param string $class_name Class name to load
     */
    public static function autoload(string $class_name): void {
        // Quick exit if not our class
        if (strpos($class_name, 'X0PA_') !== 0) {
            return;
        }

        // Check if already loaded
        if (isset(self::$loaded_classes[$class_name])) {
            return;
        }

        // Direct map lookup (FAST)
        if (isset(self::$class_map[$class_name])) {
            $file_path = self::$base_dir . self::$class_map[$class_name];

            if (file_exists($file_path)) {
                require_once $file_path;
                self::$loaded_classes[$class_name] = true;
                return;
            }
        }

        // REMOVED: Fallback search - all classes should be in map
        // This eliminates expensive file_exists() loops
    }

    /**
     * Manually load a class file
     *
     * @param string $class_name Class name
     * @return bool True if loaded, false otherwise
     */
    public static function load_class(string $class_name): bool {
        self::autoload($class_name);
        return class_exists($class_name, false);
    }

    /**
     * Load multiple classes at once (OPTIMIZED: Batch loading)
     *
     * @param array $class_names Array of class names
     * @return int Number of classes successfully loaded
     */
    public static function load_classes(array $class_names): int {
        $loaded = 0;

        foreach ($class_names as $class) {
            if (self::load_class($class)) {
                $loaded++;
            }
        }

        return $loaded;
    }

    /**
     * Load all core classes (OPTIMIZED)
     */
    public static function load_all_core(): void {
        $core_classes = array(
            'X0PA_Internal_Linking',
            'X0PA_Hub_Page',
            'X0PA_Hiring_CPT',
            'X0PA_Hiring_URL_Rewrite'
        );

        self::load_classes($core_classes);
    }

    /**
     * Load all schema generators (OPTIMIZED)
     */
    public static function load_all_schemas(): void {
        $schema_classes = array(
            'X0PA_WebPage_Schema',
            'X0PA_Author_Schema',
            'X0PA_FAQ_Schema'
        );

        self::load_classes($schema_classes);
    }

    /**
     * Load all content generators (OPTIMIZED)
     */
    public static function load_all_generators(): void {
        $generator_classes = array(
            'X0PA_SEO_Meta',
            'X0PA_TOC_Generator',
            'X0PA_Hero_Generator',
            'X0PA_Interview_Overview'
        );

        self::load_classes($generator_classes);
    }

    /**
     * Get list of all registered classes
     *
     * @return array Class names
     */
    public static function get_registered_classes(): array {
        return array_keys(self::$class_map);
    }

    /**
     * Check if a class is registered
     *
     * @param string $class_name Class name
     * @return bool True if registered
     */
    public static function is_registered(string $class_name): bool {
        return isset(self::$class_map[$class_name]);
    }

    /**
     * Get loaded classes count (for debugging)
     *
     * @return int Number of loaded classes
     */
    public static function get_loaded_count(): int {
        return count(self::$loaded_classes);
    }
}

// Initialize autoloader
X0PA_Autoloader::init();
