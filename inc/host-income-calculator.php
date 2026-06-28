<?php
/**
 * Tourbi Host Income Calculator shortcode.
 *
 * Shortcode: [tourbi_host_income_calculator]
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Return the configured Tourbi platform fee percentage.
 *
 * Tourbi Core may provide a central setting later. Until then the project
 * requirement default is 15%. The filter keeps this value configurable
 * without editing the calculator template.
 *
 * @return float
 */
function tourbi_theme_income_calculator_platform_fee() {
    $fee = 15.0;

    if ( function_exists( 'tourbi_core_get_platform_fee_percent' ) ) {
        $fee = (float) tourbi_core_get_platform_fee_percent();
    }

    /**
     * Filter the platform fee used by the public earnings estimator.
     *
     * @param float $fee Platform fee percentage.
     */
    $fee = (float) apply_filters(
        'tourbi_host_income_calculator_platform_fee',
        $fee
    );

    return max( 0.0, min( 100.0, $fee ) );
}

/**
 * Register calculator assets and conditionally enqueue them when possible.
 *
 * Elementor stores shortcode widgets in post meta, so both post content and
 * Elementor data are checked. The shortcode callback also enqueues the assets
 * as a fallback.
 *
 * @return void
 */
function tourbi_theme_register_income_calculator_assets() {
    $style_path = '/assets/css/host-income-calculator.css';
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
    40
);

/**
 * Determine whether the current singular page contains the calculator.
 *
 * @return bool
 */
function tourbi_theme_income_calculator_is_on_current_page() {
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
 * Print a late-enqueued stylesheet in the footer when a third-party builder
 * renders the shortcode after wp_head has already run.
 *
 * @return void
 */
function tourbi_theme_income_calculator_print_late_style() {
    if (
        wp_style_is(
            'tourbi-host-income-calculator',
            'enqueued'
        ) &&
        ! wp_style_is(
            'tourbi-host-income-calculator',
            'done'
        )
    ) {
        wp_print_styles(
            'tourbi-host-income-calculator'
        );
    }
}
add_action(
    'wp_footer',
    'tourbi_theme_income_calculator_print_late_style',
    1
);

/**
 * Clamp an integer shortcode setting to an allowed range.
 *
 * @param mixed $value   Submitted value.
 * @param int   $minimum Minimum allowed value.
 * @param int   $maximum Maximum allowed value.
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
 * Render the Tourbi Host Income Calculator.
 *
 * Supported example:
 * [tourbi_host_income_calculator max_width="1300"]
 *
 * @param array<string,mixed> $atts Shortcode attributes.
 * @return string
 */
function tourbi_theme_host_income_calculator_shortcode( $atts ) {
    $atts = shortcode_atts(
        array(
            'max_width'           => 1300,
            'price_min'           => 25,
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

    $max_width = tourbi_theme_income_calculator_clamp_int(
        $atts['max_width'],
        600,
        1600,
        1300
    );

    $price_min = tourbi_theme_income_calculator_clamp_int(
        $atts['price_min'],
        1,
        10000,
        25
    );
    $price_max = tourbi_theme_income_calculator_clamp_int(
        $atts['price_max'],
        $price_min,
        10000,
        300
    );
    $price_step = tourbi_theme_income_calculator_clamp_int(
        $atts['price_step'],
        1,
        max( 1, $price_max - $price_min ),
        5
    );
    $price_default = tourbi_theme_income_calculator_clamp_int(
        $atts['price_default'],
        $price_min,
        $price_max,
        75
    );

    $guests_min = tourbi_theme_income_calculator_clamp_int(
        $atts['guests_min'],
        1,
        100,
        1
    );
    $guests_max = tourbi_theme_income_calculator_clamp_int(
        $atts['guests_max'],
        $guests_min,
        100,
        24
    );
    $guests_default = tourbi_theme_income_calculator_clamp_int(
        $atts['guests_default'],
        $guests_min,
        $guests_max,
        6
    );

    $experiences_min = tourbi_theme_income_calculator_clamp_int(
        $atts['experiences_min'],
        1,
        100,
        1
    );
    $experiences_max = tourbi_theme_income_calculator_clamp_int(
        $atts['experiences_max'],
        $experiences_min,
        100,
        30
    );
    $experiences_default = tourbi_theme_income_calculator_clamp_int(
        $atts['experiences_default'],
        $experiences_min,
        $experiences_max,
        6
    );

    $platform_fee = tourbi_theme_income_calculator_platform_fee();
    $host_share = 100 - $platform_fee;

    $currency = function_exists( 'get_woocommerce_currency' )
        ? get_woocommerce_currency()
        : 'USD';

    $currency_symbol = function_exists(
        'get_woocommerce_currency_symbol'
    )
        ? get_woocommerce_currency_symbol( $currency )
        : '$';

    $cta = function_exists( 'tourbi_theme_get_host_primary_cta' )
        ? tourbi_theme_get_host_primary_cta()
        : array(
            'label' => __( 'Start Your Host Application', 'torby' ),
            'url'   => home_url( '/vendor-register/' ),
        );

    $instance_id = wp_unique_id(
        'tourbi-income-calculator-'
    );

    wp_enqueue_style( 'tourbi-host-income-calculator' );
    wp_enqueue_script( 'tourbi-host-income-calculator' );

    ob_start();
    ?>
    <section
        id="<?php echo esc_attr( $instance_id ); ?>"
        class="tourbi-income-calculator"
        style="--tourbi-estimator-max-width: <?php echo esc_attr( $max_width ); ?>px;"
        data-currency="<?php echo esc_attr( $currency ); ?>"
        data-currency-symbol="<?php echo esc_attr( $currency_symbol ); ?>"
        data-platform-fee="<?php echo esc_attr( $platform_fee ); ?>"
        data-host-share="<?php echo esc_attr( $host_share ); ?>"
        aria-labelledby="<?php echo esc_attr( $instance_id ); ?>-title"
    >
        <div class="tourbi-income-calculator__panel">
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
                    <?php
                    esc_html_e(
                        'Adjust the sliders to explore your potential payout based on ticket price, group size, and monthly schedule.',
                        'torby'
                    );
                    ?>
                </p>

                <div class="tourbi-income-calculator__result-card">
                    <div class="tourbi-income-calculator__result-heading">
                        <p
                            class="tourbi-income-calculator__result-label"
                            data-result-label
                        >
                            <?php esc_html_e( 'Estimated monthly host payout', 'torby' ); ?>
                        </p>

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
                                    esc_html( rtrim( rtrim( number_format( $platform_fee, 2, '.', '' ), '0' ), '.' ) )
                                );
                                ?>
                            </dt>
                            <dd data-fee><?php echo esc_html( '-' . $currency_symbol . '0' ); ?></dd>
                        </div>
                    </dl>

                    <p
                        class="tourbi-income-calculator__formula"
                        data-formula
                    ></p>
                </div>
            </div>

            <div class="tourbi-income-calculator__controls">
                <div class="tourbi-income-calculator__control">
                    <div class="tourbi-income-calculator__control-head">
                        <label for="<?php echo esc_attr( $instance_id ); ?>-price">
                            <?php esc_html_e( 'Price per guest', 'torby' ); ?>
                        </label>
                        <span class="tourbi-income-calculator__number-field">
                            <span aria-hidden="true"><?php echo esc_html( $currency_symbol ); ?></span>
                            <input
                                type="number"
                                class="tourbi-income-calculator__number"
                                min="<?php echo esc_attr( $price_min ); ?>"
                                max="<?php echo esc_attr( $price_max ); ?>"
                                step="<?php echo esc_attr( $price_step ); ?>"
                                value="<?php echo esc_attr( $price_default ); ?>"
                                data-number="price"
                                aria-label="<?php esc_attr_e( 'Price per guest value', 'torby' ); ?>"
                            >
                        </span>
                    </div>
                    <input
                        id="<?php echo esc_attr( $instance_id ); ?>-price"
                        class="tourbi-income-calculator__range"
                        type="range"
                        min="<?php echo esc_attr( $price_min ); ?>"
                        max="<?php echo esc_attr( $price_max ); ?>"
                        step="<?php echo esc_attr( $price_step ); ?>"
                        value="<?php echo esc_attr( $price_default ); ?>"
                        data-range="price"
                    >
                </div>

                <div class="tourbi-income-calculator__control">
                    <div class="tourbi-income-calculator__control-head">
                        <label for="<?php echo esc_attr( $instance_id ); ?>-guests">
                            <?php esc_html_e( 'Guests per experience', 'torby' ); ?>
                        </label>
                        <input
                            type="number"
                            class="tourbi-income-calculator__number tourbi-income-calculator__number--plain"
                            min="<?php echo esc_attr( $guests_min ); ?>"
                            max="<?php echo esc_attr( $guests_max ); ?>"
                            step="1"
                            value="<?php echo esc_attr( $guests_default ); ?>"
                            data-number="guests"
                            aria-label="<?php esc_attr_e( 'Guests per experience value', 'torby' ); ?>"
                        >
                    </div>
                    <input
                        id="<?php echo esc_attr( $instance_id ); ?>-guests"
                        class="tourbi-income-calculator__range"
                        type="range"
                        min="<?php echo esc_attr( $guests_min ); ?>"
                        max="<?php echo esc_attr( $guests_max ); ?>"
                        step="1"
                        value="<?php echo esc_attr( $guests_default ); ?>"
                        data-range="guests"
                    >
                </div>

                <div class="tourbi-income-calculator__control">
                    <div class="tourbi-income-calculator__control-head">
                        <label for="<?php echo esc_attr( $instance_id ); ?>-experiences">
                            <?php esc_html_e( 'Experiences per month', 'torby' ); ?>
                        </label>
                        <input
                            type="number"
                            class="tourbi-income-calculator__number tourbi-income-calculator__number--plain"
                            min="<?php echo esc_attr( $experiences_min ); ?>"
                            max="<?php echo esc_attr( $experiences_max ); ?>"
                            step="1"
                            value="<?php echo esc_attr( $experiences_default ); ?>"
                            data-number="experiences"
                            aria-label="<?php esc_attr_e( 'Experiences per month value', 'torby' ); ?>"
                        >
                    </div>
                    <input
                        id="<?php echo esc_attr( $instance_id ); ?>-experiences"
                        class="tourbi-income-calculator__range"
                        type="range"
                        min="<?php echo esc_attr( $experiences_min ); ?>"
                        max="<?php echo esc_attr( $experiences_max ); ?>"
                        step="1"
                        value="<?php echo esc_attr( $experiences_default ); ?>"
                        data-range="experiences"
                    >
                </div>

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
                        esc_html__(
                            'Estimate only, based on a %s%% host share. Actual earnings depend on completed bookings, availability, taxes, refunds, and approved Host terms.',
                            'torby'
                        ),
                        esc_html( rtrim( rtrim( number_format( $host_share, 2, '.', '' ), '0' ), '.' ) )
                    );
                    ?>
                </p>
            </div>
        </div>
    </section>
    <?php

    return (string) ob_get_clean();
}
add_shortcode(
    'tourbi_host_income_calculator',
    'tourbi_theme_host_income_calculator_shortcode'
);
