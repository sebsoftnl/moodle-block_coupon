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
 * Course chooser implementation for use with block_coupon
 *
 * File         coursegroupingchoosecourse.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\controller;

use block_coupon\exception;
use block_coupon\coupon\typebase;

/**
 * block_coupon\manager\coursegroupingchoosecourse
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coursegroupingchoosecourse {

    /**
     * @var \moodle_page
     */
    protected $page;

    /**
     * @var \core_renderer
     */
    protected $output;

    /**
     * @var \block_coupon_renderer
     */
    protected $renderer;

    /**
     * Create new manager instance
     * @param \moodle_page $page
     * @param \core\output_renderer $output
     * @param \core_renderer|null $renderer
     */
    public function __construct($page, $output, $renderer = null) {
        $this->page = $page;
        $this->output = $output;
        $this->renderer = $renderer;
    }

    /**
     * Execute page request
     */
    public function execute_request() {
        $action = optional_param('action', null, PARAM_ALPHA);
        switch ($action) {
            default:
                $this->process_chooser();
                break;
        }
    }

    /**
     * Display course chooser
     */
    protected function process_chooser() {
        global $USER, $DB, $CFG;
        $code = required_param('code', PARAM_ALPHANUM);
        $gpid = required_param('gpid', PARAM_INT);

        // Get instances.
        $typeproc = typebase::get_type_instance($code);
        // Assert this is a coursegrouping one.
        if (!($typeproc instanceof \block_coupon\coupon\types\coursegrouping)) {
            throw new exception('err:invalid-coupon-type');
        }
        // Assertions.
        $typeproc->assert_not_claimed();
        $typeproc->assert_internal_checks($USER->id);

        // Fetch grouping record.
        $grouping = $DB->get_record('block_coupon_groupings', ['id' => $gpid], '*', MUST_EXIST);
        // Fetch coursegrouping record.
        $coursegrouping = $DB->get_record('block_coupon_coursegroupings',
                ['id' => $grouping->coursegroupingid], '*', MUST_EXIST);
        // Fetch cgcourse records.
        $cgcourses = $DB->get_records('block_coupon_cgcourses', ['coursegroupingid' => $coursegrouping->id], '', '*');
        $courseids = [];
        foreach ($cgcourses as $cgcourse) {
            $courseids[] = $cgcourse->courseid;
        }
        // Now correct for already enrolled.
        $courseids = \block_coupon\helper::fix_course_choices($courseids, $USER->id);
        if (empty($courseids)) {
            $redirect = (empty($typeproc->coupon->redirect_url)) ? $CFG->wwwroot . "/my" : $typeproc->coupon->redirect_url;
            \core\notification::warning(get_string('error:no-more-course-choices', 'block_coupon'));
            redirect($redirect);
        }
        // Gather courses.
        $courses = $DB->get_records_list('course', 'id', $courseids, 'fullname ASC', 'id,shortname,fullname,idnumber');

        $url = $this->get_url();
        $customdata = [
            $courses,
            $coursegrouping,
            $typeproc
        ];
        $mform = new \block_coupon\forms\coursechooser($url, $customdata);
        if ($mform->is_cancelled()) {
            // Redirect to /my.
            $redirect = new \moodle_url($CFG->wwwroot . '/my');
            redirect($redirect);
        } else if ($data = $mform->get_data()) {
            // And finally do the processing.
            $options = (object)['courses' => $data->courses];
            $typeproc->claim($USER->id, $options);

            $redirect = (empty($typeproc->coupon->redirect_url)) ? $CFG->wwwroot . "/my" : $typeproc->coupon->redirect_url;
            redirect($redirect, get_string('success:coupon_used', 'block_coupon'));
        }

        echo $this->output->header();
        echo $mform->render();
        echo $this->output->footer();
    }

    /**
     * Return new url based on the current page-url
     *
     * @param array $mergeparams
     * @return \moodle_url
     */
    protected function get_url($mergeparams = []) {
        $url = $this->page->url;
        $url->params($mergeparams);
        return $url;
    }

}
