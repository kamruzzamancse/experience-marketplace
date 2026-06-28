<?php
/**
 * WpRently booking panel inside the custom Experience layout.
 *
 * @package Torby
 */

$experience = $args['experience'] ?? array();
?>
<div class="tourbi-booking-card">
    <div class="tourbi-booking-card__topline">
        <span class="tourbi-booking-card__status">
            <i aria-hidden="true"></i>
            <?php esc_html_e( 'Live availability', 'torby' ); ?>
        </span>

        <?php if ( ! empty( $experience['featured'] ) ) : ?>
            <span class="tourbi-booking-card__featured">
                <?php esc_html_e( 'Featured', 'torby' ); ?>
            </span>
        <?php endif; ?>
    </div>

    <div class="tourbi-booking-card__price">
        <strong><?php echo wp_kses_post( $experience['price_html'] ); ?></strong>
        <span><?php esc_html_e( 'per person', 'torby' ); ?></span>
    </div>

    <div class="tourbi-booking-card__summary">
        <span>
            <b aria-hidden="true">◷</b>
            <?php echo esc_html( $experience['duration_label'] ); ?>
        </span>

        <span>
            <b aria-hidden="true">◎</b>
            <?php echo esc_html( $experience['participant_label'] ); ?>
        </span>

        <?php if ( ! empty( $experience['start_time_label'] ) ) : ?>
            <span>
                <b aria-hidden="true">◴</b>
                <?php
                echo esc_html(
                    sprintf(
                        /* translators: 1: Start time. 2: End time. */
                        __( '%1$s – %2$s', 'torby' ),
                        $experience['start_time_label'],
                        $experience['end_time_label']
                    )
                );
                ?>
            </span>
        <?php endif; ?>

        <span>
            <b aria-hidden="true">♢</b>
            <?php echo esc_html( $experience['bike_type_label'] ); ?>
        </span>
    </div>

    <div class="tourbi-booking-card__divider"></div>

    <div class="tourbi-experience-booking-widget">
        <?php
        echo do_shortcode(
            $experience['booking_shortcode']
        ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
    </div>

    <div class="tourbi-booking-card__trust">
        <span>✓ <?php esc_html_e( 'Secure checkout', 'torby' ); ?></span>
        <span>✓ <?php esc_html_e( 'Inventory protected', 'torby' ); ?></span>
        <span>✓ <?php esc_html_e( 'Instant booking details', 'torby' ); ?></span>
    </div>
</div>
