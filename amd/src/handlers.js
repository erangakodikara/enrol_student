// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

define([], function() {
    /**
     * Get parameter value by name from the URI.
     *
     * @param name
     * @param url
     * @returns {string|null}
     */
    var getParameterByName = function(name, url) {
        name = name.replace(/[\[\]]/g, '\\$&');
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
            results = regex.exec(url);

        if (!results) {
            return null;
        }

        if (!results[2]) {
            return '';
        }

        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    };

    /**
     * Check and return true if value is empty.
     *
     * @param value
     * @returns {boolean|boolean}
     */
    var checkEmpty = function(value) {
        return (!value || /^\s*$/.test(value));
    };

    /**
     * Check value is undefined.
     *
     * @param value
     * @returns {boolean}
     */
    var isUndefined = function(value) {
        return typeof value === 'undefined';
    };

    return {
        getParameterByName: getParameterByName,
        checkEmpty: checkEmpty,
        isUndefined: isUndefined
    };
});
