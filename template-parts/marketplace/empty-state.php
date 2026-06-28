<?php
/**
 * Marketplace empty results state.
 *
 * @package Torby
 */

$state = $args['state'] ?? array();
?>
<div class="tourbi-marketplace-empty">
    <div class="tourbi-marketplace-empty__icon" aria-hidden="true">⌕</div>

    <span class="tourbi-kicker">
        <?php esc_html_e( 'No Results Yet', 'torby' ); ?>
    </span>

    <h2 class="tourbi-section-title">
        <?php esc_html_e( 'Try a wider search.', 'torby' ); ?>
    </h2>

    <p>
        <?php
        esc_html_e(
            'Change the keyword, category, or location to discover more Tourbi experiences.',
            'torby'
        );
        ?>
    </p>

    <a
        class="tourbi-button tourbi-button--primary"
        href="<?php echo esc_url( tourbi_theme_get_experience_archive_url() ); ?>"
    >
        <?php esc_html_e( 'View All Experiences', 'torby' ); ?>
    </a>
</div>
