<?php
/**
 * Included and excluded items.
 *
 * @package Torby
 */

$experience = $args['experience'] ?? array();
$inclusions = (array) ( $experience['inclusions'] ?? array() );
$exclusions = (array) ( $experience['exclusions'] ?? array() );

if ( empty( $inclusions ) && empty( $exclusions ) ) {
    return;
}
?>
<section class="tourbi-experience-section tourbi-inclusions-section">
    <div class="tourbi-experience-section__heading">
        <span class="tourbi-kicker">
            <?php esc_html_e( 'Good To Know', 'torby' ); ?>
        </span>

        <h2 class="tourbi-section-title">
            <?php esc_html_e( 'What is included.', 'torby' ); ?>
        </h2>
    </div>

    <div class="tourbi-inclusions-grid">
        <?php if ( ! empty( $inclusions ) ) : ?>
            <div class="tourbi-list-card tourbi-list-card--included">
                <h3><?php esc_html_e( 'Included', 'torby' ); ?></h3>

                <ul>
                    <?php foreach ( $inclusions as $item ) : ?>
                        <li>
                            <span aria-hidden="true">✓</span>
                            <?php echo esc_html( $item ); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ( ! empty( $exclusions ) ) : ?>
            <div class="tourbi-list-card tourbi-list-card--excluded">
                <h3><?php esc_html_e( 'Not Included', 'torby' ); ?></h3>

                <ul>
                    <?php foreach ( $exclusions as $item ) : ?>
                        <li>
                            <span aria-hidden="true">—</span>
                            <?php echo esc_html( $item ); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</section>
