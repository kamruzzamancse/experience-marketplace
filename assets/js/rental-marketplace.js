/**
 * Tourbi Bike Rentals marketplace interactions.
 */

( function () {
    'use strict';

    const form = document.querySelector(
        '[data-tourbi-rental-filters]'
    );

    if ( ! form ) {
        return;
    }

    const selects = form.querySelectorAll(
        'select'
    );

    selects.forEach(
        function ( select ) {
            select.addEventListener(
                'change',
                function () {
                    form.submit();
                }
            );
        }
    );

    form.addEventListener(
        'submit',
        function () {
            form
                .querySelectorAll(
                    'input, select'
                )
                .forEach(
                    function ( field ) {
                        if (
                            '' === field.value ||
                            (
                                'rental_sort' === field.name &&
                                'recommended' === field.value
                            )
                        ) {
                            field.disabled = true;
                        }
                    }
                );
        }
    );
}() );
