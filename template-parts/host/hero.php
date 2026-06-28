<?php
/**
 * Become a Host hero.
 *
 * @package Torby
 */

$host_page = $args['host_page'] ?? array();
$image = $host_page['hero_image'] ?? '';
$primary = $host_page['primary_cta'] ?? array();
$secondary = $host_page['secondary_cta'] ?? array();
?>
<section
    class="tourbi-host-hero <?php echo $image ? 'has-image' : 'has-no-image'; ?>"
    <?php if ( $image ) : ?>
        style="--tourbi-host-hero-image:url('<?php echo esc_url( $image ); ?>');"
    <?php endif; ?>
>
    <div class="tourbi-host-hero__overlay"></div>

    <div class="tourbi-shell--wide tourbi-host-hero__inner">
        <div class="tourbi-host-hero__copy">
            <span class="tourbi-host-kicker">
                <?php esc_html_e( 'Host With Tourbi', 'torby' ); ?>
            </span>

            <h1>
                <?php esc_html_e( 'Turn your local knowledge into an unforgettable ride.', 'torby' ); ?>
            </h1>

            <p>
                <?php
                echo esc_html(
                    sprintf(
                        /* translators: %s: Location label. */
                        __(
                            'Design an e-bike Experience in %s, welcome guests from around the world, and let Tourbi handle the booking infrastructure.',
                            'torby'
                        ),
                        $host_page['location'] ?? ''
                    )
                );
                ?>
            </p>

            <div class="tourbi-host-hero__actions">
                <a
                    class="tourbi-button tourbi-button--primary tourbi-button--large"
                    href="<?php echo esc_url( $primary['url'] ?? '#' ); ?>"
                >
                    <?php echo esc_html( $primary['label'] ?? '' ); ?>
                    <span aria-hidden="true">→</span>
                </a>

                <a
                    class="tourbi-button tourbi-button--glass tourbi-button--large"
                    href="<?php echo esc_url( $secondary['url'] ?? '#' ); ?>"
                >
                    <?php echo esc_html( $secondary['label'] ?? '' ); ?>
                </a>
            </div>

            <?php if ( ! empty( $primary['note'] ) ) : ?>
                <small class="tourbi-host-hero__note">
                    <span aria-hidden="true">✓</span>
                    <?php echo esc_html( $primary['note'] ); ?>
                </small>
            <?php endif; ?>
        </div>

        <aside class="tourbi-host-earning-card">
            <span><?php esc_html_e( 'Host earning', 'torby' ); ?></span>
            <strong>85%</strong>
            <p>
                <?php
                esc_html_e(
                    'You receive 85% of eligible Experience revenue. Tourbi retains 15% for marketplace operations and support.',
                    'torby'
                );
                ?>
            </p>

            <div>
                <span>
                    <b>85%</b>
                    <?php esc_html_e( 'Host', 'torby' ); ?>
                </span>

                <span>
                    <b>15%</b>
                    <?php esc_html_e( 'Tourbi', 'torby' ); ?>
                </span>
            </div>
        </aside>
    </div>

    <div class="tourbi-host-hero__facts">
        <div class="tourbi-shell--wide">
            <span>
                <b aria-hidden="true">✓</b>
                <?php esc_html_e( 'Protected bike inventory', 'torby' ); ?>
            </span>

            <span>
                <b aria-hidden="true">✓</b>
                <?php esc_html_e( 'Secure customer checkout', 'torby' ); ?>
            </span>

            <span>
                <b aria-hidden="true">✓</b>
                <?php esc_html_e( 'Host booking dashboard', 'torby' ); ?>
            </span>

            <span>
                <b aria-hidden="true">✓</b>
                <?php esc_html_e( 'Admin quality review', 'torby' ); ?>
            </span>
        </div>
    </div>
</section>
