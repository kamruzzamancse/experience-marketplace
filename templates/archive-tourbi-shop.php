<?php
/**
 * Tourbi Shop Hub template.
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$shop = function_exists(
    'tourbi_theme_get_shop_hub_view_model'
)
    ? tourbi_theme_get_shop_hub_view_model()
    : array();
?>
<main
    id="primary"
    class="tourbi-app tourbi-shop-hub"
>
    <section class="tourbi-shop-hub__hero">
        <div class="tourbi-shell--wide">
            <span class="tourbi-shop-hub__kicker">
                <?php esc_html_e( 'Choose Your Ride', 'torby' ); ?>
            </span>

            <h1>
                <?php esc_html_e( 'How would you like to explore?', 'torby' ); ?>
            </h1>

            <p>
                <?php
                esc_html_e(
                    'Join a guided local Experience or reserve an E-Bike for your own schedule. Tourbi keeps each booking flow clear and separate.',
                    'torby'
                );
                ?>
            </p>
        </div>
    </section>

    <section class="tourbi-shop-hub__choices">
        <div class="tourbi-shell--wide tourbi-shop-hub__grid">
            <article
                class="tourbi-shop-choice tourbi-shop-choice--experience"
                <?php if ( ! empty( $shop['experience_image'] ) ) : ?>
                    style="--tourbi-shop-image:url('<?php echo esc_url( $shop['experience_image'] ); ?>');"
                <?php endif; ?>
            >
                <div class="tourbi-shop-choice__overlay"></div>
                <div class="tourbi-shop-choice__content">
                    <span><?php esc_html_e( 'Guided', 'torby' ); ?></span>
                    <h2><?php esc_html_e( 'Book an Experience', 'torby' ); ?></h2>
                    <p>
                        <?php
                        esc_html_e(
                            'Ride with a local Host, follow a curated itinerary, and discover memorable places and stories.',
                            'torby'
                        );
                        ?>
                    </p>

                    <ul>
                        <li><?php esc_html_e( 'Local Host', 'torby' ); ?></li>
                        <li><?php esc_html_e( 'Planned itinerary', 'torby' ); ?></li>
                        <li><?php esc_html_e( 'Per-person booking', 'torby' ); ?></li>
                    </ul>

                    <a
                        class="tourbi-button tourbi-button--primary"
                        href="<?php echo esc_url( $shop['experience_url'] ?? home_url( '/experiences/' ) ); ?>"
                    >
                        <?php esc_html_e( 'Explore Experiences', 'torby' ); ?>
                        <span aria-hidden="true">→</span>
                    </a>
                </div>
            </article>

            <article
                class="tourbi-shop-choice tourbi-shop-choice--rental"
                <?php if ( ! empty( $shop['rental_image'] ) ) : ?>
                    style="--tourbi-shop-image:url('<?php echo esc_url( $shop['rental_image'] ); ?>');"
                <?php endif; ?>
            >
                <div class="tourbi-shop-choice__overlay"></div>
                <div class="tourbi-shop-choice__content">
                    <span><?php esc_html_e( 'Self-Guided', 'torby' ); ?></span>
                    <h2><?php esc_html_e( 'Rent an E-Bike', 'torby' ); ?></h2>
                    <p>
                        <?php
                        esc_html_e(
                            'Choose your date, time, duration, and quantity, then pick up the bike and explore at your own pace.',
                            'torby'
                        );
                        ?>
                    </p>

                    <ul>
                        <li><?php esc_html_e( 'No guide or itinerary', 'torby' ); ?></li>
                        <li><?php esc_html_e( 'Pickup and return', 'torby' ); ?></li>
                        <li><?php esc_html_e( 'Duration-based pricing', 'torby' ); ?></li>
                    </ul>

                    <a
                        class="tourbi-button tourbi-button--lime"
                        href="<?php echo esc_url( $shop['rental_url'] ?? home_url( '/rent/' ) ); ?>"
                    >
                        <?php esc_html_e( 'View Bike Rentals', 'torby' ); ?>
                        <span aria-hidden="true">→</span>
                    </a>
                </div>
            </article>
        </div>
    </section>

    <section class="tourbi-shop-hub__support">
        <div class="tourbi-shell--wide">
            <div>
                <span><?php esc_html_e( 'Secure Booking', 'torby' ); ?></span>
                <strong><?php esc_html_e( 'Live availability and protected inventory', 'torby' ); ?></strong>
            </div>

            <div>
                <span><?php esc_html_e( 'Already booked?', 'torby' ); ?></span>
                <a href="<?php echo esc_url( $shop['account_url'] ?? home_url( '/my-account/' ) ); ?>">
                    <?php esc_html_e( 'Open My Account', 'torby' ); ?>
                    <b aria-hidden="true">→</b>
                </a>
            </div>
        </div>
    </section>
</main>
<?php
get_footer();
