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
// You should have received a copy of the GNU General Public License`
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Download generated coupons
 *
 * File         download.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');

use block_coupon\helper;

error_reporting(0);
ini_set('display_errors', 0);
unset($CFG->debugusers, $CFG->debugdisplay);
$CFG->debug = 0;

$batchid = required_param('bid', PARAM_TEXT); // Changed to TEXT because that's how the form defines the param type!
$timeid = required_param('t', PARAM_ALPHANUM);
$dodl = optional_param('dl', false, PARAM_BOOL);

require_login();

$title = 'view:download:title';
$heading = 'view:download:heading';

$PAGE->navbar->add(get_string($title, 'block_coupon'));

$params = ['id' => $zipid];
$url = new moodle_url($CFG->wwwroot . '/blocks/coupon/download.php', $params);
$PAGE->set_url($url);

$PAGE->set_title(get_string($title, 'block_coupon'));
$PAGE->set_heading(get_string($heading, 'block_coupon'));
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');

try {
    if ((bool) $dodl) {
        helper::download($batchid, $timeid, true);
    } else {
        echo $OUTPUT->header();
        helper::download($batchid, $timeid);
        echo $OUTPUT->footer();
    }
} catch (block_coupon\exception $e) {
    if (!$OUTPUT->has_started()) {
        echo $OUTPUT->header();
    }
    \core\notification::error($e->getMessage());
    echo $OUTPUT->footer();
}
