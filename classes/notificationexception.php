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
 * Notification exception class implementation
 *
 * This is an exception with a notification that can be shown to the end user.
 *
 * File         notificationexception.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

namespace block_coupon;

/**
 * block_coupon\notificationexception
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notificationexception extends \moodle_exception {

    /**
     * @var string
     */
    protected $notificationtype;

    /**
     * Create a new instance of the exception
     *
     * @param string $errorcode
     * @param string $notificationtype -- see \core\notification for types.
     * @param \stdClass|null $a
     */
    public function __construct($errorcode, $notificationtype, $a = null) {
        $link = '';
        $debuginfo = null;
        parent::__construct($errorcode, 'block_coupon', $link, $a, $debuginfo);
        $this->notificationtype = $notificationtype;
    }

    /**
     * Get rendered notification message.
     *
     * @return string
     */
    public function get_rendered_notification() {
        global $OUTPUT;
        $component = new \core\output\notification($this->getMessage(), $this->notificationtype, true);
        return $OUTPUT->render($component);
    }

    /**
     * Add notification message to stack.
     */
    public function notify() {
        \core\notification::add($this->getMessage(), $this->notificationtype);
    }

}
