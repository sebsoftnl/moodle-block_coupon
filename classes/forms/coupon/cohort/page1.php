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
 * Cohort coupon generator form (step 1)
 *
 * File         page1.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\forms\coupon\cohort;

defined('MOODLE_INTERNAL') || die();

use block_coupon\helper;
use block_coupon\coupon\generatoroptions;

require_once($CFG->libdir . '/formslib.php');

/**
 * block_coupon\forms\coupon\cohort\page1
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page1 extends \moodleform {

    /**
     * @var generatoroptions
     */
    protected $generatoroptions;

    /**
     * Get reference to database
     * @return \moodle_database
     */
    protected function db() {
        global $DB;
        return $DB;
    }

    /**
     * form definition
     */
    public function definition() {
        global $CFG;
        $mform = & $this->_form;

        // Register element.
        $path = $CFG->dirroot . '/blocks/coupon/classes/forms/element/findcohorts.php';
        \MoodleQuickForm::registerElementType('findcohorts', $path, '\block_coupon\forms\element\findcohorts');

        list($this->generatoroptions) = $this->_customdata;

        $mform->addElement('header', 'header', get_string('heading:courseandvars', 'block_coupon'));
        if (!$strinfo = get_config('block_coupon', 'info_coupon_cohorts')) {
            $strinfo = get_string('missing_config_info', 'block_coupon');
        }
        $mform->addElement('static', 'info', '', $strinfo);

        // Coupon logo selection.
        \block_coupon\logostorage::add_select_form_elements($mform);

        // Add custom batchid.
        $mform->addElement('text', 'batchid', get_string('label:batchid', 'block_coupon'), ['maxlength' => 255]);
        $mform->setType('batchid', PARAM_TEXT);
        $mform->addHelpButton('batchid', 'label:batchid', 'block_coupon');
        $mform->addRule('batchid', null, 'maxlength', 255, 'client');

        // Select cohort(s).
        $multiselect = true;
        if (!empty($this->_customdata['cohortmultiselect'])) {
            $multiselect = (bool)$this->_customdata['cohortmultiselect'];
        }

        $mform->addElement('header', 'header', get_string('heading:input_cohorts', 'block_coupon'));

        // First we'll get some useful info.
        $cohorts = helper::get_cohorts();

        // And create data for multiselect.
        $arrcohortselect = array();
        foreach ($cohorts as $cohort) {
            $arrcohortselect[$cohort->id] = $cohort->name;
        }

        $options = ['multiple' => true, 'onlyvisible' => true];
        $mform->addElement('findcohorts', 'coupon_cohorts',
                get_string('label:coupon_cohorts', 'block_coupon'), $options);
        $mform->addRule('coupon_cohorts', get_string('error:required', 'block_coupon'), 'required', null, 'client');
        $mform->addHelpButton('coupon_cohorts', 'label:coupon_cohorts', 'block_coupon');

        // Select role(s).
        $roles = helper::get_role_menu(null, true);
        $attributes = [];
        // Role id.
        $selectrole = &$mform->addElement('select', 'coupon_role',
                get_string('label:coupon_role', 'block_coupon'), $roles, $attributes);
        $selectrole->setMultiple(false);
        $mform->setDefault('coupon_role', helper::get_default_coupon_role()->id);
        $mform->addHelpButton('coupon_role', 'label:coupon_role', 'block_coupon');

        // Configurable enrolment time.
        $mform->addElement('duration', 'enrolment_period',
                get_string('label:enrolment_period', 'block_coupon'), array('size' => 40, 'optional' => true));
        $mform->setDefault('enrolment_period', '0');
        $mform->addHelpButton('enrolment_period', 'label:enrolment_period', 'block_coupon');

        $this->add_action_buttons(true, get_string('button:next', 'block_coupon'));
    }

    /**
     * Validate input
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $err = parent::validation($data, $files);
        return $err;
    }

}
