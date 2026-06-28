<?php
/**
 * Experience gallery with accessible lightbox controls.
 *
 * @package Torby
 */

$experience = $args['experience'] ?? array();
$gallery = (array) ( $experience['gallery'] ?? array() );

if ( count( $gallery ) < 2 ) {
    return;
}
?>
<section class="tourbi-experience-section tourbi-gallery-section">
    <div class="tourbi-experience-section__heading tourbi-experience-section__heading--row">
        <div>
            <span class="tourbi-kicker">
                <?php esc_html_e( 'Gallery', 'torby' ); ?>
            </span>

            <h2 class="tourbi-section-title">
                <?php esc_html_e( 'See the experience.', 'torby' ); ?>
            </h2>
        </div>

        <button
            type="button"
            class="tourbi-button tourbi-button--outline tourbi-button--small"
            data-tourbi-gallery-open="0"
        >
            <?php esc_html_e( 'View All Photos', 'torby' ); ?>
        </button>
    </div>

    <div class="tourbi-experience-gallery">
        <?php foreach ( array_slice( $gallery, 0, 5 ) as $index => $image ) : ?>
            <button
                type="button"
                class="tourbi-experience-gallery__item tourbi-experience-gallery__item--<?php echo esc_attr( $index + 1 ); ?>"
                data-tourbi-gallery-open="<?php echo esc_attr( $index ); ?>"
                aria-label="<?php echo esc_attr( sprintf( __( 'Open photo %d', 'torby' ), $index + 1 ) ); ?>"
            >
                <img
                    src="<?php echo esc_url( $image['large'] ); ?>"
                    alt="<?php echo esc_attr( $image['alt'] ); ?>"
                    loading="lazy"
                >
            </button>
        <?php endforeach; ?>
    </div>

    <div
        class="tourbi-gallery-lightbox"
        data-tourbi-gallery-lightbox
        hidden
        role="dialog"
        aria-modal="true"
        aria-label="<?php esc_attr_e( 'Experience photo gallery', 'torby' ); ?>"
    >
        <button
            type="button"
            class="tourbi-gallery-lightbox__close"
            data-tourbi-gallery-close
            aria-label="<?php esc_attr_e( 'Close gallery', 'torby' ); ?>"
        >×</button>

        <button
            type="button"
            class="tourbi-gallery-lightbox__nav tourbi-gallery-lightbox__nav--previous"
            data-tourbi-gallery-previous
            aria-label="<?php esc_attr_e( 'Previous photo', 'torby' ); ?>"
        >←</button>

        <figure>
            <img
                src=""
                alt=""
                data-tourbi-gallery-image
            >

            <figcaption data-tourbi-gallery-caption></figcaption>
        </figure>

        <button
            type="button"
            class="tourbi-gallery-lightbox__nav tourbi-gallery-lightbox__nav--next"
            data-tourbi-gallery-next
            aria-label="<?php esc_attr_e( 'Next photo', 'torby' ); ?>"
        >→</button>

        <script type="application/json" data-tourbi-gallery-data>
            <?php
            echo wp_json_encode(
                array_map(
                    static function ( $image ) {
                        return array(
                            'src'     => esc_url_raw( $image['full'] ),
                            'alt'     => sanitize_text_field( $image['alt'] ),
                            'caption' => sanitize_text_field( $image['caption'] ),
                        );
                    },
                    $gallery
                )
            );
            ?>
        </script>
    </div>
</section>
