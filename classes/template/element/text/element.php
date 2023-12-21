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
 * This file contains the template element text's core interaction API.
 *
 * @package    block_coupon
 * @copyright  2023 R.J. van Dongen <rogier@sebsoft.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\template\element\text;

/**
 * The template element text's core interaction API.
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
        $a = '<table>';
        $vars = $this->get_template_vars();
        foreach ($vars as $k => $v) {
            $a .= "<tr><td>{$k}</td><td>{$v}</td></tr>";
        }
        $a .= '</table>';
        $desc = get_string('element:text:templatevars', 'block_coupon', $a);
        $mform->addElement('static', '_textvars', '', $desc);

        $mform->addElement('textarea', 'text', get_string('text', 'block_coupon'));
        $mform->setType('text', PARAM_RAW);
        $mform->addHelpButton('text', 'text', 'block_coupon');

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
        return $data->text;
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
        \block_coupon\template\element_helper::render_content($pdf, $this, $this->get_text($extradata));
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
        $extradata = (object)[
            'code' => random_string(16)
        ];
        return \block_coupon\template\element_helper::render_html_content($this, $this->get_text($extradata));
    }

    /**
     * Sets the data on the form when editing an element.
     *
     * @param \MoodleQuickForm $mform the edit_form instance
     */
    public function definition_after_data($mform) {
        if (!empty($this->get_data())) {
            $element = $mform->getElement('text');
            $element->setValue($this->get_data());
        }
        parent::definition_after_data($mform);
    }

    /**
     * Helper function that returns the text.
     *
     * @param \stdClass $extradata -- expects "code" to be present
     * @return string
     */
    protected function get_text(\stdClass $extradata = null) : string {
        $context = \block_coupon\template\element_helper::get_context($this->get_id());
        $templatedata = $this->get_template_data($extradata);
        $translations = $this->mod_vars($templatedata);
        $text = strtr($this->get_data(), $translations);
        return format_text($text, FORMAT_HTML, ['context' => $context]);
    }

    /**
     * Helper function that returns the template vars.
     *
     * @return array
     */
    protected function get_template_vars() : array {
        return array(
            '###code###' => get_string('label:coupon_code', 'block_coupon'),
            '###sitename###' => get_string('fullsitename'),
            '###siteurl###' => get_string('siteurl', 'hub'),
        );
    }

    /**
     * Helper function that returns the template data.
     *
     * @param \stdClass $extradata -- expects "code" to be present
     * @return array
     */
    protected function get_template_data(\stdClass $extradata = null) : \stdClass {
        global $CFG, $SITE;
        if (empty($extradata)) {
            $extradata = new \stdClass();
        }
        if (!isset($extradata->code)) {
            $extradata->code = random_string();
        }
        if (!isset($extradata->sitename)) {
            $extradata->sitename = $SITE->fullname;
        }
        if (!isset($extradata->siteurl)) {
            $extradata->siteurl = $CFG->wwwroot;
        }
        return $extradata;
    }

    /**
     * Modify templateable variables.
     *
     * @param array|\stdClass $a variables
     * @return array array of key/values for use in strtr
     * @throws \moodle_exception
     */
    protected function mod_vars($a) {
        if (is_object($a)) {
            $a = (array)$a;
        }
        $tr = array();
        foreach ($a as $k => $v) {
            $tr['###'.$k.'###'] = $v;
        }
        return $tr;
    }

}
