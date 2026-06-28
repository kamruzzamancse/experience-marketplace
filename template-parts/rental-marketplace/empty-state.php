<?php
/**
 * Rental marketplace empty state.
 *
 * @package Torby
 */

$has_filters = ! empty( $args['has_filters'] );
?>
<section class="tourbi-rental-marketplace-empty">
    <span aria-hidden="true">⌁</span>

    <h2>
        <?php
        echo $has_filters
            ? esc_html__(
                'No rentals match these filters.',
                'torby'
            )
            : esc_html__(
                'Bike rentals are being prepared.',
                'torby'
            );
        ?>
    </h2>

    <p>
        <?php
        echo $has_filters
            ? esc_html__(
                'Clear the current filters or try a different search.',
                'torby'
            )
            : esc_html__(
                'Publish a normal Rent Item with Rental Mapping enabled to display it here.',
                'torby'
            );
        ?>
    </p>

    <?php if ( $has_filters ) : ?>
        <a
            class="tourbi-button tourbi-button--primary"
            href="<?php echo esc_url( tourbi_theme_get_rental_marketplace_url() ); ?>"
        >
            <?php esc_html_e( 'View All Rentals', 'torby' ); ?>
        </a>
    <?php endif; ?>
</section>
