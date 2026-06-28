<?php
/**
 * Custom Single Experience template.
 *
 * Used only for WpRently Rent Items with an enabled Tourbi Experience
 * Mapping. Normal rentals continue using the original WpRently template.
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

while ( have_posts() ) :
    the_post();

    $experience = function_exists(
        'tourbi_theme_get_single_experience_view_model'
    )
        ? tourbi_theme_get_single_experience_view_model(
            get_the_ID()
        )
        : array();

    if ( empty( $experience['id'] ) ) {
        continue;
    }
    ?>
    <main
        id="primary"
        class="tourbi-app tourbi-experience-single"
    >
        <?php
        get_template_part(
            'template-parts/experience/hero',
            null,
            array(
                'experience' => $experience,
            )
        );
        ?>

        <div class="tourbi-shell--wide tourbi-experience-single__layout">
            <div class="tourbi-experience-single__content">
                <?php
                get_template_part(
                    'template-parts/experience/overview',
                    null,
                    array(
                        'experience' => $experience,
                    )
                );

                get_template_part(
                    'template-parts/experience/itinerary',
                    null,
                    array(
                        'experience' => $experience,
                    )
                );

                get_template_part(
                    'template-parts/experience/gallery',
                    null,
                    array(
                        'experience' => $experience,
                    )
                );

                get_template_part(
                    'template-parts/experience/inclusions',
                    null,
                    array(
                        'experience' => $experience,
                    )
                );

                get_template_part(
                    'template-parts/experience/host',
                    null,
                    array(
                        'experience' => $experience,
                    )
                );

                get_template_part(
                    'template-parts/experience/meeting-location',
                    null,
                    array(
                        'experience' => $experience,
                    )
                );

                get_template_part(
                    'template-parts/experience/policies',
                    null,
                    array(
                        'experience' => $experience,
                    )
                );
                ?>
            </div>

            <aside
                id="tourbi-booking-panel"
                class="tourbi-experience-single__booking"
                aria-label="<?php esc_attr_e( 'Experience booking', 'torby' ); ?>"
            >
                <?php
                get_template_part(
                    'template-parts/experience/booking-panel',
                    null,
                    array(
                        'experience' => $experience,
                    )
                );
                ?>
            </aside>
        </div>

        <div
            class="tourbi-mobile-booking-bar"
            data-tourbi-mobile-booking-bar
        >
            <div>
                <small><?php esc_html_e( 'From', 'torby' ); ?></small>
                <strong>
                    <?php echo wp_kses_post( $experience['price_html'] ); ?>
                </strong>
                <span><?php esc_html_e( 'per person', 'torby' ); ?></span>
            </div>

            <a
                class="tourbi-button tourbi-button--primary"
                href="#tourbi-booking-panel"
                data-tourbi-booking-anchor
            >
                <?php esc_html_e( 'Book Experience', 'torby' ); ?>
            </a>
        </div>
    </main>
    <?php
endwhile;

get_footer();
