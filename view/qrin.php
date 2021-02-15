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
 * QR inbound processor
 *
 * File         qrin.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../../config.php');

$code = required_param('c', PARAM_ALPHANUMEXT);
$hash = required_param('h', PARAM_ALPHANUMEXT);

// And process the coupon code.
try {
    $data = $DB->get_record('block_coupon', array('submission_code' => $code), '*', IGNORE_MISSING);
    if (empty($data->id)) {
        throw new block_coupon\exception('error:invalid_coupon_code');
    } else if ($hash !== sha1($data->id . $data->ownerid . $data->submission_code)) {
        throw new block_coupon\exception('error:invalid_coupon_code');
    } else if (!empty($data->userid)) {
        throw new block_coupon\exception('error:coupon_already_used');
    } else {
        if (!isloggedin()) {
            // Redirect to signup with coupon code.
            $params = array('submissioncode' => $data->submission_code);
            $couponsignup = new \moodle_url($CFG->wwwroot . '/blocks/coupon/view/signup.php', $params);
            redirect($couponsignup);
            exit; // Never reached.
        }
        require_login(null, false);
        // Get type processor.
        $typeproc = block_coupon\coupon\typebase::get_type_instance($data->submission_code);
        // Perform assertions.
        $typeproc->assert_not_claimed();
        $typeproc->assert_internal_checks($USER->id);
        // Process the claim.
        // The base is to just claim, but various coupons might have their own processing.
        $typeproc->process_claim($USER->id);
    }
} catch (block_coupon\exception $e) {
    \core\notification::error($e->getMessage());
} catch (\Exception $ex) {
    \core\notification::error(get_string('err:coupon:generic', 'block_coupon'));
}
redirect($CFG->wwwroot . '/my');
