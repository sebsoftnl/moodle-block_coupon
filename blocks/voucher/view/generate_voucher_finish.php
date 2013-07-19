<?php

/*
 * File: generate_voucher_step_four.php
 * Encoding: UTF-8
 * @package voucher
 * 
 * @Version 1.0.0
 * @Since 12-jul-2013
 * @copyright Sebsoft.nl
 * @author Menno de Ridder <menno@sebsoft.nl>
 */

require_once(dirname(__FILE__) . '/../../../config.php');
require_once $CFG->dirroot . '/blocks/voucher/class/settings.php';

$id = required_param('id', PARAM_INT);

if ($id)    //DEFAULT CHECKS
{
    if (!$instance = $DB->get_record('block_instances', array('id' => $id)))
    {
        print_error("Instance id incorrect");
    }
    $context = get_context_instance(CONTEXT_BLOCK, $instance->id);
    $courseid = get_courseid_from_context($context);

    if (!$course = $DB->get_record("course", array("id" => $courseid)))
    {
        //print_error("Course is misconfigured");
        $course = get_site();
    }

    require_login($course, true);
    //ADD course LINK
    $PAGE->navbar->add(ucfirst($course->fullname), new moodle_url('/course/view.php', array('id' => $course->id)));
}

$url = new moodle_url('/blocks/voucher/view/generate_voucher_step_four.php', array('id' => $id));
$PAGE->set_url($url);

$PAGE->set_title(get_string('view:generate_voucher:title', BLOCK_VOUCHER));
$PAGE->set_heading(get_string('view:generate_voucher:heading', BLOCK_VOUCHER));
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');

// Get rid of the session
unset($SESSION->voucher);

//make sure the moodle editmode is off
voucher_Helper::forceNoEditingMode();

if (voucher_Helper::getPermission('generatevouchers'))
{
    
    echo $OUTPUT->header();
    
    echo "<p>" . get_string('vouchers_sent', BLOCK_VOUCHER) . "</p>";
    
    echo $OUTPUT->continue_button(new moodle_url('/course/view.php', array('id' => $course->id)));
    echo $OUTPUT->footer();
}
else
{
    print_error(get_string('error:nopermission', BLOCK_VOUCHER));
}
