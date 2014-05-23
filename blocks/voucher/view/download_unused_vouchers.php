<?php
/*
 * download_unused_vouchers.php
 * @copyright Sebsoft
 * @author Menno de Ridder :: menno@sebsoft.nl
 * @package: moodle-voucher
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

    $course = get_site();
    $context = context_course::instance($course->id);

    require_login($course, true);
}

if (voucher_Helper::getPermission('viewreports'))
{
    $vouchers = voucher_Helper::GetUnusedVouchers();
    
    $reportData = array();
    $reportHeading = array(
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
   
    // Build up voucher report array
    foreach($vouchers as $voucher) {
       
        $voucherReport = new stdClass();
       
        // Fix order of columns
        $voucherReport->showname = '';
        $voucherReport->for_user_email = '';
        $voucherReport->senddate = '';
        $voucherReport->enrolperiod = '';
        $voucherReport->code = '';
        $voucherReport->course = '';
        $voucherReport->cohorts = '';
        $voucherReport->groups = '';
        $voucherReport->issend = '';
       
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
        if (!is_null($voucher->for_user_email)) $voucherReport->for_user_email = $voucher->for_user_email;
       
        if (!is_null($voucher->issend)) {
            $voucherReport->issend = ($voucher->issend) ? get_string('yes') : get_string('no');
        }
       
        // And add record to the report
        $reportData[] = $voucherReport;
       
    }
   
    voucher_Helper::exportUnusedVouchers($reportHeading, $reportData);

}
?>

<!--<script type="text/javascript">
    window.onload = function() {
        window.close();
    }
</script>-->