<?php
/**
 * Automatic template for a WordPress page with the slug "home".
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once get_stylesheet_directory() . '/inc/reference-home-runtime.php';

tourbi_reference_home_prepare();

get_header();

get_template_part(
    'template-parts/home/reference-home-content'
);

get_footer();
