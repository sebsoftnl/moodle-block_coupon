<?php

/*
 * File: db.php
 * Encoding: UTF-8
 * @package voucher
 * 
 * @Version 1.0.0
 * @Since 11-jul-2013
 * @copyright Sebsoft.nl
 * @author Menno de Ridder <menno@sebsoft.nl>
 */

class voucher_Db
{

    /**
     * __construct() HIDE: WE'RE STATIC 
     */
    protected function __construct()
    {
        // static's only please!
    }

    /**
     * Collect all courses connected to the provided cohort ID
     * 
     * Return false if no courses are connected or an array of course records
     */
    final static public function GetCoursesByCohort($cohortid) {
        global $DB;

        $sql_cohort_courses = "
            SELECT * FROM {enrol} e
            LEFT JOIN {course} c
                ON e.courseid = c.id
            WHERE customint1 = {$cohortid}
            AND e.enrol = 'cohort'
            AND c.visible = 1
            AND c.id != 1
            ORDER BY c.fullname ASC";
        $cohort_courses = $DB->get_records_sql($sql_cohort_courses);

        return (count($cohort_courses) > 0) ? $cohort_courses : false;
    }
    
    public static final function GetUser($conditions) {
        global $DB;
        
        return $DB->get_record('user', $conditions);
    }
    
    final static public function GetVoucherGroups($voucherid) {
        global $DB;
        
        $sql_groups = "
            SELECT vg.id, g.name FROM {voucher_groups} vg
            LEFT JOIN {groups} g ON vg.groupid = g.id
            WHERE vg.voucherid = $voucherid";
        $voucherGroups = $DB->get_records_sql($sql_groups);
        
        return (!empty($voucherGroups)) ? $voucherGroups : false;
    }
    
    final static public function GetVoucherCohorts($voucherid) {
        global $DB;
        
        $sql_cohorts = "
            SELECT * FROM {voucher_cohorts} vc
            LEFT JOIN {cohort} c ON vc.cohortid = c.id
            WHERE vc.voucherid = $voucherid";
        $voucherCohorts = $DB->get_records_sql($sql_cohorts);
        
        return (!empty($voucherCohorts)) ? $voucherCohorts : false;
    }
    
    final static public function GetUnconnectedCohortCourses($cohortid) {
        global $DB;
        
        $sql_unconnected_courses = "
            SELECT * FROM {course} c
            WHERE c.id != 1
            AND c.visible = 1
            AND c.id NOT IN (
                SELECT courseid FROM {enrol} e
                WHERE e.customint1 = {$cohortid}
                AND e.enrol = 'cohort'
            )
            ORDER BY c.fullname ASC";
        $unconnected_courses = $DB->get_records_sql($sql_unconnected_courses);
        
        return (!empty($unconnected_courses)) ? $unconnected_courses : false;
    }
    
    static public final function GetGroupsByCourseId($courseid) {
        global $DB;
        
        $groups = $DB->get_records('groups', array('courseid'=>$courseid), null, 'id, name');
        
        return (!empty($groups)) ? $groups : false;
    }
    
    static public final function GetCourseById($courseid) {
        global $DB;
        
        return ($DB->get_record('course', array('id'=>$courseid)));
    }
    
    static public final function GetCohorts() {
        global $DB;
        
        $cohorts = $DB->get_records('cohort', null, null, 'id, name');
        
        return (!empty($cohorts)) ? $cohorts : false;
    }
    
    static public final function GetVouchers() {
        global $DB;
        
        $sql_vouchers = "
            SELECT * FROM {vouchers}
            WHERE userid IS NOT NULL";
        $vouchers = $DB->get_records_sql($sql_vouchers);
        
        return (!empty($vouchers)) ? $vouchers : false;
    }
    
    static public final function GetVouchersByOwner($ownerid) {
        global $DB;
        
        $sql_vouchers = "
            SELECT * FROM {vouchers}
            WHERE userid IS NOT NULL
            AND ownerid = $ownerid";
        $vouchers = $DB->get_records_sql($sql_vouchers);

        return (!empty($vouchers)) ? $vouchers : false;
    }
    
    static public final function GetUnusedVouchers() {
        global $DB;
        
        $sql_vouchers = "
            SELECT v.*, u.firstname, u.lastname, u.username FROM {vouchers} v
            LEFT JOIN {user} u ON v.ownerid = u.id
            WHERE v.userid IS NULL
            ORDER BY v.senddate DESC";
        $vouchers = $DB->get_records_sql($sql_vouchers);
        
        return (!empty($vouchers)) ? $vouchers : false;
    }
    
    static public final function GetUnusedVouchersByOwner($ownerid) {
        global $DB;
        
        $sql_vouchers = "
            SELECT v.*, u.firstname, u.lastname, u.username
            FROM {vouchers} v
            LEFT JOIN {user} u ON v.ownerid = u.id
            WHERE userid IS NULL
            AND ownerid = $ownerid
            ORDER BY v.senddate DESC";
        $vouchers = $DB->get_records_sql($sql_vouchers);

        return (!empty($vouchers)) ? $vouchers : false;
    }
    
    static public final function GetVisibleCourses() {
        global $DB;
        
        $courses_select = "id != 1 AND visible = 1";
        $courses = $DB->get_records_select('course', $courses_select, null, 'fullname ASC');
        
        return (!empty($courses)) ? $courses : false;
    }
    
    static public final function GetBlockVersion()
    {
        static $currentVersion = null;
        if ($currentVersion === null)
        {
            global $DB;
            $currentVersion = $DB->get_field('block', 'version', array('name' => 'voucher'));
        }
        return $currentVersion;
    }

    /**
     * get profile field id
     * @global moodle_database $DB moodle_database
     * @param type $name field shortname
     * @return mixed
     * @throws Exception if not found
     */
    static public final function GetProfileFieldId($name)
    {
        if (isset(self::$_profileFieldIds[$name]))
        {
            return self::$_profileFieldIds[$name];
        }
        global $DB;
        $field = $DB->get_field('user_info_field', 'id', array('shortname' => $name), MUST_EXIST);
        self::$_profileFieldIds[$name] = $field;
        return self::$_profileFieldIds[$name];
    }

    /**
     * check whether or not a custom profile field is set for for a specified user
     * @param int $uid user id
     * @param string $field custom profile field SHORTNAME
     * @return mixed the field data
     * @throws Exception if not found
     */
    static public final function HasProfileField($uid, $field)
    {
        $fid = self::GetProfileFieldId($field);
        //
        global $DB;
        $fielddata = $DB->get_field('user_info_data', 'data', array('userid' => $uid, 'fieldid' => $fid), MUST_EXIST);
        return $fielddata;
    }

    /**
     * update a custom profile field for a user, or insert it if not yet present
     * @global moodle_database $DB
     * @param int $uid userid
     * @param string $field custom profile field name
     * @param mixed $value profile field data
     */
    static public final function UpdateProfileField($uid, $field, $value)
    {
        $fid = self::GetProfileFieldId($field);
        if ($fid <= 0)
        {
            return false;
        }
        //
        global $DB;
        $data = $DB->get_record('user_info_data', array('userid' => $uid, 'fieldid' => $fid));
        if ($data === false)
        {
            // add data, it doesn't exist
            $data = new stdClass();
            $data->fieldid = $fid;
            $data->userid = $uid;
            $data->data = $value;
            $DB->insert_record('user_info_data', $data);
        }
        else
        {
            if ($data->data != $value)
            {
                // update record
                $data->data = $value;
                $DB->update_record('user_info_data', $data);
            }
        }
    }
    
    
    public static final function GetVouchersToSend() {
        global $DB;
        
        $senddate = time();
        
        $query = "
            SELECT * FROM {vouchers} v
            WHERE senddate < $senddate
            AND issend = 0
            AND for_user_email IS NOT NULL
            LIMIT 500
        ";
        $vouchers = $DB->get_records_sql($query);
        
        return $vouchers;
    }
    
    /* HasSendAllVouchers
     * 
     * Checks if the cron has send all the vouchers generated
     * at specific time by specific owner.
     */
    public static final function HasSendAllVouchers($ownerid, $timecreated) {
        global $DB;
        
        $conditions = array(
            'issend'=>0,
            'ownerid'=>$ownerid,
            'timecreated'=>$timecreated
        );
        
        return ($DB->count_records('vouchers', $conditions) === 0);
    }
    
    /*
     * UpdateVoucher
     * 
     * Updates the voucher record
     */
    public static final function UpdateVoucher($voucher) {
        global $DB;
        
        return ($DB->update_record('vouchers', $voucher));
    }
    
    public static final function GetFilteredVouchers($ownerid, $fromDate, $tillDate) {
        global $DB;
        
        $params = array(
            'ownerid'   => is_null($ownerid) ? null : $ownerid,
            'fromdate'  => is_null($fromDate) ? null : strtotime($fromDate),
            'tilldate'  => is_null($tillDate) ? null : strtotime($tillDate)
        );
        $select = self::GetReportFilters($ownerid, $fromDate, $tillDate);
        
        $sql = 'SELECT * FROM {vouchers}';
        if (!empty($select)) {
            $sql .= ' WHERE ' . implode(' AND ', $select);
        }
        $sql .= ' ORDER BY timecreated';
        
        return $DB->get_records_sql($sql, $params);
    }
    
    public static final function GetCourseVouchers($ownerid, $fromDate, $tillDate) {
        global $DB;
        
        $vouchers = self::GetFilteredVouchers($ownerid, $fromDate, $tillDate);
        $courseVouchers = array();
        foreach($vouchers as $voucher) {
            
            $voucherCourses = $DB->get_records('voucher_courses', array('voucherid'=>$voucher->id));
            if (empty($voucherCourses)) {
                continue;
            } else {
                $courses = array();
                foreach($voucherCourses as $voucherCourse) {
                    $courses[] = $voucherCourse->courseid;
                }
            }
            
            $voucher->courses = $courses;
            $courseVouchers[] = $voucher;
            
        }
        
        return $courseVouchers;
    }
    
    public static final function GetCohortVouchers($ownerid, $fromDate, $tillDate) {
        global $DB;
        
        $vouchers = self::GetFilteredVouchers($ownerid, $fromDate, $tillDate);
        $cohortVouchers = array();
        foreach($vouchers as $voucher) {
            
            $voucherCohorts = $DB->get_records('voucher_cohorts', array('voucherid'=>$voucher->id));
            if (empty($voucherCohorts)) {
                continue;
            } else {
                $cohorts = array();
                foreach($voucherCohorts as $voucherCohort) {
                    $cohorts[] = $voucherCohort->cohortid;
                }
            }
            
            $voucher->cohorts = $cohorts;
            $cohortVouchers[] = $voucher;
            
        }
        
        return $cohortVouchers;
    }
    
    public static final function GetAllVouchers($ownerid, $fromDate, $tillDate) {
        global $DB;
        
        $vouchers = self::GetFilteredVouchers($ownerid, $fromDate, $tillDate);
        $allVouchers = array();
        foreach($vouchers as $voucher) {
            
            $voucherCourses = $DB->get_records('voucher_courses', array('voucherid'=>$voucher->id));
            if (!empty($voucherCourses)) {
                
                $courses = array();
                foreach($voucherCourses as $voucherCourse) {
                    $courses[] = $voucherCourse->courseid;
                }
                $voucher->courses = $courses;
                
            } else {
                
                $voucherCohorts = $DB->get_records('voucher_cohorts', array('voucherid'=>$voucher->id));
                if (!empty($voucherCohorts)) {
                    
                    $cohorts = array();
                    foreach($voucherCohorts as $cohort) {
                        $cohorts[] = $cohort->cohortid;
                    }
                    $voucher->cohorts = $cohorts;
                    
                } else {
                    continue;
                }
                
            }
            
            $allVouchers[] = $voucher;
        }
        
        return $allVouchers;
    }
    
    public static final function GetCohortById($cohortid) {
        global $DB;
        
        $cohort = $DB->get_record('cohort', array('id'=>$cohortid));
        return $cohort;
    }
    
    public static final function GetReportFilters($ownerid, $fromDate, $tillDate) {
        
        $select = array();
        if (!is_null($ownerid)) {
            $select[] = 'ownerid = :ownerid';
        }
        
        if (!is_null($fromDate) || !is_null($tillDate)) {
            
            if (!is_null($fromDate) && !is_null($tillDate)) {
                
                $select[] = 'timecreated BETWEEN :fromdate AND :tilldate';
                
            } elseif (!is_null($fromDate)) {
                
                $select[] = 'timecreated > :fromdate';
                
            } elseif (!is_null($tillDate)) {
                
                $select[] = 'timecreated < :tilldate';
                
            }
        }
        
        return $select;
    }

}