/**
 * Experience Marketplace mobile filter drawer.
 *
 * Search and filtering remain server-side and work without JavaScript.
 */

( function () {
    'use strict';

    function initializeFilterDrawer() {
        const panel = document.querySelector(
            '[data-tourbi-filter-panel]'
        );
        const backdrop = document.querySelector(
            '[data-tourbi-filter-backdrop]'
        );
        const openButton = document.querySelector(
            '[data-tourbi-filter-open]'
        );
        const closeButton = document.querySelector(
            '[data-tourbi-filter-close]'
        );

        if ( ! panel || ! backdrop || ! openButton || ! closeButton ) {
            return;
        }

        let lastFocused = null;

        function openFilters() {
            lastFocused = document.activeElement;
            panel.classList.add( 'is-open' );
            backdrop.hidden = false;
            document.body.classList.add( 'tourbi-filters-open' );
            openButton.setAttribute( 'aria-expanded', 'true' );

            const firstField = panel.querySelector(
                'input, select, button'
            );

            if ( firstField ) {
                window.setTimeout(
                    function () {
                        firstField.focus();
                    },
                    230
                );
            }
        }

        function closeFilters() {
            panel.classList.remove( 'is-open' );
            backdrop.hidden = true;
            document.body.classList.remove( 'tourbi-filters-open' );
            openButton.setAttribute( 'aria-expanded', 'false' );

            if (
                lastFocused &&
                typeof lastFocused.focus === 'function'
            ) {
                lastFocused.focus();
            }
        }

        openButton.addEventListener( 'click', openFilters );
        closeButton.addEventListener( 'click', closeFilters );
        backdrop.addEventListener( 'click', closeFilters );

        document.addEventListener(
            'keydown',
            function ( event ) {
                if (
                    'Escape' === event.key &&
                    panel.classList.contains( 'is-open' )
                ) {
                    closeFilters();
                }
            }
        );

        window.addEventListener(
            'resize',
            function () {
                if (
                    window.matchMedia( '(min-width: 761px)' )
                        .matches &&
                    panel.classList.contains( 'is-open' )
                ) {
                    closeFilters();
                }
            }
        );
    }

    document.addEventListener(
        'DOMContentLoaded',
        initializeFilterDrawer
    );
}() );
