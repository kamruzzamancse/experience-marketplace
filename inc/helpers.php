<?php
/**
 * Helper Functions (Procedural)
 * Reusable utility functions for the theme.
 *
 * @package Experience_Marketplace
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Debug function to print arrays/objects nicely.
 *
 * @param mixed $data Data to be printed.
 */
function debug_pre( $data ) {
    echo '<pre style="background: #f4f4f4; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">';
    print_r( $data );
    echo '</pre>';
}

/**
 * Get current user role.
 *
 * @return string|false Role name or false if not logged in.
 */
function experience_marketplace_get_current_user_role() {
    if ( ! is_user_logged_in() ) {
        return false;
    }

    $user = wp_get_current_user();
    return ( ! empty( $user->roles ) ) ? $user->roles[0] : false;
}

/**
 * Check if current user has a specific role.
 *
 * @param string $role Role name to check.
 * @return bool True if user has the role.
 */
function experience_marketplace_user_has_role( $role ) {
    $current_role = experience_marketplace_get_current_user_role();
    return ( $current_role === $role );
}

/**
 * Redirect users after login based on their role.
 */
add_filter( 'login_redirect', 'experience_marketplace_role_based_redirect', 10, 3 );
function experience_marketplace_role_based_redirect( $redirect_to, $request, $user ) {
    // Check if user is valid and has roles
    if ( isset( $user->roles ) && is_array( $user->roles ) ) {
        if ( in_array( 'host', $user->roles ) ) {
            return home_url( '/host-dashboard/' );
        } elseif ( in_array( 'rider', $user->roles ) ) {
            return home_url( '/my-bookings/' );
        } elseif ( in_array( 'administrator', $user->roles ) ) {
            return admin_url();
        }
    }
    return $redirect_to;
}

/**
 * Example: Add custom CSS class to body based on user role.
 */
add_filter( 'body_class', 'experience_marketplace_add_role_body_class' );
function experience_marketplace_add_role_body_class( $classes ) {
    $role = experience_marketplace_get_current_user_role();
    if ( $role ) {
        $classes[] = 'user-role-' . sanitize_html_class( $role );
    }
    return $classes;
}