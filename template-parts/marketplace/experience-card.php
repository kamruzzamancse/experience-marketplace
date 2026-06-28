<?php
/**
 * Experience marketplace card.
 *
 * @package Torby
 */

$experience = $args['experience'] ?? array();
$index = absint( $args['index'] ?? 0 );

if ( empty( $experience['id'] ) ) {
    return;
}

$color_classes = array(
    'green',
    'blue',
    'teal',
    'orange',
);

$color = $color_classes[
    $index % count( $color_classes )
];
?>
<article class="tourbi-experience-card tourbi-experience-card--<?php echo esc_attr( $color ); ?>">
    <a
        class="tourbi-experience-card__media"
        href="<?php echo esc_url( $experience['permalink'] ); ?>"
        aria-label="<?php echo esc_attr( $experience['short_title'] ); ?>"
    >
        <?php if ( ! empty( $experience['card_image'] ) ) : ?>
            <img
                src="<?php echo esc_url( $experience['card_image'] ); ?>"
                alt=""
                loading="lazy"
            >
        <?php else : ?>
            <span class="tourbi-experience-card__fallback">
                <?php esc_html_e( 'Tourbi Experience', 'torby' ); ?>
            </span>
        <?php endif; ?>

        <?php if ( ! empty( $experience['featured'] ) ) : ?>
            <span class="tourbi-experience-card__ribbon">
                <b aria-hidden="true">★</b>
                <?php esc_html_e( 'Featured', 'torby' ); ?>
            </span>
        <?php elseif ( ! empty( $experience['primary_category'] ) ) : ?>
            <span class="tourbi-experience-card__ribbon">
                <?php echo esc_html( $experience['primary_category'] ); ?>
            </span>
        <?php endif; ?>
    </a>

    <div class="tourbi-experience-card__body">
        <?php if ( ! empty( $experience['city'] ) ) : ?>
            <span class="tourbi-experience-card__location">
                <b aria-hidden="true">⌖</b>
                <?php echo esc_html( $experience['city'] ); ?>
            </span>
        <?php endif; ?>

        <h2>
            <a href="<?php echo esc_url( $experience['permalink'] ); ?>">
                <?php echo esc_html( $experience['short_title'] ); ?>
            </a>
        </h2>

        <?php if ( ! empty( $experience['summary'] ) ) : ?>
            <p>
                <?php
                echo esc_html(
                    wp_trim_words(
                        $experience['summary'],
                        18,
                        '…'
                    )
                );
                ?>
            </p>
        <?php endif; ?>

        <div class="tourbi-experience-card__meta">
            <span>
                <b aria-hidden="true">◷</b>
                <?php echo esc_html( $experience['duration_label'] ); ?>
            </span>

            <span>
                <b aria-hidden="true">◎</b>
                <?php echo esc_html( $experience['participant_label'] ); ?>
            </span>

            <span>
                <b aria-hidden="true">♢</b>
                <?php echo wp_kses_post( $experience['price_html'] ); ?>
            </span>
        </div>

        <a
            class="tourbi-experience-card__button"
            href="<?php echo esc_url( $experience['permalink'] ); ?>"
        >
            <span aria-hidden="true">▣</span>
            <?php esc_html_e( 'Book Experience', 'torby' ); ?>
        </a>
    </div>
</article>
