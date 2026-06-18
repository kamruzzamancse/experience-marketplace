<?php
/**
 * Inventory Manager (Procedural)
 * Handles e-bike inventory logic and availability checking.
 *
 * @package Experience_Marketplace
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get total number of e-bikes in the fleet.
 *
 * @return int Total bike count.
 */
function experience_marketplace_get_total_bikes() {
    // Default fleet size (can be made dynamic via admin settings later)
    return 10;
}

/**
 * Get number of bikes booked for a specific date.
 *
 * @param string $date Date in Y-m-d format.
 * @return int Number of booked bikes.
 */
function experience_marketplace_get_booked_bikes( $date ) {
    // Placeholder: Query WooCommerce orders or custom table for bookings
    // For now, return 0 (no bookings)
    return 0;
}

/**
 * Check if a booking is possible for a given date and required bikes.
 *
 * @param string $date          Date in Y-m-d format.
 * @param int    $required_bikes Number of bikes needed for the experience.
 * @return bool True if available, false if not.
 */
function experience_marketplace_is_inventory_available( $date, $required_bikes ) {
    $total_bikes    = experience_marketplace_get_total_bikes();
    $booked_bikes   = experience_marketplace_get_booked_bikes( $date );
    $available_bikes = $total_bikes - $booked_bikes;

    return ( $available_bikes >= $required_bikes );
}

/**
 * Get remaining bike count for a specific date.
 *
 * @param string $date Date in Y-m-d format.
 * @return int Number of bikes available.
 */
function experience_marketplace_get_available_bikes( $date ) {
    $total_bikes    = experience_marketplace_get_total_bikes();
    $booked_bikes   = experience_marketplace_get_booked_bikes( $date );

    return ( $total_bikes - $booked_bikes );
}

/**
 * Example hook to validate booking (woocommerce_checkout_process).
 * This prevents checkout if inventory is not available.
 */
add_action( 'woocommerce_checkout_process', 'experience_marketplace_validate_booking_inventory' );
function experience_marketplace_validate_booking_inventory() {
    // Example: Check cart items for booking date and bike count
    // Detailed implementation will go here when booking system is ready.
}