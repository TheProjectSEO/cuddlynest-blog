<?php
/**
 * Optimization Configuration
 *
 * Environment-aware configuration for HTML/CSS/JS optimization system.
 * Automatically detects environment and applies appropriate settings.
 *
 * @package x0pa-hiring-templates
 */

class OptimizationConfig {

    /**
     * Configuration settings per environment
     *
     * @var array
     */
    private static $config = array(
        'development' => array(
            'enabled' => true,                // Master switch ON
            'html_minification' => true,      // Working ✓
            'css_minification' => true,       // Working ✓
            'js_minification' => true,        // Working ✓
            'js_defer' => true,               // Re-enabled with hiring-script exclusion
            'gzip' => true,                   // Working ✓
            'caching' => false,               // OFF
            'cache_duration' => 0,
        ),
        'staging' => array(
            'enabled' => true,
            'html_minification' => true,
            'css_minification' => true,
            'js_minification' => true,
            'js_defer' => true,
            'gzip' => true,
            'caching' => false,               // Test without cache first
            'cache_duration' => 1800,         // 30 minutes
        ),
        'production' => array(
            'enabled' => true,
            'html_minification' => true,
            'css_minification' => true,
            'js_minification' => true,
            'js_defer' => true,
            'gzip' => true,
            'caching' => true,
            'cache_duration' => 3600,         // 1 hour
        ),
    );

    /**
     * Get configuration for specified or auto-detected environment
     *
     * @param string|null $environment Override environment detection
     * @return array Configuration settings
     */
    public static function get($environment = null) {
        if ($environment === null) {
            $environment = self::detect_environment();
        }

        return isset(self::$config[$environment])
            ? self::$config[$environment]
            : self::$config['production'];
    }

    /**
     * Detect current environment based on constants and server variables
     *
     * @return string Environment name (development, staging, or production)
     */
    private static function detect_environment() {
        // Check WordPress debug constant
        if (defined('WP_DEBUG') && WP_DEBUG) {
            return 'development';
        }

        // Check WordPress environment constant
        if (defined('WP_ENV')) {
            $wp_env = strtolower(WP_ENV);
            if (in_array($wp_env, array('development', 'staging', 'production'))) {
                return $wp_env;
            }
        }

        // Check custom environment constant
        if (defined('APP_ENV')) {
            $app_env = strtolower(APP_ENV);
            if (in_array($app_env, array('development', 'staging', 'production'))) {
                return $app_env;
            }
        }

        // Check server name patterns
        $server_name = isset($_SERVER['SERVER_NAME']) ? strtolower($_SERVER['SERVER_NAME']) : '';

        if (strpos($server_name, 'localhost') !== false ||
            strpos($server_name, '127.0.0.1') !== false ||
            strpos($server_name, '.local') !== false ||
            strpos($server_name, '.dev') !== false) {
            return 'development';
        }

        if (strpos($server_name, 'staging') !== false ||
            strpos($server_name, 'stage') !== false ||
            strpos($server_name, 'test') !== false) {
            return 'staging';
        }

        // Default to production for safety
        return 'production';
    }

    /**
     * Check if optimization is enabled
     *
     * @param string|null $environment Optional environment override
     * @return bool Whether optimization is enabled
     */
    public static function is_enabled($environment = null) {
        $config = self::get($environment);
        return !empty($config['enabled']);
    }

    /**
     * Get specific setting value
     *
     * @param string $key Setting key
     * @param string|null $environment Optional environment override
     * @return mixed Setting value or null if not found
     */
    public static function get_setting($key, $environment = null) {
        $config = self::get($environment);
        return isset($config[$key]) ? $config[$key] : null;
    }

    /**
     * Get cache directory path
     *
     * @return string Absolute path to cache directory
     */
    public static function get_cache_dir() {
        return dirname(__DIR__) . '/cache';
    }
}
?>
