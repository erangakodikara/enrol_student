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

namespace block_enrol_student\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;

use course_enrolment_manager;
use external_multiple_structure;
use external_function_parameters;
use external_single_structure;
use external_value;
use context_course;
use moodle_url;

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->dirroot/enrol/locallib.php");
require_once($CFG->dirroot . '/enrol/externallib.php');
require(__DIR__ . '/../../lib.php');

/**
 * @package    enrol_student
 * Class enrol_student_external
 */
class enrol_student_external extends \external_api
{

    /**
     * enrol_student_data_parameters function
     * @return external_function_parameters
     */
    public static function enrol_student_data_parameters() {
        // The external_function_parameters constructor expects an array of external_description.
        return new external_function_parameters(
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
                ),
                'perpage' => new external_value(
                    PARAM_INT,
                    'Students per page',
                    VALUE_REQUIRED
                ),
            )
        );
    }

    /**
     * enrol_student_data function
     * @param $courseid
     * @param $page
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function enrol_student_data($courseid, $page, $perpage) {
        global $OUTPUT, $PAGE;
        // Parameters validation.
        $params = self::validate_parameters(self::enrol_student_data_parameters(),
            [
                'courseid' => $courseid,
                'page' => $page,
                'perpage' => $perpage]
        );

        $context = context_course::instance($courseid);
        $capability = 'block/enrol_student:view';

        // Set course to page.
        $course = get_course($params['courseid']);
        $PAGE->set_course($course);

        // Get students.
        $manager = new course_enrolment_manager($PAGE, $PAGE->course, null, ROLE_STUDENT);
        $studentscount = $manager->get_total_users();

        $users = [];

        if (has_capability($capability, $context)) {
            $students = $manager->get_users('firstname', $params['sortdir'], $params['page'], $params['perpage']);
            foreach ($students as $student) {
                $urlobj = new moodle_url('/user/view.php', ['id' => $student->id], ['course' => $courseid]);
                $users[] = [
                    'userid' => $student->id,
                    'firstname' => $student->firstname,
                    'lastname' => $student->lastname,
                    'email' => $student->email,
                    'fullname' => $student->firstname . ' ' . $student->lastname,
                    'url' => $urlobj->out()
                ];
            }
        }

        return ['pagination' => $params['perpage'] > 0 ? $OUTPUT->paging_bar(
            $studentscount,
            $params['page'],
            $params['perpage'],
            new moodle_url('index.php')
        ) : '',
            'data' => $users
        ];
    }

    /**
     * enrol_student_data_returns function
     * Returns description of method result value
     * @return external_description
     */
    public static function enrol_student_data_returns() {
        return new external_single_structure(array(
            'pagination' => new external_value(PARAM_RAW, 'First name of the student'),
            'data' => new external_multiple_structure(
                new external_single_structure(array(
                    'userid' => new external_value(PARAM_INT, 'Enrolled student ID'),
                    'firstname' => new external_value(PARAM_TEXT, 'Students first name'),
                    'lastname' => new external_value(PARAM_TEXT, 'Students last name'),
                    'url' => new external_value(PARAM_URL, 'Student profile url'),
                    'email' => new external_value(PARAM_EMAIL, 'Students email'),
                    'fullname' => new external_value(PARAM_TEXT, 'Students full name')
                ))
            )
        ));
    }
}
