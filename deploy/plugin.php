<?php
/**
 * Plugin Name: Ardosi Calculator Modules
 * Plugin URI:  https://example.com/
 * Description: Provides shortcodes for various calculators and tools, starting with a Calculator for Ardosi.
 * Version:     1.0.0
 * Author:      Your Name
 * Author URI:  https://victory.digital
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: pps-modules
 * Domain Path: /languages
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PPS_MODULES_VERSION', '1.0.0' );
define( 'PPS_MODULES_PATH', plugin_dir_path( __FILE__ ) );
define( 'PPS_MODULES_URL', plugin_dir_url( __FILE__ ) );

/**
 * Include necessary files
 */
require_once PPS_MODULES_PATH . 'inc/frontend.php';
require_once PPS_MODULES_PATH . 'inc/email.php';

/**
 * Enqueue scripts and styles for the frontend.
 */
function pps_modules_enqueue_scripts() {
    // Only enqueue if a relevant shortcode might be present (optimization - basic check)
    if ( ! is_admin() ) {
        // Assuming Vite builds to a 'dist' folder
        $asset_manifest = PPS_MODULES_PATH . 'dist/.vite/manifest.json';

        if ( file_exists( $asset_manifest ) ) {
            // Add base calculator styles that ensure consistent rendering regardless of theme
            wp_enqueue_style(
                'pps-modules-base-styles',
                PPS_MODULES_URL . 'inc/base-calculator.css',
                array(),
                PPS_MODULES_VERSION
            );
            
            $manifest_content = file_get_contents( $asset_manifest );
            $manifest = json_decode( $manifest_content, true );

            // Check for the main JS entry point in the manifest
            $main_js_entry = null;
            foreach ($manifest as $key => $value) {
                if (isset($value['isEntry']) && $value['isEntry'] === true && isset($value['file'])) {
                    $main_js_entry = $value;
                    break;
                }
            }

            if ( $main_js_entry && isset($main_js_entry['file']) ) {
                wp_enqueue_script(
                    'pps-modules-main',
                    PPS_MODULES_URL . 'dist/' . $main_js_entry['file'],
                    array(), 
                    PPS_MODULES_VERSION,
                    true // Load in footer
                );

                // Look for CSS files associated with the entry point
                if (isset($main_js_entry['css'])) {
                    foreach ($main_js_entry['css'] as $css_file) {
                         wp_enqueue_style(
                            'pps-modules-styles-' . pathinfo($css_file, PATHINFO_FILENAME),
                            PPS_MODULES_URL . 'dist/' . $css_file,
                            array('pps-modules-base-styles'), // Make sure our base styles load first
                            PPS_MODULES_VERSION
                        );
                    }
                }
            } elseif (isset($manifest['main.js']['file'])) { // Fallback for older/different manifest structures
                 wp_enqueue_script(
                    'pps-modules-main',
                    PPS_MODULES_URL . 'dist/' . $manifest['main.js']['file'],
                    array(),
                    PPS_MODULES_VERSION,
                    true
                );
                 if (isset($manifest['main.js']['css'])) {
                    foreach ($manifest['main.js']['css'] as $css_file) {
                         wp_enqueue_style(
                            'pps-modules-styles-' . pathinfo($css_file, PATHINFO_FILENAME),
                            PPS_MODULES_URL . 'dist/' . $css_file,
                            array('pps-modules-base-styles'),
                            PPS_MODULES_VERSION
                        );
                    }
                } elseif (isset($manifest['style.css']['file'])) { // Further fallback
                     wp_enqueue_style(
                        'pps-modules-styles',
                        PPS_MODULES_URL . 'dist/' . $manifest['style.css']['file'],
                        array('pps-modules-base-styles'),
                        PPS_MODULES_VERSION
                    );
                }
            }

            // Pass any necessary data to JavaScript
            wp_localize_script('pps-modules-main', 'ppsData', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('pps_module_nonce'),
                'plugin_url' => PPS_MODULES_URL
            ));
            
        } else {
            // Fallback or error handling if manifest not found
            wp_enqueue_style(
                'pps-modules-base-styles',
                PPS_MODULES_URL . 'inc/base-calculator.css',
                array(),
                PPS_MODULES_VERSION
            );
            
            wp_enqueue_script(
                'pps-modules-main',
                PPS_MODULES_URL . 'app/dist/main.js', // Adjust path if needed
                array(),
                PPS_MODULES_VERSION,
                true
            );
            
            wp_enqueue_style(
                'pps-modules-styles',
                PPS_MODULES_URL . 'app/dist/style.css', // Adjust path if needed
                array('pps-modules-base-styles'),
                PPS_MODULES_VERSION
            );
            
            error_log('PPS Modules: Vite manifest file not found at ' . $asset_manifest);
        }
    }
}
add_action( 'wp_enqueue_scripts', 'pps_modules_enqueue_scripts' );

/**
 * Remove the default shortcode handler since we're using the one in frontend.php
 */
remove_shortcode( 'premium_paving_calculator' );

/**
 * AJAX handler to send quote email (implementation in email.php)
 */
add_action('wp_ajax_nopriv_pps_send_quote_email', 'pps_send_quote_email');
add_action('wp_ajax_pps_send_quote_email', 'pps_send_quote_email');

// --- Add More Modules Below ---
/*
function pps_another_module_shortcode( $atts ) {
    // Enqueue specific scripts/styles for this module if needed, or rely on global enqueue
    return '<div id="another-app">Loading Another Module...</div>';
}
add_shortcode( 'another_module', 'pps_another_module_shortcode' );
*/
?>
