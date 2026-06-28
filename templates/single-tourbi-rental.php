<?php
/**
 * Custom normal Rental template.
 *
 * This template is never used for mapped Tourbi Experiences.
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

while ( have_posts() ) :
    the_post();

    $rental = function_exists(
        'tourbi_theme_get_single_rental_view_model'
    )
        ? tourbi_theme_get_single_rental_view_model(
            get_the_ID()
        )
        : array();

    if ( empty( $rental['id'] ) ) {
        continue;
    }
    ?>
    <main
        id="primary"
        class="tourbi-app tourbi-rental-single"
    >
        <?php
        get_template_part(
            'template-parts/rental/hero',
            null,
            array( 'rental' => $rental )
        );
        ?>

        <div class="tourbi-shell--wide tourbi-rental-single__layout">
            <div class="tourbi-rental-single__content">
                <?php
                get_template_part(
                    'template-parts/rental/overview',
                    null,
                    array( 'rental' => $rental )
                );
                ?>
            </div>

            <aside
                id="tourbi-rental-booking"
                class="tourbi-rental-single__booking"
                aria-label="<?php esc_attr_e( 'Rental booking', 'torby' ); ?>"
            >
                <?php
                get_template_part(
                    'template-parts/rental/booking-panel',
                    null,
                    array( 'rental' => $rental )
                );
                ?>
            </aside>
        </div>

        <div class="tourbi-rental-mobile-bar">
            <div>
                <small><?php esc_html_e( 'Starting from', 'torby' ); ?></small>
                <strong><?php echo wp_kses_post( $rental['price_html'] ); ?></strong>
            </div>

            <a
                class="tourbi-button tourbi-button--primary"
                href="#tourbi-rental-booking"
                data-tourbi-rental-booking-anchor
            >
                <?php esc_html_e( 'Check Availability', 'torby' ); ?>
            </a>
        </div>
    </main>
    <?php
endwhile;

get_footer();
