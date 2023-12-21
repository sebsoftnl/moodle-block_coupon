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
 * this file contains the couponpage helper
 *
 * File         couponpage.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_coupon;
use context_system;
use context_course;
use context_block;


/**
 * this file contains the couponpage helper
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class couponpage {

    /** @var string An internal name for this external page. Must be unique amongst ALL part_of_admin_tree objects */
    public $name;

    /** @var string The displayed name for this external page. Usually obtained through get_string(). */
    public $visiblename;

    /** @var string The external URL that we should link to when someone requests this external page. */
    public $url;

    /** @var array The role capability/permission a user must have to access this external page. */
    public $reqcapability;

    /** @var object The context in which capability/permission should be checked, default is site context. */
    public $context;

    /**
     * Fetch view url
     *
     * @param string $relurl
     * @param array $params
     * @return \moodle_url
     */
    public static function get_view_url($relurl, array $params = []) {
        global $CFG;
        $u = $CFG->wwwroot . '/blocks/coupon/view/' . ltrim($relurl, '/');
        return new \moodle_url($u, $params);
    }

    /**
     * Constructor for adding an external page into the admin tree.
     *
     * @param string $name The internal name for this external page. Must be unique amongst ALL part_of_admin_tree objects.
     * @param string $visiblename The displayed name for this external page. Usually obtained through get_string().
     * @param string $url The external URL that we should link to when someone requests this external page.
     * @param mixed $reqcapability The role capability/permission a user must have to access this external page.
     *      Defaults to 'moodle/site:config'.
     * @param stdClass $context The context the page relates to. Not sure what happens
     *      if you specify something other than system or front page. Defaults to system.
     * @param array $options -- valid options are
     *      - pagelayout
     *      - title
     *      - heading
     */
    public static function setup($name, $visiblename, $url, $reqcapability = 'moodle/site:config', $context = null, $options = []) {
        global $CFG, $PAGE, $USER, $SITE, $OUTPUT;
        $page = new static($name, $visiblename, $url, $reqcapability, $context);

        $PAGE->set_context($page->context);

        require_login(null, false);

        if (!empty($options['pagelayout'])) {
            $PAGE->set_pagelayout($options['pagelayout']);
        } else {
            $PAGE->set_pagelayout('admin');
        }

        // This eliminates our need to authenticate on the actual pages.
        if (!$page->check_access()) {
            throw new \moodle_exception('accessdenied', 'admin');
        }

        $PAGE->set_url($url);

        // Normal case.
        $adminediting = optional_param('adminedit', -1, PARAM_BOOL);
        if ($PAGE->user_allowed_editing() && $adminediting != -1) {
            $USER->editing = $adminediting;
        }

        // Set title.
        if (!empty($options['title'])) {
            $PAGE->set_title($options['title']);
        } else {
            $PAGE->set_title("$SITE->shortname");
        }
        // Set heading.
        if (!empty($options['heading'])) {
            $PAGE->set_heading($options['heading']);
        }

        return $page;
    }

    /**
     * Constructor for adding an external page into the admin tree.
     *
     * @param string $name The internal name for this external page. Must be unique amongst ALL part_of_admin_tree objects.
     * @param string $visiblename The displayed name for this external page. Usually obtained through get_string().
     * @param string $url The external URL that we should link to when someone requests this external page.
     * @param mixed $reqcapability The role capability/permission a user must have to access this external page.
     *      Defaults to 'moodle/site:config'.
     * @param stdClass $context The context the page relates to. Not sure what happens
     *      if you specify something other than system or front page. Defaults to system.
     */
    public function __construct($name, $visiblename, $url, $reqcapability='moodle/site:config', $context = null) {
        $this->name        = $name;
        $this->visiblename = $visiblename;
        $this->url         = $url;
        if (is_array($reqcapability)) {
            $this->reqcapability = $reqcapability;
        } else {
            $this->reqcapability = array($reqcapability);
        }
        $this->context = $context;
    }

    /**
     * Get the URL to view this page.
     *
     * @return moodle_url
     */
    public function get_page_url(): moodle_url {
        return new moodle_url($this->url);
    }

    /**
     * Determines if the current user has access to this page based on $this->reqcapability.
     *
     * @return bool True if user has access, false otherwise.
     */
    public function check_access() {
        $context = empty($this->context) ? context_system::instance() : $this->context;
        foreach ($this->reqcapability as $cap) {
            if (!has_capability($cap, $context)) {
                return false;
            }
        }
        return true;
    }

}
