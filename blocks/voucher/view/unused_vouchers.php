<?php

/*
 * File: my_vouchers.php
 * Encoding: UTF-8
 * @package voucher
 * 
 * @Version 1.0.0
 * @Since 19-jul-2013
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

$url = new moodle_url('/blocks/voucher/view/unused_vouchers.php', array('id' => $id));
$PAGE->set_url($url);

$PAGE->set_title(get_string('view:reports-unused:title', BLOCK_VOUCHER));
$PAGE->set_heading(get_string('view:reports-unused:heading', BLOCK_VOUCHER));
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');

//make sure the moodle editmode is off
voucher_Helper::forceNoEditingMode();

if (voucher_Helper::getPermission('viewreports'))
{

    echo $OUTPUT->header();
    
    $vouchers = voucher_Helper::GetUnusedVouchers();
    
    $reportData = array();
    // Build up voucher report array
    foreach($vouchers as $voucher) {
        
        $voucherCourses = $DB->get_records('voucher_courses', array('voucherid'=>$voucher->id));
        $voucherReport = new stdClass();
        
        // Fix order of columns
        $voucherReport->showname = '';
        $voucherReport->for_user_email = '';
        $voucherReport->senddate = '';
        $voucherReport->enrolperiod = '';
        $voucherReport->code = '';
        $voucherReport->courses = '';
        $voucherReport->cohorts = '';
        $voucherReport->groups = '';
        $voucherReport->issend = '';
        
        if (!is_null($voucher->ownerid)) {
            $voucherReport->showname = trim($voucher->firstname . " " . $voucher->lastname);
            if (empty($voucherReport->showname)) $voucherReport->showname = $voucher->username;
        }
        
        // Vouchers based on course
        if (!empty($voucherCourses)) {
            
            foreach($voucherCourses as $voucherCourse) {
                
                $course = voucher_Db::GetCourseById($voucherCourse->courseid);
                
                $voucherReport->courses .= $course->fullname;
                if ($course->id != end($voucherCourses)->courseid) {
                    $voucherReport->courses .= ', ';
                }
            }

        // Voucher based on cohort
        } else {
            
            // Set cohorts name
            if ($voucherCohorts = voucher_Db::GetVoucherCohorts($voucher->id)) {
                
                foreach($voucherCohorts as $cohort) {
                    $voucherReport->cohorts .= $cohort->name . "<br />";
                }
                
            }
        }
        
        // Set groups
        if ($voucherGroups = voucher_Db::GetVoucherGroups($voucher->id)) {

            foreach($voucherGroups as $group) {
                $voucherReport->groups .= $group->name . "<br />";
            }

        }
        
        // Last, some other useful info
        $voucherReport->code = $voucher->submission_code;
        $voucherReport->senddate = (!is_null($voucher->senddate)) ? date("d-m-Y", $voucher->senddate) : get_string('report:immediately', BLOCK_VOUCHER);
        $voucherReport->enrolperiod = (!is_null($voucher->enrolperiod)) ? $voucher->enrolperiod : '';
        if (!is_null($voucher->for_user_email)) $voucherReport->for_user_email = $voucher->for_user_email;
        
        if (!is_null($voucher->issend)) {
            $voucherReport->issend = ($voucher->issend) ? get_string('yes') : get_string('no');
        }
        
        // And add record to the report
        $reportData[] = $voucherReport;
        
    }
    
    // Build up voucher table
    $table = new html_table();
    $table->head = array(
        get_string('report:owner', BLOCK_VOUCHER),
        get_string('report:for_user_email', BLOCK_VOUCHER),
        get_string('report:senddate', BLOCK_VOUCHER),
        get_string('report:enrolperiod', BLOCK_VOUCHER),
        get_string('report:voucher_code', BLOCK_VOUCHER),
        get_string('course'),
        get_string('report:cohorts', BLOCK_VOUCHER),
        get_string('groups'),
        get_string('report:issend', BLOCK_VOUCHER)
    );
    
    $table->data = $reportData;
    
    echo html_writer::table($table);
    
    $url = voucher_Helper::createBlockUrl('view/download_unused_vouchers.php', array('id'=>$id));
    echo html_writer::link($url, get_string('report:download-excel', BLOCK_VOUCHER), array('target'=>'_blank'));
    
    echo $OUTPUT->footer();
    
}
else
{
    print_error(get_string('error:nopermission', BLOCK_VOUCHER));
}