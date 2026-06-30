<?php
/**
 * Torby child theme functions and definitions.
 *
 * Keep this file limited to presentation-layer concerns. Booking logic,
 * shared inventory, vendor integration, and order handling must live in the
 * separate Tourbi Core plugin so those features remain active if the theme is
 * changed.
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Define reusable child-theme constants.
 */
$torby_theme = wp_get_theme();

if ( ! defined( 'TORBY_CHILD_THEME_VERSION' ) ) {
    define(
        'TORBY_CHILD_THEME_VERSION',
        $torby_theme->get( 'Version' ) ? $torby_theme->get( 'Version' ) : '1.0.0'
    );
}

if ( ! defined( 'TORBY_CHILD_THEME_DIR' ) ) {
    define( 'TORBY_CHILD_THEME_DIR', get_stylesheet_directory() );
}

if ( ! defined( 'TORBY_CHILD_THEME_URI' ) ) {
    define( 'TORBY_CHILD_THEME_URI', get_stylesheet_directory_uri() );
}

/**
 * Return a cache-busting version for a theme asset.
 *
 * During development, the file modification time is used. If the file does
 * not exist, the child-theme version is returned.
 *
 * @param string $relative_path Relative path inside the child theme.
 * @return string
 */
function torby_child_asset_version( $relative_path ) {
    $relative_path = ltrim( (string) $relative_path, '/' );
    $absolute_path = trailingslashit( TORBY_CHILD_THEME_DIR ) . $relative_path;

    if ( file_exists( $absolute_path ) ) {
        return (string) filemtime( $absolute_path );
    }

    return TORBY_CHILD_THEME_VERSION;
}

/**
 * Configure child-theme features.
 *
 * Hello Elementor already registers its core theme, menu, Elementor, and
 * WooCommerce features. These additions are specific to the Tourbi project.
 *
 * @return void
 */
function torby_child_theme_setup() {
    load_child_theme_textdomain(
        'torby',
        trailingslashit( TORBY_CHILD_THEME_DIR ) . 'languages'
    );

    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );

    add_image_size( 'torby-experience-card', 720, 480, true );
    add_image_size( 'torby-experience-hero', 1600, 900, true );
    add_image_size( 'torby-host-avatar', 320, 320, true );
}
add_action( 'after_setup_theme', 'torby_child_theme_setup', 20 );

/**
 * Enqueue the child-theme stylesheet and project assets.
 *
 * @return void
 */
function torby_child_enqueue_assets() {
    $child_style_path = '/style.css';

    wp_enqueue_style(
        'torby-child-style',
        TORBY_CHILD_THEME_URI . $child_style_path,
        array(),
        torby_child_asset_version( $child_style_path )
    );

    $project_css_path = '/assets/css/torby-theme.css';

    if ( file_exists( TORBY_CHILD_THEME_DIR . $project_css_path ) ) {
        wp_enqueue_style(
            'torby-theme',
            TORBY_CHILD_THEME_URI . $project_css_path,
            array( 'torby-child-style' ),
            torby_child_asset_version( $project_css_path )
        );
    }

    $project_js_path = '/assets/js/torby-theme.js';

    if ( file_exists( TORBY_CHILD_THEME_DIR . $project_js_path ) ) {
        wp_enqueue_script(
            'torby-theme',
            TORBY_CHILD_THEME_URI . $project_js_path,
            array(),
            torby_child_asset_version( $project_js_path ),
            true
        );
    }
}
add_action( 'wp_enqueue_scripts', 'torby_child_enqueue_assets', 20 );

/**
 * Determine whether the current WooCommerce account screen is the dashboard.
 *
 * @return bool
 */
function torby_child_is_account_dashboard() {
    if ( ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
        return false;
    }

    if ( function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url() ) {
        return false;
    }

    return true;
}


/**
 * Return a readable public customer name.
 *
 * First and last name are preferred. Display name, nickname, and login name
 * are safe fallbacks when the profile is incomplete.
 *
 * @param WP_User|null $user Optional user object.
 * @return string
 */
function torby_child_get_account_public_name( $user = null ) {
    if ( ! $user instanceof WP_User ) {
        $user = wp_get_current_user();
    }

    if ( ! $user instanceof WP_User || ! $user->exists() ) {
        return __( 'Tourbi Rider', 'torby' );
    }

    $first_name = sanitize_text_field(
        get_user_meta(
            $user->ID,
            'first_name',
            true
        )
    );

    $last_name = sanitize_text_field(
        get_user_meta(
            $user->ID,
            'last_name',
            true
        )
    );

    $full_name = trim(
        $first_name . ' ' . $last_name
    );

    if ( '' !== $full_name ) {
        return $full_name;
    }

    $display_name = sanitize_text_field(
        $user->display_name
    );

    if ( '' !== $display_name ) {
        return $display_name;
    }

    $nickname = sanitize_text_field(
        get_user_meta(
            $user->ID,
            'nickname',
            true
        )
    );

    if ( '' !== $nickname ) {
        return $nickname;
    }

    return sanitize_text_field(
        $user->user_login
    );
}

/**
 * Return an inline SVG used by account statistics.
 *
 * Inline SVG avoids missing-font boxes and keeps every icon visually
 * consistent across Windows, macOS, Android, and iOS.
 *
 * @param string $icon Icon key.
 * @return string
 */
function torby_child_get_account_stat_icon( $icon ) {
    $icons = array(
        'orders' =>
            '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6 3h12v18H6z"/><path d="M9 7h6M9 11h6M9 15h4"/></svg>',
        'active' =>
            '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>',
        'spent' =>
            '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 7h16v11H4z"/><path d="M16 10h4v5h-4a2.5 2.5 0 0 1 0-5z"/><path d="M7 7V5h9v2"/></svg>',
        'status' =>
            '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></svg>',
    );

    return $icons[ sanitize_key( $icon ) ] ??
        $icons['status'];
}

/**
 * Add useful presentation classes to the body element.
 *
 * These classes are intended only for styling. User permissions and access
 * control must be handled by WordPress, WooCommerce, WCFM, and Tourbi Core.
 *
 * @param string[] $classes Existing body classes.
 * @return string[]
 */
function torby_child_body_classes( $classes ) {
    if ( is_user_logged_in() ) {
        $classes[] = 'torby-user-logged-in';

        $user  = wp_get_current_user();
        $roles = $user instanceof WP_User ? (array) $user->roles : array();

        if (
            in_array( 'wcfm_vendor', $roles, true ) ||
            in_array( 'vendor', $roles, true )
        ) {
            $classes[] = 'torby-user-host';
        }

        if ( in_array( 'customer', $roles, true ) ) {
            $classes[] = 'torby-user-rider';
        }

        if ( current_user_can( 'manage_options' ) ) {
            $classes[] = 'torby-user-admin';
        }
    } else {
        $classes[] = 'torby-user-guest';
    }

    if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
        $classes[] = 'torby-woocommerce-page';
    }

    if ( function_exists( 'is_account_page' ) && is_account_page() ) {
        $classes[] = 'tourbi-account-page';
        $classes[] = is_user_logged_in()
            ? 'tourbi-account-authenticated'
            : 'tourbi-account-guest';

        if ( torby_child_is_account_dashboard() ) {
            $classes[] = 'tourbi-account-dashboard';
        }
    }

    return array_values( array_unique( $classes ) );
}
add_filter( 'body_class', 'torby_child_body_classes' );

/**
 * Add the branded introduction above the customer login and registration form.
 *
 * @return void
 */
function torby_child_render_account_login_intro() {
    if ( is_user_logged_in() ) {
        return;
    }
    ?>
    <section class="tourbi-account-login-intro" aria-labelledby="tourbi-account-login-title">
        <div class="tourbi-account-login-intro__copy">
            <span class="tourbi-account-eyebrow">
                <?php esc_html_e( 'Rider Portal', 'torby' ); ?>
            </span>

            <h1 id="tourbi-account-login-title">
                <?php esc_html_e( 'Welcome to your Tourbi account', 'torby' ); ?>
            </h1>

            <p>
                <?php
                esc_html_e(
                    'Book rides, review upcoming rentals, manage payment methods, and keep your account details in one secure place.',
                    'torby'
                );
                ?>
            </p>
        </div>

        <div class="tourbi-account-login-intro__features" aria-label="Account benefits">
            <span><?php esc_html_e( 'Fast booking', 'torby' ); ?></span>
            <span><?php esc_html_e( 'Order history', 'torby' ); ?></span>
            <span><?php esc_html_e( 'Secure checkout', 'torby' ); ?></span>
        </div>
    </section>
    <?php
}
add_action(
    'woocommerce_before_customer_login_form',
    'torby_child_render_account_login_intro',
    5
);

/**
 * Render the account identity card above the WooCommerce navigation.
 *
 * @return void
 */
function torby_child_render_account_user_card() {
    if ( ! is_user_logged_in() ) {
        return;
    }

    $user         = wp_get_current_user();
    $display_name = $user instanceof WP_User && $user->display_name
        ? $user->display_name
        : __( 'Tourbi Rider', 'torby' );
    $email        = $user instanceof WP_User ? $user->user_email : '';
    ?>
    <section class="tourbi-account-user-card">
        <div class="tourbi-account-user-card__avatar">
            <?php echo get_avatar( $user->ID, 88 ); ?>
        </div>

        <div class="tourbi-account-user-card__details">
            <span class="tourbi-account-eyebrow">
                <?php esc_html_e( 'My Tourbi', 'torby' ); ?>
            </span>

            <strong><?php echo esc_html( $display_name ); ?></strong>

            <?php if ( $email ) : ?>
                <small><?php echo esc_html( $email ); ?></small>
            <?php endif; ?>
        </div>

        <button
            class="tourbi-account-menu-toggle"
            type="button"
            aria-expanded="false"
            aria-controls="tourbi-account-navigation"
        >
            <span><?php esc_html_e( 'Account menu', 'torby' ); ?></span>
            <span aria-hidden="true">☰</span>
        </button>
    </section>
    <?php
}
add_action(
    'woocommerce_before_account_navigation',
    'torby_child_render_account_user_card',
    10
);

/**
 * Add a predictable ID to the WooCommerce account navigation.
 *
 * @param string[] $items Existing menu items.
 * @return string[]
 */
function torby_child_account_menu_items( $items ) {
    if ( isset( $items['dashboard'] ) ) {
        $items['dashboard'] = __( 'Overview', 'torby' );
    }

    if ( isset( $items['edit-address'] ) ) {
        $items['edit-address'] = __( 'Addresses', 'torby' );
    }

    if ( isset( $items['edit-account'] ) ) {
        $items['edit-account'] = __( 'Account details', 'torby' );
    }

    return $items;
}
add_filter(
    'woocommerce_account_menu_items',
    'torby_child_account_menu_items',
    20
);

/**
 * Add navigation attributes used by the responsive account menu.
 *
 * @param string $template      Template path.
 * @param string $template_name Template name.
 * @param array  $args          Template arguments.
 * @param string $template_path Template path prefix.
 * @param string $default_path  Default template path.
 * @return string
 */
function torby_child_account_navigation_template(
    $template,
    $template_name,
    $args,
    $template_path,
    $default_path
) {
    return $template;
}

/**
 * Return the preferred Experience marketplace URL.
 *
 * The child theme first uses the Tourbi marketplace helper when available,
 * then falls back to the public Experiences page or rental-item archive.
 *
 * @return string
 */
function torby_child_get_experience_page_url() {
    if ( function_exists( 'tourbi_theme_get_experience_archive_url' ) ) {
        return tourbi_theme_get_experience_archive_url();
    }

    $experience_page = get_page_by_path(
        'experiences',
        OBJECT,
        'page'
    );

    if ( $experience_page instanceof WP_Post ) {
        return get_permalink( $experience_page );
    }

    $archive = get_post_type_archive_link(
        'rbfw_item'
    );

    return $archive ?: home_url( '/experiences/' );
}

/**
 * Return the preferred rental page URL.
 *
 * @return string
 */
function torby_child_get_rental_page_url() {
    if ( function_exists( 'tourbi_theme_get_rent_now_url' ) ) {
        return tourbi_theme_get_rent_now_url();
    }

    foreach (
        array(
            'bike-rentals',
            'rent-a-bike',
            'rentals',
        ) as $slug
    ) {
        $rental_page = get_page_by_path(
            $slug,
            OBJECT,
            'page'
        );

        if ( $rental_page instanceof WP_Post ) {
            return get_permalink( $rental_page );
        }
    }

    $archive = get_post_type_archive_link(
        'rbfw_item'
    );

    return $archive ?: home_url( '/rent/' );
}

/**
 * Render the custom customer account dashboard overview.
 *
 * @return void
 */
function torby_child_render_account_dashboard_overview() {
    if ( ! is_user_logged_in() || ! torby_child_is_account_dashboard() ) {
        return;
    }

    $user_id      = get_current_user_id();
    $user          = wp_get_current_user();
    $display_name  = $user instanceof WP_User && $user->display_name
        ? $user->display_name
        : __( 'Rider', 'torby' );
    $order_count   = function_exists( 'wc_get_customer_order_count' )
        ? absint( wc_get_customer_order_count( $user_id ) )
        : 0;
    $total_spent   = function_exists( 'wc_get_customer_total_spent' )
        ? wc_get_customer_total_spent( $user_id )
        : 0;
    $active_orders = array();

    if ( function_exists( 'wc_get_orders' ) ) {
        $active_orders = wc_get_orders(
            array(
                'customer_id' => $user_id,
                'status'      => array( 'pending', 'processing', 'on-hold' ),
                'limit'       => -1,
                'return'      => 'ids',
            )
        );
    }

    $active_order_count = is_array( $active_orders )
        ? count( $active_orders )
        : 0;
    $orders_url         = function_exists( 'wc_get_account_endpoint_url' )
        ? wc_get_account_endpoint_url( 'orders' )
        : home_url( '/my-account/orders/' );
    $account_url        = function_exists( 'wc_get_account_endpoint_url' )
        ? wc_get_account_endpoint_url( 'edit-account' )
        : home_url( '/my-account/edit-account/' );
    $experiences_url    = torby_child_get_experience_page_url();
    $rental_url         = torby_child_get_rental_page_url();
    ?>
    <section class="tourbi-account-dashboard-hero">
        <div>
            <span class="tourbi-account-eyebrow">
                <?php esc_html_e( 'Customer Dashboard', 'torby' ); ?>
            </span>

            <h1>
                <?php
                printf(
                    esc_html__( 'Welcome back, %s', 'torby' ),
                    esc_html( $display_name )
                );
                ?>
            </h1>

            <p>
                <?php
                esc_html_e(
                    'Discover hosted Experiences, rent an e-bike, review your orders, and keep your rider profile up to date.',
                    'torby'
                );
                ?>
            </p>
        </div>

        <div class="tourbi-account-dashboard-hero__actions" aria-label="<?php esc_attr_e( 'Booking options', 'torby' ); ?>">
            <a
                class="tourbi-button tourbi-button--primary tourbi-account-booking-action tourbi-account-booking-action--experience"
                href="<?php echo esc_url( $experiences_url ); ?>"
            >
                <?php esc_html_e( 'Book an Experience', 'torby' ); ?>
            </a>

            <a
                class="tourbi-button tourbi-button--secondary tourbi-account-booking-action tourbi-account-booking-action--rental"
                href="<?php echo esc_url( $rental_url ); ?>"
            >
                <?php esc_html_e( 'Rent a Bike', 'torby' ); ?>
            </a>
        </div>
    </section>

    <section class="tourbi-account-stats" aria-label="Account overview">
        <article class="tourbi-account-stat-card">
            <span class="tourbi-account-stat-card__icon"><?php echo torby_child_get_account_stat_icon( 'orders' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
            <div>
                <strong><?php echo esc_html( number_format_i18n( $order_count ) ); ?></strong>
                <span><?php esc_html_e( 'Total orders', 'torby' ); ?></span>
            </div>
        </article>

        <article class="tourbi-account-stat-card">
            <span class="tourbi-account-stat-card__icon"><?php echo torby_child_get_account_stat_icon( 'active' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
            <div>
                <strong><?php echo esc_html( number_format_i18n( $active_order_count ) ); ?></strong>
                <span><?php esc_html_e( 'Active orders', 'torby' ); ?></span>
            </div>
        </article>

        <article class="tourbi-account-stat-card">
            <span class="tourbi-account-stat-card__icon"><?php echo torby_child_get_account_stat_icon( 'spent' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
            <div>
                <strong>
                    <?php
                    echo wp_kses_post(
                        function_exists( 'wc_price' )
                            ? wc_price( $total_spent )
                            : esc_html( $total_spent )
                    );
                    ?>
                </strong>
                <span><?php esc_html_e( 'Total spent', 'torby' ); ?></span>
            </div>
        </article>

        <article class="tourbi-account-stat-card">
            <span class="tourbi-account-stat-card__icon"><?php echo torby_child_get_account_stat_icon( 'status' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
            <div>
                <strong><?php esc_html_e( 'Active', 'torby' ); ?></strong>
                <span><?php esc_html_e( 'Account status', 'torby' ); ?></span>
            </div>
        </article>
    </section>

    <section class="tourbi-account-quick-panel">
        <div>
            <span class="tourbi-account-eyebrow">
                <?php esc_html_e( 'Quick access', 'torby' ); ?>
            </span>
            <h2><?php esc_html_e( 'Ready for your next ride?', 'torby' ); ?></h2>
            <p>
                <?php
                esc_html_e(
                    'Review your latest orders or update your profile before making the next booking.',
                    'torby'
                );
                ?>
            </p>
        </div>

        <div class="tourbi-account-quick-panel__links">
            <a href="<?php echo esc_url( $orders_url ); ?>">
                <?php esc_html_e( 'Order history', 'torby' ); ?>
                <span aria-hidden="true">→</span>
            </a>

            <a href="<?php echo esc_url( $account_url ); ?>">
                <?php esc_html_e( 'Update profile', 'torby' ); ?>
                <span aria-hidden="true">→</span>
            </a>
        </div>
    </section>
    <?php
}
add_action(
    'woocommerce_account_content',
    'torby_child_render_account_dashboard_overview',
    1
);

/**
 * Add custom image sizes to the WordPress media selector.
 *
 * @param array<string,string> $sizes Existing image sizes.
 * @return array<string,string>
 */
function torby_child_image_size_names( $sizes ) {
    $sizes['torby-experience-card'] = esc_html__(
        'Torby Experience Card',
        'torby'
    );

    $sizes['torby-experience-hero'] = esc_html__(
        'Torby Experience Hero',
        'torby'
    );

    $sizes['torby-host-avatar'] = esc_html__(
        'Torby Host Avatar',
        'torby'
    );

    return $sizes;
}
add_filter( 'image_size_names_choose', 'torby_child_image_size_names' );

/**
 * Remove the default Hello Elementor page title only when the current page
 * was built with Elementor. WooCommerce and other non-Elementor pages keep
 * their normal headings.
 *
 * @param bool $display_title Whether the title should be displayed.
 * @return bool
 */
function torby_child_maybe_hide_elementor_page_title( $display_title ) {
    if ( ! is_singular() || ! did_action( 'elementor/loaded' ) ) {
        return $display_title;
    }

    $post_id = get_queried_object_id();

    if ( ! $post_id || ! class_exists( '\Elementor\Plugin' ) ) {
        return $display_title;
    }

    $elementor = \Elementor\Plugin::$instance;

    if (
        isset( $elementor->db ) &&
        method_exists( $elementor->db, 'is_built_with_elementor' ) &&
        $elementor->db->is_built_with_elementor( $post_id )
    ) {
        return false;
    }

    return $display_title;
}
add_filter(
    'hello_elementor_page_title',
    'torby_child_maybe_hide_elementor_page_title'
);

/**
 * Add a dedicated body class to the WCFM vendor registration page.
 *
 * @param string[] $classes Existing body classes.
 * @return string[]
 */
function torby_child_vendor_registration_body_class( $classes ) {
    if ( is_page( 'vendor-register' ) ) {
        $classes[] = 'tourbi-vendor-registration-page';
    }

    return array_values( array_unique( $classes ) );
}
add_filter(
    'body_class',
    'torby_child_vendor_registration_body_class',
    30
);

/**
 * Return the customer account page URL.
 *
 * @return string
 */
function torby_child_get_customer_account_url() {
    if ( function_exists( 'wc_get_page_permalink' ) ) {
        $account_url = wc_get_page_permalink( 'myaccount' );

        if ( $account_url ) {
            return $account_url;
        }
    }

    return home_url( '/my-account/' );
}

/**
 * Wrap the WCFM vendor registration shortcode output in the Tourbi host
 * onboarding layout.
 *
 * This changes presentation only. WCFM continues to process registration,
 * validation, approval, and account creation.
 *
 * @param string $content Page content after shortcode rendering.
 * @return string
 */
function torby_child_wrap_vendor_registration_content( $content ) {
    if (
        ! is_page( 'vendor-register' ) ||
        ! is_main_query() ||
        ! in_the_loop()
    ) {
        return $content;
    }

    if ( false !== strpos( $content, 'tourbi-host-registration-shell' ) ) {
        return $content;
    }

    $account_url = torby_child_get_customer_account_url();

    ob_start();
    ?>
    <section class="tourbi-host-registration-shell" aria-labelledby="tourbi-host-registration-title">
        <aside class="tourbi-host-registration-intro">
            <span class="tourbi-host-registration-kicker">
                <?php esc_html_e( 'Host Portal', 'torby' ); ?>
            </span>

            <h1 id="tourbi-host-registration-title">
                <?php esc_html_e( 'Share great rides with Tourbi', 'torby' ); ?>
            </h1>

            <p class="tourbi-host-registration-lead">
                <?php
                esc_html_e(
                    'Create your host account, submit your store for approval, and manage your Tourbi listings from one dashboard.',
                    'torby'
                );
                ?>
            </p>

            <div class="tourbi-host-registration-benefits" aria-label="Host benefits">
                <div>
                    <span aria-hidden="true">✓</span>
                    <p>
                        <strong><?php esc_html_e( 'Simple onboarding', 'torby' ); ?></strong>
                        <small><?php esc_html_e( 'Create your host profile in a few steps.', 'torby' ); ?></small>
                    </p>
                </div>

                <div>
                    <span aria-hidden="true">✓</span>
                    <p>
                        <strong><?php esc_html_e( 'Host dashboard', 'torby' ); ?></strong>
                        <small><?php esc_html_e( 'Review listings, orders, earnings, and withdrawals.', 'torby' ); ?></small>
                    </p>
                </div>

                <div>
                    <span aria-hidden="true">✓</span>
                    <p>
                        <strong><?php esc_html_e( '85% host earning', 'torby' ); ?></strong>
                        <small><?php esc_html_e( 'Current marketplace commission settings allocate 85% to the host.', 'torby' ); ?></small>
                    </p>
                </div>
            </div>

            <div class="tourbi-host-registration-steps" aria-label="Registration process">
                <span><?php esc_html_e( '1. Register', 'torby' ); ?></span>
                <span><?php esc_html_e( '2. Get approved', 'torby' ); ?></span>
                <span><?php esc_html_e( '3. Set up your store', 'torby' ); ?></span>
                <span><?php esc_html_e( '4. Start hosting', 'torby' ); ?></span>
            </div>
        </aside>

        <div class="tourbi-host-registration-form-card">
            <span class="tourbi-host-registration-kicker">
                <?php esc_html_e( 'Host application', 'torby' ); ?>
            </span>

            <h2><?php esc_html_e( 'Create your host account', 'torby' ); ?></h2>

            <p class="tourbi-host-registration-form-card__intro">
                <?php
                esc_html_e(
                    'Enter your account and store information below. New host applications require administrator approval.',
                    'torby'
                );
                ?>
            </p>

            <div class="tourbi-host-registration-form__plugin">
                <?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </div>

            <p class="tourbi-host-registration-login-link">
                <?php esc_html_e( 'Already have an account?', 'torby' ); ?>
                <a href="<?php echo esc_url( $account_url ); ?>">
                    <?php esc_html_e( 'Log in here', 'torby' ); ?>
                </a>
            </p>
        </div>
    </section>
    <?php

    return (string) ob_get_clean();
}
add_filter(
    'the_content',
    'torby_child_wrap_vendor_registration_content',
    30
);


/**
 * Load the Tourbi hybrid custom-template foundation.
 *
 * The existing Elementor homepage remains untouched. New functional screens
 * will use namespaced PHP templates, CSS, and JavaScript loaded only on Tourbi
 * custom surfaces.
 */
if ( ! defined( 'TOURBI_THEME_FOUNDATION_VERSION' ) ) {
    define( 'TOURBI_THEME_FOUNDATION_VERSION', '1.7.0' );
}

/**
 * Load one presentation module from the child theme.
 *
 * @param string $relative_path Relative file path inside the child theme.
 * @return void
 */
function tourbi_theme_load_module( $relative_path ) {
    $relative_path = ltrim( (string) $relative_path, '/' );
    $file_path     = trailingslashit( get_stylesheet_directory() ) . $relative_path;

    if ( file_exists( $file_path ) ) {
        require_once $file_path;
    }
}

tourbi_theme_load_module( 'inc/foundation-setup.php' );
tourbi_theme_load_module( 'inc/elementor-homepage-protection.php' );
tourbi_theme_load_module( 'inc/foundation-assets.php' );
tourbi_theme_load_module( 'inc/template-routing.php' );
tourbi_theme_load_module( 'inc/template-helpers.php' );
tourbi_theme_load_module( 'inc/experience-view-model.php' );
tourbi_theme_load_module( 'inc/rental-view-model.php' );
tourbi_theme_load_module( 'inc/rental-marketplace.php' );
tourbi_theme_load_module( 'inc/shop-hub.php' );
tourbi_theme_load_module( 'inc/experience-query.php' );
tourbi_theme_load_module( 'inc/experience-showcase.php' );
tourbi_theme_load_module( 'inc/become-host-page.php' );
tourbi_theme_load_module( 'inc/host-income-calculator.php' );
tourbi_theme_load_module( 'inc/host-store-experiences.php' );
tourbi_theme_load_module( 'inc/site-chrome.php' );
