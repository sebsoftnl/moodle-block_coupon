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
 * display a logo
 *
 * File         logodisplay.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->dirroot . '/blocks/coupon/classes/settings.php');

$url = new moodle_url('/blocks/coupon/view/logodisplay.php');
$PAGE->set_url($url);

require_login(null, false);

_imgdisplay(block_coupon\helper::get_coupon_logo());

/**
 * display image
 * @param string $fn full path to file
 */
function _imgdisplay($fn) {
    if (file_exists($fn)) {
        $sizeinfo = getimagesize($fn);
        if ($sizeinfo) {
            list($w, $h, $itype, $tagwh) = $sizeinfo;
            $mime = $sizeinfo['mime'];
            header("Content-type: $mime");
            header("Content-Length: " . filesize($fn));
            $fp = fopen($fn, 'rb');
            if ($fp !== false) {
                fpassthru($fp);
                fclose($fp);
                exit;
            }
        }
    }
}
