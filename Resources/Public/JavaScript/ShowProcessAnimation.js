define([
    'jquery'
], function ($) {
    'use strict';

    let ShowProcessAnimation = {};

    ShowProcessAnimation.init = function() {
        document.querySelectorAll('[data-shows-process-animation-after-click="true"]').forEach(function (activator) {
            activator.addEventListener('click', function () {
                document.getElementById('db-rector-processing').style.display = 'flex';
            }, false);
        });
    };

    ShowProcessAnimation.init()

    // expose to global
    TYPO3.ShowProcessAnimation = ShowProcessAnimation;

    return ShowProcessAnimation;
});