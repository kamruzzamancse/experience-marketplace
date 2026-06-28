<?php
/**
 * Template Name: Tourbi Bike Rentals
 * Template Post Type: page
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$state = tourbi_theme_get_rental_marketplace_state();

$all_cards =
    tourbi_theme_get_filtered_rental_cards(
        array_merge(
            $state,
            array(
                'bike_type' => '',
            )
        )
    );

$bike_types =
    tourbi_theme_get_rental_marketplace_bike_types(
        $all_cards
    );

$filtered_cards =
    tourbi_theme_get_filtered_rental_cards(
        $state
    );

$pagination =
    tourbi_theme_paginate_rental_cards(
        $filtered_cards,
        $state['page']
    );
?>
<main
    id="primary"
    class="tourbi-app tourbi-rental-marketplace"
>
    <section class="tourbi-rental-marketplace-hero">
        <div class="tourbi-shell--wide tourbi-rental-marketplace-hero__inner">
            <div>
                <span class="tourbi-rental-marketplace-kicker">
                    <?php esc_html_e( 'Self-Guided E-Bike Rentals', 'torby' ); ?>
                </span>

                <h1>
                    <?php esc_html_e( 'Choose your bike. Ride on your schedule.', 'torby' ); ?>
                </h1>

                <p>
                    <?php
                    esc_html_e(
                        'Reserve a Tourbi E-Bike without a guide or fixed itinerary. Select your bike, date, time, duration, and quantity, then pick it up and explore at your own pace.',
                        'torby'
                    );
                    ?>
                </p>
            </div>

            <aside class="tourbi-rental-marketplace-hero__facts">
                <div>
                    <strong>01</strong>
                    <span><?php esc_html_e( 'Choose a Rental', 'torby' ); ?></span>
                </div>
                <div>
                    <strong>02</strong>
                    <span><?php esc_html_e( 'Check Live Availability', 'torby' ); ?></span>
                </div>
                <div>
                    <strong>03</strong>
                    <span><?php esc_html_e( 'Pick Up, Ride, Return', 'torby' ); ?></span>
                </div>
            </aside>
        </div>
    </section>

    <section class="tourbi-rental-marketplace-results">
        <div class="tourbi-shell--wide">
            <?php
            get_template_part(
                'template-parts/rental-marketplace/filters',
                null,
                array(
                    'state' => $state,
                    'bike_types' => $bike_types,
                    'total' => $pagination['total'],
                )
            );
            ?>

            <?php if ( ! empty( $pagination['items'] ) ) : ?>
                <div class="tourbi-rental-marketplace-grid">
                    <?php foreach ( $pagination['items'] as $card ) : ?>
                        <?php
                        get_template_part(
                            'template-parts/rental-marketplace/card',
                            null,
                            array(
                                'card' => $card,
                            )
                        );
                        ?>
                    <?php endforeach; ?>
                </div>

                <?php
                tourbi_theme_render_rental_marketplace_pagination(
                    $pagination,
                    $state
                );
                ?>
            <?php else : ?>
                <?php
                get_template_part(
                    'template-parts/rental-marketplace/empty-state',
                    null,
                    array(
                        'has_filters' =>
                            $state['has_filters'],
                    )
                );
                ?>
            <?php endif; ?>
        </div>
    </section>

    <section class="tourbi-rental-marketplace-compare">
        <div class="tourbi-shell--wide tourbi-rental-marketplace-compare__inner">
            <div>
                <span class="tourbi-rental-marketplace-kicker">
                    <?php esc_html_e( 'Looking for a guided activity?', 'torby' ); ?>
                </span>
                <h2><?php esc_html_e( 'Rentals and Experiences are different.', 'torby' ); ?></h2>
            </div>

            <div class="tourbi-rental-marketplace-compare__cards">
                <article>
                    <strong><?php esc_html_e( 'Bike Rental', 'torby' ); ?></strong>
                    <p><?php esc_html_e( 'Self-guided, flexible duration, pickup and return, no Host or fixed itinerary.', 'torby' ); ?></p>
                </article>

                <article>
                    <strong><?php esc_html_e( 'Guided Experience', 'torby' ); ?></strong>
                    <p><?php esc_html_e( 'Led by a local Host with a planned itinerary, meeting point, and per-person booking.', 'torby' ); ?></p>
                    <a href="<?php echo esc_url( tourbi_theme_get_experience_archive_url() ); ?>">
                        <?php esc_html_e( 'Explore Experiences', 'torby' ); ?>
                        <span aria-hidden="true">→</span>
                    </a>
                </article>
            </div>
        </div>
    </section>
</main>
<?php
get_footer();
