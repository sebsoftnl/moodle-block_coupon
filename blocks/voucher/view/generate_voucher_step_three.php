<?php

/*
 * File: generate_voucher_step_three.php
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

$url = new moodle_url('/blocks/voucher/view/generate_voucher_step_three.php', array('id' => $id));
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
        
        require_once BLOCK_VOUCHER_CLASSROOT.'forms/generate_voucher_groups_form.php';
        $mform = new generate_voucher_groups_form($url);

    } else {

        require_once BLOCK_VOUCHER_CLASSROOT.'forms/generate_voucher_cohortcourses_form.php';
        $mform = new generate_voucher_cohortcourses_form($url);
        
    }
    
    if ($mform->is_cancelled())
    {
        unset($SESSION->voucher);
        redirect(new moodle_url('/course/view.php', array('id' => $course->id)));
    }
    elseif ($data = $mform->get_data())
    {

        // Save param, its only about course or cohorts
//        $SESSION->voucher->{$SESSION->voucher->type} = $data->{$SESSION->voucher->type};
        if ($SESSION->voucher->type == 'course') {
            
            // Add selected groups to session
            if (isset($data->voucher_groups)) $SESSION->voucher->groups = $data->voucher_groups;
        } else {
            
            // Check if a course is selected
            if (isset($data->connect_courses)) {

                // Get required records
                $enrol = enrol_get_plugin('cohort');
                $role = $DB->get_record('role', array('shortname'=>'student'));
                
                // Loop over all cohorts
                foreach($data->connect_courses as $cohort_id => $courses) {
                    
                    // Loop over all courses selected for this cohort
                    foreach($courses as $course_id) {
                        
                        // And enroll the shizzle
                        $course = $DB->get_record('course', array('id'=>$course_id));
                        $enrol->add_instance($course, array('customint1'=>$cohort_id, 'roleid'=>$role->id));
                        
                    }
                    
                }
            }
        }
        redirect(voucher_Helper::createBlockUrl('view/generate_voucher_step_four.php', array('id'=>$id)));
    }
    else
    {
//        if (isset($SESSION->voucher_type)) unset($SESSION->voucher_type);
        
        echo $OUTPUT->header();
        $mform->display();
        echo $OUTPUT->footer();
    }
}
else
{
    print_error(get_string('error:nopermission', BLOCK_VOUCHER));
}
