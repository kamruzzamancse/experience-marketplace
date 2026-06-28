<?php
/**
 * Rental marketplace card.
 *
 * @package Torby
 */

$card = $args['card'] ?? array();

if ( empty( $card['id'] ) ) {
    return;
}

$features = array_slice(
    (array) ( $card['features'] ?? array() ),
    0,
    3
);
?>
<article class="tourbi-rental-marketplace-card">
    <a
        class="tourbi-rental-marketplace-card__image"
        href="<?php echo esc_url( $card['url'] ?? '#' ); ?>"
    >
        <?php if ( ! empty( $card['image'] ) ) : ?>
            <img
                src="<?php echo esc_url( $card['image'] ); ?>"
                alt="<?php echo esc_attr( $card['title'] ?? '' ); ?>"
                loading="lazy"
                decoding="async"
            >
        <?php else : ?>
            <span class="tourbi-rental-marketplace-card__placeholder" aria-hidden="true">E-BIKE</span>
        <?php endif; ?>

        <span class="tourbi-rental-marketplace-card__badge">
            <?php esc_html_e( 'Self-Guided Rental', 'torby' ); ?>
        </span>
    </a>

    <div class="tourbi-rental-marketplace-card__content">
        <div class="tourbi-rental-marketplace-card__meta">
            <span><?php echo esc_html( $card['bike_label'] ?? __( 'E-Bike', 'torby' ) ); ?></span>
            <span><?php echo esc_html( $card['type_label'] ?? __( 'Flexible Rental', 'torby' ) ); ?></span>
        </div>

        <h2>
            <a href="<?php echo esc_url( $card['url'] ?? '#' ); ?>">
                <?php echo esc_html( $card['title'] ?? '' ); ?>
            </a>
        </h2>

        <?php if ( ! empty( $card['summary'] ) ) : ?>
            <p><?php echo esc_html( wp_trim_words( $card['summary'], 22 ) ); ?></p>
        <?php endif; ?>

        <?php if ( ! empty( $features ) ) : ?>
            <ul>
                <?php foreach ( $features as $feature ) : ?>
                    <li>
                        <span aria-hidden="true">✓</span>
                        <?php echo esc_html( $feature ); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class="tourbi-rental-marketplace-card__footer">
            <div>
                <small><?php esc_html_e( 'Starting from', 'torby' ); ?></small>
                <strong><?php echo wp_kses_post( $card['price_html'] ?? '' ); ?></strong>
            </div>

            <a href="<?php echo esc_url( $card['url'] ?? '#' ); ?>">
                <?php esc_html_e( 'Check Availability', 'torby' ); ?>
                <span aria-hidden="true">→</span>
            </a>
        </div>
    </div>
</article>
