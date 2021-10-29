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
 * @category   output
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_enrol_student\output;

defined('MOODLE_INTERNAL') || die();

require(__DIR__ . '/../../lib.php');

use renderable;
use renderer_base;
use templatable;

/**
 * Email list renderable class.
 *
 * @package    enrol_student
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class email_list implements renderable, templatable {

    /** @var int The course ID. */
    protected $course;

    /**
     * Constructor.
     *
     * @param  $course The course data.
     */
    public function __construct($course) {
        $this->course = $course;
    }

    /**
     * @param renderer_base $output
     * @return array|\stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = [
            'courseid' => $this->course->id,
            'perpage' => DEFAULT_STUDENT_PER_PAGE
        ];

        return $data;
    }
}

