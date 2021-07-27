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
 * Install script for block_coupon
 *
 * File         install.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

/**
 * Install
 */
function xmldb_block_coupon_install() {
    global $DB, $CFG;

    // IF we have a custom logo, please place into Moodle's Filesystem.
    // This should NOT happen, but it could (e.g de-installation of a previous version).
    $logofile = $CFG->dataroot.'/coupon_logos/couponlogo.png';
    if (file_exists($logofile)) {
        // Store.
        $content = file_get_contents($logofile);
        \block_coupon\logostorage::store_from_content('couponlogo.png', $content);
        // Delete original.
        unlink($logofile);
        // ANd remove dir.
        remove_dir(dirname($logofile));
    }

}
