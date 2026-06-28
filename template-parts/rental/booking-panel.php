<?php
/**
 * WpRently booking widget for a normal Rental item.
 *
 * @package Torby
 */

$rental = $args['rental'] ?? array();
?>
<div class="tourbi-rental-booking-card">
    <div class="tourbi-rental-booking-card__topline">
        <span>
            <i aria-hidden="true"></i>
            <?php esc_html_e( 'Rental availability', 'torby' ); ?>
        </span>
        <small><?php esc_html_e( 'Secure booking', 'torby' ); ?></small>
    </div>

    <div class="tourbi-rental-booking-card__price">
        <small><?php esc_html_e( 'Starting from', 'torby' ); ?></small>
        <strong><?php echo wp_kses_post( $rental['price_html'] ); ?></strong>
        <span><?php esc_html_e( 'Final price depends on the selected duration and quantity.', 'torby' ); ?></span>
    </div>

    <div class="tourbi-rental-booking-card__divider"></div>

    <div class="tourbi-rental-booking-widget">
        <?php
        echo do_shortcode(
            $rental['booking_shortcode']
        ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
    </div>

    <div class="tourbi-rental-booking-card__trust">
        <span>✓ <?php esc_html_e( 'Live availability', 'torby' ); ?></span>
        <span>✓ <?php esc_html_e( 'Protected inventory', 'torby' ); ?></span>
        <span>✓ <?php esc_html_e( 'Secure checkout', 'torby' ); ?></span>
    </div>
</div>
