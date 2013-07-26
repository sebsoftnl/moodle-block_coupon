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
    
    // Select all voucher users by owner id
    $vouchers = $DB->get_records('vouchers', array('userid'=>'NOT NULL', 'ownerid'=>$USER->id));
    
    // For each user we'll get a list of courses that apply for them
    foreach($vouchers as $vid=>$voucher) {
        
        // Now we get the user
        $user = $DB->get_record('user', array('id'=>$voucher->userid));
        
        $voucher_users[$voucher->userid] = $user;
        $voucher_users[$voucher->userid]->courseids = array();
        
        // If its a course voucher its simple
        if ($voucher->courseid !== NULL) {
            $voucher_users[$voucher->userid]->courseids[] = $voucher->courseid;
        } else {
            
            // Call the cohorts belonging to this voucher
            $voucher_cohorts = $DB->get_records('voucher_cohorts', array('voucherid'=>$voucher->id));
            
            foreach($voucher_cohorts as $voucher_cohort) {
                
                // Get the courses by cohort
                $cohort_courses = voucher_Helper::get_courses_by_cohort($voucher_cohort->cohortid);
                // And add a course id for each cohort_course we've found
                foreach($cohort_courses as $cohort_course) {
                    $voucher_users[$voucher->userid]->courseids[] = $cohort_course->id;
                }
                
            }
            
        }
        
    }
    
    exit("<pre>" . print_r($vouchers, true) . "</pre>");
    
    
    $reportdata = new stdClass();
    $reportdata->courses = $DB->get_records_sql("SELECT * FROM {$CFG->prefix}course");
    $reportdata->users = $DB->get_records_sql("SELECT * FROM {$CFG->prefix}user");
    $reportdata->userdata = array();

    foreach($reportdata->users as $uid=>$user) {
        $user_courseids = array(1, 2, 3, 4);
        $reportdata->users[$uid]->courseids = $user_courseids;
    }

    foreach ($reportdata->users as $uid=>$user)
    {
        $reportdata->userdata[$uid] = array();
        foreach ($user->courseids as $courseid)
        {
            $reportdata->userdata[$uid][$courseid] = self::_LoadMyCourseInfo($user, $reportdata->courses[$courseid]);
        }
    }

    


//    redirect($CFG->wwwroot . '/my', get_string('success:voucher_used', BLOCK_VOUCHER));
    echo $OUTPUT->header();
    echo $reports_table;
    echo $OUTPUT->footer();
}
else
{
    print_error(get_string('error:nopermission', BLOCK_VOUCHER));
}