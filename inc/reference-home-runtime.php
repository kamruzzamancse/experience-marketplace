<?php
/**
 * Runtime helpers for the Tourbi reference Home page.
 *
 * This page uses the shared Tourbi child-theme header and footer. The Home
 * template only owns the page content and its isolated design assets.
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'tourbi_reference_home_prepare' ) ) {
    /**
     * Prepare the Home page before get_header() renders.
     *
     * @return void
     */
    function tourbi_reference_home_prepare() {
        /*
         * Ensure a manually assigned Home template also receives the shared
         * Tourbi header/footer even when it is not yet selected as front page.
         */
        add_filter(
            'tourbi_theme_custom_chrome_page_slugs',
            static function ( $slugs ) {
                $slugs = (array) $slugs;
                $slugs[] = 'home';

                $queried_id = get_queried_object_id();

                if ( $queried_id ) {
                    $current_slug = get_post_field(
                        'post_name',
                        $queried_id
                    );

                    if ( is_string( $current_slug ) && '' !== $current_slug ) {
                        $slugs[] = sanitize_title( $current_slug );
                    }
                }

                return array_values(
                    array_unique(
                        array_filter(
                            array_map( 'sanitize_title', $slugs )
                        )
                    )
                );
            },
            20
        );

        add_filter(
            'body_class',
            static function ( $classes ) {
                $classes[] = 'tourbi-reference-home';

                return array_values(
                    array_unique( (array) $classes )
                );
            },
            999
        );

        $theme_dir = get_stylesheet_directory();
        $theme_uri = get_stylesheet_directory_uri();
        $css_path  = '/assets/css/tourbi-reference-home.css';
        $js_path   = '/assets/js/tourbi-reference-home.js';

        wp_enqueue_style(
            'tourbi-reference-home-fonts',
            'https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@400;500;600;700&display=swap',
            array(),
            null
        );

        if ( file_exists( $theme_dir . $css_path ) ) {
            wp_enqueue_style(
                'tourbi-reference-home',
                $theme_uri . $css_path,
                array( 'tourbi-reference-home-fonts' ),
                (string) filemtime( $theme_dir . $css_path )
            );
        }

        if ( file_exists( $theme_dir . $js_path ) ) {
            wp_enqueue_script(
                'tourbi-reference-home',
                $theme_uri . $js_path,
                array(),
                (string) filemtime( $theme_dir . $js_path ),
                true
            );
        }
    }
}

if ( ! function_exists( 'tourbi_reference_home_asset_url' ) ) {
    /**
     * Return a URL for a bundled Home page asset.
     *
     * @param string $filename File name inside assets/images/tourbi-home.
     * @return string
     */
    function tourbi_reference_home_asset_url( $filename ) {
        return trailingslashit(
            get_stylesheet_directory_uri()
        ) . 'assets/images/tourbi-home/' . ltrim(
            sanitize_file_name( $filename ),
            '/'
        );
    }
}

if ( ! function_exists( 'tourbi_reference_home_logo_url' ) ) {
    /**
     * Return the main Tourbi logo with a bundled fallback.
     *
     * @return string
     */
    function tourbi_reference_home_logo_url() {
        if ( function_exists( 'tourbi_theme_get_required_logo_url' ) ) {
            return tourbi_theme_get_required_logo_url();
        }

        return tourbi_reference_home_asset_url(
            'tourbi-logo-reference.png'
        );
    }
}

if ( ! function_exists( 'tourbi_reference_home_experiences_url' ) ) {
    /**
     * Return the public Experiences marketplace URL.
     *
     * @return string
     */
    function tourbi_reference_home_experiences_url() {
        if ( function_exists( 'tourbi_theme_get_experience_archive_url' ) ) {
            return tourbi_theme_get_experience_archive_url();
        }

        return home_url( '/experiences/' );
    }
}

if ( ! function_exists( 'tourbi_reference_home_account_url' ) ) {
    /**
     * Return the public account URL.
     *
     * @return string
     */
    function tourbi_reference_home_account_url() {
        if ( function_exists( 'tourbi_theme_get_site_account_url' ) ) {
            return tourbi_theme_get_site_account_url();
        }

        if ( function_exists( 'wc_get_page_permalink' ) ) {
            $url = wc_get_page_permalink( 'myaccount' );

            if ( $url ) {
                return $url;
            }
        }

        return home_url( '/my-account/' );
    }
}

if ( ! function_exists( 'tourbi_reference_home_host_url' ) ) {
    /**
     * Return the public Become a Host URL.
     *
     * @return string
     */
    function tourbi_reference_home_host_url() {
        if ( function_exists( 'tourbi_theme_get_become_host_url' ) ) {
            return tourbi_theme_get_become_host_url();
        }

        return home_url( '/become-a-host/' );
    }
}

if ( ! function_exists( 'tourbi_reference_home_get_experience_ids' ) ) {
    /**
     * Return published guided Experiences in descending publish-date order.
     *
     * @param int   $limit Number of IDs to return.
     * @param int[] $exclude IDs to exclude.
     * @return int[]
     */
    function tourbi_reference_home_get_experience_ids(
        $limit = 4,
        $exclude = array()
    ) {
        $args = function_exists(
            'tourbi_theme_get_marketplace_base_query_args'
        )
            ? tourbi_theme_get_marketplace_base_query_args()
            : array(
                'post_type'   => 'rbfw_item',
                'post_status' => 'publish',
                'meta_query'  => array(
                    array(
                        'key'     => '_tourbi_experience_enabled',
                        'value'   => 'yes',
                        'compare' => '=',
                    ),
                ),
            );

        $args['posts_per_page'] = max( 1, absint( $limit ) );
        $args['fields']         = 'ids';
        $args['no_found_rows']  = true;
        $args['orderby']        = 'date';
        $args['order']          = 'DESC';
        $args['post__not_in']   = array_values(
            array_filter(
                array_map( 'absint', (array) $exclude )
            )
        );

        $query = new WP_Query( $args );

        return array_values(
            array_filter(
                array_map( 'absint', (array) $query->posts )
            )
        );
    }
}

if ( ! function_exists( 'tourbi_reference_home_get_featured_experience' ) ) {
    /**
     * Return the featured Experience, falling back to the newest published one.
     *
     * @return array<string,mixed>
     */
    function tourbi_reference_home_get_featured_experience() {
        $state = array(
            'has_filters' => false,
            'page'        => 1,
        );

        if ( function_exists( 'tourbi_theme_get_marketplace_featured_experience' ) ) {
            $featured = tourbi_theme_get_marketplace_featured_experience(
                $state
            );

            if ( ! empty( $featured['id'] ) ) {
                return $featured;
            }
        }

        $ids = tourbi_reference_home_get_experience_ids( 1 );

        if (
            ! empty( $ids[0] ) &&
            function_exists( 'tourbi_theme_get_single_experience_view_model' )
        ) {
            return tourbi_theme_get_single_experience_view_model(
                $ids[0]
            );
        }

        return array();
    }
}

if ( ! function_exists( 'tourbi_reference_home_get_card' ) ) {
    /**
     * Return one normalized card view model.
     *
     * @param int $experience_id Experience ID.
     * @return array<string,mixed>
     */
    function tourbi_reference_home_get_card( $experience_id ) {
        if ( function_exists( 'tourbi_theme_get_marketplace_card' ) ) {
            return tourbi_theme_get_marketplace_card(
                absint( $experience_id )
            );
        }

        return array(
            'id'                => absint( $experience_id ),
            'short_title'       => get_the_title( $experience_id ),
            'summary'           => get_the_excerpt( $experience_id ),
            'duration_label'    => '',
            'participant_label' => '',
            'price_html'        => '',
            'permalink'         => get_permalink( $experience_id ),
        );
    }
}

if ( ! function_exists( 'tourbi_reference_home_get_main_image_id' ) ) {
    /**
     * Return the original WordPress Featured Image attachment ID.
     *
     * The Host Experience Builder stores the selected main image as the post
     * thumbnail. The content-profile fallback keeps older mapped Experiences
     * compatible when their thumbnail relationship has not yet been repaired.
     *
     * @param int                 $experience_id Experience or rental item ID.
     * @param array<string,mixed> $experience    Optional normalized view model.
     * @return int
     */
    function tourbi_reference_home_get_main_image_id(
        $experience_id,
        $experience = array()
    ) {
        $experience_id = absint( $experience_id );
        $attachment_id = $experience_id
            ? absint( get_post_thumbnail_id( $experience_id ) )
            : 0;

        if (
            ! $attachment_id &&
            $experience_id &&
            function_exists( 'tourbi_core_get_experience_content_profile' )
        ) {
            $content_profile = (array)
                tourbi_core_get_experience_content_profile(
                    $experience_id
                );

            $attachment_id = absint(
                $content_profile['main_image_id'] ?? 0
            );
        }

        if ( ! $attachment_id ) {
            $gallery = (array) ( $experience['gallery'] ?? array() );
            $first   = (array) ( $gallery[0] ?? array() );

            $attachment_id = absint(
                $first['id'] ??
                $first['attachment_id'] ??
                0
            );
        }

        return $attachment_id;
    }
}

if ( ! function_exists( 'tourbi_reference_home_get_main_image_url' ) ) {
    /**
     * Return the original main-image URL with compatibility fallbacks.
     *
     * @param int                 $experience_id Experience or rental item ID.
     * @param array<string,mixed> $experience    Optional normalized view model.
     * @param string              $context       hero or card.
     * @return string
     */
    function tourbi_reference_home_get_main_image_url(
        $experience_id,
        $experience = array(),
        $context = 'card'
    ) {
        $attachment_id =
            tourbi_reference_home_get_main_image_id(
                $experience_id,
                $experience
            );

        if ( $attachment_id ) {
            $url = wp_get_attachment_image_url(
                $attachment_id,
                'full'
            );

            if ( $url ) {
                return $url;
            }
        }

        $gallery = (array) ( $experience['gallery'] ?? array() );
        $first   = (array) ( $gallery[0] ?? array() );
        $keys    = array( 'full', 'large', 'url', 'src' );

        foreach ( $keys as $key ) {
            if ( ! empty( $first[ $key ] ) ) {
                return esc_url_raw( $first[ $key ] );
            }
        }

        if ( ! empty( $experience['card_image'] ) ) {
            return esc_url_raw( $experience['card_image'] );
        }

        if (
            'card' === $context &&
            function_exists( 'wc_placeholder_img_src' )
        ) {
            return esc_url_raw( wc_placeholder_img_src( 'large' ) );
        }

        return tourbi_reference_home_asset_url(
            'hero-reference.jpg'
        );
    }
}

if ( ! function_exists( 'tourbi_reference_home_get_main_image_markup' ) ) {
    /**
     * Build responsive image markup from the product's original main image.
     *
     * @param int                 $experience_id Experience or rental item ID.
     * @param array<string,mixed> $experience    Optional normalized view model.
     * @param string              $context       hero or card.
     * @param string              $class_name    Image CSS class.
     * @return string
     */
    function tourbi_reference_home_get_main_image_markup(
        $experience_id,
        $experience = array(),
        $context = 'card',
        $class_name = ''
    ) {
        $experience_id = absint( $experience_id );
        $attachment_id =
            tourbi_reference_home_get_main_image_id(
                $experience_id,
                $experience
            );

        $title = sanitize_text_field(
            $experience['short_title'] ??
            $experience['title'] ??
            ( $experience_id ? get_the_title( $experience_id ) : '' )
        );

        $attributes = array(
            'class'    => sanitize_html_class( $class_name ),
            'alt'      => $title,
            'decoding' => 'async',
            'sizes'    => 'hero' === $context
                ? '(max-width: 900px) 100vw, 1300px'
                : '(max-width: 720px) 100vw, (max-width: 1100px) 50vw, 25vw',
        );

        if ( 'hero' === $context ) {
            $attributes['loading']       = 'eager';
            $attributes['fetchpriority'] = 'high';
        } else {
            $attributes['loading'] = 'lazy';
        }

        if ( $attachment_id ) {
            $markup = wp_get_attachment_image(
                $attachment_id,
                'full',
                false,
                $attributes
            );

            if ( $markup ) {
                return $markup;
            }
        }

        $url = tourbi_reference_home_get_main_image_url(
            $experience_id,
            $experience,
            $context
        );

        return sprintf(
            '<img class="%1$s" src="%2$s" alt="%3$s" loading="%4$s" decoding="async">',
            esc_attr( $class_name ),
            esc_url( $url ),
            esc_attr( $title ),
            'hero' === $context ? 'eager' : 'lazy'
        );
    }
}

if ( ! function_exists( 'tourbi_reference_home_hero_title' ) ) {
    /**
     * Create the compact uppercase hero title used by the reference design.
     *
     * @param array<string,mixed> $experience Featured Experience data.
     * @return string
     */
    function tourbi_reference_home_hero_title( $experience ) {
        $title = sanitize_text_field(
            $experience['short_title'] ??
            $experience['title'] ??
            __( 'Brunch and Ride', 'torby' )
        );

        if ( false !== strpos( $title, ':' ) ) {
            $title = trim(
                (string) strtok( $title, ':' )
            );
        }

        $title = str_ireplace( '&', 'and', $title );

        return strtoupper( $title );
    }
}
