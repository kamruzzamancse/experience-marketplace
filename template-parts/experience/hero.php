<?php
/**
 * Single Experience hero.
 *
 * @package Torby
 */

$experience = $args['experience'] ?? array();
$hero = $experience['gallery'][0] ?? array();
$hero_url = $hero['full'] ?? '';
?>
<section
    class="tourbi-experience-hero <?php echo $hero_url ? 'has-image' : 'has-no-image'; ?>"
    <?php if ( $hero_url ) : ?>
        style="--tourbi-experience-hero-image:url('<?php echo esc_url( $hero_url ); ?>');"
    <?php endif; ?>
>
    <div class="tourbi-experience-hero__overlay"></div>

    <div class="tourbi-shell--wide tourbi-experience-hero__inner">
        <a
            class="tourbi-experience-back"
            href="<?php echo esc_url( tourbi_theme_get_experience_archive_url() ); ?>"
        >
            <span aria-hidden="true">←</span>
            <?php esc_html_e( 'Back to all experiences', 'torby' ); ?>
        </a>

        <div class="tourbi-experience-hero__copy">
            <div class="tourbi-experience-hero__badges">
                <span class="tourbi-experience-hero__guided">
                    <span aria-hidden="true">●</span>
                    <?php esc_html_e( 'Guided by a Local Host', 'torby' ); ?>
                </span>
                <?php if ( ! empty( $experience['featured'] ) ) : ?>
                    <span class="tourbi-badge">
                        <span aria-hidden="true">★</span>
                        <?php esc_html_e( 'Featured Experience', 'torby' ); ?>
                    </span>
                <?php endif; ?>

                <?php if ( ! empty( $experience['primary_category'] ) ) : ?>
                    <span class="tourbi-experience-hero__category">
                        <?php echo esc_html( $experience['primary_category'] ); ?>
                    </span>
                <?php endif; ?>
            </div>

            <h1 class="tourbi-experience-hero__title">
                <?php echo esc_html( $experience['short_title'] ); ?>
            </h1>

            <?php if ( ! empty( $experience['summary'] ) ) : ?>
                <p class="tourbi-experience-hero__summary">
                    <?php echo esc_html( $experience['summary'] ); ?>
                </p>
            <?php endif; ?>

            <div class="tourbi-experience-hero__facts">
                <?php if ( ! empty( $experience['duration_label'] ) ) : ?>
                    <span>
                        <b aria-hidden="true">◷</b>
                        <?php echo esc_html( $experience['duration_label'] ); ?>
                    </span>
                <?php endif; ?>

                <span>
                    <b aria-hidden="true">◎</b>
                    <?php echo esc_html( $experience['participant_label'] ); ?>
                </span>

                <span>
                    <b aria-hidden="true">◉</b>
                    <?php echo esc_html( $experience['bike_type_label'] ); ?>
                </span>

                <span>
                    <b aria-hidden="true">✓</b>
                    <?php echo esc_html( $experience['difficulty_label'] ); ?>
                </span>
            </div>
        </div>
    </div>
</section>
