<?php
/**
 * Template Name: Tourbi Reference Home
 * Template Post Type: page
 *
 * Manually assignable version of the reference-matched Tourbi Home page.
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
