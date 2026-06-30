<?php
/**
 * Reference-matched Tourbi Home page content.
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$featured = tourbi_reference_home_get_featured_experience();
$featured_id = absint( $featured['id'] ?? 0 );
$adventure_ids = tourbi_reference_home_get_experience_ids(
    4,
    $featured_id ? array( $featured_id ) : array()
);

$featured_title = tourbi_reference_home_hero_title( $featured );
$featured_summary = sanitize_textarea_field(
    $featured['summary'] ??
    __( 'Good food. Great vibes. Scenic routes. All on e-bikes.', 'torby' )
);
$featured_duration = sanitize_text_field(
    $featured['duration_label'] ?? __( '3 Hours', 'torby' )
);
$featured_people = sanitize_text_field(
    $featured['participant_label'] ?? __( '2–12 People', 'torby' )
);
$featured_price = $featured['price_html'] ?? __( 'From $69', 'torby' );
$featured_url = $featured['permalink'] ?? tourbi_reference_home_experiences_url();

$card_labels = array(
    __( 'Popular', 'torby' ),
    __( 'Top Rated', 'torby' ),
    __( 'Win Big', 'torby' ),
    __( 'Top Pick', 'torby' ),
);

$card_classes = array(
    'green',
    'blue',
    'teal',
    'orange',
);
?>
<div class="tourbi-home-page">
    <main id="tourbi-home-main" class="tourbi-home-main">
        <section class="tourbi-home-intro">
            <div class="tourbi-home-shell tourbi-home-intro__grid">
                <div class="tourbi-home-origin">
                    <span class="tourbi-home-origin__icon" aria-hidden="true">🚲</span>
                    <div>
                        <p><?php esc_html_e( 'All experiences begin at', 'torby' ); ?></p>
                        <h1><?php esc_html_e( 'King Electric Bike Shop.', 'torby' ); ?></h1>
                        <address>
                            <span aria-hidden="true">⌖</span>
                            <?php esc_html_e( '502 23rd Street, NW', 'torby' ); ?><br>
                            <?php esc_html_e( 'Washington, D.C. 20037', 'torby' ); ?>
                        </address>
                    </div>
                </div>

                <a
                    class="tourbi-home-host-cta"
                    href="<?php echo esc_url( tourbi_reference_home_host_url() ); ?>"
                >
                    <span class="tourbi-home-host-cta__icon" aria-hidden="true">♙</span>
                    <span>
                        <strong><?php esc_html_e( 'Host Your Own Experience', 'torby' ); ?></strong>
                        <small><?php esc_html_e( 'Share your idea. We’ll help make it epic.', 'torby' ); ?></small>
                    </span>
                </a>
            </div>
        </section>

        <section class="tourbi-home-featured">
            <div class="tourbi-home-shell">
                <article class="tourbi-home-featured__card">
                    <?php
                    echo wp_kses_post(
                        tourbi_reference_home_get_main_image_markup(
                            $featured_id,
                            $featured,
                            'hero',
                            'tourbi-home-featured__image'
                        )
                    );
                    ?>

                    <div class="tourbi-home-featured__gradient"></div>

                    <div class="tourbi-home-featured__copy">
                        <span class="tourbi-home-featured__badge">
                            <span aria-hidden="true">★</span>
                            <?php esc_html_e( 'Featured Experience', 'torby' ); ?>
                        </span>

                        <h2><?php echo esc_html( $featured_title ); ?></h2>

                        <p>
                            <?php
                            echo esc_html(
                                wp_trim_words(
                                    $featured_summary,
                                    17,
                                    '…'
                                )
                            );
                            ?>
                        </p>

                        <div class="tourbi-home-featured__meta">
                            <span><b aria-hidden="true">◷</b><?php echo esc_html( $featured_duration ); ?></span>
                            <span><b aria-hidden="true">♧</b><?php echo esc_html( $featured_people ); ?></span>
                            <span><b aria-hidden="true">◇</b><?php echo wp_kses_post( $featured_price ); ?></span>
                        </div>

                        <a class="tourbi-home-featured__button" href="<?php echo esc_url( $featured_url ); ?>">
                            <span aria-hidden="true">▣</span>
                            <?php esc_html_e( 'Book This Experience', 'torby' ); ?>
                        </a>
                    </div>

                    <div class="tourbi-home-featured__benefits" aria-label="<?php esc_attr_e( 'Experience benefits', 'torby' ); ?>">
                        <div>
                            <span aria-hidden="true">♜</span>
                            <p><strong><?php esc_html_e( 'Delicious Bites', 'torby' ); ?></strong><small><?php esc_html_e( 'Local eats and drinks', 'torby' ); ?></small></p>
                        </div>
                        <div>
                            <span aria-hidden="true">⌘</span>
                            <p><strong><?php esc_html_e( 'Scenic Routes', 'torby' ); ?></strong><small><?php esc_html_e( 'Beautiful sights. Great vibes.', 'torby' ); ?></small></p>
                        </div>
                        <div>
                            <span aria-hidden="true">🚲</span>
                            <p><strong><?php esc_html_e( 'Easy & Fun', 'torby' ); ?></strong><small><?php esc_html_e( 'Premium e-bikes for all', 'torby' ); ?></small></p>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <section class="tourbi-home-adventures">
            <div class="tourbi-home-shell">
                <div class="tourbi-home-section-title">
                    <span></span>
                    <h2><?php esc_html_e( 'View More Adventures', 'torby' ); ?></h2>
                    <span></span>
                </div>

                <?php if ( ! empty( $adventure_ids ) ) : ?>
                    <div class="tourbi-home-adventure-grid">
                        <?php foreach ( $adventure_ids as $index => $experience_id ) : ?>
                            <?php
                            $card = tourbi_reference_home_get_card( $experience_id );
                            $label = $card_labels[ $index % count( $card_labels ) ];
                            $accent = $card_classes[ $index % count( $card_classes ) ];
                            ?>
                            <article class="tourbi-home-adventure-card tourbi-home-adventure-card--<?php echo esc_attr( $accent ); ?>">
                                <a class="tourbi-home-adventure-card__media" href="<?php echo esc_url( $card['permalink'] ?? get_permalink( $experience_id ) ); ?>">
                                    <?php
                                    echo wp_kses_post(
                                        tourbi_reference_home_get_main_image_markup(
                                            $experience_id,
                                            $card,
                                            'card',
                                            'tourbi-home-adventure-card__image'
                                        )
                                    );
                                    ?>
                                    <span class="tourbi-home-adventure-card__badge"><?php echo esc_html( $label ); ?></span>
                                </a>

                                <div class="tourbi-home-adventure-card__body">
                                    <h3>
                                        <a href="<?php echo esc_url( $card['permalink'] ?? get_permalink( $experience_id ) ); ?>">
                                            <?php echo esc_html( $card['short_title'] ?? get_the_title( $experience_id ) ); ?>
                                        </a>
                                    </h3>

                                    <?php if ( ! empty( $card['summary'] ) ) : ?>
                                        <p>
                                            <?php
                                            echo esc_html(
                                                wp_trim_words(
                                                    $card['summary'],
                                                    12,
                                                    '…'
                                                )
                                            );
                                            ?>
                                        </p>
                                    <?php endif; ?>

                                    <div class="tourbi-home-adventure-card__meta">
                                        <?php if ( ! empty( $card['duration_label'] ) ) : ?>
                                            <span><b aria-hidden="true">◷</b><?php echo esc_html( $card['duration_label'] ); ?></span>
                                        <?php endif; ?>

                                        <?php if ( ! empty( $card['participant_label'] ) ) : ?>
                                            <span><b aria-hidden="true">♧</b><?php echo esc_html( $card['participant_label'] ); ?></span>
                                        <?php endif; ?>

                                        <?php if ( ! empty( $card['price_html'] ) ) : ?>
                                            <span><b aria-hidden="true">◇</b><?php echo wp_kses_post( $card['price_html'] ); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <a class="tourbi-home-adventure-card__button" href="<?php echo esc_url( $card['permalink'] ?? get_permalink( $experience_id ) ); ?>">
                                        <span aria-hidden="true">▣</span>
                                        <?php esc_html_e( 'Book Experience', 'torby' ); ?>
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="tourbi-home-empty">
                        <strong><?php esc_html_e( 'No published Experiences yet.', 'torby' ); ?></strong>
                        <p><?php esc_html_e( 'Published guided Experiences will appear here automatically in newest-first order.', 'torby' ); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="tourbi-home-trust" aria-label="<?php esc_attr_e( 'Why Tourbi', 'torby' ); ?>">
            <div class="tourbi-home-shell tourbi-home-trust__grid">
                <div class="tourbi-home-trust__item">
                    <span aria-hidden="true">🚲</span>
                    <p><strong><?php esc_html_e( 'Top Quality E-Bikes', 'torby' ); ?></strong><small><?php esc_html_e( 'Reliable, powerful, and fun to ride.', 'torby' ); ?></small></p>
                </div>
                <div class="tourbi-home-trust__item">
                    <span aria-hidden="true">♢</span>
                    <p><strong><?php esc_html_e( 'Safe & Reliable', 'torby' ); ?></strong><small><?php esc_html_e( 'Safety first. Always. We’ve got you.', 'torby' ); ?></small></p>
                </div>
                <div class="tourbi-home-trust__item">
                    <span aria-hidden="true">♧</span>
                    <p><strong><?php esc_html_e( 'Local Vibes, Real Connections', 'torby' ); ?></strong><small><?php esc_html_e( 'Real people. Real places. Real memories.', 'torby' ); ?></small></p>
                </div>
            </div>
        </section>
    </main>
</div>
