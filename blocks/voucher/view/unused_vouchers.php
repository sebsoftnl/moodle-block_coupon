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

$url = new moodle_url('/blocks/voucher/view/my_vouchers.php', array('id' => $id));
$PAGE->set_url($url);

$PAGE->set_title(get_string('view:generate_voucher:title', BLOCK_VOUCHER));
$PAGE->set_heading(get_string('view:generate_voucher:heading', BLOCK_VOUCHER));
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
        
        $voucherReport = new stdClass();
        
        // Fix order of columns
        $voucherReport->showname = '';
        $voucherReport->for_user = '';
        $voucherReport->senddate = '';
        $voucherReport->enrolperiod = '';
        $voucherReport->code = '';
        $voucherReport->course = '';
        $voucherReport->cohorts = '';
        $voucherReport->groups = '';
//        $voucherReport->issend = ($voucher->issend) ? get_string('yes') : get_string('no');
        
        if (!is_null($voucher->ownerid)) {
            $voucherReport->showname = trim($voucher->firstname . " " . $voucher->lastname);
            if (empty($voucherReport->showname)) $voucherReport->showname = $voucher->username;
        }
        
        // Voucher based on course
        if (!is_null($voucher->courseid)) {
            // Set course name
            $course = voucher_Db::GetCourseById($voucher->courseid);
            $voucherReport->course = $course->shortname;

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
        if (!is_null($voucher->for_user)) $voucherReport->for_user;
        
        // And add record to the report
        $reportData[] = $voucherReport;
        
    }
    
    // Build up voucher table
    $table = new html_table();
    $table->head = array(
        get_string('report:owner', BLOCK_VOUCHER),
        get_string('report:for_user', BLOCK_VOUCHER),
        get_string('report:senddate', BLOCK_VOUCHER),
        get_string('report:enrolperiod', BLOCK_VOUCHER),
        get_string('report:voucher_code', BLOCK_VOUCHER),
        get_string('course'),
        get_string('report:cohorts', BLOCK_VOUCHER),
        get_string('groups')
//        get_string('report:issend', BLOCK_VOUCHER)
    );
    
    $table->data = $reportData;
    
//$table->data = array(
//    array('Harry Potter', '76%', 'Getting better', 'bla', ' bla'),
//    array('Rincewind', '89%', 'Lucky as usual', 'bla', ' bla'),
//    array('Elminster Aumar', '100%', 'Easy when you know everything!', 'bla', ' bla')
//);
    echo html_writer::table($table);
    
//            [id] => 5
//            [userid] => 
//            [ownerid] => 2
//            [courseid] => 2
//            [submission_code] => aPHduGYdqycHDyxV
//            [timecreated] => 1375090748
//            [timemodified] => 
//            [timeexpired] => 
    
    
    echo $OUTPUT->footer();

//    require_once BLOCK_VOUCHER_CLASSROOT.'forms/generate_voucher_form.php';
//    $mform = new generate_voucher_form($url);
//    
//    if ($mform->is_cancelled())
//    {
//        unset($SESSION->voucher);
//        redirect(new moodle_url('/course/view.php', array('id' => $course->id)));
//    }
//    elseif ($data = $mform->get_data())
//    {
//        // Cache form input
//        $SESSION->voucher = new stdClass();
//        $SESSION->voucher->type = ($data->voucher_type['type'] == 0) ? 'course' : 'cohorts';
//        
//        // And redirect user to next page
//        redirect(voucher_Helper::createBlockUrl('view/generate_voucher_step_two.php', array('id' => $id)));
//    }
//    else
//    {
//        if (isset($SESSION->voucher)) unset($SESSION->voucher);
//        
//        echo $OUTPUT->header();
//        $mform->display();
//        echo $OUTPUT->footer();
//    }
}
else
{
    print_error(get_string('error:nopermission', BLOCK_VOUCHER));
}