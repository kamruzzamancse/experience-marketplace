<?php
/**
 * View-model helpers for the custom Single Experience template.
 *
 * All commercial, schedule, inventory, availability, hold, and checkout
 * values continue to come from Tourbi Core and WpRently. The child theme only
 * normalizes those values for presentation.
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Format a Tourbi Experience duration.
 *
 * @param int $minutes Duration in minutes.
 * @return string
 */
function tourbi_theme_format_experience_duration( $minutes ) {
    $minutes = absint( $minutes );

    if ( $minutes < 1 ) {
        return '';
    }

    $hours = intdiv( $minutes, 60 );
    $remaining = $minutes % 60;

    if ( $hours > 0 && 0 === $remaining ) {
        return sprintf(
            /* translators: %d: Number of hours. */
            _n(
                '%d Hour',
                '%d Hours',
                $hours,
                'torby'
            ),
            $hours
        );
    }

    if ( $hours > 0 ) {
        return sprintf(
            /* translators: 1: Hours. 2: Minutes. */
            __( '%1$d hr %2$d min', 'torby' ),
            $hours,
            $remaining
        );
    }

    return sprintf(
        /* translators: %d: Number of minutes. */
        _n(
            '%d Minute',
            '%d Minutes',
            $minutes,
            'torby'
        ),
        $minutes
    );
}

/**
 * Format a stored Tourbi bike type for customers.
 *
 * @param string $bike_type Bike type key.
 * @return string
 */
function tourbi_theme_format_bike_type( $bike_type ) {
    $bike_type = sanitize_key( $bike_type );

    $labels = array(
        'standard' => __( 'Standard E-Bike', 'torby' ),
        'compact'  => __( 'Compact E-Bike', 'torby' ),
        'cargo'    => __( 'Cargo E-Bike', 'torby' ),
    );

    if ( isset( $labels[ $bike_type ] ) ) {
        return $labels[ $bike_type ];
    }

    return '' !== $bike_type
        ? ucwords(
            str_replace(
                array( '-', '_' ),
                ' ',
                $bike_type
            )
        )
        : __( 'E-Bike', 'torby' );
}

/**
 * Return a sanitized Experience description without invoking the WpRently
 * single-page renderer again.
 *
 * @param int $experience_id Experience ID.
 * @return string
 */
function tourbi_theme_get_experience_description_html(
    $experience_id
) {
    $content = (string) get_post_field(
        'post_content',
        absint( $experience_id )
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
 * Normalize one media attachment for gallery rendering.
 *
 * @param int $attachment_id Attachment ID.
 * @return array<string,mixed>|null
 */
function tourbi_theme_get_experience_media_item(
    $attachment_id
) {
    $attachment_id = absint( $attachment_id );

    if (
        ! $attachment_id ||
        'attachment' !== get_post_type( $attachment_id )
    ) {
        return null;
    }

    $full = wp_get_attachment_image_url(
        $attachment_id,
        'full'
    );

    $large = wp_get_attachment_image_url(
        $attachment_id,
        'large'
    );

    if ( ! $full && ! $large ) {
        return null;
    }

    return array(
        'id'      => $attachment_id,
        'full'    => $full ?: $large,
        'large'   => $large ?: $full,
        'alt'     => sanitize_text_field(
            get_post_meta(
                $attachment_id,
                '_wp_attachment_image_alt',
                true
            )
        ),
        'caption' => sanitize_text_field(
            wp_get_attachment_caption(
                $attachment_id
            )
        ),
    );
}

/**
 * Return the Experience host presentation profile.
 *
 * @param int $host_id Host user ID.
 * @return array<string,mixed>
 */
function tourbi_theme_get_experience_host_profile( $host_id ) {
    $host_id = absint( $host_id );
    $user = $host_id ? get_user_by( 'id', $host_id ) : false;

    if ( ! $user instanceof WP_User ) {
        return array(
            'id'          => 0,
            'name'        => __( 'Your Tourbi Host', 'torby' ),
            'description' => '',
            'avatar'      => get_avatar_url( 0, array( 'size' => 320 ) ),
            'location'    => '',
        );
    }

    $settings = get_user_meta(
        $host_id,
        'wcfmmp_profile_settings',
        true
    );

    $settings = is_array( $settings )
        ? $settings
        : array();

    $store_name = sanitize_text_field(
        $settings['store_name'] ?? ''
    );

    $first_name = sanitize_text_field(
        get_user_meta(
            $host_id,
            'first_name',
            true
        )
    );

    $last_name = sanitize_text_field(
        get_user_meta(
            $host_id,
            'last_name',
            true
        )
    );

    $personal_name = trim(
        $first_name . ' ' . $last_name
    );

    $display_name = sanitize_text_field(
        $user->display_name
    );

    /*
     * A Meet Your Host section should prioritize a person's public name.
     * The WCFM store name remains the last presentation fallback.
     */
    $host_name = '' !== $personal_name
        ? $personal_name
        : (
            '' !== $display_name
                ? $display_name
                : $store_name
        );

    if ( '' === $host_name ) {
        $host_name = __(
            'Your Tourbi Host',
            'torby'
        );
    }

    $description = sanitize_textarea_field(
        get_user_meta(
            $host_id,
            'description',
            true
        )
    );

    $address = $settings['address'] ?? array();
    $address = is_array( $address ) ? $address : array();

    $location_parts = array_filter(
        array_map(
            'sanitize_text_field',
            array(
                $address['city'] ?? '',
                $address['state'] ?? '',
                $address['country'] ?? '',
            )
        )
    );

    return array(
        'id'          => $host_id,
        'name'        => $host_name,
        'description' => $description,
        'avatar'      => get_avatar_url(
            $host_id,
            array( 'size' => 420 )
        ),
        'location'    => implode( ', ', $location_parts ),
    );
}

/**
 * Return a no-key OpenStreetMap embed configuration.
 *
 * @param string $latitude Latitude.
 * @param string $longitude Longitude.
 * @return array<string,string>
 */
function tourbi_theme_get_experience_map( $latitude, $longitude ) {
    if ( '' === (string) $latitude || '' === (string) $longitude ) {
        return array(
            'embed_url'     => '',
            'directions_url'=> '',
        );
    }

    $latitude = (float) $latitude;
    $longitude = (float) $longitude;
    $offset = 0.008;

    $bbox = implode(
        ',',
        array(
            $longitude - $offset,
            $latitude - $offset,
            $longitude + $offset,
            $latitude + $offset,
        )
    );

    return array(
        'embed_url' => add_query_arg(
            array(
                'bbox'   => $bbox,
                'layer'  => 'mapnik',
                'marker' => $latitude . ',' . $longitude,
            ),
            'https://www.openstreetmap.org/export/embed.html'
        ),
        'directions_url' => add_query_arg(
            array(
                'api'   => 1,
                'query' => $latitude . ',' . $longitude,
            ),
            'https://www.google.com/maps/search/'
        ),
    );
}

/**
 * Build the complete custom Single Experience view model.
 *
 * @param int $experience_id Experience Rent Item ID.
 * @return array<string,mixed>
 */
function tourbi_theme_get_single_experience_view_model(
    $experience_id
) {
    $experience_id = absint( $experience_id );

    $content_profile = function_exists(
        'tourbi_core_get_experience_content_profile'
    )
        ? tourbi_core_get_experience_content_profile(
            $experience_id
        )
        : array();

    $mapping = function_exists(
        'tourbi_core_get_experience_mapping'
    )
        ? tourbi_core_get_experience_mapping(
            $experience_id
        )
        : array();

    $commercial = function_exists(
        'tourbi_core_get_experience_commercial_profile'
    )
        ? tourbi_core_get_experience_commercial_profile(
            $experience_id
        )
        : array();

    $itinerary = function_exists(
        'tourbi_core_get_experience_itinerary'
    )
        ? tourbi_core_get_experience_itinerary(
            $experience_id
        )
        : array();

    $price = (float) ( $commercial['price'] ?? 0 );
    $currency_symbol = function_exists(
        'get_woocommerce_currency_symbol'
    )
        ? get_woocommerce_currency_symbol()
        : '$';

    $price_html = $price > 0
        ? (
            function_exists( 'wc_price' )
                ? wc_price( $price )
                : esc_html(
                    $currency_symbol .
                    number_format_i18n( $price, 2 )
                )
        )
        : esc_html__( 'Check availability', 'torby' );

    $main_image_id = absint(
        $content_profile['main_image_id'] ??
        get_post_thumbnail_id( $experience_id )
    );

    $gallery_ids = array_values(
        array_unique(
            array_filter(
                array_merge(
                    array( $main_image_id ),
                    (array) (
                        $content_profile['gallery_ids'] ??
                        array()
                    )
                )
            )
        )
    );

    $gallery = array_values(
        array_filter(
            array_map(
                'tourbi_theme_get_experience_media_item',
                $gallery_ids
            )
        )
    );

    $categories = (array) (
        $content_profile['categories'] ?? array()
    );

    $tags = (array) (
        $content_profile['tags'] ?? array()
    );

    $difficulty = sanitize_key(
        $content_profile['difficulty'] ?? 'moderate'
    );

    $difficulty_options = function_exists(
        'tourbi_core_get_experience_difficulty_options'
    )
        ? tourbi_core_get_experience_difficulty_options()
        : array(
            'easy'        => __( 'Easy', 'torby' ),
            'moderate'    => __( 'Moderate', 'torby' ),
            'challenging' => __( 'Challenging', 'torby' ),
        );

    $duration = absint(
        $commercial['duration'] ??
        $mapping['duration_minutes'] ?? 0
    );

    $host_id = absint(
        $content_profile['host_id'] ??
        $mapping['host_id'] ?? 0
    );

    $host = tourbi_theme_get_experience_host_profile(
        $host_id
    );

    if (
        '' !== trim(
            (string) (
                $content_profile['host_introduction'] ?? ''
            )
        )
    ) {
        $host['description'] = sanitize_textarea_field(
            $content_profile['host_introduction']
        );
    }

    $map = tourbi_theme_get_experience_map(
        $content_profile['latitude'] ?? '',
        $content_profile['longitude'] ?? ''
    );

    $start_time = sanitize_text_field(
        $commercial['start_time'] ??
        $mapping['default_start_time'] ?? ''
    );

    $end_time = sanitize_text_field(
        $commercial['end_time'] ??
        $mapping['default_end_time'] ?? ''
    );

    $format_time = static function ( $time ) {
        return function_exists(
            'tourbi_core_format_experience_time_label'
        )
            ? tourbi_core_format_experience_time_label(
                $time
            )
            : $time;
    };

    $max_participants = max(
        1,
        absint(
            $content_profile['max_participants'] ??
            $mapping['max_participants'] ?? 1
        )
    );

    $min_participants = max(
        1,
        absint(
            $content_profile['min_participants'] ?? 1
        )
    );

    $view_model = array(
        'id'                  => $experience_id,
        'title'               => sanitize_text_field(
            get_the_title( $experience_id )
        ),
        'short_title'         => sanitize_text_field(
            $content_profile['short_title'] ??
            get_the_title( $experience_id )
        ),
        'summary'             => sanitize_textarea_field(
            $content_profile['short_summary'] ?? ''
        ),
        'description_html'    =>
            tourbi_theme_get_experience_description_html(
                $experience_id
            ),
        'featured'            => ! empty(
            $content_profile['featured']
        ),
        'categories'          => $categories,
        'tags'                => $tags,
        'primary_category'    => $categories[0]['name'] ?? '',
        'price'               => $price,
        'price_html'          => $price_html,
        'duration_minutes'    => $duration,
        'duration_label'      =>
            tourbi_theme_format_experience_duration(
                $duration
            ),
        'start_time'          => $start_time,
        'end_time'            => $end_time,
        'start_time_label'    => $format_time( $start_time ),
        'end_time_label'      => $format_time( $end_time ),
        'min_participants'    => $min_participants,
        'max_participants'    => $max_participants,
        'participant_label'   => sprintf(
            /* translators: 1: Minimum guests. 2: Maximum guests. */
            __( '%1$d–%2$d People', 'torby' ),
            $min_participants,
            $max_participants
        ),
        'bike_type'           => sanitize_key(
            $mapping['bike_type'] ?? ''
        ),
        'bike_type_label'     =>
            tourbi_theme_format_bike_type(
                $mapping['bike_type'] ?? ''
            ),
        'difficulty'          => $difficulty,
        'difficulty_label'    =>
            $difficulty_options[ $difficulty ] ??
            ucfirst( $difficulty ),
        'audience'            => sanitize_text_field(
            $content_profile['audience'] ?? ''
        ),
        'city'                => sanitize_text_field(
            $content_profile['city'] ?? ''
        ),
        'meeting_address'     => sanitize_textarea_field(
            $content_profile['meeting_address'] ?? ''
        ),
        'latitude'            => sanitize_text_field(
            $content_profile['latitude'] ?? ''
        ),
        'longitude'           => sanitize_text_field(
            $content_profile['longitude'] ?? ''
        ),
        'map'                 => $map,
        'inclusions'          => (array) (
            $content_profile['inclusions'] ?? array()
        ),
        'exclusions'          => (array) (
            $content_profile['exclusions'] ?? array()
        ),
        'cancellation_policy' => sanitize_textarea_field(
            $content_profile['cancellation_policy'] ?? ''
        ),
        'gallery'             => $gallery,
        'itinerary'           => $itinerary,
        'host'                => $host,
        'booking_shortcode'   => sprintf(
            '[rent-add-to-cart id="%d"]',
            $experience_id
        ),
        'permalink'           => get_permalink(
            $experience_id
        ),
    );

    /**
     * Filter the Single Experience view model.
     *
     * @param array<string,mixed> $view_model Experience data.
     * @param int                 $experience_id Experience ID.
     */
    return (array) apply_filters(
        'tourbi_theme_single_experience_view_model',
        $view_model,
        $experience_id
    );
}
