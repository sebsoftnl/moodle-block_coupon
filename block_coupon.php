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
 * Block implementation
 *
 * File         block_coupon.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * block_coupon
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_coupon extends block_base {

    /**
     * initializes block
     */
    public function init() {
        global $CFG;
        $this->title = get_string('blockname', 'block_coupon');
        include($CFG->dirroot . '/blocks/coupon/version.php');
        $this->version = $plugin->version;
        $this->cron = $plugin->cron;
    }

    /**
     * Get/load block contents
     * @return stdClass
     */
    public function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        if (empty($this->instance)) {
            print_error('No instance ' . 'block_coupon');
        }

        $arrparams = array();
        $arrparams['id'] = $this->instance->id;
        $arrparams['courseid'] = $this->course->id;

        // We'll fill the array of menu items with everything the logged in user has permission to.
        $menuitems = array();

        // Generate Coupon.
        if (has_capability('block/coupon:generatecoupons', $this->context)) {
            $urlgeneratecoupons = new moodle_url('/blocks/coupon/view/generate_coupon.php', array('id' => $this->instance->id));
            $urluploadimage = new moodle_url('/blocks/coupon/view/uploadimage.php', array('id' => $this->instance->id));

            $menuitems[] = html_writer::link($urlgeneratecoupons, get_string('url:generate_coupons', 'block_coupon'));
            $menuitems[] = html_writer::link($urluploadimage, get_string('url:uploadimage', 'block_coupon'));
        }

        // View Reports.
        if (has_capability('block/coupon:viewreports', $this->context)) {
            $urlreports = new moodle_url('/blocks/coupon/view/reports.php', array('id' => $this->instance->id));
            $urlunusedreports = new moodle_url('/blocks/coupon/view/coupon_view.php',
                    array('id' => $this->instance->id, 'tab' => 'unused'));

            $menuitems[] = html_writer::link($urlreports, get_string('url:view_reports', 'block_coupon'));
            $menuitems[] = html_writer::link($urlunusedreports, get_string('url:view_unused_coupons', 'block_coupon'));
        }

        // Input Coupon.
        if (has_capability('block/coupon:inputcoupons', $this->context)) {
            $urlinputcoupon = new moodle_url('/blocks/coupon/view/input_coupon.php', array('id' => $this->instance->id));

            $couponform = "
                <form action='$urlinputcoupon' method='post'>
                    <table>
                        <tr><td>" . get_string('label:enter_coupon_code', 'block_coupon') . ":</td></tr>
                        <tr><td><input type='text' name='coupon_code'></td></tr>
                        <tr><td><input type='submit' name='submitbutton' value='"
                    . get_string('button:submit_coupon_code', 'block_coupon') . "'></td></tr>
                    </table>
                    <input type='hidden' name='id' value='{$this->instance->id}' />
                    <input type='hidden' name='submitbutton' value='Submit Coupon' />
                    <input type='hidden' name='_qf__block_coupon_forms_coupon_validator' value='1' />
                    <input type='hidden' name='sesskey' value='" . sesskey() . "' />
                </form>";

            $menuitems[] = $couponform;
        }

        // Now print the menu blocks.
        foreach ($menuitems as $item) {

            $this->content->footer .= $item . "<br />";
        }
    }

    /**
     * Which page types this block may appear on.
     *
     * @return array page-type prefix => true/false.
     */
    public function applicable_formats() {
        return array('site-index' => true, 'my' => true);
    }

    /**
     * block specialization
     */
    public function specialization() {
        global $COURSE;
        $this->course = $COURSE;
    }

    /**
     * Is each block of this type going to have instance-specific configuration?
     *
     * @return bool true
     */
    public function instance_allow_config() {
        return true;
    }

    /**
     * Allow multiple instances of this block?
     *
     * @return bool false
     */
    public function instance_allow_multiple() {
        return false;
    }

    /**
     * Do we hide the block header?
     *
     * @return bool false
     */
    public function hide_header() {
        return false;
    }

    /**
     * Run cron job
     *
     * @deprecated since Moodle 2.6
     * @return bool true always
     */
    public function cron() {
        return true;
    }

    /**
     * has own config?
     *
     * @return bool true
     */
    public function has_config() {
        return true;
    }

}