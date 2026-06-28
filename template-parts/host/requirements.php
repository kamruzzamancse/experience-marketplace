<?php
/**
 * Host requirements and standards.
 *
 * @package Torby
 */

$host_page = $args['host_page'] ?? array();
?>
<section class="tourbi-host-section tourbi-host-requirements">
    <div class="tourbi-shell--wide tourbi-host-requirements__layout">
        <div class="tourbi-host-requirements__copy">
            <span class="tourbi-host-kicker">
                <?php esc_html_e( 'Host Standards', 'torby' ); ?>
            </span>

            <h2>
                <?php esc_html_e( 'A great Experience begins with preparation and care.', 'torby' ); ?>
            </h2>

            <p>
                <?php
                esc_html_e(
                    'Tourbi reviews every submission before publication. Hosts should provide accurate information, reliable communication, and a safe guest experience.',
                    'torby'
                );
                ?>
            </p>

            <a
                class="tourbi-button tourbi-button--primary"
                href="<?php echo esc_url( $host_page['primary_cta']['url'] ?? '#' ); ?>"
            >
                <?php echo esc_html( $host_page['primary_cta']['label'] ?? '' ); ?>
                <span aria-hidden="true">→</span>
            </a>
        </div>

        <div class="tourbi-host-requirement-list">
            <article>
                <span>01</span>
                <div>
                    <h3><?php esc_html_e( 'Know Your Route', 'torby' ); ?></h3>
                    <p><?php esc_html_e( 'Use a route you understand well and provide accurate meeting, duration, difficulty, and itinerary information.', 'torby' ); ?></p>
                </div>
            </article>

            <article>
                <span>02</span>
                <div>
                    <h3><?php esc_html_e( 'Put Safety First', 'torby' ); ?></h3>
                    <p><?php esc_html_e( 'Give a clear safety briefing, follow local rules, use appropriate equipment, and match the Experience to guest ability.', 'torby' ); ?></p>
                </div>
            </article>

            <article>
                <span>03</span>
                <div>
                    <h3><?php esc_html_e( 'Communicate Reliably', 'torby' ); ?></h3>
                    <p><?php esc_html_e( 'Monitor bookings, arrive prepared, and communicate important updates through the Host workflow.', 'torby' ); ?></p>
                </div>
            </article>

            <article>
                <span>04</span>
                <div>
                    <h3><?php esc_html_e( 'Respect Every Guest', 'torby' ); ?></h3>
                    <p><?php esc_html_e( 'Create an inclusive, professional atmosphere and clearly explain what is included, excluded, and expected.', 'torby' ); ?></p>
                </div>
            </article>
        </div>
    </div>
</section>
