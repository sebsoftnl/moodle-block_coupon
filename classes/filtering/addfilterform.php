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
 * Add filter form
 *
 * File         addfilterform.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   1999 Martin Dougiamas  http://dougiamas.com
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

namespace block_coupon\filtering;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/formslib.php');

/**
 * block_coupon\filtering\addfilterform
 *
 * @package     block_coupon
 *
 * @copyright   1999 Martin Dougiamas  http://dougiamas.com
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class addfilterform extends \moodleform {

    /**
     * Form definition.
     * Shamelessly ripped from user filter implementation.
     */
    public function definition() {
        $mform       =& $this->_form;
        $fields      = $this->_customdata['fields'];
        $extraparams = $this->_customdata['extraparams'];

        $mform->addElement('header', 'newfilter', get_string('newfilter', 'filters'));
        if (!empty($this->_customdata['alwayscollapsed'])) {
            // Use option to collapse filters as per default.
            // Expanded filters by deault generally drive me NUTS on larger filter forms!
            $mform->setExpanded('newfilter', false, true);
        }

        foreach ($fields as $ft) {
            // Nasty little hack so WE, internally, adhere to Moodle standards.
            if (method_exists($ft, 'setup_form')) {
                $ft->setup_form($mform);
            } else {
                // Legacy.
                $ft->setupForm($mform);
            }
        }

        // In case we wasnt to track some page params.
        if ($extraparams) {
            foreach ($extraparams as $key => $value) {
                $mform->addElement('hidden', $key, $value);
                $mform->setType($key, PARAM_RAW);
            }
        }

        // Add button.
        $mform->addElement('submit', 'addfilter', get_string('addfilter', 'filters'));
    }

}
