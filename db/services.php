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
 *
 * @package    enrol_student
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
    'block_enrol_student_enrol_student_data' => array(
        'classname' => 'block_enrol_student\external\enrol_student_external',
        'methodname' => 'enrol_student_data',
        'classpath' => 'block_enrol_student\external',
        'description' => 'Return enrolled students',
        'type' => 'read',
        'capabilities' => 'block/enrol_student:view',
        'ajax' => true,
    ),
);

