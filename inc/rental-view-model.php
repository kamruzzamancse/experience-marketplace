<?php
/**
 * Presentation helpers for a normal Tourbi Rental page.
 *
 * Booking, pricing calculations, availability, inventory holds, checkout,
 * reservations, and order handling remain owned by WpRently and Tourbi Core.
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Render stored Rental content without recursively invoking a single template.
 *
 * @param int $rental_id Rental ID.
 * @return string
 */
function tourbi_theme_get_rental_description_html( $rental_id ) {
    $content = (string) get_post_field(
        'post_content',
        absint( $rental_id )
    );

    if ( '' === trim( $content ) ) {
        return '';
    }

    $content = do_blocks( $content );
    $content = shortcode_unautop( $content );
    $content = do_shortcode( $content );

    if ( false === strpos( $content, '<p' ) ) {
        $content = wpautop( $content );
    }

    return wp_kses_post( $content );
}

/**
 * Recursively collect positive numeric values.
 *
 * @param mixed   $value Stored value.
 * @param float[] $numbers Existing numbers.
 * @return float[]
 */
function tourbi_theme_collect_rental_numbers(
    $value,
    $numbers = array()
) {
    if ( is_array( $value ) ) {
        foreach ( $value as $child ) {
            $numbers = tourbi_theme_collect_rental_numbers(
                $child,
                $numbers
            );
        }

        return $numbers;
    }

    if ( is_object( $value ) ) {
        return tourbi_theme_collect_rental_numbers(
            (array) $value,
            $numbers
        );
    }

    if ( is_numeric( $value ) ) {
        $number = (float) $value;

        if ( $number > 0 && $number < 100000 ) {
            $numbers[] = $number;
        }
    }

    return $numbers;
}

/**
 * Return a conservative starting Rental price for presentation.
 *
 * Final price always comes from the WpRently booking widget.
 *
 * @param int $rental_id Rental ID.
 * @return float
 */
function tourbi_theme_get_rental_starting_price( $rental_id ) {
    $rental_id = absint( $rental_id );

    $preferred_keys = array(
        '_price',
        '_regular_price',
        'rbfw_price',
        'rbfw_rent_price',
        'rbfw_default_price',
        'rbfw_hourly_price',
        'rbfw_daily_price',
    );

    foreach ( $preferred_keys as $key ) {
        $numbers = tourbi_theme_collect_rental_numbers(
            maybe_unserialize(
                get_post_meta(
                    $rental_id,
                    $key,
                    true
                )
            )
        );

        if ( ! empty( $numbers ) ) {
            return min( $numbers );
        }
    }

    $all_meta = get_post_meta( $rental_id );
    $numbers = array();

    foreach ( $all_meta as $key => $values ) {
        if (
            false === stripos( $key, 'price' ) ||
            false !== stripos( $key, 'deposit' ) ||
            false !== stripos( $key, 'discount' )
        ) {
            continue;
        }

        foreach ( (array) $values as $value ) {
            $numbers = tourbi_theme_collect_rental_numbers(
                maybe_unserialize( $value ),
                $numbers
            );
        }
    }

    return ! empty( $numbers )
        ? min( $numbers )
        : 0.0;
}

/**
 * Recursively collect valid image attachment IDs.
 *
 * @param mixed $value Stored value.
 * @param int[] $ids Existing IDs.
 * @return int[]
 */
function tourbi_theme_collect_rental_image_ids(
    $value,
    $ids = array()
) {
    if ( is_array( $value ) ) {
        foreach ( $value as $child ) {
            $ids = tourbi_theme_collect_rental_image_ids(
                $child,
                $ids
            );
        }

        return $ids;
    }

    if ( is_object( $value ) ) {
        return tourbi_theme_collect_rental_image_ids(
            (array) $value,
            $ids
        );
    }

    if ( is_string( $value ) && false !== strpos( $value, ',' ) ) {
        foreach ( explode( ',', $value ) as $part ) {
            $ids = tourbi_theme_collect_rental_image_ids(
                trim( $part ),
                $ids
            );
        }

        return $ids;
    }

    $attachment_id = absint( $value );

    if (
        $attachment_id &&
        wp_attachment_is_image( $attachment_id )
    ) {
        $ids[] = $attachment_id;
    }

    return array_values(
        array_unique( $ids )
    );
}

/**
 * Return Rental gallery images.
 *
 * @param int $rental_id Rental ID.
 * @return array<int,array<string,mixed>>
 */
function tourbi_theme_get_rental_gallery( $rental_id ) {
    $rental_id = absint( $rental_id );
    $ids = array_filter(
        array( get_post_thumbnail_id( $rental_id ) )
    );

    $gallery_keys = array(
        'rbfw_gallery_images',
        'rbfw_gallery',
        'rbfw_gallery_ids',
        'rbfw_extra_images',
        'rbfw_image_gallery',
        'mp_gallery_images',
    );

    foreach ( $gallery_keys as $key ) {
        $ids = tourbi_theme_collect_rental_image_ids(
            maybe_unserialize(
                get_post_meta(
                    $rental_id,
                    $key,
                    true
                )
            ),
            $ids
        );
    }

    if ( count( $ids ) < 2 ) {
        $children = get_children(
            array(
                'post_parent' => $rental_id,
                'post_type' => 'attachment',
                'post_mime_type' => 'image',
                'orderby' => 'menu_order ID',
                'order' => 'ASC',
                'numberposts' => 8,
                'fields' => 'ids',
            )
        );

        $ids = array_values(
            array_unique(
                array_merge(
                    $ids,
                    array_map( 'absint', $children )
                )
            )
        );
    }

    $gallery = array();

    foreach ( array_slice( $ids, 0, 10 ) as $attachment_id ) {
        $large = wp_get_attachment_image_url(
            $attachment_id,
            'large'
        );
        $full = wp_get_attachment_image_url(
            $attachment_id,
            'full'
        );

        if ( ! $large && ! $full ) {
            continue;
        }

        $gallery[] = array(
            'id' => absint( $attachment_id ),
            'large' => $large ?: $full,
            'full' => $full ?: $large,
            'alt' => sanitize_text_field(
                get_post_meta(
                    $attachment_id,
                    '_wp_attachment_image_alt',
                    true
                )
            ),
        );
    }

    return $gallery;
}

/**
 * Recursively collect short feature labels from Rental metadata.
 *
 * @param mixed    $value Stored value.
 * @param string[] $labels Existing labels.
 * @return string[]
 */
function tourbi_theme_collect_rental_feature_labels(
    $value,
    $labels = array()
) {
    if ( is_array( $value ) ) {
        foreach ( $value as $key => $child ) {
            if (
                is_string( $key ) &&
                ! is_numeric( $key ) &&
                in_array(
                    strtolower( $key ),
                    array(
                        'label',
                        'title',
                        'name',
                        'feature',
                        'text',
                    ),
                    true
                )
            ) {
                $labels = tourbi_theme_collect_rental_feature_labels(
                    $child,
                    $labels
                );
                continue;
            }

            if ( is_array( $child ) || is_object( $child ) ) {
                $labels = tourbi_theme_collect_rental_feature_labels(
                    $child,
                    $labels
                );
            }
        }

        return $labels;
    }

    if ( is_object( $value ) ) {
        return tourbi_theme_collect_rental_feature_labels(
            (array) $value,
            $labels
        );
    }

    $label = sanitize_text_field( (string) $value );
    $length = strlen( $label );

    if (
        $length >= 3 &&
        $length <= 70 &&
        ! is_numeric( $label ) &&
        false === stripos( $label, 'http' )
    ) {
        $labels[] = $label;
    }

    return array_values(
        array_unique( $labels )
    );
}

/**
 * Return customer-facing Rental feature labels.
 *
 * @param int $rental_id Rental ID.
 * @return string[]
 */
function tourbi_theme_get_rental_features( $rental_id ) {
    $all_meta = get_post_meta( absint( $rental_id ) );
    $labels = array();

    foreach ( $all_meta as $key => $values ) {
        if (
            false === stripos( $key, 'feature' ) &&
            false === stripos( $key, 'amenit' ) &&
            false === stripos( $key, 'include' )
        ) {
            continue;
        }

        foreach ( (array) $values as $value ) {
            $labels = tourbi_theme_collect_rental_feature_labels(
                maybe_unserialize( $value ),
                $labels
            );
        }
    }

    if ( empty( $labels ) ) {
        $labels = array(
            __( 'Live date and time availability', 'torby' ),
            __( 'Secure online checkout', 'torby' ),
            __( 'Quantity protected during booking', 'torby' ),
            __( 'Pickup and return details after checkout', 'torby' ),
        );
    }

    return array_slice( $labels, 0, 8 );
}

/**
 * Return a readable Rental type label.
 *
 * @param int $rental_id Rental ID.
 * @return string
 */
function tourbi_theme_get_rental_type_label( $rental_id ) {
    $keys = array(
        'rbfw_rent_type',
        'rbfw_item_type',
        'rbfw_type',
        '_rbfw_rent_type',
    );

    foreach ( $keys as $key ) {
        $value = get_post_meta(
            absint( $rental_id ),
            $key,
            true
        );

        if ( is_scalar( $value ) && '' !== trim( (string) $value ) ) {
            return ucwords(
                str_replace(
                    array( '-', '_' ),
                    ' ',
                    sanitize_text_field( (string) $value )
                )
            );
        }
    }

    return __( 'Flexible E-Bike Rental', 'torby' );
}

/**
 * Build the normal Rental page view model.
 *
 * @param int $rental_id Rental ID.
 * @return array<string,mixed>
 */
function tourbi_theme_get_single_rental_view_model( $rental_id ) {
    $rental_id = absint( $rental_id );

    if (
        ! $rental_id ||
        'rbfw_item' !== get_post_type( $rental_id ) ||
        (
            function_exists(
                'tourbi_theme_item_is_experience'
            ) &&
            tourbi_theme_item_is_experience(
                $rental_id
            )
        )
    ) {
        return array();
    }

    $price = tourbi_theme_get_rental_starting_price(
        $rental_id
    );

    $price_html = $price > 0
        ? (
            function_exists( 'wc_price' )
                ? wc_price( $price )
                : '$' . number_format_i18n(
                    $price,
                    2
                )
        )
        : __( 'Check availability', 'torby' );

    $excerpt = get_the_excerpt( $rental_id );

    if ( '' === trim( $excerpt ) ) {
        $excerpt = wp_trim_words(
            wp_strip_all_tags(
                get_post_field(
                    'post_content',
                    $rental_id
                )
            ),
            28
        );
    }

    $view_model = array(
        'id' => $rental_id,
        'title' => sanitize_text_field(
            get_the_title( $rental_id )
        ),
        'summary' => sanitize_textarea_field(
            $excerpt
        ),
        'type_label' =>
            tourbi_theme_get_rental_type_label(
                $rental_id
            ),
        'price' => $price,
        'price_html' => $price_html,
        'description_html' =>
            tourbi_theme_get_rental_description_html(
                $rental_id
            ),
        'gallery' =>
            tourbi_theme_get_rental_gallery(
                $rental_id
            ),
        'features' =>
            tourbi_theme_get_rental_features(
                $rental_id
            ),
        'booking_shortcode' => sprintf(
            '[rent-add-to-cart id="%d"]',
            $rental_id
        ),
        'archive_url' => function_exists(
            'get_post_type_archive_link'
        )
            ? get_post_type_archive_link(
                'rbfw_item'
            )
            : home_url( '/rent/' ),
    );

    /**
     * Filter the normal Rental view model.
     *
     * @param array<string,mixed> $view_model Rental data.
     * @param int                 $rental_id Rental ID.
     */
    return (array) apply_filters(
        'tourbi_theme_single_rental_view_model',
        $view_model,
        $rental_id
    );
}
