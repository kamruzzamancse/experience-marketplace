<?php
/**
 * Single Experience overview.
 *
 * @package Torby
 */

$experience = $args['experience'] ?? array();
?>
<section class="tourbi-experience-section tourbi-experience-overview">
    <div class="tourbi-experience-section__heading">
        <span class="tourbi-kicker">
            <?php esc_html_e( 'The Experience', 'torby' ); ?>
        </span>

        <h2 class="tourbi-section-title">
            <?php echo esc_html( $experience['title'] ); ?>
        </h2>
    </div>

    <?php if ( ! empty( $experience['description_html'] ) ) : ?>
        <div class="tourbi-experience-prose">
            <?php echo $experience['description_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </div>
    <?php endif; ?>

    <div class="tourbi-experience-key-facts">
        <div>
            <span><?php esc_html_e( 'Duration', 'torby' ); ?></span>
            <strong><?php echo esc_html( $experience['duration_label'] ); ?></strong>
        </div>

        <div>
            <span><?php esc_html_e( 'Group Size', 'torby' ); ?></span>
            <strong><?php echo esc_html( $experience['participant_label'] ); ?></strong>
        </div>

        <div>
            <span><?php esc_html_e( 'Difficulty', 'torby' ); ?></span>
            <strong><?php echo esc_html( $experience['difficulty_label'] ); ?></strong>
        </div>

        <div>
            <span><?php esc_html_e( 'Bike', 'torby' ); ?></span>
            <strong><?php echo esc_html( $experience['bike_type_label'] ); ?></strong>
        </div>
    </div>

    <?php if ( ! empty( $experience['audience'] ) ) : ?>
        <div class="tourbi-experience-audience">
            <span aria-hidden="true">◯</span>
            <div>
                <strong><?php esc_html_e( 'Who can join?', 'torby' ); ?></strong>
                <p><?php echo esc_html( $experience['audience'] ); ?></p>
            </div>
        </div>
    <?php endif; ?>
</section>
