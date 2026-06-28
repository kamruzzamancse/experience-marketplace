<?php
/**
 * Meeting location and map.
 *
 * @package Torby
 */

$experience = $args['experience'] ?? array();
$map = (array) ( $experience['map'] ?? array() );

if (
    empty( $experience['meeting_address'] ) &&
    empty( $map['embed_url'] )
) {
    return;
}
?>
<section class="tourbi-experience-section tourbi-meeting-section">
    <div class="tourbi-experience-section__heading">
        <span class="tourbi-kicker">
            <?php esc_html_e( 'Where We Meet', 'torby' ); ?>
        </span>

        <h2 class="tourbi-section-title">
            <?php echo esc_html( $experience['city'] ?: __( 'Meeting Location', 'torby' ) ); ?>
        </h2>
    </div>

    <div class="tourbi-meeting-card">
        <?php if ( ! empty( $map['embed_url'] ) ) : ?>
            <div class="tourbi-meeting-card__map">
                <iframe
                    src="<?php echo esc_url( $map['embed_url'] ); ?>"
                    title="<?php esc_attr_e( 'Experience meeting location map', 'torby' ); ?>"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                ></iframe>
            </div>
        <?php endif; ?>

        <div class="tourbi-meeting-card__details">
            <span class="tourbi-meeting-card__pin" aria-hidden="true">⌖</span>

            <div>
                <h3><?php esc_html_e( 'Meeting Address', 'torby' ); ?></h3>

                <?php if ( ! empty( $experience['meeting_address'] ) ) : ?>
                    <p><?php echo nl2br( esc_html( $experience['meeting_address'] ) ); ?></p>
                <?php endif; ?>

                <?php if ( ! empty( $map['directions_url'] ) ) : ?>
                    <a
                        class="tourbi-button tourbi-button--outline tourbi-button--small"
                        href="<?php echo esc_url( $map['directions_url'] ); ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <?php esc_html_e( 'Get Directions', 'torby' ); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
