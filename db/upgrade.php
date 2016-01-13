<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Upgrade script for block_coupon
 *
 * File         upgrade.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade
 *
 * @param int $oldversion old (current) plugin version
 * @return boolean
 */
function xmldb_block_coupon_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2016011000) {
        // Add activity completion table.
        $table = new xmldb_table('block_coupon_errors');
        $table->add_field('id', XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('couponid', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null, 'id');
        $table->add_field('errortype', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null, 'couponid');
        $table->add_field('errormessage', XMLDB_TYPE_TEXT, 'medium', null, XMLDB_NOTNULL, null, null, 'errortype');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, null, null, 'errormessage');
        // Add KEYS.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        // Add INDEXES.
        $table->add_index('couponid', XMLDB_INDEX_NOTUNIQUE, array('couponid'));

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // We shall add indexes to link tables!
        $table = new xmldb_table('block_coupon_cohorts');
        $index = new xmldb_index('couponid', XMLDB_INDEX_NOTUNIQUE, array('couponid'));
        if ($dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        $index = new xmldb_index('cohortid', XMLDB_INDEX_NOTUNIQUE, array('cohortid'));
        if ($dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $table = new xmldb_table('block_coupon_groups');
        $index = new xmldb_index('couponid', XMLDB_INDEX_NOTUNIQUE, array('couponid'));
        if ($dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        $index = new xmldb_index('groupid', XMLDB_INDEX_NOTUNIQUE, array('groupid'));
        if ($dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $table = new xmldb_table('block_coupon_courses');
        $index = new xmldb_index('couponid', XMLDB_INDEX_NOTUNIQUE, array('couponid'));
        if ($dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        $index = new xmldb_index('courseid', XMLDB_INDEX_NOTUNIQUE, array('courseid'));
        if ($dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Block_tped savepoint reached.
        upgrade_block_savepoint(true, 2016011000, 'coupon');

    }
    return true;
}