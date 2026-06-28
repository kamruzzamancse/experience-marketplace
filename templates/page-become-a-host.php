<?php
/**
 * Template Name: Tourbi Become a Host
 * Template Post Type: page
 *
 * @package Torby
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

$host_page = function_exists(
    'tourbi_theme_get_become_host_view_model'
)
    ? tourbi_theme_get_become_host_view_model()
    : array();
?>
<main
    id="primary"
    class="tourbi-app tourbi-become-host"
>
    <?php
    get_template_part(
        'template-parts/host/hero',
        null,
        array(
            'host_page' => $host_page,
        )
    );

    get_template_part(
        'template-parts/host/benefits',
        null,
        array(
            'host_page' => $host_page,
        )
    );

    get_template_part(
        'template-parts/host/how-it-works',
        null,
        array(
            'host_page' => $host_page,
        )
    );

    get_template_part(
        'template-parts/host/ideas',
        null,
        array(
            'host_page' => $host_page,
        )
    );

    get_template_part(
        'template-parts/host/requirements',
        null,
        array(
            'host_page' => $host_page,
        )
    );

    get_template_part(
        'template-parts/host/faq',
        null,
        array(
            'host_page' => $host_page,
        )
    );

    get_template_part(
        'template-parts/host/final-cta',
        null,
        array(
            'host_page' => $host_page,
        )
    );
    ?>
</main>
<?php
get_footer();
