<?php

/*
 * File: Helper.php
 * Encoding: UTF-8
 * @package voucher
 * 
 * @Version 1.0.0
 * @Since 11-jul-2013
 * @copyright Sebsoft.nl
 * @author Menno de Ridder <menno@sebsoft.nl>
 */

/**
 * Helper class for various functionality
 */
class voucher_Helper {

    /**
     * __construct() HIDE: WE'RE STATIC 
     */
    protected function __construct() {
        // static's only please!
    }

    /**
     * GenerateVouchers
     * This function will generate the vouchers.
     * Basically all it does is insert the records
     * in the database, as all checks have been 
     * done in the view.
     * 
     * @param array $vouchers An array of voucher objects
     * @return True or an array of errors
     * */
    public static final function GenerateVouchers($vouchers) {
        global $DB, $SITE, $SESSION;

        $errors = array();

        // Lets loop through the vouchers
        foreach ($vouchers as $voucher) {
            
            // An object for the voucher itself
            $obj_voucher = new stdClass();
            $obj_voucher->ownerid = $voucher->ownerid;
            $obj_voucher->submission_code = $voucher->submission_code;
            $obj_voucher->timecreated = time();
            $obj_voucher->timeexpired = null;
            $obj_voucher->userid = null;
            
            // Extra columns
            $obj_voucher->for_user_email = (isset($voucher->for_user_email) && !empty($voucher->for_user_email)) ? $voucher->for_user_email : null;
            $obj_voucher->for_user_name = (isset($voucher->for_user_name) && !empty($voucher->for_user_name)) ? $voucher->for_user_name : null;
            $obj_voucher->for_user_gender = (isset($voucher->for_user_gender) && !empty($voucher->for_user_gender)) ? $voucher->for_user_gender : null;
            $obj_voucher->redirect_url = (isset($voucher->redirect_url) && !empty($voucher->redirect_url)) ? $voucher->redirect_url : null;
            $obj_voucher->issend = 0;
            $obj_voucher->senddate = (isset($voucher->senddate) && !empty($voucher->senddate)) ? $voucher->senddate : null;
            $obj_voucher->enrolperiod = (isset($voucher->enrolperiod) && !empty($voucher->enrolperiod)) ? $voucher->enrolperiod : 0;
            
            if (isset($voucher->email_body) && !empty($voucher->email_body)) {
                
                $genderTxt = (!is_null($obj_voucher->for_user_gender)) ? $obj_voucher->for_user_gender : '';
                
                // Replace some strings in the email body
                $arr_replace = array(
                    '##to_name##',
                    '##site_name##',
                    '##to_gender##'
                );
                $arr_with = array(
                    $voucher->for_user_name,
                    $SITE->fullname,
                    $genderTxt
                );
                
                // Check if we're generating based on course, in which case we enter the course name too.
                if (isset($voucher->courses) && !empty($voucher->courses)) {
                    
                    $strCourseFullnames = '';
                    foreach($voucher->courses as $courseid) {
                        
                        if (!$course = $DB->get_record('course', array('id'=>$courseid))) {
                            print_error('error:course-not-found', BLOCK_VOUCHER);
                        }
                        
                        if ($courseid != end($voucher->courses)) {
                            $strCourseFullnames .= $course->fullname . ', ';
                        } elseif ($courseid) {
                            $strCourseFullnames .= get_string('and', BLOCK_VOUCHER) . ' ' . $course->fullname;
                        }
                    }
                    
                    $arr_replace[] = '##course_fullnames##';
                    $arr_with[] = $strCourseFullnames;
                }

                $obj_voucher->email_body = str_replace($arr_replace, $arr_with, $voucher->email_body);
            } else {
                $obj_voucher->email_body = null;
            }
            
            // insert voucher in db so we've got an id
            if (!$voucher_id = $DB->insert_record('vouchers', $obj_voucher)) {
                $errors[] = 'Failed to create general voucher object in database.';
                continue;
            }
            
            // Course voucher
            if (isset($voucher->courses)) {
                
                // create group records for this voucher
                if (isset($voucher->groups) && !empty($voucher->groups)) {
                    foreach ($voucher->groups as $group) {

                        // An object for each added cohort
                        $obj_group = new stdClass();
                        $obj_group->groupid = $group->groupid;
                        $obj_group->voucherid = $voucher_id;

                        // And insert in db
                        if (!$DB->insert_record('voucher_groups', $obj_group)) {
                            $errors[] = 'Failed to create group ' . $group->groupid . ' record for voucher id ' . $voucher_id . '.';
                            continue;
                        }
                    }
                }
                
                // create course records for this voucher id
                if (!empty($voucher->courses)) { // can't be empty right..?
                    foreach($voucher->courses as $courseid) {
                        $obj_course = new stdClass();
                        $obj_course->courseid = $courseid;
                        $obj_course->voucherid = $voucher_id;

                        if (!$DB->insert_record('voucher_courses', $obj_course)) {
                            $errors[] = 'Failed to create course (id ' . $courseid . ') for voucher id ' . $voucher_id . '.';
                            continue;
                        }
                    }
                }
                
            // Cohort voucher
            } else {
                
                if (isset($voucher->cohorts) && !empty($voucher->cohorts)) {
                    // Loop through all cohorts
                    foreach ($voucher->cohorts as $cohort) {

                        // An object for each added cohort
                        $obj_cohort = new stdClass();
                        $obj_cohort->voucherid = $voucher_id;
                        $obj_cohort->cohortid = $cohort->cohortid;

                        // And insert in db
                        if (!$DB->insert_record('voucher_cohorts', $obj_cohort)) {
                            $errors[] = 'Failed to create cohort ' . $cohort->cohortid . ' record for voucher id ' . $voucher_id . '.';
                            continue;
                        }
                    }
                }
                
            }
        }
        
        return (count($errors) > 0) ? $errors : true;
    }
    
    public static final function GetRecipientsFromCsv($recipients_str) {
        
        $recipients = array();
        $count = 0;

        // Split up in rows
        $expectedColumns = array('e-mail', 'gender', 'name');
        if (!$csvData = str_getcsv($recipients_str, "\n")) return false;
        // Split up in columns
        foreach($csvData as &$row) {
            
            // Get the next row
            $row = str_getcsv($row, ",");
            
            // Check if we're looking at the first row
            if ($count == 0) {
                
                $expectedRow = array();
                // Set the columns we'll need
                foreach($row as $key=>&$column) {
                    
                    $column = trim(strtolower($column));
                    if (!in_array($column, $expectedColumns)) {
                        continue;
                    }
                    
                    $expectedRow[$key] = $column;
                }
                // if we're missing columns
                if (count($expectedColumns) != count($expectedRow)) {
                    return false;
                }
                
                // Now set which columns we'll need to use when extracting the information
                $nameKey = array_search('name', $expectedRow);
                $emailKey = array_search('e-mail', $expectedRow);
                $genderKey = array_search('gender', $expectedRow);
                
                $count++;
                continue;
            }

            $recipient = new stdClass();
            $recipient->name = trim($row[$nameKey]);
            $recipient->email = trim($row[$emailKey]);
            $recipient->gender = trim($row[$genderKey]);
            
            $recipients[] = $recipient;
        }
        
        return $recipients;
    }
    
    /**
     * MailVouchers
     * This function will mail the generated vouchers.
     * 
     * @param array $vouchers An array of generated vouchers
     * @param string $email The email address the vouchers are to be send to
     * @param bool $generate_single_pdfs Whether each voucher gets a PDF or 1 PDF for all vouchers
     * */
    public static final function MailVouchers($vouchers, $emailTo, $generate_single_pdfs = false, $emailBody = false, $initiatedByCron = false) {
        global $DB, $CFG;
        
        // include pdf generator
        require_once BLOCK_VOUCHER_CLASSROOT . "VoucherPDFGenerator.php";

        // One PDF for each voucher
        if ($generate_single_pdfs) {

            // Initiate the mailer
            $phpmailer = self::_GenerateVoucherMail($emailTo, $emailBody, $initiatedByCron);
            $zip = new ZipArchive();

            $filename = "{$CFG->dataroot}/vouchers.zip";
            if (file_exists($filename))
                unlink($filename);

            $zip->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            $increment = 1;
            foreach ($vouchers as $voucher) {
                // Generate the PDF
                $pdfgen = new voucher_PDF(get_string('pdf:titlename', BLOCK_VOUCHER));
                // Fill the voucher with text
                $pdfgen->setVoucherPageTemplateMain(get_string('default-voucher-page-template-main', BLOCK_VOUCHER));
                $pdfgen->setVoucherPageTemplateBotLeft(get_string('default-voucher-page-template-botleft', BLOCK_VOUCHER));
                $pdfgen->setVoucherPageTemplateBotRight(get_string('default-voucher-page-template-botright', BLOCK_VOUCHER));
                // Generate it
                $pdfgen->generate($voucher);
                $pdfstr = $pdfgen->Output('voucher_' . $increment . '.pdf', 'S'); //'FI' enables storing on local system, this could be nice to have?
                // Add PDF to the zip
                $zip->addFromString("voucher_$increment.pdf", $pdfstr);
                // And up the increment
                $increment++;
            }

            $zip->close();
            // Add zip to the attachment
            $phpmailer->AddAttachment($filename);

            // All vouchers in 1 PDF
        } else {
            
            $phpmailer = self::_GenerateVoucherMail($emailTo, $emailBody, $initiatedByCron);

            $pdfgen = new voucher_PDF(get_string('pdf:titlename', BLOCK_VOUCHER));
            $pdfgen->setVoucherPageTemplateMain(get_string('default-voucher-page-template-main', BLOCK_VOUCHER));
            $pdfgen->setVoucherPageTemplateBotLeft(get_string('default-voucher-page-template-botleft', BLOCK_VOUCHER));
            $pdfgen->setVoucherPageTemplateBotRight(get_string('default-voucher-page-template-botright', BLOCK_VOUCHER));
            $pdfgen->generate($vouchers);
            $pdfstr = $pdfgen->Output('vouchers.pdf', 'S'); //'FI' enables storing on local system, this could be nice to have?
            $phpmailer->AddStringAttachment($pdfstr, 'vouchers.pdf');
        }
        
        $res = $phpmailer->Send();
    }

    protected static final function _GenerateVoucherMail($emailTo, $emailBody = false, $initiatedByCron = false) {
        global $CFG, $USER;

        require_once $CFG->libdir . '/phpmailer/class.phpmailer.php';

        // instantiate mailer
        $phpMailer = new PHPMailer();
        
        // set email from
        if ($initiatedByCron) {
            // get supportuser
            $supportuser = generate_email_supportuser();
            $phpMailer->FromName = fullname($supportuser);
            $phpMailer->From = $supportuser->email;
            
        } else {
            
            $phpMailer->FromName = fullname($USER);
            $phpMailer->From = $USER->email;
            
        }
        
        // set email body
        if ($emailBody !== false) {
            
            $phpMailer->Body = $emailBody;
            
        } else {
            
            $bodyParams = array(
                'to_name'=>fullname($USER),
                'from_name'=>fullname($USER)
            );
            $phpMailer->Body = get_string('voucher_mail_content', BLOCK_VOUCHER, $bodyParams);
            
        }
        
        // set last phpMailer params
        $phpMailer->AltBody = strip_tags($phpMailer->Body);
        $phpMailer->Subject = get_string('voucher_mail_subject', BLOCK_VOUCHER);
        $phpMailer->IsHTML(true);
        $phpMailer->AddAddress($emailTo);// might not have a name cause the voucher recipients aren't neccesarily Moodle users
        $phpMailer->AddReplyTo($phpMailer->From, $phpMailer->FromName);
        $phpMailer->AddCustomHeader("X-VOUCHER-Send: " . time());
        
        return $phpMailer;
    }
    
    /* ConfirmVouchersSent
     * 
     * Send confirmation email when the cron has send all the vouchers
     */
    public static final function ConfirmVouchersSent($ownerid, $timecreated) {
        global $CFG;
        
        require_once $CFG->libdir . '/phpmailer/class.phpmailer.php';
        
        $owner = voucher_Db::GetUser(array('id'=>$ownerid));
        
        $supportuser = generate_email_supportuser();
        $mail_content = get_string("confirm_vouchers_sent_body", BLOCK_VOUCHER, array('timecreated'=>date('Y-m-d', $timecreated)));

        $phpmailer = new PHPMailer();
        $phpmailer->Body = $mail_content;
        $phpmailer->AltBody = strip_tags($mail_content);
        $phpmailer->From = $supportuser->email;
        $phpmailer->FromName = trim($supportuser->firstname . ' ' . $supportuser->lastname);
        $phpmailer->IsHTML(true);
        $phpmailer->Subject = get_string('confirm_vouchers_sent_subject', BLOCK_VOUCHER);
        $phpmailer->AddAddress($owner->email);

        $res = $phpmailer->Send();
        
        return ($res);
    }
    
    public static final function GetVouchers() {
        global $USER;
        
        if (self::getPermission('viewallreports')) {
            $vouchers = voucher_Db::GetVouchers();
        } else {
            $vouchers = voucher_Db::GetVouchersByOwner($USER->id);
        }
        
        return $vouchers;
    }
    
    public static final function GetUnusedVouchers() {
        global $USER;

        if (self::getPermission('viewallreports')) {
            $vouchers = voucher_Db::GetUnusedVouchers();
        } else {
            $vouchers = voucher_Db::GetUnusedVouchersByOwner($USER->id);
        }
        
        return $vouchers;
        
    }

    /**
     * Load the course completion info
     * 
     * @param object $user User object from database
     * @param object $cinfo Course object from database
     */
    public static final function _LoadCourseCompletionInfo($user, $cinfo) {
        global $DB, $CFG;
        static $cstatus, $completion_info = array();
        //static $cert_mod;

        require_once $CFG->dirroot . '/grade/querylib.php';
        require_once $CFG->dirroot . '/lib/completionlib.php';

        // completion status 'cache' values (speed up, lass!)
        if ($cstatus === null) {
            $cstatus = array();
            $cstatus['started'] = get_string('report:status_started', BLOCK_VOUCHER);
            $cstatus['notstarted'] = get_string('report:status_not_started', BLOCK_VOUCHER);
            $cstatus['complete'] = get_string('report:status_completed', BLOCK_VOUCHER);
        }
        // completion info 'cache' (speed up, lass!)
        if (!isset($completion_info[$cinfo->id])) {
            $completion_info[$cinfo->id] = new completion_info($cinfo);
        }

        $ci = new stdClass();
        $ci->complete = false;
        $ci->str_status = $cstatus['notstarted'];
        $ci->date_started = '-';
        $ci->date_complete = '-';
        $ci->str_grade = '-';
        $ci->gradeinfo = null;

        // ok, fill out real data according to completion status/info
        $com = $completion_info[$cinfo->id];
        if ($com->is_tracked_user($user->id)) {
            // do we have an enrolment for the course for this user
            $sql = 'SELECT ue.* FROM {user_enrolments} ue JOIN {enrol} e ON ue.enrolid=e.id WHERE ue.userid=' . $user->id . ' AND e.courseid='.$cinfo->id.' ORDER BY timestart ASC, timecreated ASC';
            $records = $DB->get_records_sql($sql);
            if ($user->id == 28) echo("<pre>" . print_r($records, true) . "</pre>");

            if (count($records) === 1) {
                $record = array_shift($records);
                $ci->time_started = (($record->timestart > 0) ? $record->timestart : $record->timecreated);
                $ci->date_started = date('d-m-Y H:i:s', $ci->time_started);
            } else {
                $started = 0;
                $created = 0;

                foreach ($records as $record) {
                    if ($record->timestart > 0) {
                        $started = ($started == 0) ? $record->timestart : min($record->timestart, $started);
                    }
                    $created = ($created == 0) ? $record->timecreated : min($record->timecreated, $created);
                }

                $ci->time_started = (($started > 0) ? $started : $created);
                $ci->date_started = date('d-m-Y H:i:s', ($started > 0) ? $started : $created);
            }

            if ($com->is_course_complete($user->id)) {
                // fetch details for course completion
                $ci->complete = true;
                $comcom = new completion_completion(array(
                            'userid' => $user->id,
                            'course' => $cinfo->id
                        ));
                $ci->date_complete = date('d-m-Y H:i:s', $comcom->timecompleted);
                $ci->gradeinfo = grade_get_course_grade($cinfo->id);
                if ($ci->gradeinfo !== false) {
                    $ci->str_grade = $ci->grade_info->str_grade;
                }
                $ci->str_status = $cstatus['complete'];
            } else {
                // grrr... we need some complete info percentage... :(
                $ci->str_status = $cstatus['started'];
                // now append get completion percentage
            }
        }

        return $ci;
    }
    
    public static final function enrol_cohort_sync() {
        global $CFG;
        
        require_once($CFG->dirroot . '/enrol/cohort/locallib.php');
        
        if ($CFG->version < 2013051400) {
            return enrol_cohort_sync();
        } else {
            $trace = new null_progress_trace();
            return enrol_cohort_sync($trace);
        }
    }
    

    /**
     * Render HTML table from the course completion info
     * 
     * @param object $reportdata Data provided by loadCourseCompletionInfo
     */
    public static final function _render_html($reportdata) {
        global $CFG;
        $title = 'User Report';

//        exit("<pre>" . print_r($reportdata, true) . "</pre>");
        $table = new html_table();

        $table->head = array(
            get_string('report:heading:username', BLOCK_VOUCHER),
//            get_string('report:heading:function', BLOCK_VOUCHER),
//            get_string('report:heading:department', BLOCK_VOUCHER),
            get_string('report:heading:coursename', BLOCK_VOUCHER),
            //get_string('report:heading:coursetype', BLOCK_VOUCHER),
            get_string('report:heading:status', BLOCK_VOUCHER),
            get_string('report:heading:datestart', BLOCK_VOUCHER),
            get_string('report:heading:datecomplete', BLOCK_VOUCHER),
            get_string('report:heading:grade', BLOCK_VOUCHER)
        );

        $colcount = count($table->head);
        $table->summary = $title;
        $table->align = array_fill(0, $colcount - 1, 'left');
        //$table->size = array_fill(0, $colcount-1, (100/$colcount).'%');
        // SOME value-specific coloring on cells
        $typeDef = array(
            get_string('str:mandatory', BLOCK_VOUCHER) => '<span style="background-color: orange; font-weight: bold">' . get_string('str:mandatory', BLOCK_VOUCHER) . '</span>',
            get_string('str:optional', BLOCK_VOUCHER) => '<span style="background-color: yellow; font-weight: bold">' . get_string('str:optional', BLOCK_VOUCHER) . '</span>',
        );
        $statusDef = array(
            get_string('report:status_started', BLOCK_VOUCHER) => '<span style="background-color: orange; font-weight: bold">' . get_string('report:status_started', BLOCK_VOUCHER) . '</span>',
            get_string('report:status_not_started', BLOCK_VOUCHER) => '<span style="background-color: red; font-weight: bold">' . get_string('report:status_not_started', BLOCK_VOUCHER) . '</span>',
            get_string('report:status_completed', BLOCK_VOUCHER) => '<span style="background-color: lime; font-weight: bold">' . get_string('report:status_completed', BLOCK_VOUCHER) . '</span>',
        );
        // add data
        $table->data = array();
        foreach ($reportdata->userdata as $uid => $cdata) {
            $user = $reportdata->users[$uid];
            foreach ($cdata as $cid => $data) {
                $course = $reportdata->courses[$cid];
                $ulink = '<a href="' . $CFG->wwwroot . '/user/profile.php?id=' . $user->id . '">' . fullname($user) . '</a>';
                $rowdata = array(
                    $ulink,
//                $data->function,
//                $data->department,
                    $course->fullname,
                    //$typeDef[$data->coursetype],
                    $data->str_status,
                    is_numeric($data->date_started) ? self::renderDate($data->date_started, false) : $data->date_started,
                    is_numeric($data->date_complete) ? self::renderDate($data->date_complete, false) : $data->date_complete,
                    $data->str_grade
                );

                $table->data[] = $rowdata;
            }
        }

        return $table;
    }
    
    final static public function renderDate($time, $incTime = true) {
        return userdate($time, get_string($incTime ? 'report:dateformat' : 'report:dateformatymd', BLOCK_VOUCHER));
    }

    /**
     * Collect all cohort records based on an array of ids
     * 
     * returns false if no records are found or an array of cohort records
     */
    final static public function get_cohorts_by_ids($cohort_ids) {
        global $CFG, $DB;

        // Collect cohort records
        $sql_cohorts = "
            SELECT * FROM {$CFG->prefix}cohort
            WHERE id IN (" . join($cohort_ids, ',') . ")";
        $cohorts = $DB->get_records_sql($sql_cohorts);

        return (count($cohorts) > 0) ? $cohorts : false;
    }

    /**
     * Collect all group records based on an array of ids
     * 
     * returns false if no records are found or an array of cohort records
     */
    final static public function get_groups_by_ids($group_ids) {
        global $CFG, $DB;

        // Collect cohort records
        $sql_groups = "
            SELECT * FROM {$CFG->prefix}groups
            WHERE id IN (" . join($group_ids, ',') . ")";
        $groups = $DB->get_records_sql($sql_groups);

        return (count($groups) > 0) ? $groups : false;
    }

    /**
     * Check if we have permission for this
     * @param type $name
     * @return array | boolean
     */
    final static public function getPermission($name = '') {
        $context = get_context_instance(CONTEXT_SYSTEM);

        $array = array();
        //FIRST check if you are a super admin
        $array['administration'] = (has_capability('block/voucher:administration', $context)) ? true : false;
        $array['addinstance'] = (has_capability('block/voucher:administration', $context)) ? true : false;
        $array['inputvouchers'] = (has_capability('block/voucher:inputvouchers', $context)) ? true : false;
        $array['generatevouchers'] = (has_capability('block/voucher:generatevouchers', $context)) ? true : false;
        $array['viewreports'] = (has_capability('block/voucher:viewreports', $context)) ? true : false;
        $array['viewallreports'] = (has_capability('block/voucher:viewallreports', $context)) ? true : false;

        if (!empty($name)) {
            return $array[$name];
        } else {
            return $array;
        }
    }

    final public static function formatSize($size) {
        $size = (int) $size;
        $fmt = '%.02f' . self::_size_modifier($size);
        while ($size > 1024) {
            $size /= 1024.0;
        }
        return sprintf($fmt, $size);
    }

    final private static function _size_modifier($size) {
        if ($size > 1099511627776)
            return 'TB';
        elseif ($size > 1073741824)
            return 'GB';
        elseif ($size > 1048576)
            return 'MB';
        elseif ($size > 1024)
            return 'kB';
        else
            return 'B';
    }

    /**
     * Generate the HTML for a helpbutton.
     * @param  
     */
    final public static function generateHelpButton($element, $element_str, $block) {
        global $CFG;

        $element_button = '
            <span class="helplink">
                <a class="tooltip" aria-haspopup="true" href="' . $CFG->wwwroot . '/help.php?identifier=' . $element . '&component=' . $block . '&lang=' . current_language() . '">
                    <img class="iconhelp" src="' . $CFG->wwwroot . '/pix/help.png">
                </a>
            </span>
        ';

        return $element_button;
    }

    /**
     * Make sure editing mode is off and moodle doesn't use complete overview
     * @global object $USER
     * @global object $PAGE
     * @param moodle_url $redirectUrl
     */
    public static function forceNoEditingMode($redirectUrl = '') {
        global $USER, $PAGE;
        if (!empty($USER->editing)) {
            $USER->editing = 0;

            if (empty($redirectUrl)) {
                $params = $PAGE->url->params();
                $redirectUrl = new moodle_url($PAGE->url, $params);
            }
            redirect($redirectUrl);
        }
    }

    public static final function printAutoRedirect(moodle_url $url, $return = false) {
        $str = '';
        $str .= '<div id="redir" style="color: #00aa00; background-color: #ffff80; text-align: center; font-weight: bold; font-family: Verdana, Helvetica, Arial, Sans-serif"></div>';
        $str .= '<script type = "text/javascript">';
        $str .= 'var bc=5;';
        $str .= 'function autorefresh(){bc--; if (bc>0){ document.getElementById(\'redir\').innerHTML="' . get_string('redirect_in', BLOCK_VOUCHER) . '" + bc + " ' . get_string('seconds', BLOCK_VOUCHER) . '";setTimeout("autorefresh()",1000); }else {window.location="' . $url->out(true) . '";}}';
        $str .= '</script>';

        if ($return) {
            return $str;
        } else {
            echo $str;
        }
    }

    public static final function createBlockUrl($relUrl, $params = array()) {
        return new moodle_url(BLOCK_VOUCHER_WWWROOT . $relUrl, $params);
    }

    public static final function createBlockLink($relUrl, $params, $linktext, $linktitle = '') {
        $uri = new moodle_url(BLOCK_VOUCHER_WWWROOT . $relUrl, $params);
        $aparams = array('href' => str_replace('&amp;', '&', $uri));
        if (!empty($linktitle)) {
            $aparams['title'] = $linktitle;
        }
        return html_writer::tag('a', $linktext, $aparams);
    }

    public static final function createBlockButton($relUrl, $params, $buttontext, $title = '') {
        $uri = new moodle_url(BLOCK_VOUCHER_WWWROOT . $relUrl, $params);
        $aparams = array('onclick' => 'window.location=\'' . str_replace('&amp;', '&', $uri) . '\'');
        if (!empty($title)) {
            $aparams['title'] = $title;
        }
        return html_writer::tag('button', $buttontext, $aparams);
    }
    
    public static final function getVoucherRecipientsColumns() {
        $columns = array(
            'username',
            'firstname',
            'lastname',
            'email'
        );
    }
    
    public static final function ValidateVoucherRecipients($csvdata) {
        
        $error = false;
        
        if (!$recipients = voucher_Helper::GetRecipientsFromCsv($csvdata)) {
            
            // Required columns aren't found in the csv
            $error = get_string('error:recipients-columns-missing', BLOCK_VOUCHER);
            
        } else {
            
            // No recipient rows were added to the csv
            if (empty($recipients)) {
                
                $error = get_string('error:recipients-empty', BLOCK_VOUCHER);
                
            // Check max of the file
            } elseif (count($recipients) > 10000) {
                
                $error = get_string('error:recipients-max-exceeded', BLOCK_VOUCHER);
                
            } else {
                // Lets run through the file to check on email addresses
                foreach($recipients as $recipient) {
                    if (!filter_var($recipient->email, FILTER_VALIDATE_EMAIL)) {
                        $error = get_string('error:recipients-email-invalid', BLOCK_VOUCHER, $recipient);
                    }
                }
            }
            
        }

        return ($error === false) ? true : $error;
    }
    
    public static final function exportUnusedVouchers($reportColumns, $reportRows) {
        global $CFG;
        
        require_once("$CFG->libdir/excellib.class.php");

        $filename = 'unused-vouchers';

        $workbook = new MoodleExcelWorkbook($filename, 'Excel2007');

        $worksheet = array();

        $worksheet[0] = $workbook->add_worksheet('sheet1');
        $colNum = 0;
        foreach ($reportColumns as $column) {
            $worksheet[0]->write(0, $colNum, $column);
            $colNum++;
        }
        
        $rowNum = 1;
        foreach($reportRows as $reportRow) {
           
            $colNum = 0;
            foreach($reportRow as $value) {
                $worksheet[0]->write($rowNum, $colNum, $value);
                $colNum ++;
            }
            $rowNum ++;
           
        }
        
        $workbook->close();
        
        // Must die to avoid mismatching content length
        exit();
    }
    
}