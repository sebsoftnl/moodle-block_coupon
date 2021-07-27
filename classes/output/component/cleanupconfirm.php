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
 * Request admin manager implementation for use with block_coupon
 *
 * File         cleanupconfirm.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @author      Sebastian Berm <sebastian@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\output\component;

defined('MOODLE_INTERNAL') || die();

/**
 * block_coupon\manager\cleanupconfirm
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @author      Sebastian Berm <sebastian@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cleanupconfirm implements \renderable, \templatable {

    /**
     * @var \stdClass deletion data
     */
    protected $data;

    /**
     * Create new data
     * @param \stdClass $data deletion options
     */
    public function __construct($data) {
        $this->data = $data;
    }

    /**
     * Return template variables
     * @param \renderer_base $output
     * @return \stdClass
     */
    public function export_for_template(\renderer_base $output) {
        global $DB;
        $data = new \stdClass();

        $data->hasstartdate = false;
        $data->hasenddate = false;
        if (!empty($this->data->timebefore)) {
            $data->hasstartdate = true;
            $data->startdate = userdate($this->data->timebefore);
        }
        if (!empty($this->data->timeafter)) {
            $data->hasenddate = true;
            $data->enddate = userdate($this->data->timeafter);
        }

        $useopts = [
            0 => get_string('coupon:used:all', 'block_coupon'),
            1 => get_string('coupon:used:yes', 'block_coupon'),
            2 => get_string('coupon:used:no', 'block_coupon')
        ];
        $data->usedtypestr = $useopts[$this->data->used];

        $typeopts = array(
            0 => get_string('coupon:type:all', 'block_coupon'),
            1 => get_string('course'),
            2 => get_string('cohort', 'core_cohort'),
            3 => get_string('th:batchid', 'block_coupon'),
        );
        $data->typestr = $typeopts[$this->data->type];

        $data->deletestrings = [];
        switch ($this->data->type) {
            case 1:
                if (!empty($this->data->course)) {
                    $records = $DB->get_records_list('course', 'id', $this->data->course, 'fullname ASC', 'id,fullname');
                    foreach ($records as $record) {
                        $data->deletestrings[] = $record->fullname;
                    }
                }
                break;
            case 2:
                if (!empty($this->data->cohort)) {
                    $records = $DB->get_records_list('cohort', 'id', $this->data->cohort, 'name ASC', 'id,name,idnumber');
                    foreach ($records as $record) {
                        if (!empty($record->idnumber)) {
                            $data->deletestrings[] = "{$record->name} ({$record->idnumber})";
                        } else {
                            $data->deletestrings[] = "{$record->name}";
                        }
                    }
                }
                break;
            case 3:
                $data->deletestrings = $this->data->batchid;
                break;
            case 0:
            default:
                break;
        }
        $data->hasdeletestrings = !empty($data->deletestrings);

        $data->deletecount = \block_coupon\helper::count_cleanup_coupons($this->data);

        return $data;
    }

}
