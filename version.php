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
 * Version information for block_coupon
 *
 * File         version.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
$plugin = new stdClass();
$plugin->version     = 2025120101;
$plugin->requires    = 2023042400; // YYYYMMDDHH (This is the release version for Moodle 4.2).
$plugin->component   = 'block_coupon';
$plugin->cron        = 0;
$plugin->maturity    = MATURITY_STABLE;
$plugin->release     = '4.4.4.1 (build 2025120101)';
$plugin->dependencies = [];
$plugin->supported = [405, 502];
