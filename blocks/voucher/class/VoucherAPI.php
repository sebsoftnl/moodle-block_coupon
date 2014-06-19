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
     * @param array $courses Array of IDs of the courses the vouchers will be generated for.
     * @param array $groups Array of IDs of all groups the users will be added to after using a Voucher.
     * @param bool $generate_single_pdfs Will generate one PDF file for each voucher if true.
     * @return boolean $result
     * 
     * @example <pre>
     * $url = 'http://moodle.menno.extern.ds.office.sebsoft.nl/blocks/voucher/view/api.php?method=GenerateVouchersForCourse&courses[0]=1&courses[1]=2&amount=5&email=menno@sebsoft.nl&groups[0]=1&groups[1]=2&generate_single_pdfs=1&resultType=xml';<br />
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
    static final public function GenerateVouchersForCourse($email, $amount, $courses, $groups = false, $generate_single_pdfs = false){
        global $CFG;
        
        require_once($CFG->dirroot . '/blocks/voucher/class/VoucherGenerator.php');
        
        // Get max length for the voucher code
        if (!$voucher_code_length = get_config('voucher', 'voucher_code_length')) $voucher_code_length = 16;
        
        // Now that we've got all information we'll create the voucher objects
        $vouchers = array();
        for($i = 0; $i < $amount; $i++) {
            
            $voucher = new stdClass();
            $voucher->ownerid = null;
            $voucher->amount = $amount;
            $voucher->submission_code = VoucherGenerator::GenerateUniqueCode($voucher_code_length);
            
            $voucher->courses = array();
            foreach($courses as $course) {
                $voucher->courses[] = $course;
            }
            
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
    
    /**
     * Builds the vouchers for the given course and returns the voucher codes.
     * 
     * @param int $amount Amount of vouchers to be generated.
     * @param int $courses Array of IDs of the courses the vouchers will be generated for.
     * @param array $groups Array of IDs of all groups the users will be added to after using a Voucher.
     * @return array $voucher_codes Array of voucher codes.
     * 
     * @example <pre>
     * $url = 'http://moodle.menno.extern.ds.office.sebsoft.nl/blocks/voucher/view/api.php?method=GenerateVouchersForCourse&courseid=1&amount=5&groups[0]=1&groups[1]=2&resultType=xml';<br />
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
    static final public function RequestVoucherCodesForCourse($amount, $courses, $groups = false){
        global $CFG;
        
        require_once($CFG->dirroot . '/blocks/voucher/class/VoucherGenerator.php');
        
        // Get max length for the voucher code
        if (!$voucher_code_length = get_config('voucher', 'voucher_code_length')) $voucher_code_length = 16;
        
        // Now that we've got all information we'll create the voucher objects
        $vouchers = array();
        $voucher_codes = array();
        for($i = 0; $i < $amount; $i++) {
            
            $voucher = new stdClass();
            $voucher->ownerid = null;
            $voucher->amount = $amount;
            $voucher->submission_code = VoucherGenerator::GenerateUniqueCode($voucher_code_length);
            $voucher_codes[] = $voucher->submission_code;
            
            $voucher->courses = array();
            foreach($courses as $course) {
                $voucher->courses[] = $course;
            }
            
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

        voucher_Helper::GenerateVouchers($vouchers);

        return $voucher_codes;
    }

    /**
     * Builds the vouchers for the given cohorts and returns the voucher codes.
     * 
     * @param int $amount Amount of vouchers to be generated.
     * @param array $cohorts Array of IDs of the cohorts the vouchers will be generated for.
     * @return array $voucher_codes Array of voucher codes.
     * 
     * @example <pre>
     * $url = 'http://moodle.menno.extern.ds.office.sebsoft.nl/blocks/voucher/view/api.php?method=RequestVoucherCodesForCohorts&amount=5&cohorts[0]=1&cohorts[1]=2&resultType=xml';<br />
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
    static final public function RequestVoucherCodesForCohorts($amount, $cohorts){
        global $CFG;
        
        require_once($CFG->dirroot . '/blocks/voucher/class/VoucherGenerator.php');

        // Get max length for the voucher code
        if (!$voucher_code_length = get_config('voucher', 'voucher_code_length')) $voucher_code_length = 16;
        
        // Now that we've got all information we'll create the voucher objects
        $vouchers = array();
        $voucher_codes = array();
        for($i = 0; $i < $amount; $i++) {
            
            $voucher = new stdClass();
            $voucher->ownerid = null;
            $voucher->courseid = null;
            $voucher->amount = $amount;
            $voucher->submission_code = VoucherGenerator::GenerateUniqueCode($voucher_code_length);
            $voucher_codes[] = $voucher->submission_code;
            
            $voucher->cohorts = array();
            foreach($cohorts as $cohortid) {
                // Build cohort object
                $cohort = new stdClass();
                $cohort->cohortid = $cohortid;
                $voucher->cohorts[] = $cohort;
            }
            
            $vouchers[] = $voucher;
        }

        voucher_Helper::GenerateVouchers($vouchers);
        
        return $voucher_codes;

    }
    
    
    /**
     * Returns the reports of the vouchers that have been created. You can add extra parameters to force some filters on the data.
     * 
     * @param string $type Type of vouchers to get reports for. This can be 'courses', 'cohorts' or 'all', and it defaults to 'all'
     * @param int $ownerid ID of the creator of the vouchers.
     * @param date $fromDate Request voucher reports created from this date. If given this should be passed in American format (yyyy-mm-dd)
     * @param date $tillDate Request voucher reports created until this date. If given this should be passed in American format (yyyy-mm-dd)
     * @return array $voucher_codes Array of voucher codes.
     * 
     * @example <pre>
     * $url = 'http://moodle.menno.extern.ds.office.sebsoft.nl/blocks/voucher/view/api.php?method=GetVoucherReports&type=all&ownerid=2&fromDate=2013-01-01&tillDate=2013-12-31&resultType=xml';<br />
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
    static final public function GetVoucherReports($type = 'all', $ownerid = null, $fromDate = null, $tillDate = null) {
        global $DB;
        
        switch($type) {
            
            case 'all':
                $vouchers = voucher_Db::GetAllVouchers($ownerid, $fromDate, $tillDate);
                break;
            
            case 'courses':
                $vouchers = voucher_Db::GetCourseVouchers($ownerid, $fromDate, $tillDate);
                break;
            
            case 'cohorts':
                $vouchers = voucher_Db::GetCohortVouchers($ownerid, $fromDate, $tillDate);
                break;
            
            default:
                throw new Exception("Incorrect type of vouchers requested. Please call either the type 'cohorts', 'courses' or 'all'.");
                break;
            
        }
        
        $reports = array();
        foreach($vouchers as $voucher) {
            
            $report = new stdClass();
            $report->code = $voucher->submission_code;
            $report->timecreated = date('Y:m:d H:i:s', $voucher->timecreated);
            
            if (!is_null($voucher->userid)) {
                
                if ($user = voucher_Db::GetUser(array('id'=>$voucher->userid))) {
                    
                    $report->user = new stdClass();
                    $report->user->fullname = fullname($user);
                    $report->user->email = $user->email;
                    $report->user->idnumber = $user->idnumber;
                    
                }
            }
            
            if (isset($voucher->courses)) {
                
                $report->courses = array();
                foreach($voucher->courses as $courseid) {
                    
                    if (!$course = voucher_Db::GetCourseById($courseid)) {
                        continue;
                    }
                    
                    $reportCourse = new stdClass();
                    $reportCourse->id = $courseid;
                    $reportCourse->course = $course->fullname;
                    $reportCourse->idnumber = $course->idnumber;
                    
                    if (isset($user) && $user !== false) {
                        
                        $completionInfo = voucher_Helper::_LoadCourseCompletionInfo($user, $course);
                        
                        $params = array('course'=>$courseid, 'criteriatype'=>COMPLETION_CRITERIA_TYPE_GRADE);
                        $completionCriteria = $DB->get_record('course_completion_criteria', $params);
                        
                        $reportCourse->datestarted = $completionInfo->date_started;
                        $reportCourse->datecompleted = $completionInfo->date_complete;
                        $reportCourse->finalgrade = $completionInfo->str_grade;
                        
                        if ($completionCriteria !== false && !is_null($completionCriteria->gradepass)) {
                            $reportCourse->requiredgrade = $completionCriteria->gradepass;
                        } else {
                            $reportCourse->requiredgrade = '-';
                        }
                        
                    }
                    
                    $report->courses[] = $reportCourse;
                }
                
            } else {
                
                $report->cohorts = array();
                foreach($voucher->cohorts as $cohortid) {
                    
                    if (!$cohort = voucher_Db::GetCohortById($cohortid)) {
                        continue;
                    }
                    
                    $reportCohort = new stdClass();
                    $reportCohort->id = $cohortid;
                    $reportCohort->name = $cohort->name;
                    
                    $report->cohorts[] = $reportCohort;
                }
                
            }
            
            $reports[] = $report;
        }
        
        return $reports;
    }
    
}
?>