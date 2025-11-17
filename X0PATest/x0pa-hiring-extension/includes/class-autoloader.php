<?php
/**
 * Class Autoloader
 *
 * @package X0PA_Hiring_Extension
 * @since 1.0.0
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
     * Class map for quick lookup
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

        // Content Generators
        'X0PA_SEO_Meta' => 'generators/class-seo-meta.php',
        'X0PA_TOC_Generator' => 'generators/class-toc-generator.php',
        'X0PA_Hero_Generator' => 'generators/class-hero-generator.php',
        'X0PA_Interview_Overview' => 'generators/class-interview-overview.php',
    );

    /**
     * Base directory for includes
     *
     * @var string
     */
    private static $base_dir;

    /**
     * Initialize autoloader
     */
    public static function init() {
        self::$base_dir = dirname(__FILE__) . '/';

        // Register autoloader
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    /**
     * Autoload class files
     *
     * @param string $class_name Class name to load
     */
    public static function autoload($class_name) {
        // Only autoload our classes
        if (strpos($class_name, 'X0PA_') !== 0) {
            return;
        }

        // Check class map
        if (isset(self::$class_map[$class_name])) {
            $file_path = self::$base_dir . self::$class_map[$class_name];

            if (file_exists($file_path)) {
                require_once $file_path;
                return;
            }
        }

        // Fallback: Try to load based on naming convention
        $file_name = 'class-' . strtolower(str_replace('_', '-', $class_name)) . '.php';

        // Search in subdirectories
        $search_dirs = array('core', 'admin', 'schemas', 'generators');

        foreach ($search_dirs as $dir) {
            $file_path = self::$base_dir . $dir . '/' . $file_name;

            if (file_exists($file_path)) {
                require_once $file_path;
                return;
            }
        }
    }

    /**
     * Manually load a class file
     *
     * @param string $class_name Class name
     * @return bool True if loaded, false otherwise
     */
    public static function load_class($class_name) {
        self::autoload($class_name);
        return class_exists($class_name);
    }

    /**
     * Load all core classes
     */
    public static function load_all_core() {
        $core_classes = array(
            'X0PA_Internal_Linking',
            'X0PA_Hub_Page'
        );

        foreach ($core_classes as $class) {
            self::load_class($class);
        }
    }

    /**
     * Load all schema generators
     */
    public static function load_all_schemas() {
        $schema_classes = array(
            'X0PA_WebPage_Schema',
            'X0PA_Author_Schema',
            'X0PA_FAQ_Schema'
        );

        foreach ($schema_classes as $class) {
            self::load_class($class);
        }
    }

    /**
     * Load all content generators
     */
    public static function load_all_generators() {
        $generator_classes = array(
            'X0PA_SEO_Meta',
            'X0PA_TOC_Generator',
            'X0PA_Hero_Generator',
            'X0PA_Interview_Overview'
        );

        foreach ($generator_classes as $class) {
            self::load_class($class);
        }
    }

    /**
     * Get list of all registered classes
     *
     * @return array Class names
     */
    public static function get_registered_classes() {
        return array_keys(self::$class_map);
    }

    /**
     * Check if a class is registered
     *
     * @param string $class_name Class name
     * @return bool True if registered
     */
    public static function is_registered($class_name) {
        return isset(self::$class_map[$class_name]);
    }
}

// Initialize autoloader
X0PA_Autoloader::init();
