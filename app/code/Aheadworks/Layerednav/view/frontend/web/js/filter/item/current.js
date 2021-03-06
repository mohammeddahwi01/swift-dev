define([], function () {
    'use strict';

    var currentFilterItem = null;

    return {
        /**
         * Set current filter item
         *
         * @param {Object} filterItem
         */
        set: function (filterItem) {
            currentFilterItem = filterItem;
        },

        /**
         * Get current filter item
         *
         * @returns {Object}
         */
        get: function () {
            return currentFilterItem;
        },

        /**
         * Reset current filter item
         */
        reset: function () {
            currentFilterItem = null;
        }
    };
});
