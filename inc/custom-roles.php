<?php
/**
 * Custom User Roles (Procedural)
 *
 * @package Experience_Marketplace
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Setup custom user roles and capabilities.
 * This function adds Host and Rider roles with appropriate capabilities.
 */
function experience_marketplace_setup_roles() {
    // 1. Add Host role
    if ( ! get_role( 'host' ) ) {
        add_role(
            'host',
            'Host',
            array(
                'read'                   => true,
                'edit_posts'             => true,
                'delete_posts'           => true,
                'publish_posts'          => true,
                'upload_files'           => true,
                'edit_products'          => true, // WooCommerce
                'view_woocommerce_reports' => true,
                'manage_woocommerce'     => false, // Restricted
            )
        );
    }

    // 2. Add Rider role
    if ( ! get_role( 'rider' ) ) {
        add_role(
            'rider',
            'Rider',
            array(
                'read' => true,
                // Riders can only read, no editing capabilities
            )
        );
    }

    // 3. Add custom capabilities to Administrator
    $admin = get_role( 'administrator' );
    if ( $admin ) {
        $admin->add_cap( 'manage_woocommerce' );
        $admin->add_cap( 'edit_products' );
        $admin->add_cap( 'delete_products' );
        $admin->add_cap( 'view_woocommerce_reports' );
    }

    // 4. Remove unwanted capabilities from Host
    $host = get_role( 'host' );
    if ( $host ) {
        $host->remove_cap( 'delete_others_posts' );
        $host->remove_cap( 'install_plugins' );
        $host->remove_cap( 'activate_plugins' );
    }
}
add_action( 'init', 'experience_marketplace_setup_roles' );