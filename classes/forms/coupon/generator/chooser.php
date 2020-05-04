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
 * Coupon generator choice form
 *
 * File         chooser.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\forms\coupon\generator;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * block_coupon\forms\coupon\generator\chooser
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class chooser extends \moodleform {

    /**
     * form definition
     */
    public function definition() {
        $mform = & $this->_form;

        $mform->addElement('header', 'header', get_string('heading:coupon_type', 'block_coupon'));
        if (!$strinfo = get_config('block_coupon', 'info_coupon_type')) {
            $strinfo = get_string('missing_config_info', 'block_coupon');
        }
        $mform->addElement('static', 'info', '', $strinfo);

        // Type of coupon.
        // Only create first element with label.
        $mform->addElement('radio', 'coupon_type[type]', get_string('label:coupon_type', 'block_coupon'),
                get_string('label:type_course', 'block_coupon'), 0);
        $mform->addElement('radio', 'coupon_type[type]', '', get_string('label:type_cohorts', 'block_coupon'), 1);
        $mform->setDefault('coupon_type[type]', 0);
        $mform->addRule('coupon_type[type]', get_string('error:required', 'block_coupon'), 'required', null, 'client');

        $this->add_action_buttons(true, get_string('button:next', 'block_coupon'));
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
        $rs = parent::validation($data, $files);
        if (!isset($data['coupon_type']['type'])) {
            $rs['coupon_type[type]'] = get_string('required');
        }
        return $rs;
    }

}
