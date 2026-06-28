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
                    <?php
                    echo ! empty( $host_page['is_host'] )
                        ? esc_html__(
                            'Ready for your next listing?',
                            'torby'
                        )
                        : esc_html__(
                            'Your route can become someone’s favorite memory.',
                            'torby'
                        );
                    ?>
                </span>

                <h2>
                    <?php
                    echo ! empty( $host_page['is_host'] )
                        ? esc_html__(
                            'Create your next Tourbi Experience.',
                            'torby'
                        )
                        : esc_html__(
                            'Start your Tourbi Host journey today.',
                            'torby'
                        );
                    ?>
                </h2>

                <p>
                    <?php echo esc_html( $primary['note'] ?? '' ); ?>
                </p>
            </div>

            <a
                class="tourbi-button tourbi-button--lime tourbi-button--large"
                href="<?php echo esc_url( $primary['url'] ?? '#' ); ?>"
            >
                <?php echo esc_html( $primary['label'] ?? '' ); ?>
                <span aria-hidden="true">→</span>
            </a>
        </div>
    </div>
</section>
