<?php
/**
 * Frontend functionality for Premium Paving Solutions calculators
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register shortcodes
 */
function pps_register_shortcodes() {
    add_shortcode('premium_paving_calculator', 'pps_render_paving_calculator');
}
add_action('init', 'pps_register_shortcodes');

/**
 * Render the paving calculator
 * 
 * @return string HTML output for the calculator
 */
function pps_render_paving_calculator() {
    // Start output buffering
    ob_start();
    
   
    
    // Add custom CSS to fix styling issues and hide initial "Loading" text
    ?>
    <style>
        /* Add style for the new title */
        .pps-calculator-main-title {
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1.8rem; /* Adjust size as needed */
            font-weight: 600;
            color: #333; /* Adjust color to match theme if needed */
        }
        
        /* Additional inline styles specific to this instance */
        .pps-calculator-container .pps-calculator-loading {
            padding: 20px;
            text-align: center;
            color: #666;
            background-color: #f9fafb;
            border-radius: 0.5rem;
        }
        
        /* Hide WordPress content that might appear above the calculator */
        .wp-block-shortcode {
            display: none;
        }
    </style>
    <?php
    
    // Output the container div with ID "app" for Svelte to mount to, plus a loading message
    // The loading message will be hidden by CSS when the app loads
    echo '<div class="pps-calculator-container">';
    echo '<div id="app" class="pps-calculator"></div>';
    echo '</div>';
    
    // Return the buffered content
    return ob_get_clean();
}