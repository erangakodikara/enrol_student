<?php
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

/**
 * PLUGIN external file
 *
 * @package    enrol_student
 * @category   external
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . "/externallib.php");

use block_enrol_student\output\email_list;

class enrol_student_external extends external_api
{

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function enrol_student_data_parameters()
    {

        // The external_function_parameters constructor expects an array of external_description.
        return new external_function_parameters(
        // a external_description can be: external_value, external_single_structure or external_multiple structure
            array(
                'courseid' => new external_value(
                    PARAM_INT,
                    'Page number',
                    VALUE_REQUIRED
                ),
                'page' => new external_value(
                    PARAM_INT,
                    'Page number',
                    VALUE_REQUIRED
                )
            )
        );
    }

    /**
     * The function itself
     * @return string welcome message
     */
    public static function enrol_student_data($courseid, $page)
    {
        global $DB, $OUTPUT;
        //Parameters validation
        $params = self::validate_parameters(self::enrol_student_data_parameters(),
            array('courseid' => $courseid, 'page' => $page)
        );

        $context = context_course::instance($courseid);
        $capability = 'block/enrol_student:view';
        $emaillist = [];

        if (has_capability($capability, $context)) {
            $courseobject = $DB->get_record('course', array('id' => $courseid));
            $emaillist = new email_list($courseobject);
        }

        return [
            'pagination' => $OUTPUT->paging_bar(
                array_key_exists("users", $emaillist) ? count($emaillist["users"]) : 0,
                $params['page'],
                $params['perpage'],
                new moodle_url('index.php')
            ),
            'data' => $emaillist
        ];
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function enrol_student_data_returns()
    {
        return new external_single_structure(array(
            'pagination' => new external_value(PARAM_RAW, 'First name of the course'),
            'data' => new external_multiple_structure(
                new external_single_structure(array(
                    'firstname' => new external_value(PARAM_TEXT, 'Students first name'),
                    'lastname' => new external_value(PARAM_TEXT, 'Students last name'),
                    'email' => new external_value(PARAM_EMAIL, 'Students email'),
                ))
            )
        ));
    }
}