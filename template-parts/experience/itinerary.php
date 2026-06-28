<?php
/**
 * Experience itinerary timeline.
 *
 * @package Torby
 */

$experience = $args['experience'] ?? array();
$itinerary = (array) ( $experience['itinerary'] ?? array() );

if ( empty( $itinerary ) ) {
    return;
}
?>
<section class="tourbi-experience-section tourbi-itinerary-section">
    <div class="tourbi-experience-section__heading">
        <span class="tourbi-kicker">
            <?php esc_html_e( 'The Journey', 'torby' ); ?>
        </span>

        <h2 class="tourbi-section-title">
            <?php esc_html_e( 'Your route, stop by stop.', 'torby' ); ?>
        </h2>
    </div>

    <div class="tourbi-itinerary-timeline">
        <?php foreach ( $itinerary as $index => $stop ) : ?>
            <?php
            $image_id = absint( $stop['image_id'] ?? 0 );
            $image = $image_id
                ? wp_get_attachment_image(
                    $image_id,
                    'tourbi-itinerary-stop',
                    false,
                    array(
                        'loading' => 'lazy',
                    )
                )
                : '';
            ?>
            <article class="tourbi-itinerary-stop">
                <div class="tourbi-itinerary-stop__number">
                    <?php echo esc_html( $index + 1 ); ?>
                </div>

                <div class="tourbi-itinerary-stop__body">
                    <?php if ( $image ) : ?>
                        <div class="tourbi-itinerary-stop__image">
                            <?php echo wp_kses_post( $image ); ?>
                        </div>
                    <?php endif; ?>

                    <div class="tourbi-itinerary-stop__copy">
                        <h3><?php echo esc_html( $stop['title'] ?? '' ); ?></h3>

                        <?php if ( ! empty( $stop['description'] ) ) : ?>
                            <p><?php echo esc_html( $stop['description'] ); ?></p>
                        <?php endif; ?>

                        <div class="tourbi-itinerary-stop__meta">
                            <?php if ( ! empty( $stop['location'] ) ) : ?>
                                <span>
                                    <b aria-hidden="true">⌖</b>
                                    <?php echo esc_html( $stop['location'] ); ?>
                                </span>
                            <?php endif; ?>

                            <?php if ( ! empty( $stop['duration_minutes'] ) ) : ?>
                                <span>
                                    <b aria-hidden="true">◷</b>
                                    <?php
                                    echo esc_html(
                                        tourbi_theme_format_experience_duration(
                                            $stop['duration_minutes']
                                        )
                                    );
                                    ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
