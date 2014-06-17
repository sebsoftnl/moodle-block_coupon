<?php

/*
 * File: reports.php
 * Encoding: UTF-8
 * @package voucher
 * 
 * @Version 1.0.0
 * @Since 26-jul-2013
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

$url = new moodle_url('/blocks/voucher/view/reports.php', array('id' => $id));
$PAGE->set_url($url);

$PAGE->set_title(get_string('view:reports:title', BLOCK_VOUCHER));
$PAGE->set_heading(get_string('view:reports:heading', BLOCK_VOUCHER));
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');

//make sure the moodle editmode is off
voucher_Helper::forceNoEditingMode();

if (voucher_Helper::getPermission('viewreports'))
{

    // We're going to build the voucher_users array
    $voucher_users = array();
    
    // Build reportdata
    $reportdata = new stdClass();
    $reportdata->courses = array();
    $reportdata->users = array();

    // Select submitted vouchers
    $vouchers = voucher_Helper::GetVouchers();
    if (!$vouchers) print_error('error:no_vouchers_submitted', BLOCK_VOUCHER);
    
    // For each user we'll get a list of courses that apply for them
    foreach($vouchers as $vid=>$voucher) {
        
        // Now we get the data
        $user = $DB->get_record('user', array('id'=>$voucher->userid));
        $voucherCourses = $DB->get_records('voucher_courses', array('voucherid'=>$voucher->id));
        
        if (!isset($reportdata->users[$user->id])) $reportdata->users[$user->id] = $user;
        
        $voucher_users[$voucher->userid] = $user;
        $voucher_users[$voucher->userid]->courses = array();
        
        // If its a course voucher its simple
        if (!empty($voucherCourses)) {
            
            foreach($voucherCourses as $voucherCourse) {
                
                // Skip if its already added, could happen with multiple vouchers on 1 course
                if (in_array($voucherCourse->courseid, $voucher_users[$voucher->userid]->courses)) {
                    continue;
                }
                $voucher_users[$voucher->userid]->courses[$voucherCourse->courseid] = $DB->get_record('course', array('id'=>$voucherCourse->courseid));
                
                if (!isset($reportdata->courses[$voucherCourse->courseid])) {
                    $reportdata->courses[$voucherCourse->courseid] = $voucher_users[$voucher->userid]->courses[$voucherCourse->courseid];
                }
            }
            
        } else {
            
            // Call the cohorts belonging to this voucher
            $voucher_cohorts = $DB->get_records('voucher_cohorts', array('voucherid'=>$voucher->id));
            
            foreach($voucher_cohorts as $voucher_cohort) {
                
                // Get the courses by cohort
                $cohort_courses = voucher_Db::GetCoursesByCohort($voucher_cohort->cohortid);
                // And add a course id for each cohort_course we've found
                foreach($cohort_courses as $cohort_course) {
                    
                    if (in_array($cohort_course->id, $voucher_users[$voucher->userid]->courses)) continue;
                    $voucher_users[$voucher->userid]->courses[$cohort_course->id] = $cohort_course;
            
                    if (!isset($reportdata->courses[$cohort_course->id])) {
                        $reportdata->courses[$cohort_course->id] = $cohort_course;
                    }
                }
                
            }
            
        }
        
    }
    
    // Now we've got fully initialized voucher objects, we'll fill the reportdata
    $reportdata->userdata = array();
    foreach ($voucher_users as $uid=>$voucher_user)
    {
        $reportdata->userdata[$uid] = array();
        foreach ($voucher_user->courses as $cid=>$course)
        {
            $reportdata->userdata[$uid][$cid] = voucher_Helper::_LoadCourseCompletionInfo($voucher_user, $course);
        }
    }
    
    $reports_table = voucher_Helper::_render_html($reportdata);
    
    echo $OUTPUT->header();
    echo html_writer::table($reports_table);
    echo $OUTPUT->footer();
}
else
{
    print_error(get_string('error:nopermission', BLOCK_VOUCHER));
}