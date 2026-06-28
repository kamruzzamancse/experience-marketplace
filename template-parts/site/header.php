<?php
/**
 * Tourbi custom site header.
 *
 * @package Torby
 */

$navigation = $args['navigation'] ?? array();
$rent_url = $args['rent_url'] ?? home_url( '/rent/' );
$home_url = $args['home_url'] ?? home_url( '/' );
?>
<a
    class="tourbi-skip-link"
    href="#primary"
>
    <?php esc_html_e( 'Skip to content', 'torby' ); ?>
</a>

<header
    class="tourbi-site-header"
    data-tourbi-site-header
>
    <div class="tourbi-site-header__inner">
        <a
            class="tourbi-site-logo"
            href="<?php echo esc_url( $home_url ); ?>"
            aria-label="<?php esc_attr_e( 'Go to Tourbi home page', 'torby' ); ?>"
        >
            <?php
            echo wp_kses_post(
                tourbi_theme_get_site_logo_markup()
            );
            ?>
        </a>

        <button
            type="button"
            class="tourbi-site-nav-toggle"
            aria-expanded="false"
            aria-controls="tourbi-site-navigation"
            aria-label="<?php esc_attr_e( 'Open navigation', 'torby' ); ?>"
            data-tourbi-nav-toggle
        >
            <span></span>
            <span></span>
            <span></span>
        </button>

        <nav
            id="tourbi-site-navigation"
            class="tourbi-site-navigation"
            aria-label="<?php esc_attr_e( 'Primary navigation', 'torby' ); ?>"
            data-tourbi-navigation
        >
            <ul>
                <?php foreach ( $navigation as $item ) : ?>
                    <li>
                        <a
                            href="<?php echo esc_url( $item['url'] ); ?>"
                            class="<?php echo ! empty( $item['active'] ) ? 'is-active' : ''; ?>"
                            <?php echo ! empty( $item['active'] ) ? 'aria-current="page"' : ''; ?>
                        >
                            <?php echo esc_html( $item['label'] ); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <a
                class="tourbi-site-header__rent"
                href="<?php echo esc_url( $rent_url ); ?>"
            >
                <?php esc_html_e( 'Rent Now', 'torby' ); ?>
                <span aria-hidden="true">→</span>
            </a>
        </nav>
    </div>
</header>

<div
    class="tourbi-site-nav-backdrop"
    data-tourbi-nav-backdrop
    hidden
></div>
