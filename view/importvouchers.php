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
 * index
 *
 * File         index.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// Login_check is done in couponpage class.
// @codingStandardsIgnoreLine
require_once(dirname(__FILE__) . '/../../../config.php');
use block_coupon\couponpage;

$title = 'Import * from block_voucher';
$page = couponpage::setup(
    'block_coupon_view_index',
    $title,
    couponpage::get_view_url('index.php'),
    'block/coupon:administration',
    \context_system::instance(),
    [
        'pagelayout' => 'report',
        'title' => $title
    ]
);

$dorun = optional_param('exec', 0, PARAM_BOOL);
if ($dorun) {
    block_coupon\tool\voucherimporter::process();
}

echo $OUTPUT->header();
$url = new \moodle_url($PAGE->url, ['exec' => 1]);
echo \html_writer::div(get_string('import:voucher:desc', 'block_coupon'), 'alert alert-info');
echo $OUTPUT->single_button($url, get_string('import:voucher:confirm', 'block_coupon'));
echo $OUTPUT->footer();
