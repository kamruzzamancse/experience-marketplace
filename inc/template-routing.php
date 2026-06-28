<?php
/**
 * Safe template routing foundation.
 *
 * Step 68A does not route the homepage or replace any existing frontend page.
 * Future steps may register archive, single, and host-page templates through
 * the map below. The protected Elementor homepage is always excluded.
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Locate a Tourbi template inside the active child theme.
 *
 * @param string $relative_path Relative template path.
 * @return string
 */
function tourbi_theme_locate_template( $relative_path ) {
    $relative_path = ltrim(
        (string) $relative_path,
        '/'
    );

    $template = locate_template(
        array( $relative_path ),
        false,
        false
    );

    return $template
        ? wp_normalize_path( $template )
        : '';
}

/**
 * Return future custom template routes.
 *
 * Each route contains a callable condition and a child-theme file.
 * Step 68A intentionally ships with no automatic routes.
 *
 * @return array<int,array<string,mixed>>
 */
function tourbi_theme_get_template_routes() {
    return (array) apply_filters(
        'tourbi_theme_template_routes',
        array()
    );
}

/**
 * Apply registered custom routes without touching the Elementor homepage.
 *
 * @param string $template Current WordPress template.
 * @return string
 */
function tourbi_theme_apply_template_routes( $template ) {
    if (
        function_exists(
            'tourbi_theme_is_protected_elementor_homepage'
        ) &&
        tourbi_theme_is_protected_elementor_homepage()
    ) {
        return $template;
    }

    foreach (
        tourbi_theme_get_template_routes()
        as $route
    ) {
        $condition = $route['condition'] ?? null;
        $file      = $route['file'] ?? '';

        if (
            ! is_callable( $condition ) ||
            ! call_user_func( $condition )
        ) {
            continue;
        }

        $custom_template =
            tourbi_theme_locate_template( $file );

        if ( $custom_template ) {
            return $custom_template;
        }
    }

    return $template;
}
add_filter(
    'template_include',
    'tourbi_theme_apply_template_routes',
    60
);

/**
 * Normalize a stored yes/no flag.
 *
 * @param mixed $value Stored value.
 * @return bool
 */
function tourbi_theme_meta_flag_is_enabled( $value ) {
    return in_array(
        strtolower( trim( (string) $value ) ),
        array(
            '1',
            'yes',
            'true',
            'on',
            'enabled',
        ),
        true
    );
}

/**
 * Determine whether one Rent Item has an enabled normal-rental mapping.
 *
 * Normal Rental mapping is authoritative because Tourbi Core does not allow
 * one item to be processed as both a normal Rental and an Experience.
 *
 * @param int $item_id Rent Item ID.
 * @return bool
 */
function tourbi_theme_item_is_rental_mapped( $item_id ) {
    $item_id = absint( $item_id );

    if ( ! $item_id ) {
        return false;
    }

    return tourbi_theme_meta_flag_is_enabled(
        get_post_meta(
            $item_id,
            '_tourbi_inventory_enabled',
            true
        )
    );
}

/**
 * Return both mapping flags for diagnostics.
 *
 * @param int $item_id Rent Item ID.
 * @return array<string,bool>
 */
function tourbi_theme_get_item_mapping_flags( $item_id ) {
    $item_id = absint( $item_id );

    return array(
        'rental' => tourbi_theme_item_is_rental_mapped(
            $item_id
        ),
        'experience' =>
            tourbi_theme_meta_flag_is_enabled(
                get_post_meta(
                    $item_id,
                    '_tourbi_experience_enabled',
                    true
                )
            ),
    );
}

/**
 * Determine whether one Rent Item is explicitly an Experience.
 *
 * Newer Tourbi records use the explicit mapping flag. Older records without
 * that meta key may fall back to the Tourbi Core mapping helper.
 *
 * @param int $item_id Rent Item ID.
 * @return bool
 */
function tourbi_theme_item_is_experience( $item_id ) {
    $item_id = absint( $item_id );

    if ( ! $item_id ) {
        return false;
    }

    /*
     * A normal Rental mapping always wins. This also repairs old records that
     * accidentally contain both mapping flags from template cloning or earlier
     * development steps.
     */
    if ( tourbi_theme_item_is_rental_mapped( $item_id ) ) {
        return false;
    }

    /*
     * The explicit Experience Mapping switch is authoritative. A saved "no"
     * must never fall back to another helper and accidentally render the
     * guided Experience template.
     */
    if (
        metadata_exists(
            'post',
            $item_id,
            '_tourbi_experience_enabled'
        )
    ) {
        return tourbi_theme_meta_flag_is_enabled(
            get_post_meta(
                $item_id,
                '_tourbi_experience_enabled',
                true
            )
        );
    }

    /*
     * Older Experience records may predate the explicit switch. Only treat
     * them as Experiences when they contain Tourbi Experience content and the
     * existing mapping helper agrees.
     */
    $experience_content_keys = array(
        '_tourbi_experience_short_title',
        '_tourbi_experience_short_summary',
        '_tourbi_experience_itinerary',
        '_tourbi_experience_city',
        '_tourbi_experience_host_introduction',
    );

    $has_experience_content = false;

    foreach ( $experience_content_keys as $key ) {
        $value = get_post_meta(
            $item_id,
            $key,
            true
        );

        if (
            is_array( $value )
                ? ! empty( $value )
                : '' !== trim( (string) $value )
        ) {
            $has_experience_content = true;
            break;
        }
    }

    return $has_experience_content &&
        function_exists(
            'tourbi_core_is_experience_mapped'
        ) &&
        tourbi_core_is_experience_mapped(
            $item_id
        );
}

/**
 * Determine whether the current request is a mapped Tourbi Experience.
 *
 * @return bool
 */
function tourbi_theme_is_single_experience_request() {
    return is_singular( 'rbfw_item' ) &&
        tourbi_theme_item_is_experience(
            get_queried_object_id()
        );
}

/**
 * Determine whether the current request is a normal Rental item.
 *
 * @return bool
 */
function tourbi_theme_is_single_rental_request() {
    if ( ! is_singular( 'rbfw_item' ) ) {
        return false;
    }

    $item_id = get_queried_object_id();

    if ( tourbi_theme_item_is_rental_mapped( $item_id ) ) {
        return true;
    }

    return ! tourbi_theme_item_is_experience(
        $item_id
    );
}

/**
 * Register the normal Rental single-template route.
 *
 * @param array<int,array<string,mixed>> $routes Existing routes.
 * @return array<int,array<string,mixed>>
 */
function tourbi_theme_register_single_rental_route( $routes ) {
    array_unshift(
        $routes,
        array(
            'condition' =>
                'tourbi_theme_is_single_rental_request',
            'file'      =>
                'templates/single-tourbi-rental.php',
        )
    );

    return $routes;
}
add_filter(
    'tourbi_theme_template_routes',
    'tourbi_theme_register_single_rental_route',
    25
);

/**
 * Register the mapped Experience single-template route.
 *
 * @param array<int,array<string,mixed>> $routes Existing routes.
 * @return array<int,array<string,mixed>>
 */
function tourbi_theme_register_single_experience_route( $routes ) {
    array_unshift(
        $routes,
        array(
            'condition' =>
                'tourbi_theme_is_single_experience_request',
            'file'      =>
                'templates/single-tourbi-experience.php',
        )
    );

    return $routes;
}
add_filter(
    'tourbi_theme_template_routes',
    'tourbi_theme_register_single_experience_route',
    20
);

/**
 * Add explicit body classes for Rental/Experience debugging and styling.
 *
 * @param string[] $classes Existing classes.
 * @return string[]
 */
function tourbi_theme_rent_item_body_classes( $classes ) {
    if ( ! is_singular( 'rbfw_item' ) ) {
        return $classes;
    }

    $item_id = get_queried_object_id();
    $flags = tourbi_theme_get_item_mapping_flags(
        $item_id
    );

    if ( tourbi_theme_is_single_experience_request() ) {
        $classes[] = 'tourbi-single-experience';
        $classes[] = 'tourbi-listing-type-experience';
    }

    if ( tourbi_theme_is_single_rental_request() ) {
        $classes[] = 'tourbi-single-rental';
        $classes[] = 'tourbi-listing-type-rental';
    }

    if (
        ! empty( $flags['rental'] ) &&
        ! empty( $flags['experience'] )
    ) {
        $classes[] = 'tourbi-listing-mapping-conflict';
    }

    return array_values(
        array_unique( $classes )
    );
}
add_filter(
    'body_class',
    'tourbi_theme_rent_item_body_classes',
    70
);



/**
 * Show the resolved listing type in the WordPress admin bar.
 *
 * @param WP_Admin_Bar $admin_bar Admin bar object.
 * @return void
 */
function tourbi_theme_add_listing_type_admin_bar(
    $admin_bar
) {
    if (
        ! is_singular( 'rbfw_item' ) ||
        ! current_user_can( 'edit_posts' )
    ) {
        return;
    }

    $item_id = get_queried_object_id();
    $flags = tourbi_theme_get_item_mapping_flags(
        $item_id
    );

    $type = tourbi_theme_is_single_experience_request()
        ? __( 'Guided Experience', 'torby' )
        : __( 'Self-Guided Rental', 'torby' );

    $has_conflict =
        ! empty( $flags['rental'] ) &&
        ! empty( $flags['experience'] );

    $title = $has_conflict
        ? sprintf(
            /* translators: %s: Resolved listing type. */
            __(
                'Tourbi Type: %s — mapping conflict; Rental wins',
                'torby'
            ),
            $type
        )
        : sprintf(
            /* translators: %s: Resolved listing type. */
            __(
                'Tourbi Type: %s',
                'torby'
            ),
            $type
        );

    $admin_bar->add_node(
        array(
            'id' => 'tourbi-listing-type',
            'title' => esc_html( $title ),
            'href' => get_edit_post_link(
                $item_id
            ),
            'meta' => array(
                'title' => esc_attr__(
                    'Open this Rent Item to review its Tourbi mappings.',
                    'torby'
                ),
            ),
        )
    );
}
add_action(
    'admin_bar_menu',
    'tourbi_theme_add_listing_type_admin_bar',
    85
);

/**
 * Register the customer-facing Shop Hub route.
 *
 * @param array<int,array<string,mixed>> $routes Existing routes.
 * @return array<int,array<string,mixed>>
 */
function tourbi_theme_register_shop_hub_route( $routes ) {
    array_unshift(
        $routes,
        array(
            'condition' =>
                'tourbi_theme_is_shop_hub_request',
            'file'      =>
                'templates/archive-tourbi-shop.php',
        )
    );

    return $routes;
}
add_filter(
    'tourbi_theme_template_routes',
    'tourbi_theme_register_shop_hub_route',
    12
);

/**
 * Register the Experience Marketplace route.
 *
 * @param array<int,array<string,mixed>> $routes Existing routes.
 * @return array<int,array<string,mixed>>
 */
function tourbi_theme_register_marketplace_route( $routes ) {
    array_unshift(
        $routes,
        array(
            'condition' =>
                'tourbi_theme_is_marketplace_request',
            'file'      =>
                'templates/archive-tourbi-experiences.php',
        )
    );

    return $routes;
}
add_filter(
    'tourbi_theme_template_routes',
    'tourbi_theme_register_marketplace_route',
    10
);

/**
 * Register the Become a Host page route.
 *
 * @param array<int,array<string,mixed>> $routes Existing routes.
 * @return array<int,array<string,mixed>>
 */
function tourbi_theme_register_become_host_route( $routes ) {
    array_unshift(
        $routes,
        array(
            'condition' =>
                'tourbi_theme_is_become_host_request',
            'file'      =>
                'templates/page-become-a-host.php',
        )
    );

    return $routes;
}
add_filter(
    'tourbi_theme_template_routes',
    'tourbi_theme_register_become_host_route',
    5
);
