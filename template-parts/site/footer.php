<?php
/**
 * Tourbi custom site footer.
 *
 * @package Torby
 */

$rent_url = $args['rent_url'] ?? home_url( '/rent/' );
$account_url = $args['account_url'] ?? home_url( '/my-account/' );
$is_host = ! empty( $args['is_host'] );

$footer_pages = array(
    'privacy-policy' => __(
        'Privacy Policy',
        'torby'
    ),
    'refund-policy' => __(
        'Refund Policy',
        'torby'
    ),
    'terms-of-service' => __(
        'Terms of Service',
        'torby'
    ),
    'shipping-policy' => __(
        'Shipping Policy',
        'torby'
    ),
    'contact-us' => __(
        'Contact Us',
        'torby'
    ),
);
?>
<footer class="tourbi-site-footer">
    <div class="tourbi-site-footer__main">
        <div class="tourbi-site-footer__brand">
            <a
                class="tourbi-site-logo tourbi-site-logo--footer"
                href="<?php echo esc_url( home_url( '/' ) ); ?>"
            >
                <?php
                echo wp_kses_post(
                    tourbi_theme_get_site_logo_markup()
                );
                ?>
            </a>

            <p>
                <?php
                esc_html_e(
                    'Explore the city by e-bike, book memorable local Experiences, or share your own route as a Tourbi Host.',
                    'torby'
                );
                ?>
            </p>

            <a
                class="tourbi-site-footer__email"
                href="mailto:<?php echo esc_attr( get_option( 'admin_email' ) ); ?>"
            >
                <?php echo esc_html( get_option( 'admin_email' ) ); ?>
            </a>
        </div>

        <div class="tourbi-site-footer__column">
            <h2><?php esc_html_e( 'Explore', 'torby' ); ?></h2>
            <a href="<?php echo esc_url( tourbi_theme_get_experience_archive_url() ); ?>">
                <?php esc_html_e( 'Experiences', 'torby' ); ?>
            </a>
            <a href="<?php echo esc_url( $rent_url ); ?>">
                <?php esc_html_e( 'Bike Rentals', 'torby' ); ?>
            </a>
            <a href="<?php echo esc_url( tourbi_theme_get_become_host_url() ); ?>">
                <?php esc_html_e( 'Become a Host', 'torby' ); ?>
            </a>
        </div>

        <div class="tourbi-site-footer__column">
            <h2><?php esc_html_e( 'Account', 'torby' ); ?></h2>
            <a href="<?php echo esc_url( $account_url ); ?>">
                <?php esc_html_e( 'My Account', 'torby' ); ?>
            </a>

            <?php if ( $is_host ) : ?>
                <a href="<?php echo esc_url( tourbi_theme_get_host_dashboard_url() ); ?>">
                    <?php esc_html_e( 'Host Dashboard', 'torby' ); ?>
                </a>
                <a href="<?php echo esc_url( tourbi_theme_get_host_builder_url() ); ?>">
                    <?php esc_html_e( 'Experience Builder', 'torby' ); ?>
                </a>
            <?php else : ?>
                <a href="<?php echo esc_url( tourbi_theme_get_vendor_registration_url() ); ?>">
                    <?php esc_html_e( 'Host Application', 'torby' ); ?>
                </a>
            <?php endif; ?>
        </div>

        <div class="tourbi-site-footer__column">
            <h2><?php esc_html_e( 'Information', 'torby' ); ?></h2>

            <?php foreach ( $footer_pages as $slug => $label ) : ?>
                <?php
                $page = get_page_by_path(
                    $slug,
                    OBJECT,
                    'page'
                );

                if ( ! $page instanceof WP_Post ) {
                    continue;
                }
                ?>
                <a href="<?php echo esc_url( get_permalink( $page ) ); ?>">
                    <?php echo esc_html( $label ); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="tourbi-site-footer__bottom">
        <p>
            <?php
            echo esc_html(
                sprintf(
                    /* translators: 1: Year. 2: Site name. */
                    __(
                        '© %1$s %2$s. All rights reserved.',
                        'torby'
                    ),
                    wp_date( 'Y' ),
                    get_bloginfo( 'name' )
                )
            );
            ?>
        </p>

        <p>
            <?php esc_html_e( 'Host share 85% · Tourbi share 15%', 'torby' ); ?>
        </p>
    </div>
</footer>
