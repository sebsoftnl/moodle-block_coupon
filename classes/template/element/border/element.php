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
 * This file contains the template element border's core interaction API.
 *
 * @package    block_coupon
 * @copyright  2023 R.J. van Dongen <rogier@sebsoft.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\template\element\border;

/**
 * The template element border's core interaction API.
 *
 * @package    block_coupon
 * @copyright  2023 R.J. van Dongen <rogier@sebsoft.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class element extends \block_coupon\template\element {

    /**
     * This function renders the form elements when adding a template element.
     *
     * @param \MoodleQuickForm $mform the edit_form instance
     */
    public function render_form_elements($mform) {
        // We want to define the width of the border.
        \block_coupon\template\element_helper::render_form_element_width($mform);

        // The only other thing to define is the colour we want the border to be.
        \block_coupon\template\element_helper::render_form_element_colour($mform);
    }

    /**
     * Handles rendering the element on the pdf.
     *
     * @param \pdf $pdf the pdf object
     * @param bool $preview true if it is a preview, false otherwise
     * @param \stdClass $user the user we are rendering this for
     * @param \stdClass $extradata -- expects "code" to be present
     */
    public function render($pdf, $preview, $user, \stdClass $extradata = null) {
        $colour = \TCPDF_COLORS::convertHTMLColorToDec($this->get_colour(), $colour);
        $pdf->SetLineStyle(array('width' => $this->get_data(), 'color' => $colour));
        $pdf->Line(0, 0, $pdf->getPageWidth(), 0);
        $pdf->Line($pdf->getPageWidth(), 0, $pdf->getPageWidth(), $pdf->getPageHeight());
        $pdf->Line(0, $pdf->getPageHeight(), $pdf->getPageWidth(), $pdf->getPageHeight());
        $pdf->Line(0, 0, 0, $pdf->getPageHeight());
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
        return '';
    }

    /**
     * Performs validation on the element values.
     *
     * @param array $data the submitted data
     * @param array $files the submitted files
     * @return array the validation errors
     */
    public function validate_form_elements($data, $files) {
        // Array to return the errors.
        $errors = array();

        // Validate the width.
        $errors += \block_coupon\template\element_helper::validate_form_element_width($data, false);

        // Validate the colour.
        $errors += \block_coupon\template\element_helper::validate_form_element_colour($data);

        return $errors;
    }

    /**
     * Sets the data on the form when editing an element.
     *
     * @param \MoodleQuickForm $mform the edit_form instance
     */
    public function definition_after_data($mform) {
        if (!empty($this->get_data())) {
            $element = $mform->getElement('width');
            $element->setValue($this->get_data());
        }
        parent::definition_after_data($mform);
    }

    /**
     * This will handle how form data will be saved into the data column in the
     * block_coupon_elements table.
     *
     * @param \stdClass $data the form data
     * @return string the json encoded array
     */
    public function save_unique_data($data) {
        return $data->width;
    }

    /**
     * Is element draggable in HTML view?
     *
     * @return boolean
     */
    public function is_draggable_in_html_view() {
        return false;
    }

    /**
     * Is element visible in HTML view?
     *
     * @return boolean
     */
    public function is_visible_in_html_view() {
        return false;
    }

}
