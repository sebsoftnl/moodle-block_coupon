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
 * This file contains the template element course's core interaction API.
 *
 * @package    block_coupon
 * @copyright  2023 RvD <helpdesk@sebsoft.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\template\element\course;

/**
 * The template element course's core interaction API.
 *
 * @package    block_coupon
 * @copyright  2023 RvD <helpdesk@sebsoft.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class element extends \block_coupon\template\element {
    /**
     * Handles rendering the element on the pdf.
     *
     * @param \pdf $pdf the pdf object
     * @param boolean $preview true if it is a preview, false otherwise
     * @param \stdClass $user the user we are rendering this for
     * @param \stdClass|null $extradata -- expects "course" to be present
     */
    public function render($pdf, $preview, $user, ?\stdClass $extradata = null) {
        // If there is no element data, we have nothing to display.
        if (empty($this->get_data())) {
            return;
        }

        $data = json_decode($this->get_data());

        $courses = ($extradata?->courses) ?? [];
        $coursenames = [];
        foreach ($courses as $course) {
            $optid = ($course->id == 0) ? null : $course->id; // Prevent fatal error on format_string.
            if ($data->coursenaming == 'fullname') {
                $coursenames[] = format_string($course->fullname, true, $optid);
            } else {
                $coursenames[] = format_string($course->shortname, true, $optid);
            }
        }
        $coursestr = implode(', ', $coursenames);

        \block_coupon\template\element_helper::render_content($pdf, $this, $coursestr);
    }

    /**
     * Render the element in html.
     *
     * This function is used to render the element when we are using the
     * drag and drop interface to position it.
     *
     * @return string the html
     */
    public function render_html() {
        $course = 'course' . random_string();

        return \block_coupon\template\element_helper::render_html_content($this, $course);
    }

    /**
     * This function renders the form elements when adding a template element.
     *
     * @param \MoodleQuickForm $mform the edit_form instance.
     */
    public function render_form_elements($mform) {
        // Control shortname/fullname.
        $options = [
            'shortname' => get_string('shortname'),
            'fullname' => get_string('fullname'),
        ];
        $mform->addElement('select', 'coursenaming', get_string('coursenaming', 'block_coupon'), $options);
        $mform->setDefault('coursenaming', 'fullname');
        $mform->addHelpButton('coursenaming', 'coursenaming', 'block_coupon');

        parent::render_form_elements($mform);
    }

    /**
     * This will handle how form data will be saved into the data column in the
     * block_coupon_elements table.
     *
     * @param \stdClass $data the form data
     * @return string the text
     */
    public function save_unique_data($data) {
        $extradata = (object)['coursenaming' => $data->coursenaming];
        return json_encode($extradata);
    }

    /**
     * Sets the data on the form when editing an element.
     *
     * @param \MoodleQuickForm $mform the edit_form instance
     */
    public function definition_after_data($mform) {
        // Set the item and format for this element.
        if (!empty($this->get_data())) {
            $data = json_decode($this->get_data());

            $element = $mform->getElement('coursenaming');
            $element->setValue($data->coursenaming);
        }
        parent::definition_after_data($mform);
    }

    /**
     * Get/load extra data that's needed for this element.
     *
     * @param stdClass $coupon
     * @param bool $preview -- is this a preview mode?
     * @return mixed
     */
    public function get_extra_data($coupon, bool $preview) {
        // Get coupon courses.
        if ($preview) {
            $courses = [
                (object)['id' => 0, 'shortname' => 'COURSESHORTNAME', 'fullname' => 'COURSEFULLNAME'],
            ];
        } else {
            $courses = \block_coupon\helper::get_coupon_courses($coupon);
        }
        return (object)['courses' => $courses];
    }
}
