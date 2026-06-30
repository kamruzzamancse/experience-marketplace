/**
 * Experience Marketplace interactions.
 *
 * Search/filtering remains server-side and fully functional without JS.
 * JavaScript progressively enhances the compact filter selects so option
 * background, text, hover and active colours remain consistent in browsers.
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

    function initializeCustomSelects() {
        const selects = document.querySelectorAll(
            '.tourbi-showcase-search__field select'
        );

        if ( ! selects.length ) {
            return;
        }

        let openField = null;

        function closeSelect( field, returnFocus ) {
            if ( ! field ) {
                return;
            }

            const root = field.querySelector( '.tourbi-custom-select' );
            const trigger = field.querySelector( '.tourbi-custom-select__trigger' );
            const menu = field.querySelector( '.tourbi-custom-select__menu' );

            field.classList.remove( 'is-open' );

            if ( root ) {
                root.classList.remove( 'is-open' );
            }

            if ( trigger ) {
                trigger.setAttribute( 'aria-expanded', 'false' );
            }

            if ( menu ) {
                menu.hidden = true;
            }

            if ( openField === field ) {
                openField = null;
            }

            if ( returnFocus && trigger ) {
                trigger.focus();
            }
        }

        function closeAll( exceptField ) {
            if ( openField && openField !== exceptField ) {
                closeSelect( openField, false );
            }
        }

        selects.forEach( function ( select, selectIndex ) {
            const field = select.closest( '.tourbi-showcase-search__field' );

            if ( ! field || field.classList.contains( 'has-custom-select' ) ) {
                return;
            }

            const custom = document.createElement( 'div' );
            const trigger = document.createElement( 'button' );
            const value = document.createElement( 'span' );
            const chevron = document.createElementNS(
                'http://www.w3.org/2000/svg',
                'svg'
            );
            const chevronPath = document.createElementNS(
                'http://www.w3.org/2000/svg',
                'path'
            );
            const menu = document.createElement( 'div' );
            const menuId = ( select.id || 'tourbi-custom-select-' + selectIndex ) + '-menu';

            select.classList.add( 'tourbi-native-select' );
            select.setAttribute( 'aria-hidden', 'true' );
            select.tabIndex = -1;

            custom.className = 'tourbi-custom-select';
            trigger.className = 'tourbi-custom-select__trigger';
            trigger.type = 'button';
            trigger.setAttribute( 'aria-haspopup', 'listbox' );
            trigger.setAttribute( 'aria-expanded', 'false' );
            trigger.setAttribute( 'aria-controls', menuId );

            value.className = 'tourbi-custom-select__value';

            chevron.classList.add( 'tourbi-custom-select__chevron' );
            chevron.setAttribute( 'viewBox', '0 0 20 20' );
            chevron.setAttribute( 'aria-hidden', 'true' );
            chevron.setAttribute( 'focusable', 'false' );
            chevronPath.setAttribute( 'd', 'm5 7.5 5 5 5-5' );
            chevron.appendChild( chevronPath );

            menu.className = 'tourbi-custom-select__menu';
            menu.id = menuId;
            menu.setAttribute( 'role', 'listbox' );
            menu.setAttribute( 'aria-label', select.getAttribute( 'aria-label' ) || '' );
            menu.hidden = true;

            function getSelectedOption() {
                return select.options[ select.selectedIndex ] || select.options[0];
            }

            function updateSelection() {
                const selected = getSelectedOption();
                const optionButtons = menu.querySelectorAll(
                    '.tourbi-custom-select__option'
                );

                value.textContent = selected ? selected.textContent.trim() : '';

                optionButtons.forEach( function ( button ) {
                    const isSelected = button.dataset.value === select.value;
                    button.setAttribute(
                        'aria-selected',
                        isSelected ? 'true' : 'false'
                    );
                } );
            }

            Array.from( select.options ).forEach( function ( option, optionIndex ) {
                const optionButton = document.createElement( 'button' );

                optionButton.className = 'tourbi-custom-select__option';
                optionButton.type = 'button';
                optionButton.setAttribute( 'role', 'option' );
                optionButton.dataset.value = option.value;
                optionButton.dataset.index = String( optionIndex );
                optionButton.textContent = option.textContent.trim();

                optionButton.addEventListener( 'click', function () {
                    select.selectedIndex = optionIndex;
                    select.dispatchEvent(
                        new Event( 'change', { bubbles: true } )
                    );
                    updateSelection();
                    closeSelect( field, true );
                } );

                optionButton.addEventListener( 'keydown', function ( event ) {
                    const options = Array.from(
                        menu.querySelectorAll( '.tourbi-custom-select__option' )
                    );
                    const currentIndex = options.indexOf( optionButton );
                    let nextIndex = currentIndex;

                    if ( 'ArrowDown' === event.key ) {
                        nextIndex = Math.min( options.length - 1, currentIndex + 1 );
                    } else if ( 'ArrowUp' === event.key ) {
                        nextIndex = Math.max( 0, currentIndex - 1 );
                    } else if ( 'Home' === event.key ) {
                        nextIndex = 0;
                    } else if ( 'End' === event.key ) {
                        nextIndex = options.length - 1;
                    } else if ( 'Escape' === event.key ) {
                        event.preventDefault();
                        closeSelect( field, true );
                        return;
                    } else if ( 'Tab' === event.key ) {
                        closeSelect( field, false );
                        return;
                    } else {
                        return;
                    }

                    event.preventDefault();

                    if ( options[ nextIndex ] ) {
                        options[ nextIndex ].focus();
                    }
                } );

                menu.appendChild( optionButton );
            } );

            function openSelect() {
                closeAll( field );
                field.classList.add( 'is-open' );
                custom.classList.add( 'is-open' );
                trigger.setAttribute( 'aria-expanded', 'true' );
                menu.hidden = false;
                openField = field;

                const selectedButton = menu.querySelector(
                    '.tourbi-custom-select__option[aria-selected="true"]'
                );
                const firstButton = menu.querySelector(
                    '.tourbi-custom-select__option'
                );

                window.requestAnimationFrame( function () {
                    ( selectedButton || firstButton )?.focus();
                } );
            }

            trigger.addEventListener( 'click', function () {
                if ( field.classList.contains( 'is-open' ) ) {
                    closeSelect( field, false );
                } else {
                    openSelect();
                }
            } );

            trigger.addEventListener( 'keydown', function ( event ) {
                if (
                    'ArrowDown' === event.key ||
                    'ArrowUp' === event.key ||
                    'Enter' === event.key ||
                    ' ' === event.key
                ) {
                    event.preventDefault();
                    openSelect();
                }
            } );

            select.addEventListener( 'change', updateSelection );

            trigger.appendChild( value );
            trigger.appendChild( chevron );
            custom.appendChild( trigger );
            custom.appendChild( menu );
            field.appendChild( custom );
            field.classList.add( 'has-custom-select' );

            updateSelection();
        } );

        document.addEventListener( 'pointerdown', function ( event ) {
            if (
                openField &&
                ! openField.contains( event.target )
            ) {
                closeSelect( openField, false );
            }
        } );

        document.addEventListener( 'keydown', function ( event ) {
            if ( 'Escape' === event.key && openField ) {
                closeSelect( openField, true );
            }
        } );
    }

    document.addEventListener(
        'DOMContentLoaded',
        function () {
            initializeFilterDrawer();
            initializeCustomSelects();
        }
    );
}() );
