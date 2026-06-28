<?php
/**
 * Featured Experience marketplace hero.
 *
 * @package Torby
 */

$experience = $args['experience'] ?? array();

if ( empty( $experience['id'] ) ) {
    return;
}

$image = $experience['gallery'][0]['full'] ?? '';
?>
<section class="tourbi-marketplace-featured">
    <div class="tourbi-shell--wide">
        <article
            class="tourbi-featured-experience <?php echo $image ? 'has-image' : 'has-no-image'; ?>"
            <?php if ( $image ) : ?>
                style="--tourbi-featured-image:url('<?php echo esc_url( $image ); ?>');"
            <?php endif; ?>
        >
            <div class="tourbi-featured-experience__overlay"></div>

            <div class="tourbi-featured-experience__copy">
                <span class="tourbi-badge">
                    <span aria-hidden="true">★</span>
                    <?php esc_html_e( 'Featured Experience', 'torby' ); ?>
                </span>

                <h2>
                    <?php echo esc_html( $experience['short_title'] ); ?>
                </h2>

                <?php if ( ! empty( $experience['summary'] ) ) : ?>
                    <p>
                        <?php echo esc_html( $experience['summary'] ); ?>
                    </p>
                <?php endif; ?>

                <div class="tourbi-featured-experience__facts">
                    <span>
                        <b aria-hidden="true">◷</b>
                        <?php echo esc_html( $experience['duration_label'] ); ?>
                    </span>

                    <span>
                        <b aria-hidden="true">◎</b>
                        <?php echo esc_html( $experience['participant_label'] ); ?>
                    </span>

                    <span>
                        <b aria-hidden="true">⌖</b>
                        <?php echo esc_html( $experience['city'] ); ?>
                    </span>

                    <span>
                        <b aria-hidden="true">♢</b>
                        <?php echo wp_kses_post( $experience['price_html'] ); ?>
                    </span>
                </div>

                <a
                    class="tourbi-button tourbi-button--lime tourbi-button--large"
                    href="<?php echo esc_url( $experience['permalink'] ); ?>"
                >
                    <span aria-hidden="true">▣</span>
                    <?php esc_html_e( 'Book This Experience', 'torby' ); ?>
                </a>
            </div>

            <div class="tourbi-featured-experience__benefits">
                <span>
                    <b aria-hidden="true">♧</b>
                    <strong><?php esc_html_e( 'Local Stories', 'torby' ); ?></strong>
                </span>

                <span>
                    <b aria-hidden="true">⌖</b>
                    <strong><?php esc_html_e( 'Scenic Routes', 'torby' ); ?></strong>
                </span>

                <span>
                    <b aria-hidden="true">◎</b>
                    <strong><?php esc_html_e( 'Easy & Fun', 'torby' ); ?></strong>
                </span>
            </div>
        </article>
    </div>
</section>
