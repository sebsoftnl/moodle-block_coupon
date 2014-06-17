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

// Make sure the voucher object is set in cache
if (!isset($SESSION->voucher)) print_error(get_string('error:nopermission', BLOCK_VOUCHER));

$url = new moodle_url('/blocks/voucher/view/generate_voucher_step_four.php', array('id' => $id));
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
    
    // Depending on our data we'll get the right form
    if ($SESSION->voucher->type == 'course') {
        
        require_once BLOCK_VOUCHER_CLASSROOT.'forms/generate_confirm_course_form.php';
        $mform = new generate_confirm_course_form($url);

    } else {

        require_once BLOCK_VOUCHER_CLASSROOT.'forms/generate_confirm_cohorts_form.php';
        $mform = new generate_confirm_cohorts_form($url);
        
    }
    
    if ($mform->is_cancelled())
    {
        unset($SESSION->voucher);
        redirect(new moodle_url('/course/view.php', array('id' => $course->id)));
    }
    elseif ($data = $mform->get_data())
    {
        
        // These settings are always the same
        $SESSION->voucher->showform = $data->showform;
        $SESSION->voucher->redirect_url = (empty($data->redirect_url)) ? null : $data->redirect_url;
        $SESSION->voucher->enrolperiod = (empty($data->enrolment_period)) ? null : $data->enrolment_period;
        
        // If we're generating based on csv we'll redirect first to confirm the csv input
        if ($data->showform == 'csv') {
            
            $SESSION->voucher->date_send_vouchers = $data->date_send_vouchers;
            $SESSION->voucher->csv_content = $mform->get_file_content('voucher_recipients');
            $SESSION->voucher->email_body = $data->email_body['text'];
            // To the extra step
            redirect(voucher_Helper::createBlockUrl('view/generate_voucher_step_five.php', array('id'=>$id)));
        }
        
        // Otherwise we'll be generating vouchers right away
        require_once(BLOCK_VOUCHER_CLASSROOT . 'VoucherGenerator.php');
        $voucher_code_length = get_config('voucher', 'voucher_code_length');
        if (!$voucher_code_length) {
             $voucher_code_length = 16;
        }
        
        // If we're generating based on manual csv input
        if ($data->showform == 'manual') {
            $SESSION->voucher->date_send_vouchers = $data->date_send_vouchers_manual;
            $SESSION->voucher->email_body = $data->email_body_manual['text'];
            // We'll get users right away
            $recipients = voucher_Helper::GetRecipientsFromCsv($data->voucher_recipients_manual);
            
            $amountOfVouchers = count($recipients);
        }
        
        // If we're generating based on 'amount' of vouchers
        if ($data->showform == 'amount') {
            // Save last settings in sessions
            $amountOfVouchers = $data->voucher_amount;
            $SESSION->voucher->email_to = (isset($data->use_alternative_email) && $data->use_alternative_email) ? $data->alternative_email : $USER->email;
            $SESSION->voucher->generate_single_pdfs = (isset($data->generate_pdf) && $data->generate_pdf) ? true : false;
        }
        
        // Now that we've got all information we'll create the voucher objects
        $vouchers = array();
        for($i = 0; $i < $amountOfVouchers; $i++) {
            
            $voucher = new stdClass();
            $voucher->ownerid = $USER->id;
            $voucher->courses = ($SESSION->voucher->type == 'course') ? $SESSION->voucher->courses : null;
            $voucher->redirect_url = $SESSION->voucher->redirect_url;
            $voucher->enrolperiod = $SESSION->voucher->enrolperiod;
            $voucher->issend = ($data->showform == 'amount') ? 1 : 0;
            $voucher->single_pdf = ($data->showform == 'amount') ? $SESSION->voucher->generate_single_pdfs : null;
            $voucher->submission_code = VoucherGenerator::GenerateUniqueCode($voucher_code_length);
            
            if ($data->showform == 'manual') {
                
                $recipient = $recipients[$i];
                
                $voucher->senddate = $SESSION->voucher->date_send_vouchers;
                $voucher->for_user_email = $recipient->email;
                $voucher->for_user_name = $recipient->name;
                $voucher->for_user_gender = $recipient->gender;
                $voucher->redirect_url = $SESSION->voucher->redirect_url;
                $voucher->enrolperiod = $SESSION->voucher->enrolperiod;
                $voucher->email_body = $SESSION->voucher->email_body;
            }
            
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
        
        if ($data->showform == 'amount') {
            // Stuur maar gewoon gelijk...
            exit("<pre>" . print_r("Done?", true) . "</pre>");
            voucher_Helper::MailVouchers($vouchers, $SESSION->voucher->email_to, $SESSION->voucher->generate_single_pdfs);
            unset($SESSION->voucher);
            redirect($CFG->wwwroot . '/my', get_string('vouchers_sent', BLOCK_VOUCHER));
        } else {
            redirect($CFG->wwwroot . '/my', get_string('vouchers_ready_to_send', BLOCK_VOUCHER));
        }
        
    }
    else
    {
        
        echo $OUTPUT->header();
        $mform->display();
        echo $OUTPUT->footer();
    }
}
else
{
    print_error(get_string('error:nopermission', BLOCK_VOUCHER));
}
