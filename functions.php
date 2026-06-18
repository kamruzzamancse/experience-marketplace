<?php
/**
 * Theme functions and definitions
 *
 * @package Experience_Marketplace
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue parent and child theme stylesheets.
 */
add_action( 'wp_enqueue_scripts', 'experience_marketplace_enqueue_styles' );
function experience_marketplace_enqueue_styles() {
    // Enqueue parent theme style
    wp_enqueue_style(
        'parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        '1.0.0'
    );

    // Enqueue child theme style (depends on parent)
    wp_enqueue_style(
        'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( 'parent-style' ),
        '1.0.0'
    );
}

/**
 * Load modular files
 */
define( 'EXPERIENCE_MARKETPLACE_INC', get_stylesheet_directory() . '/inc/' );

// Load modular files
require_once EXPERIENCE_MARKETPLACE_INC . 'custom-roles.php';
require_once EXPERIENCE_MARKETPLACE_INC . 'inventory-manager.php';
require_once EXPERIENCE_MARKETPLACE_INC . 'helpers.php';

/**
 * Initialize custom functions after theme setup
 */
add_action( 'after_setup_theme', 'experience_marketplace_init' );
function experience_marketplace_init() {
    // Call the role setup function
    if ( function_exists( 'experience_marketplace_setup_roles' ) ) {
        experience_marketplace_setup_roles();
    }
}

/**
 * Your custom code goes below this line.
 */