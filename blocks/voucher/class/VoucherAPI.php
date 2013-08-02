<?php

final class VoucherAPI
{
    
    /**
     * Get all non-sidewide and visible courses.
     * 
     * @returnArray int $id Course ID
     * @returnArray string $fullname Course fullname
     * 
     * @return array List of courses.
     * 
     * @example <pre>
     * $url = 'http://moodle.menno.extern.ds.office.sebsoft.nl/blocks/voucher/view/api.php?method=GetCourses&resultType=xml';<br />
     * <br />
     * $params = array(<br />
     *     'username' => '{API username}',<br />
     *     'password' => '{API password}'<br />
     * );<br />
     * <br />
     * $ch = curl_init($url);<br />
     * curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);<br />
     * curl_setopt($ch, CURLOPT_USERPWD, $params['username'].':'.$params['password']);<br />
     * $result = curl_exec($ch);<br />
     * <br />
     * echo htmlspecialchars($result);</pre><br />
     */
    static final public function GetCourses() {
        return voucher_Db::GetVisibleCourses();
    }
    
    /**
     * Get all cohorts.
     * 
     * @returnArray int $id Cohort ID
     * @returnArray string $name Cohort name
     * 
     * @return array List of cohorts.
     * 
     * @example <pre>
     * $url = 'http://moodle.menno.extern.ds.office.sebsoft.nl/blocks/voucher/view/api.php?method=GetCohorts&resultType=xml';<br />
     * <br />
     * $params = array(<br />
     *     'username' => '{API username}',<br />
     *     'password' => '{API password}'<br />
     * );<br />
     * <br />
     * $ch = curl_init($url);<br />
     * curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);<br />
     * curl_setopt($ch, CURLOPT_USERPWD, $params['username'].':'.$params['password']);<br />
     * $result = curl_exec($ch);<br />
     * <br />
     * echo htmlspecialchars($result);</pre><br />
     */
    static final public function GetCohorts(){
        return voucher_Db::GetCohorts();
    }
    
    /**
     * Get all groups of the given course id.
     * 
     * @param int $courseid
     * @returnArray int $id Group ID
     * @returnArray string $name Group name
     * 
     * @return array List of groups belonging to $courseid.
     * 
     * @example <pre>
     * $url = 'http://moodle.menno.extern.ds.office.sebsoft.nl/blocks/voucher/view/api.php?method=GetCourseGroups&courseid=1&resultType=xml';<br />
     * <br />
     * $params = array(<br />
     *     'username' => '{API username}',<br />
     *     'password' => '{API password}'<br />
     * );<br />
     * <br />
     * $ch = curl_init($url);<br />
     * curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);<br />
     * curl_setopt($ch, CURLOPT_USERPWD, $params['username'].':'.$params['password']);<br />
     * $result = curl_exec($ch);<br />
     * <br />
     * echo htmlspecialchars($result);</pre><br />
     */
    static final public function GetCourseGroups($courseid){
        return voucher_Db::GetGroupsByCourseId($courseid);
    }
    
    /**
     * Generate vouchers for a course.
     * 
     * @param string $email Email address the vouchers will be sent to.
     * @param int $amount Amount of vouchers to be generated.
     * @param int $courseid ID of the course the vouchers will be generated for.
     * @param array $groups Array of IDs of all groups the users will be added to after using a Voucher.
     * @param bool $generate_single_pdfs Will generate one PDF file for each voucher if true.
     * @return boolean $result
     * 
     * @example <pre>
     * $url = 'http://moodle.menno.extern.ds.office.sebsoft.nl/blocks/voucher/view/api.php?method=GenerateVouchersForCourse&courseid=1&amount=5&email=menno@sebsoft.nl&groups[0]=1&groups[1]=2&generate_single_pdfs=1&resultType=xml';<br />
     * <br />
     * $params = array(<br />
     *     'username' => '{API username}',<br />
     *     'password' => '{API password}'<br />
     * );<br />
     * <br />
     * $ch = curl_init($url);<br />
     * curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);<br />
     * curl_setopt($ch, CURLOPT_USERPWD, $params['username'].':'.$params['password']);<br />
     * $result = curl_exec($ch);<br />
     * <br />
     * echo htmlspecialchars($result);</pre><br />
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
            return false;
        } else {
            voucher_Helper::MailVouchers($vouchers, $email, $generate_single_pdfs);
        }
        
        return true;
    }
    
    /**
     * Generate vouchers for one or multiple cohorts.
     * 
     * @param string $email Email address the vouchers will be sent to.
     * @param int $amount Amount of vouchers to be generated.
     * @param array $cohorts Array of IDs of the cohorts the vouchers will be generated for.
     * @param bool $generate_single_pdfs Will generate one PDF file for each voucher if true.
     * @return boolean $result
     * 
     * @example <pre>
     * $url = 'http://moodle.menno.extern.ds.office.sebsoft.nl/blocks/voucher/view/api.php?method=GenerateVouchersForCohorts&amount=5&email=menno@sebsoft.nl&cohorts[0]=1&cohorts[1]=2&resultType=xml';<br />
     * <br />
     * $params = array(<br />
     *     'username' => '{API username}',<br />
     *     'password' => '{API password}'<br />
     * );<br />
     * 
     * $ch = curl_init($url);<br />
     * curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);<br />
     * curl_setopt($ch, CURLOPT_USERPWD, $params['username'].':'.$params['password']);<br />
     * $result = curl_exec($ch);<br />
     * <br />
     * echo htmlspecialchars($result);</pre><br />
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