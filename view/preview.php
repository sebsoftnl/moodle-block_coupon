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
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/../../../config.php');

use block_coupon\helper;
use block_coupon\coupon\codegenerator;

$id = required_param('id', PARAM_INT);

$instance = $DB->get_record('block_instances', array('id' => $id), '*', MUST_EXIST);
$context       = \context_block::instance($instance->id);
$coursecontext = $context->get_course_context(false);
$course = false;
if ($coursecontext !== false) {
    $course = $DB->get_record("course", array("id" => $coursecontext->instanceid));
}
if ($course === false) {
    $course = get_site();
}

require_login($course, true);

// Make sure the moodle editmode is off.
helper::force_no_editing_mode();
require_capability('block/coupon:generatecoupons', $context);

// Prepare.
try {
    $options = block_coupon\coupon\generatoroptions::from_session();
} catch (Exception $ex) {
    $options = new block_coupon\coupon\generatoroptions();
}

$roleid = optional_param('roleid', $options->roleid, PARAM_INT);
$logoid = optional_param('logoid', $options->logoid, PARAM_INT);
$renderqrcode = optional_param('qr', $options->renderqrcode, PARAM_INT);

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

// Generate the PDF.
$pdfgen = new block_coupon\coupon\pdf(get_string('pdf:titlename', 'block_coupon'));
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
