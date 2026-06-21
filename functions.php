<?php
/**
 * Torby child theme functions and definitions.
 *
 * Keep this file limited to presentation-layer concerns. Booking logic,
 * shared inventory, vendor integration, and order handling must live in the
 * separate Torby Core plugin so those features remain active if the theme is
 * changed.
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Define reusable child-theme constants.
 */
$torby_theme = wp_get_theme();

if ( ! defined( 'TORBY_CHILD_THEME_VERSION' ) ) {
    define(
        'TORBY_CHILD_THEME_VERSION',
        $torby_theme->get( 'Version' ) ? $torby_theme->get( 'Version' ) : '1.0.0'
    );
}

if ( ! defined( 'TORBY_CHILD_THEME_DIR' ) ) {
    define( 'TORBY_CHILD_THEME_DIR', get_stylesheet_directory() );
}

if ( ! defined( 'TORBY_CHILD_THEME_URI' ) ) {
    define( 'TORBY_CHILD_THEME_URI', get_stylesheet_directory_uri() );
}

/**
 * Return a cache-busting version for a theme asset.
 *
 * During development, the file modification time is used. If the file does
 * not exist, the child-theme version is returned.
 *
 * @param string $relative_path Relative path inside the child theme.
 * @return string
 */
function torby_child_asset_version( $relative_path ) {
    $relative_path = ltrim( (string) $relative_path, '/' );
    $absolute_path = trailingslashit( TORBY_CHILD_THEME_DIR ) . $relative_path;

    if ( file_exists( $absolute_path ) ) {
        return (string) filemtime( $absolute_path );
    }

    return TORBY_CHILD_THEME_VERSION;
}

/**
 * Configure child-theme features.
 *
 * Hello Elementor already registers its core theme, menu, Elementor, and
 * WooCommerce features. These additions are specific to the Torby project.
 *
 * @return void
 */
function torby_child_theme_setup() {
    load_child_theme_textdomain(
        'torby',
        trailingslashit( TORBY_CHILD_THEME_DIR ) . 'languages'
    );

    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );

    add_image_size( 'torby-experience-card', 720, 480, true );
    add_image_size( 'torby-experience-hero', 1600, 900, true );
    add_image_size( 'torby-host-avatar', 320, 320, true );
}
add_action( 'after_setup_theme', 'torby_child_theme_setup', 20 );

/**
 * Enqueue the child-theme stylesheet and optional project assets.
 *
 * Hello Elementor loads its own parent styles, so this child theme does not
 * enqueue the parent stylesheet again. This avoids duplicate CSS requests.
 *
 * @return void
 */
function torby_child_enqueue_assets() {
    $child_style_path = '/style.css';

    wp_enqueue_style(
        'torby-child-style',
        TORBY_CHILD_THEME_URI . $child_style_path,
        array(),
        torby_child_asset_version( $child_style_path )
    );

    $project_css_path = '/assets/css/torby-theme.css';

    if ( file_exists( TORBY_CHILD_THEME_DIR . $project_css_path ) ) {
        wp_enqueue_style(
            'torby-theme',
            TORBY_CHILD_THEME_URI . $project_css_path,
            array( 'torby-child-style' ),
            torby_child_asset_version( $project_css_path )
        );
    }

    $project_js_path = '/assets/js/torby-theme.js';

    if ( file_exists( TORBY_CHILD_THEME_DIR . $project_js_path ) ) {
        wp_enqueue_script(
            'torby-theme',
            TORBY_CHILD_THEME_URI . $project_js_path,
            array(),
            torby_child_asset_version( $project_js_path ),
            true
        );
    }
}
add_action( 'wp_enqueue_scripts', 'torby_child_enqueue_assets', 20 );

/**
 * Add useful presentation classes to the body element.
 *
 * These classes are intended only for styling. User permissions and access
 * control must be handled by WordPress, WooCommerce, WCFM, and Torby Core.
 *
 * @param string[] $classes Existing body classes.
 * @return string[]
 */
function torby_child_body_classes( $classes ) {
    if ( is_user_logged_in() ) {
        $classes[] = 'torby-user-logged-in';

        $user  = wp_get_current_user();
        $roles = $user instanceof WP_User ? (array) $user->roles : array();

        if (
            in_array( 'wcfm_vendor', $roles, true ) ||
            in_array( 'vendor', $roles, true )
        ) {
            $classes[] = 'torby-user-host';
        }

        if ( in_array( 'customer', $roles, true ) ) {
            $classes[] = 'torby-user-rider';
        }

        if ( current_user_can( 'manage_options' ) ) {
            $classes[] = 'torby-user-admin';
        }
    } else {
        $classes[] = 'torby-user-guest';
    }

    if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
        $classes[] = 'torby-woocommerce-page';
    }

    return array_values( array_unique( $classes ) );
}
add_filter( 'body_class', 'torby_child_body_classes' );

/**
 * Add custom image sizes to the WordPress media selector.
 *
 * @param array<string,string> $sizes Existing image sizes.
 * @return array<string,string>
 */
function torby_child_image_size_names( $sizes ) {
    $sizes['torby-experience-card'] = esc_html__(
        'Torby Experience Card',
        'torby'
    );

    $sizes['torby-experience-hero'] = esc_html__(
        'Torby Experience Hero',
        'torby'
    );

    $sizes['torby-host-avatar'] = esc_html__(
        'Torby Host Avatar',
        'torby'
    );

    return $sizes;
}
add_filter( 'image_size_names_choose', 'torby_child_image_size_names' );

/**
 * Remove the default Hello Elementor page title only when the current page
 * was built with Elementor. WooCommerce and other non-Elementor pages keep
 * their normal headings.
 *
 * @param bool $display_title Whether the title should be displayed.
 * @return bool
 */
function torby_child_maybe_hide_elementor_page_title( $display_title ) {
    if ( ! is_singular() || ! did_action( 'elementor/loaded' ) ) {
        return $display_title;
    }

    $post_id = get_queried_object_id();

    if ( ! $post_id || ! class_exists( '\Elementor\Plugin' ) ) {
        return $display_title;
    }

    $elementor = \Elementor\Plugin::$instance;

    if (
        isset( $elementor->db ) &&
        method_exists( $elementor->db, 'is_built_with_elementor' ) &&
        $elementor->db->is_built_with_elementor( $post_id )
    ) {
        return false;
    }

    return $display_title;
}
add_filter(
    'hello_elementor_page_title',
    'torby_child_maybe_hide_elementor_page_title'
);
