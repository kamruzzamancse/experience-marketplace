<?php
/**
 * Conditional assets for Tourbi custom PHP templates.
 *
 * Existing globally loaded account/vendor CSS remains unchanged. The new
 * foundation assets load only on Tourbi custom surfaces and never on the
 * protected Elementor homepage.
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Return the version of a foundation asset.
 *
 * @param string $relative_path Relative path inside the child theme.
 * @return string
 */
function tourbi_theme_foundation_asset_version( $relative_path ) {
    $relative_path = ltrim(
        (string) $relative_path,
        '/'
    );

    $file_path = trailingslashit(
        get_stylesheet_directory()
    ) . $relative_path;

    return file_exists( $file_path )
        ? (string) filemtime( $file_path )
        : TOURBI_THEME_FOUNDATION_VERSION;
}

/**
 * Return page slugs that belong to the custom Tourbi frontend.
 *
 * More slugs may be added later without changing the asset loader.
 *
 * @return string[]
 */
function tourbi_theme_custom_surface_page_slugs() {
    return (array) apply_filters(
        'tourbi_theme_custom_surface_page_slugs',
        array(
            'experiences',
            'become-a-host',
            'host-dashboard',
            'tourbi-foundation-preview',
        )
    );
}

/**
 * Determine whether foundation assets should load.
 *
 * @return bool
 */
function tourbi_theme_is_custom_surface() {
    if (
        function_exists(
            'tourbi_theme_is_protected_elementor_homepage'
        ) &&
        tourbi_theme_is_protected_elementor_homepage()
    ) {
        return false;
    }

    $is_surface = false;

    if (
        is_singular( 'rbfw_item' ) ||
        is_post_type_archive( 'rbfw_item' )
    ) {
        $is_surface = true;
    }

    if (
        function_exists( 'is_shop' ) &&
        is_shop()
    ) {
        $is_surface = true;
    }

    if (
        is_page_template(
            'templates/page-tourbi-foundation-preview.php'
        )
    ) {
        $is_surface = true;
    }

    if (
        is_page(
            tourbi_theme_custom_surface_page_slugs()
        )
    ) {
        $is_surface = true;
    }

    /**
     * Filter whether the current request is a Tourbi custom design surface.
     *
     * @param bool $is_surface Current result.
     */
    return (bool) apply_filters(
        'tourbi_theme_is_custom_surface',
        $is_surface
    );
}

/**
 * Enqueue namespaced foundation styles and scripts.
 *
 * @return void
 */
function tourbi_theme_enqueue_foundation_assets() {
    if ( ! tourbi_theme_is_custom_surface() ) {
        return;
    }

    $style_dependency = wp_style_is(
        'torby-theme',
        'registered'
    )
        ? array( 'torby-theme' )
        : array();

    $styles = array(
        'tourbi-design-tokens' => array(
            'path' => 'assets/css/tourbi-tokens.css',
            'deps' => $style_dependency,
        ),
        'tourbi-components' => array(
            'path' => 'assets/css/tourbi-components.css',
            'deps' => array(
                'tourbi-design-tokens',
            ),
        ),
        'tourbi-layout' => array(
            'path' => 'assets/css/tourbi-layout.css',
            'deps' => array(
                'tourbi-components',
            ),
        ),
    );

    if (
        is_page_template(
            'templates/page-tourbi-foundation-preview.php'
        )
    ) {
        $styles['tourbi-foundation-preview'] = array(
            'path' => 'assets/css/tourbi-foundation-preview.css',
            'deps' => array(
                'tourbi-layout',
            ),
        );
    }


    if (
        function_exists(
            'tourbi_theme_is_single_experience_request'
        ) &&
        tourbi_theme_is_single_experience_request()
    ) {
        $styles['tourbi-experience-single'] = array(
            'path' => 'assets/css/experience-single.css',
            'deps' => array(
                'tourbi-layout',
            ),
        );
    }


    if (
        function_exists(
            'tourbi_theme_is_single_rental_request'
        ) &&
        tourbi_theme_is_single_rental_request()
    ) {
        $styles['tourbi-rental-single'] = array(
            'path' => 'assets/css/rental-single.css',
            'deps' => array(
                'tourbi-layout',
            ),
        );
    }


    if (
        function_exists(
            'tourbi_theme_is_marketplace_request'
        ) &&
        tourbi_theme_is_marketplace_request()
    ) {
        $styles['tourbi-experience-marketplace'] = array(
            'path' => 'assets/css/experience-marketplace.css',
            'deps' => array(
                'tourbi-layout',
            ),
        );
    }


    if (
        function_exists(
            'tourbi_theme_is_shop_hub_request'
        ) &&
        tourbi_theme_is_shop_hub_request()
    ) {
        $styles['tourbi-shop-hub'] = array(
            'path' => 'assets/css/shop-hub.css',
            'deps' => array(
                'tourbi-layout',
            ),
        );
    }


    if (
        function_exists(
            'tourbi_theme_is_become_host_request'
        ) &&
        tourbi_theme_is_become_host_request()
    ) {
        $styles['tourbi-become-host'] = array(
            'path' => 'assets/css/become-host.css',
            'deps' => array(
                'tourbi-layout',
            ),
        );
    }

    foreach ( $styles as $handle => $style ) {
        $file_path = trailingslashit(
            get_stylesheet_directory()
        ) . $style['path'];

        if ( ! file_exists( $file_path ) ) {
            continue;
        }

        wp_enqueue_style(
            $handle,
            trailingslashit(
                get_stylesheet_directory_uri()
            ) . $style['path'],
            $style['deps'],
            tourbi_theme_foundation_asset_version(
                $style['path']
            )
        );
    }

    $script_path =
        'assets/js/tourbi-foundation.js';

    if (
        file_exists(
            trailingslashit(
                get_stylesheet_directory()
            ) . $script_path
        )
    ) {
        wp_enqueue_script(
            'tourbi-foundation',
            trailingslashit(
                get_stylesheet_directory_uri()
            ) . $script_path,
            array(),
            tourbi_theme_foundation_asset_version(
                $script_path
            ),
            true
        );

        wp_add_inline_script(
            'tourbi-foundation',
            'window.TourbiThemeFoundation = ' .
            wp_json_encode(
                array(
                    'version' =>
                        TOURBI_THEME_FOUNDATION_VERSION,
                    'isPreview' =>
                        is_page_template(
                            'templates/page-tourbi-foundation-preview.php'
                        ),
                )
            ) .
            ';',
            'before'
        );
    }

    if (
        function_exists(
            'tourbi_theme_is_single_experience_request'
        ) &&
        tourbi_theme_is_single_experience_request()
    ) {
        $experience_script =
            'assets/js/experience-single.js';

        if (
            file_exists(
                trailingslashit(
                    get_stylesheet_directory()
                ) . $experience_script
            )
        ) {
            wp_enqueue_script(
                'tourbi-experience-single',
                trailingslashit(
                    get_stylesheet_directory_uri()
                ) . $experience_script,
                array(),
                tourbi_theme_foundation_asset_version(
                    $experience_script
                ),
                true
            );
        }
    }


    if (
        function_exists(
            'tourbi_theme_is_single_rental_request'
        ) &&
        tourbi_theme_is_single_rental_request()
    ) {
        $rental_script =
            'assets/js/rental-single.js';

        if (
            file_exists(
                trailingslashit(
                    get_stylesheet_directory()
                ) . $rental_script
            )
        ) {
            wp_enqueue_script(
                'tourbi-rental-single',
                trailingslashit(
                    get_stylesheet_directory_uri()
                ) . $rental_script,
                array(),
                tourbi_theme_foundation_asset_version(
                    $rental_script
                ),
                true
            );
        }
    }


    if (
        function_exists(
            'tourbi_theme_is_marketplace_request'
        ) &&
        tourbi_theme_is_marketplace_request()
    ) {
        $marketplace_script =
            'assets/js/experience-marketplace.js';

        if (
            file_exists(
                trailingslashit(
                    get_stylesheet_directory()
                ) . $marketplace_script
            )
        ) {
            wp_enqueue_script(
                'tourbi-experience-marketplace',
                trailingslashit(
                    get_stylesheet_directory_uri()
                ) . $marketplace_script,
                array(),
                tourbi_theme_foundation_asset_version(
                    $marketplace_script
                ),
                true
            );
        }
    }


    if (
        function_exists(
            'tourbi_theme_is_become_host_request'
        ) &&
        tourbi_theme_is_become_host_request()
    ) {
        $host_script =
            'assets/js/become-host.js';

        if (
            file_exists(
                trailingslashit(
                    get_stylesheet_directory()
                ) . $host_script
            )
        ) {
            wp_enqueue_script(
                'tourbi-become-host',
                trailingslashit(
                    get_stylesheet_directory_uri()
                ) . $host_script,
                array(),
                tourbi_theme_foundation_asset_version(
                    $host_script
                ),
                true
            );
        }
    }
}
add_action(
    'wp_enqueue_scripts',
    'tourbi_theme_enqueue_foundation_assets',
    60
);
