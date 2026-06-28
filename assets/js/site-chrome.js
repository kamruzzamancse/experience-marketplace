/**
 * Tourbi custom site navigation.
 */

( function () {
    'use strict';

    const header = document.querySelector(
        '[data-tourbi-site-header]'
    );
    const toggle = document.querySelector(
        '[data-tourbi-nav-toggle]'
    );
    const navigation = document.querySelector(
        '[data-tourbi-navigation]'
    );
    const backdrop = document.querySelector(
        '[data-tourbi-nav-backdrop]'
    );

    if ( ! header || ! toggle || ! navigation || ! backdrop ) {
        return;
    }

    let lastFocused = null;

    function closeNavigation() {
        navigation.classList.remove( 'is-open' );
        toggle.classList.remove( 'is-open' );
        toggle.setAttribute( 'aria-expanded', 'false' );
        toggle.setAttribute( 'aria-label', 'Open navigation' );
        backdrop.hidden = true;
        document.body.classList.remove(
            'tourbi-site-nav-open'
        );

        if (
            lastFocused &&
            typeof lastFocused.focus === 'function'
        ) {
            lastFocused.focus();
        }
    }

    function openNavigation() {
        lastFocused = document.activeElement;
        navigation.classList.add( 'is-open' );
        toggle.classList.add( 'is-open' );
        toggle.setAttribute( 'aria-expanded', 'true' );
        toggle.setAttribute( 'aria-label', 'Close navigation' );
        backdrop.hidden = false;
        document.body.classList.add(
            'tourbi-site-nav-open'
        );

        const firstLink = navigation.querySelector( 'a' );

        if ( firstLink ) {
            window.setTimeout(
                function () {
                    firstLink.focus();
                },
                220
            );
        }
    }

    toggle.addEventListener(
        'click',
        function () {
            if (
                navigation.classList.contains(
                    'is-open'
                )
            ) {
                closeNavigation();
            } else {
                openNavigation();
            }
        }
    );

    backdrop.addEventListener(
        'click',
        closeNavigation
    );

    navigation.addEventListener(
        'click',
        function ( event ) {
            if ( event.target.closest( 'a' ) ) {
                closeNavigation();
            }
        }
    );

    document.addEventListener(
        'keydown',
        function ( event ) {
            if (
                'Escape' === event.key &&
                navigation.classList.contains(
                    'is-open'
                )
            ) {
                closeNavigation();
            }
        }
    );

    window.addEventListener(
        'resize',
        function () {
            if (
                window.matchMedia(
                    '(min-width: 821px)'
                ).matches
            ) {
                closeNavigation();
            }
        }
    );

    let lastScroll = 0;

    window.addEventListener(
        'scroll',
        function () {
            const current = window.scrollY || 0;

            header.classList.toggle(
                'is-sticky',
                current > 20
            );

            lastScroll = current;
        },
        { passive: true }
    );
}() );
