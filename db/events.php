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
 * Event handlers for block_coupon
 *
 * File         events.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/
defined('MOODLE_INTERNAL') || die;
$observers = array(
    array(
        'eventname'   => core\event\course_deleted::class,
        'callback'    => 'block_coupon\eventobservers::course_deleted',
        'internal' => false
    ),
    array(
        'eventname'   => core\event\cohort_deleted::class,
        'callback'    => 'block_coupon\eventobservers::cohort_deleted',
        'internal' => false
    ),
    array(
        'eventname'   => core\event\user_deleted::class,
        'callback'    => 'block_coupon\eventobservers::user_deleted',
        'internal' => false
    ),
);
