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
 * Course chooser form
 *
 * File         coursechooser.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\forms;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * block_coupon\forms\coursechooser
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coursechooser extends \moodleform {

    /**
     * @var array
     */
    protected $courses;

    /**
     * @var \stdClass
     */
    protected $coursegrouping;

    /**
     * @var \block_coupon\coupon\types\coursegrouping
     */
    protected $typeproc;

    /**
     * form definition
     */
    public function definition() {
        $mform = & $this->_form;

        list($this->courses, $this->coursegrouping, $this->typeproc) = $this->_customdata;

        // Add explanation.
        $mform->addElement('static', '_desc', get_string('view:selectcourses:title', 'block_coupon'),
                get_string('choose:courses:explain', 'block_coupon', $this->coursegrouping));

        // Add choices.
        if ($this->coursegrouping->maxamount == 1) {
            foreach ($this->courses as $course) {
                $mform->addElement('radio', 'course', '', $course->fullname, $course->id);
            }
        } else {
            foreach ($this->courses as $course) {
                $mform->addElement('advcheckbox', "course[{$course->id}]", '', $course->fullname);
            }
        }

        $this->add_action_buttons(true);
    }

    /**
     * Validation
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Perform number of selections check if applicable.
        $courses = [];
        if ($this->coursegrouping->maxamount == 1) {
            if (isset($data['course'])) {
                $courses = [$data['course']];
            }
        } else {
            $tmp = $data['course'];
            foreach ($tmp as $id => $selected) {
                if ((bool)$selected) {
                    $courses[] = $id;
                }
            }
        }
        if (count($courses) === 0) {
            $errors['_desc'] = get_string('err:choose:atleastone', 'block_coupon', $this->coursegrouping->maxamount);
        } else if (count($courses) > $this->coursegrouping->maxamount) {
            $errors['_desc'] = get_string('err:choose:maxamount', 'block_coupon', $this->coursegrouping->maxamount);
        }

        return $errors;
    }

    /**
     * Return submitted data if properly submitted or returns NULL if validation fails or
     * if there is no submitted data.
     *
     * note: $slashed param removed
     *
     * @return object submitted data; NULL if not valid or not submitted or cancelled
     */
    public function get_data() {
        $data = parent::get_data();

        if (!$this->is_cancelled() && $this->is_submitted() && $this->is_validated()) {
            $courses = [];
            if ($this->coursegrouping->maxamount == 1) {
                if (isset($data->course)) {
                    $courses = [$data->course];
                }
            } else {
                $tmp = $data->course;
                foreach ($tmp as $id => $selected) {
                    if ((bool)$selected) {
                        $courses[] = $id;
                    }
                }
            }
            $data->courses = $courses;
        }

        return $data;
    }

}
