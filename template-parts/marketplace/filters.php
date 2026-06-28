<?php
/**
 * Marketplace search and filters.
 *
 * @package Torby
 */

$state = $args['state'] ?? array();
$categories = $args['categories'] ?? array();
$locations = $args['locations'] ?? array();
$sort_options = tourbi_theme_get_marketplace_sort_options();
?>
<div
    id="tourbi-marketplace-filters"
    class="tourbi-marketplace-filters"
    data-tourbi-filter-panel
>
    <div class="tourbi-marketplace-filters__mobile-header">
        <strong>
            <?php esc_html_e( 'Search Experiences', 'torby' ); ?>
        </strong>

        <button
            type="button"
            data-tourbi-filter-close
            aria-label="<?php esc_attr_e( 'Close filters', 'torby' ); ?>"
        >×</button>
    </div>

    <form
        class="tourbi-marketplace-filter-form"
        method="get"
        action="<?php echo esc_url( tourbi_theme_get_experience_archive_url() ); ?>"
    >
        <label class="tourbi-marketplace-filter tourbi-marketplace-filter--search">
            <span><?php esc_html_e( 'Search', 'torby' ); ?></span>

            <div>
                <b aria-hidden="true">⌕</b>

                <input
                    type="search"
                    name="experience_search"
                    value="<?php echo esc_attr( $state['search'] ?? '' ); ?>"
                    placeholder="<?php esc_attr_e( 'Tours, themes, or landmarks', 'torby' ); ?>"
                >
            </div>
        </label>

        <label class="tourbi-marketplace-filter">
            <span><?php esc_html_e( 'Category', 'torby' ); ?></span>

            <select name="experience_category">
                <option value="">
                    <?php esc_html_e( 'All Categories', 'torby' ); ?>
                </option>

                <?php foreach ( $categories as $category ) : ?>
                    <option
                        value="<?php echo esc_attr( $category->slug ); ?>"
                        <?php selected(
                            $state['category'] ?? '',
                            $category->slug
                        ); ?>
                    >
                        <?php echo esc_html( $category->name ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label class="tourbi-marketplace-filter">
            <span><?php esc_html_e( 'Location', 'torby' ); ?></span>

            <select name="experience_location">
                <option value="">
                    <?php esc_html_e( 'All Locations', 'torby' ); ?>
                </option>

                <?php foreach ( $locations as $location ) : ?>
                    <option
                        value="<?php echo esc_attr( $location ); ?>"
                        <?php selected(
                            $state['location'] ?? '',
                            $location
                        ); ?>
                    >
                        <?php echo esc_html( $location ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label class="tourbi-marketplace-filter">
            <span><?php esc_html_e( 'Sort', 'torby' ); ?></span>

            <select name="experience_sort">
                <?php foreach ( $sort_options as $value => $label ) : ?>
                    <option
                        value="<?php echo esc_attr( $value ); ?>"
                        <?php selected(
                            $state['sort'] ?? 'recommended',
                            $value
                        ); ?>
                    >
                        <?php echo esc_html( $label ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <div class="tourbi-marketplace-filter-form__actions">
            <button
                type="submit"
                class="tourbi-button tourbi-button--primary"
            >
                <?php esc_html_e( 'Find Experiences', 'torby' ); ?>
            </button>

            <?php if ( ! empty( $state['has_filters'] ) ) : ?>
                <a
                    class="tourbi-marketplace-filter-reset"
                    href="<?php echo esc_url( tourbi_theme_get_experience_archive_url() ); ?>"
                >
                    <?php esc_html_e( 'Clear all', 'torby' ); ?>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div
    class="tourbi-marketplace-filter-backdrop"
    data-tourbi-filter-backdrop
    hidden
></div>
