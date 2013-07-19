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
class voucher_Helper
{

    /**
     * __construct() HIDE: WE'RE STATIC 
     */
    protected function __construct()
    {
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
     **/
    public static final function GenerateVouchers($vouchers) {
        global $SESSION, $DB;
        
        $errors = array();
        
        // Lets loop through the vouchers
        foreach($vouchers as $voucher) {
            
            // An object for the voucher itself
            $obj_voucher = new stdClass();
            $obj_voucher->ownerid = $voucher->ownerid;
            $obj_voucher->amount = $SESSION->voucher->amount;
            $obj_voucher->submission_code = $voucher->submission_code;
            $obj_voucher->timecreated = time();
            $obj_voucher->userid = null;
            $obj_voucher->timeexpired = null;
            $obj_voucher->courseid = (!isset($voucher->courseid) || $voucher->courseid === null) ? null : $voucher->courseid;
            
            // insert voucher in db so we've got an id
            if (!$voucher_id = $DB->insert_record('vouchers', $obj_voucher)) {
                $errors[] = 'Failed to create general voucher object in database.';
                continue;
            }
            
            // Cohort voucher
            if (!isset($voucher->courseid) || $voucher->courseid === null) {
                
                // Loop through all cohorts
                foreach($voucher->cohorts as $cohort) {

                    // An object for each added cohort
                    $obj_cohort = new stdClass();
                    $obj_cohort->voucherid = $voucher_id;
                    $obj_cohort->cohortid = $cohort->cohortid;

                    // And insert in db
                    if (!$DB->insert_record('voucher_cohorts', $obj_cohort)) {
                        $errors[] = 'Failed to create cohort ' . $cohort->cohortid . ' for voucher id ' . $voucher_id . '.';
                        continue;
                    }
                }

            // Course voucher
            } elseif(isset($voucher->groups)) {
                
                foreach($voucher->groups as $group) {

                    // An object for each added cohort
                    $obj_group = new stdClass();
                    $obj_group->groupid = $group->groupid;
                    $obj_group->voucherid = $voucher_id;

                    // And insert in db
                    if (!$DB->insert_record('voucher_groups', $obj_group)) {
                        $errors[] = 'Failed to create group ' . $group->groupid . ' for voucher id ' . $voucher_id . '.';
                        continue;
                    }
                }
            }
        }
        
        return (count($errors) > 0) ? $errors : true;
    }
    
    /**
     * MailVouchers
     * This function will mail the generated vouchers.
     * 
     * @param array $vouchers An array of generated vouchers
     * @param string $email The email address the vouchers are to be send to
     * @param bool $generate_single_pdfs Whether each voucher gets a PDF or 1 PDF for all vouchers
     **/
    public static final function MailVouchers($vouchers, $email, $generate_single_pdfs)
    {
        global $DB, $CFG, $USER;
        
        // include pdf generator
        require_once BLOCK_VOUCHER_CLASSROOT."VoucherPDFGenerator.php";

        // One PDF for each voucher
        if ($generate_single_pdfs) {
            
            // Initiate the mailer
            $phpmailer = self::_GenerateVoucherMail($email);
            $zip = new ZipArchive();
            
            $filename = "{$CFG->dataroot}/vouchers.zip";
            if (file_exists($filename)) unlink($filename);
            
            $zip->open($filename, ZipArchive::CREATE|ZipArchive::OVERWRITE);
            
            $increment = 0;
            foreach($vouchers as $voucher)
            {
                // Generate the PDF
                $pdfgen = new voucher_PDF(get_string('pdf:titlename', BLOCK_VOUCHER));
                $pdfgen->setVoucherPageTemplate(get_string('default-voucher-page-template', BLOCK_VOUCHER));
                $pdfgen->generate($voucher);
                $pdfstr = $pdfgen->Output('voucher_'.$increment.'.pdf', 'S'); //'FI' enables storing on local system, this could be nice to have?
                // Add PDF to the zip
                $zip->addFromString("voucher_$increment.pdf", $pdfstr);
                // And up the increment
                $increment ++;
            }
            
            $zip->close();
            // Add zip to the attachment
            $phpmailer->AddAttachment($filename);
            
        // All vouchers in 1 PDF
        } else {
            
            $phpmailer = self::_GenerateVoucherMail($email);

            $pdfgen = new voucher_PDF(get_string('pdf:titlename', BLOCK_VOUCHER));
            $pdfgen->setVoucherPageTemplate(get_string('default-voucher-page-template', BLOCK_VOUCHER));
            $pdfgen->generate($vouchers);
            $pdfstr = $pdfgen->Output('vouchers.pdf', 'S'); //'FI' enables storing on local system, this could be nice to have?
            $phpmailer->AddStringAttachment($pdfstr, 'vouchers.pdf');
            
        }
        $res = $phpmailer->Send();

    }


    protected static final function _GenerateVoucherMail($email) {
        global $CFG, $USER;
        
        require_once $CFG->libdir.'/phpmailer/class.phpmailer.php';

        // FROM is always $USER
        // TO is either support user or $email
        $supportuser = generate_email_supportuser();
        
        $email_to = new stdClass();
        $email_to->str_name = ($email == $supportuser->email) ? ' ' . $supportuser->firstname : '';
        
        $mail_content = get_string('voucher_mail_content', BLOCK_VOUCHER, $email_to);
        
        $phpmailer = new PHPMailer();
        $phpmailer->Body = $mail_content;
        $phpmailer->AltBody = strip_tags($mail_content);
        $phpmailer->From = $USER->email;
        $phpmailer->FromName = $USER->firstname . ' ' . $USER->lastname;
        $phpmailer->IsHTML(true);
        $phpmailer->Subject = get_string('voucher_mail_subject', BLOCK_VOUCHER);
        $phpmailer->AddReplyTo($CFG->noreplyaddress);
        
//        $phpmailer->AddBcc('sebastian@sebsoft.nl');
//        $phpmailer->AddBcc('rogier@sebsoft.nl');
//        if (strstr($this->siteemail, ':') !== false)
//        {
//            $mailaddrs = explode(':', $this->siteemail);
//            foreach ($mailaddrs as $mailaddr)
//            {
//                $phpmailer->AddBcc($mailaddr);
//            }
//        }
//        elseif (!empty($this->siteemail))
//        {
//            $phpmailer->AddBcc($this->siteemail);
//        }
        $phpmailer->AddCustomHeader("X-VOUCHER-Send: " . time());
        $phpmailer->AddAddress($email);
        
        return $phpmailer;
    }

    
    /**
     * Collect all courses connected to the provided cohort ID
     * 
     * Return false if no courses are connected or an array of course records
     */
    final static public function get_courses_by_cohort($cohort_id) {
        global $CFG, $DB;
        
        $sql_connected_courses = "
            SELECT * FROM {$CFG->prefix}enrol e
            LEFT JOIN {$CFG->prefix}course c
                ON e.courseid = c.id
            WHERE customint1 = {$cohort_id}
            AND e.enrol = 'cohort'";
        $connected_courses = $DB->get_records_sql($sql_connected_courses);

        return (count($connected_courses) > 0) ? $connected_courses : false;
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
    final static public function getPermission($name = '')
    {
        $context = get_context_instance(CONTEXT_SYSTEM);

        $array = array();
        //FIRST check if you are a super admin
        $array['administration'] = (has_capability('block/voucher:administration', $context)) ? true : false;
        $array['addinstance'] = (has_capability('block/voucher:administration', $context)) ? true : false;
        $array['inputvouchers'] = (has_capability('block/voucher:inputvouchers', $context)) ? true : false;
        $array['generatevouchers'] = (has_capability('block/voucher:generatevouchers', $context)) ? true : false;

        if (!empty($name))
        {
            return $array[$name];
        }
        else
        {
            return $array;
        }
    }

    final public static function formatSize($size)
    {
        $size = (int) $size;
        $fmt = '%.02f' . self::_size_modifier($size);
        while ($size > 1024)
        {
            $size /= 1024.0;
        }
        return sprintf($fmt, $size);
    }

    final private static function _size_modifier($size)
    {
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
     * Make sure editing mode is off and moodle doesn't use complete overview
     * @global object $USER
     * @global object $PAGE
     * @param moodle_url $redirectUrl
     */
    public static function forceNoEditingMode($redirectUrl = '')
    {
        global $USER, $PAGE;
        if (!empty($USER->editing))
        {
            $USER->editing = 0;

            if (empty($redirectUrl))
            {
                $params = $PAGE->url->params();
                $redirectUrl = new moodle_url($PAGE->url, $params);
            }
            redirect($redirectUrl);
        }
    }

    public static final function printAutoRedirect(moodle_url $url, $return = false)
    {
        $str = '';
        $str .= '<div id="redir" style="color: #00aa00; background-color: #ffff80; text-align: center; font-weight: bold; font-family: Verdana, Helvetica, Arial, Sans-serif"></div>';
        $str .= '<script type = "text/javascript">';
        $str .= 'var bc=5;';
        $str .= 'function autorefresh(){bc--; if (bc>0){ document.getElementById(\'redir\').innerHTML="' . get_string('redirect_in', BLOCK_VOUCHER) . '" + bc + " ' . get_string('seconds', BLOCK_VOUCHER) . '";setTimeout("autorefresh()",1000); }else {window.location="' . $url->out(true) . '";}}';
        $str .= '</script>';

        if ($return)
        {
            return $str;
        }
        else
        {
            echo $str;
        }
    }

    public static final function createBlockUrl($relUrl, $params = array())
    {
        return new moodle_url(BLOCK_VOUCHER_WWWROOT . $relUrl, $params);
    }

    public static final function createBlockLink($relUrl, $params, $linktext, $linktitle = '')
    {
        $uri = new moodle_url(BLOCK_VOUCHER_WWWROOT . $relUrl, $params);
        $aparams = array('href' => str_replace('&amp;', '&', $uri));
        if (!empty($linktitle))
        {
            $aparams['title'] = $linktitle;
        }
        return html_writer::tag('a', $linktext, $aparams);
    }

    public static final function createBlockButton($relUrl, $params, $buttontext, $title = '')
    {
        $uri = new moodle_url(BLOCK_VOUCHER_WWWROOT . $relUrl, $params);
        $aparams = array('onclick' => 'window.location=\''.str_replace('&amp;', '&', $uri).'\'');
        if (!empty($title))
        {
            $aparams['title'] = $title;
        }
        return html_writer::tag('button', $buttontext, $aparams);
    }

}