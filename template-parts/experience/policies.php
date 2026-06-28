<?php
/**
 * Cancellation policy and tags.
 *
 * @package Torby
 */

$experience = $args['experience'] ?? array();
$tags = (array) ( $experience['tags'] ?? array() );

if (
    empty( $experience['cancellation_policy'] ) &&
    empty( $tags )
) {
    return;
}
?>
<section class="tourbi-experience-section tourbi-policy-section">
    <?php if ( ! empty( $experience['cancellation_policy'] ) ) : ?>
        <div class="tourbi-policy-card">
            <div class="tourbi-policy-card__icon" aria-hidden="true">✓</div>

            <div>
                <span class="tourbi-kicker">
                    <?php esc_html_e( 'Cancellation Policy', 'torby' ); ?>
                </span>

                <p>
                    <?php echo nl2br( esc_html( $experience['cancellation_policy'] ) ); ?>
                </p>
            </div>
        </div>
    <?php endif; ?>

    <?php if ( ! empty( $tags ) ) : ?>
        <div class="tourbi-experience-tags">
            <strong><?php esc_html_e( 'Experience Tags', 'torby' ); ?></strong>

            <ul class="tourbi-chip-list">
                <?php foreach ( $tags as $tag ) : ?>
                    <li class="tourbi-chip">
                        <?php echo esc_html( $tag['name'] ?? '' ); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</section>
