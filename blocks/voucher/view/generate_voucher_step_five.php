<?php

/*
 * File: generate_voucher_step_five.php
 * Encoding: UTF-8
 * @package voucher
 * 
 * @Version 1.0.0
 * @Since 11-jul-2013
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

$url = new moodle_url('/blocks/voucher/view/generate_voucher_step_five.php', array('id' => $id));
$PAGE->set_url($url);

$PAGE->set_title(get_string('view:generate_voucher:title', BLOCK_VOUCHER));
$PAGE->set_heading(get_string('view:generate_voucher:heading', BLOCK_VOUCHER));
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');

//make sure the moodle editmode is off
voucher_Helper::forceNoEditingMode();

if (voucher_Helper::getPermission('generatevouchers'))
{
    
    // Make sure sessions are still alive
    if (!isset($SESSION->voucher)) {
        print_error("error:sessions-expired", BLOCK_VOUCHER);
    }
    
    require_once BLOCK_VOUCHER_CLASSROOT.'forms/generate_voucher_extra_form.php';
    $mform = new generate_voucher_extra_form($url);
    
    if ($mform->is_cancelled())
    {
        unset($SESSION->voucher);
        redirect(new moodle_url('/course/view.php', array('id' => $course->id)));
    }
    elseif ($data = $mform->get_data())
    {
        // Include the voucher generator
        require_once(BLOCK_VOUCHER_CLASSROOT . 'VoucherGenerator.php');
        // Get recipients
        $recipients = voucher_Helper::GetRecipientsFromCsv($data->voucher_recipients);
        
        // Get max length for the voucher code
        if (!$voucher_code_length = get_config('voucher', 'voucher_code_length')) $voucher_code_length = 16;

        // Now that we've got all information we'll create the voucher objects
        $vouchers = array();
        foreach($recipients as $recipient) {
            
//            $moodle_user = voucher_Db::GetUser((array)$recipient);
            
            $voucher = new stdClass();
            $voucher->ownerid = $USER->id;
            $voucher->courseid = ($SESSION->voucher->type == 'course') ? $SESSION->voucher->course : null;
            $voucher->submission_code = VoucherGenerator::GenerateUniqueCode($voucher_code_length);
            
            // Extra fields
            $voucher->senddate = $SESSION->voucher->date_send_vouchers;
            $voucher->for_user_email = $recipient->email;
            $voucher->for_user_name = $recipient->name;
            $voucher->redirect_url = $SESSION->voucher->redirect_url;
            $voucher->enrolperiod = $SESSION->voucher->enrolperiod;
            $voucher->email_body = $SESSION->voucher->email_body;
            
            if ($SESSION->voucher->type == 'cohorts') {
                
                $voucher->cohorts = array();
                foreach($SESSION->voucher->cohorts as $cohort_id) {
                    // Build cohort object
                    $voucher_cohort = new stdClass();
                    $voucher_cohort->cohortid = $cohort_id;
                    $voucher->cohorts[] = $voucher_cohort;
                }
            // Otherwise we'll add groups if they are selected
            } elseif (isset($SESSION->voucher->groups)) {
                
                $voucher->groups = array();
                foreach($SESSION->voucher->groups as $group_id) {
                    // Build groups object
                    $voucher_group = new stdClass();
                    $voucher_group->groupid = $group_id;
                    $voucher->groups[] = $voucher_group;
                }
            }
            $vouchers[] = $voucher;
        }
        
        // Now that we've got all the vouchers
        $result = voucher_Helper::GenerateVouchers($vouchers);

        if ($result !== true) {
            // Means we've got an error
            // Don't know yet what we're gonne do in this situation. Maybe mail to supportuser?
            echo "<p>An error occured while trying to generate the vouchers. Please contact support.</p>";
            echo "<pre>" . print_r($result, true) . "</pre>";
            die();
        }
        
        // Finish
        unset($SESSION->voucher);
        redirect($CFG->wwwroot . '/my', get_string('vouchers_ready_to_send', BLOCK_VOUCHER));
    } else {
        echo $OUTPUT->header();
        $mform->display();
        echo $OUTPUT->footer();
    }
}
else
{
    print_error(get_string('error:nopermission', BLOCK_VOUCHER));
}