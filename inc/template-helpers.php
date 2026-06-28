<?php
/**
 * Reusable helpers for future Tourbi custom templates.
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Return a class string from a list of class values.
 *
 * @param array<int|string,mixed> $classes Class values.
 * @return string
 */
function tourbi_theme_class_names( $classes ) {
    $normalized = array();

    foreach ( (array) $classes as $key => $value ) {
        if ( is_string( $key ) ) {
            if ( $value ) {
                $normalized[] = $key;
            }

            continue;
        }

        if ( is_string( $value ) && '' !== trim( $value ) ) {
            $normalized[] = $value;
        }
    }

    $normalized = array_values(
        array_unique(
            array_map(
                'sanitize_html_class',
                $normalized
            )
        )
    );

    return implode( ' ', $normalized );
}

/**
 * Render a reusable Tourbi button link.
 *
 * @param string              $label Button label.
 * @param string              $url Destination URL.
 * @param array<string,mixed> $args Optional settings.
 * @return string
 */
function tourbi_theme_button(
    $label,
    $url,
    $args = array()
) {
    $args = wp_parse_args(
        $args,
        array(
            'variant'   => 'primary',
            'size'      => 'medium',
            'icon'      => '',
            'class'     => '',
            'new_tab'   => false,
            'aria_label'=> '',
        )
    );

    $classes = tourbi_theme_class_names(
        array(
            'tourbi-button',
            'tourbi-button--' .
                sanitize_key(
                    $args['variant']
                ),
            'tourbi-button--' .
                sanitize_key(
                    $args['size']
                ),
            $args['class'],
        )
    );

    $target = $args['new_tab']
        ? ' target="_blank" rel="noopener noreferrer"'
        : '';

    $aria_label = '' !== $args['aria_label']
        ? ' aria-label="' .
            esc_attr( $args['aria_label'] ) .
            '"'
        : '';

    $icon = '' !== $args['icon']
        ? '<span class="tourbi-button__icon" aria-hidden="true">' .
            esc_html( $args['icon'] ) .
            '</span>'
        : '';

    return sprintf(
        '<a class="%1$s" href="%2$s"%3$s%4$s><span class="tourbi-button__label">%5$s</span>%6$s</a>',
        esc_attr( $classes ),
        esc_url( $url ),
        $target,
        $aria_label,
        esc_html( $label ),
        $icon
    );
}

/**
 * Return the Experience marketplace URL.
 *
 * @return string
 */
function tourbi_theme_get_experience_archive_url() {
    $page_id = function_exists(
        'tourbi_theme_get_marketplace_page_id'
    )
        ? tourbi_theme_get_marketplace_page_id()
        : 0;

    if ( $page_id ) {
        return get_permalink( $page_id );
    }

    $page = get_page_by_path( 'experiences' );

    if ( $page instanceof WP_Post ) {
        return get_permalink( $page );
    }

    $archive_url = get_post_type_archive_link(
        'rbfw_item'
    );

    return $archive_url
        ? $archive_url
        : home_url( '/experiences/' );
}

/**
 * Return the Become a Host page URL.
 *
 * @return string
 */
function tourbi_theme_get_become_host_url() {
    $page_id = function_exists(
        'tourbi_theme_get_become_host_page_id'
    )
        ? tourbi_theme_get_become_host_page_id()
        : 0;

    if ( $page_id ) {
        return get_permalink( $page_id );
    }

    $page = get_page_by_path(
        'become-a-host'
    );

    return $page instanceof WP_Post
        ? get_permalink( $page )
        : home_url( '/become-a-host/' );
}

/**
 * Return the current site/location label for UI placeholders.
 *
 * @return string
 */
function tourbi_theme_get_location_label() {
    return (string) apply_filters(
        'tourbi_theme_location_label',
        'Washington, D.C.'
    );
}
