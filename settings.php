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
 * general global plugin settings
 *
 * File         settings.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die('moodle_internal not defined');
if ($ADMIN->fulltree) {
    // Logo.
    $image = '<a href="http://www.sebsoft.nl" target="_new"><img src="' .
            $OUTPUT->image_url('logo', 'block_coupon') . '" /></a>&nbsp;&nbsp;&nbsp;';
    $donate = '<a href="https://customerpanel.sebsoft.nl/sebsoft/donate/intro.php" target="_new">' .
            '<img src="' . $OUTPUT->image_url('donate', 'block_coupon') . '" /></a>';
    $header = '<div class="block-coupon-logopromo">' . $image . $donate . '</div>';
    $settings->add(new admin_setting_heading('block_coupon_logopromo',
            get_string('promo', 'block_coupon'),
            get_string('promodesc', 'block_coupon', $header)));

    $settings->add(new admin_setting_configselect(
            'block_coupon/buttonclass',
            get_string('label:buttonclass', 'block_coupon'),
            get_string('label:buttonclass_desc', 'block_coupon'),
            'btn-primary',
            ['none' => '', 'btn btn-primary' => 'btn-primary', 'btn btn-secondary' => 'btn-secondary']
        ));

    $settings->add(new admin_setting_configcheckbox(
            'block_coupon/personalsendpdf',
            get_string('label:personalsendpdf', 'block_coupon'),
            get_string('label:personalsendpdf_help', 'block_coupon'),
            0
        ));

    $settings->add(new admin_setting_configcheckbox(
            'block_coupon/seperatepersonalcoupontab',
            get_string('label:seperatepersonalcoupontab', 'block_coupon'),
            get_string('label:seperatepersonalcoupontab_help', 'block_coupon'),
            0
        ));

    $settings->add(new admin_setting_configcheckbox(
            'block_coupon/use_alternative_email',
            get_string('label:use_alternative_email', 'block_coupon'),
            get_string('label:use_alternative_email_help', 'block_coupon'),
            0
        ));
    $settings->add(new admin_setting_configtext(
            'block_coupon/alternative_email',
            get_string('label:alternative_email', 'block_coupon'),
            get_string('label:alternative_email_help', 'block_coupon'),
            ''
        ));

    $roleoptions = \block_coupon\helper::get_role_menu();
    $settings->add(new admin_setting_configselect(
            'block_coupon/defaultrole',
            get_string('label:defaultrole', 'block_coupon'),
            get_string('label:defaultrole_help', 'block_coupon'),
            0,
            $roleoptions
        ));

    $maxcodelengthchoices = array(6 => 6, 8 => 8, 16 => 16, 32 => 32);
    $settings->add(new admin_setting_configselect(
            'block_coupon/coupon_code_length',
            get_string('label:coupon_code_length', 'block_coupon'),
            get_string('label:coupon_code_length_help', 'block_coupon'),
            16,
            $maxcodelengthchoices
        ));

    $generatorchoices = array(
        1 => get_string('numeric', 'block_coupon'),
        2 => get_string('letters', 'block_coupon'),
        4 => get_string('capitals', 'block_coupon'),
    );
    $settings->add(new admin_setting_configmulticheckbox(
            'block_coupon/coupon_code_flags',
            get_string('label:coupon_code_flags', 'block_coupon'),
            get_string('label:coupon_code_flags_help', 'block_coupon'),
            [1 => 1, 2 => 1, 4 => 1],
            $generatorchoices
        ));

    $settings->add(new admin_setting_configtext(
            'block_coupon/max_coupons',
            get_string('label:max_coupons', 'block_coupon'),
            get_string('label:max_coupons_desc', 'block_coupon'),
            50, PARAM_INT
        ));

    // Display "help" in block.
    $settings->add(new admin_setting_configcheckbox(
            'block_coupon/displayregisterhelp',
            get_string('label:displayregisterhelp', 'block_coupon'),
            get_string('label:displayregisterhelp_help', 'block_coupon'),
            0
        ));

    $settings->add(new admin_setting_configcheckbox(
            'block_coupon/displayinputhelp',
            get_string('label:displayinputhelp', 'block_coupon'),
            get_string('label:displayinputhelp_help', 'block_coupon'),
            0
        ));

    $settings->add(new admin_setting_configcheckbox(
            'block_coupon/useloginlayoutonsignup',
            get_string('label:useloginlayoutonsignup', 'block_coupon'),
            get_string('label:useloginlayoutonsignup_help', 'block_coupon'),
            1
        ));
    $settings->add(new admin_setting_configcheckbox(
            'block_coupon/forceenableemailregistration',
            get_string('label:forceenableemailregistration', 'block_coupon'),
            get_string('label:forceenableemailregistration_help', 'block_coupon'),
            0
    ));

    // Settings for request users.
    $settings->add(new admin_setting_heading('block_coupon_requestusersettings',
            get_string('requestusersettings', 'block_coupon'),
            get_string('requestusersettings_desc', 'block_coupon', $header)));
    $settings->add(new admin_setting_configcheckbox(
            'block_coupon/enablemycouponsforru',
            get_string('label:enablemycouponsforru', 'block_coupon'),
            get_string('label:enablemycouponsforru_help', 'block_coupon'),
            1
        ));
    $settings->add(new admin_setting_configcheckbox(
            'block_coupon/enablemyprogressforru',
            get_string('label:enablemyprogressforru', 'block_coupon'),
            get_string('label:enablemyprogressforru_help', 'block_coupon'),
            1
        ));

    // Task settings.
    $settings->add(new admin_setting_heading('block_coupon_tasksettings',
            get_string('tasksettings', 'block_coupon'),
            get_string('tasksettings_desc', 'block_coupon', $header)));
    $settings->add(new admin_setting_configcheckbox(
            'block_coupon/enablecleanup',
            get_string('label:enablecleanup', 'block_coupon'),
            get_string('label:enablecleanup_help', 'block_coupon'),
            0
        ));
    $settings->add(new admin_setting_configduration(
            'block_coupon/cleanupage',
            get_string('label:cleanupage', 'block_coupon'),
            get_string('label:cleanupage_help', 'block_coupon'),
            30 * 86400, 86400
        ));

    // Settings related to coursegrouping.
    $settings->add(new admin_setting_configcheckbox(
            'block_coupon/groupingselectactiveonly',
            get_string('label:groupingselectactiveonly', 'block_coupon'),
            get_string('label:groupingselectactiveonly_help', 'block_coupon'),
            0
        ));

    // Template settings.
    $settings->add(new admin_setting_heading('block_coupon_templatesettings',
            get_string('templatesettings', 'block_coupon'), ''));

    $settings->add(new admin_setting_configcheckbox('block_coupon/showposxy',
        get_string('showposxy', 'block_coupon'),
        get_string('showposxy_desc', 'block_coupon'),
        1));

    $settings->add(new \block_coupon\admin_setting_link('block_coupon/managetemplates',
        get_string('managetemplates', 'block_coupon'), get_string('managetemplatesdesc', 'block_coupon'),
        get_string('managetemplates', 'block_coupon'), new moodle_url('/blocks/coupon/view/templates/index.php'), ''));

    $settings->add(new \block_coupon\admin_setting_link('block_coupon/uploadimage',
        get_string('uploadimage', 'block_coupon'), get_string('uploadimagedesc', 'block_coupon'),
        get_string('uploadimage', 'block_coupon'), new moodle_url('/blocks/coupon/view/templates/upload_image.php'), ''));

    // Information fields, to be displayed above each form.
    $settings->add(new admin_setting_heading('block_coupon_textsettings',
            get_string('textsettings', 'block_coupon'),
            get_string('textsettings_desc', 'block_coupon', $header)));
    $settings->add(new admin_setting_configtext(
            'block_coupon/info_coupon_type',
            get_string('label:info_coupon_type', 'block_coupon'),
            get_string('label:info_desc', 'block_coupon'),
            ''
        ));
    $settings->add(new admin_setting_configtext(
            'block_coupon/info_coupon_course',
            get_string('label:info_coupon_course', 'block_coupon'),
            get_string('label:info_desc', 'block_coupon'),
            ''
        ));
    $settings->add(new admin_setting_configtext(
            'block_coupon/info_coupon_course_groups',
            get_string('label:info_coupon_course_groups', 'block_coupon'),
            get_string('label:info_desc', 'block_coupon'),
            ''
        ));
    $settings->add(new admin_setting_configtext(
            'block_coupon/info_coupon_cohorts',
            get_string('label:info_coupon_cohorts', 'block_coupon'),
            get_string('label:info_desc', 'block_coupon'),
            ''
        ));
    $settings->add(new admin_setting_configtext(
            'block_coupon/info_coupon_cohort_courses',
            get_string('label:info_coupon_cohort_courses', 'block_coupon'),
            get_string('label:info_desc', 'block_coupon'),
            ''
        ));
    $settings->add(new admin_setting_configtext(
            'block_coupon/info_coupon_confirm',
            get_string('label:info_coupon_confirm', 'block_coupon'),
            get_string('label:info_desc', 'block_coupon'),
            ''
        ));
    $settings->add(new admin_setting_configtext(
            'block_coupon/info_imageupload',
            get_string('label:info_imageupload', 'block_coupon'),
            get_string('label:info_desc', 'block_coupon'),
            ''
        ));

}
