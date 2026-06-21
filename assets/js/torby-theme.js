/**
 * Torby child-theme front-end scripts.
 *
 * Booking, inventory, payment, and vendor functionality must not be placed in
 * this file. Those features belong in the Torby Core plugin.
 */

( function () {
    'use strict';

    document.documentElement.classList.add( 'torby-js' );

    document.addEventListener( 'DOMContentLoaded', function () {
        document.body.classList.add( 'torby-dom-ready' );
    } );
}() );
