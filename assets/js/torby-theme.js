/**
 * Torby child-theme front-end scripts.
 *
 * Booking, inventory, payment, and vendor functionality must not be placed in
 * this file. Those features belong in the Tourbi Core plugin.
 */

( function () {
    'use strict';

    document.documentElement.classList.add( 'torby-js' );

    /**
     * Configure the responsive WooCommerce My Account navigation.
     *
     * @return {void}
     */
    function setupAccountNavigation() {
        const body = document.body;
        const toggle = document.querySelector( '.tourbi-account-menu-toggle' );
        const navigation = document.querySelector( '.woocommerce-MyAccount-navigation' );

        if ( ! toggle || ! navigation ) {
            return;
        }

        navigation.id = 'tourbi-account-navigation';

        toggle.addEventListener( 'click', function () {
            const isOpen = body.classList.toggle( 'tourbi-account-menu-open' );

            toggle.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
        } );

        navigation.addEventListener( 'click', function ( event ) {
            if ( ! event.target.closest( 'a' ) ) {
                return;
            }

            body.classList.remove( 'tourbi-account-menu-open' );
            toggle.setAttribute( 'aria-expanded', 'false' );
        } );

        window.addEventListener( 'resize', function () {
            if ( window.innerWidth > 900 ) {
                body.classList.remove( 'tourbi-account-menu-open' );
                toggle.setAttribute( 'aria-expanded', 'false' );
            }
        } );
    }

    document.addEventListener( 'DOMContentLoaded', function () {
        document.body.classList.add( 'torby-dom-ready' );
        setupAccountNavigation();
    } );
}() );
