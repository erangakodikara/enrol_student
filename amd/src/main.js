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

import ajax from  'core/ajax';
import Templates from  'core/templates';
import $ from 'jquery';
import loadingicon from "core/loadingicon";
import ModalFactory  from 'core/modal_factory';
import handlers from 'block_enrol_student/handlers';
import Notifications from 'core/notification';
import * as Str from 'core/str';

export let _perPageCount = 25;
export let _page = 0;
export let _lang = [];
export let _courseId = null;

/**
 * Initialize the main js and register events
 * @method
 * @param courseid
 * @param perpage
 */
export const init = (courseid,perpage) => {
    _courseId = courseid;
    _perPageCount = perpage;

    populateStrings();
    getEnrolmentBlockData(0,addLoader());
    $(".enrol-student-container").on('click', '#user_id', (e) => loadUserModal(e));
    $('.student-block-pagination').on('click', 'ul.pagination li>a', (e) => loadUserPagination(e));
};

/**
 * Populate string for the messages
 * @method
 */
export const populateStrings = () => {
    var strings = [
        {
            key: 'error_title',
            component: 'block_enrol_student'
        },
        {
            key: 'block_enrol_student_load_error',
            component: 'block_enrol_student'
        },
    ];

    // eslint-disable-next-line promise/catch-or-return
    Str.get_strings(strings).then(function(results) {
        _lang = results;
    });
};

/**
 * Get Enrolment Block Data
 * @param page
 */
export const getEnrolmentBlockData = function (page, addLoader) {
    ajax.call([{
        methodname: 'block_enrol_student_enrol_student_data',
        args: {
            courseid: _courseId,
            perpage: _perPageCount,
            page: page
        },
        done: function (data) {
            if (data.data.length > 0) {
                Templates.renderForPromise(
                    'block_enrol_student/table/users',
                    {
                        users: data.data
                    }
                ).then(({html, js}) => {
                    Templates.replaceNodeContents('.enrol-student-container>tbody', html, js);
                    if (data.pagination !== '') {
                        $('.student-block-pagination').html(data.pagination);
                    } else {
                        $('student-block-pagination').html('');
                    }
                    removeLoader(addLoader);
                }).catch(
                    () => handleError(addLoader, _lang[1])
                );
            } else {
                removeLoader(addLoader);
            }

        },
        fail: function () {
            handleError(addLoader, _lang[1]);
        }
    }]);
};

/**
 * Load User Modal
 * @param e
 */
export const loadUserModal = (e) => {
    e.preventDefault();
    let _userId = $(e.target).is('a') ? $("#user_id").data('id') : $(e.target).closest('a').data('id');
    let _firstName = $(e.target).is('a') ? $("#user_id").data('firstname') : $(e.target).closest('a').data('firstname');
    let _lastName = $(e.target).is('a') ? $("#user_id").data('lastname') : $(e.target).closest('a').data('lastname');
    let _fullName = $(e.target).is('a') ? $("#user_id").data('fullname') : $(e.target).closest('a').data('fullname');

    // Create Modal Factory.
    ModalFactory.create({
        title: 'Student Information',
        body: "       <table>\n" +
            "            <tr>\n" +
            "                <th>ID</th>\n" +
            "                <th>FIRST NAME</th>\n" +
            "                <th>LAST NAME</th>\n" +
            "                <th>FULL NAME</th>\n" +
            "            </tr>\n" +
            "            <tbody>\n" +
            "\n" +
            "            <tr>\n" +
            "                <td>" +
            _userId +
            "</td>\n" +
            "                <td>" +
            _firstName +
            "</td>\n" +
            "                <td>" +
            _lastName +
            "</td>\n" +
            "                <td>" +
            _fullName +
            "</td>\n" +
            "\n" +
            "            </tr>\n" +
            "\n" +
            "            </tbody>\n" +
            "        </table>",
    })
        .done(function (modal) {
            modal.show();
        });

};

/**
 *  Load User Pagination
 *  @method
 *  @param e
 */
export const loadUserPagination = (e) => {
    e.preventDefault();
    let _hrefTarget = $(e.target).is('a') ? $(e.target).attr('href') : $(e.target).closest('a').attr('href');
    _page = handlers.getParameterByName('page', _hrefTarget);
    getEnrolmentBlockData(_page, addLoader());
};

/**
 * Adds a loader to the screen whenever called
 *
 * @method
 * @returns {Window.jQuery|Promise|*}
 */
export const addLoader = () => {
    $('.enrol-student-container').addClass('loading');
    return loadingicon.addIconToContainerWithPromise($('.enrol-student-container'));
};

/**
 * Removes the previously added loader from the screen
 *
 * @method
 * @param loaderAdded {object} added loader to the dom
 */
export const removeLoader = (loaderAdded) => {
    $('.loading-icon').remove();
    loaderAdded.resolve();
    $('.enrol-student-container').removeClass('loading');
};

/**
 * Display exception message in a notification
 *
 * @method
 * @param loaderAdded {object} added loaded to the dom
 * @param body {string} the message string for the popup
 */
export const handleError =  (loaderAdded, body) => {
    removeLoader(loaderAdded);
    Notifications.alert(_lang[0], body);
};