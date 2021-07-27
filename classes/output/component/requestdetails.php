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
 * File         requestdetails.php
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
 * block_coupon\manager\requestdetails
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @author      Sebastian Berm <sebastian@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class requestdetails implements \renderable, \templatable {

    /**
     * @var \stdClass instance from requests table
     */
    protected $instance;

    /**
     * Create new instance
     * @param \stdClass $request instance from requests table
     */
    public function __construct($request) {
        $this->instance = $request;
    }

    /**
     * Return template variables
     * @param \renderer_base $output
     * @return \stdClass
     */
    public function export_for_template(\renderer_base $output) {
        global $DB;
        $data = new \stdClass();

        $generatoroptions = unserialize($this->instance->configuration);

        $data->generatoroptions = json_decode(json_encode($generatoroptions));

        $data->amount = $generatoroptions->amount;
        $data->codesize = $generatoroptions->codesize;
        $data->emailto = $generatoroptions->emailto;

        $data->ownerfullname = fullname(\core_user::get_user($generatoroptions->ownerid));
        $data->redirecturl = $generatoroptions->redirecturl;
        if (empty($data->redirecturl)) {
            $data->redirecturl = '-';
        }

        $data->generatesinglepdfs = $generatoroptions->generatesinglepdfs;
        $data->renderqrcode = $generatoroptions->renderqrcode;

        if ($generatoroptions->logoid == -1) {
            $data->logo = get_string('logo:none', 'block_coupon');
        } else if ($generatoroptions->logoid == 0) {
            $data->logo = get_string('logo:default', 'block_coupon');
            $data->logo = $generatoroptions->logoid;
        } else {
            $data->logo = $generatoroptions->logoid;
        }

        if ($generatoroptions->enrolperiod == 0) {
            $data->enrolperiod = get_string('enrolperiod:indefinite', 'block_coupon');
        } else {
            $data->enrolperiod = format_time($generatoroptions->enrolperiod);
        }

        if ($generatoroptions->type == \block_coupon\coupon\generatoroptions::COURSE) {
            $data->type = get_string('label:type_course', 'block_coupon');
            $data->hascourses = true;
            $data->courses = array_values($DB->get_records_list('course', 'id',
                    $generatoroptions->courses, 'fullname ASC', 'id,shortname,fullname,idnumber'));
        } else {
            $data->type = get_string('label:type_cohort', 'block_coupon');
            $data->cohorts = array_values($DB->get_records_list('cohort', 'id',
                    $generatoroptions->cohorts, 'name ASC', 'id,name,idnumber'));
            $data->hascohorts = true;
        }

        return $data;
    }

}
