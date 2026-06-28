<?php
/**
 * Become a Host page setup and view-model helpers.
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Return the configured Become a Host page ID.
 *
 * @return int
 */
function tourbi_theme_get_become_host_page_id() {
    $stored_id = absint(
        get_option(
            'tourbi_theme_become_host_page_id',
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
        'become-a-host',
        OBJECT,
        'page'
    );

    if ( $page instanceof WP_Post ) {
        update_option(
            'tourbi_theme_become_host_page_id',
            $page->ID,
            false
        );

        return absint( $page->ID );
    }

    return 0;
}

/**
 * Create or configure the Become a Host page.
 *
 * Existing page content is never overwritten.
 *
 * @return void
 */
function tourbi_theme_ensure_become_host_page() {
    if (
        ! current_user_can( 'edit_pages' ) ||
        wp_doing_ajax()
    ) {
        return;
    }

    $page_id =
        tourbi_theme_get_become_host_page_id();

    if ( ! $page_id ) {
        $page_id = wp_insert_post(
            array(
                'post_type'    => 'page',
                'post_status'  => 'publish',
                'post_title'   => __(
                    'Become a Host',
                    'torby'
                ),
                'post_name'    => 'become-a-host',
                'post_content' => '',
            ),
            true
        );

        if ( is_wp_error( $page_id ) ) {
            return;
        }

        update_option(
            'tourbi_theme_become_host_page_id',
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
        'templates/page-become-a-host.php' !==
        $template
    ) {
        update_post_meta(
            $page_id,
            '_wp_page_template',
            'templates/page-become-a-host.php'
        );
    }
}
add_action(
    'admin_init',
    'tourbi_theme_ensure_become_host_page',
    35
);

/**
 * Determine whether the current request is Become a Host.
 *
 * @return bool
 */
function tourbi_theme_is_become_host_request() {
    if ( is_admin() ) {
        return false;
    }

    $page_id =
        tourbi_theme_get_become_host_page_id();

    if ( $page_id && is_page( $page_id ) ) {
        return true;
    }

    return is_page( 'become-a-host' ) ||
        is_page_template(
            'templates/page-become-a-host.php'
        );
}

/**
 * Determine whether the current user is a marketplace Host.
 *
 * @return bool
 */
function tourbi_theme_current_user_is_host() {
    if ( ! is_user_logged_in() ) {
        return false;
    }

    $user = wp_get_current_user();
    $roles = $user instanceof WP_User
        ? (array) $user->roles
        : array();

    return in_array(
        'wcfm_vendor',
        $roles,
        true
    ) || in_array(
        'vendor',
        $roles,
        true
    );
}

/**
 * Return the existing WCFM Vendor Registration URL.
 *
 * @return string
 */
function tourbi_theme_get_vendor_registration_url() {
    $page = get_page_by_path(
        'vendor-register',
        OBJECT,
        'page'
    );

    if ( $page instanceof WP_Post ) {
        return get_permalink( $page );
    }

    return home_url( '/vendor-register/' );
}

/**
 * Return the WCFM dashboard URL.
 *
 * @return string
 */
function tourbi_theme_get_host_dashboard_url() {
    if ( function_exists( 'wcfm_get_page_permalink' ) ) {
        $url = wcfm_get_page_permalink(
            'wcfm_store_manager_url'
        );

        if ( $url ) {
            return $url;
        }
    }

    $page = get_page_by_path(
        'store-manager',
        OBJECT,
        'page'
    );

    return $page instanceof WP_Post
        ? get_permalink( $page )
        : home_url( '/store-manager/' );
}

/**
 * Return the Host Experience Builder URL.
 *
 * @return string
 */
function tourbi_theme_get_host_builder_url() {
    if (
        function_exists(
            'tourbi_core_get_host_wizard_url'
        )
    ) {
        return tourbi_core_get_host_wizard_url();
    }

    return trailingslashit(
        tourbi_theme_get_host_dashboard_url()
    ) . 'tourbi-new-experience/';
}

/**
 * Return the correct CTA for the current visitor.
 *
 * @return array<string,string>
 */
function tourbi_theme_get_host_primary_cta() {
    if ( tourbi_theme_current_user_is_host() ) {
        return array(
            'label' => __(
                'Open Experience Builder',
                'torby'
            ),
            'url'   =>
                tourbi_theme_get_host_builder_url(),
            'note'  => __(
                'Create or update your next Tourbi Experience.',
                'torby'
            ),
        );
    }

    return array(
        'label' => __(
            'Start Your Host Application',
            'torby'
        ),
        'url'   =>
            tourbi_theme_get_vendor_registration_url(),
        'note'  => is_user_logged_in()
            ? __(
                'Continue to the existing Tourbi Host application.',
                'torby'
            )
            : __(
                'Create your Host account and submit it for approval.',
                'torby'
            ),
    );
}

/**
 * Return one published Experience image for the Host hero.
 *
 * @return string
 */
function tourbi_theme_get_host_hero_image_url() {
    $cache_key =
        'tourbi_become_host_hero_image_v1';

    $cached = get_transient( $cache_key );

    if ( is_string( $cached ) ) {
        return $cached;
    }

    $base_args = array(
        'post_type'      => 'rbfw_item',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
        'meta_query'     => array(
            array(
                'key'     =>
                    '_tourbi_experience_enabled',
                'value'   => 'yes',
                'compare' => '=',
            ),
        ),
        'orderby'        => array(
            'modified' => 'DESC',
            'date'     => 'DESC',
        ),
    );

    $featured_args = $base_args;
    $featured_args['meta_query'][] = array(
        'key'     =>
            '_tourbi_experience_featured',
        'value'   => '1',
        'compare' => '=',
    );

    $query = new WP_Query( $featured_args );
    $experience_id = ! empty( $query->posts[0] )
        ? absint( $query->posts[0] )
        : 0;

    if ( ! $experience_id ) {
        $query = new WP_Query( $base_args );
        $experience_id = ! empty(
            $query->posts[0]
        )
            ? absint( $query->posts[0] )
            : 0;
    }

    $url = $experience_id
        ? (string) get_the_post_thumbnail_url(
            $experience_id,
            'full'
        )
        : '';

    set_transient(
        $cache_key,
        $url,
        HOUR_IN_SECONDS
    );

    return $url;
}

/**
 * Clear the Host hero image cache after an Experience changes.
 *
 * @param int $post_id Saved post ID.
 * @return void
 */
function tourbi_theme_clear_host_hero_cache(
    $post_id
) {
    if (
        'rbfw_item' === get_post_type(
            absint( $post_id )
        )
    ) {
        delete_transient(
            'tourbi_become_host_hero_image_v1'
        );
    }
}
add_action(
    'save_post_rbfw_item',
    'tourbi_theme_clear_host_hero_cache',
    110
);

/**
 * Return the full Become a Host view model.
 *
 * @return array<string,mixed>
 */
function tourbi_theme_get_become_host_view_model() {
    $primary_cta =
        tourbi_theme_get_host_primary_cta();

    return array(
        'primary_cta' =>
            $primary_cta,
        'secondary_cta' => array(
            'label' => __(
                'Explore Experiences',
                'torby'
            ),
            'url'   =>
                tourbi_theme_get_experience_archive_url(),
        ),
        'dashboard_url' =>
            tourbi_theme_get_host_dashboard_url(),
        'registration_url' =>
            tourbi_theme_get_vendor_registration_url(),
        'is_host' =>
            tourbi_theme_current_user_is_host(),
        'is_logged_in' =>
            is_user_logged_in(),
        'hero_image' =>
            tourbi_theme_get_host_hero_image_url(),
        'location' =>
            tourbi_theme_get_location_label(),
    );
}
