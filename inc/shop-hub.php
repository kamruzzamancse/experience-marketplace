<?php
/**
 * Customer-facing replacement for the generic WooCommerce Shop archive.
 *
 * Tourbi sells booking flows rather than a traditional product catalogue.
 * This module turns /shop/ into a clear choice between guided Experiences
 * and normal E-Bike Rentals, while keeping WooCommerce checkout intact.
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Determine whether the current request is the WooCommerce Shop archive.
 *
 * @return bool
 */
function tourbi_theme_is_shop_hub_request() {
    return function_exists( 'is_shop' ) &&
        is_shop();
}

/**
 * Return one image for a Shop Hub service card.
 *
 * @param bool $experience Whether to query an Experience.
 * @return string
 */
function tourbi_theme_get_shop_hub_image_url(
    $experience
) {
    $meta_query = array();

    if ( $experience ) {
        $meta_query[] = array(
            'key'     =>
                '_tourbi_experience_enabled',
            'value'   => 'yes',
            'compare' => '=',
        );
    } else {
        $meta_query[] = array(
            'relation' => 'OR',
            array(
                'key'     =>
                    '_tourbi_experience_enabled',
                'compare' => 'NOT EXISTS',
            ),
            array(
                'key'     =>
                    '_tourbi_experience_enabled',
                'value'   => 'yes',
                'compare' => '!=',
            ),
        );
    }

    $query = new WP_Query(
        array(
            'post_type'      => 'rbfw_item',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'no_found_rows'  => true,
            'meta_query'     => $meta_query,
            'orderby'        => array(
                'modified' => 'DESC',
                'date'     => 'DESC',
            ),
        )
    );

    if ( empty( $query->posts[0] ) ) {
        return '';
    }

    return (string) get_the_post_thumbnail_url(
        absint( $query->posts[0] ),
        'large'
    );
}

/**
 * Return the Shop Hub page data.
 *
 * @return array<string,mixed>
 */
function tourbi_theme_get_shop_hub_view_model() {
    return array(
        'experience_url' =>
            tourbi_theme_get_experience_archive_url(),
        'rental_url' =>
            function_exists(
                'tourbi_theme_get_rent_now_url'
            )
                ? tourbi_theme_get_rent_now_url()
                : home_url( '/rent/' ),
        'account_url' =>
            function_exists(
                'tourbi_theme_get_site_account_url'
            )
                ? tourbi_theme_get_site_account_url()
                : home_url( '/my-account/' ),
        'experience_image' =>
            tourbi_theme_get_shop_hub_image_url(
                true
            ),
        'rental_image' =>
            tourbi_theme_get_shop_hub_image_url(
                false
            ),
    );
}

/**
 * Add a Shop Hub body class.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function tourbi_theme_shop_hub_body_classes(
    $classes
) {
    if ( tourbi_theme_is_shop_hub_request() ) {
        $classes[] = 'tourbi-shop-hub-page';
    }

    return array_values(
        array_unique( $classes )
    );
}
add_filter(
    'body_class',
    'tourbi_theme_shop_hub_body_classes',
    80
);
