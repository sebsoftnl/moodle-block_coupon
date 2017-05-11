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
 * File         logo.php
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

defined('MOODLE_INTERNAL') || die();

use block_coupon\logostorage;

require_once($CFG->libdir . '/formslib.php');

/**
 * block_coupon\forms\logo
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class logo extends \moodleform {

    /**
     * Define the form.
     */
    protected function definition() {
        $mform = $this->_form;
        logostorage::add_form_elements($mform);
        $this->add_action_buttons();
    }

    /**
     * Store or cancel
     * @return boolean true is cancelled or stored, false otherwise
     */
    public function process_store() {
        if ($this->is_cancelled()) {
            return true;
        }
        if ($data = $this->get_data()) {
            logostorage::store_draft_files($data->logos);
            return true;
        }
        return false;
    }

}

