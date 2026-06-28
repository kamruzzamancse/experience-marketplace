<?php
/**
 * Normal Rental information sections.
 *
 * @package Torby
 */

$rental = $args['rental'] ?? array();
$features = (array) ( $rental['features'] ?? array() );
?>
<section class="tourbi-rental-section">
    <div class="tourbi-rental-section__heading">
        <span><?php esc_html_e( 'The Rental', 'torby' ); ?></span>
        <h2><?php esc_html_e( 'About this bike rental', 'torby' ); ?></h2>
    </div>

    <?php if ( ! empty( $rental['description_html'] ) ) : ?>
        <div class="tourbi-rental-prose">
            <?php echo wp_kses_post( $rental['description_html'] ); ?>
        </div>
    <?php else : ?>
        <div class="tourbi-rental-prose">
            <p>
                <?php
                esc_html_e(
                    'Select an available date, rental duration, and quantity from the booking panel. Final pricing and pickup details are confirmed during checkout.',
                    'torby'
                );
                ?>
            </p>
        </div>
    <?php endif; ?>
</section>

<section class="tourbi-rental-section">
    <div class="tourbi-rental-section__heading">
        <span><?php esc_html_e( 'Rental Features', 'torby' ); ?></span>
        <h2><?php esc_html_e( 'What to expect', 'torby' ); ?></h2>
    </div>

    <div class="tourbi-rental-feature-grid">
        <?php foreach ( $features as $feature ) : ?>
            <div>
                <b aria-hidden="true">✓</b>
                <span><?php echo esc_html( $feature ); ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="tourbi-rental-section">
    <div class="tourbi-rental-section__heading">
        <span><?php esc_html_e( 'Simple Process', 'torby' ); ?></span>
        <h2><?php esc_html_e( 'Book, pick up, ride, return', 'torby' ); ?></h2>
    </div>

    <div class="tourbi-rental-steps">
        <article>
            <strong>01</strong>
            <h3><?php esc_html_e( 'Choose your time', 'torby' ); ?></h3>
            <p><?php esc_html_e( 'Select the available date, start time, duration, and rental quantity.', 'torby' ); ?></p>
        </article>
        <article>
            <strong>02</strong>
            <h3><?php esc_html_e( 'Complete checkout', 'torby' ); ?></h3>
            <p><?php esc_html_e( 'Your selected inventory is protected while you complete the secure checkout.', 'torby' ); ?></p>
        </article>
        <article>
            <strong>03</strong>
            <h3><?php esc_html_e( 'Pick up and ride', 'torby' ); ?></h3>
            <p><?php esc_html_e( 'Review the order details for pickup, return, and any rental-specific instructions.', 'torby' ); ?></p>
        </article>
    </div>
</section>

<section class="tourbi-rental-section tourbi-rental-notice">
    <div>
        <span aria-hidden="true">i</span>
    </div>
    <div>
        <h2><?php esc_html_e( 'Rental information', 'torby' ); ?></h2>
        <p>
            <?php
            esc_html_e(
                'This page is for a normal bike rental. It does not include a guided Host, Experience itinerary, participant-based tour content, or Experience meeting points.',
                'torby'
            );
            ?>
        </p>
    </div>
</section>
