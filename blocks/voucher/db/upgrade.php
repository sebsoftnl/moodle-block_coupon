<?php

function xmldb_block_voucher_upgrade($oldversion) {
    global $DB;
    
    $dbman = $DB->get_manager();

    if ($oldversion < 2013121101) {
        
        // Define table to edit
        $table = new xmldb_table('vouchers');

        $fieldsToUpdate = array();
        
        // Define fields to update/add
        $fieldsToUpdate[] = new xmldb_field('for_user_email', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'courseid');
        $fieldsToUpdate[] = new xmldb_field('for_user_name', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'for_user_email');
        $fieldsToUpdate[] = new xmldb_field('enrolperiod', XMLDB_TYPE_INTEGER, '8', null, null, null, null, 'for_user_name');
        $fieldsToUpdate[] = new xmldb_field('senddate', XMLDB_TYPE_INTEGER, '18', null, null, null, null, 'enrolperiod');
        $fieldsToUpdate[] = new xmldb_field('issend', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'senddate');
        $fieldsToUpdate[] = new xmldb_field('redirect_url', XMLDB_TYPE_CHAR, '256', null, null, null, null, 'issend');
        $fieldsToUpdate[] = new xmldb_field('email_body', XMLDB_TYPE_TEXT, null, null, null, null, null, 'redirect_url');

        // Update/add the fields
        foreach($fieldsToUpdate as $field) {

            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }

        // Voucher savepoint reached.
        upgrade_block_savepoint(true, 2013121101, 'voucher');

    }
    
    if ($oldversion < 2014012101) {

        // Define table to edit
        $table = new xmldb_table('vouchers');
        
        // Define fields to update/add
        $field = new xmldb_field('for_user_gender', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'for_user_name');
        
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Voucher savepoint reached.
        upgrade_block_savepoint(true, 2014012101, 'voucher');

    }
    
    /**
     * @global moodle_database $DB
     */
    if ($oldversion < 2014052301) {
        
        // First create a new table for voucher_courses
        $coursesTable = new xmldb_table('voucher_courses');
        $coursesTable->add_field('id', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $coursesTable->add_field('voucherid', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, null, null);
        $coursesTable->add_field('courseid', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, null, null);
        $coursesTable->add_key('id', XMLDB_KEY_PRIMARY, array('id'));
        
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        
        // Now fill it with data
        $vouchers = $DB->get_records_select('vouchers', 'courseid IS NOT NULL');
        foreach($vouchers as $voucher) {
            
            // Take courseid out and put new record in voucher_courses
            $voucherCourse = new stdClass();
            $voucherCourse->courseid = $voucher->courseid;
            $voucherCourse->voucherid = $voucher->id;
            
            $DB->insert_record('voucher_courses', $voucherCourse);
            
        }
        
        // And drop the old field
        $vouchersTable = new xmldb_table('vouchers');
        $courseidField = new xmldb_field('courseid', XMLDB_TYPE_INT, '18', null, null, null, null, 'ownerid');
        $dbman->drop_field($vouchersTable, $courseidField);
        
        // Voucher savepoint reached.
        upgrade_block_savepoint(true, 2014052301, 'voucher');
    }
    
    return true;
}
?>
