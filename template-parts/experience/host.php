<?php
/**
 * Meet the host section.
 *
 * @package Torby
 */

$experience = $args['experience'] ?? array();
$host = (array) ( $experience['host'] ?? array() );

if ( empty( $host['name'] ) ) {
    return;
}
?>
<section class="tourbi-experience-section tourbi-host-section">
    <div class="tourbi-host-card">
        <div class="tourbi-host-card__portrait">
            <img
                src="<?php echo esc_url( $host['avatar'] ); ?>"
                alt="<?php echo esc_attr( $host['name'] ); ?>"
                loading="lazy"
            >
        </div>

        <div class="tourbi-host-card__copy">
            <span class="tourbi-kicker">
                <?php esc_html_e( 'Meet Your Host', 'torby' ); ?>
            </span>

            <h2 class="tourbi-section-title">
                <?php echo esc_html( $host['name'] ); ?>
            </h2>

            <?php if ( ! empty( $host['description'] ) ) : ?>
                <p><?php echo nl2br( esc_html( $host['description'] ) ); ?></p>
            <?php endif; ?>

            <div class="tourbi-host-card__facts">
                <?php if ( ! empty( $host['location'] ) ) : ?>
                    <span>
                        <b aria-hidden="true">⌖</b>
                        <?php echo esc_html( $host['location'] ); ?>
                    </span>
                <?php endif; ?>

                <span>
                    <b aria-hidden="true">✓</b>
                    <?php esc_html_e( 'Tourbi verified host', 'torby' ); ?>
                </span>
            </div>
        </div>
    </div>
</section>
