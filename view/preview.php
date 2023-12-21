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
 * display unused coupons
 *
 * File         unused_coupons.php
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

use block_coupon\coupon\codegenerator;
use block_coupon\couponpage;

$title = get_string('preview-pdf', 'block_coupon');
$heading = get_string('preview-pdf', 'block_coupon');

$page = couponpage::setup(
    'block_coupon_view_preview',
    $title,
    couponpage::get_view_url('preview.php'),
    'block/coupon:generatecoupons',
    \context_system::instance(),
    [
        'pagelayout' => 'embedded',
        'title' => $title,
        'heading' => $heading
    ]
);

// Prepare.
try {
    $options = block_coupon\coupon\generatoroptions::from_session();
} catch (Exception $ex) {
    $options = new block_coupon\coupon\generatoroptions();
}

$roleid = optional_param('roleid', $options->roleid, PARAM_INT);
$logoid = optional_param('logoid', $options->logoid, PARAM_INT);
$renderqrcode = optional_param('qr', $options->renderqrcode, PARAM_INT);
$font = optional_param('font', $options->font, PARAM_TEXT);
$templateid = optional_param('templateid', $options->templateid, PARAM_INT);

// Create fake coupon instance.
$coupon = new stdClass;
$coupon->id = 0;
$coupon->userid = null;
$coupon->ownerid = $USER->id;
$coupon->for_user_email = null;
$coupon->for_user_name = null;
$coupon->for_user_gender = null;
$coupon->enrolperiod = $options->enrolperiod;
$coupon->senddate = null;
$coupon->issend = 0;
$coupon->redirect_url = null;
$coupon->email_body = null;
$coupon->submission_code = codegenerator::generate_unique_code($options->codesize);
$coupon->logoid = $logoid;
$coupon->typ = 'course';
$coupon->claimed = 0;
$coupon->renderqrcode = $renderqrcode;
$coupon->roleid = $roleid;
$coupon->batchid = $options->batchid;
$coupon->timecreated = time();
$coupon->timemodified = time();
$coupon->timeexpired = null;
$coupon->timeclaimed = null;

error_reporting(0);
ini_set('display_errors', 0);

if (!empty($templateid)) {
    $tplrec = $DB->get_record('block_coupon_templates', ['id' => $templateid]);
    $template = new block_coupon\template($tplrec);
    $template->generate_pdf([$coupon], true);
} else {
    // Generate the PDF.
    $pdfgen = new block_coupon\coupon\pdf(get_string('pdf:titlename', 'block_coupon'));
    // Set default font.
    $pdfgen->set_defaultfont($font);
    // Fill the coupon with text.
    $pdfgen->set_templatemain(get_string('default-coupon-page-template-main', 'block_coupon'));
    $pdfgen->set_templatebotleft(get_string('default-coupon-page-template-botleft', 'block_coupon'));
    $pdfgen->set_templatebotright(get_string('default-coupon-page-template-botright', 'block_coupon'));
    // Set preview mode.
    $pdfgen->set_preview(true, $options->courses);
    // Generate it.
    $pdfgen->generate($coupon);
    // And display.
    $pdfgen->Output();
}
