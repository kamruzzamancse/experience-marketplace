<?php
/**
 * Experience Marketplace query, filters, and dedicated page setup.
 *
 * The marketplace includes only published Rent Items with an enabled Tourbi
 * Experience Mapping. Normal bike-rental items are intentionally excluded.
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Return the dedicated Experience Marketplace page ID.
 *
 * @return int
 */
function tourbi_theme_get_marketplace_page_id() {
    $stored_id = absint(
        get_option(
            'tourbi_theme_marketplace_page_id',
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
        'experiences',
        OBJECT,
        'page'
    );

    if ( $page instanceof WP_Post ) {
        update_option(
            'tourbi_theme_marketplace_page_id',
            $page->ID,
            false
        );

        return absint( $page->ID );
    }

    return 0;
}

/**
 * Create or configure the Experiences page once.
 *
 * The operation is restricted to administrators/editors and never runs during
 * frontend requests.
 *
 * @return void
 */
function tourbi_theme_ensure_marketplace_page() {
    if (
        ! current_user_can( 'edit_pages' ) ||
        wp_doing_ajax()
    ) {
        return;
    }

    $page_id = tourbi_theme_get_marketplace_page_id();

    if ( ! $page_id ) {
        $page_id = wp_insert_post(
            array(
                'post_type'    => 'page',
                'post_status'  => 'publish',
                'post_title'   => __(
                    'Experiences',
                    'torby'
                ),
                'post_name'    => 'experiences',
                'post_content' => '',
            ),
            true
        );

        if ( is_wp_error( $page_id ) ) {
            return;
        }

        update_option(
            'tourbi_theme_marketplace_page_id',
            absint( $page_id ),
            false
        );
    }

    $template = get_post_meta(
        $page_id,
        '_wp_page_template',
        true
    );

    if (
        'templates/archive-tourbi-experiences.php' !==
        $template
    ) {
        update_post_meta(
            $page_id,
            '_wp_page_template',
            'templates/archive-tourbi-experiences.php'
        );
    }
}
add_action(
    'admin_init',
    'tourbi_theme_ensure_marketplace_page',
    30
);

/**
 * Determine whether the current request is the Tourbi Marketplace.
 *
 * @return bool
 */
function tourbi_theme_is_marketplace_request() {
    if ( is_admin() ) {
        return false;
    }

    $page_id = tourbi_theme_get_marketplace_page_id();

    if (
        $page_id &&
        is_page( $page_id )
    ) {
        return true;
    }

    if (
        is_page_template(
            'templates/archive-tourbi-experiences.php'
        ) ||
        is_page( 'experiences' )
    ) {
        return true;
    }

    return is_tax(
        array(
            defined(
                'TOURBI_CORE_EXPERIENCE_CATEGORY_TAXONOMY'
            )
                ? TOURBI_CORE_EXPERIENCE_CATEGORY_TAXONOMY
                : 'tourbi_experience_category',
            defined(
                'TOURBI_CORE_EXPERIENCE_TAG_TAXONOMY'
            )
                ? TOURBI_CORE_EXPERIENCE_TAG_TAXONOMY
                : 'tourbi_experience_tag',
        )
    );
}

/**
 * Return permitted marketplace sort modes.
 *
 * @return array<string,string>
 */
function tourbi_theme_get_marketplace_sort_options() {
    return array(
        'recommended' => __(
            'Recommended',
            'torby'
        ),
        'newest' => __(
            'Newest',
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
        'duration_short' => __(
            'Shortest Duration',
            'torby'
        ),
    );
}

/**
 * Return the current sanitized marketplace filter state.
 *
 * @return array<string,mixed>
 */
function tourbi_theme_get_marketplace_state() {
    $category_taxonomy = defined(
        'TOURBI_CORE_EXPERIENCE_CATEGORY_TAXONOMY'
    )
        ? TOURBI_CORE_EXPERIENCE_CATEGORY_TAXONOMY
        : 'tourbi_experience_category';

    $tag_taxonomy = defined(
        'TOURBI_CORE_EXPERIENCE_TAG_TAXONOMY'
    )
        ? TOURBI_CORE_EXPERIENCE_TAG_TAXONOMY
        : 'tourbi_experience_tag';

    $search = isset( $_GET['experience_search'] )
        ? sanitize_text_field(
            wp_unslash(
                $_GET['experience_search']
            )
        )
        : '';

    $category = isset(
        $_GET['experience_category']
    )
        ? sanitize_title(
            wp_unslash(
                $_GET['experience_category']
            )
        )
        : '';

    $tag = isset( $_GET['experience_tag'] )
        ? sanitize_title(
            wp_unslash(
                $_GET['experience_tag']
            )
        )
        : '';

    if (
        is_tax( $category_taxonomy ) &&
        '' === $category
    ) {
        $term = get_queried_object();

        if ( $term instanceof WP_Term ) {
            $category = sanitize_title(
                $term->slug
            );
        }
    }

    if (
        is_tax( $tag_taxonomy ) &&
        '' === $tag
    ) {
        $term = get_queried_object();

        if ( $term instanceof WP_Term ) {
            $tag = sanitize_title(
                $term->slug
            );
        }
    }

    $location = isset(
        $_GET['experience_location']
    )
        ? sanitize_text_field(
            wp_unslash(
                $_GET['experience_location']
            )
        )
        : '';

    $sort = isset( $_GET['experience_sort'] )
        ? sanitize_key(
            wp_unslash(
                $_GET['experience_sort']
            )
        )
        : 'recommended';

    if (
        ! array_key_exists(
            $sort,
            tourbi_theme_get_marketplace_sort_options()
        )
    ) {
        $sort = 'recommended';
    }

    $page = isset( $_GET['experience_page'] )
        ? max(
            1,
            absint(
                wp_unslash(
                    $_GET['experience_page']
                )
            )
        )
        : 1;

    return array(
        'search'      => $search,
        'category'    => $category,
        'tag'         => $tag,
        'location'    => $location,
        'sort'        => $sort,
        'page'        => $page,
        'has_filters' =>
            '' !== $search ||
            '' !== $category ||
            '' !== $tag ||
            '' !== $location ||
            'recommended' !== $sort,
    );
}

/**
 * Return published marketplace locations.
 *
 * @return string[]
 */
function tourbi_theme_get_marketplace_locations() {
    global $wpdb;

    $cache_key =
        'tourbi_marketplace_locations_v1';

    $cached = get_transient( $cache_key );

    if ( is_array( $cached ) ) {
        return $cached;
    }

    $city_key = '_tourbi_experience_city';
    $enabled_key =
        '_tourbi_experience_enabled';

    $locations = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT DISTINCT city.meta_value
            FROM {$wpdb->postmeta} AS city
            INNER JOIN {$wpdb->posts} AS posts
                ON posts.ID = city.post_id
            INNER JOIN {$wpdb->postmeta} AS enabled
                ON enabled.post_id = posts.ID
            WHERE posts.post_type = %s
              AND posts.post_status = %s
              AND city.meta_key = %s
              AND city.meta_value <> ''
              AND enabled.meta_key = %s
              AND enabled.meta_value = %s
            ORDER BY city.meta_value ASC",
            'rbfw_item',
            'publish',
            $city_key,
            $enabled_key,
            'yes'
        )
    );

    $locations = array_values(
        array_unique(
            array_filter(
                array_map(
                    'sanitize_text_field',
                    (array) $locations
                )
            )
        )
    );

    set_transient(
        $cache_key,
        $locations,
        HOUR_IN_SECONDS
    );

    return $locations;
}

/**
 * Clear cached marketplace locations when an Experience is saved.
 *
 * @param int $post_id Saved Rent Item ID.
 * @return void
 */
function tourbi_theme_clear_marketplace_location_cache(
    $post_id
) {
    if (
        'rbfw_item' !== get_post_type(
            absint( $post_id )
        )
    ) {
        return;
    }

    delete_transient(
        'tourbi_marketplace_locations_v1'
    );
}
add_action(
    'save_post_rbfw_item',
    'tourbi_theme_clear_marketplace_location_cache',
    100
);

/**
 * Return common Experience-only query clauses.
 *
 * @return array<string,mixed>
 */
function tourbi_theme_get_marketplace_base_query_args() {
    return array(
        'post_type'              => 'rbfw_item',
        'post_status'            => 'publish',
        'ignore_sticky_posts'    => true,
        'no_found_rows'          => false,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => true,
        'meta_query'             => array(
            array(
                'key'     =>
                    '_tourbi_experience_enabled',
                'value'   => 'yes',
                'compare' => '=',
            ),
        ),
    );
}

/**
 * Return a featured Experience for the unfiltered marketplace hero.
 *
 * @param array<string,mixed> $state Marketplace state.
 * @return array<string,mixed>
 */
function tourbi_theme_get_marketplace_featured_experience(
    $state
) {
    if (
        ! empty( $state['has_filters'] ) ||
        1 !== absint( $state['page'] ?? 1 )
    ) {
        return array();
    }

    $base_args =
        tourbi_theme_get_marketplace_base_query_args();

    $featured_args = $base_args;
    $featured_args['posts_per_page'] = 1;
    $featured_args['fields'] = 'ids';
    $featured_args['no_found_rows'] = true;
    $featured_args['orderby'] = array(
        'modified' => 'DESC',
        'date'     => 'DESC',
    );
    $featured_args['meta_query'][] = array(
        'key'     =>
            '_tourbi_experience_featured',
        'value'   => '1',
        'compare' => '=',
    );

    $featured_query = new WP_Query(
        $featured_args
    );

    $featured_id = ! empty(
        $featured_query->posts[0]
    )
        ? absint( $featured_query->posts[0] )
        : 0;

    if ( ! $featured_id ) {
        $fallback_args = $base_args;
        $fallback_args['posts_per_page'] = 1;
        $fallback_args['fields'] = 'ids';
        $fallback_args['no_found_rows'] = true;
        $fallback_args['orderby'] = array(
            'modified' => 'DESC',
            'date'     => 'DESC',
        );

        $fallback_query = new WP_Query(
            $fallback_args
        );

        $featured_id = ! empty(
            $fallback_query->posts[0]
        )
            ? absint(
                $fallback_query->posts[0]
            )
            : 0;
    }

    return (
        $featured_id &&
        function_exists(
            'tourbi_theme_get_single_experience_view_model'
        )
    )
        ? tourbi_theme_get_single_experience_view_model(
            $featured_id
        )
        : array();
}

/**
 * Build the filtered marketplace query.
 *
 * @param array<string,mixed> $state Filter state.
 * @param int                 $exclude_id Optional featured Experience ID.
 * @return WP_Query
 */
function tourbi_theme_get_marketplace_query(
    $state,
    $exclude_id = 0
) {
    $args =
        tourbi_theme_get_marketplace_base_query_args();

    $args['posts_per_page'] = 8;
    $args['paged'] = max(
        1,
        absint( $state['page'] ?? 1 )
    );

    if ( $exclude_id ) {
        $args['post__not_in'] = array(
            absint( $exclude_id ),
        );
    }

    if ( ! empty( $state['search'] ) ) {
        $args['s'] = sanitize_text_field(
            $state['search']
        );
    }

    if ( ! empty( $state['location'] ) ) {
        $args['meta_query'][] = array(
            'relation' => 'OR',
            array(
                'key'     =>
                    '_tourbi_experience_city',
                'value'   =>
                    sanitize_text_field(
                        $state['location']
                    ),
                'compare' => '=',
            ),
            array(
                'key'     =>
                    '_tourbi_experience_meeting_address',
                'value'   =>
                    sanitize_text_field(
                        $state['location']
                    ),
                'compare' => 'LIKE',
            ),
        );
    }

    $tax_query = array();

    if ( ! empty( $state['category'] ) ) {
        $tax_query[] = array(
            'taxonomy' => defined(
                'TOURBI_CORE_EXPERIENCE_CATEGORY_TAXONOMY'
            )
                ? TOURBI_CORE_EXPERIENCE_CATEGORY_TAXONOMY
                : 'tourbi_experience_category',
            'field'    => 'slug',
            'terms'    => sanitize_title(
                $state['category']
            ),
        );
    }

    if ( ! empty( $state['tag'] ) ) {
        $tax_query[] = array(
            'taxonomy' => defined(
                'TOURBI_CORE_EXPERIENCE_TAG_TAXONOMY'
            )
                ? TOURBI_CORE_EXPERIENCE_TAG_TAXONOMY
                : 'tourbi_experience_tag',
            'field'    => 'slug',
            'terms'    => sanitize_title(
                $state['tag']
            ),
        );
    }

    if ( ! empty( $tax_query ) ) {
        if ( count( $tax_query ) > 1 ) {
            $tax_query['relation'] = 'AND';
        }

        $args['tax_query'] = $tax_query;
    }

    $sort = sanitize_key(
        $state['sort'] ?? 'recommended'
    );

    if ( 'price_low' === $sort ) {
        $args['meta_key'] =
            '_tourbi_experience_price_per_participant';
        $args['orderby'] = 'meta_value_num';
        $args['order'] = 'ASC';
    } elseif ( 'price_high' === $sort ) {
        $args['meta_key'] =
            '_tourbi_experience_price_per_participant';
        $args['orderby'] = 'meta_value_num';
        $args['order'] = 'DESC';
    } elseif ( 'duration_short' === $sort ) {
        $args['meta_key'] =
            '_tourbi_experience_duration_minutes';
        $args['orderby'] = 'meta_value_num';
        $args['order'] = 'ASC';
    } elseif ( 'newest' === $sort ) {
        $args['orderby'] = 'date';
        $args['order'] = 'DESC';
    } else {
        $args['orderby'] = array(
            'menu_order' => 'ASC',
            'modified'   => 'DESC',
            'date'       => 'DESC',
        );
    }

    /**
     * Filter the server-side Experience Marketplace query.
     *
     * @param array<string,mixed> $args Query arguments.
     * @param array<string,mixed> $state Filter state.
     */
    $args = (array) apply_filters(
        'tourbi_theme_marketplace_query_args',
        $args,
        $state
    );

    return new WP_Query( $args );
}

/**
 * Convert a marketplace query post into the card view model.
 *
 * @param int $experience_id Experience ID.
 * @return array<string,mixed>
 */
function tourbi_theme_get_marketplace_card(
    $experience_id
) {
    $experience = function_exists(
        'tourbi_theme_get_single_experience_view_model'
    )
        ? tourbi_theme_get_single_experience_view_model(
            $experience_id
        )
        : array();

    $image = $experience['gallery'][0] ?? array();

    $experience['card_image'] =
        $image['large'] ?? '';

    return $experience;
}

/**
 * Return URL arguments that preserve active marketplace filters.
 *
 * @param array<string,mixed> $state Filter state.
 * @return array<string,string|int>
 */
function tourbi_theme_get_marketplace_url_args(
    $state
) {
    $arguments = array();

    $mapping = array(
        'search'   => 'experience_search',
        'category' => 'experience_category',
        'tag'      => 'experience_tag',
        'location' => 'experience_location',
        'sort'     => 'experience_sort',
    );

    foreach ( $mapping as $state_key => $url_key ) {
        $value = $state[ $state_key ] ?? '';

        if (
            '' === (string) $value ||
            (
                'sort' === $state_key &&
                'recommended' === $value
            )
        ) {
            continue;
        }

        $arguments[ $url_key ] =
            sanitize_text_field(
                (string) $value
            );
    }

    return $arguments;
}

/**
 * Render marketplace pagination.
 *
 * @param WP_Query            $query Marketplace query.
 * @param array<string,mixed> $state Filter state.
 * @return void
 */
function tourbi_theme_render_marketplace_pagination(
    $query,
    $state
) {
    if (
        ! $query instanceof WP_Query ||
        $query->max_num_pages < 2
    ) {
        return;
    }

    $current = max(
        1,
        absint( $state['page'] ?? 1 )
    );

    $base_url =
        tourbi_theme_get_experience_archive_url();

    $filter_args =
        tourbi_theme_get_marketplace_url_args(
            $state
        );

    $links = paginate_links(
        array(
            'base'      => add_query_arg(
                array_merge(
                    $filter_args,
                    array(
                        'experience_page' => '%#%',
                    )
                ),
                $base_url
            ),
            'format'    => '',
            'current'   => $current,
            'total'     => absint(
                $query->max_num_pages
            ),
            'mid_size'  => 1,
            'end_size'  => 1,
            'prev_text' => '← ' .
                __(
                    'Previous',
                    'torby'
                ),
            'next_text' =>
                __(
                    'Next',
                    'torby'
                ) .
                ' →',
            'type'      => 'list',
        )
    );

    if ( ! $links ) {
        return;
    }
    ?>
    <nav
        class="tourbi-marketplace-pagination"
        aria-label="<?php esc_attr_e( 'Experience results pagination', 'torby' ); ?>"
    >
        <?php echo wp_kses_post( $links ); ?>
    </nav>
    <?php
}
