<?php
/**
 * Tourbi Host Income Calculator.
 *
 * Full shortcode: [tourbi_host_income_calculator]
 * The Become a Host hero uses the same renderer with the compact "hero"
 * variant, so both calculators always share one calculation source.
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Return the configured Tourbi platform fee percentage.
 *
 * @return float
 */
function tourbi_theme_income_calculator_platform_fee() {
    $fee = 15.0;

    if ( function_exists( 'tourbi_core_get_platform_fee_percent' ) ) {
        $fee = (float) tourbi_core_get_platform_fee_percent();
    }

    $fee = (float) apply_filters(
        'tourbi_host_income_calculator_platform_fee',
        $fee
    );

    return max( 0.0, min( 100.0, $fee ) );
}

/**
 * Determine whether the current page needs calculator assets in wp_head.
 *
 * @return bool
 */
function tourbi_theme_income_calculator_is_on_current_page() {
    if ( is_page( 'become-a-host' ) ) {
        return true;
    }

    if ( is_page_template( 'templates/page-become-a-host.php' ) ) {
        return true;
    }

    if ( ! is_singular() ) {
        return false;
    }

    $post = get_post();

    if ( ! $post instanceof WP_Post ) {
        return false;
    }

    if (
        has_shortcode(
            (string) $post->post_content,
            'tourbi_host_income_calculator'
        )
    ) {
        return true;
    }

    $elementor_data = get_post_meta(
        $post->ID,
        '_elementor_data',
        true
    );

    return is_string( $elementor_data ) &&
        false !== strpos(
            $elementor_data,
            'tourbi_host_income_calculator'
        );
}

/**
 * Register the shared calculator assets.
 *
 * @return void
 */
function tourbi_theme_register_income_calculator_assets() {
    $style_path  = '/assets/css/host-income-calculator.css';
    $script_path = '/assets/js/host-income-calculator.js';

    wp_register_style(
        'tourbi-host-income-calculator',
        TORBY_CHILD_THEME_URI . $style_path,
        array(),
        torby_child_asset_version( $style_path )
    );

    wp_register_script(
        'tourbi-host-income-calculator',
        TORBY_CHILD_THEME_URI . $script_path,
        array(),
        torby_child_asset_version( $script_path ),
        true
    );

    if ( tourbi_theme_income_calculator_is_on_current_page() ) {
        wp_enqueue_style( 'tourbi-host-income-calculator' );
        wp_enqueue_script( 'tourbi-host-income-calculator' );
    }
}
add_action(
    'wp_enqueue_scripts',
    'tourbi_theme_register_income_calculator_assets',
    999
);

/**
 * Print a late-enqueued stylesheet when a builder renders the shortcode after
 * wp_head. The hero is detected early, so this is only a safe fallback.
 *
 * @return void
 */
function tourbi_theme_income_calculator_print_late_style() {
    if (
        wp_style_is( 'tourbi-host-income-calculator', 'enqueued' ) &&
        ! wp_style_is( 'tourbi-host-income-calculator', 'done' )
    ) {
        wp_print_styles( 'tourbi-host-income-calculator' );
    }
}
add_action(
    'wp_footer',
    'tourbi_theme_income_calculator_print_late_style',
    1
);

/**
 * Clamp an integer setting.
 *
 * @param mixed $value Submitted value.
 * @param int   $minimum Minimum value.
 * @param int   $maximum Maximum value.
 * @param int   $default Default value.
 * @return int
 */
function tourbi_theme_income_calculator_clamp_int(
    $value,
    $minimum,
    $maximum,
    $default
) {
    $value = is_numeric( $value )
        ? (int) $value
        : (int) $default;

    return max(
        (int) $minimum,
        min( (int) $maximum, $value )
    );
}

/**
 * Format a percentage without unnecessary trailing zeroes.
 *
 * @param float $value Percentage.
 * @return string
 */
function tourbi_theme_income_calculator_percent_label( $value ) {
    return rtrim(
        rtrim(
            number_format( (float) $value, 2, '.', '' ),
            '0'
        ),
        '.'
    );
}

/**
 * Normalize calculator settings shared by shortcode and hero.
 *
 * @param array<string,mixed> $args Raw settings.
 * @return array<string,mixed>
 */
function tourbi_theme_income_calculator_normalize_args( $args ) {
    $variant = isset( $args['variant'] ) && 'hero' === $args['variant']
        ? 'hero'
        : 'full';

    $defaults = array(
        'variant'             => $variant,
        'max_width'           => 'hero' === $variant ? 500 : 1300,
        'price_min'           => 20,
        'price_max'           => 300,
        'price_step'          => 5,
        'price_default'       => 75,
        'guests_min'          => 1,
        'guests_max'          => 24,
        'guests_default'      => 6,
        'experiences_min'     => 1,
        'experiences_max'     => 30,
        'experiences_default' => 6,
    );

    $args = wp_parse_args( (array) $args, $defaults );

    $max_width = tourbi_theme_income_calculator_clamp_int(
        $args['max_width'],
        'hero' === $variant ? 320 : 600,
        1600,
        $defaults['max_width']
    );

    // Project rule: Price per guest always begins at $20 or the store currency
    // equivalent. A shortcode cannot lower this minimum accidentally.
    $price_min = tourbi_theme_income_calculator_clamp_int(
        $args['price_min'],
        20,
        10000,
        20
    );
    $price_max = tourbi_theme_income_calculator_clamp_int(
        $args['price_max'],
        $price_min,
        10000,
        300
    );
    $price_step = tourbi_theme_income_calculator_clamp_int(
        $args['price_step'],
        1,
        max( 1, $price_max - $price_min ),
        5
    );
    $price_default = tourbi_theme_income_calculator_clamp_int(
        $args['price_default'],
        $price_min,
        $price_max,
        75
    );

    $guests_min = tourbi_theme_income_calculator_clamp_int(
        $args['guests_min'],
        1,
        100,
        1
    );
    $guests_max = tourbi_theme_income_calculator_clamp_int(
        $args['guests_max'],
        $guests_min,
        100,
        24
    );
    $guests_default = tourbi_theme_income_calculator_clamp_int(
        $args['guests_default'],
        $guests_min,
        $guests_max,
        6
    );

    $experiences_min = tourbi_theme_income_calculator_clamp_int(
        $args['experiences_min'],
        1,
        100,
        1
    );
    $experiences_max = tourbi_theme_income_calculator_clamp_int(
        $args['experiences_max'],
        $experiences_min,
        100,
        30
    );
    $experiences_default = tourbi_theme_income_calculator_clamp_int(
        $args['experiences_default'],
        $experiences_min,
        $experiences_max,
        6
    );

    return array(
        'variant'             => $variant,
        'max_width'           => $max_width,
        'price_min'           => $price_min,
        'price_max'           => $price_max,
        'price_step'          => $price_step,
        'price_default'       => $price_default,
        'guests_min'          => $guests_min,
        'guests_max'          => $guests_max,
        'guests_default'      => $guests_default,
        'experiences_min'     => $experiences_min,
        'experiences_max'     => $experiences_max,
        'experiences_default' => $experiences_default,
    );
}

/**
 * Render one calculator control.
 *
 * @param array<string,mixed> $args Control settings.
 * @return string
 */
function tourbi_theme_income_calculator_control( $args ) {
    $name            = (string) $args['name'];
    $label           = (string) $args['label'];
    $aria_label      = (string) $args['aria_label'];
    $instance_id     = (string) $args['instance_id'];
    $minimum         = (int) $args['minimum'];
    $maximum         = (int) $args['maximum'];
    $step            = (int) $args['step'];
    $default         = (int) $args['default'];
    $variant         = isset( $args['variant'] ) ? (string) $args['variant'] : 'full';
    $currency_symbol = isset( $args['currency_symbol'] )
        ? (string) $args['currency_symbol']
        : '';

    $icons = array(
        'price'       => '<svg viewBox="0 0 24 24" focusable="false"><circle cx="12" cy="12" r="8"></circle><path d="M14.5 8.8c-.7-.5-1.5-.8-2.5-.8-1.7 0-2.8.8-2.8 2 0 1.1.8 1.7 2.8 2.1 1.8.4 2.6.9 2.6 2 0 1.3-1.1 2.2-2.9 2.2-1.1 0-2.1-.3-2.9-1"></path><path d="M12 6.4v11.2"></path></svg>',
        'guests'      => '<svg viewBox="0 0 24 24" focusable="false"><circle cx="9" cy="8" r="3"></circle><circle cx="16.5" cy="9.5" r="2.5"></circle><path d="M3.5 18c.5-3.3 2.4-5 5.5-5s5 1.7 5.5 5"></path><path d="M14 14c2.8.1 4.6 1.4 5.2 4"></path></svg>',
        'experiences' => '<svg viewBox="0 0 24 24" focusable="false"><rect x="4" y="5.5" width="16" height="14" rx="2"></rect><path d="M8 3.5v4M16 3.5v4M4 9.5h16"></path></svg>',
    );

    ob_start();
    ?>
    <div class="tourbi-income-calculator__control">
        <?php if ( 'hero' === $variant ) : ?>
            <span class="tourbi-income-calculator__control-icon" aria-hidden="true">
                <?php echo $icons[ $name ] ?? ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </span>
            <div class="tourbi-income-calculator__control-body">
        <?php endif; ?>

        <div class="tourbi-income-calculator__control-head">
            <label for="<?php echo esc_attr( $instance_id . '-' . $name ); ?>">
                <?php echo esc_html( $label ); ?>
            </label>

            <?php if ( '' !== $currency_symbol ) : ?>
                <span class="tourbi-income-calculator__number-field">
                    <span aria-hidden="true"><?php echo esc_html( $currency_symbol ); ?></span>
                    <input
                        type="number"
                        class="tourbi-income-calculator__number"
                        min="<?php echo esc_attr( $minimum ); ?>"
                        max="<?php echo esc_attr( $maximum ); ?>"
                        step="<?php echo esc_attr( $step ); ?>"
                        value="<?php echo esc_attr( $default ); ?>"
                        data-number="<?php echo esc_attr( $name ); ?>"
                        aria-label="<?php echo esc_attr( $aria_label ); ?>"
                    >
                </span>
            <?php else : ?>
                <input
                    type="number"
                    class="tourbi-income-calculator__number tourbi-income-calculator__number--plain"
                    min="<?php echo esc_attr( $minimum ); ?>"
                    max="<?php echo esc_attr( $maximum ); ?>"
                    step="<?php echo esc_attr( $step ); ?>"
                    value="<?php echo esc_attr( $default ); ?>"
                    data-number="<?php echo esc_attr( $name ); ?>"
                    aria-label="<?php echo esc_attr( $aria_label ); ?>"
                >
            <?php endif; ?>
        </div>

        <input
            id="<?php echo esc_attr( $instance_id . '-' . $name ); ?>"
            class="tourbi-income-calculator__range"
            type="range"
            min="<?php echo esc_attr( $minimum ); ?>"
            max="<?php echo esc_attr( $maximum ); ?>"
            step="<?php echo esc_attr( $step ); ?>"
            value="<?php echo esc_attr( $default ); ?>"
            data-range="<?php echo esc_attr( $name ); ?>"
        >

        <?php if ( 'hero' === $variant ) : ?>
            </div>
        <?php endif; ?>
    </div>
    <?php

    return (string) ob_get_clean();
}

/**
 * Render monthly/annual toggle.
 *
 * @return string
 */
function tourbi_theme_income_calculator_period_toggle() {
    ob_start();
    ?>
    <div
        class="tourbi-income-calculator__period-toggle"
        role="group"
        aria-label="<?php esc_attr_e( 'Estimate period', 'torby' ); ?>"
    >
        <button
            type="button"
            class="is-active"
            data-period="monthly"
            aria-pressed="true"
        >
            <?php esc_html_e( 'Monthly', 'torby' ); ?>
        </button>
        <button
            type="button"
            data-period="annual"
            aria-pressed="false"
        >
            <?php esc_html_e( 'Annual', 'torby' ); ?>
        </button>
    </div>
    <?php

    return (string) ob_get_clean();
}

/**
 * Render the shared Tourbi Host Income Calculator.
 *
 * Variants:
 * - full: existing reusable 1300px shortcode layout.
 * - hero: compact card embedded directly inside Become a Host hero.
 *
 * @param array<string,mixed> $args Calculator settings.
 * @return string
 */
function tourbi_theme_render_host_income_calculator( $args = array() ) {
    $settings = tourbi_theme_income_calculator_normalize_args( $args );
    $variant  = $settings['variant'];

    $platform_fee = tourbi_theme_income_calculator_platform_fee();
    $host_share   = 100 - $platform_fee;

    $currency = function_exists( 'get_woocommerce_currency' )
        ? get_woocommerce_currency()
        : 'USD';

    $currency_symbol = function_exists( 'get_woocommerce_currency_symbol' )
        ? get_woocommerce_currency_symbol( $currency )
        : '$';

    $cta = function_exists( 'tourbi_theme_get_host_primary_cta' )
        ? tourbi_theme_get_host_primary_cta()
        : array(
            'label' => __( 'Start Your Host Application', 'torby' ),
            'url'   => home_url( '/vendor-register/' ),
        );

    $instance_id = wp_unique_id( 'tourbi-income-calculator-' );

    wp_enqueue_style( 'tourbi-host-income-calculator' );
    wp_enqueue_script( 'tourbi-host-income-calculator' );

    $controls = tourbi_theme_income_calculator_control(
        array(
            'name'            => 'price',
            'label'           => __( 'Price per guest', 'torby' ),
            'aria_label'      => __( 'Price per guest value', 'torby' ),
            'instance_id'     => $instance_id,
            'variant'         => $variant,
            'minimum'         => $settings['price_min'],
            'maximum'         => $settings['price_max'],
            'step'            => $settings['price_step'],
            'default'         => $settings['price_default'],
            'currency_symbol' => $currency_symbol,
        )
    );

    $controls .= tourbi_theme_income_calculator_control(
        array(
            'name'        => 'guests',
            'label'       => __( 'Guests per experience', 'torby' ),
            'aria_label'  => __( 'Guests per experience value', 'torby' ),
            'instance_id' => $instance_id,
            'variant'     => $variant,
            'minimum'     => $settings['guests_min'],
            'maximum'     => $settings['guests_max'],
            'step'        => 1,
            'default'     => $settings['guests_default'],
        )
    );

    $controls .= tourbi_theme_income_calculator_control(
        array(
            'name'        => 'experiences',
            'label'       => __( 'Experiences per month', 'torby' ),
            'aria_label'  => __( 'Experiences per month value', 'torby' ),
            'instance_id' => $instance_id,
            'variant'     => $variant,
            'minimum'     => $settings['experiences_min'],
            'maximum'     => $settings['experiences_max'],
            'step'        => 1,
            'default'     => $settings['experiences_default'],
        )
    );

    $fee_label  = tourbi_theme_income_calculator_percent_label( $platform_fee );
    $host_label = tourbi_theme_income_calculator_percent_label( $host_share );

    ob_start();
    ?>
    <section
        id="<?php echo esc_attr( $instance_id ); ?>"
        class="tourbi-income-calculator tourbi-income-calculator--<?php echo esc_attr( $variant ); ?>"
        style="--tourbi-estimator-max-width: <?php echo esc_attr( $settings['max_width'] ); ?>px;"
        data-currency="<?php echo esc_attr( $currency ); ?>"
        data-currency-symbol="<?php echo esc_attr( $currency_symbol ); ?>"
        data-platform-fee="<?php echo esc_attr( $platform_fee ); ?>"
        data-host-share="<?php echo esc_attr( $host_share ); ?>"
        data-variant="<?php echo esc_attr( $variant ); ?>"
        aria-labelledby="<?php echo esc_attr( $instance_id ); ?>-title"
    >
        <div class="tourbi-income-calculator__panel">
            <?php if ( 'hero' === $variant ) : ?>
                <header class="tourbi-income-calculator__hero-head">
                    <p class="tourbi-income-calculator__eyebrow">
                        <?php esc_html_e( 'Earnings Estimator', 'torby' ); ?>
                    </p>
                    <h2
                        id="<?php echo esc_attr( $instance_id ); ?>-title"
                        class="tourbi-income-calculator__hero-title"
                    >
                        <?php esc_html_e( 'Estimate your host earnings', 'torby' ); ?>
                    </h2>
                </header>

                <section class="tourbi-income-calculator__hero-result" aria-label="<?php esc_attr_e( 'Estimated host payout', 'torby' ); ?>">
                    <p
                        class="tourbi-income-calculator__result-label"
                        data-result-label
                    >
                        <?php esc_html_e( 'Estimated monthly host payout', 'torby' ); ?>
                    </p>
                    <p
                        class="tourbi-income-calculator__payout"
                        data-payout
                        aria-live="polite"
                    >
                        <?php echo esc_html( $currency_symbol . '0' ); ?>
                    </p>

                    <div class="tourbi-income-calculator__share-bar" aria-label="<?php esc_attr_e( 'Revenue share', 'torby' ); ?>">
                        <span>
                            <?php esc_html_e( 'Host', 'torby' ); ?>
                            <b><?php echo esc_html( $host_label ); ?>%</b>
                        </span>
                        <span>
                            <?php esc_html_e( 'Tourbi', 'torby' ); ?>
                            <b><?php echo esc_html( $fee_label ); ?>%</b>
                        </span>
                    </div>
                </section>

                <?php echo tourbi_theme_income_calculator_period_toggle(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

                <div class="tourbi-income-calculator__hero-divider" aria-hidden="true"></div>

                <div class="tourbi-income-calculator__controls">
                    <?php echo $controls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

                    <dl class="tourbi-income-calculator__breakdown tourbi-income-calculator__breakdown--hero">
                        <div>
                            <dt><?php esc_html_e( 'Gross bookings', 'torby' ); ?></dt>
                            <dd data-gross><?php echo esc_html( $currency_symbol . '0' ); ?></dd>
                        </div>
                        <div>
                            <dt>
                                <?php
                                printf(
                                    /* translators: %s: platform fee percentage. */
                                    esc_html__( 'Tourbi fee (%s%%)', 'torby' ),
                                    esc_html( $fee_label )
                                );
                                ?>
                            </dt>
                            <dd data-fee><?php echo esc_html( '-' . $currency_symbol . '0' ); ?></dd>
                        </div>
                        <div class="tourbi-income-calculator__breakdown-total">
                            <dt><?php esc_html_e( 'Estimated monthly host payout', 'torby' ); ?></dt>
                            <dd data-payout><?php echo esc_html( $currency_symbol . '0' ); ?></dd>
                        </div>
                    </dl>

                    <p class="tourbi-income-calculator__formula" data-formula></p>

                    <a
                        class="tourbi-income-calculator__cta"
                        href="<?php echo esc_url( $cta['url'] ?? home_url( '/vendor-register/' ) ); ?>"
                    >
                        <span><?php echo esc_html( $cta['label'] ?? __( 'Start Hosting', 'torby' ) ); ?></span>
                        <span aria-hidden="true">→</span>
                    </a>

                    <p class="tourbi-income-calculator__disclaimer">
                        <span class="tourbi-income-calculator__disclaimer-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" focusable="false"><path d="M12 3 19 6v5c0 4.6-2.6 8-7 10-4.4-2-7-5.4-7-10V6l7-3Z"></path><path d="m9 12 2 2 4-4"></path></svg>
                        </span>
                        <span>
                            <?php
                            printf(
                                /* translators: %s: host share percentage. */
                                esc_html__( 'Estimate only, based on a %s%% host share. Actual earnings depend on completed bookings and approved Host terms.', 'torby' ),
                                esc_html( $host_label )
                            );
                            ?>
                        </span>
                    </p>
                </div>
            <?php else : ?>
                <div class="tourbi-income-calculator__intro">
                    <p class="tourbi-income-calculator__eyebrow">
                        <?php esc_html_e( 'Earnings Estimator', 'torby' ); ?>
                    </p>

                    <h2
                        id="<?php echo esc_attr( $instance_id ); ?>-title"
                        class="tourbi-income-calculator__title"
                    >
                        <?php esc_html_e( 'What you could', 'torby' ); ?>
                        <em><?php esc_html_e( 'take home.', 'torby' ); ?></em>
                    </h2>

                    <p class="tourbi-income-calculator__description">
                        <?php esc_html_e( 'Adjust the sliders to explore your potential payout based on ticket price, group size, and monthly schedule.', 'torby' ); ?>
                    </p>

                    <div class="tourbi-income-calculator__result-card">
                        <div class="tourbi-income-calculator__result-heading">
                            <p
                                class="tourbi-income-calculator__result-label"
                                data-result-label
                            >
                                <?php esc_html_e( 'Estimated monthly host payout', 'torby' ); ?>
                            </p>
                            <?php echo tourbi_theme_income_calculator_period_toggle(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>

                        <p
                            class="tourbi-income-calculator__payout"
                            data-payout
                            aria-live="polite"
                        >
                            <?php echo esc_html( $currency_symbol . '0' ); ?>
                        </p>

                        <dl class="tourbi-income-calculator__breakdown">
                            <div>
                                <dt><?php esc_html_e( 'Gross bookings', 'torby' ); ?></dt>
                                <dd data-gross><?php echo esc_html( $currency_symbol . '0' ); ?></dd>
                            </div>
                            <div>
                                <dt>
                                    <?php
                                    printf(
                                        /* translators: %s: platform fee percentage. */
                                        esc_html__( 'Tourbi platform fee (%s%%)', 'torby' ),
                                        esc_html( $fee_label )
                                    );
                                    ?>
                                </dt>
                                <dd data-fee><?php echo esc_html( '-' . $currency_symbol . '0' ); ?></dd>
                            </div>
                        </dl>

                        <p class="tourbi-income-calculator__formula" data-formula></p>
                    </div>
                </div>

                <div class="tourbi-income-calculator__controls">
                    <?php echo $controls; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

                    <a
                        class="tourbi-income-calculator__cta"
                        href="<?php echo esc_url( $cta['url'] ?? home_url( '/vendor-register/' ) ); ?>"
                    >
                        <span><?php echo esc_html( $cta['label'] ?? __( 'Start Hosting', 'torby' ) ); ?></span>
                        <span aria-hidden="true">→</span>
                    </a>

                    <p class="tourbi-income-calculator__disclaimer">
                        <?php
                        printf(
                            /* translators: %s: host share percentage. */
                            esc_html__( 'Estimate only, based on a %s%% host share. Actual earnings depend on completed bookings, availability, taxes, refunds, and approved Host terms.', 'torby' ),
                            esc_html( $host_label )
                        );
                        ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <?php

    return (string) ob_get_clean();
}

/**
 * Existing reusable shortcode callback.
 *
 * @param array<string,mixed> $atts Shortcode attributes.
 * @return string
 */
function tourbi_theme_host_income_calculator_shortcode( $atts ) {
    $atts = shortcode_atts(
        array(
            'max_width'           => 1300,
            'price_min'           => 20,
            'price_max'           => 300,
            'price_step'          => 5,
            'price_default'       => 75,
            'guests_min'          => 1,
            'guests_max'          => 24,
            'guests_default'      => 6,
            'experiences_min'     => 1,
            'experiences_max'     => 30,
            'experiences_default' => 6,
        ),
        (array) $atts,
        'tourbi_host_income_calculator'
    );

    $atts['variant'] = 'full';

    return tourbi_theme_render_host_income_calculator( $atts );
}
add_shortcode(
    'tourbi_host_income_calculator',
    'tourbi_theme_host_income_calculator_shortcode'
);
