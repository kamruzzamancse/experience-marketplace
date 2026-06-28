<?php
/**
 * WCFM default store Products-tab override for Tourbi.
 *
 * For edit copy path:
 * child-theme/wcfm/store/wcfmmp-view-store-products.php
 *
 * This page intentionally lists the Host's guided Tourbi Experiences instead
 * of generic WooCommerce products.
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$vendor_id = 0;

if (
    isset( $store_user ) &&
    is_object( $store_user ) &&
    method_exists( $store_user, 'get_id' )
) {
    $vendor_id = absint( $store_user->get_id() );
}

if (
    ! $vendor_id &&
    function_exists( 'tourbi_theme_get_current_store_vendor_id' )
) {
    $vendor_id = tourbi_theme_get_current_store_vendor_id();
}

$host_name = __( 'This Host', 'torby' );

if (
    isset( $store_user ) &&
    is_object( $store_user ) &&
    method_exists( $store_user, 'get_shop_name' )
) {
    $resolved_name = sanitize_text_field(
        (string) $store_user->get_shop_name()
    );

    if ( '' !== $resolved_name ) {
        $host_name = $resolved_name;
    }
}

$paged = max( 1, absint( get_query_var( 'paged' ) ) );
$query = function_exists(
    'tourbi_theme_get_host_store_experience_query'
)
    ? tourbi_theme_get_host_store_experience_query(
        $vendor_id,
        $paged,
        6
    )
    : new WP_Query(
        array(
            'post_type'      => 'rbfw_item',
            'post__in'       => array( 0 ),
            'posts_per_page' => 1,
        )
    );
?>
<section class="tourbi-host-store-experiences">
    <header class="tourbi-host-store-experiences__header">
        <div>
            <span class="tourbi-host-store-experiences__eyebrow">
                <?php esc_html_e( 'Hosted Experiences', 'torby' ); ?>
            </span>
            <h2>
                <?php
                echo esc_html(
                    sprintf(
                        /* translators: %s: Host/store name. */
                        __( 'Experiences by %s', 'torby' ),
                        $host_name
                    )
                );
                ?>
            </h2>
            <p>
                <?php
                esc_html_e(
                    'Choose a guided e-bike Experience, review the route and details, then select an available date to book.',
                    'torby'
                );
                ?>
            </p>
        </div>

        <?php if ( function_exists( 'tourbi_theme_get_experience_archive_url' ) ) : ?>
            <a
                class="tourbi-host-store-experiences__browse"
                href="<?php echo esc_url( tourbi_theme_get_experience_archive_url() ); ?>"
            >
                <?php esc_html_e( 'Browse all Experiences', 'torby' ); ?>
                <span aria-hidden="true">→</span>
            </a>
        <?php endif; ?>
    </header>

    <?php if ( $query->have_posts() ) : ?>
        <div class="tourbi-host-store-experiences__grid">
            <?php
            $card_index = 0;

            while ( $query->have_posts() ) {
                $query->the_post();

                $experience_id = get_the_ID();
                $experience = function_exists(
                    'tourbi_theme_get_marketplace_card'
                )
                    ? tourbi_theme_get_marketplace_card(
                        $experience_id
                    )
                    : array();

                if ( empty( $experience['id'] ) ) {
                    continue;
                }

                get_template_part(
                    'template-parts/marketplace/experience-card',
                    null,
                    array(
                        'experience' => $experience,
                        'index'      => $card_index,
                    )
                );

                $card_index++;
            }
            ?>
        </div>

        <?php
        if ( function_exists( 'tourbi_theme_render_host_store_experience_pagination' ) ) {
            tourbi_theme_render_host_store_experience_pagination(
                $query,
                $vendor_id
            );
        }
        ?>
    <?php else : ?>
        <div class="tourbi-host-store-experiences__empty">
            <span class="tourbi-host-store-experiences__empty-icon" aria-hidden="true">🚲</span>
            <h3><?php esc_html_e( 'No published Experiences yet', 'torby' ); ?></h3>
            <p>
                <?php
                esc_html_e(
                    'This Host is preparing new guided rides. Explore the main marketplace to find another available Experience.',
                    'torby'
                );
                ?>
            </p>

            <?php if ( function_exists( 'tourbi_theme_get_experience_archive_url' ) ) : ?>
                <a
                    class="tourbi-host-store-experiences__empty-button"
                    href="<?php echo esc_url( tourbi_theme_get_experience_archive_url() ); ?>"
                >
                    <?php esc_html_e( 'Explore Experiences', 'torby' ); ?>
                    <span aria-hidden="true">→</span>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>
<?php
wp_reset_postdata();
