<?php
/**
 * Tourbi hybrid theme foundation setup.
 *
 * Presentation-only concerns belong here. Booking, inventory, approval,
 * payment, reservation, notification, and permission logic remain in the
 * Tourbi Core plugin.
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register theme supports used by future Tourbi custom templates.
 *
 * Hello Elementor remains the parent theme and the Elementor-built homepage
 * remains a normal WordPress page.
 *
 * @return void
 */
function tourbi_theme_foundation_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'align-wide' );

    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        )
    );

    add_theme_support(
        'custom-logo',
        array(
            'height'      => 120,
            'width'       => 420,
            'flex-height' => true,
            'flex-width'  => true,
        )
    );

    register_nav_menus(
        array(
            'tourbi-primary' => esc_html__(
                'Tourbi Primary Navigation',
                'torby'
            ),
            'tourbi-footer'  => esc_html__(
                'Tourbi Footer Navigation',
                'torby'
            ),
        )
    );

    add_image_size(
        'tourbi-marketplace-card',
        900,
        680,
        true
    );

    add_image_size(
        'tourbi-experience-wide',
        1600,
        980,
        true
    );

    add_image_size(
        'tourbi-itinerary-stop',
        720,
        820,
        true
    );

    add_image_size(
        'tourbi-host-profile',
        560,
        680,
        true
    );
}
add_action(
    'after_setup_theme',
    'tourbi_theme_foundation_setup',
    40
);

/**
 * Add the new Tourbi image sizes to Media Library selectors.
 *
 * @param array<string,string> $sizes Existing sizes.
 * @return array<string,string>
 */
function tourbi_theme_foundation_image_size_names( $sizes ) {
    $sizes['tourbi-marketplace-card'] = esc_html__(
        'Tourbi Marketplace Card',
        'torby'
    );

    $sizes['tourbi-experience-wide'] = esc_html__(
        'Tourbi Experience Wide',
        'torby'
    );

    $sizes['tourbi-itinerary-stop'] = esc_html__(
        'Tourbi Itinerary Stop',
        'torby'
    );

    $sizes['tourbi-host-profile'] = esc_html__(
        'Tourbi Host Profile',
        'torby'
    );

    return $sizes;
}
add_filter(
    'image_size_names_choose',
    'tourbi_theme_foundation_image_size_names',
    40
);

/**
 * Add a stable foundation version class for visual debugging.
 *
 * @param string[] $classes Existing body classes.
 * @return string[]
 */
function tourbi_theme_foundation_body_version_class( $classes ) {
    $classes[] = 'tourbi-foundation-v1';

    return array_values(
        array_unique( $classes )
    );
}
add_filter(
    'body_class',
    'tourbi_theme_foundation_body_version_class',
    40
);
