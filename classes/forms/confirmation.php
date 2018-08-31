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
 * Confirmation form.
 *
 * File         confirmation.php
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
 * block_coupon\forms\confirmation.
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class confirmation extends \moodleform {

    /**
     * form definition
     */
    protected function definition() {
        $mform = & $this->_form;

        list($headertext, $description, $confirmmessage) = $this->_customdata;

        $mform->addElement('header', 'xhead1', $headertext);
        $mform->addElement('static', 'xstaticdesc1', '', $description);

        $mform->addElement('advcheckbox', 'confirm', '', $confirmmessage, null, array(0, 1));
        $mform->setType('confirm', PARAM_BOOL);

        $this->add_action_buttons(true, get_string('button:continue', 'block_coupon'));
    }

}
