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
        redirect(new moodle_url('/course/view.php', array('id' => $course->id)));
    }
    elseif ($data = $mform->get_data())
    {
        // Include the voucher generator
        require_once(BLOCK_VOUCHER_CLASSROOT . 'VoucherGenerator.php');
        
        // Save last settings in sessions
        $SESSION->voucher->amount = $data->voucher_amount;
        $SESSION->voucher->email = $data->voucher_email;
        $SESSION->voucher->generate_pdf = (isset($data->generate_pdf) && $data->generate_pdf) ? true : false;

        // Set up array to put each voucher in
        $vouchers = array();
        
        // Get max length for the voucher code
        if (!$voucher_code_length = get_config('block/' . BLOCK_VOUCHER, 'default_email')) $voucher_code_length = null;
        
        // Build individual voucher objects
        for($i = 0; $i < $SESSION->voucher->amount; $i++) {
            
            // Now we've got everything, lets create dat data
            $voucher = new stdClass();
            $voucher->userid = null;
            $voucher->ownerid = $USER->id;
            $voucher->courseid = ($SESSION->voucher->type == 'course') ? $SESSION->voucher->course : null;
            $voucher->amount = $SESSION->voucher->amount;
            $voucher->timecreated = time();
            $voucher->timeexpired = null;
            $voucher->submission_code = VoucherGenerator::GenerateUniqueCode($voucher_code_length);
            
//            // And insert the record
//            $voucher_id = $DB->insert_record('vouchers', $voucher);

            // Now add the cohorts to the voucher
            // In case this is a Cohort Voucher
            if ($SESSION->voucher->type == 'cohorts') {

                // Make it into an array
                $voucher->cohorts = array();
                
                // Now create cohorts
                foreach($SESSION->voucher->cohorts as $cohort_id) {

                    // Build up the class
                    $voucher_cohort = new stdClass();
//                    $voucher_cohort->voucherid = $voucher_id;
                    $voucher_cohort->cohortid = $cohort_id;

//                    // And insert in db
//                    $DB->insert_record('voucher_cohorts', $voucher_cohort);
                    $voucher->cohorts[] = $voucher_cohort;
                }
                
            // Otherwise we'll add groups if they are selected
            } elseif (isset($SESSION->voucher->groups)) {
                
                $voucher->groups = array();
//                // Insert the course of the voucher
//                $voucher_course = new stdClass();
//                $voucher_course->voucher_id = $voucher_id;
//                $voucher_course->course_id = $SESSION->voucher->course;
//                
//                $DB->insert_record('voucher_courses', $voucher_course);
                
                // if we have groups set
                    
                    // Loop through the groups and insert the record
                foreach($SESSION->voucher->groups as $group_id) {

                    $voucher_group = new stdClass();
//                        $voucher_group->voucherid = $voucher_id;
                    $voucher_group->groupid = $group_id;

//                  $DB->insert_record('voucher_groups', $voucher_group);
                }
//                }
                
            }
        }
        
        // Now we've got a fully initiated vouchers array
//        voucher_Helper::GenerateVouchers($vouchers);
        voucher_Helper::MailVouchers($vouchers);

        
        
//        $send_to = $DB->get_record('user', array('email'=>get_config('block/' . BLOCK_VOUCHER, 'default_email')));
        
        // Build an object of parameters we'll need in the body of the email
        // This so we can use the get_string method properly
//        $message_params = new stdClass();
//        $message_params->user_fullname = $USER->firstname . ' ' . $USER->lastname;
////        $message_params->user_fullname = $send_to->firstname . ' ' . $send_to->lastname;
//        $message_params->voucher_amount = $SESSION->voucher->amount;
//        $message_params->voucher_type = get_string($SESSION->voucher->type, BLOCK_VOUCHER);
//        $message_params->voucher_owner = $USER->firstname . ' ' . $USER->lastname;
//        $message_params->salutation = $CFC->noreplyaddress;
//        $message_params->generate_pdf = ($SESSION->voucher->generate_pdf) ? get_string('generate_pdf', BLOCK_VOUCHER) : '';
//
//        // The param holding the course/group names or cohort names
//        $message_params->voucher_subscribes = '';
//        
//        // If we're creating course vouchers
//        if ($SESSION->voucher->type == 'course') {
//            
//            $course = $DB->get_record('course', array('id'=>$SESSION->voucher->course));
//            $message_params->voucher_subscribes = $course->fullname . '<br />';
//            
//            // If groups are set
//            if (isset($SESSION->voucher->groups)) {
//                
//                $message_params->voucher_subscribes .= '<i>Groups</i><br />';
//                $groups = $DB->get_records_sql("SELECT * FROM {$CFG->prefix}groups WHERE id IN (" . join($SESSION->voucher->groups, ',') . ")");
//                foreach($groups as $group) {
//                    $message_params->voucher_subscribes .= $group->name . '<br />';
//                }
//                
//            }
//            
//        // Else we're trying to create cohort vouchers
//        } else {
//            
//            $cohorts = $DB->get_records_sql("SELECT * FROM {$CFG->prefix}cohort WHERE id IN (" . join($SESSION->voucher->cohorts, ',') . ")");
//            foreach($cohorts as $cohort) {
//                $message_params->voucher_subscribes .= $cohort->name . '<br />';
//            }
//        }
//        
//        $message = get_string('mail:body:voucher_generated', BLOCK_VOUCHER, $message_params);
//        $subject = get_string('mail:subject:voucher_generated', BLOCK_VOUCHER);
//        
//        exit("<pre>" . print_r($message, true) . "</pre>");
//
//        email_to_user($USER, $USER, $subject, $message);

        redirect(voucher_Helper::createBlockUrl('view/generate_voucher_finish.php', array('id'=>$id)));
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
