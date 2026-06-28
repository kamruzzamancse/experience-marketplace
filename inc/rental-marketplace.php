<?php
/**
 * Dedicated Bike Rentals marketplace.
 *
 * This page lists only normal Tourbi Rentals. Guided Experiences remain on
 * /experiences/. Single listing URLs continue to use the WpRently /rent/
 * structure, but the public archive is redirected to this dedicated page.
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Return the dedicated Bike Rentals page ID.
 *
 * @return int
 */
function tourbi_theme_get_rental_marketplace_page_id() {
    $stored_id = absint(
        get_option(
            'tourbi_theme_rental_marketplace_page_id',
            0
        )
    );

    if (
        $stored_id &&
        'page' === get_post_type( $stored_id ) &&
        'trash' !== get_post_status( $stored_id )
    ) {
        return $stored_id;
    }

    $page = get_page_by_path(
        'bike-rentals',
        OBJECT,
        'page'
    );

    if ( $page instanceof WP_Post ) {
        update_option(
            'tourbi_theme_rental_marketplace_page_id',
            $page->ID,
            false
        );

        return absint( $page->ID );
    }

    return 0;
}

/**
 * Return the public Bike Rentals URL.
 *
 * @return string
 */
function tourbi_theme_get_rental_marketplace_url() {
    $page_id =
        tourbi_theme_get_rental_marketplace_page_id();

    return $page_id
        ? get_permalink( $page_id )
        : home_url( '/bike-rentals/' );
}

/**
 * Determine whether a stored Rent Now URL should be replaced.
 *
 * @param string $url Current URL.
 * @return bool
 */
function tourbi_theme_rental_destination_needs_sync(
    $url
) {
    $url = trim( (string) $url );

    if ( '' === $url ) {
        return true;
    }

    $path = (string) wp_parse_url(
        $url,
        PHP_URL_PATH
    );

    if (
        in_array(
            untrailingslashit( $path ),
            array(
                untrailingslashit(
                    (string) wp_parse_url(
                        home_url( '/rent/' ),
                        PHP_URL_PATH
                    )
                ),
                untrailingslashit(
                    (string) wp_parse_url(
                        home_url( '/shop/' ),
                        PHP_URL_PATH
                    )
                ),
            ),
            true
        )
    ) {
        return true;
    }

    foreach (
        array(
            'classic-template',
            'single-day-multi-hour',
            '/demo',
            '/test',
            'template',
        ) as $fragment
    ) {
        if ( false !== stripos( $url, $fragment ) ) {
            return true;
        }
    }

    return false;
}

/**
 * Create or configure the Bike Rentals page.
 *
 * Existing page content is never overwritten.
 *
 * @return void
 */
function tourbi_theme_ensure_rental_marketplace_page() {
    if (
        ! current_user_can( 'edit_pages' ) ||
        wp_doing_ajax()
    ) {
        return;
    }

    $page_id =
        tourbi_theme_get_rental_marketplace_page_id();

    if ( ! $page_id ) {
        $page_id = wp_insert_post(
            array(
                'post_type'    => 'page',
                'post_status'  => 'publish',
                'post_title'   => __(
                    'Bike Rentals',
                    'torby'
                ),
                'post_name'    => 'bike-rentals',
                'post_content' => '',
            ),
            true
        );

        if ( is_wp_error( $page_id ) ) {
            return;
        }

        update_option(
            'tourbi_theme_rental_marketplace_page_id',
            absint( $page_id ),
            false
        );
    }

    if (
        'templates/page-bike-rentals.php' !==
        get_post_meta(
            $page_id,
            '_wp_page_template',
            true
        )
    ) {
        update_post_meta(
            $page_id,
            '_wp_page_template',
            'templates/page-bike-rentals.php'
        );
    }

    $current_destination = (string) get_theme_mod(
        'tourbi_rent_now_url',
        ''
    );

    if (
        tourbi_theme_rental_destination_needs_sync(
            $current_destination
        )
    ) {
        set_theme_mod(
            'tourbi_rent_now_url',
            get_permalink( $page_id )
        );
    }
}
add_action(
    'admin_init',
    'tourbi_theme_ensure_rental_marketplace_page',
    31
);

/**
 * Add Bike Rentals to the Tourbi custom-surface list.
 *
 * @param string[] $slugs Existing slugs.
 * @return string[]
 */
function tourbi_theme_add_rental_marketplace_surface(
    $slugs
) {
    $slugs[] = 'bike-rentals';

    return array_values(
        array_unique( $slugs )
    );
}
add_filter(
    'tourbi_theme_custom_surface_page_slugs',
    'tourbi_theme_add_rental_marketplace_surface'
);

/**
 * Determine whether the current request is Bike Rentals.
 *
 * @return bool
 */
function tourbi_theme_is_rental_marketplace_request() {
    if ( is_admin() ) {
        return false;
    }

    $page_id =
        tourbi_theme_get_rental_marketplace_page_id();

    return (
        $page_id &&
        is_page( $page_id )
    ) ||
        is_page( 'bike-rentals' ) ||
        is_page_template(
            'templates/page-bike-rentals.php'
        );
}

/**
 * Route the Bike Rentals page template.
 *
 * @param array<int,array<string,mixed>> $routes Existing routes.
 * @return array<int,array<string,mixed>>
 */
function tourbi_theme_register_rental_marketplace_route(
    $routes
) {
    array_unshift(
        $routes,
        array(
            'condition' =>
                'tourbi_theme_is_rental_marketplace_request',
            'file'      =>
                'templates/page-bike-rentals.php',
        )
    );

    return $routes;
}
add_filter(
    'tourbi_theme_template_routes',
    'tourbi_theme_register_rental_marketplace_route',
    11
);

/**
 * Redirect the generic shared Rent Item archive to Bike Rentals.
 *
 * This prevents /rent/ from being mistaken for the Experience marketplace.
 *
 * @return void
 */
function tourbi_theme_redirect_shared_rent_archive() {
    if (
        is_post_type_archive( 'rbfw_item' ) &&
        ! is_admin() &&
        ! wp_doing_ajax()
    ) {
        wp_safe_redirect(
            tourbi_theme_get_rental_marketplace_url(),
            301
        );
        exit;
    }
}
add_action(
    'template_redirect',
    'tourbi_theme_redirect_shared_rent_archive',
    4
);

/**
 * Return supported Rental marketplace sort choices.
 *
 * @return array<string,string>
 */
function tourbi_theme_get_rental_marketplace_sort_options() {
    return array(
        'recommended' => __(
            'Recommended',
            'torby'
        ),
        'price_low' => __(
            'Price: Low to High',
            'torby'
        ),
        'price_high' => __(
            'Price: High to Low',
            'torby'
        ),
        'newest' => __(
            'Newest',
            'torby'
        ),
        'title' => __(
            'Name: A to Z',
            'torby'
        ),
    );
}

/**
 * Return the sanitized Rental marketplace state.
 *
 * @return array<string,mixed>
 */
function tourbi_theme_get_rental_marketplace_state() {
    $search = isset( $_GET['rental_search'] )
        ? sanitize_text_field(
            wp_unslash(
                $_GET['rental_search']
            )
        )
        : '';

    $bike_type = isset( $_GET['rental_bike_type'] )
        ? sanitize_key(
            wp_unslash(
                $_GET['rental_bike_type']
            )
        )
        : '';

    $sort = isset( $_GET['rental_sort'] )
        ? sanitize_key(
            wp_unslash(
                $_GET['rental_sort']
            )
        )
        : 'recommended';

    if (
        ! isset(
            tourbi_theme_get_rental_marketplace_sort_options()[
                $sort
            ]
        )
    ) {
        $sort = 'recommended';
    }

    $page = isset( $_GET['rental_page'] )
        ? max(
            1,
            absint(
                wp_unslash(
                    $_GET['rental_page']
                )
            )
        )
        : 1;

    return array(
        'search' => $search,
        'bike_type' => $bike_type,
        'sort' => $sort,
        'page' => $page,
        'has_filters' =>
            '' !== $search ||
            '' !== $bike_type ||
            'recommended' !== $sort,
    );
}

/**
 * Return the normalized normal-Rental mapping.
 *
 * @param int $rental_id Rental ID.
 * @return array<string,mixed>
 */
function tourbi_theme_get_rental_marketplace_mapping(
    $rental_id
) {
    $mapping = function_exists(
        'tourbi_core_get_rental_mapping'
    )
        ? tourbi_core_get_rental_mapping(
            absint( $rental_id )
        )
        : array();

    return is_array( $mapping )
        ? $mapping
        : array();
}

/**
 * Return a readable Bike Pool label.
 *
 * @param string $bike_type Bike type key.
 * @return string
 */
function tourbi_theme_get_rental_bike_type_label(
    $bike_type
) {
    $bike_type = sanitize_key( $bike_type );

    if (
        function_exists(
            'tourbi_core_get_experience_bike_pools'
        )
    ) {
        $pools =
            tourbi_core_get_experience_bike_pools();

        if (
            isset( $pools[ $bike_type ] ) &&
            ! empty(
                $pools[ $bike_type ]->display_name
            )
        ) {
            return sanitize_text_field(
                $pools[
                    $bike_type
                ]->display_name
            );
        }
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
 * Convert one normal Rental into a card model.
 *
 * @param int $rental_id Rental ID.
 * @return array<string,mixed>
 */
function tourbi_theme_get_rental_marketplace_card(
    $rental_id
) {
    $rental_id = absint( $rental_id );

    $rental = function_exists(
        'tourbi_theme_get_single_rental_view_model'
    )
        ? tourbi_theme_get_single_rental_view_model(
            $rental_id
        )
        : array();

    $mapping =
        tourbi_theme_get_rental_marketplace_mapping(
            $rental_id
        );

    $bike_type = sanitize_key(
        $mapping['bike_type'] ?? ''
    );

    $gallery = $rental['gallery'] ?? array();
    $image = $gallery[0] ?? array();

    return array_merge(
        $rental,
        array(
            'url' => get_permalink( $rental_id ),
            'image' =>
                $image['large'] ??
                $image['full'] ??
                (string) get_the_post_thumbnail_url(
                    $rental_id,
                    'large'
                ),
            'bike_type' => $bike_type,
            'bike_label' =>
                tourbi_theme_get_rental_bike_type_label(
                    $bike_type
                ),
            'units_per_booking' => max(
                1,
                absint(
                    $mapping[
                        'units_per_booking'
                    ] ?? 1
                )
            ),
        )
    );
}

/**
 * Return all published mapped normal Rentals as card models.
 *
 * @param array<string,mixed> $state Filter state.
 * @return array<int,array<string,mixed>>
 */
function tourbi_theme_get_filtered_rental_cards(
    $state
) {
    $query_args = array(
        'post_type' => 'rbfw_item',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'no_found_rows' => true,
        'ignore_sticky_posts' => true,
        'orderby' => array(
            'menu_order' => 'ASC',
            'modified' => 'DESC',
            'date' => 'DESC',
        ),
        'meta_query' => array(
            array(
                'key' =>
                    '_tourbi_inventory_enabled',
                'value' => 'yes',
                'compare' => '=',
            ),
        ),
    );

    if ( ! empty( $state['search'] ) ) {
        $query_args['s'] =
            sanitize_text_field(
                $state['search']
            );
    }

    $query = new WP_Query( $query_args );
    $cards = array();

    foreach ( (array) $query->posts as $rental_id ) {
        if (
            ! tourbi_theme_item_is_rental_mapped(
                $rental_id
            )
        ) {
            continue;
        }

        $card =
            tourbi_theme_get_rental_marketplace_card(
                $rental_id
            );

        if ( empty( $card['id'] ) ) {
            continue;
        }

        if (
            ! empty( $state['bike_type'] ) &&
            $state['bike_type'] !==
                $card['bike_type']
        ) {
            continue;
        }

        $cards[] = $card;
    }

    $sort = sanitize_key(
        $state['sort'] ?? 'recommended'
    );

    if ( 'price_low' === $sort ) {
        usort(
            $cards,
            static function ( $a, $b ) {
                return (float) ( $a['price'] ?? 0 ) <=>
                    (float) ( $b['price'] ?? 0 );
            }
        );
    } elseif ( 'price_high' === $sort ) {
        usort(
            $cards,
            static function ( $a, $b ) {
                return (float) ( $b['price'] ?? 0 ) <=>
                    (float) ( $a['price'] ?? 0 );
            }
        );
    } elseif ( 'newest' === $sort ) {
        usort(
            $cards,
            static function ( $a, $b ) {
                return get_post_timestamp(
                    $b['id']
                ) <=> get_post_timestamp(
                    $a['id']
                );
            }
        );
    } elseif ( 'title' === $sort ) {
        usort(
            $cards,
            static function ( $a, $b ) {
                return strcasecmp(
                    (string) ( $a['title'] ?? '' ),
                    (string) ( $b['title'] ?? '' )
                );
            }
        );
    }

    return $cards;
}

/**
 * Return the available Bike Pool options.
 *
 * @param array<int,array<string,mixed>> $cards Rental cards.
 * @return array<string,string>
 */
function tourbi_theme_get_rental_marketplace_bike_types(
    $cards
) {
    $types = array();

    foreach ( $cards as $card ) {
        $key = sanitize_key(
            $card['bike_type'] ?? ''
        );

        if ( '' === $key ) {
            continue;
        }

        $types[ $key ] = sanitize_text_field(
            $card['bike_label'] ?? $key
        );
    }

    asort(
        $types,
        SORT_NATURAL |
        SORT_FLAG_CASE
    );

    return $types;
}

/**
 * Paginate an in-memory card collection.
 *
 * @param array<int,array<string,mixed>> $cards All cards.
 * @param int                            $page Current page.
 * @return array<string,mixed>
 */
function tourbi_theme_paginate_rental_cards(
    $cards,
    $page
) {
    $per_page = 9;
    $total = count( $cards );
    $pages = max(
        1,
        (int) ceil(
            $total / $per_page
        )
    );
    $page = min(
        max( 1, absint( $page ) ),
        $pages
    );

    return array(
        'items' => array_slice(
            $cards,
            ( $page - 1 ) * $per_page,
            $per_page
        ),
        'total' => $total,
        'pages' => $pages,
        'page' => $page,
    );
}

/**
 * Return URL arguments that preserve Rental filters.
 *
 * @param array<string,mixed> $state Filter state.
 * @return array<string,string>
 */
function tourbi_theme_get_rental_marketplace_url_args(
    $state
) {
    $args = array();

    if ( ! empty( $state['search'] ) ) {
        $args['rental_search'] =
            sanitize_text_field(
                $state['search']
            );
    }

    if ( ! empty( $state['bike_type'] ) ) {
        $args['rental_bike_type'] =
            sanitize_key(
                $state['bike_type']
            );
    }

    if (
        ! empty( $state['sort'] ) &&
        'recommended' !== $state['sort']
    ) {
        $args['rental_sort'] =
            sanitize_key(
                $state['sort']
            );
    }

    return $args;
}

/**
 * Render Rental marketplace pagination.
 *
 * @param array<string,mixed> $pagination Pagination model.
 * @param array<string,mixed> $state Filter state.
 * @return void
 */
function tourbi_theme_render_rental_marketplace_pagination(
    $pagination,
    $state
) {
    if ( absint( $pagination['pages'] ?? 1 ) < 2 ) {
        return;
    }

    $base_url =
        tourbi_theme_get_rental_marketplace_url();

    $links = paginate_links(
        array(
            'base' => add_query_arg(
                array_merge(
                    tourbi_theme_get_rental_marketplace_url_args(
                        $state
                    ),
                    array(
                        'rental_page' => '%#%',
                    )
                ),
                $base_url
            ),
            'format' => '',
            'current' => absint(
                $pagination['page']
            ),
            'total' => absint(
                $pagination['pages']
            ),
            'mid_size' => 1,
            'end_size' => 1,
            'prev_text' => '← ' .
                __( 'Previous', 'torby' ),
            'next_text' =>
                __( 'Next', 'torby' ) .
                ' →',
            'type' => 'list',
        )
    );

    if ( ! $links ) {
        return;
    }
    ?>
    <nav
        class="tourbi-rental-marketplace-pagination"
        aria-label="<?php esc_attr_e( 'Bike Rental results pagination', 'torby' ); ?>"
    >
        <?php echo wp_kses_post( $links ); ?>
    </nav>
    <?php
}

/**
 * Enqueue the dedicated Rental marketplace assets.
 *
 * @return void
 */
function tourbi_theme_enqueue_rental_marketplace_assets() {
    if ( ! tourbi_theme_is_rental_marketplace_request() ) {
        return;
    }

    $css = 'assets/css/rental-marketplace.css';
    $js = 'assets/js/rental-marketplace.js';

    if (
        file_exists(
            trailingslashit(
                get_stylesheet_directory()
            ) . $css
        )
    ) {
        wp_enqueue_style(
            'tourbi-rental-marketplace',
            trailingslashit(
                get_stylesheet_directory_uri()
            ) . $css,
            wp_style_is(
                'tourbi-layout',
                'registered'
            )
                ? array( 'tourbi-layout' )
                : array(),
            tourbi_theme_foundation_asset_version(
                $css
            )
        );
    }

    if (
        file_exists(
            trailingslashit(
                get_stylesheet_directory()
            ) . $js
        )
    ) {
        wp_enqueue_script(
            'tourbi-rental-marketplace',
            trailingslashit(
                get_stylesheet_directory_uri()
            ) . $js,
            array(),
            tourbi_theme_foundation_asset_version(
                $js
            ),
            true
        );
    }
}
add_action(
    'wp_enqueue_scripts',
    'tourbi_theme_enqueue_rental_marketplace_assets',
    90
);
