<?php
/**
 * Rental marketplace filters.
 *
 * @package Torby
 */

$state = $args['state'] ?? array();
$bike_types = $args['bike_types'] ?? array();
$total = absint( $args['total'] ?? 0 );
?>
<section class="tourbi-rental-marketplace-toolbar">
    <div class="tourbi-rental-marketplace-toolbar__heading">
        <div>
            <span><?php esc_html_e( 'Available Rentals', 'torby' ); ?></span>
            <strong>
                <?php
                echo esc_html(
                    sprintf(
                        _n(
                            '%d rental found',
                            '%d rentals found',
                            $total,
                            'torby'
                        ),
                        $total
                    )
                );
                ?>
            </strong>
        </div>

        <?php if ( ! empty( $state['has_filters'] ) ) : ?>
            <a href="<?php echo esc_url( tourbi_theme_get_rental_marketplace_url() ); ?>">
                <?php esc_html_e( 'Clear filters', 'torby' ); ?>
            </a>
        <?php endif; ?>
    </div>

    <form
        class="tourbi-rental-marketplace-filters"
        action="<?php echo esc_url( tourbi_theme_get_rental_marketplace_url() ); ?>"
        method="get"
        data-tourbi-rental-filters
    >
        <label>
            <span><?php esc_html_e( 'Search', 'torby' ); ?></span>
            <input
                type="search"
                name="rental_search"
                value="<?php echo esc_attr( $state['search'] ?? '' ); ?>"
                placeholder="<?php esc_attr_e( 'Search bike rentals', 'torby' ); ?>"
            >
        </label>

        <label>
            <span><?php esc_html_e( 'Bike Type', 'torby' ); ?></span>
            <select name="rental_bike_type">
                <option value="">
                    <?php esc_html_e( 'All bike types', 'torby' ); ?>
                </option>

                <?php foreach ( $bike_types as $key => $label ) : ?>
                    <option
                        value="<?php echo esc_attr( $key ); ?>"
                        <?php selected( $state['bike_type'] ?? '', $key ); ?>
                    >
                        <?php echo esc_html( $label ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            <span><?php esc_html_e( 'Sort', 'torby' ); ?></span>
            <select name="rental_sort">
                <?php foreach ( tourbi_theme_get_rental_marketplace_sort_options() as $key => $label ) : ?>
                    <option
                        value="<?php echo esc_attr( $key ); ?>"
                        <?php selected( $state['sort'] ?? 'recommended', $key ); ?>
                    >
                        <?php echo esc_html( $label ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <button type="submit">
            <?php esc_html_e( 'Show Rentals', 'torby' ); ?>
            <span aria-hidden="true">→</span>
        </button>
    </form>
</section>
