<?php
/**
 * Template Name: Tourbi Foundation Preview
 * Template Post Type: page
 *
 * A safe visual test page for Step 68A. It does not query bookings, modify
 * Elementor content, or write data.
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>
<main
    id="primary"
    class="tourbi-app tourbi-foundation-preview"
>
    <section class="tourbi-section tourbi-foundation-preview__hero">
        <div class="tourbi-shell--wide tourbi-foundation-preview__hero-grid">
            <div class="tourbi-stack tourbi-stack--6">
                <span class="tourbi-kicker">
                    <?php esc_html_e( 'Hybrid theme foundation', 'torby' ); ?>
                </span>

                <h1 class="tourbi-display-title">
                    <?php esc_html_e( 'Tourbi custom pages are ready to begin.', 'torby' ); ?>
                </h1>

                <p class="tourbi-lead">
                    <?php
                    esc_html_e(
                        'The current Elementor homepage stays protected while new marketplace, experience, and host screens receive a fast custom PHP template layer.',
                        'torby'
                    );
                    ?>
                </p>

                <div class="tourbi-cluster">
                    <?php
                    echo wp_kses_post(
                        tourbi_theme_button(
                            __(
                                'Explore Experiences',
                                'torby'
                            ),
                            tourbi_theme_get_experience_archive_url(),
                            array(
                                'variant' => 'primary',
                                'size'    => 'large',
                                'icon'    => '→',
                            )
                        )
                    );

                    echo wp_kses_post(
                        tourbi_theme_button(
                            __(
                                'Become a Host',
                                'torby'
                            ),
                            tourbi_theme_get_become_host_url(),
                            array(
                                'variant' => 'outline',
                                'size'    => 'large',
                            )
                        )
                    );
                    ?>
                </div>
            </div>

            <div class="tourbi-foundation-preview__visual">
                <span class="tourbi-badge">
                    <?php esc_html_e( 'Design system active', 'torby' ); ?>
                </span>

                <div class="tourbi-foundation-preview__image">
                    <?php esc_html_e( 'Orange · Black · White', 'torby' ); ?>
                </div>

                <div class="tourbi-foundation-preview__visual-row">
                    <div class="tourbi-stat">
                        <strong>85%</strong>
                        <span><?php esc_html_e( 'Host share', 'torby' ); ?></span>
                    </div>

                    <div class="tourbi-stat">
                        <strong>15%</strong>
                        <span><?php esc_html_e( 'Tourbi share', 'torby' ); ?></span>
                    </div>

                    <div class="tourbi-stat">
                        <strong>1</strong>
                        <span><?php esc_html_e( 'Protected home', 'torby' ); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="tourbi-section">
        <div class="tourbi-shell">
            <div class="tourbi-section-heading">
                <span class="tourbi-kicker">
                    <?php esc_html_e( 'Reusable components', 'torby' ); ?>
                </span>

                <h2 class="tourbi-section-title">
                    <?php esc_html_e( 'A consistent visual language for every new page.', 'torby' ); ?>
                </h2>

                <p class="tourbi-lead">
                    <?php
                    esc_html_e(
                        'Future archive, single-experience, host-builder, and onboarding templates will share the same namespaced foundation.',
                        'torby'
                    );
                    ?>
                </p>
            </div>

            <div class="tourbi-grid tourbi-grid--3">
                <?php for ( $index = 1; $index <= 3; $index++ ) : ?>
                    <article class="tourbi-card">
                        <div class="tourbi-foundation-preview__card-media"></div>

                        <div class="tourbi-card__body tourbi-stack tourbi-stack--4">
                            <span class="tourbi-badge">
                                <?php
                                echo esc_html(
                                    sprintf(
                                        /* translators: %d: Card number. */
                                        __(
                                            'Experience %d',
                                            'torby'
                                        ),
                                        $index
                                    )
                                );
                                ?>
                            </span>

                            <h3 class="tourbi-card-title">
                                <?php esc_html_e( 'Washington E-Bike Adventure', 'torby' ); ?>
                            </h3>

                            <p class="tourbi-muted">
                                <?php esc_html_e( 'Curated rides, local stories, and memorable city moments.', 'torby' ); ?>
                            </p>

                            <ul class="tourbi-chip-list">
                                <li class="tourbi-chip"><?php esc_html_e( '3 Hours', 'torby' ); ?></li>
                                <li class="tourbi-chip"><?php esc_html_e( '2–12 People', 'torby' ); ?></li>
                                <li class="tourbi-chip"><?php esc_html_e( 'From $69', 'torby' ); ?></li>
                            </ul>
                        </div>
                    </article>
                <?php endfor; ?>
            </div>
        </div>
    </section>

    <section class="tourbi-section tourbi-section--compact">
        <div class="tourbi-shell">
            <div class="tourbi-card tourbi-foundation-preview__form">
                <div class="tourbi-split">
                    <div class="tourbi-stack tourbi-stack--4">
                        <span class="tourbi-kicker">
                            <?php esc_html_e( 'Form foundation', 'torby' ); ?>
                        </span>

                        <h2 class="tourbi-section-title">
                            <?php esc_html_e( 'Prepared for the Host Experience Wizard.', 'torby' ); ?>
                        </h2>

                        <p class="tourbi-lead">
                            <?php
                            esc_html_e(
                                'This preview tests fields, buttons, cards, spacing, responsive grids, and accessibility without changing existing Tourbi data.',
                                'torby'
                            );
                            ?>
                        </p>
                    </div>

                    <div class="tourbi-stack tourbi-stack--4">
                        <div class="tourbi-field">
                            <label for="tourbi-preview-title">
                                <?php esc_html_e( 'Experience title', 'torby' ); ?>
                            </label>

                            <input
                                id="tourbi-preview-title"
                                class="tourbi-input"
                                type="text"
                                value="<?php esc_attr_e( 'Brunch and Ride: DC E-Bike Adventure', 'torby' ); ?>"
                                readonly
                            >
                        </div>

                        <div class="tourbi-field">
                            <label for="tourbi-preview-location">
                                <?php esc_html_e( 'Location', 'torby' ); ?>
                            </label>

                            <select
                                id="tourbi-preview-location"
                                class="tourbi-select"
                                disabled
                            >
                                <option>
                                    <?php echo esc_html( tourbi_theme_get_location_label() ); ?>
                                </option>
                            </select>
                        </div>

                        <div class="tourbi-field">
                            <label for="tourbi-preview-summary">
                                <?php esc_html_e( 'Summary', 'torby' ); ?>
                            </label>

                            <textarea
                                id="tourbi-preview-summary"
                                class="tourbi-textarea"
                                readonly
                            ><?php esc_html_e( 'Explore DC on an e-bike, enjoy scenic views, and share a memorable local experience.', 'torby' ); ?></textarea>
                        </div>

                        <div class="tourbi-cluster">
                            <?php
                            echo wp_kses_post(
                                tourbi_theme_button(
                                    __(
                                        'Primary Button',
                                        'torby'
                                    ),
                                    '#',
                                    array(
                                        'variant' => 'primary',
                                    )
                                )
                            );

                            echo wp_kses_post(
                                tourbi_theme_button(
                                    __(
                                        'Lime Booking CTA',
                                        'torby'
                                    ),
                                    '#',
                                    array(
                                        'variant' => 'lime',
                                    )
                                )
                            );
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <p class="tourbi-muted tourbi-foundation-preview__footer-note">
                <?php
                esc_html_e(
                    'Delete this preview page after Step 68A verification. The template file may remain for future diagnostics.',
                    'torby'
                );
                ?>
            </p>
        </div>
    </section>
</main>
<?php
get_footer();
