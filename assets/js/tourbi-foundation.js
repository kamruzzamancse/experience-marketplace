/**
 * Tourbi custom-template foundation interactions.
 *
 * Booking, pricing, inventory, checkout, approval, and reservation logic
 * remain in the Tourbi Core plugin.
 */

( function () {
    'use strict';

    document.documentElement.classList.add(
        'tourbi-foundation-js'
    );

    /**
     * Toggle a target using data-tourbi-toggle="#selector".
     *
     * @param {HTMLElement} trigger Toggle control.
     * @return {void}
     */
    function bindToggle( trigger ) {
        const selector = trigger.getAttribute(
            'data-tourbi-toggle'
        );

        if ( ! selector ) {
            return;
        }

        const target = document.querySelector(
            selector
        );

        if ( ! target ) {
            return;
        }

        trigger.addEventListener(
            'click',
            function () {
                const isOpen = target.classList.toggle(
                    'is-open'
                );

                trigger.setAttribute(
                    'aria-expanded',
                    isOpen ? 'true' : 'false'
                );
            }
        );
    }

    document.addEventListener(
        'DOMContentLoaded',
        function () {
            document.body.classList.add(
                'tourbi-foundation-ready'
            );

            document
                .querySelectorAll(
                    '[data-tourbi-toggle]'
                )
                .forEach( bindToggle );
        }
    );
}() );
