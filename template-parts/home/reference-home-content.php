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
$featured_has_price = (float) ( $featured['price'] ?? 0 ) > 0;
$featured_url = $featured['permalink'] ?? tourbi_reference_home_experiences_url();
$hero_reference_image_path = trailingslashit( TORBY_CHILD_THEME_DIR ) . 'assets/images/tourbi-home/home-hero-riders.jpg';
$hero_reference_image_url  = trailingslashit( TORBY_CHILD_THEME_URI ) . 'assets/images/tourbi-home/home-hero-riders.jpg';
$has_hero_reference_image  = file_exists( $hero_reference_image_path );

$card_labels = array(
    __( 'Popular', 'torby' ),
    __( 'Coming Soon', 'torby' ),
    __( 'Top Pick', 'torby' ),
    __( 'New', 'torby' ),
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
        <section class="tourbi-home-hero" aria-labelledby="tourbi-home-hero-title">
            <div class="tourbi-home-shell tourbi-home-hero__grid">
                <div class="tourbi-home-hero__copy">
                    <span class="tourbi-home-hero__eyebrow"><?php esc_html_e( 'Pure Fun', 'torby' ); ?></span>
                    <h1 id="tourbi-home-hero-title">
                        <span class="tourbi-home-hero__line"><?php esc_html_e( 'Book or Host', 'torby' ); ?></span>
                        <span class="tourbi-home-hero__line tourbi-home-hero__line--orange"><?php esc_html_e( 'Experiences', 'torby' ); ?></span>
                        <span class="tourbi-home-hero__line"><?php esc_html_e( 'on E-Bikes.', 'torby' ); ?></span>
                    </h1>
                    <div class="tourbi-home-hero__callouts" aria-label="<?php esc_attr_e( 'Tourbi highlights', 'torby' ); ?>">
                        <span><b aria-hidden="true">♙</b><?php esc_html_e( 'Created by local hosts.', 'torby' ); ?></span>
                        <span><b aria-hidden="true">⚡</b><?php esc_html_e( 'Powered by e-bikes.', 'torby' ); ?></span>
                    </div>
                    <div class="tourbi-home-hero__actions">
                        <a class="tourbi-home-hero__button tourbi-home-hero__button--primary" href="<?php echo esc_url( tourbi_reference_home_experiences_url() ); ?>">
                            <?php esc_html_e( 'Explore Experiences', 'torby' ); ?>
                        </a>
                        <a class="tourbi-home-hero__button tourbi-home-hero__button--secondary" href="<?php echo esc_url( tourbi_reference_home_host_url() ); ?>">
                            <?php esc_html_e( 'Host Your Own', 'torby' ); ?>
                        </a>
                    </div>
                </div>

                <div class="tourbi-home-hero__visual" aria-hidden="true">
                    <?php if ( $has_hero_reference_image ) : ?>
                        <img
                            class="tourbi-home-hero__image"
                            src="<?php echo esc_url( $hero_reference_image_url ); ?>"
                            alt=""
                            loading="eager"
                            decoding="async"
                        >
                    <?php else : ?>
                        <?php
                        echo wp_kses_post(
                            tourbi_reference_home_get_main_image_markup(
                                $featured_id,
                                $featured,
                                'hero',
                                'tourbi-home-hero__image'
                            )
                        );
                        ?>
                    <?php endif; ?>
                </div>
            </div>

        </section>

        <section class="tourbi-home-intro">
            <div class="tourbi-home-shell tourbi-home-intro__grid">
                <div class="tourbi-home-origin">
                    <span class="tourbi-home-origin__icon" aria-hidden="true">🚲</span>
                    <div>
                        <p><?php esc_html_e( 'All experiences begin at', 'torby' ); ?></p>
                        <h2><?php esc_html_e( 'King Electric Bike Shop.', 'torby' ); ?></h2>
                        <address>
                            <span aria-hidden="true">⌖</span>
                            <?php esc_html_e( '502 23rd Street. NW', 'torby' ); ?><br>
                            <?php esc_html_e( 'Washington, D.C. 20037', 'torby' ); ?>
                        </address>
                    </div>
                </div>

                <aside class="tourbi-home-booking-cta">
                    <span class="tourbi-home-booking-cta__icon" aria-hidden="true">▣</span>
                    <div>
                        <h2><?php esc_html_e( 'Book Your Adventure Today', 'torby' ); ?></h2>
                        <p><?php esc_html_e( 'Choose a date, meet at King Electric Bike Shop, and enjoy a guided e-bike experience in Washington, D.C.', 'torby' ); ?></p>
                    </div>
                    <a href="<?php echo esc_url( tourbi_reference_home_experiences_url() ); ?>">
                        <?php esc_html_e( 'Explore Dates', 'torby' ); ?>
                        <span aria-hidden="true">→</span>
                    </a>
                </aside>
            </div>
        </section>

        <section class="tourbi-home-steps" aria-labelledby="tourbi-home-steps-title">
            <div class="tourbi-home-shell">
                <div class="tourbi-home-section-title tourbi-home-section-title--compact">
                    <span></span>
                    <h2 id="tourbi-home-steps-title"><?php esc_html_e( 'How It Works', 'torby' ); ?></h2>
                    <span></span>
                </div>

                <div class="tourbi-home-steps__grid">
                    <article>
                        <strong>01</strong>
                        <h3><?php esc_html_e( 'Meet at King Electric Bike Shop', 'torby' ); ?></h3>
                        <p><?php esc_html_e( 'Arrive at the pickup location and get ready for your ride.', 'torby' ); ?></p>
                    </article>
                    <article>
                        <strong>02</strong>
                        <h3><?php esc_html_e( 'Start your ride', 'torby' ); ?></h3>
                        <p><?php esc_html_e( 'Grab your e-bike, meet your host, and follow the route.', 'torby' ); ?></p>
                    </article>
                    <article>
                        <strong>03</strong>
                        <h3><?php esc_html_e( 'Create memories', 'torby' ); ?></h3>
                        <p><?php esc_html_e( 'Enjoy great stops, local stories, and shared experiences.', 'torby' ); ?></p>
                    </article>
                    <article>
                        <strong>04</strong>
                        <h3><?php esc_html_e( 'Share and repeat', 'torby' ); ?></h3>
                        <p><?php esc_html_e( 'Tell friends, book again, or host your own adventure.', 'torby' ); ?></p>
                    </article>
                </div>
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
                            <span>
                                <b aria-hidden="true">◇</b>
                                <span class="tourbi-card-price-per-person">
                                    <?php echo wp_kses_post( $featured_price ); ?>
                                    <?php if ( $featured_has_price ) : ?>
                                        <small class="tourbi-card-price-per-person__suffix">/person</small>
                                    <?php endif; ?>
                                </span>
                            </span>
                        </div>

                        <a class="tourbi-home-featured__button" href="<?php echo esc_url( $featured_url ); ?>">
                            <span aria-hidden="true">▣</span>
                            <?php esc_html_e( 'Explore Dates', 'torby' ); ?>
                        </a>
                    </div>

                    <div class="tourbi-home-featured__benefits" aria-label="<?php esc_attr_e( 'Experience benefits', 'torby' ); ?>">
                        <div>
                            <span aria-hidden="true">♨</span>
                            <p><strong><?php esc_html_e( 'Brunch & Ride', 'torby' ); ?></strong><small><?php esc_html_e( 'Food, friends, and city routes', 'torby' ); ?></small></p>
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
                    <h2><?php esc_html_e( 'Endless Ways to Ride Together', 'torby' ); ?></h2>
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
                                            <span>
                                                <b aria-hidden="true">◇</b>
                                                <span class="tourbi-card-price-per-person">
                                                    <?php echo wp_kses_post( $card['price_html'] ); ?>
                                                    <?php if ( (float) ( $card['price'] ?? 0 ) > 0 ) : ?>
                                                        <small class="tourbi-card-price-per-person__suffix">/person</small>
                                                    <?php endif; ?>
                                                </span>
                                            </span>
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
                    <p class="tourbi-home-adventures__note"><?php esc_html_e( 'And more. Create your own.', 'torby' ); ?></p>
                <?php else : ?>
                    <div class="tourbi-home-empty">
                        <strong><?php esc_html_e( 'New adventures are coming soon.', 'torby' ); ?></strong>
                        <p><?php esc_html_e( 'Published guided Experiences will appear here automatically in newest-first order.', 'torby' ); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="tourbi-home-trust" aria-label="<?php esc_attr_e( 'Why Tourbi', 'torby' ); ?>">
            <div class="tourbi-home-shell tourbi-home-trust__grid">
                <div class="tourbi-home-trust__item">
                    <span aria-hidden="true">▣</span>
                    <p><strong><?php esc_html_e( 'E-bikes, helmets & locks provided', 'torby' ); ?></strong><small><?php esc_html_e( 'Everything needed for a smooth hosted ride.', 'torby' ); ?></small></p>
                </div>
                <div class="tourbi-home-trust__item">
                    <span aria-hidden="true">♢</span>
                    <p><strong><?php esc_html_e( 'Pick up & drop off in one easy location', 'torby' ); ?></strong><small><?php esc_html_e( 'All experiences begin at King Electric Bike Shop.', 'torby' ); ?></small></p>
                </div>
                <div class="tourbi-home-trust__item">
                    <span aria-hidden="true">◎</span>
                    <p><strong><?php esc_html_e( 'Safe. Fun. Unforgettable.', 'torby' ); ?></strong><small><?php esc_html_e( 'Simple experiences built around shared memories.', 'torby' ); ?></small></p>
                </div>
            </div>
        </section>
    </main>
</div>
