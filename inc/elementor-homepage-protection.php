<?php
/**
 * Elementor homepage protection helpers.
 *
 * No front-page.php, page-home.php, header.php, or footer.php is introduced
 * by Step 68A. The current Elementor homepage therefore continues to render
 * through the existing Hello Elementor page flow.
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Determine whether a post is built with Elementor.
 *
 * @param int $post_id Optional post ID.
 * @return bool
 */
function tourbi_theme_is_built_with_elementor( $post_id = 0 ) {
    $post_id = $post_id
        ? absint( $post_id )
        : get_queried_object_id();

    if (
        ! $post_id ||
        ! did_action( 'elementor/loaded' ) ||
        ! class_exists( '\Elementor\Plugin' )
    ) {
        return false;
    }

    $elementor = \Elementor\Plugin::$instance;

    if (
        ! isset( $elementor->db ) ||
        ! method_exists(
            $elementor->db,
            'is_built_with_elementor'
        )
    ) {
        return false;
    }

    return (bool) $elementor->db->is_built_with_elementor(
        $post_id
    );
}

/**
 * Determine whether the current request is the protected Elementor homepage.
 *
 * @return bool
 */
function tourbi_theme_is_protected_elementor_homepage() {
    if ( is_admin() || ! is_front_page() ) {
        return false;
    }

    return tourbi_theme_is_built_with_elementor(
        get_queried_object_id()
    );
}

/**
 * Add explicit body classes for protected and custom rendering surfaces.
 *
 * @param string[] $classes Existing body classes.
 * @return string[]
 */
function tourbi_theme_elementor_protection_body_classes( $classes ) {
    if ( tourbi_theme_is_protected_elementor_homepage() ) {
        $classes[] = 'tourbi-elementor-homepage';
        $classes[] = 'tourbi-homepage-protected';
    }

    if (
        function_exists(
            'tourbi_theme_is_custom_surface'
        ) &&
        tourbi_theme_is_custom_surface()
    ) {
        $classes[] = 'tourbi-custom-surface';
    }

    return array_values(
        array_unique( $classes )
    );
}
add_filter(
    'body_class',
    'tourbi_theme_elementor_protection_body_classes',
    60
);

/**
 * Expose a small debug comment to administrators only.
 *
 * This confirms protection without changing the page markup or layout.
 *
 * @return void
 */
function tourbi_theme_render_homepage_protection_marker() {
    if (
        ! tourbi_theme_is_protected_elementor_homepage() ||
        ! current_user_can( 'manage_options' )
    ) {
        return;
    }

    echo "\n<!-- Tourbi: Elementor homepage protected; custom foundation assets skipped. -->\n";
}
add_action(
    'wp_footer',
    'tourbi_theme_render_homepage_protection_marker',
    999
);
