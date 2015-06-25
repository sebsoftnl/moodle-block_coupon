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
 * Coupon generator form (first step)
 *
 * File         generator.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\forms\coupon;
require_once($CFG->libdir . '/formslib.php');

/**
 * block_coupon\forms\
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generator extends \moodleform {

    /**
     * form definition
     */
    public function definition() {
        $mform = & $this->_form;

        $mform->addElement('header', 'header', get_string('heading:info', 'block_coupon'));

        if (!$strinfo = get_config('block_coupon', 'info_coupon_type')) {
            $strinfo = get_string('missing_config_info', 'block_coupon');
        }
        $mform->addElement('static', 'info', '', $strinfo);

        $mform->addElement('header', 'header', get_string('heading:coupon_type', 'block_coupon'));

        // Type of coupon.
        $typeoptions = array();
        $typeoptions[] = & $mform->createElement('radio', 'type', '', get_string('label:type_course', 'block_coupon'), 0);
        $typeoptions[] = & $mform->createElement('radio', 'type', '', get_string('label:type_cohorts', 'block_coupon'), 1);
        $mform->addGroup($typeoptions, 'coupon_type', get_string('label:coupon_type', 'block_coupon'), array(' '));
        $mform->setDefault('coupon_type[type]', 0);
        $mform->addRule('coupon_type', get_string('error:required', 'block_coupon'), 'required', null, 'client');
        $mform->addHelpButton('coupon_type', 'label:coupon_type', 'block_coupon');

        $this->add_action_buttons(true, get_string('button:next', 'block_coupon'));
    }

}