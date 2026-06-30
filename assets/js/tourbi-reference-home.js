(function () {
    'use strict';

    var search = document.getElementById('tourbi-home-search-input');

    if (!search) {
        return;
    }

    search.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            search.value = '';
            search.blur();
        }
    });
}());
