<?php
/**
 * Normal Rental hero and gallery.
 *
 * @package Torby
 */

$rental = $args['rental'] ?? array();
$gallery = (array) ( $rental['gallery'] ?? array() );
$main_image = $gallery[0] ?? array();
?>
<section class="tourbi-rental-hero">
    <div class="tourbi-shell--wide tourbi-rental-hero__inner">
        <div class="tourbi-rental-hero__copy">
            <a
                class="tourbi-rental-back"
                href="<?php echo esc_url( $rental['archive_url'] ?: home_url( '/rent/' ) ); ?>"
            >
                <span aria-hidden="true">←</span>
                <?php esc_html_e( 'All Bike Rentals', 'torby' ); ?>
            </a>

            <div class="tourbi-rental-kind-row">
                <span class="tourbi-rental-kind-badge">
                    <?php esc_html_e( 'Self-Guided Rental', 'torby' ); ?>
                </span>

                <span class="tourbi-rental-eyebrow">
                    <?php esc_html_e( 'Tourbi E-Bike Rental', 'torby' ); ?>
                </span>
            </div>

            <h1><?php echo esc_html( $rental['title'] ); ?></h1>

            <?php if ( ! empty( $rental['summary'] ) ) : ?>
                <p><?php echo esc_html( $rental['summary'] ); ?></p>
            <?php endif; ?>

            <div class="tourbi-rental-hero__facts">
                <span>
                    <b aria-hidden="true">◷</b>
                    <?php echo esc_html( $rental['type_label'] ); ?>
                </span>
                <span>
                    <b aria-hidden="true">✓</b>
                    <?php esc_html_e( 'Live inventory', 'torby' ); ?>
                </span>
                <span>
                    <b aria-hidden="true">⌁</b>
                    <?php esc_html_e( 'Pickup & return rental', 'torby' ); ?>
                </span>
            </div>
        </div>

        <div class="tourbi-rental-gallery" data-tourbi-rental-gallery>
            <div class="tourbi-rental-gallery__main">
                <?php if ( ! empty( $main_image['large'] ) ) : ?>
                    <img
                        src="<?php echo esc_url( $main_image['large'] ); ?>"
                        alt="<?php echo esc_attr( $main_image['alt'] ?: $rental['title'] ); ?>"
                        data-tourbi-rental-main-image
                    >
                <?php else : ?>
                    <div class="tourbi-rental-gallery__placeholder">
                        <span aria-hidden="true">🚲</span>
                        <small><?php esc_html_e( 'Rental image coming soon', 'torby' ); ?></small>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ( count( $gallery ) > 1 ) : ?>
                <div class="tourbi-rental-gallery__thumbs">
                    <?php foreach ( array_slice( $gallery, 0, 5 ) as $index => $image ) : ?>
                        <button
                            type="button"
                            class="<?php echo 0 === $index ? 'is-active' : ''; ?>"
                            data-tourbi-rental-thumb
                            data-image="<?php echo esc_url( $image['large'] ); ?>"
                            data-alt="<?php echo esc_attr( $image['alt'] ?: $rental['title'] ); ?>"
                            aria-label="<?php echo esc_attr( sprintf( __( 'View rental image %d', 'torby' ), $index + 1 ) ); ?>"
                        >
                            <img
                                src="<?php echo esc_url( $image['large'] ); ?>"
                                alt=""
                                loading="lazy"
                            >
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
    <div class="tourbi-rental-type-strip">
        <div class="tourbi-shell--wide">
            <span>
                <b aria-hidden="true">01</b>
                <?php esc_html_e( 'No Host or Guided Tour', 'torby' ); ?>
            </span>
            <span>
                <b aria-hidden="true">02</b>
                <?php esc_html_e( 'Choose Date, Time and Quantity', 'torby' ); ?>
            </span>
            <span>
                <b aria-hidden="true">03</b>
                <?php esc_html_e( 'Pick Up, Ride and Return', 'torby' ); ?>
            </span>
        </div>
    </div>

