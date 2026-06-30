<?php
/**
 * Dynamic Experience showcase landing page.
 *
 * Adds presentation-only settings for the Experience landing page, category
 * sections, and Experience cards. Booking, inventory, availability, and
 * checkout data remain owned by Tourbi Core / WpRently.
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Return the Experience category taxonomy name.
 *
 * @return string
 */
function tourbi_showcase_category_taxonomy() {
    return defined( 'TOURBI_CORE_EXPERIENCE_CATEGORY_TAXONOMY' )
        ? TOURBI_CORE_EXPERIENCE_CATEGORY_TAXONOMY
        : 'tourbi_experience_category';
}

/**
 * Return the Experience landing page ID.
 *
 * @return int
 */
function tourbi_showcase_page_id() {
    if ( function_exists( 'tourbi_theme_get_marketplace_page_id' ) ) {
        return absint( tourbi_theme_get_marketplace_page_id() );
    }

    $page = get_page_by_path( 'experiences', OBJECT, 'page' );

    return $page instanceof WP_Post ? absint( $page->ID ) : 0;
}

/**
 * Return default landing-page settings.
 *
 * @return array<string,string>
 */
function tourbi_showcase_default_page_settings() {
    return array(
        'hero_title_top'       => __( 'Electric Bike', 'torby' ),
        'hero_title_accent'    => __( 'Adventures.', 'torby' ),
        'hero_subtitle'        => __( 'Ride. Explore. Connect.', 'torby' ),
        'benefit_1_icon'       => 'lightning',
        'benefit_1_title'      => __( 'Electric.', 'torby' ),
        'benefit_1_text'       => __( 'Effortless.', 'torby' ),
        'benefit_2_icon'       => 'pin',
        'benefit_2_title'      => __( 'Unforgettable', 'torby' ),
        'benefit_2_text'       => __( 'Moments.', 'torby' ),
        'benefit_3_icon'       => 'heart',
        'benefit_3_title'      => __( 'Good Vibes.', 'torby' ),
        'benefit_3_text'       => __( 'Great People.', 'torby' ),
        'cta_icon'             => 'calendar',
        'cta_title'            => __( 'Book Your Adventure Today', 'torby' ),
        'cta_text'             => __( 'Spots fill fast. Good vibes guaranteed.', 'torby' ),
        'cta_button'           => __( 'Explore Dates', 'torby' ),
        'cta_url'              => '',
        'empty_title'          => __( 'New adventures are coming soon.', 'torby' ),
        'empty_text'           => __( 'Publish an enabled Experience and assign an Experience category to display it here automatically.', 'torby' ),
        'hero_image_position'  => 'center center',
    );
}

/**
 * Return sanitized landing-page settings.
 *
 * @param int $page_id Page ID.
 * @return array<string,string>
 */
function tourbi_showcase_get_page_settings( $page_id = 0 ) {
    $page_id  = $page_id ? absint( $page_id ) : tourbi_showcase_page_id();
    $defaults = tourbi_showcase_default_page_settings();
    $stored   = $page_id ? get_post_meta( $page_id, '_tourbi_showcase_settings', true ) : array();
    $stored   = is_array( $stored ) ? $stored : array();
    $settings = wp_parse_args( $stored, $defaults );

    foreach ( $settings as $key => $value ) {
        if ( 'cta_url' === $key ) {
            $settings[ $key ] = esc_url_raw( (string) $value );
        } elseif ( 'hero_image_position' === $key ) {
            $settings[ $key ] = tourbi_showcase_sanitize_hero_position( $value );
        } else {
            $settings[ $key ] = sanitize_text_field( (string) $value );
        }
    }

    return $settings;
}


/**
 * Return allowed hero image positions.
 *
 * @return array<string,string>
 */
function tourbi_showcase_hero_position_choices() {
    return array(
        'center center' => __( 'Center', 'torby' ),
        'center top'    => __( 'Center top', 'torby' ),
        'center bottom' => __( 'Center bottom', 'torby' ),
        'right center'  => __( 'Right center', 'torby' ),
        'right top'     => __( 'Right top', 'torby' ),
        'right bottom'  => __( 'Right bottom', 'torby' ),
        'left center'   => __( 'Left center', 'torby' ),
        'left top'      => __( 'Left top', 'torby' ),
        'left bottom'   => __( 'Left bottom', 'torby' ),
    );
}

/**
 * Sanitize the hero image position.
 *
 * @param string $position Position value.
 * @return string
 */
function tourbi_showcase_sanitize_hero_position( $position ) {
    $position = sanitize_text_field( (string) $position );
    $choices  = tourbi_showcase_hero_position_choices();

    return array_key_exists( $position, $choices ) ? $position : 'center center';
}

/**
 * Return icon choices used in admin controls.
 *
 * @return array<string,string>
 */
function tourbi_showcase_icon_choices() {
    return array(
        'lightning' => __( 'Lightning', 'torby' ),
        'pin'       => __( 'Location pin', 'torby' ),
        'heart'     => __( 'Heart', 'torby' ),
        'calendar'  => __( 'Calendar', 'torby' ),
        'food'      => __( 'Food / dining', 'torby' ),
        'city'      => __( 'City / buildings', 'torby' ),
        'games'     => __( 'Games / trophy', 'torby' ),
        'bike'      => __( 'Bicycle', 'torby' ),
        'star'      => __( 'Star', 'torby' ),
    );
}

/**
 * Return an inline SVG icon.
 *
 * @param string $icon Icon key.
 * @return string
 */
function tourbi_showcase_icon_svg( $icon ) {
    $icons = array(
        'lightning' => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M13.2 2 5 13h6l-.8 9L19 10h-6l.2-8Z"/></svg>',
        'pin'       => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 22s7-6.1 7-13A7 7 0 1 0 5 9c0 6.9 7 13 7 13Z"/><circle cx="12" cy="9" r="2.4"/></svg>',
        'heart'     => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8Z"/></svg>',
        'calendar'  => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"/></svg>',
        'food'      => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 3v7M7 3v7M10 3v7M7 10v11M16 3v18M16 3c3 2.4 4 5.1 4 8h-4"/></svg>',
        'city'      => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M3 21V9h5v12M8 21V4h6v17M14 21v-9h7v9M5 12h1M5 15h1M10 7h2M10 10h2M10 13h2M17 15h1M17 18h1M2 21h20"/></svg>',
        'games'     => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M7 4h10v4a5 5 0 0 1-10 0V4Z"/><path d="M7 6H4v2a4 4 0 0 0 4 4M17 6h3v2a4 4 0 0 1-4 4M12 13v4M8 21h8M10 17h4"/></svg>',
        'bike'      => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="5" cy="17" r="4"/><circle cx="19" cy="17" r="4"/><path d="m5 17 4-8h4l3 8M9 9l4 8H5M13 17h6M8 6h4"/></svg>',
        'star'      => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m12 2 3 6 6.6 1-4.8 4.7 1.1 6.6-5.9-3.1-5.9 3.1 1.1-6.6L2.4 9 9 8l3-6Z"/></svg>',
        'clock'     => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>',
        'arrow'     => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 12h14M14 7l5 5-5 5"/></svg>',
    );

    $icon = sanitize_key( $icon );

    return $icons[ $icon ] ?? $icons['star'];
}

/**
 * Register the Experience landing-page settings meta box.
 *
 * @return void
 */
function tourbi_showcase_register_page_meta_box() {
    add_meta_box(
        'tourbi-experience-showcase-settings',
        __( 'Experience Page Design & Content', 'torby' ),
        'tourbi_showcase_render_page_meta_box',
        'page',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes_page', 'tourbi_showcase_register_page_meta_box' );

/**
 * Render the page settings meta box only on the Experience page/template.
 *
 * @param WP_Post $post Current page.
 * @return void
 */
function tourbi_showcase_render_page_meta_box( $post ) {
    $template = get_post_meta( $post->ID, '_wp_page_template', true );
    $is_experience_page = 'experiences' === $post->post_name ||
        'templates/archive-tourbi-experiences.php' === $template ||
        absint( $post->ID ) === tourbi_showcase_page_id();

    if ( ! $is_experience_page ) {
        echo '<p>' . esc_html__( 'These settings are used only by the Experiences page template.', 'torby' ) . '</p>';
        return;
    }

    $settings = tourbi_showcase_get_page_settings( $post->ID );
    $icons    = tourbi_showcase_icon_choices();

    wp_nonce_field( 'tourbi_showcase_save_page', 'tourbi_showcase_page_nonce' );
    ?>
    <style>
        .tourbi-showcase-admin-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px 24px}.tourbi-showcase-admin-grid .wide{grid-column:1/-1}.tourbi-showcase-admin-grid label{display:grid;gap:7px;font-weight:600}.tourbi-showcase-admin-grid input,.tourbi-showcase-admin-grid select{width:100%}.tourbi-showcase-admin-note{padding:12px 14px;background:#f6f7f7;border-left:4px solid #8b3fe0}.tourbi-showcase-admin-divider{grid-column:1/-1;margin:4px 0 0;border-top:1px solid #dcdcde;padding-top:14px;font-size:16px}
        @media(max-width:782px){.tourbi-showcase-admin-grid{grid-template-columns:1fr}}
    </style>
    <p class="tourbi-showcase-admin-note">
        <?php esc_html_e( 'The hero uses assets/images/experience-hero-image.png. All published, enabled Experiences and their Featured Images are loaded automatically from the database and grouped by Experience Category.', 'torby' ); ?>
    </p>
    <div class="tourbi-showcase-admin-grid">
        <h3 class="tourbi-showcase-admin-divider"><?php esc_html_e( 'Hero content', 'torby' ); ?></h3>
        <?php
        $simple_fields = array(
            'hero_title_top'      => __( 'Hero title — first line', 'torby' ),
            'hero_title_accent'   => __( 'Hero title — orange line', 'torby' ),
            'hero_subtitle'       => __( 'Hero subtitle', 'torby' ),
        );
        foreach ( $simple_fields as $key => $label ) :
            ?>
            <label>
                <span><?php echo esc_html( $label ); ?></span>
                <input type="text" name="tourbi_showcase[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $settings[ $key ] ); ?>">
            </label>
        <?php endforeach; ?>

        <label>
            <span><?php esc_html_e( 'Hero image position', 'torby' ); ?></span>
            <select name="tourbi_showcase[hero_image_position]">
                <?php foreach ( tourbi_showcase_hero_position_choices() as $position_key => $position_label ) : ?>
                    <option value="<?php echo esc_attr( $position_key ); ?>" <?php selected( $settings['hero_image_position'], $position_key ); ?>><?php echo esc_html( $position_label ); ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <?php for ( $i = 1; $i <= 3; $i++ ) : ?>
            <h3 class="tourbi-showcase-admin-divider">
                <?php echo esc_html( sprintf( __( 'Hero benefit %d', 'torby' ), $i ) ); ?>
            </h3>
            <label>
                <span><?php esc_html_e( 'Icon', 'torby' ); ?></span>
                <select name="tourbi_showcase[benefit_<?php echo esc_attr( $i ); ?>_icon]">
                    <?php foreach ( $icons as $icon_key => $icon_label ) : ?>
                        <option value="<?php echo esc_attr( $icon_key ); ?>" <?php selected( $settings[ 'benefit_' . $i . '_icon' ], $icon_key ); ?>><?php echo esc_html( $icon_label ); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                <span><?php esc_html_e( 'First line', 'torby' ); ?></span>
                <input type="text" name="tourbi_showcase[benefit_<?php echo esc_attr( $i ); ?>_title]" value="<?php echo esc_attr( $settings[ 'benefit_' . $i . '_title' ] ); ?>">
            </label>
            <label>
                <span><?php esc_html_e( 'Second line', 'torby' ); ?></span>
                <input type="text" name="tourbi_showcase[benefit_<?php echo esc_attr( $i ); ?>_text]" value="<?php echo esc_attr( $settings[ 'benefit_' . $i . '_text' ] ); ?>">
            </label>
        <?php endfor; ?>

        <h3 class="tourbi-showcase-admin-divider"><?php esc_html_e( 'Bottom call to action', 'torby' ); ?></h3>
        <label>
            <span><?php esc_html_e( 'CTA icon', 'torby' ); ?></span>
            <select name="tourbi_showcase[cta_icon]">
                <?php foreach ( $icons as $icon_key => $icon_label ) : ?>
                    <option value="<?php echo esc_attr( $icon_key ); ?>" <?php selected( $settings['cta_icon'], $icon_key ); ?>><?php echo esc_html( $icon_label ); ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <?php
        $cta_fields = array(
            'cta_title'   => __( 'CTA title', 'torby' ),
            'cta_text'    => __( 'CTA text', 'torby' ),
            'cta_button'  => __( 'CTA button label', 'torby' ),
            'cta_url'     => __( 'CTA button URL', 'torby' ),
            'empty_title' => __( 'Empty-state title', 'torby' ),
            'empty_text'  => __( 'Empty-state text', 'torby' ),
        );
        foreach ( $cta_fields as $key => $label ) :
            ?>
            <label class="<?php echo in_array( $key, array( 'empty_title', 'empty_text' ), true ) ? 'wide' : ''; ?>">
                <span><?php echo esc_html( $label ); ?></span>
                <input type="<?php echo 'cta_url' === $key ? 'url' : 'text'; ?>" name="tourbi_showcase[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $settings[ $key ] ); ?>">
            </label>
        <?php endforeach; ?>
    </div>
    <?php
}

/**
 * Save Experience page settings.
 *
 * @param int $post_id Page ID.
 * @return void
 */
function tourbi_showcase_save_page_settings( $post_id ) {
    if (
        ! isset( $_POST['tourbi_showcase_page_nonce'] ) ||
        ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tourbi_showcase_page_nonce'] ) ), 'tourbi_showcase_save_page' ) ||
        ! current_user_can( 'edit_post', $post_id ) ||
        wp_is_post_autosave( $post_id ) ||
        wp_is_post_revision( $post_id ) ||
        ! isset( $_POST['tourbi_showcase'] ) ||
        ! is_array( $_POST['tourbi_showcase'] )
    ) {
        return;
    }

    $defaults = tourbi_showcase_default_page_settings();
    $raw      = wp_unslash( $_POST['tourbi_showcase'] );
    $clean    = array();

    foreach ( $defaults as $key => $default ) {
        $value = isset( $raw[ $key ] ) ? $raw[ $key ] : $default;
        if ( 'cta_url' === $key ) {
            $clean[ $key ] = esc_url_raw( (string) $value );
        } elseif ( 'hero_image_position' === $key ) {
            $clean[ $key ] = tourbi_showcase_sanitize_hero_position( $value );
        } else {
            $clean[ $key ] = sanitize_text_field( (string) $value );
        }
    }

    update_post_meta( $post_id, '_tourbi_showcase_settings', $clean );
}
add_action( 'save_post_page', 'tourbi_showcase_save_page_settings' );

/**
 * Register card display fields for Rent Items.
 *
 * @return void
 */
function tourbi_showcase_register_card_meta_box() {
    add_meta_box(
        'tourbi-experience-showcase-card',
        __( 'Experience Page Card', 'torby' ),
        'tourbi_showcase_render_card_meta_box',
        'rbfw_item',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes_rbfw_item', 'tourbi_showcase_register_card_meta_box' );

/**
 * Render card fields.
 *
 * @param WP_Post $post Current item.
 * @return void
 */
function tourbi_showcase_render_card_meta_box( $post ) {
    $values = array(
        'badge'       => get_post_meta( $post->ID, '_tourbi_showcase_badge', true ),
        'badge_color' => get_post_meta( $post->ID, '_tourbi_showcase_badge_color', true ),
        'rating'      => get_post_meta( $post->ID, '_tourbi_showcase_rating', true ),
        'reviews'     => get_post_meta( $post->ID, '_tourbi_showcase_reviews', true ),
        'excerpt'     => get_post_meta( $post->ID, '_tourbi_showcase_card_excerpt', true ),
        'order'       => get_post_meta( $post->ID, '_tourbi_showcase_order', true ),
    );

    wp_nonce_field( 'tourbi_showcase_save_card', 'tourbi_showcase_card_nonce' );
    ?>
    <p><?php esc_html_e( 'These optional values control how this Experience appears on the Experiences page.', 'torby' ); ?></p>
    <p><label><strong><?php esc_html_e( 'Badge text', 'torby' ); ?></strong><br><input class="widefat" type="text" name="tourbi_showcase_badge" value="<?php echo esc_attr( $values['badge'] ); ?>" placeholder="<?php esc_attr_e( 'Top Rated', 'torby' ); ?>"></label></p>
    <p><label><strong><?php esc_html_e( 'Badge color', 'torby' ); ?></strong><br><input class="widefat" type="color" name="tourbi_showcase_badge_color" value="<?php echo esc_attr( $values['badge_color'] ?: '#8f3fe0' ); ?>"></label></p>
    <p><label><strong><?php esc_html_e( 'Rating (0–5)', 'torby' ); ?></strong><br><input class="widefat" type="number" min="0" max="5" step="0.1" name="tourbi_showcase_rating" value="<?php echo esc_attr( $values['rating'] ); ?>"></label></p>
    <p><label><strong><?php esc_html_e( 'Review count', 'torby' ); ?></strong><br><input class="widefat" type="number" min="0" step="1" name="tourbi_showcase_reviews" value="<?php echo esc_attr( $values['reviews'] ); ?>"></label></p>
    <p><label><strong><?php esc_html_e( 'Card description', 'torby' ); ?></strong><br><textarea class="widefat" rows="4" name="tourbi_showcase_card_excerpt"><?php echo esc_textarea( $values['excerpt'] ); ?></textarea></label></p>
    <p><label><strong><?php esc_html_e( 'Display order', 'torby' ); ?></strong><br><input class="widefat" type="number" min="0" step="1" name="tourbi_showcase_order" value="<?php echo esc_attr( '' === $values['order'] ? '100' : $values['order'] ); ?>"></label></p>
    <?php
}

/**
 * Save card fields.
 *
 * @param int $post_id Item ID.
 * @return void
 */
function tourbi_showcase_save_card_settings( $post_id ) {
    if (
        ! isset( $_POST['tourbi_showcase_card_nonce'] ) ||
        ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tourbi_showcase_card_nonce'] ) ), 'tourbi_showcase_save_card' ) ||
        ! current_user_can( 'edit_post', $post_id ) ||
        wp_is_post_autosave( $post_id ) ||
        wp_is_post_revision( $post_id )
    ) {
        return;
    }

    $map = array(
        'tourbi_showcase_badge'       => array( '_tourbi_showcase_badge', 'sanitize_text_field' ),
        'tourbi_showcase_badge_color' => array( '_tourbi_showcase_badge_color', 'sanitize_hex_color' ),
        'tourbi_showcase_rating'      => array( '_tourbi_showcase_rating', 'floatval' ),
        'tourbi_showcase_reviews'     => array( '_tourbi_showcase_reviews', 'absint' ),
        'tourbi_showcase_card_excerpt'=> array( '_tourbi_showcase_card_excerpt', 'sanitize_textarea_field' ),
        'tourbi_showcase_order'       => array( '_tourbi_showcase_order', 'absint' ),
    );

    foreach ( $map as $field => $config ) {
        if ( ! isset( $_POST[ $field ] ) ) {
            continue;
        }

        $value = call_user_func( $config[1], wp_unslash( $_POST[ $field ] ) );
        update_post_meta( $post_id, $config[0], $value );
    }
}
add_action( 'save_post_rbfw_item', 'tourbi_showcase_save_card_settings', 110 );

/**
 * Add fields to the Experience category create form.
 *
 * @return void
 */
function tourbi_showcase_category_add_fields() {
    $icons = tourbi_showcase_icon_choices();
    ?>
    <div class="form-field">
        <label for="tourbi-showcase-icon"><?php esc_html_e( 'Section icon', 'torby' ); ?></label>
        <select id="tourbi-showcase-icon" name="tourbi_showcase_icon">
            <?php foreach ( $icons as $key => $label ) : ?>
                <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-field"><label for="tourbi-showcase-heading"><?php esc_html_e( 'Section heading override', 'torby' ); ?></label><input id="tourbi-showcase-heading" type="text" name="tourbi_showcase_heading"><p><?php esc_html_e( 'Leave blank to use the category name.', 'torby' ); ?></p></div>
    <div class="form-field"><label for="tourbi-showcase-accent"><?php esc_html_e( 'Accent color', 'torby' ); ?></label><input id="tourbi-showcase-accent" type="color" name="tourbi_showcase_accent" value="#9b42e6"></div>
    <div class="form-field"><label for="tourbi-showcase-order"><?php esc_html_e( 'Section order', 'torby' ); ?></label><input id="tourbi-showcase-order" type="number" name="tourbi_showcase_order" min="0" step="1" value="100"></div>
    <div class="form-field"><label for="tourbi-showcase-limit"><?php esc_html_e( 'Cards shown', 'torby' ); ?></label><input id="tourbi-showcase-limit" type="number" name="tourbi_showcase_limit" min="1" max="12" step="1" value="2"></div>
    <div class="form-field"><label><input type="checkbox" name="tourbi_showcase_hidden" value="1"> <?php esc_html_e( 'Hide this section on the Experiences page', 'torby' ); ?></label></div>
    <?php
}

/**
 * Add fields to the Experience category edit form.
 *
 * @param WP_Term $term Current term.
 * @return void
 */
function tourbi_showcase_category_edit_fields( $term ) {
    $icons   = tourbi_showcase_icon_choices();
    $icon    = get_term_meta( $term->term_id, '_tourbi_showcase_icon', true ) ?: 'star';
    $heading = get_term_meta( $term->term_id, '_tourbi_showcase_heading', true );
    $accent  = get_term_meta( $term->term_id, '_tourbi_showcase_accent', true ) ?: '#9b42e6';
    $order   = get_term_meta( $term->term_id, '_tourbi_showcase_order', true );
    $limit   = get_term_meta( $term->term_id, '_tourbi_showcase_limit', true );
    $hidden  = get_term_meta( $term->term_id, '_tourbi_showcase_hidden', true );
    ?>
    <tr class="form-field"><th scope="row"><label for="tourbi-showcase-icon"><?php esc_html_e( 'Section icon', 'torby' ); ?></label></th><td><select id="tourbi-showcase-icon" name="tourbi_showcase_icon"><?php foreach ( $icons as $key => $label ) : ?><option value="<?php echo esc_attr( $key ); ?>" <?php selected( $icon, $key ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select></td></tr>
    <tr class="form-field"><th scope="row"><label for="tourbi-showcase-heading"><?php esc_html_e( 'Section heading override', 'torby' ); ?></label></th><td><input id="tourbi-showcase-heading" type="text" name="tourbi_showcase_heading" value="<?php echo esc_attr( $heading ); ?>"><p class="description"><?php esc_html_e( 'Leave blank to use the category name.', 'torby' ); ?></p></td></tr>
    <tr class="form-field"><th scope="row"><label for="tourbi-showcase-accent"><?php esc_html_e( 'Accent color', 'torby' ); ?></label></th><td><input id="tourbi-showcase-accent" type="color" name="tourbi_showcase_accent" value="<?php echo esc_attr( $accent ); ?>"></td></tr>
    <tr class="form-field"><th scope="row"><label for="tourbi-showcase-order"><?php esc_html_e( 'Section order', 'torby' ); ?></label></th><td><input id="tourbi-showcase-order" type="number" name="tourbi_showcase_order" min="0" step="1" value="<?php echo esc_attr( '' === $order ? '100' : $order ); ?>"></td></tr>
    <tr class="form-field"><th scope="row"><label for="tourbi-showcase-limit"><?php esc_html_e( 'Cards shown', 'torby' ); ?></label></th><td><input id="tourbi-showcase-limit" type="number" name="tourbi_showcase_limit" min="1" max="12" step="1" value="<?php echo esc_attr( '' === $limit ? '2' : $limit ); ?>"></td></tr>
    <tr class="form-field"><th scope="row"><?php esc_html_e( 'Visibility', 'torby' ); ?></th><td><label><input type="checkbox" name="tourbi_showcase_hidden" value="1" <?php checked( $hidden, '1' ); ?>> <?php esc_html_e( 'Hide this section on the Experiences page', 'torby' ); ?></label></td></tr>
    <?php
}

/**
 * Save Experience category display fields.
 *
 * @param int $term_id Term ID.
 * @return void
 */
function tourbi_showcase_save_category_fields( $term_id ) {
    $icon = isset( $_POST['tourbi_showcase_icon'] ) ? sanitize_key( wp_unslash( $_POST['tourbi_showcase_icon'] ) ) : 'star';
    if ( ! array_key_exists( $icon, tourbi_showcase_icon_choices() ) ) {
        $icon = 'star';
    }

    $heading = isset( $_POST['tourbi_showcase_heading'] ) ? sanitize_text_field( wp_unslash( $_POST['tourbi_showcase_heading'] ) ) : '';
    $accent  = isset( $_POST['tourbi_showcase_accent'] ) ? sanitize_hex_color( wp_unslash( $_POST['tourbi_showcase_accent'] ) ) : '#9b42e6';
    $order   = isset( $_POST['tourbi_showcase_order'] ) ? absint( wp_unslash( $_POST['tourbi_showcase_order'] ) ) : 100;
    $limit   = isset( $_POST['tourbi_showcase_limit'] ) ? min( 12, max( 1, absint( wp_unslash( $_POST['tourbi_showcase_limit'] ) ) ) ) : 2;
    $hidden  = isset( $_POST['tourbi_showcase_hidden'] ) ? '1' : '0';

    update_term_meta( $term_id, '_tourbi_showcase_icon', $icon );
    update_term_meta( $term_id, '_tourbi_showcase_heading', $heading );
    update_term_meta( $term_id, '_tourbi_showcase_accent', $accent ?: '#9b42e6' );
    update_term_meta( $term_id, '_tourbi_showcase_order', $order );
    update_term_meta( $term_id, '_tourbi_showcase_limit', $limit );
    update_term_meta( $term_id, '_tourbi_showcase_hidden', $hidden );
}

/**
 * Attach term hooks after taxonomies are registered.
 *
 * @return void
 */
function tourbi_showcase_register_category_hooks() {
    $taxonomy = tourbi_showcase_category_taxonomy();

    add_action( $taxonomy . '_add_form_fields', 'tourbi_showcase_category_add_fields' );
    add_action( $taxonomy . '_edit_form_fields', 'tourbi_showcase_category_edit_fields' );
    add_action( 'created_' . $taxonomy, 'tourbi_showcase_save_category_fields' );
    add_action( 'edited_' . $taxonomy, 'tourbi_showcase_save_category_fields' );
}
add_action( 'init', 'tourbi_showcase_register_category_hooks', 40 );

/**
 * Return the current sanitized showcase filter state.
 *
 * The state reuses the marketplace filter names so the same database fields,
 * URLs, and sort modes remain compatible with the rest of the theme.
 *
 * @return array<string,mixed>
 */
function tourbi_showcase_get_filter_state() {
    if ( function_exists( 'tourbi_theme_get_marketplace_state' ) ) {
        return tourbi_theme_get_marketplace_state();
    }

    $search = isset( $_GET['experience_search'] )
        ? sanitize_text_field( wp_unslash( $_GET['experience_search'] ) )
        : '';
    $category = isset( $_GET['experience_category'] )
        ? sanitize_title( wp_unslash( $_GET['experience_category'] ) )
        : '';
    $location = isset( $_GET['experience_location'] )
        ? sanitize_text_field( wp_unslash( $_GET['experience_location'] ) )
        : '';
    $sort = isset( $_GET['experience_sort'] )
        ? sanitize_key( wp_unslash( $_GET['experience_sort'] ) )
        : 'recommended';

    $allowed_sorts = array( 'recommended', 'newest', 'price_low', 'price_high', 'duration_short' );
    if ( ! in_array( $sort, $allowed_sorts, true ) ) {
        $sort = 'recommended';
    }

    return array(
        'search'      => $search,
        'category'    => $category,
        'location'    => $location,
        'sort'        => $sort,
        'has_filters' => '' !== $search || '' !== $category || '' !== $location || 'recommended' !== $sort,
    );
}

/**
 * Return Experience IDs assigned to one category and matching active filters.
 *
 * @param WP_Term|null       $term Category term or null for uncategorized fallback.
 * @param int                $limit Number of cards.
 * @param array<string,mixed> $state Current filter state.
 * @return int[]
 */
function tourbi_showcase_get_experience_ids( $term = null, $limit = 2, $state = array() ) {
    $limit = min( 50, max( 1, absint( $limit ) ) );
    $state = wp_parse_args(
        is_array( $state ) ? $state : array(),
        array(
            'search'   => '',
            'location' => '',
            'tag'      => '',
            'sort'     => 'recommended',
        )
    );

    $args = array(
        'post_type'              => 'rbfw_item',
        'post_status'            => 'publish',
        'posts_per_page'         => 100,
        'fields'                 => 'ids',
        'no_found_rows'          => true,
        'ignore_sticky_posts'    => true,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => false,
        'orderby'                => array(
            'menu_order' => 'ASC',
            'modified'   => 'DESC',
            'date'       => 'DESC',
        ),
        'meta_query'             => array(
            array(
                'key'     => '_tourbi_experience_enabled',
                'value'   => 'yes',
                'compare' => '=',
            ),
        ),
    );

    if ( ! empty( $state['search'] ) ) {
        $args['s'] = sanitize_text_field( $state['search'] );
    }

    if ( ! empty( $state['location'] ) ) {
        $location = sanitize_text_field( $state['location'] );
        $args['meta_query'][] = array(
            'relation' => 'OR',
            array(
                'key'     => '_tourbi_experience_city',
                'value'   => $location,
                'compare' => '=',
            ),
            array(
                'key'     => '_tourbi_experience_meeting_address',
                'value'   => $location,
                'compare' => 'LIKE',
            ),
        );
    }

    $tax_query = array();

    if ( $term instanceof WP_Term ) {
        $tax_query[] = array(
            'taxonomy' => $term->taxonomy,
            'field'    => 'term_id',
            'terms'    => array( $term->term_id ),
        );
    }

    if ( ! empty( $state['tag'] ) ) {
        $tag_taxonomy = defined( 'TOURBI_CORE_EXPERIENCE_TAG_TAXONOMY' )
            ? TOURBI_CORE_EXPERIENCE_TAG_TAXONOMY
            : 'tourbi_experience_tag';
        if ( taxonomy_exists( $tag_taxonomy ) ) {
            $tax_query[] = array(
                'taxonomy' => $tag_taxonomy,
                'field'    => 'slug',
                'terms'    => sanitize_title( $state['tag'] ),
            );
        }
    }

    if ( ! empty( $tax_query ) ) {
        if ( count( $tax_query ) > 1 ) {
            $tax_query['relation'] = 'AND';
        }
        $args['tax_query'] = $tax_query;
    }

    $sort = sanitize_key( $state['sort'] );
    if ( 'price_low' === $sort ) {
        $args['meta_key'] = '_tourbi_experience_price_per_participant';
        $args['orderby']  = 'meta_value_num';
        $args['order']    = 'ASC';
    } elseif ( 'price_high' === $sort ) {
        $args['meta_key'] = '_tourbi_experience_price_per_participant';
        $args['orderby']  = 'meta_value_num';
        $args['order']    = 'DESC';
    } elseif ( 'duration_short' === $sort ) {
        $args['meta_key'] = '_tourbi_experience_duration_minutes';
        $args['orderby']  = 'meta_value_num';
        $args['order']    = 'ASC';
    } elseif ( 'newest' === $sort ) {
        $args['orderby'] = 'date';
        $args['order']   = 'DESC';
    }

    /**
     * Filter the showcase database query.
     *
     * @param array<string,mixed> $args Query arguments.
     * @param WP_Term|null        $term Current section term.
     * @param array<string,mixed> $state Current filter state.
     */
    $args = (array) apply_filters( 'tourbi_showcase_query_args', $args, $term, $state );

    $query = new WP_Query( $args );
    $ids   = array_map( 'absint', (array) $query->posts );

    if ( 'recommended' === $sort ) {
        usort(
            $ids,
            static function ( $a, $b ) {
                $a_order = get_post_meta( $a, '_tourbi_showcase_order', true );
                $b_order = get_post_meta( $b, '_tourbi_showcase_order', true );
                $a_order = '' === $a_order ? 100 : absint( $a_order );
                $b_order = '' === $b_order ? 100 : absint( $b_order );

                if ( $a_order === $b_order ) {
                    return strcasecmp( get_the_title( $a ), get_the_title( $b ) );
                }

                return $a_order <=> $b_order;
            }
        );
    }

    return array_slice( $ids, 0, $limit );
}

/**
 * Return category sections for the page.
 *
 * @param array<string,mixed> $state Current filter state.
 * @return array<int,array<string,mixed>>
 */
function tourbi_showcase_get_sections( $state = array() ) {
    $taxonomy = tourbi_showcase_category_taxonomy();
    $state    = wp_parse_args( is_array( $state ) ? $state : array(), tourbi_showcase_get_filter_state() );
    $filter   = sanitize_title( $state['category'] ?? '' );
    $sections = array();

    if ( taxonomy_exists( $taxonomy ) ) {
        $terms = get_terms(
            array(
                'taxonomy'   => $taxonomy,
                'hide_empty' => false,
            )
        );

        if ( ! is_wp_error( $terms ) ) {
            foreach ( $terms as $term ) {
                if ( $filter && $term->slug !== $filter ) {
                    continue;
                }

                if ( '1' === get_term_meta( $term->term_id, '_tourbi_showcase_hidden', true ) && ! $filter ) {
                    continue;
                }

                $configured_limit = get_term_meta( $term->term_id, '_tourbi_showcase_limit', true );
                $limit            = ! empty( $state['has_filters'] )
                    ? 12
                    : ( $configured_limit ? absint( $configured_limit ) : 2 );
                $ids              = tourbi_showcase_get_experience_ids( $term, $limit, $state );

                if ( empty( $ids ) ) {
                    continue;
                }

                $sections[] = array(
                    'term'      => $term,
                    'title'     => get_term_meta( $term->term_id, '_tourbi_showcase_heading', true ) ?: $term->name,
                    'icon'      => get_term_meta( $term->term_id, '_tourbi_showcase_icon', true ) ?: 'star',
                    'accent'    => get_term_meta( $term->term_id, '_tourbi_showcase_accent', true ) ?: '#9b42e6',
                    'order'     => get_term_meta( $term->term_id, '_tourbi_showcase_order', true ) !== '' ? absint( get_term_meta( $term->term_id, '_tourbi_showcase_order', true ) ) : 100,
                    'ids'       => $ids,
                    'is_filter' => ! empty( $state['has_filters'] ),
                );
            }
        }
    }

    usort(
        $sections,
        static function ( $a, $b ) {
            if ( $a['order'] === $b['order'] ) {
                return strcasecmp( $a['title'], $b['title'] );
            }
            return $a['order'] <=> $b['order'];
        }
    );

    if ( empty( $sections ) && ! $filter ) {
        $limit = ! empty( $state['has_filters'] ) ? 12 : 6;
        $ids   = tourbi_showcase_get_experience_ids( null, $limit, $state );
        if ( ! empty( $ids ) ) {
            $sections[] = array(
                'term'      => null,
                'title'     => __( 'Experiences on E-Bikes', 'torby' ),
                'icon'      => 'bike',
                'accent'    => '#9b42e6',
                'order'     => 100,
                'ids'       => $ids,
                'is_filter' => ! empty( $state['has_filters'] ),
            );
        }
    }

    return $sections;
}

/**
 * Return a normalized card model.
 *
 * @param int $experience_id Experience ID.
 * @return array<string,mixed>
 */
function tourbi_showcase_get_card( $experience_id ) {
    $experience_id = absint( $experience_id );
    $model = function_exists( 'tourbi_theme_get_marketplace_card' )
        ? tourbi_theme_get_marketplace_card( $experience_id )
        : array();

    /*
     * Always prefer the Experience main / Featured Image. The Host Experience
     * Builder stores that value as the normal post thumbnail, while older
     * mapped Experiences may keep it in the content profile. The shared home
     * helper already understands both storage formats, so use it when present.
     */
    $image_id = function_exists( 'tourbi_reference_home_get_main_image_id' )
        ? tourbi_reference_home_get_main_image_id( $experience_id, $model )
        : absint( get_post_thumbnail_id( $experience_id ) );

    if (
        ! $image_id &&
        function_exists( 'tourbi_core_get_experience_content_profile' )
    ) {
        $content_profile = (array) tourbi_core_get_experience_content_profile( $experience_id );
        $image_id        = absint( $content_profile['main_image_id'] ?? 0 );
    }

    $image = $image_id
        ? wp_get_attachment_image_url( $image_id, 'full' )
        : '';

    if ( ! $image && function_exists( 'tourbi_reference_home_get_main_image_url' ) ) {
        $image = tourbi_reference_home_get_main_image_url(
            $experience_id,
            $model,
            'card'
        );
    }

    // Fall back to the first saved Experience gallery image, then a theme image.
    if ( ! $image ) {
        $gallery = (array) ( $model['gallery'] ?? array() );
        $first   = (array) ( $gallery[0] ?? array() );

        foreach ( array( 'full', 'large', 'url', 'src' ) as $image_key ) {
            if ( ! empty( $first[ $image_key ] ) ) {
                $image = esc_url_raw( $first[ $image_key ] );
                break;
            }
        }
    }

    if ( ! $image ) {
        $image = $model['card_image'] ?? '';
    }
    if ( ! $image ) {
        $image = trailingslashit( get_stylesheet_directory_uri() ) . 'assets/images/tourbi-home/adventure-photo-quest.jpg';
    }

    $image_srcset = $image_id
        ? wp_get_attachment_image_srcset( $image_id, 'full' )
        : '';

    $summary = get_post_meta( $experience_id, '_tourbi_showcase_card_excerpt', true );
    if ( '' === trim( (string) $summary ) ) {
        $summary = $model['summary'] ?? get_the_excerpt( $experience_id );
    }
    if ( '' === trim( (string) $summary ) ) {
        $summary = wp_strip_all_tags( get_post_field( 'post_content', $experience_id ) );
    }

    $rating = get_post_meta( $experience_id, '_tourbi_showcase_rating', true );
    if ( '' === (string) $rating ) {
        $rating = get_post_meta( $experience_id, '_wc_average_rating', true );
    }
    $rating = max( 0, min( 5, (float) $rating ) );

    $reviews = get_post_meta( $experience_id, '_tourbi_showcase_reviews', true );
    if ( '' === (string) $reviews ) {
        $reviews = get_post_meta( $experience_id, '_wc_review_count', true );
    }

    return array(
        'id'          => $experience_id,
        'title'       => sanitize_text_field( $model['short_title'] ?? get_the_title( $experience_id ) ),
        'summary'     => sanitize_text_field( wp_trim_words( (string) $summary, 18, '…' ) ),
        'image_id'    => absint( $image_id ),
        'image'       => esc_url_raw( $image ),
        'image_srcset' => is_string( $image_srcset ) ? $image_srcset : '',
        'duration'    => sanitize_text_field( $model['duration_label'] ?? '' ),
        'price_html'  => $model['price_html'] ?? '',
        'permalink'   => esc_url_raw( $model['permalink'] ?? get_permalink( $experience_id ) ),
        'badge'       => sanitize_text_field( get_post_meta( $experience_id, '_tourbi_showcase_badge', true ) ),
        'badge_color' => sanitize_hex_color( get_post_meta( $experience_id, '_tourbi_showcase_badge_color', true ) ) ?: '#8f3fe0',
        'rating'      => $rating,
        'reviews'     => absint( $reviews ),
    );
}

/**
 * Add a body class for tightly scoped CSS.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function tourbi_showcase_body_class( $classes ) {
    if ( function_exists( 'tourbi_theme_is_marketplace_request' ) && tourbi_theme_is_marketplace_request() ) {
        $classes[] = 'tourbi-experience-showcase-page';
    }

    return array_values( array_unique( $classes ) );
}
add_filter( 'body_class', 'tourbi_showcase_body_class', 60 );
