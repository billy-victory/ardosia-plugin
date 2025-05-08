<?php
/**
 * Plugin Name: Ardosia Calculator Modules
 * Plugin URI:  https://victory.digital/
 * Description: Provides shortcodes for various calculators and tools, starting with a Calculator for Ardosia.
 * Author:      Victory Digital
 * Author URI:  https://victory.digital
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: pps-modules
 * Domain Path: /languages
 * Version:     1.0.5
 * Update URI:  https://git-updater.com
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PPS_MODULES_VERSION', '1.0.5' );
define( 'PPS_MODULES_PATH', plugin_dir_path( __FILE__ ) );
define( 'PPS_MODULES_URL', plugin_dir_url( __FILE__ ) );

/**
 * Require Composer autoloader
 */
require_once __DIR__ . '/vendor/autoload.php';

/**
 * Initialize Git Updater Lite
 */
( new \Fragen\Git_Updater\Lite( __FILE__ ) )->run();

/**
 * Declare GitHub repository for Git Updater Lite
 */
add_filter( 'gu_lite_repo_url', function( $url, $file ) {
	if ( plugin_basename( $file ) === plugin_basename( __FILE__ ) ) {
		return 'https://github.com/billy-victory/ardosia-plugin';
	}
	return $url;
}, 10, 2 );

/**
 * Include additional plugin files
 */
require_once PPS_MODULES_PATH . 'inc/frontend.php';
require_once PPS_MODULES_PATH . 'inc/email.php';
require_once PPS_MODULES_PATH . 'inc/admin.php'; // Include the admin functionality

/**
 * Create database tables on plugin activation
 */
function pps_modules_activate() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'pps_quotes';

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
		email varchar(100) NOT NULL,
		postcode varchar(20) DEFAULT '' NOT NULL,
		paving_type varchar(255) NOT NULL,
		size_option varchar(255) NOT NULL,
		size_detail varchar(255) NOT NULL,
		area float NOT NULL,
		price_per_sqm float NOT NULL,
		total_cost float NOT NULL,
		quote_data longtext NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}
register_activation_hook( __FILE__, 'pps_modules_activate' );

/**
 * Enqueue scripts and styles for the frontend.
 */
function pps_modules_enqueue_scripts() {
	if ( ! is_admin() ) {
		$asset_manifest = PPS_MODULES_PATH . 'dist/.vite/manifest.json';

		if ( file_exists( $asset_manifest ) ) {
			wp_enqueue_style(
				'pps-modules-base-styles',
				PPS_MODULES_URL . 'inc/base-calculator.css',
				array(),
				PPS_MODULES_VERSION
			);
			
			$manifest_content = file_get_contents( $asset_manifest );
			$manifest = json_decode( $manifest_content, true );

			$main_js_entry = null;
			foreach ( $manifest as $key => $value ) {
				if ( isset( $value['isEntry'] ) && $value['isEntry'] === true && isset( $value['file'] ) ) {
					$main_js_entry = $value;
					break;
				}
			}

			if ( $main_js_entry && isset( $main_js_entry['file'] ) ) {
				wp_enqueue_script(
					'pps-modules-main',
					PPS_MODULES_URL . 'dist/' . $main_js_entry['file'],
					array(),
					PPS_MODULES_VERSION,
					true
				);

				if ( isset( $main_js_entry['css'] ) ) {
					foreach ( $main_js_entry['css'] as $css_file ) {
						wp_enqueue_style(
							'pps-modules-styles-' . pathinfo( $css_file, PATHINFO_FILENAME ),
							PPS_MODULES_URL . 'dist/' . $css_file,
							array( 'pps-modules-base-styles' ),
							PPS_MODULES_VERSION
						);
					}
				}
			} elseif ( isset( $manifest['main.js']['file'] ) ) {
				wp_enqueue_script(
					'pps-modules-main',
					PPS_MODULES_URL . 'dist/' . $manifest['main.js']['file'],
					array(),
					PPS_MODULES_VERSION,
					true
				);

				if ( isset( $manifest['main.js']['css'] ) ) {
					foreach ( $manifest['main.js']['css'] as $css_file ) {
						wp_enqueue_style(
							'pps-modules-styles-' . pathinfo( $css_file, PATHINFO_FILENAME ),
							PPS_MODULES_URL . 'dist/' . $css_file,
							array( 'pps-modules-base-styles' ),
							PPS_MODULES_VERSION
						);
					}
				} elseif ( isset( $manifest['style.css']['file'] ) ) {
					wp_enqueue_style(
						'pps-modules-styles',
						PPS_MODULES_URL . 'dist/' . $manifest['style.css']['file'],
						array( 'pps-modules-base-styles' ),
						PPS_MODULES_VERSION
					);
				}
			}

			wp_localize_script( 'pps-modules-main', 'ppsData', array(
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'nonce'      => wp_create_nonce( 'pps_module_nonce' ),
				'plugin_url' => PPS_MODULES_URL
			) );
		} else {
			wp_enqueue_style(
				'pps-modules-base-styles',
				PPS_MODULES_URL . 'inc/base-calculator.css',
				array(),
				PPS_MODULES_VERSION
			);

			wp_enqueue_script(
				'pps-modules-main',
				PPS_MODULES_URL . 'app/dist/main.js',
				array(),
				PPS_MODULES_VERSION,
				true
			);

			wp_enqueue_style(
				'pps-modules-styles',
				PPS_MODULES_URL . 'app/dist/style.css',
				array( 'pps-modules-base-styles' ),
				PPS_MODULES_VERSION
			);

			error_log( 'PPS Modules: Vite manifest file not found at ' . $asset_manifest );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'pps_modules_enqueue_scripts' );

remove_shortcode( 'premium_paving_calculator' );

add_action( 'wp_ajax_nopriv_pps_send_quote_email', 'pps_send_quote_email' );
add_action( 'wp_ajax_pps_send_quote_email', 'pps_send_quote_email' );

// --- Add More Modules Below ---
/*
function pps_another_module_shortcode( $atts ) {
	return '<div id="another-app">Loading Another Module...</div>';
}
add_shortcode( 'another_module', 'pps_another_module_shortcode' );
*/