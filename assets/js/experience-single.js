/**
 * Custom Single Experience interactions.
 *
 * WpRently remains responsible for calendar, quantity, availability, cart,
 * and checkout behavior.
 */

( function () {
    'use strict';

    function initializeBookingAnchors() {
        document
            .querySelectorAll( '[data-tourbi-booking-anchor]' )
            .forEach( function ( anchor ) {
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

                        window.setTimeout(
                            function () {
                                const focusable = target.querySelector(
                                    'input:not([type="hidden"]), select, button, a[href]'
                                );

                                if ( focusable ) {
                                    focusable.focus( {
                                        preventScroll: true,
                                    } );
                                }
                            },
                            420
                        );
                    }
                );
            } );
    }

    function initializeMobileBookingBar() {
        const bar = document.querySelector(
            '[data-tourbi-mobile-booking-bar]'
        );

        const panel = document.querySelector(
            '#tourbi-booking-panel'
        );

        if ( ! bar || ! panel || !( 'IntersectionObserver' in window ) ) {
            return;
        }

        const observer = new IntersectionObserver(
            function ( entries ) {
                entries.forEach( function ( entry ) {
                    bar.classList.toggle(
                        'is-hidden',
                        entry.isIntersecting
                    );
                } );
            },
            {
                threshold: 0.16,
            }
        );

        observer.observe( panel );
    }

    function initializeGallery() {
        const lightbox = document.querySelector(
            '[data-tourbi-gallery-lightbox]'
        );

        if ( ! lightbox ) {
            return;
        }

        const dataElement = lightbox.querySelector(
            '[data-tourbi-gallery-data]'
        );

        let images = [];

        try {
            images = JSON.parse(
                dataElement ? dataElement.textContent : '[]'
            );
        } catch ( error ) {
            images = [];
        }

        if ( ! images.length ) {
            return;
        }

        const imageElement = lightbox.querySelector(
            '[data-tourbi-gallery-image]'
        );
        const captionElement = lightbox.querySelector(
            '[data-tourbi-gallery-caption]'
        );
        const closeButton = lightbox.querySelector(
            '[data-tourbi-gallery-close]'
        );
        const previousButton = lightbox.querySelector(
            '[data-tourbi-gallery-previous]'
        );
        const nextButton = lightbox.querySelector(
            '[data-tourbi-gallery-next]'
        );

        let currentIndex = 0;
        let lastFocused = null;

        function renderImage() {
            const image = images[ currentIndex ];

            imageElement.src = image.src || '';
            imageElement.alt = image.alt || '';
            captionElement.textContent = image.caption || '';
        }

        function openGallery( index ) {
            currentIndex = Math.max(
                0,
                Math.min(
                    images.length - 1,
                    Number.parseInt( index, 10 ) || 0
                )
            );

            lastFocused = document.activeElement;
            renderImage();
            lightbox.hidden = false;
            document.body.classList.add( 'tourbi-gallery-open' );
            closeButton.focus();
        }

        function closeGallery() {
            lightbox.hidden = true;
            document.body.classList.remove( 'tourbi-gallery-open' );

            if ( lastFocused && typeof lastFocused.focus === 'function' ) {
                lastFocused.focus();
            }
        }

        function showPrevious() {
            currentIndex =
                ( currentIndex - 1 + images.length ) % images.length;
            renderImage();
        }

        function showNext() {
            currentIndex = ( currentIndex + 1 ) % images.length;
            renderImage();
        }

        document
            .querySelectorAll( '[data-tourbi-gallery-open]' )
            .forEach( function ( trigger ) {
                trigger.addEventListener(
                    'click',
                    function () {
                        openGallery(
                            trigger.getAttribute(
                                'data-tourbi-gallery-open'
                            )
                        );
                    }
                );
            } );

        closeButton.addEventListener( 'click', closeGallery );
        previousButton.addEventListener( 'click', showPrevious );
        nextButton.addEventListener( 'click', showNext );

        lightbox.addEventListener(
            'click',
            function ( event ) {
                if ( event.target === lightbox ) {
                    closeGallery();
                }
            }
        );

        document.addEventListener(
            'keydown',
            function ( event ) {
                if ( lightbox.hidden ) {
                    return;
                }

                if ( event.key === 'Escape' ) {
                    closeGallery();
                }

                if ( event.key === 'ArrowLeft' ) {
                    showPrevious();
                }

                if ( event.key === 'ArrowRight' ) {
                    showNext();
                }
            }
        );
    }

    document.addEventListener(
        'DOMContentLoaded',
        function () {
            initializeBookingAnchors();
            initializeMobileBookingBar();
            initializeGallery();
        }
    );
}() );
