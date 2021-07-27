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
        global $CFG, $DB, $USER;
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        if (empty($this->instance)) {
            throw new \moodle_exception('No instance', 'block_coupon');
        }

        $arrparams = array();
        $arrparams['id'] = $this->instance->id;
        $arrparams['courseid'] = $this->course->id;

        // We'll fill the array of menu items with everything the logged in user has permission to.
        $menuitems = array();

        // The "button class" for links.
        $linkseparator = '';
        $cfgbuttonclass = get_config('block_coupon', 'buttonclass');
        $btnclass = 'btn-coupon';
        if ($cfgbuttonclass != 'none') {
            $btnclass .= ' ' . $cfgbuttonclass;
            $linkseparator = '<br/>';
        }
        // Generate Coupon.
        $baseparams = array('id' => $this->instance->id);
        if (has_capability('block/coupon:generatecoupons', $this->context)) {
            $urlgeneratecoupons = new moodle_url($CFG->wwwroot . '/blocks/coupon/view/generator/index.php', $baseparams);
            $menuitems[] = html_writer::link($urlgeneratecoupons,
                    get_string('url:generate_coupons', 'block_coupon'), ['class' => $btnclass]);

            $urlmanagelogos = new moodle_url($CFG->wwwroot . '/blocks/coupon/view/managelogos.php', $baseparams);
            $menuitems[] = html_writer::link($urlmanagelogos,
                    get_string('url:managelogos', 'block_coupon'), ['class' => $btnclass]);

            // Add link to requests.
            $requestusersurl = new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/requests/admin.php',
                    $baseparams + ['action' => 'users']);
            $menuitems[] = html_writer::link($requestusersurl,
                    get_string('tab:requestusers', 'block_coupon'), ['class' => $btnclass]);
            $requestsurl = new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/requests/admin.php',
                    $baseparams + ['action' => 'requests']);
            $menuitems[] = html_writer::link($requestsurl,
                    get_string('tab:requests', 'block_coupon'), ['class' => $btnclass]);
        }

        // View Reports.
        if (has_capability('block/coupon:viewreports', $this->context)) {
            $urlreports = new moodle_url($CFG->wwwroot . '/blocks/coupon/view/reports.php', $baseparams);
            $urlunusedreports = new moodle_url($CFG->wwwroot . '/blocks/coupon/view/couponview.php',
                    array('id' => $this->instance->id, 'tab' => 'unused'));

            $menuitems[] = html_writer::link($urlreports,
                    get_string('url:view_reports', 'block_coupon'), ['class' => $btnclass]);
            $menuitems[] = html_writer::link($urlunusedreports,
                    get_string('url:view_unused_coupons', 'block_coupon'), ['class' => $btnclass]);
        }

        // Input Coupon.
        if (has_capability('block/coupon:inputcoupons', $this->context)) {
            $urlinputcoupon = new moodle_url($CFG->wwwroot . '/blocks/coupon/view/input_coupon.php', $baseparams);

            $couponform = "
                <form action='$urlinputcoupon' method='post'>
                    <table>
                        <tr><td>" . get_string('label:enter_coupon_code', 'block_coupon') . ":</td></tr>
                        <tr><td><input type='text' name='coupon_code'></td></tr>
                        <tr><td><input type='submit' name='submitbutton' value='"
                    . get_string('button:submit_coupon_code', 'block_coupon') . "' class='{$btnclass}'></td></tr>
                    </table>
                    <input type='hidden' name='id' value='{$this->instance->id}' />
                    <input type='hidden' name='submitbutton' value='Submit Coupon' />
                    <input type='hidden' name='_qf__block_coupon_forms_coupon_validator' value='1' />
                    <input type='hidden' name='sesskey' value='" . sesskey() . "' />
                </form>";

            $displayinputhelp = (bool)get_config('block_coupon', 'displayinputhelp');
            if ($displayinputhelp) {
                $menuitems[] = "<div>".get_string('str:inputhelp', 'block_coupon')."<br/>{$couponform}</div>";

            } else {
                $menuitems[] = $couponform;
            }
        }

        // Signup using a coupon.
        if (!isloggedin() || isguestuser()) {
            $urlsignupcoupon = new moodle_url($CFG->wwwroot . '/blocks/coupon/view/signup.php', $baseparams);
            $signupurl = html_writer::link($urlsignupcoupon,
                    get_string('url:couponsignup', 'block_coupon'), ['class' => $btnclass]);
            $displaysignuphelp = (bool)get_config('block_coupon', 'displayregisterhelp');
            if ($displaysignuphelp) {
                $menuitems[] = "<div>".get_string('str:signuphelp', 'block_coupon')."<br/>{$signupurl}</div>";

            } else {
                $menuitems[] = $signupurl;
            }
        }

        // Add link to ability to request coupons if applicable.
        if ($DB->record_exists('block_coupon_rusers', ['userid' => $USER->id])) {
            $urlrequestcoupon = new moodle_url($CFG->wwwroot . '/blocks/coupon/view/requests/userrequest.php', $baseparams);
            $menuitems[] = html_writer::link($urlrequestcoupon,
                    get_string('request:coupons', 'block_coupon'), ['class' => $btnclass]);
        }

        // Now print the menu blocks.
        foreach ($menuitems as $item) {
            $this->content->footer .= $item . $linkseparator;
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
