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
 * validate coupon input
 *
 * File         input_coupon.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// Login_check is done in couponpage class.
// @codingStandardsIgnoreLine
require_once(dirname(__FILE__) . '/../../../config.php');

use block_coupon\couponpage;
use block_coupon\forms\coupon\validator;

$redirect = optional_param('redirect', null, PARAM_LOCALURL);
if (empty($redirect)) {
    $redirect = get_local_referer(false);
    if (empty($redirect)) {
        $redirect = $CFG->wwwroot . '/my';
    }
}

$title = get_string('view:input_coupon:title', 'block_coupon');
$heading = get_string('view:input_coupon:heading', 'block_coupon');

$page = couponpage::setup(
    'block_coupon_view_input_coupon',
    $title,
    couponpage::get_view_url('inputcoupons.php'),
    'block/coupon:inputcoupons',
    \context_system::instance(),
    [
        'pagelayout' => 'standard',
        'title' => $title,
        'heading' => $heading,
    ]
);

// Include the form.
try {
    $mform = new validator();
    if ($mform->is_cancelled()) {
        redirect($redirect);
    } else if ($data = $mform->get_data()) {
        // Get type processor.
        $typeproc = block_coupon\coupon\typebase::get_type_instance($data->coupon_code);
        // Perform assertions.
        $typeproc->assert_not_claimed();
        $typeproc->assert_internal_checks($USER->id);
        // Process the claim.
        // The base is to just claim, but various coupons might have their own processing.
        $typeproc->process_claim($USER->id);
    } else {
        echo $OUTPUT->header();
        echo '<div class="block-coupon-container">';
        $mform->display();
        echo '</div>';
        echo $OUTPUT->footer();
    }
} catch (block_coupon\notificationexception $ne) {
    // Add message to stack.
    $ne->notify();
} catch (block_coupon\exception $e) {
    \core\notification::error($e->getMessage());
} catch (\Exception $ex) {
    \core\notification::error(get_string('err:coupon:generic', 'block_coupon'));
}
redirect($redirect);
