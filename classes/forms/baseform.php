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
 * Base generator form implementation
 *
 * File         baseform.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_coupon\forms;

use block_coupon\coupon\generatoroptions;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');


/**
 * Base generator form implementation
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class baseform extends \moodleform {

    /**
     * Get reference to database
     * @return \moodle_database
     */
    protected function db() {
        global $DB;
        return $DB;
    }

    /**
     * @var generatoroptions
     */
    protected $generatoroptions;

    /**
     * @var array of "back" buttons
     */
    protected $previousbuttons = [];

    /**
     * Extend button array.
     * Submit, cancel and optionally back should already be there.
     *
     * @param array $buttonarray
     */
    protected function add_extra_buttons(array &$buttonarray) {
        // No-op.
    }

    /**
     * Use this method to a cancel and submit button to the end of your form. Pass a param of false
     * if you don't want a cancel button in your form. If you have a cancel button make sure you
     * check for it being pressed using is_cancelled() and redirecting if it is true before trying to
     * get data with get_data().
     *
     * @param bool $cancel whether to show cancel button, default true
     * @param string $submitlabel label for submit button, defaults to get_string('savechanges')
     * @param bool $backbutton whether to show back button, default false
     */
    public function add_action_buttons($cancel = true, $submitlabel = null, $backbutton = false) {
        if (is_null($submitlabel)) {
            $submitlabel = get_string('savechanges');
        }
        $mform =& $this->_form;
        if ($cancel) {
            // When two elements we need a group.
            $buttonarray = array();
            if ($backbutton) {
                $this->register_back_button('backbutton');
                $buttonarray[] = &$mform->createElement('submit', 'backbutton', get_string('back'));
            }
            $buttonarray[] = &$mform->createElement('submit', 'submitbutton', $submitlabel);
            $buttonarray[] = &$mform->createElement('cancel');
            $this->add_extra_buttons($buttonarray);
            $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
            $mform->closeHeaderBefore('buttonar');
        } else {
            // No group needed.
            $mform->addElement('submit', 'submitbutton', $submitlabel);
            $mform->closeHeaderBefore('submitbutton');
        }
    }

    /**
     * Determine whether or not the button pressed was the "previous" button
     *
     * @return boolean
     */
    public function is_previous() {
        $mform =& $this->_form;
        if ($mform->isSubmitted() && is_array($this->previousbuttons)) {
            foreach ($this->previousbuttons as $previousbutton) {
                if (optional_param($previousbutton, 0, PARAM_RAW)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Register named button as a "previous" button
     *
     * @param string $fieldsname
     */
    protected function register_back_button($fieldsname) {
        if (!is_array($this->previousbuttons)) {
            $this->previousbuttons = [];
        }
        $this->previousbuttons[] = $fieldsname;
    }

}
