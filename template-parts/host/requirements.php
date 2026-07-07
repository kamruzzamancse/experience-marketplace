<?php
/**
 * Host included equipment and support.
 *
 * @package Torby
 */

$host_page = $args['host_page'] ?? array();
?>
<section class="tourbi-host-section tourbi-host-requirements tourbi-host-included">
    <div class="tourbi-shell--wide tourbi-host-requirements__layout">
        <div class="tourbi-host-requirements__copy">
            <span class="tourbi-host-kicker">
                <?php esc_html_e( 'Every Booking Includes', 'torby' ); ?>
            </span>

            <h2>
                <?php esc_html_e( 'The essentials are already covered.', 'torby' ); ?>
            </h2>

            <p>
                <?php esc_html_e( 'Guests can focus on the experience while Tourbi supports the ride with reliable e-bikes, safety basics, and a consistent pickup location.', 'torby' ); ?>
            </p>

            <a
                class="tourbi-button tourbi-button--primary"
                href="<?php echo esc_url( $host_page['primary_cta']['url'] ?? '#' ); ?>"
            >
                <?php esc_html_e( 'Apply to Become a Host', 'torby' ); ?>
                <span aria-hidden="true">→</span>
            </a>
        </div>

        <div class="tourbi-host-requirement-list tourbi-host-included-list">
            <article>
                <span>01</span>
                <div>
                    <h3><?php esc_html_e( 'Premium e-bike', 'torby' ); ?></h3>
                    <p><?php esc_html_e( 'Each booking can include Tourbi e-bikes based on the host’s required quantity and schedule.', 'torby' ); ?></p>
                </div>
            </article>

            <article>
                <span>02</span>
                <div>
                    <h3><?php esc_html_e( 'Helmet', 'torby' ); ?></h3>
                    <p><?php esc_html_e( 'Basic safety gear is part of the ride setup for a smoother guest experience.', 'torby' ); ?></p>
                </div>
            </article>

            <article>
                <span>03</span>
                <div>
                    <h3><?php esc_html_e( 'Lock', 'torby' ); ?></h3>
                    <p><?php esc_html_e( 'Locks are available for stops, food breaks, and route moments that need flexibility.', 'torby' ); ?></p>
                </div>
            </article>

            <article>
                <span>04</span>
                <div>
                    <h3><?php esc_html_e( 'Pickup & drop-off', 'torby' ); ?></h3>
                    <p><?php esc_html_e( 'All experiences begin and end at King Electric Bike Shop for a clear guest flow.', 'torby' ); ?></p>
                </div>
            </article>
        </div>
    </div>
</section>
