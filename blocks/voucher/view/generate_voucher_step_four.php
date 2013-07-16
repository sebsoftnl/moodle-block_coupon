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
//                exit("<pre>" . print_r($SESSION->voucher, true) . "</pre>");

        // Set last settings
        $SESSION->voucher->amount = $data->voucher_amount;
        $SESSION->voucher->email = $data->voucher_email;
        $SESSION->voucher->generate_pdf = (isset($data->generate_pdf) && $data->generate_pdf) ? true : false;
        
        // Now we've got everything, lets create dat data
        $voucher = new stdClass();
        $voucher->userid = null;
        $voucher->ownerid = $USER->id;
        $voucher->courseid = (isset($SESSION->voucher->course)) ? $SESSION->voucher->course : false;
        $voucher->amount = $SESSION->voucher->amount;
        $voucher->timecreated = time();
        $voucher->timeexpired = null;
        
        // Create vouchers
        for($i = 0; $i < $SESSION->voucher->amount; $i++) {

            // Generate a unique code
            $voucher->submission_code = voucher_Helper::get_submission_code();
            
            // And insert the record
            $voucher_id = $DB->insert_record('vouchers', $voucher);

            // In case this is a Cohort Voucher
            if ($SESSION->voucher->type == 'cohorts') {

                // Now create cohorts
                foreach($SESSION->voucher->cohorts as $cohort_id) {

                    // Build up the class
                    $voucher_cohort = new stdClass();
                    $voucher_cohort->voucher_id = $voucher_id;
                    $voucher_cohort->cohort_id = $cohort_id;

                    // And insert in db
                    $DB->insert_record('voucher_cohorts', $voucher_cohort);
                }
                
            // Otherwise it must be a course voucher
            } else {
                
                // Insert the course of the voucher
//                $voucher_course = new stdClass();
//                $voucher_course->voucher_id = $voucher_id;
//                $voucher_course->course_id = $SESSION->voucher->course;
//                
//                $DB->insert_record('voucher_courses', $voucher_course);
                // if we have groups set
                if (isset($SESSION->voucher->groups)) {
                    
                    // Loop through the groups and insert the record
                    foreach($SESSION->voucher->groups as $group_id) {

                        $voucher_group = new stdClass();
                        $voucher_group->voucher_id = $voucher_id;
                        $voucher_group->group_id = $group_id;

                        $DB->insert_record('voucher_groups', $voucher_group);
                    }
                }
                
            }
            
            // Mail
            // Generate PDF
        }
        
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
