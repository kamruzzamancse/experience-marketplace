<?php
/**
 * Template Name: Tourbi Experience Marketplace
 * Template Post Type: page
 *
 * Custom Experience Marketplace with server-side search and filters.
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$state = function_exists(
    'tourbi_theme_get_marketplace_state'
)
    ? tourbi_theme_get_marketplace_state()
    : array();

$featured = function_exists(
    'tourbi_theme_get_marketplace_featured_experience'
)
    ? tourbi_theme_get_marketplace_featured_experience(
        $state
    )
    : array();

$featured_id = absint(
    $featured['id'] ?? 0
);

$experience_query = function_exists(
    'tourbi_theme_get_marketplace_query'
)
    ? tourbi_theme_get_marketplace_query(
        $state,
        $featured_id
    )
    : new WP_Query();

$categories = taxonomy_exists(
    defined(
        'TOURBI_CORE_EXPERIENCE_CATEGORY_TAXONOMY'
    )
        ? TOURBI_CORE_EXPERIENCE_CATEGORY_TAXONOMY
        : 'tourbi_experience_category'
)
    ? get_terms(
        array(
            'taxonomy'   => defined(
                'TOURBI_CORE_EXPERIENCE_CATEGORY_TAXONOMY'
            )
                ? TOURBI_CORE_EXPERIENCE_CATEGORY_TAXONOMY
                : 'tourbi_experience_category',
            'hide_empty' => true,
            'orderby'    => 'name',
            'order'      => 'ASC',
        )
    )
    : array();

if ( is_wp_error( $categories ) ) {
    $categories = array();
}

$locations = function_exists(
    'tourbi_theme_get_marketplace_locations'
)
    ? tourbi_theme_get_marketplace_locations()
    : array();

$display_count = absint(
    $experience_query->found_posts
) + ( $featured_id ? 1 : 0 );
?>
<main
    id="primary"
    class="tourbi-app tourbi-marketplace"
>
    <section class="tourbi-marketplace-intro">
        <div class="tourbi-shell--wide tourbi-marketplace-intro__inner">
            <div class="tourbi-marketplace-intro__location">
                <span
                    class="tourbi-marketplace-intro__bike"
                    aria-hidden="true"
                >◎</span>

                <div>
                    <small>
                        <?php esc_html_e( 'Discover experiences in', 'torby' ); ?>
                    </small>

                    <strong>
                        <?php echo esc_html( tourbi_theme_get_location_label() ); ?>
                    </strong>
                </div>
            </div>

            <a
                class="tourbi-marketplace-host-cta"
                href="<?php echo esc_url( tourbi_theme_get_become_host_url() ); ?>"
            >
                <span
                    class="tourbi-marketplace-host-cta__icon"
                    aria-hidden="true"
                >◯</span>

                <span>
                    <strong>
                        <?php esc_html_e( 'Host Your Own Experience', 'torby' ); ?>
                    </strong>

                    <small>
                        <?php esc_html_e( 'Share your idea. We will help make it memorable.', 'torby' ); ?>
                    </small>
                </span>

                <b aria-hidden="true">→</b>
            </a>
        </div>
    </section>

    <?php
    get_template_part(
        'template-parts/marketplace/featured',
        null,
        array(
            'experience' => $featured,
        )
    );
    ?>

    <section class="tourbi-marketplace-results">
        <div class="tourbi-shell--wide">
            <div class="tourbi-marketplace-results__heading">
                <div>
                    <span class="tourbi-kicker">
                        <?php esc_html_e( 'Explore Tourbi', 'torby' ); ?>
                    </span>

                    <h1 class="tourbi-section-title">
                        <?php
                        echo ! empty( $state['has_filters'] )
                            ? esc_html__(
                                'Experiences matching your search.',
                                'torby'
                            )
                            : esc_html__(
                                'View more adventures.',
                                'torby'
                            );
                        ?>
                    </h1>

                    <p>
                        <?php
                        echo esc_html(
                            sprintf(
                                /* translators: %d: Experience result count. */
                                _n(
                                    '%d Experience available',
                                    '%d Experiences available',
                                    $display_count,
                                    'torby'
                                ),
                                $display_count
                            )
                        );
                        ?>
                    </p>
                </div>

                <button
                    type="button"
                    class="tourbi-button tourbi-button--outline tourbi-marketplace-filter-toggle"
                    data-tourbi-filter-open
                    aria-controls="tourbi-marketplace-filters"
                    aria-expanded="false"
                >
                    <span aria-hidden="true">☰</span>
                    <?php esc_html_e( 'Search & Filters', 'torby' ); ?>
                </button>
            </div>

            <?php
            get_template_part(
                'template-parts/marketplace/filters',
                null,
                array(
                    'state'      => $state,
                    'categories' => $categories,
                    'locations'  => $locations,
                )
            );
            ?>

            <?php if ( $experience_query->have_posts() ) : ?>
                <div class="tourbi-marketplace-grid">
                    <?php
                    $card_index = 0;

                    while (
                        $experience_query->have_posts()
                    ) :
                        $experience_query->the_post();

                        $card = function_exists(
                            'tourbi_theme_get_marketplace_card'
                        )
                            ? tourbi_theme_get_marketplace_card(
                                get_the_ID()
                            )
                            : array();

                        get_template_part(
                            'template-parts/marketplace/experience-card',
                            null,
                            array(
                                'experience' => $card,
                                'index'      => $card_index,
                            )
                        );

                        $card_index++;
                    endwhile;

                    wp_reset_postdata();
                    ?>
                </div>

                <?php
                tourbi_theme_render_marketplace_pagination(
                    $experience_query,
                    $state
                );
                ?>
            <?php else : ?>
                <?php
                get_template_part(
                    'template-parts/marketplace/empty-state',
                    null,
                    array(
                        'state' => $state,
                    )
                );
                ?>
            <?php endif; ?>
        </div>
    </section>

    <section class="tourbi-marketplace-trust">
        <div class="tourbi-shell--wide tourbi-marketplace-trust__grid">
            <div>
                <span aria-hidden="true">◎</span>
                <strong><?php esc_html_e( 'Top Quality E-Bikes', 'torby' ); ?></strong>
                <small><?php esc_html_e( 'Reliable, comfortable, and ready to ride.', 'torby' ); ?></small>
            </div>

            <div>
                <span aria-hidden="true">✓</span>
                <strong><?php esc_html_e( 'Safe & Reliable', 'torby' ); ?></strong>
                <small><?php esc_html_e( 'Protected inventory and secure checkout.', 'torby' ); ?></small>
            </div>

            <div>
                <span aria-hidden="true">◯</span>
                <strong><?php esc_html_e( 'Local Vibes', 'torby' ); ?></strong>
                <small><?php esc_html_e( 'Real hosts, local stories, memorable routes.', 'torby' ); ?></small>
            </div>

            <div>
                <span aria-hidden="true">♥</span>
                <strong><?php esc_html_e( 'Good People', 'torby' ); ?></strong>
                <small><?php esc_html_e( 'Small groups and shared experiences.', 'torby' ); ?></small>
            </div>
        </div>
    </section>
</main>
<?php
get_footer();
