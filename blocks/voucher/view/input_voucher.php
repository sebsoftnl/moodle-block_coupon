<?php

/*
 * File: input_voucher.php
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
        $course = get_site();
    }

    require_login($course, true);
    //ADD course LINK
    $PAGE->navbar->add(ucfirst($course->fullname), new moodle_url('/course/view.php', array('id' => $course->id)));
}

$url = new moodle_url('/blocks/voucher/view/input_voucher.php', array('id' => $id));
$PAGE->set_url($url);

$PAGE->set_title(get_string('view:input_voucher:title', BLOCK_VOUCHER));
$PAGE->set_heading(get_string('view:input_voucher:heading', BLOCK_VOUCHER));
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');

//make sure the moodle editmode is off
voucher_Helper::forceNoEditingMode();

if (voucher_Helper::getPermission('inputvouchers'))
{
//    exit("<pre>" . print_r($_POST, true) . "</pre>");
    
    // Include the form
    require_once BLOCK_VOUCHER_CLASSROOT.'forms/input_voucher_form.php';
    $mform = new input_voucher_form($url);
    
    if ($mform->is_cancelled())
    {
        redirect(new moodle_url('/course/view.php', array('id' => $course->id)));
    }
    elseif ($data = $mform->get_data())
    {
        
        // Because we're outside course context we've got to include groups library manually
        require_once($CFG->dirroot . '/group/lib.php');
        require_once($CFG->dirroot . '/cohort/lib.php');
        require_once($CFG->dirroot . '/enrol/cohort/locallib.php');
        
//        exit("About to input the voucher and enrol the user 'n stuff..");
        
        $role = $DB->get_record('role', array('shortname'=>'student'));
        $voucher = $DB->get_record('vouchers', array('submission_code'=>$data->voucher_code));
        
        // We'll handle voucher_cohorts
        if ($voucher->courseid === null) {
            
            $voucher_cohorts = $DB->get_records('voucher_cohorts', array('voucherid'=>$voucher->id));
            if (count($voucher_cohorts) == 0) print_error(get_string('error:missing_cohort', BLOCK_VOUCHER));
            
            // Add user to cohort
            foreach($voucher_cohorts as $voucher_cohort) {

                if (!$DB->get_record('cohort', array('id'=>$voucher_cohort->cohortid))) print_error(get_string('error:missing_cohort', BLOCK_VOUCHER));
                
                cohort_add_member($voucher_cohort->cohortid, $USER->id);
                
            }
            // Now execute the cohort sync
            $result = enrol_cohort_sync();
            // If result = 0 it went ok. (lol!)
            if ($result === 1) {
                print_error(get_string('error:cohort_sync', BLOCK_VOUCHER));
            } elseif ($result === 2) {
                print_error(get_string('error:plugin_disabled', BLOCK_VOUCHER));
            }
            
        // Otherwise we'll handle based on courses
        } else {
            
            // Important checks
            if (!$DB->get_record('course', array('id'=>$voucher->courseid))) print_error(get_string('error:missing_course', BLOCK_VOUCHER));
            
            // Make sure we only enrol if its not enrolled yet
            $context = get_context_instance(CONTEXT_COURSE, $voucher->courseid);
            if (!is_enrolled($context, $USER->id)) {
                
                // Now we can enrol
                if (!enrol_try_internal_enrol($voucher->courseid, $USER->id, $role->id)) {
                    print_error(get_string('error:unable_to_enrol', BLOCK_VOUCHER));
                }
                
            }
            
            // And add user to groups
            $voucher_groups = $DB->get_records('voucher_groups', array('voucherid'=>$voucher->id));
            if (count($voucher_groups) > 0) {
                foreach($voucher_groups as $voucher_group) {
                    
                    // Check if the group exists
                    if (!$DB->get_record('groups', array('id'=>$voucher_group->groupid))) print_error(get_string('error:missing_group', BLOCK_VOUCHER));
                    
                    // Add user if its not a member yet
                    if (!groups_is_member($voucher_group->groupid, $USER->id)) {
                        groups_add_member($voucher_group->groupid, $USER->id);
                    }
                    
                }
            }
            
        }
        
        // And finally update the voucher record
        $voucher->userid = $USER->id;
        $voucher->timemodified = time();
        $DB->update_record('vouchers', $voucher);
        
        // Redirect to my directly
//        redirect(voucher_Helper::createBlockUrl('view/input_voucher_finish.php', array('id' => $id)));
        redirect($CFG->wwwroot . '/my', get_string('success:voucher_used', BLOCK_VOUCHER));
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