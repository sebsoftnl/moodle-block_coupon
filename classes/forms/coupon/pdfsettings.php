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
 * Course coupon generator form (step 1)
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

namespace block_coupon\forms\coupon;

use block_coupon\forms\baseform;
use block_coupon\helper;

/**
 * block_coupon\forms\coupon\course\page1
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class pdfsettings extends baseform {

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

        list($this->generatoroptions) = $this->_customdata;

        $mform->addElement('header', 'header', get_string('pdfsettings', 'block_coupon'));

        // What i wish here:
        // - static code | generated code.
        // I *think* I can pull this off AFTER the "campaign type".
        // When a campaign type does not support "1 code; N useages", we have a different form.

        helper::add_template_options($mform);

        $this->add_action_buttons(true, get_string('button:next', 'block_coupon'), true);

        $data = [];
        $data['generate_pdf'] = $this->generatoroptions->generatesinglepdfs;
        $data['usetype'] = $this->generatoroptions->pdftype;
        $data['templateid'] = $this->generatoroptions->templateid;
        $data['logo'] = $this->generatoroptions->logoid;
        $data['font'] = $this->generatoroptions->font;
        $data['renderqrcode'] = $this->generatoroptions->renderqrcode ? 1 : 0;
        $this->set_data($data);
    }

    /**
     * Validate input
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        global $DB;
        // Make sure batch id is unique if provided.
        $err = parent::validation($data, $files);
        return $err;
    }

}
