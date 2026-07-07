<?php
/**
 * Template Name: Tourbi Experience Marketplace
 * Template Post Type: page
 *
 * Dynamic reference-matched Experience landing page.
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$page_id  = get_queried_object_id();
$settings = function_exists( 'tourbi_showcase_get_page_settings' )
    ? tourbi_showcase_get_page_settings( $page_id )
    : array();
$state    = function_exists( 'tourbi_showcase_get_filter_state' )
    ? tourbi_showcase_get_filter_state()
    : array(
        'search'      => '',
        'category'    => '',
        'location'    => '',
        'sort'        => 'recommended',
        'has_filters' => false,
    );
$sections = function_exists( 'tourbi_showcase_get_sections' )
    ? tourbi_showcase_get_sections( $state )
    : array();

/*
 * Keep the requested, fixed child-theme path as the first and preferred hero
 * source. A real <img> element is rendered below instead of relying only on a
 * CSS background variable, which avoids path/escaping issues on local Windows
 * installations and on hosts with aggressive CSS optimisation.
 */
$hero_relative = 'assets/images/experience-hero-image.png';
$hero_file     = wp_normalize_path( trailingslashit( get_stylesheet_directory() ) . $hero_relative );
$hero          = trailingslashit( get_stylesheet_directory_uri() ) . $hero_relative;

if ( ! is_readable( $hero_file ) ) {
    $hero = get_the_post_thumbnail_url( $page_id, 'full' );
}

if ( ! $hero ) {
    $hero = trailingslashit( get_stylesheet_directory_uri() ) . 'assets/images/tourbi-home/hero-reference.jpg';
}

$base_url = function_exists( 'tourbi_theme_get_experience_archive_url' )
    ? tourbi_theme_get_experience_archive_url()
    : get_permalink( $page_id );
$cta_url = ! empty( $settings['cta_url'] ) ? $settings['cta_url'] : '#tourbi-experience-sections';
$is_filter = ! empty( $state['has_filters'] );

$taxonomy  = function_exists( 'tourbi_showcase_category_taxonomy' )
    ? tourbi_showcase_category_taxonomy()
    : 'tourbi_experience_category';
$categories = taxonomy_exists( $taxonomy )
    ? get_terms(
        array(
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
        )
    )
    : array();
$categories = is_wp_error( $categories ) ? array() : $categories;

$locations = function_exists( 'tourbi_theme_get_marketplace_locations' )
    ? tourbi_theme_get_marketplace_locations()
    : array();
$sort_options = function_exists( 'tourbi_theme_get_marketplace_sort_options' )
    ? tourbi_theme_get_marketplace_sort_options()
    : array(
        'recommended'    => __( 'Recommended', 'torby' ),
        'newest'         => __( 'Newest', 'torby' ),
        'price_low'      => __( 'Price: Low to High', 'torby' ),
        'price_high'     => __( 'Price: High to Low', 'torby' ),
        'duration_short' => __( 'Shortest Duration', 'torby' ),
    );

$active_filter_count = 0;
$active_filter_count += ! empty( $state['search'] ) ? 1 : 0;
$active_filter_count += ! empty( $state['category'] ) ? 1 : 0;
$active_filter_count += ! empty( $state['location'] ) ? 1 : 0;
$active_filter_count += ! empty( $state['sort'] ) && 'recommended' !== $state['sort'] ? 1 : 0;
?>
<main id="primary" class="tourbi-experience-showcase">
    <section
        class="tourbi-showcase-hero"
        style="--tourbi-showcase-hero-position:<?php echo esc_attr( $settings['hero_image_position'] ?? 'center center' ); ?>;"
        aria-labelledby="tourbi-showcase-title"
    >
        <img
            class="tourbi-showcase-hero__image"
            src="<?php echo esc_url( $hero ); ?>"
            alt=""
            loading="eager"
            decoding="async"
            fetchpriority="high"
        >

        <div class="tourbi-showcase-shell tourbi-showcase-hero__inner">
            <div class="tourbi-showcase-hero__copy">
                <h1 id="tourbi-showcase-title">
                    <span><?php echo esc_html( $settings['hero_title_top'] ?? __( 'Electric Bike', 'torby' ) ); ?></span>
                    <strong><?php echo esc_html( $settings['hero_title_accent'] ?? __( 'Adventures.', 'torby' ) ); ?></strong>
                </h1>

                <p class="tourbi-showcase-hero__subtitle">
                    <?php echo esc_html( $settings['hero_subtitle'] ?? __( 'Ride. Explore. Connect.', 'torby' ) ); ?>
                </p>

                <div class="tourbi-showcase-benefits" aria-label="<?php esc_attr_e( 'Experience benefits', 'torby' ); ?>">
                    <?php for ( $i = 1; $i <= 3; $i++ ) : ?>
                        <div class="tourbi-showcase-benefit tourbi-showcase-benefit--<?php echo esc_attr( $i ); ?>">
                            <span class="tourbi-showcase-benefit__icon">
                                <?php echo function_exists( 'tourbi_showcase_icon_svg' ) ? tourbi_showcase_icon_svg( $settings[ 'benefit_' . $i . '_icon' ] ?? 'star' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            </span>
                            <p>
                                <strong><?php echo esc_html( $settings[ 'benefit_' . $i . '_title' ] ?? '' ); ?></strong>
                                <small><?php echo esc_html( $settings[ 'benefit_' . $i . '_text' ] ?? '' ); ?></small>
                            </p>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </section>

    <?php
    /*
     * Launch UI: keep the dynamic search/filter engine in the theme, but hide
     * the public filter bar for now because the starting Experience inventory
     * is intentionally small. Query-string filters still work if needed later.
     */
    ?>

    <section id="tourbi-experience-sections" class="tourbi-showcase-content">
        <div class="tourbi-showcase-shell">
            <?php if ( $is_filter ) : ?>
                <div class="tourbi-showcase-filter-bar">
                    <p>
                        <strong><?php esc_html_e( 'Filtered experiences', 'torby' ); ?></strong>
                        <span><?php esc_html_e( 'Results are loaded directly from your published Experience database.', 'torby' ); ?></span>
                    </p>
                    <a href="<?php echo esc_url( $base_url ); ?>">
                        <span aria-hidden="true">×</span>
                        <?php esc_html_e( 'Reset all', 'torby' ); ?>
                    </a>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $sections ) ) : ?>
                <?php foreach ( $sections as $section ) : ?>
                    <?php
                    $accent   = sanitize_hex_color( $section['accent'] ?? '' ) ?: '#9b42e6';
                    $term     = $section['term'] ?? null;
                    $view_url = $base_url;

                    if ( $term instanceof WP_Term ) {
                        $view_args = array( 'experience_category' => $term->slug );
                        if ( ! empty( $state['search'] ) ) {
                            $view_args['experience_search'] = sanitize_text_field( $state['search'] );
                        }
                        if ( ! empty( $state['location'] ) ) {
                            $view_args['experience_location'] = sanitize_text_field( $state['location'] );
                        }
                        if ( ! empty( $state['sort'] ) && 'recommended' !== $state['sort'] ) {
                            $view_args['experience_sort'] = sanitize_key( $state['sort'] );
                        }
                        $view_url = add_query_arg( $view_args, $base_url );
                    }
                    ?>
                    <section class="tourbi-showcase-section" style="--tourbi-showcase-accent:<?php echo esc_attr( $accent ); ?>;">
                        <header class="tourbi-showcase-section__header">
                            <div class="tourbi-showcase-section__title">
                                <span class="tourbi-showcase-section__icon">
                                    <?php echo function_exists( 'tourbi_showcase_icon_svg' ) ? tourbi_showcase_icon_svg( $section['icon'] ?? 'star' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                </span>
                                <h2><?php echo esc_html( $section['title'] ?? '' ); ?></h2>
                            </div>

                            <?php if ( ! $is_filter && $term instanceof WP_Term ) : ?>
                                <a class="tourbi-showcase-section__view" href="<?php echo esc_url( $view_url ); ?>">
                                    <?php esc_html_e( 'View all', 'torby' ); ?>
                                </a>
                            <?php endif; ?>
                        </header>

                        <div class="tourbi-showcase-grid">
                            <?php foreach ( (array) ( $section['ids'] ?? array() ) as $experience_id ) : ?>
                                <?php
                                $card = function_exists( 'tourbi_showcase_get_card' )
                                    ? tourbi_showcase_get_card( $experience_id )
                                    : array();

                                if ( empty( $card ) ) {
                                    continue;
                                }
                                ?>
                                <article class="tourbi-showcase-card">
                                    <a class="tourbi-showcase-card__media" href="<?php echo esc_url( $card['permalink'] ); ?>" aria-label="<?php echo esc_attr( $card['title'] ); ?>">
                                        <img
                                            src="<?php echo esc_url( $card['image'] ); ?>"
                                            alt="<?php echo esc_attr( $card['title'] ); ?>"
                                            loading="lazy"
                                            decoding="async"
                                            <?php if ( ! empty( $card['image_srcset'] ) ) : ?>
                                                srcset="<?php echo esc_attr( $card['image_srcset'] ); ?>"
                                                sizes="(max-width: 680px) 100vw, (max-width: 1100px) 50vw, 44vw"
                                            <?php endif; ?>
                                        >

                                        <?php if ( ! empty( $card['badge'] ) ) : ?>
                                            <span class="tourbi-showcase-card__badge" style="--tourbi-card-badge:<?php echo esc_attr( $card['badge_color'] ); ?>;">
                                                <?php echo esc_html( $card['badge'] ); ?>
                                            </span>
                                        <?php endif; ?>
                                    </a>

                                    <div class="tourbi-showcase-card__body">
                                        <h3>
                                            <a href="<?php echo esc_url( $card['permalink'] ); ?>">
                                                <?php echo esc_html( $card['title'] ); ?>
                                            </a>
                                        </h3>

                                        <?php if ( ! empty( $card['summary'] ) ) : ?>
                                            <p class="tourbi-showcase-card__summary">
                                                <?php echo esc_html( $card['summary'] ); ?>
                                            </p>
                                        <?php endif; ?>

                                        <div class="tourbi-showcase-card__footer">
                                            <div class="tourbi-showcase-card__facts">
                                                <?php if ( ! empty( $card['duration'] ) ) : ?>
                                                    <span class="tourbi-showcase-card__duration">
                                                        <?php echo function_exists( 'tourbi_showcase_icon_svg' ) ? tourbi_showcase_icon_svg( 'clock' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                                        <?php echo esc_html( $card['duration'] ); ?>
                                                    </span>
                                                <?php endif; ?>

                                                <?php if ( (float) $card['rating'] > 0 || (int) $card['reviews'] > 0 ) : ?>
                                                    <span class="tourbi-showcase-card__rating" aria-label="<?php echo esc_attr( sprintf( __( '%1$s out of 5 stars from %2$d reviews', 'torby' ), number_format_i18n( (float) $card['rating'], 1 ), (int) $card['reviews'] ) ); ?>">
                                                        <span class="tourbi-showcase-card__stars" aria-hidden="true">
                                                            <?php for ( $star = 1; $star <= 5; $star++ ) : ?>
                                                                <b class="<?php echo $star <= round( (float) $card['rating'] ) ? 'is-active' : ''; ?>">★</b>
                                                            <?php endfor; ?>
                                                        </span>
                                                        <?php if ( (int) $card['reviews'] > 0 ) : ?>
                                                            <small>(<?php echo esc_html( number_format_i18n( (int) $card['reviews'] ) ); ?>)</small>
                                                        <?php endif; ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>

                                            <?php if ( ! empty( $card['price_html'] ) ) : ?>
                                                <strong class="tourbi-showcase-card__price">
                                                    <?php echo wp_kses_post( $card['price_html'] ); ?><?php if ( (float) ( $card['price'] ?? 0 ) > 0 ) : ?><small class="tourbi-showcase-card__price-suffix">/person</small><?php endif; ?>
                                                </strong>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="tourbi-showcase-empty">
                    <span><?php echo function_exists( 'tourbi_showcase_icon_svg' ) ? tourbi_showcase_icon_svg( 'bike' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                    <h2><?php echo esc_html( $is_filter ? __( 'No matching experiences found.', 'torby' ) : ( $settings['empty_title'] ?? __( 'New adventures are coming soon.', 'torby' ) ) ); ?></h2>
                    <p><?php echo esc_html( $is_filter ? __( 'Try a different keyword, category, location, or sort option.', 'torby' ) : ( $settings['empty_text'] ?? '' ) ); ?></p>
                    <?php if ( $is_filter ) : ?>
                        <a href="<?php echo esc_url( $base_url ); ?>"><?php esc_html_e( 'Clear all filters', 'torby' ); ?></a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <aside class="tourbi-showcase-cta">
                <div class="tourbi-showcase-cta__copy">
                    <span class="tourbi-showcase-cta__icon">
                        <?php echo function_exists( 'tourbi_showcase_icon_svg' ) ? tourbi_showcase_icon_svg( $settings['cta_icon'] ?? 'calendar' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </span>
                    <p>
                        <strong><?php echo esc_html( $settings['cta_title'] ?? __( 'Book Your Adventure Today', 'torby' ) ); ?></strong>
                        <small><?php echo esc_html( $settings['cta_text'] ?? '' ); ?></small>
                    </p>
                </div>

                <a class="tourbi-showcase-cta__button" href="<?php echo esc_url( $cta_url ); ?>">
                    <?php echo esc_html( $settings['cta_button'] ?? __( 'Explore Dates', 'torby' ) ); ?>
                    <?php echo function_exists( 'tourbi_showcase_icon_svg' ) ? tourbi_showcase_icon_svg( 'arrow' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </a>
            </aside>
        </div>
    </section>
</main>
<?php
get_footer();
