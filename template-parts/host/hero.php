<?php
/**
 * Become a Host hero with compact earnings calculator.
 *
 * @package Torby
 */

$host_page = $args['host_page'] ?? array();
$image     = $host_page['hero_image'] ?? '';
$primary   = $host_page['primary_cta'] ?? array();
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
                <?php esc_html_e( 'Create something fun.', 'torby' ); ?>
            </h1>

            <p>
                <?php esc_html_e( 'You create it, we provide the e-bikes. Together we turn great ideas into memories.', 'torby' ); ?>
            </p>

            <div class="tourbi-host-hero__actions">
                <a
                    class="tourbi-button tourbi-button--primary tourbi-button--large"
                    href="<?php echo esc_url( $primary['url'] ?? '#' ); ?>"
                >
                    <?php esc_html_e( 'Start Hosting', 'torby' ); ?>
                    <span aria-hidden="true">→</span>
                </a>

                <a
                    class="tourbi-button tourbi-button--glass tourbi-button--large"
                    href="<?php echo esc_url( $secondary['url'] ?? home_url( '/experiences/' ) ); ?>"
                >
                    <?php esc_html_e( 'Explore Experiences', 'torby' ); ?>
                </a>
            </div>

            <small class="tourbi-host-hero__note">
                <span aria-hidden="true">✓</span>
                <?php esc_html_e( '$18 per bike per hour + 5% service fee.', 'torby' ); ?>
            </small>
        </div>

        <div class="tourbi-host-hero__calculator">
            <?php if ( function_exists( 'tourbi_theme_render_host_income_calculator' ) ) : ?>
                <?php
                echo tourbi_theme_render_host_income_calculator(
                    array(
                        'variant'             => 'hero',
                        'max_width'           => 500,
                        'price_min'           => 20,
                        'price_default'       => 40,
                        'guests_default'      => 4,
                        'experiences_default' => 2,
                    )
                ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                ?>
            <?php else : ?>
                <aside class="tourbi-host-earning-card tourbi-host-earning-card--pricing">
                    <span><?php esc_html_e( 'Simple pricing', 'torby' ); ?></span>
                    <strong>$18</strong>
                    <p>
                        <?php esc_html_e( 'Per bike per hour, plus a 5% Tourbi service fee.', 'torby' ); ?>
                    </p>
                    <div>
                        <span><b>$18/hr</b><?php esc_html_e( 'E-bike', 'torby' ); ?></span>
                        <span><b>5%</b><?php esc_html_e( 'Fee', 'torby' ); ?></span>
                    </div>
                </aside>
            <?php endif; ?>
        </div>
    </div>

    <div class="tourbi-host-hero__facts">
        <div class="tourbi-shell--wide">
            <span><b aria-hidden="true">✓</b><?php esc_html_e( 'You create the experience', 'torby' ); ?></span>
            <span><b aria-hidden="true">✓</b><?php esc_html_e( 'We provide the bikes', 'torby' ); ?></span>
            <span><b aria-hidden="true">✓</b><?php esc_html_e( 'King Electric Bike Shop pickup', 'torby' ); ?></span>
            <span><b aria-hidden="true">✓</b><?php esc_html_e( 'Simple host dashboard', 'torby' ); ?></span>
        </div>
    </div>
</section>
