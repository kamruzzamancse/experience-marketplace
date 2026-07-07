<?php
/**
 * Final Host CTA.
 *
 * @package Torby
 */

$host_page = $args['host_page'] ?? array();
$primary = $host_page['primary_cta'] ?? array();
?>
<section class="tourbi-host-final-cta">
    <div class="tourbi-shell--wide">
        <div class="tourbi-host-final-cta__card">
            <div>
                <span class="tourbi-host-kicker">
                    <?php esc_html_e( 'Ready to start hosting?', 'torby' ); ?>
                </span>

                <h2>
                    <?php esc_html_e( 'Apply to become a Host.', 'torby' ); ?>
                </h2>

                <p>
                    <?php esc_html_e( 'Bring your idea, route, and local energy. Tourbi will help power the ride with e-bikes and booking tools.', 'torby' ); ?>
                </p>
            </div>

            <a
                class="tourbi-button tourbi-button--lime tourbi-button--large"
                href="<?php echo esc_url( $primary['url'] ?? '#' ); ?>"
            >
                <?php esc_html_e( 'Apply to Become a Host', 'torby' ); ?>
                <span aria-hidden="true">→</span>
            </a>
        </div>
    </div>
</section>
