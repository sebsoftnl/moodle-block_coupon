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

//    /**
//     * Might not be neccesary, instead we'll probably use what we've 
//     * already got stored in the sessions.
//     */
//    public final static function GetVouchersByDate($date)
//    {
//        if (is_string($date))
//        {
//            $date = strtotime($date);
//        }
//        global $DB;
//        $rs = $DB->get_records('vouchergen_vouchers', array('created_on' => $date));
//        if (!$rs)
//        {
//            return null;
//        }
//        $v0 = reset($rs);
//        $store = $DB->get_record('jumbo_franchise_store', array('userid' => $v0->issuer_id), 'establ_name, storenumber, debtor_city');
//        if (!$store)
//        {
//            $store = new stdClass();
//            $store->store_name = 'UNKNOWN';
//            $store->store_number = '000';
//            $store->store_city = 'UNKNOWN';
//        }
//        foreach ($rs as &$voucher)
//        {
//            $voucher->store_name = $store->establ_name;
//            $voucher->store_number = $store->storenumber;
//            $voucher->store_city = $store->debtor_city;
//        }
//        return $rs;
//    }
    
    

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

}