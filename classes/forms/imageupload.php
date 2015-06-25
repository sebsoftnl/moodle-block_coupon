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
 * Image upload form
 *
 * File         imageupload.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

namespace block_coupon\forms;
require_once($CFG->libdir . '/formslib.php');

/**
 * block_coupon\forms\imageupload
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class imageupload extends \moodleform {

    /**
     * form definition
     */
    public function definition() {
        $mform = & $this->_form;

        $mform->addElement('header', 'header', get_string('heading:info', 'block_coupon'));
        if (!$strinfo = get_config('block_coupon', 'info_imageupload')) {
            $strinfo = get_string('missing_config_info', 'block_coupon');
        }
        $mform->addElement('static', 'info', '', $strinfo);

        $mform->addElement('header', 'header', get_string('heading:imageupload', 'block_coupon'));

        $url = new \moodle_url('/blocks/coupon/view/logodisplay.php');
        $display = '<img src="' . $url . '" width="210" height="297" title="' .
                get_string('label:current_image', 'block_coupon') . '" />';
        $mform->addElement('static', 'logodisplay', get_string('label:current_image', 'block_coupon'), $display);

        // Add IMAGE uploader.
        $attributes = array('accepted_types' => array('.png', '.jpg'));
        $mform->addElement('filepicker', 'userfile', get_string('file'), null, $attributes);
        $mform->addRule('userfile', 'required', 'required', null, 'client');

        // Buttons.
        $this->add_action_buttons(true, get_string('button:next', 'block_coupon'));
    }

}