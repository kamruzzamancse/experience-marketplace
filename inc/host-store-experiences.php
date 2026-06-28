<?php
/**
 * Tourbi public Host storefront Experiences integration.
 *
 * WCFM continues to provide the public store profile, About, Policies,
 * Reviews, Inquiry, and vendor identity. The default WCFM "Products" tab is
 * repurposed as "Experiences" and its template is overridden from the child
 * theme at wcfm/store/wcfmmp-view-store-products.php.
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Determine whether the current request is a public WCFM store page.
 *
 * @return bool
 */
function tourbi_theme_is_public_host_store() {
    return ! is_admin() &&
        function_exists( 'wcfm_is_store_page' ) &&
        wcfm_is_store_page();
}

/**
 * Return the vendor ID represented by the current WCFM store URL.
 *
 * @return int
 */
function tourbi_theme_get_current_store_vendor_id() {
    if ( ! tourbi_theme_is_public_host_store() ) {
        return 0;
    }

    $store_url_key = function_exists( 'wcfm_get_option' )
        ? (string) wcfm_get_option( 'wcfm_store_url', 'store' )
        : 'store';

    $store_slug = sanitize_title(
        (string) get_query_var( $store_url_key )
    );

    if ( '' === $store_slug ) {
        return 0;
    }

    $user = get_user_by( 'slug', $store_slug );

    return $user instanceof WP_User
        ? absint( $user->ID )
        : 0;
}

/**
 * Make WCFM's default Products tab customer-facing as Experiences.
 *
 * The internal key remains "products" so the native WCFM store URL, active
 * tab handling, About, Policies, and Reviews continue to work without adding
 * new rewrite rules or requiring a permalink flush.
 *
 * @param array<string,string> $tabs     Existing WCFM store tabs.
 * @param int                  $store_id Vendor ID.
 * @return array<string,string>
 */
function tourbi_theme_rename_store_products_tab( $tabs, $store_id ) {
    unset( $store_id );

    if ( isset( $tabs['products'] ) ) {
        $tabs['products'] = __( 'Experiences', 'torby' );
    }

    return $tabs;
}
add_filter(
    'wcfmmp_store_tabs',
    'tourbi_theme_rename_store_products_tab',
    90,
    2
);

/**
 * Determine whether the active WCFM tab is the default Experiences tab.
 *
 * @return bool
 */
function tourbi_theme_is_host_store_experiences_tab() {
    if ( ! tourbi_theme_is_public_host_store() ) {
        return false;
    }

    $non_experience_tabs = array(
        'about',
        'policies',
        'reviews',
        'followers',
        'followings',
        'articles',
    );

    foreach ( $non_experience_tabs as $tab ) {
        if ( get_query_var( $tab ) ) {
            return false;
        }
    }

    $request_path = wp_parse_url(
        wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ),
        PHP_URL_PATH
    );

    $request_path = '/' . trim( (string) $request_path, '/' ) . '/';

    foreach ( $non_experience_tabs as $tab ) {
        if ( false !== strpos( $request_path, '/' . $tab . '/' ) ) {
            return false;
        }
    }

    return true;
}

/**
 * Add Host store presentation classes.
 *
 * @param string[] $classes Existing body classes.
 * @return string[]
 */
function tourbi_theme_host_store_experience_body_classes( $classes ) {
    if ( tourbi_theme_is_public_host_store() ) {
        $classes[] = 'tourbi-host-store-profile';
    }

    if ( tourbi_theme_is_host_store_experiences_tab() ) {
        $classes[] = 'tourbi-host-store-experiences-tab';
    }

    return array_values( array_unique( $classes ) );
}
add_filter(
    'body_class',
    'tourbi_theme_host_store_experience_body_classes',
    95
);

/**
 * Treat the public Host store as a Tourbi custom design surface.
 *
 * This loads the shared Tourbi design tokens/layout before the store-specific
 * Experience card stylesheet is enqueued.
 *
 * @param bool $is_surface Current result.
 * @return bool
 */
function tourbi_theme_host_store_is_custom_surface( $is_surface ) {
    return $is_surface || tourbi_theme_is_public_host_store();
}
add_filter(
    'tourbi_theme_is_custom_surface',
    'tourbi_theme_host_store_is_custom_surface',
    30
);

/**
 * Enqueue the existing Tourbi Experience-card design and store integration.
 *
 * @return void
 */
function tourbi_theme_enqueue_host_store_experience_assets() {
    if ( ! tourbi_theme_is_public_host_store() ) {
        return;
    }

    $theme_dir = trailingslashit( get_stylesheet_directory() );
    $theme_uri = trailingslashit( get_stylesheet_directory_uri() );

    $marketplace_path = 'assets/css/experience-marketplace.css';

    if ( file_exists( $theme_dir . $marketplace_path ) ) {
        wp_enqueue_style(
            'tourbi-experience-marketplace',
            $theme_uri . $marketplace_path,
            wp_style_is( 'tourbi-layout', 'registered' )
                ? array( 'tourbi-layout' )
                : array(),
            function_exists( 'tourbi_theme_foundation_asset_version' )
                ? tourbi_theme_foundation_asset_version( $marketplace_path )
                : (string) filemtime( $theme_dir . $marketplace_path )
        );
    }

    $store_path = 'assets/css/host-store-experiences.css';

    if ( file_exists( $theme_dir . $store_path ) ) {
        wp_enqueue_style(
            'tourbi-host-store-experiences',
            $theme_uri . $store_path,
            wp_style_is( 'tourbi-experience-marketplace', 'registered' )
                ? array( 'tourbi-experience-marketplace' )
                : array(),
            (string) filemtime( $theme_dir . $store_path )
        );
    }
}
add_action(
    'wp_enqueue_scripts',
    'tourbi_theme_enqueue_host_store_experience_assets',
    75
);

/**
 * Return the published guided Experiences owned by one Host.
 *
 * @param int $vendor_id Host/vendor user ID.
 * @param int $paged     Current page.
 * @param int $per_page  Experiences per page.
 * @return WP_Query
 */
function tourbi_theme_get_host_store_experience_query(
    $vendor_id,
    $paged = 1,
    $per_page = 6
) {
    $vendor_id = absint( $vendor_id );

    if ( ! $vendor_id ) {
        return new WP_Query(
            array(
                'post_type'      => 'rbfw_item',
                'post__in'       => array( 0 ),
                'posts_per_page' => 1,
            )
        );
    }

    $args = array(
        'post_type'              => 'rbfw_item',
        'post_status'            => 'publish',
        'author'                 => $vendor_id,
        'posts_per_page'         => max( 1, absint( $per_page ) ),
        'paged'                  => max( 1, absint( $paged ) ),
        'ignore_sticky_posts'    => true,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => true,
        'orderby'                => array(
            'menu_order' => 'ASC',
            'modified'   => 'DESC',
            'date'       => 'DESC',
        ),
        'meta_query'             => array(
            'relation' => 'AND',
            array(
                'key'     => '_tourbi_experience_enabled',
                'value'   => 'yes',
                'compare' => '=',
            ),
            array(
                'relation' => 'OR',
                array(
                    'key'     => '_tourbi_inventory_enabled',
                    'compare' => 'NOT EXISTS',
                ),
                array(
                    'key'     => '_tourbi_inventory_enabled',
                    'value'   => array(
                        'yes',
                        '1',
                        'true',
                        'on',
                        'enabled',
                    ),
                    'compare' => 'NOT IN',
                ),
            ),
        ),
    );

    /**
     * Filter the public Host-store Experience query.
     *
     * @param array<string,mixed> $args      Query arguments.
     * @param int                 $vendor_id Host ID.
     */
    $args = (array) apply_filters(
        'tourbi_theme_host_store_experience_query_args',
        $args,
        $vendor_id
    );

    return new WP_Query( $args );
}

/**
 * Render pagination for a Host's public Experience listing.
 *
 * @param WP_Query $query     Experience query.
 * @param int      $vendor_id Host ID.
 * @return void
 */
function tourbi_theme_render_host_store_experience_pagination(
    $query,
    $vendor_id
) {
    if (
        ! $query instanceof WP_Query ||
        $query->max_num_pages < 2 ||
        ! function_exists( 'wcfmmp_get_store_url' )
    ) {
        return;
    }

    $current = max( 1, absint( get_query_var( 'paged' ) ) );
    $store_url = wcfmmp_get_store_url( absint( $vendor_id ) );

    $links = paginate_links(
        array(
            'base'      => add_query_arg(
                'paged',
                '%#%',
                $store_url
            ) . '#tab_links_area',
            'format'    => '',
            'current'   => $current,
            'total'     => absint( $query->max_num_pages ),
            'mid_size'  => 1,
            'end_size'  => 1,
            'prev_text' => '← ' . __( 'Previous', 'torby' ),
            'next_text' => __( 'Next', 'torby' ) . ' →',
            'type'      => 'list',
        )
    );

    if ( ! $links ) {
        return;
    }
    ?>
    <nav
        class="tourbi-host-store-pagination"
        aria-label="<?php esc_attr_e( 'Host Experiences pagination', 'torby' ); ?>"
    >
        <?php echo wp_kses_post( $links ); ?>
    </nav>
    <?php
}

/**
 * Return whether a user is a WCFM Host/vendor.
 *
 * @param int $user_id User ID.
 * @return bool
 */
function tourbi_theme_user_is_store_host( $user_id ) {
    $user = get_user_by( 'id', absint( $user_id ) );

    if ( ! $user instanceof WP_User ) {
        return false;
    }

    return (bool) array_intersect(
        array( 'wcfm_vendor', 'vendor' ),
        (array) $user->roles
    );
}

/**
 * Redirect obsolete vendor WooCommerce product pages to the Host profile.
 *
 * Tourbi Hosts publish guided Experiences through the custom rbfw_item
 * workflow. Generic vendor WooCommerce products are not part of the current
 * project scope. Administrators and users who can edit the product may still
 * open the product page for diagnostics. A product can be explicitly kept
 * public by saving _tourbi_keep_public_product = yes.
 *
 * @return void
 */
function tourbi_theme_redirect_legacy_vendor_product() {
    if (
        is_admin() ||
        wp_doing_ajax() ||
        ! is_singular( 'product' )
    ) {
        return;
    }

    $product_id = get_queried_object_id();
    $vendor_id = absint(
        get_post_field( 'post_author', $product_id )
    );

    if (
        ! $product_id ||
        ! $vendor_id ||
        ! tourbi_theme_user_is_store_host( $vendor_id )
    ) {
        return;
    }

    if (
        current_user_can( 'edit_post', $product_id ) ||
        tourbi_theme_meta_flag_is_enabled(
            get_post_meta(
                $product_id,
                '_tourbi_keep_public_product',
                true
            )
        )
    ) {
        return;
    }

    $should_redirect = (bool) apply_filters(
        'tourbi_theme_redirect_vendor_product_to_store',
        true,
        $product_id,
        $vendor_id
    );

    if (
        ! $should_redirect ||
        ! function_exists( 'wcfmmp_get_store_url' )
    ) {
        return;
    }

    $destination = wcfmmp_get_store_url( $vendor_id );

    if ( $destination ) {
        wp_safe_redirect( $destination, 302, 'Tourbi' );
        exit;
    }
}
add_action(
    'template_redirect',
    'tourbi_theme_redirect_legacy_vendor_product',
    35
);
