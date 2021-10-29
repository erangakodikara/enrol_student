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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/enrol/locallib.php');

use block_base;
use block_enrol_student\output\email_list;

/**
 * Form for editing HTML block instances.
 *
 * @package    block_enrol_student
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_enrol_student extends block_base {

    /**
     * function init
     * @throws coding_exception
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_enrol_student');
    }

    /**
     * funtion has_config
     * @return bool
     */
    public function has_config() {
        return true;
    }

    /**
     * fuction applicable_formats
     * @return array|bool[]
     */
    public function applicable_formats() {
        return array('all' => true);
    }

    /**
     * funtion instance_allow_multiple
     * @return bool
     */
    public function instance_allow_multiple() {
        return true;
    }

    /**
     * funtion get_content
     * @return stdClass|stdObject|null
     * @throws coding_exception
     * @throws dml_exception
     */
    public function get_content() {
        $courseid = optional_param('course', 0, PARAM_INT);
        if ($courseid == 0) {
            $courseid = optional_param('id', 0, PARAM_INT);
        }

        $syscontext = context_system::instance();

        if ($courseid > 0) {
            $context = context_course::instance($courseid);
            $capability = 'block/enrol_student:view';

            if (has_capability($capability, $context)) {

                if ($this->content !== null) {
                    return $this->content;
                }

                $this->content = new stdClass;

                $this->content->items = [];

                $output = $this->page->get_renderer('block_enrol_student');
                $emaillist = new email_list($this->page->course);

                $this->content->text = $output->render($emaillist);

                $event = \block_enrol_student\event\instrumentation_log::create(
                    array('context' => $syscontext)
                );
                $event->trigger();
                return $this->content;
            }
        }
    }
}

