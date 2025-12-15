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
 * this file contains the table to display coupons
 *
 * File         coupons.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon\tables;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

/**
 * block_coupon\tables\coupons
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base extends \table_sql {
    /**
     *
     * @var \block_coupon\filtering\filtering
     */
    protected $filtering;

    /**
     * Get filtering instance
     * @return \block_coupon\filtering\filtering
     */
    public function get_filtering() {
        return $this->filtering;
    }

    /**
     * Set filtering instance
     * @param \block_coupon\filtering\filtering $filtering
     * @return \block_coupon\tables\coupons
     */
    public function set_filtering(\block_coupon\filtering\filtering $filtering) {
        $this->filtering = $filtering;
        return $this;
    }

    /**
     * Render visual representation of the 'timecreated' column for use in the table
     *
     * @param \stdClass $row
     * @return string time string
     */
    public function col_timecreated($row) {
        return userdate($row->timecreated);
    }

    /**
     * Return the image tag representing an action image
     *
     * @param string $action
     * @return string HTML image tag
     */
    protected function get_action_image($action) {
        global $OUTPUT;
        return '<img src="' . $OUTPUT->image_url($action, 'block_coupon') . '"/>';
    }

    /**
     * Return a string containing the link to an action
     *
     * @param \stdClass $row
     * @param string $action
     * @param boolean $confirm true to enable javascript confirmation of this action
     * @return string link representing the action with an image
     */
    protected function get_action($row, $action, $confirm = false) {
        $actionstr = 'str' . $action;
        $onclick = '';
        if ($confirm) {
            $actionconfirmstr = 'str' . $action . 'confirm';
            $onclick = ' onclick="return confirm(\'' . $this->{$actionconfirmstr} . '\');"';
        }
        $url = new \moodle_url(
            $this->baseurl,
            ['action' => $action, 'itemid' => $row->id, 'sesskey' => sesskey()]
        );
        return '<a ' . $onclick . 'href="' . $url->out() .
                '" alt="' . $this->{$actionstr} .
                '">' . $this->get_action_image($action) . '</a>';
    }
}
