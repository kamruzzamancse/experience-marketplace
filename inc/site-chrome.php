<?php
/**
 * Tourbi custom header, footer, navigation, and final responsive shell.
 *
 * The protected Elementor homepage keeps its existing Elementor header and
 * footer. This module replaces site chrome only on Tourbi functional pages.
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Return whether the current visitor is an approved Host.
 *
 * @return bool
 */
function tourbi_theme_site_chrome_is_host() {
    if (
        function_exists(
            'tourbi_theme_current_user_is_host'
        )
    ) {
        return tourbi_theme_current_user_is_host();
    }

    if ( ! is_user_logged_in() ) {
        return false;
    }

    $user = wp_get_current_user();
    $roles = $user instanceof WP_User
        ? (array) $user->roles
        : array();

    return in_array(
        'wcfm_vendor',
        $roles,
        true
    ) || in_array(
        'vendor',
        $roles,
        true
    );
}

/**
 * Return the account page URL.
 *
 * @return string
 */
function tourbi_theme_get_site_account_url() {
    if ( function_exists( 'wc_get_page_permalink' ) ) {
        $url = wc_get_page_permalink( 'myaccount' );

        if ( $url ) {
            return $url;
        }
    }

    return home_url( '/my-account/' );
}

/**
 * Return a normal Rental URL for the main Rent a Bike CTA.
 *
 * A mapped Experience is excluded. The first published Rent Item with Tourbi
 * inventory enabled becomes the fallback rental destination.
 *
 * @return string
 */
function tourbi_theme_get_rent_now_url() {
    $configured = trim(
        (string) get_theme_mod(
            'tourbi_rent_now_url',
            ''
        )
    );

    /*
     * Reject development/demo destinations that should never be used as the
     * main customer-facing Rent a Bike link.
     */
    $blocked_fragments = array(
        'classic-template',
        'demo',
        'test-',
        '/test/',
        'single-day-multi-hour',
        'template',
    );

    $configured_is_safe = '' !== $configured;

    foreach ( $blocked_fragments as $fragment ) {
        if (
            false !== stripos(
                $configured,
                $fragment
            )
        ) {
            $configured_is_safe = false;
            break;
        }
    }

    if ( $configured_is_safe ) {
        return esc_url_raw( $configured );
    }

    /*
     * Prefer a dedicated customer-facing Bike Rentals page when one exists.
     */
    foreach (
        array(
            'bike-rentals',
            'rent-a-bike',
            'rentals',
        ) as $slug
    ) {
        $page = get_page_by_path(
            $slug,
            OBJECT,
            'page'
        );

        if (
            $page instanceof WP_Post &&
            'publish' === get_post_status(
                $page
            )
        ) {
            return get_permalink( $page );
        }
    }

    /*
     * Otherwise use the Rent Item archive instead of automatically linking to
     * a single demo/template product.
     */
    $archive_url = get_post_type_archive_link(
        'rbfw_item'
    );

    if ( $archive_url ) {
        return $archive_url;
    }

    return home_url( '/rent/' );
}


/**
 * Sanitize a Customizer checkbox value.
 *
 * @param mixed $value Submitted value.
 * @return bool
 */
function tourbi_theme_sanitize_checkbox( $value ) {
    return (bool) $value;
}

/**
 * Register Tourbi header/navigation controls.
 *
 * @param WP_Customize_Manager $customizer Customizer instance.
 * @return void
 */
function tourbi_theme_register_navigation_customizer(
    $customizer
) {
    $customizer->add_section(
        'tourbi_header_navigation',
        array(
            'title' => __(
                'Tourbi Header & Navigation',
                'torby'
            ),
            'priority' => 35,
            'description' => __(
                'Control the customer-facing Rent a Bike destination. The custom Tourbi header and footer are used consistently across the homepage and functional pages.',
                'torby'
            ),
        )
    );

    $customizer->add_setting(
        'tourbi_rent_now_url',
        array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport' => 'refresh',
        )
    );

    $customizer->add_control(
        'tourbi_rent_now_url',
        array(
            'section' => 'tourbi_header_navigation',
            'type' => 'url',
            'label' => __(
                'Rent a Bike URL',
                'torby'
            ),
            'description' => __(
                'Use a customer-facing Bike Rentals page or a clean published Rental item. Leave this empty to use the Rent archive automatically. Demo, test, and template URLs are ignored.',
                'torby'
            ),
        )
    );

}
add_action(
    'customize_register',
    'tourbi_theme_register_navigation_customizer'
);

/**
 * Return whether the current request is a public WCFM vendor storefront.
 *
 * The WCFM helper is preferred because the store base slug is configurable
 * and may not always remain `/store/`.
 *
 * @return bool
 */
function tourbi_theme_is_wcfm_storefront() {
    if ( is_admin() ) {
        return false;
    }

    return function_exists( 'wcfm_is_store_page' ) &&
        (bool) wcfm_is_store_page();
}

/**
 * Return the normal WordPress page slugs that should use Tourbi chrome.
 *
 * These pages may be designed with Elementor, but they still need the same
 * Tourbi header and footer used across the marketplace. The list is filterable
 * so another landing page can be added later without editing this module.
 *
 * @return string[]
 */
function tourbi_theme_get_custom_chrome_page_slugs() {
    $slugs = array(
        'home',
        'home-2',
        'contact',
        'contact-us',
        'calculate-earnings',
        'vendor-register',
        'store-manager',
    );

    /**
     * Filter page slugs that use the Tourbi header and footer.
     *
     * @param string[] $slugs Page slugs.
     */
    $slugs = (array) apply_filters(
        'tourbi_theme_custom_chrome_page_slugs',
        $slugs
    );

    return array_values(
        array_filter(
            array_map(
                'sanitize_title',
                $slugs
            )
        )
    );
}

/**
 * Return whether the current request is an Elementor/WordPress landing page
 * that should use the shared Tourbi header and footer.
 *
 * @return bool
 */
function tourbi_theme_is_custom_chrome_page() {
    if ( is_admin() || ! is_page() ) {
        return false;
    }

    if (
        is_page_template( 'templates/page-tourbi-reference-home.php' )
    ) {
        return true;
    }

    return is_page(
        tourbi_theme_get_custom_chrome_page_slugs()
    );
}

/**
 * Return whether custom Tourbi site chrome should replace the native chrome.
 *
 * @return bool
 */
function tourbi_theme_use_custom_site_chrome() {
    if ( is_admin() ) {
        return false;
    }

    /*
     * The Tourbi header and footer are mandatory on the static homepage.
     * Elementor/Hello native chrome is hidden by the scoped stylesheet.
     */
    if (
        is_front_page() ||
        (
            function_exists(
                'tourbi_theme_is_protected_elementor_homepage'
            ) &&
            tourbi_theme_is_protected_elementor_homepage()
        )
    ) {
        return true;
    }

    if (
        function_exists(
            'tourbi_theme_is_custom_surface'
        ) &&
        tourbi_theme_is_custom_surface()
    ) {
        return true;
    }

    if ( tourbi_theme_is_wcfm_storefront() ) {
        return true;
    }

    if (
        function_exists( 'is_account_page' ) &&
        is_account_page()
    ) {
        return true;
    }

    if (
        function_exists( 'is_cart' ) &&
        is_cart()
    ) {
        return true;
    }

    if (
        function_exists( 'is_checkout' ) &&
        is_checkout()
    ) {
        return true;
    }

    return tourbi_theme_is_custom_chrome_page();
}

/**
 * Add final site-shell body classes.
 *
 * @param string[] $classes Existing classes.
 * @return string[]
 */
function tourbi_theme_site_chrome_body_classes(
    $classes
) {
    if ( tourbi_theme_use_custom_site_chrome() ) {
        $classes[] = 'tourbi-custom-site-chrome';
    }

    if ( is_page( 'store-manager' ) ) {
        $classes[] = 'tourbi-wcfm-surface';
    }

    if ( tourbi_theme_is_custom_chrome_page() ) {
        $classes[] = 'tourbi-custom-chrome-page';
    }

    if ( is_page( 'home-2' ) ) {
        $classes[] = 'tourbi-home-2-page';
    }

    if ( is_page( 'calculate-earnings' ) ) {
        $classes[] = 'tourbi-earnings-page';
    }

    if ( is_page( array( 'contact', 'contact-us' ) ) ) {
        $classes[] = 'tourbi-contact-page';
    }

    if ( tourbi_theme_is_wcfm_storefront() ) {
        $classes[] = 'tourbi-wcfm-store-page';
    }

    return array_values(
        array_unique( $classes )
    );
}
add_filter(
    'body_class',
    'tourbi_theme_site_chrome_body_classes',
    40
);

/**
 * Return the required Tourbi logo URL.
 *
 * The URL follows the current WordPress uploads base URL, so it works after
 * migration while keeping the requested /2026/06/tourbi-logo.png path.
 *
 * @return string
 */
function tourbi_theme_get_required_logo_url() {
    $uploads = wp_get_upload_dir();

    if ( ! empty( $uploads['baseurl'] ) ) {
        return trailingslashit(
            $uploads['baseurl']
        ) . '2026/06/tourbi-logo.png';
    }

    return content_url(
        '/uploads/2026/06/tourbi-logo.png'
    );
}

/**
 * Return the current-site logo markup.
 *
 * @param string $class_name Optional image class.
 * @return string
 */
function tourbi_theme_get_site_logo_markup(
    $class_name = ''
) {
    $class_name = sanitize_html_class(
        $class_name
    );

    return sprintf(
        '<img src="%1$s" class="%2$s" alt="%3$s" loading="eager" decoding="async">',
        esc_url(
            tourbi_theme_get_required_logo_url()
        ),
        esc_attr(
            trim(
                'tourbi-site-logo__image ' .
                $class_name
            )
        ),
        esc_attr__(
            'Tourbi',
            'torby'
        )
    );
}

/**
 * Return the navigation items for the current visitor.
 *
 * @return array<int,array<string,mixed>>
 */
function tourbi_theme_get_primary_navigation_items() {
    $experience_active = (
        function_exists(
            'tourbi_theme_is_marketplace_request'
        ) &&
        tourbi_theme_is_marketplace_request()
    ) || is_singular( 'rbfw_item' );

    $items = array(
        array(
            'label' => __( 'Home', 'torby' ),
            'url' => home_url( '/' ),
            'active' => is_front_page(),
        ),
        array(
            'label' => __( 'Experiences', 'torby' ),
            'url' => tourbi_theme_get_experience_archive_url(),
            'active' => $experience_active,
        ),
    );

    if ( tourbi_theme_site_chrome_is_host() ) {
        $items[] = array(
            'label' => __( 'Host Dashboard', 'torby' ),
            'url' => function_exists(
                'tourbi_theme_get_host_dashboard_url'
            )
                ? tourbi_theme_get_host_dashboard_url()
                : home_url( '/store-manager/' ),
            'active' => is_page( 'store-manager' ),
        );
    }

    $items[] = array(
        'label' => __( 'My Account', 'torby' ),
        'url' => tourbi_theme_get_site_account_url(),
        'active' => function_exists( 'is_account_page' ) &&
            is_account_page(),
    );

    if ( ! tourbi_theme_site_chrome_is_host() ) {
        $items[] = array(
            'label' => __( 'Become a Host', 'torby' ),
            'url' => tourbi_theme_get_become_host_url(),
            'active' => function_exists(
                'tourbi_theme_is_become_host_request'
            ) && tourbi_theme_is_become_host_request(),
        );
    }

    /**
     * Filter Tourbi primary navigation.
     *
     * @param array<int,array<string,mixed>> $items Navigation items.
     */
    return (array) apply_filters(
        'tourbi_theme_primary_navigation_items',
        $items
    );
}

/**
 * Render the custom Tourbi header.
 *
 * @return void
 */
function tourbi_theme_render_custom_header() {
    if ( ! tourbi_theme_use_custom_site_chrome() ) {
        return;
    }

    get_template_part(
        'template-parts/site/header',
        null,
        array(
            'navigation' =>
                tourbi_theme_get_primary_navigation_items(),
            'home_url' => home_url( '/' ),
            'rent_url' =>
                tourbi_theme_get_rent_now_url(),
            'account_url' =>
                tourbi_theme_get_site_account_url(),
            'is_host' =>
                tourbi_theme_site_chrome_is_host(),
        )
    );
}
add_action(
    'wp_body_open',
    'tourbi_theme_render_custom_header',
    5
);

/**
 * Render the custom Tourbi footer.
 *
 * @return void
 */
function tourbi_theme_render_custom_footer() {
    if ( ! tourbi_theme_use_custom_site_chrome() ) {
        return;
    }

    get_template_part(
        'template-parts/site/footer',
        null,
        array(
            'rent_url' =>
                tourbi_theme_get_rent_now_url(),
            'account_url' =>
                tourbi_theme_get_site_account_url(),
            'is_host' =>
                tourbi_theme_site_chrome_is_host(),
        )
    );
}
add_action(
    'wp_footer',
    'tourbi_theme_render_custom_footer',
    5
);

/**
 * Enqueue custom chrome and final responsive assets.
 *
 * @return void
 */
function tourbi_theme_enqueue_site_chrome_assets() {
    if ( ! tourbi_theme_use_custom_site_chrome() ) {
        return;
    }

    $styles = array(
        'tourbi-site-chrome' =>
            'assets/css/site-chrome.css',
        'tourbi-final-responsive' =>
            'assets/css/final-responsive.css',
        'tourbi-reference-alignment' =>
            'assets/css/reference-alignment.css',
    );

    foreach ( $styles as $handle => $path ) {
        $absolute = trailingslashit(
            get_stylesheet_directory()
        ) . $path;

        if ( ! file_exists( $absolute ) ) {
            continue;
        }

        wp_enqueue_style(
            $handle,
            trailingslashit(
                get_stylesheet_directory_uri()
            ) . $path,
            wp_style_is(
                'torby-theme',
                'registered'
            )
                ? array( 'torby-theme' )
                : array(),
            tourbi_theme_foundation_asset_version(
                $path
            )
        );
    }

    $script =
        'assets/js/site-chrome.js';

    if (
        file_exists(
            trailingslashit(
                get_stylesheet_directory()
            ) . $script
        )
    ) {
        wp_enqueue_script(
            'tourbi-site-chrome',
            trailingslashit(
                get_stylesheet_directory_uri()
            ) . $script,
            array(),
            tourbi_theme_foundation_asset_version(
                $script
            ),
            true
        );
    }
}
add_action(
    'wp_enqueue_scripts',
    'tourbi_theme_enqueue_site_chrome_assets',
    95
);
