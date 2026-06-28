/**
 * Normal Rental image gallery and mobile booking anchor.
 */

( function () {
    'use strict';

    const gallery = document.querySelector(
        '[data-tourbi-rental-gallery]'
    );

    if ( gallery ) {
        const mainImage = gallery.querySelector(
            '[data-tourbi-rental-main-image]'
        );

        gallery.addEventListener(
            'click',
            function ( event ) {
                const button = event.target.closest(
                    '[data-tourbi-rental-thumb]'
                );

                if ( ! button || ! mainImage ) {
                    return;
                }

                const image = button.getAttribute(
                    'data-image'
                );
                const alt = button.getAttribute(
                    'data-alt'
                ) || '';

                if ( ! image ) {
                    return;
                }

                gallery.querySelectorAll(
                    '[data-tourbi-rental-thumb]'
                ).forEach( function ( thumb ) {
                    thumb.classList.remove(
                        'is-active'
                    );
                } );

                button.classList.add( 'is-active' );
                mainImage.classList.add( 'is-changing' );

                window.setTimeout(
                    function () {
                        mainImage.src = image;
                        mainImage.alt = alt;
                        mainImage.classList.remove(
                            'is-changing'
                        );
                    },
                    120
                );
            }
        );
    }

    document.querySelectorAll(
        '[data-tourbi-rental-booking-anchor]'
    ).forEach( function ( anchor ) {
        anchor.addEventListener(
            'click',
            function ( event ) {
                const target = document.querySelector(
                    anchor.getAttribute( 'href' )
                );

                if ( ! target ) {
                    return;
                }

                event.preventDefault();
                target.scrollIntoView( {
                    behavior: window.matchMedia(
                        '(prefers-reduced-motion: reduce)'
                    ).matches
                        ? 'auto'
                        : 'smooth',
                    block: 'start',
                } );
            }
        );
    } );
}() );
