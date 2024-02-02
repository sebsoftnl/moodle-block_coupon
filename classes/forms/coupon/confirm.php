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
 * Course coupon generator form (step 5)
 *
 * File         page5.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_coupon\forms\coupon;

use block_coupon\forms\baseform;

/**
 * block_coupon\forms\coupon\course\page5
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class confirm extends baseform {

    /**
     * form definition
     */
    public function definition() {
        global $PAGE, $OUTPUT;
        $PAGE->requires->js_call_amd('block_coupon/confirm', 'init', ['.block-coupon-container form.mform']);
        $mform = & $this->_form;

        list($this->generatoroptions) = $this->_customdata;

        $mform->addElement('header', 'header', get_string('confirm'));

        $mform->addElement('html', $this->generatoroptions->create_confirmation_info());

        $mform->addElement('static', '_msg', '', '<div class="processing hidden alert alert-info">'.
                $OUTPUT->render_from_template('core/loading', []). '<span class="ml-2">'.
                get_string('coupon:generator:processing', 'block_coupon').
                '</span></div>');

        $this->add_action_buttons(true, get_string('button:next', 'block_coupon'), true);
    }

    /**
     * Inject preview button
     *
     * @param array $buttonarray
     */
    protected function add_extra_buttons(array &$buttonarray) {
        $mform = & $this->_form;
        if (!$this->generatoroptions->generatecodesonly) {
            if ($this->generatoroptions->pdftype === 'logo') {
                $buttonarray[] = &$mform->createElement('button', 'preview', get_string('preview-pdf', 'block_coupon'), [
                    'id' => 'block-coupon-preview-btn',
                    'data-templated' => false,
                    'data-font' => $this->generatoroptions->font,
                    'data-logo' => $this->generatoroptions->logoid,
                    'data-qr' => $this->generatoroptions->renderqrcode ? 1 : 0,
                ]);
            } else {
                $buttonarray[] = &$mform->createElement('button', 'preview', get_string('preview-pdf', 'block_coupon'), [
                    'id' => 'block-coupon-preview-btn',
                    'data-templated' => true,
                    'data-templateid' => $this->generatoroptions->templateid,
                ]);
            }
        }
    }

    /**
     * Perform validation.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        return $errors;
    }

}
