<?php

final class VoucherAPI
{
    
    /**
     * Get all non-sidewide and visible courses.
     * 
     * @return array $courses
     */
    static final public function GetCourses() {
        return voucher_Db::GetVisibleCourses();
    }
    
    /**
     * Get all cohorts.
     * 
     * @param int $courseid
     * @return array $groups
     */
    static final public function GetCohorts(){
        return voucher_Db::GetCohorts();
    }
    
    /**
     * Get all groups of the given course id.
     * 
     * @param int $courseid
     * @return array $groups
     */
    static final public function GetCourseGroups($courseid){
        return voucher_Db::GetGroupsByCourseId($courseid);
    }
    
    /**
     * Generate vouchers for a course.
     * 
     * @param string $email
     * @param int $amount
     * @param int $courseid
     * @param array $groups
     * @param bool $generate_single_pdfs
     * @return boolean $result
     */
    static final public function GenerateVouchersForCourse($email, $amount, $courseid, $groups = false, $generate_single_pdfs = false){
        global $CFG;
        
        require_once($CFG->dirroot . '/blocks/voucher/class/VoucherGenerator.php');
        
        // Get max length for the voucher code
        if (!$voucher_code_length = get_config('voucher', 'voucher_code_length')) $voucher_code_length = 16;
        
        // Now that we've got all information we'll create the voucher objects
        $vouchers = array();
        for($i = 0; $i < $amount; $i++) {
            
            $voucher = new stdClass();
            $voucher->ownerid = null;
            $voucher->courseid = $courseid;
            $voucher->amount = $amount;
            $voucher->submission_code = VoucherGenerator::GenerateUniqueCode($voucher_code_length);
            
            if ($groups) {
                
                $voucher->groups = array();
                foreach($groups as $groupid) {
                    // Build groups object
                    $group = new stdClass();
                    $group->groupid = $groupid;
                    $voucher->groups[] = $group;
                }
                
            }
            
            $vouchers[] = $voucher;
        }

        $result = voucher_Helper::GenerateVouchers($vouchers);

        if ($result !== true) {
            return $result;
        } else {
            voucher_Helper::MailVouchers($vouchers, $email, $generate_single_pdfs);
        }
        
        return true;
    }
    
    /**
     * Generate vouchers for one or multiple cohorts.
     * 
     * @param string $email
     * @param int $amount
     * @param array $cohorts
     * @param bool $generate_single_pdfs
     * @return boolean $result
     */
    static final public function GenerateVouchersForCohorts($email, $amount, $cohorts, $generate_single_pdfs = false){
        global $CFG;
        
        require_once($CFG->dirroot . '/blocks/voucher/class/VoucherGenerator.php');

        // Get max length for the voucher code
        if (!$voucher_code_length = get_config('voucher', 'voucher_code_length')) $voucher_code_length = 16;
        
        // Now that we've got all information we'll create the voucher objects
        $vouchers = array();
        for($i = 0; $i < $amount; $i++) {
            
            $voucher = new stdClass();
            $voucher->ownerid = null;
            $voucher->courseid = null;
            $voucher->amount = $amount;
            $voucher->submission_code = VoucherGenerator::GenerateUniqueCode($voucher_code_length);
            
            $voucher->cohorts = array();
            foreach($cohorts as $cohortid) {
                // Build cohort object
                $cohort = new stdClass();
                $cohort->cohortid = $cohortid;
                $voucher->cohorts[] = $cohort;
            }
            
            $vouchers[] = $voucher;
        }

        $result = voucher_Helper::GenerateVouchers($vouchers);

        if ($result !== true) {
            return $result;
        } else {
            voucher_Helper::MailVouchers($vouchers, $email, $generate_single_pdfs);
        }
        
        return true;

    }
    
//    static final public function GenerateReport($ownerid=0){}
}
?>