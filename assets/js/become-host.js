/**
 * Become a Host FAQ interactions.
 */

( function () {
    'use strict';

    const list = document.querySelector(
        '[data-tourbi-host-faq]'
    );

    if ( ! list ) {
        return;
    }

    list.addEventListener(
        'click',
        function ( event ) {
            const button = event.target.closest(
                '[data-tourbi-host-faq-toggle]'
            );

            if ( ! button ) {
                return;
            }

            const item = button.closest(
                '.tourbi-host-faq-item'
            );
            const answerId = button.getAttribute(
                'aria-controls'
            );
            const answer = document.getElementById(
                answerId
            );
            const willOpen =
                'true' !==
                button.getAttribute(
                    'aria-expanded'
                );

            list
                .querySelectorAll(
                    '.tourbi-host-faq-item'
                )
                .forEach( function ( otherItem ) {
                    const otherButton =
                        otherItem.querySelector(
                            '[data-tourbi-host-faq-toggle]'
                        );
                    const otherAnswer = document.getElementById(
                        otherButton.getAttribute(
                            'aria-controls'
                        )
                    );

                    otherItem.classList.remove(
                        'is-open'
                    );
                    otherButton.setAttribute(
                        'aria-expanded',
                        'false'
                    );

                    if ( otherAnswer ) {
                        otherAnswer.hidden = true;
                    }
                } );

            if ( willOpen ) {
                item.classList.add( 'is-open' );
                button.setAttribute(
                    'aria-expanded',
                    'true'
                );

                if ( answer ) {
                    answer.hidden = false;
                }
            }
        }
    );
}() );
