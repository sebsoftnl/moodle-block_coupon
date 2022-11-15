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
 * Language file for block_coupon, EN
 *
 * File         block_coupon.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// Capabilities.
$string['coupon:addinstance'] = 'Add a new Coupon block';
$string['coupon:administration'] = 'Administrate the Coupon block';
$string['coupon:generatecoupons'] = 'Generate a new coupon';
$string['coupon:inputcoupons'] = 'Use a coupon to subscribe';
$string['coupon:myaddinstance'] = 'Add a new Coupon block to the My Moodle page';
$string['coupon:viewreports'] = 'View Coupon reports (for my owned coupons)';
$string['coupon:viewallreports'] = 'View Coupon reports (for all coupons)';
$string['coupon:extendenrolments'] = 'Generate coupons to extend course enrolments';
$string['error:sessions-expired'] = 'Your session expired.';
$string['promo'] = 'Coupon plugin for Moodle';
$string['promodesc'] = 'This plugin is written by Sebsoft Managed Hosting & Software Development
(<a href=\'http://www.sebsoft.nl/\' target=\'_new\'>http://sebsoft.nl</a>).<br /><br />
{$a}<br /><br />';

// DEFAULT.
$string['blockname'] = 'Coupon';
$string['pluginname'] = 'Coupon';

// Headers.
$string['heading:administration'] = 'Manage';
$string['heading:generatecoupons'] = 'Generate coupons';
$string['heading:inputcoupons'] = 'Input Coupon';
$string['heading:label_instructions'] = 'Instructions';
$string['heading:coupon_type'] = 'Type of coupon';
$string['heading:input_coupon'] = 'Input coupon';
$string['heading:general_settings'] = 'Last settings';
$string['heading:input_cohorts'] = 'Select cohort(s)';
$string['heading:input_course'] = 'Select course(s)';
$string['heading:input_groups'] = 'Select groups';
$string['heading:imageupload'] = 'Upload image';
$string['heading:info'] = 'Info';
$string['heading:courseandvars'] = 'Select coupon variables, course(s) and course enrolment variables';
$string['heading:coursegroups'] = 'Connect course groups to selected courses';
$string['heading:cohortandvars'] = 'Select coupon variables, cohort(s) and course enrolment variables';
$string['heading:cohortlinkcourses'] = 'Link courses to cohort(s)';
$string['heading:generatormethod'] = 'Select how you want to generate the coupons';
$string['heading:csvForm'] = 'CSV settings';
$string['heading:amountForm'] = 'Amount settings';
$string['heading:manualForm'] = 'Manual settings';

// Errors.
$string['error:nopermission'] = 'You have no permission to do this';
$string['error:required'] = 'This field is required.';
$string['error:numeric_only'] = 'This field must be numeric.';
$string['error:invalid_email'] = 'Please enter a valid email adress.';
$string['error:invalid_coupon_code'] = 'You have entered an invalid coupon code.';
$string['error:coupon_already_used'] = 'The coupon with this code has already been used.';
$string['error:coupon_reserved'] = 'The coupon with this code has been reserved for an other user.';
$string['error:unable_to_enrol'] = 'An error occured while trying to enrol you in the new course. Please contact support.';
$string['error:missing_course'] = 'The course linked to this coupon does not exist anymore. Please contact support.';
$string['error:cohort_sync'] = 'An error occured while trying to synchronize the cohorts. Please contact support.';
$string['error:plugin_disabled'] = 'The cohort_sync plugin has been disabled. Please contact support.';
$string['error:missing_cohort'] = 'The cohort(s) linked to this coupon does not exist anymore. Please contact support.';
$string['error:missing_group'] = 'The group(s) linked to this coupon does not exist anymore. Please contact support.';
$string['error:coupon_amount_too_high'] = 'Please enter an amonut between {$a->min} and {$a->max}.';
$string['error:alternative_email_required'] = 'If you have checked \'use alternative email\' this field is required.';
$string['error:alternative_email_invalid'] = 'If you have checked \'use alternative email\' this field should contain a valid email address.';
$string['error:course-not-found'] = 'The course could not be found.';
$string['error:course-coupons-not-copied'] = 'An error occured while trying to copy coupon-courses to the new coupon_courses table. Please contact support.';
$string['error:wrong_code_length'] = 'Please enter a number between 6 and 32.';
$string['error:no_coupons_submitted'] = 'None of your coupons have been used yet.';
$string['error:wrong_image_size'] = 'The uploaded background does not have the required size. Please upload an image with a ratio of 210 mm by 297 mm.';
$string['error:moodledata_not_writable'] = 'Your moodledata/coupon_logos folder is not writable. Please fix your permissions.';
$string['error:wrong_doc_page'] = 'You are trying to access a page that does not exist.';

// Success strings.
$string['success:coupon_used'] = 'Coupon used - You can now access the course(s)';
$string['success:uploadimage'] = 'Your new coupon image has been uploaded.';

// URL texts.
$string['url:generate_coupons'] = 'Generate Coupon';
$string['url:api_docs'] = 'API Documentation';
$string['url:uploadimage'] = 'Change coupon image';
$string['url:input_coupon'] = 'Input Coupon';
$string['url:view_reports'] = 'View reports';
$string['url:view_unused_coupons'] = 'View unused coupons';

// Form Labels.
$string['label:coupon_type'] = 'Generate coupon(s) for';
$string['label:coupon_email'] = 'Email address';
$string['label:coupon_amount'] = 'Amount of coupons';
$string['label:type_course'] = 'Course enrolment';
$string['label:type_cohorts'] = 'Enrolment in cohort(s)';
$string['label:coupon_connect_course'] = 'Add course(s)';
$string['label:coupon_connect_course_help'] = 'Select all courses you wish to add to the cohort.
    <br /><b><i>Note: </i></b>All users who are already enrolled in this cohort will also be enrolled in the selected courses!';
$string['label:connected_courses'] = 'Connected course(s)';
$string['label:no_courses_connected'] = 'There are no courses connected to this cohort.';
$string['label:coupon_courses'] = 'Course(s)';
$string['label:coupon_courses_help'] = 'Select the courses your students should be enrolled in';
$string['label:coupon_role'] = 'Role';
$string['label:coupon_role_help'] = 'Select the role with which coupons will be configured or leave empty for the configured default (usually student).';
$string['label:coupon_cohorts'] = 'Cohort(s)';
$string['label:cohort'] = 'Cohort';
$string['label:coupon_code'] = 'Coupon Code';
$string['label:coupon_code_help'] = 'The coupon code is the unique code which is linked to each individual coupon. You can find this code on your coupon.';
$string['label:enter_coupon_code'] = 'Please enter your coupon code here';
$string['label:alternative_email'] = 'Alternative email';
$string['label:alternative_email_help'] = 'Send coupons by default to this email address.';
$string['label:use_alternative_email'] = 'Send to alternative email';
$string['label:use_alternative_email_help'] = 'When checked it will by default use the email address provided in the Alternative email field.';
$string['label:max_coupons'] = 'Maximum coupons';
$string['label:max_coupons_desc'] = 'Amount of coupons that can be created in one time.';
$string['label:coupon_code_length'] = 'Code length';
$string['label:coupon_code_length_help'] = 'Amount of characters of the coupon code.';

$string['label:selected_groups'] = 'Selected group(s)';
$string['label:selected_courses'] = 'Selected courses';
$string['label:selected_cohort'] = 'Selected cohort(s)';
$string['label:api_enabled'] = 'Enable API';
$string['label:api_enabled_desc'] = 'The Coupon API grants the possibility to generate coupons from an external system.';
$string['label:api_user'] = 'API User';
$string['label:api_user_desc'] = 'The username that can be used to generate a coupon using the API.';
$string['label:api_password'] = 'API Password';
$string['label:api_password_desc'] = 'The password that can be used to generate a coupon using the API.';
$string['label:generate_pdfs'] = 'Generate seperate PDF\'s';
$string['label:generate_pdfs_help'] = 'You can select here if you want to receive your coupons in either a single file or each coupon in a separate PDF file.';
$string['label:info_desc'] = 'The information shown above the form.';
$string['label:info_coupon_type'] = 'Information on page: Select coupon type';
$string['label:info_coupon_course'] = 'Information on page: Select course';
$string['label:info_coupon_cohorts'] = 'Information on page: Select cohorts';
$string['label:info_coupon_course_groups'] = 'Information on page: Select course groups';
$string['label:info_coupon_cohort_courses'] = 'Information on page: Cohort courses';
$string['label:info_coupon_confirm'] = 'Information on page: Confirm coupon';
$string['label:info_imageupload'] = 'Information on page: Upload image';
$string['label:image'] = 'Coupon background';
$string['label:image_desc'] = 'Background to be placed in the generated coupons';
$string['label:current_image'] = 'Current Coupon background';
$string['label:coupon_groups'] = 'Add group(s)';
$string['label:coupon_groups_help'] = 'Select the groups you wish your users to be enrolled in upon enrolment in the courses.';
$string['label:no_groups_selected'] = 'There are no groups connected to these courses yet.';
$string['label:coupon_type_help'] = 'The Coupons will be generated based on either the course or one or more cohorts.';
$string['label:coupon_email_help'] = 'This is the email address the generated coupons will be send to.';
$string['label:coupon_amount_help'] = 'This is the the amount of coupons that will be generated. Please use this field OR the field recipients, not both.';
$string['label:coupon_cohorts_help'] = 'Select the one or more cohorts your users will be enrolled in.';
$string['label:coupon_courses_help'] = 'Select the courses your users will be enrolled in.';
$string['label:renderqrcode'] = 'Use QR Code?';
$string['label:renderqrcode_help'] = 'Enable or disable this option to include QR codes in the generated PDF.';
// Buttons.
$string['button:next'] = 'Next';
$string['button:save'] = 'Generate Coupons';
$string['button:submit_coupon_code'] = 'Submit Coupon';

// View strings.
$string['view:generate_coupon:title'] = 'Generate Coupon';
$string['view:generate_coupon:heading'] = 'Generate Coupon';
$string['view:generator:course:heading'] = 'Generate course coupon(s)';
$string['view:generator:course:title'] = 'Generate course coupon(s)';
$string['view:generator:cohort:heading'] = 'Generate cohort coupon(s)';
$string['view:generator:cohort:title'] = 'Generate cohort coupon(s)';
$string['view:reports:heading'] = 'Report - Coupon based progress';
$string['view:reports:title'] = 'Report - Coupon based progress';
$string['view:reports-used:title'] = 'Report - Used Coupons';
$string['view:reports-used:heading'] = 'Report - Used Coupons';
$string['view:reports-unused:title'] = 'Report - Unused Coupons';
$string['view:reports-unused:heading'] = 'Report - Unused Coupons';
$string['view:reports-personal:title'] = 'Report - Personalised Coupons';
$string['view:reports-personal:heading'] = 'Report - Personalised Coupons';
$string['view:api:heading'] = 'Coupon API';
$string['view:api:title'] = 'Coupon API';
$string['view:api_docs:heading'] = 'Coupon API Documentation';
$string['view:api_docs:title'] = 'Coupon API Documentation';
$string['view:input_coupon:title'] = 'Input Coupon';
$string['view:input_coupon:heading'] = 'Input Coupon';
$string['view:uploadimage:title'] = 'Upload coupon background';
$string['view:uploadimage:heading'] = 'Upload a new coupon background';
$string['course'] = 'course';
$string['cohort'] = 'cohort';
$string['missing_config_info'] = 'Put your extra information here - to be set up in the global configuration of the block.';
$string['pdf_generated'] = 'The coupons have been attached to this email in PDF files.<br /><br />';
$string['and'] = 'and';

$string['coupons_generated'] = '<p>Your coupon(s) have been generated.<br/>
You should have received an e-mail containing the link to download the generated coupons.<br/>
You can also choose to download your coupons directly by clicking {$a}.</p>';
$string['coupons_generated_codes_only'] = '<p>Your couponcode(s) have been generated.<br/>
Because you have opted to only generate the codes, you will not recieve an email<br/>
You can use the overview for (un)used coupons with a specific filter on the batch ID to download an overview of the generated codes.</p>';
$string['coupons_sent'] = 'Your coupon(s) have been generated. Within several minutes you will receive an email with the Coupons in the attachments.';
$string['coupons_ready_to_send'] = 'Your coupon(s) has/have been generated and will be send at the entered date.<br />
    You will receive a confirmation email message when all the coupons have been sent.';

// Report.
$string['report:status_not_started'] = 'Course not started yet';
$string['report:status_started'] = 'Course started';
$string['report:status_completed'] = 'Course completed';
$string['report:dateformat'] = '%d-%m-%Y %H:%M:%S';
$string['report:dateformatymd'] = '%d-%m-%Y';
$string['report:heading:user'] = 'User';
$string['report:heading:couponcode'] = 'Used code';
$string['report:heading:coursename'] = 'Course name';
$string['report:heading:cohortname'] = 'Cohort(s)';
$string['report:heading:coursetype'] = 'Course type';
$string['report:heading:status'] = 'Status';
$string['report:heading:datestart'] = 'Startdate';
$string['report:heading:datecomplete'] = 'Date completed';
$string['report:heading:grade'] = 'Grade';
$string['report:owner'] = 'Owner';
$string['report:senddate'] = 'Send date';
$string['report:enrolperiod'] = 'Enrolment period';
$string['report:coupon_code'] = 'Subscription code';
$string['report:cohorts'] = 'Cohort';
$string['report:issend'] = 'Is sent';
$string['report:immediately'] = 'Immediately';
$string['report:for_user_email'] = 'Planned for';
$string['report:for_user_name'] = 'Recipient name';
$string['report:timeexpired'] = 'Expires';
$string['str:mandatory'] = 'Mandatory';
$string['str:optional'] = 'Optional';

$string['download-sample-csv'] = 'Download sample CSV file';
$string['pdf:titlename'] = 'Moodle Coupon';
$string['pdf-meta:title'] = 'Moodle Coupon';
$string['pdf-meta:subject'] = 'Moodle Coupon';
$string['pdf-meta:keywords'] = 'Moodle Coupon';
$string['error:sessions-expired'] = 'Your session has been expired. Please try again.';
$string['label:coupon_recipients'] = 'Recipients';
$string['error:recipients-extension'] = 'You can only upload .csv files.';
$string['error:coupon_amount-recipients-both-set'] = 'Please specify a number of coupons to generate OR a csv list of recipients.';
$string['label:coupon_recipients_help'] = 'With this field you can upload a csv file with users.';
$string['label:coupon_recipients_txt'] = 'Recipients';
$string['label:coupon_recipients_txt_help'] = 'In this field you can make your final changes to the uploaded csv file.';
$string['error:coupon_amount-recipients-both-unset'] = 'Either this field or the field Recipients must be set.';
$string['label:email_body'] = 'Email message';
$string['label:email_body_help'] = 'The email message that will be send to the recipients of the coupons.';
$string['label:redirect_url'] = 'Redirect URL';
$string['label:redirect_url_help'] = 'The destination users will be send to after entering their coupon code.';
$string['label:enrolment_period'] = 'Enrolment period';
$string['label:enrolment_period_help'] = 'Period (in days) the user will be enrolled in the courses. If set to 0 no end will be issued.';
$string['label:date_send_coupons'] = 'Send date';
$string['label:date_send_coupons_help'] = 'Date the coupons will be send to the recipient(s).';
$string['label:showform'] = 'Generator options';
$string['showform-csv'] = 'I want to create coupons using a CSV with recipients';
$string['showform-manual'] = 'I want to manually configure the recipients';
$string['showform-amount'] = 'I want to create an arbitrary amount of coupons';
$string['error:recipients-max-exceeded'] = 'Your csv file has exceeded the maximum of 10.000 coupon users. Please limit it.';
$string['error:recipients-columns-missing'] = 'The file could not be validated. Are you sure you entered the right columns and seperator?<br/>
The following columns <i>must</i> be present in the first row with the name exactly as given: {$a}';
$string['error:recipients-invalid'] = 'The file could not be validated. Are you sure you entered the right columns and seperator?';
$string['error:recipients-empty'] = 'Please enter at least one user.';
$string['error:recipients-email-invalid'] = 'The email address {$a->email} is invalid. Please fix it in the csv file.';
$string['coupon_recipients_desc'] = 'The following columns are required to be present in the uploaded CSV, regardless of order: E-mail, Gender, Name.<br/>
For every given person in the CSV, a coupon is generated and emailed to the user.<br/>
Please take note these coupons will be created a-synchronous by a background task; <i>not</i> instantly.
This is because the process of generating coupons may be quite lengthy, especially for a large amount of users.';
$string['report:download-excel'] = 'Download unused coupons';

$string['page:generate_coupon.php:title'] = 'Generate coupons';
$string['page:generate_coupon_step_two.php:title'] = 'Generate coupons';
$string['page:generate_coupon_step_three.php:title'] = 'Generate coupons';
$string['page:generate_coupon_step_four.php:title'] = 'Generate coupons';
$string['page:generate_coupon_step_five.php:title'] = 'Generate coupons';
$string['page:unused_coupons.php:title'] = 'Unused coupons';
$string['th:owner'] = 'Owner';
$string['th:usedby'] = 'Claimed by';
$string['th:claimedon'] = 'Claimed on';
$string['th:senddate'] = 'Send date';
$string['th:enrolperiod'] = 'Enrolperiod';
$string['th:submission_code'] = 'Subscription code';
$string['th:cohorts'] = 'Cohort';
$string['th:groups'] = 'Group(s)';
$string['th:course'] = 'Course';
$string['th:issend'] = 'Sent?';
$string['th:immediately'] = 'Immediately';
$string['th:for_user_email'] = 'Planned for';
$string['th:roleid'] = 'Role';
$string['th:batchid'] = 'Batch';
$string['th:fullname'] = 'Fullname';

$string['tab:wzcoupons'] = 'Generate coupon(s)';
$string['tab:wzcouponimage'] = 'Template image';
$string['tab:apidocs'] = 'API Docs';
$string['tab:report'] = 'Progress report';
$string['tab:unused'] = 'Unused coupons';
$string['tab:used'] = 'Used coupons';
$string['task:sendcoupons'] = 'Send scheduled coupons';

// Mails.
$string['confirm_coupons_sent_subject'] = 'All Coupons have been sent';
$string['confirm_coupons_sent_body'] = '
Hello,<br /><br />

We\'d like to inform you that all the coupons created by you on {$a->timecreated} have been sent.<br /><br />

With kind regards,<br /><br />

Moodle administrator';

$string['days_access'] = '{$a} days of';
$string['unlimited_access'] = 'unlimited';
$string['default-coupon-page-template-main'] = 'With this coupon you can activate access to the e-learning module. You have {accesstime} access to this module.

Please use the following coupon code to activate access.

{coupon_code}';
$string['default-coupon-page-template-botleft'] = '<ol>
<li>Sign up at {site_url}</li>
<li>You will receive an email with the confirmation url. Click on the url to activate your account.</li>
<li>Enter your coupon code in the Moodle Coupon block</li>
<li>Happy learning!</li>
</ol>';
$string['default-coupon-page-template-botright'] = '<ol>
<li>Log in at {site_url}</li>
<li>Enter your coupon code in the Moodle Coupon block</li>
<li>Happy learning!</li>
</ol>';

$string['coupon_mail_content'] = '<p>Dear {$a->fullname},</p>
<p>You are receiving this message because there have been newly generated coupons.<br/>
The coupons are available for download on the e-learning environment.<br /><br />
Please click {$a->downloadlink} to get your coupons</p>
<p>With kind regards,<br /><br />
{$a->signoff}</p>';

$string['coupon_mail_csv_content'] = '
Dear ##to_gender## ##to_name##,<br /><br />

You have recently been enrolled for our training ##course_fullnames##.
During the course you have access to our Online Learning Environment: ##site_name##.<br /><br />

In this environment, apart from the course materials, you will have the possibility to network with fellow students.
The course will start with a number of preparation assignments, we kindly request to take a look at them
at the latest 3 (work)days before the course starts.
Both you and the teacher can then decently prepare for the course.<br /><br />

All course materials will be accessible for you, at the very latest 4 days before the course starts.
It can always happen that the teacher requests extra materials to be added at a later time, for example
after a physical session. If this happens, you will be abe to see this in the learning environment
During meetings you will not receive any printed lesson materials, we advise you to bring a laptop and/or tablet.<br /><br />

The coupon code you require to enrol is: ##submission_code##<br/><br/>

This coupon is personal and unique, and gives access to the appropriate courses for your education.
Please read the instructions on the coupon carefully.<br /><br />

If you have any questions regarding creating an account or find any other problems, you can contact the helpdesk.
Information can be found on out Learning Environment.
When nobody is available to answer your question, please leave your name, e-mailaddress and phonenumber behind and we will get back to you as
soon as possible.<br /><br />

We wish you good luck on the course.<br /><br />

With kind regards,<br /><br />

##site_name##';

$string['coupon_mail_csv_content_cohorts'] = '
Dear ##to_gender## ##to_name##,<br /><br />

You have recently been enrolled for our training **PLEASE FILL IN MANUALLY**.
During the course you have access to our Online Learning Environment: ##site_name##.<br /><br />

In this environment, apart from the course materials, you will have the possibility to network with fellow students.
The course will start with a number of preparation assignments, we kindly request to take a look at them
at the latest 3 (work)days before the course starts.
Both you and the teacher can then decently prepare for the course.<br /><br />

All course materials will be accessible for you, at the very latest 4 days before the course starts.
It can always happen that the teacher requests extra materials to be added at a later time, for example
after a physical session. If this happens, you will be abe to see this in the learning environment
During meetings you will not receive any printed lesson materials, we advise you to bring a laptop and/or tablet.<br /><br />

The coupon code you require to enrol is: ##submission_code##<br/><br/>

This coupon is personal and unique, and gives access to the appropriate courses for your education.
Please read the instructions on the coupon carefully.<br /><br />

If you have any questions regarding creating an account or find any other problems, you can contact the helpdesk.
Information can be found on our Learning Environment.
When nobody is available to answer your question, please leave your name, e-mailaddress and phonenumber behind and we will get back to you as
soon as possible.<br /><br />

We wish you good luck on the course.<br /><br />

With kind regards,<br /><br />

##site_name##';

$string['coupon_mail_subject'] = 'Moodle Coupon generated';
$string['th:action'] = 'Action(s)';
$string['action:coupon:delete'] = 'Delete coupon';
$string['action:coupon:delete:confirm'] = 'Are you sure you wish to delete this coupon? This cannot be undone!';
$string['coupon:deleted'] = 'Coupon has been deleted';

$string['textsettings'] = 'Text settings';
$string['textsettings_desc'] = 'Here you can configure custom texts to be displayed by various wizard screens for the coupon generator';
$string['task:cleanup'] = 'Cleaning up unused old coupons';
$string['tasksettings'] = 'Task settings';
$string['tasksettings_desc'] = '';
$string['label:enablecleanup'] = 'Enable cleaning up unused coupons?';
$string['label:enablecleanup_help'] = 'Check this option to automatically clean (remove) unused coupons';
$string['label:cleanupage'] = 'Maximum age?';
$string['label:cleanupage_help'] = 'Enter the maximum age of an unused coupon before it will be removed';

$string['coupon:send:fail'] = 'Sending e-mail failed! Reason: {$a}';
$string['view:errorreport:heading'] = 'Report - Coupon errors';
$string['view:errorreport:title'] = 'Report - Coupon errors';
$string['report:heading:coupon'] = 'Coupon';
$string['report:heading:type'] = 'Type';
$string['report:heading:errortype'] = 'Type';
$string['report:heading:errormessage'] = 'Error';
$string['report:heading:timecreated'] = 'Date';
$string['report:heading:action'] = 'Action(s)';
$string['action:error:delete'] = 'Delete error';
$string['tab:errors'] = 'Error reports';
$string['enrolperiod:indefinite'] = '<i>Indefinite</i>';
$string['enrolperiod:extension'] = 'for the duration of {$a}';

$string['label:defaultrole'] = 'Default role';
$string['label:defaultrole_help'] = 'This will be the default role with which users will get assigned when claiming a coupon';

$string['default-coupon-page-template-main'] = 'With this coupon you can activate access to the following e-learning module(s):<br/>
{courses} ({role})<br/><br/>
You have {accesstime} access to this module.<br/><br/>
Please use the following coupon code to activate access<br/>
{coupon_code}';

$string['view:cleanup:title'] = 'Clean coupons';
$string['view:cleanup:heading'] = 'Clean coupons';
$string['coupon:cleanup:heading'] = 'Clean coupons';
$string['coupon:cleanup:info'] = 'Use this form to configure coupons to delete from the system.<br/>
<b>Warning:</b> This process will <i>remove</i> coupons from the system, there is no way to get them back when this cleaning process has completed';
$string['coupon:timeframe'] = 'Type';
$string['coupon:used'] = 'Removal';
$string['coupon:used:all'] = 'All coupons';
$string['coupon:used:yes'] = 'Used coupons only';
$string['coupon:used:no'] = 'Unused coupons only';
$string['coupon:type'] = 'Type';
$string['coupon:type:all'] = 'All';
$string['timebefore'] = 'Created before';
$string['timeafter'] = 'Created after';
$string['tab:cleaner'] = 'Cleanup';
$string['logo:none'] = 'Do not use a logo';
$string['logo:default'] = 'Default logo';
$string['url:couponsignup'] = 'Signup with a coupon code';
$string['url:managelogos'] = 'Manage coupon images';
$string['select:logo'] = 'Select template logo';
$string['select:logo_help'] = 'Select a template logo.<br/>This will only be used when a PDF will be generated for coupons.';
$string['select:logo:desc'] = 'Select a template logo.<br/>This will only be used when a PDF will be generated for coupons.';
$string['logomanager:desc'] = 'Use the logomanager below to manage the logos that can be used on the coupon PDFs.<br/>
Beware what type of images you upload!<br/>
You <i>should</i> only be using 300 DPI images on A4 format (2480 x 3508 pixels).<br/>
<i>Any</i> other image sizes will probably lead to unwanted side effects.
';
$string['coupon:extendenrol'] = 'Enrolment extension coupons';
$string['error:validate-courses'] = 'Course validation errors:
{$a}';
$string['signup:login'] = 'I already have an account and want to login';
$string['signup:success'] = 'You have signed up and will now be redirected to the login page.<br/>
Please validate you have actually been granted access to the course after logging in.';
$string['label:users'] = 'User(s)';
$string['label:extendusers:desc'] = 'Select one or more users below.<br/>
You will only see users that have <i>manual</i> enrolment and have enddate set to their enrolments.';
$string['label:mailusers'] = 'Send coupons via e-mail to selected course participants.';
$string['label:extendperiod'] = 'Enrolment extension period';
$string['label:extendperiod:desc'] = 'Configure the optional extension period below. If <i>not</i> enabled or set to 0, enrolment will turn into indefinite enrolment';
$string['view:extendenrolment:title'] = 'Coupon: enrolment extensions';
$string['view:extendenrolment:heading'] = 'Coupon: enrolment extensions';
$string['view:extendenrolment_step1:title'] = 'Extend enrolments: select course(s)';
$string['view:extendenrolment_step1:heading'] = 'Extend enrolments: select course(s)';
$string['view:extendenrolment_step2:title'] = 'Extend enrolments: select users';
$string['view:extendenrolment_step2:heading'] = 'Extend enrolments: select users';
$string['view:extendenrolment_step3:title'] = 'Extend enrolments: confirm';
$string['view:extendenrolment_step3:heading'] = 'Extend enrolments: confirm';
$string['extendenrol:abort-no-users'] = 'Error: no users have been found for which enrolments can be extended<br/>
Users may either all be enrolled indefinitely or no users are found for this course / these courses.';

$string['coupon:type:course'] = 'Course enrolment';
$string['coupon:type:cohort'] = 'Cohort enrolment';
$string['coupon:type:enrolext'] = 'Enrolment extension';
$string['coupon:type:coursegrouping'] = 'Coursegrouping enrolment';
$string['recipient:selected:users'] = 'Selected users';
$string['recipient:none'] = 'None';
$string['coupon:senddate:instant'] = 'Instant';
$string['coupon:extenrol:summary'] = 'Coupon type: {$a->coupontype}<br/>
Amount of coupons to generate: {$a->amount}<br/>
Background used to generate coupon(s): {$a->logo}<br/>
Coupons generated by: {$a->owner}<br/>
Selected course(s): {$a->courses}<br/>
Extension period: {$a->duration}<br/>
Send coupon(s) on: {$a->senddate}<br/>
Send coupon(s) to: {$a->recipient}<br/><br/>
Email-body: {$a->emailbody}<br/>
';
$string['coupon:claim:wronguser'] = 'This personalized coupon is <i>not</i> yours to claim';
$string['coupon_mail_extend_content'] = 'Dear ##to_gender## ##to_name##,<br /><br />

You have been enrolled for our training ##course_fullnames## and have been granted and extension.
You already have access to our Online Learning Environment: ##site_name##.<br /><br />
Your extension is ##extensionperiod##.<br /><br />

You\'ll find the coupon to extend access to the course attached. This coupon is personal and unique, and extends access to the appropriate courses for your education.
Please read the instructions on the coupon carefully.<br /><br />

If you have any questions regarding any problems, you can contact the helpdesk.
Information can be found on our Learning Environment.
When nobody is available to answer your question, please leave your name, e-mailaddress and phonenumber behind and we will get back to you as
soon as possible.<br /><br />

With kind regards,<br /><br />

##site_name##';

$string['extendaccess'] = '{$a} extra';

// New.
$string['view:request:title'] = 'Request coupons';
$string['view:request:heading'] = 'Request coupons';

$string['privacy:metadata:block_coupon:userid'] = 'The primary database key of the Moodle user';
$string['privacy:metadata:block_coupon:for_user_email'] = 'Email address of person to which a (personal) coupon is sent, if at all';
$string['privacy:metadata:block_coupon:for_user_name'] = 'Name of person to which a (personal) coupon is sent, if at all';
$string['privacy:metadata:block_coupon:for_user_gender'] = 'Gender of person to which a (personal) coupon is sent, if at all';
$string['privacy:metadata:block_coupon:email_body'] = 'Contents of the email of which a (personal) coupon is sent, if at all';
$string['privacy:metadata:block_coupon:submission_code'] = 'Coupon subscription code';
$string['privacy:metadata:block_coupon:claimed'] = 'Whether or not the coupon was claimed';
$string['privacy:metadata:block_coupon:roleid'] = 'Role ID to be assigned / of the assigned coupon';
$string['privacy:metadata:block_coupon:timecreated'] = 'Time at which the coupon is created';
$string['privacy:metadata:block_coupon:timemodified'] = 'Time at which the coupon is modified';
$string['privacy:metadata:block_coupon:timeexpired'] = 'Expiration date for the coupon';

$string['view:requests:admin:title'] = 'Coupon request administration';
$string['view:requests:admin:heading'] = 'Coupon request administration';
$string['str:request:adduser'] = 'Add user';
$string['request:adduser:heading'] = 'Add a user that can make coupon requests';
$string['request:adduser:info'] = 'Select a user who will be allowed to make coupon requests below.<br/>
You can start typing in the selector below to narrow your user search.<br/>
When you selected the user, please click next and you will be redirected to further configure the settings for this user.
';
$string['findusers:noselectionstring'] = 'no user selected yet';
$string['findusers:placeholder'] = '... select user ...';
$string['findcourses:noselectionstring'] = 'no course(s) selected yet';
$string['findcourses:placeholder'] = '... select course(s) ...';
$string['findcohorts:noselectionstring'] = 'no cohort(s) selected yet';
$string['findcohorts:placeholder'] = '... select cohort(s) ...';
$string['findcohortcourses:noselectionstring'] = 'no selection made yet';
$string['coupon:user:heading'] = 'User configuration for {$a->firstname} {$a->lastname}';
$string['coupon:user:info'] = 'Use the form below to configure the options and accessible courses this use can request coupons for';
$string['knowncourses'] = 'Known courses';
$string['removecourse'] = 'Remove course \'{$a}\' from options';
$string['othersettings'] = 'Other settings / options';
$string['userconfig:allowselectlogo'] = 'Allow selection of coupon logo';
$string['userconfig:allowselectrole'] = 'Allow selection of role';
$string['userconfig:allowselectseperatepdf'] = 'Allow selection of ability to generate seperate PDFs';
$string['userconfig:allowselectqr'] = 'Allow selection of QR code inclusion';
$string['userconfig:allowselectenrolperiod'] = 'Allow selection of enrolment period';
$string['userconfig:default'] = 'Default setting';
$string['userconfig:seperatepdf:default'] = 'Enable generating seperate PDFs by default';
$string['userconfig:renderqrcode:default'] = 'Enable inclusion of QR code by default';
$string['tab:requests'] = 'Coupon requests';
$string['tab:requestusers'] = 'Coupon request users';
$string['delete:requestuser:header'] = 'Delete coupon request user';
$string['delete:requestuser:description'] = 'This will delete ability to request coupons for user <b>{$a->firstname} {$a->lastname}</b>.<br/>
The process is irreversable, but you can always re-configure this user by re-adding him or her to the allowed users.';
$string['delete:requestuser:confirmmessage'] = 'Yes, I want to delete this user';
$string['request:deny:heading'] = 'Deny this coupon request';
$string['request:accept:heading'] = 'Accept this coupon request';
$string['request:sendmessage'] = 'Inform the user?';
$string['request:message'] = 'User message';
$string['request:deny:subject'] = 'Coupon request denied.';
$string['request:accept:subject'] = 'Coupon request accepted.';
$string['request:accept:custommessage'] = '<p>The following remark has been added for you: {$a}</p>';
$string['request:accept:content'] = '<p>Dear {$a->fullname}</p>,
<p>You are receiving this message because your requested coupons have been generated.<br/>
The coupons are available for download on the e-learning environment.<br /><br />
Please click {$a->downloadlink} to get your coupons</p>{$a->custommessage}
<p>With kind regards,<br /><br />
{$a->signoff}</p>';
$string['view:userrequest:heading'] = 'My coupon requests';
$string['view:userrequest:title'] = 'My coupon requests';
$string['str:request:add'] = 'Request coupons';
$string['th:timecreated'] = 'Created on';
$string['delete:request:header'] = 'Delete my coupon request';
$string['delete:request:title'] = 'Delete my coupon request';
$string['delete:request:confirmmessage'] = 'I want to delete this request';
$string['button:continue'] = 'Continue';
$string['label:logo'] = 'Coupon logo/background';
$string['label:defaultlogo'] = 'Default logo';
$string['label:defaultlogo_help'] = 'Select the logo that will be forced on all coupons for this user';
$string['request:coupons'] = 'Request coupons';

$string['delete:request:description'] = 'todo: should be renderable/template based';

$string['label:displayregisterhelp'] = 'Display registration help text';
$string['label:displayregisterhelp_help'] = 'Enable this option to display a registration help text to the non-registered end user.<br/>
This will display a short explanation above the link in the block users can click to register a new account with a coupon code.';
$string['label:displayinputhelp'] = 'Display coupon input help text';
$string['label:displayinputhelp_help'] = 'Enable this option to display a text to end users above the coupon entry field.';
$string['str:inputhelp'] = 'Use the input field below to gain access to courses if you received a coupon code';
$string['str:signuphelp'] = 'Use the link below to create a new account <i>with</i> a coupon code if you received one but have no active account yet';
$string['label:useloginlayoutonsignup'] = 'Use \'login\' page layout on internal signup?';
$string['label:useloginlayoutonsignup_help'] = 'If enabled, this will use the default \'login\' page layout on the internal signup page.<br/>
This means the signup page is stripped of all headers and footers, and only provides the signup form itself.';
$string['label:forceenableemailregistration'] = 'Force enable self registration via email';
$string['label:forceenableemailregistration_help'] = 'If enabled, this will allow people to register via email authentication, even if this is disabled as self registration method.';
$string['label:batchid'] = 'Batch name';
$string['label:batchid_help'] = 'You can provide a custom name for this batch, so it can be identified later<br/>
Naming a batch will help you identify a group of generated coupons later.<br/>
If you don\'t provide a batch name it will be automatically generated';
$string['err:batchid'] = 'Batch name already exists. Please choose another natch name or leave this fields empty';
$string['label:generatecodesonly'] = 'Generate codes only';
$string['label:generatecodesonly_help'] = 'If you enable this option, only codes will be generated.<br/>
This means the complete mailing option and creating PDFs will be skipped!';

$string['generator:export:mail:subject'] = 'Coupons ready for download';
$string['generator:export:mail:body'] = 'Dear {$a->fullname},<br /><br />
You are receiving this message because there have been newly generated coupons.<br/>
The coupons can be downloaded from {$a->downloadlink} (requires logging in to Moodle).<br />
Please note this link can only be used once. After you\'ve downloaded the generated coupons, this link can no longer be used.<br />
With kind regards,<br /><br />
{$a->signoff}';

$string['error:already-enrolled-in-courses'] = 'You have already been enrolled in all courses';
$string['error:already-enrolled-in-cohorts'] = 'You have already been enrolled in all cohorts';
$string['error:myrequests:user'] = 'You are not allowed to execute this request on another person\'s behalf';

$string['with-names'] = 'With the following names or identifiers';
$string['remove-count'] = 'This will remove <i>{$a}</i> coupon(s)';
$string['cleanup:confirm:header'] = 'Please confirm the following cleanup options';
$string['cleanup:confirm:confirmmessage'] = 'Yes, I want to delete the coupons with these options';
$string['preview-pdf'] = 'Preview PDF';

$string['findcourses'] = 'Allowed courses';
$string['findcourses_help'] = 'The courses selected / added here will be the only courses the user will be allowed to generate coupons for<br/>
Do note you <i>have</i> to make a selection. It\'s not possible to leave this field empty, allowing all courses to be chosen';
$string['forcelogo_exp'] = '<i>If logo selection is disabled for this user, you <b>must</b> select a default logo in the dropdown to apply to all coupons requested by this user</i>';
$string['label:forcelogo'] = 'Forced logo';
$string['label:forcelogo_help'] = 'Select the logo that will be forced on all coupons for this user';

$string['forcerole_exp'] = '<i>If role selection is disabled for this user, you <b>must</b> select a default role in the dropdown to apply to all coupons requested by this user</i>';
$string['label:forcerole'] = 'Forced role';
$string['label:forcerole_help'] = 'Select the role that will be forced on all coupons for this user';
$string['label:enrolment_perioddefault'] = 'Default enrolment period';
$string['request:info'] = 'Request for {$a->amount} coupons';

$string['view:download:heading'] = 'Download your coupons';
$string['view:download:title'] = 'Download coupons';
$string['downloadcoupons:text'] = '<div>You can download your coupons by clicking the link below.<br/>
Please note you can only download this archive or PDF <i>once</i><br/>
As soon as you\'ve downloaded, the related file <i>will</i> be deleted.<br/>
{$a}
</div>';
$string['downloadcoupons:buttontext'] = 'Please click here to start your download';
$string['here'] = 'here';
$string['messageprovider:coupon_notification'] = 'Coupons generated notification';
$string['messageprovider:coupon_task_notification'] = 'Personal coupons sent out notification';
$string['coupon_notification_subject'] = 'Coupons generated!';
$string['coupon_notification_content'] = '<p>The coupon(s) you requested have been generated<br/>
You should have received an e-mail containing the link to download the generated coupons.<br/>
You can also choose to download your coupons directly by clicking {$a->downloadlink}</p>
';
$string['coupons:cleaned'] = 'A total of {$a} coupons have been cleaned / removed';
$string['err:coupon:generic'] = 'Something went wrong. Please contact the systems administrator';
$string['err:download-not-exists'] = 'The archive you want to download no longer exists<br/>
Most likely you have already downloaded the archive.<br/>
If you are absolutely sure you have <i>not</i> downloaded the generated coupons yourself, please contact the system administrator.';

$string['label:type_coursegrouping'] = 'Course grouping (choose X out of Y courses to enrol in)';
$string['tab:wzcoupongroupings'] = 'Manage course groupings';
$string['view:coursegroupings:admin:title'] = 'Course groupings';
$string['view:coursegroupings:admin:heading'] = 'Manage course groupings';
$string['action:coursegrouping:delete'] = 'Delete grouping';
$string['action:coursegrouping:edit'] = 'Edit grouping';
$string['action:coursegrouping:details'] = 'View grouping details';
$string['str:coursegroupings:add'] = 'Add coursegrouping';
$string['numcourses'] = 'Max amount of courses to select';
$string['coupon:coursegrouping:heading'] = 'Configure course grouping';
$string['coursegrouping-details'] = 'Course grouping details';
$string['delete:coursegrouping:header'] = 'Confirm deletion of coursegrouping';
$string['delete:coursegrouping:confirmmessage'] = 'I want to delete this course grouping';
$string['delete:coursegrouping:successmsg'] = 'Coursegrouping successfully deleted';
$string['coursegrouping'] = 'Course grouping';
$string['view:generator:coursegroupings:heading'] = 'Generate coupons for course grouping(s)';
$string['view:generator:coursegroupings:title'] = 'coursegrouping coupon generator';
$string['error:grouping-not-found'] = 'Grouping not found';
$string['error:validate-groupings'] = 'Grouping validation errors:<br/>{$a}';
$string['error:coupon:generator'] = 'Errors occured in the generator:<br/>{$a}';
$string['view:selectcourses:title'] = 'Choose course(s)';
$string['view:selectcourses:heading'] = 'Choose course(s) to enrol in';
$string['choose:courses:explain'] = 'Choose the course(s) you wish to enrol yourself in below.<br/>
This coupon allows you to choose {$a->maxamount} course(s).';
$string['err:choose:maxamount'] = 'You\'re only allowed to choose {$a} course(s) in total';
$string['err:choose:atleastone'] = 'Please select a course';
$string['heading:coursegroupingandvars'] = 'Select coupon variables, course grouping and enrolment variables';
$string['batchidselect'] = 'Batch ID';
$string['error:no-more-course-choices'] = 'You have no more courses to choose from.<br/>
It looks like you\'re already enrolled in all courses this coupon is valid for.<br/>
<br/>If you feel this is incorrect, please contact the system administrator.';
$string['label:groupingselectactiveonly'] = 'Only active enrolments?';
$string['label:groupingselectactiveonly_help'] = 'If not checked, all courses will be checked for enrolments, including active ones.<br/>
This means that even inactive / expired enrolments will count towards the courses the user redeeming the coupon is enrolled in.<br/>
All courses the redeeming person can select will be compensated for courses he or she is alreay enrolled in';
$string['tab:downloadbatchlist'] = 'Batch archives';
$string['view:downloadbatches:title'] = 'Downloadable batch archives';
$string['th:tid'] = 'Time ID';
$string['label:buttonclass'] = 'Button/link class';
$string['label:buttonclass_desc'] = 'Choose button class; this impacts the way links are displayed';
$string['batchidselect'] = 'Batch ID';
$string['report:heading:iserror'] = 'Is error?';
$string['view:reports-maillog:heading'] = 'E-mail log';
$string['view:reports-maillog:title'] = 'E-mail log';
$string['tab:maillog'] = 'E-mail log';
$string['tab:listrequests'] = 'My requests';
$string['str:request:details'] = 'My request details';
$string['err:not-a-requestuser'] = 'You have insufficient rights to access this page';

$string['privacy:metadata:block_coupon'] = 'The coupon block stores coupon/voucher codes and links users that have claimed it';
$string['label:personalsendpdf'] = 'Send PDF with personalised coupons?';
$string['label:personalsendpdf_help'] = 'If enabled, this will include the PDF when sending coupons to personal recipients.<br/>
Do note that when this is disabled, the e-mail for recipients of personal coupons <i>should</i> have a coupon code field/template variable.<br/>
If this template variable is missing and no PDF is sent along, the recipient would not know which coupon code to enter.
';
$string['label:seperatepersonalcoupontab'] = 'Add seperate personalized coupon tab?';
$string['label:seperatepersonalcoupontab_help'] = 'If enabled, this will include an extra tab specific to personalized coupons.<br/>
Of course, the default used/unused coupon tabs will be available no matter what and personalized coupons <i>will</i> always be available on those tabs.<br/>
Hence, this setting does not affect the used/unused coupons tabs.
';
$string['tab:personalcoupons'] = 'Personalised coupons';
$string['err:codesize:left'] = 'Codesize error: for {$a->want} coupons of {$a->size} characters we have {$a->left} slots left (given the current character options)';
$string['task:unenrolcohorts'] = 'Remove expired coupon enrolments from cohorts';
$string['err:myrequests:finalized'] = 'This coupon request has already been finalized.';
$string['clientref'] = 'Client reference';
$string['label:font'] = 'Font to use for PDF';
$string['label:font_help'] = 'Select the font to use when generating the PDF. In most cases the default settings is sufficient.<br/>
If you require special language support (cyrillic, arab, farsi, etc), you may want to select a different font.
';
$string['crcohorts'] = 'Selectable cohorts';
$string['crcohorts_help'] = 'Select the cohorts here that a request user can request coupons for.<br/>
Please be aware of what is configured here! Setting cohorts means the request user can request cohort coupons to be generated.<br/>
';
$string['required:atleastonecohortorcourse'] = 'At least one course or cohort is required!';
$string['str:request:cohortcoupons'] = 'Request cohort coupons';
$string['str:request:coursecoupons'] = 'Request course coupons';
$string['tab:cpmycoupons'] = 'My coupons';

$string['requestusersettings'] = 'Request user settings';
$string['requestusersettings_desc'] = 'This section has options to limit coupon request users interfaces.<br/>
These settings have been specifically introduced to limit what request users can see.<br/>
For GDPR compliance you may wish to disable some of these options.';
$string['err:tab:enablemycouponsforru'] = '"my coupons" is not enabled for display.';
$string['label:enablemycouponsforru'] = 'Enable "my coupons"';
$string['label:enablemycouponsforru_help'] = 'This setting enables/disables a table for request users where they can see the coupons along
with their status (used/unused) and other info';
$string['err:tab:enablemyprogressforru'] = 'Enable "my progress"';
$string['label:enablemyprogressforru'] = 'Enable "my progress"';
$string['label:enablemyprogressforru_help'] = 'This setting enables/disables a progress report for request users for coupon codes
that are owned by them.';
$string['heading:expiration_settings'] = 'Expiration settings';
$string['coupon:expirationmethod'] = 'Expiration method';
$string['coupon:expirationmethod_help'] = 'Expiration method indicates how to apply expiuration for the generated coupons.<br/>
The following options are available:<ul>
<li>None: the coupons do not expire</li>
<li>Date: Set the expiry date</li>
<li>Duration: coupons expire after the indicated amout of time (relative to the date of creation)</li>
</ul>
Please be aware expired coupons will be removed automatically when the expration date has passed.
No history or any archiving will be done (in other words: full delete without any possibility to get the removed entries restored).
';
$string['expiration:none'] = 'None';
$string['expiration:date'] = 'Date';
$string['expiration:duration'] = 'Duration';
$string['coupon:expiresat'] = 'Coupons expire at';
$string['coupon:expiresin'] = 'Coupons expire after';
$string['err:expiration:date'] = 'Expiration date is invalid (must be after {$a})';
$string['numeric'] = 'Numbers';
$string['letters'] = 'Lower case letters';
$string['capitals'] = 'Upper case letters/capitals';
$string['label:coupon_code_flags'] = 'Code generator flags';
$string['label:coupon_code_flags_help'] = 'Choose the default characterset to use when generating coupon codes';
$string['err:flags:nonumericonly'] = 'Numbers only coupon codes not allowed';

// TEMPLATES.
$string['templatesettings'] = 'Template settings';
$string['element:image'] = 'Image';
$string['alphachannel'] = 'Alpha channel';
$string['alphachannel_help'] = 'This value determines how transparent the image is. You can set the alpha channel from 0 (fully transparent) to 1 (fully opaque).';
$string['courseimage'] = 'Course image: {$a}';
$string['image'] = 'Image';
$string['systemimage'] = 'Site image: {$a}';

$string['element:bgimage'] = 'Background image';
$string['element:border'] = 'Border';
$string['element:code'] = 'Coupon code';
$string['element:date'] = 'Date';
$string['currentdate'] = 'Current date';
$string['expirydate'] = 'Expiry date';
$string['dateformat'] = 'Date format';
$string['dateformat_help'] = 'This is the format of the date that will be displayed';
$string['dateitem'] = 'Date item';
$string['dateitem_help'] = 'This will be the date that is printed on the template';
$string['numbersuffix_nd_as_in_second'] = 'nd';
$string['numbersuffix_rd_as_in_third'] = 'rd';
$string['numbersuffix_st_as_in_first'] = 'st';
$string['userdateformat'] = 'User date format';

$string['element:qrcode'] = 'QR Code';
$string['element:personname'] = '(Person) name';
$string['element:text'] = 'Text';
$string['text'] = 'Text';
$string['text_help'] = 'This is the text that will display on the PDF.';

$string['addpage'] = 'Add page';
$string['addelement'] = 'Add element';
$string['aligncenter'] = 'Centered';
$string['alignleft'] = 'Left alignment';
$string['alignment'] = 'Alignment';
$string['alignment_help'] = 'This property sets the horizontal alignment of the element. Some elements may not support this, while the behaviour of others may differ.';
$string['alignright'] = 'Right alignment';
$string['close'] = 'Close';
$string['copy'] = 'Copy';
$string['createtemplate'] = 'Create template';
$string['deletecertpage'] = 'Delete page';
$string['deleteconfirm'] = 'Delete confirmation';
$string['deleteelement'] = 'Delete element';
$string['deleteelementconfirm'] = 'Are you sure you want to delete this element?';
$string['deleteissuedcertificates'] = 'Delete issued certificates';
$string['deletepageconfirm'] = 'Are you sure you want to delete this page?';
$string['deletetemplateconfirm'] = 'Are you sure you want to delete this template?';
$string['description'] = 'Description';
$string['duplicate'] = 'Duplicate';
$string['duplicateconfirm'] = 'Duplicate confirmation';
$string['duplicatetemplateconfirm'] = 'Are you sure you want to duplicate this template?';
$string['editelement'] = 'Edit element';
$string['edittemplate'] = 'Edit template';
$string['elementheight'] = 'Height';
$string['elementheight_help'] = 'Specify the height of the element. If \'0\' is allowed it is automatically calculated.';
$string['elementname'] = 'Element name';
$string['elementname_help'] = 'This will be the name used to identify this element when editing a certificate. Note: this will not displayed on the PDF.';
$string['elementplugins'] = 'Element plugins';
$string['elements'] = 'Elements';
$string['elements_help'] = 'This is the list of elements that will be displayed on the certificate.

Please note: The elements are rendered in this order. The order can be changed by using the arrows next to each element.';
$string['elementwidth'] = 'Width';
$string['elementwidth_help'] = 'Specify the width of the element. If \'0\' is allowed it is automatically calculated.';
$string['eventelementcreated'] = 'Template element created';
$string['eventelementdeleted'] = 'Template element deleted';
$string['eventelementupdated'] = 'Template element updated';
$string['eventpagecreated'] = 'Template page created';
$string['eventpagedeleted'] = 'Template page deleted';
$string['eventpageupdated'] = 'Template page updated';
$string['eventtemplatecreated'] = 'Template created';
$string['eventtemplatedeleted'] = 'Template deleted';
$string['eventtemplateupdated'] = 'Template updated';
$string['exampledatawarning'] = 'Some of these values may just be an example to ensure positioning of the elements is possible.';
$string['font'] = 'Font';
$string['font_help'] = 'The font used when generating this element.';
$string['fontcolour'] = 'Colour';
$string['fontcolour_help'] = 'The colour of the font.';
$string['fontsize'] = 'Size';
$string['fontsize_help'] = 'The size of the font in points.';
$string['height'] = 'Height';
$string['height_help'] = 'This is the height of the PDF in mm. For reference an A4 piece of paper is 297mm high and a letter is 279mm high.';
$string['invalidcode'] = 'Invalid code supplied.';
$string['invalidcolour'] = 'Invalid colour chosen, please enter a valid HTML colour name, or a six-digit, or three-digit hexadecimal colour.';
$string['invalidelementwidthorheightnotnumber'] = 'Please enter a valid number.';
$string['invalidelementwidthorheightzeroallowed'] = 'Please enter a number greater than or equal to 0.';
$string['invalidelementwidthorheightzeronotallowed'] = 'Please enter a number greater than 0.';
$string['invalidposition'] = 'Please select a positive number for position {$a}.';
$string['invalidheight'] = 'The height has to be a valid number greater than 0.';
$string['invalidmargin'] = 'The margin has to be a valid number greater than 0.';
$string['invalidwidth'] = 'The width has to be a valid number greater than 0.';
$string['landscape'] = 'Landscape';
$string['leftmargin'] = 'Left margin';
$string['leftmargin_help'] = 'This is the left margin of the PDF in mm.';
$string['load'] = 'Load';
$string['loadtemplate'] = 'Load template';
$string['loadtemplatemsg'] = 'Are you sure you wish to load this template? This will remove any existing pages and elements for this certificate.';
$string['managetemplates'] = 'Manage templates';
$string['managetemplatesdesc'] = 'This link will take you to a new screen where you will be able to manage templates used by Template activities in courses.';
$string['modify'] = 'Modify';
$string['name'] = 'Name';
$string['nametoolong'] = 'You have exceeded the maximum length allowed for the name';
$string['noimage'] = 'No image';
$string['notemplates'] = 'No templates';
$string['options'] = 'Options';
$string['page'] = 'Page {$a}';
$string['portrait'] = 'Portrait';
$string['posx'] = 'Position X';
$string['posx_help'] = 'This is the position in mm from the top left corner you wish the element\'s reference point to locate in the x direction.';
$string['posy'] = 'Position Y';
$string['posy_help'] = 'This is the position in mm from the top left corner you wish the element\'s reference point to locate in the y direction.';
$string['print'] = 'Print';
$string['rearrangeelements'] = 'Reposition elements';
$string['rearrangeelementsheading'] = 'Drag and drop elements to change where they are positioned on the certificate.';
$string['refpoint'] = 'Reference point location';
$string['refpoint_help'] = 'The reference point is the location of an element from which its x and y coordinates are determined. It is indicated by the \'+\' that appears in the centre or corners of the element.';
$string['replacetemplate'] = 'Replace';
$string['requiredtimenotmet'] = 'You must spend at least a minimum of {$a->requiredtime} minutes in the course before you can access this certificate.';
$string['rightmargin'] = 'Right margin';
$string['rightmargin_help'] = 'This is the right margin of the PDF in mm.';
$string['save'] = 'Save';
$string['saveandclose'] = 'Save and close';
$string['saveandcontinue'] = 'Save and continue';
$string['savechangespreview'] = 'Save changes and preview';
$string['savetemplate'] = 'Save template';
$string['setprotection'] = 'Set protection';
$string['setprotection_help'] = 'Choose the actions you wish to prevent users from performing on this certificate.';
$string['showposxy'] = 'Show position X and Y';
$string['showposxy_desc'] = 'This will show the X and Y position when editing of an element, allowing the user to accurately specify the location.

This isn\'t required if you plan on solely using the drag and drop interface for this purpose.';
$string['template'] = 'Template';
$string['templates'] = 'Templates';
$string['templatename'] = 'Template name';
$string['templatenameexists'] = 'That template name is currently in use, please choose another.';
$string['topcenter'] = 'Center';
$string['topleft'] = 'Top left';
$string['topright'] = 'Top right';
$string['type'] = 'Type';
$string['uploadimage'] = 'Upload image';
$string['uploadimagedesc'] = 'This link will take you to a new screen where you will be able to upload images. Images uploaded using
this method will be available throughout your site to all users who are able to create a certificate.';
$string['width'] = 'Width';
$string['width_help'] = 'This is the width of the PDF in mm. For reference an A4 piece of paper is 210mm wide and a letter is 216mm wide.';

$string['userlanguage'] = 'Use user preferences';
$string['languageoptions'] = 'Force Language';
$string['userlanguage_help'] = 'You can force the language of the template to override the user\'s language preferences.';
$string['generatorsettings'] = 'Code generator settings';
$string['pdfsettings'] = 'PDF settings';
$string['usetype'] = 'PDF method';
$string['generator:usetemplate'] = 'Create PDF from a template';
$string['generator:uselogo'] = 'Create PDF using a logo (and static texts)';
$string['indefinite'] = 'Indefinite';
$string['excluding'] = 'excluding {$a}';
$string['pdfmerge'] = 'PDF output';
$string['separatepdfs'] = 'Generate seperate PDF\'s';
$string['combinedpdf'] = 'Generate single PDF';
$string['confirm:courseinfo'] = 'Course info';
$string['recipients'] = 'Recipients';
$string['generatortype'] = 'Source method';
$string['emailbody'] = 'Email body';
$string['confirm:coursegroupinginfo'] = 'Course grouping info';
$string['confirm:cohortinfo'] = 'Cohort info';
$string['err:idnumber-not-unique'] = 'IDNumber must be unique';
$string['generator:extendenrolment:invalidcourse'] = 'Invalid course';
$string['error:group-not-found'] = 'Group not found';
$string['error:cohort-not-found'] = 'Cohort not found';
$string['err:template:delete'] = 'Template could not be deleted';
$string['success:template:delete'] = 'Template deleted';
$string['err:template:duplicate'] = 'Template could not be duplicated';
$string['success:template:duplicate'] = 'Template duplicated';
$string['event:coupon:used'] = 'Coupon was claimed';
$string['view:index.php:title'] = 'Coupon generator';
$string['element:text:templatevars'] = 'Define your text block below. You can use the following optional templated variables:{$a}';
$string['extendusers:recipient'] = 'Recipients';
$string['extendusers:recipient_help'] = 'This setting indicates who will receive the coupon codes or files';
$string['extendusers:recipient:users'] = 'Indicated user(s)';
$string['extendusers:recipient:me'] = 'Myself';
$string['recipient:name'] = 'Recipient name';
$string['email:templatevars'] = 'Define your email contents below. You can use the following optional templated variables:{$a}';
$string['recipient:gender'] = 'Gender';
$string['heading:extendenrolment'] = 'Enrolment extention';
$string['coupon:generator:processing'] = 'Generating coupons. Please stand by, this page will automatically refresh.';
$string['view:mailtemplates'] = 'E-mail templates';
$string['mailtemplates:title'] = 'Manage e-mail templates';
$string['mailtemplates'] = 'Manage e-mail templates';
$string['heading:mailsettings'] = 'E-mail settings';
$string['load_mailtemplate'] = 'Mail template';
$string['load_mailtemplate_help'] = 'Select a mail template. Beware: this will refresh the page to load the mail template contents';
$string['import:voucher:desc'] = 'Import all available and applicable voucher data from block_voucher.<br/>
We\'ll (try to) import all vouchers, linked course/cohort data, email templates and PDF templates, although
we can <i>not</i> guarantee a full import without errors.<br/>
However, importing the basic voucher codes and linked data is most likely to succeed without issues.';
$string['import:voucher:confirm'] = 'Import data from block_voucher';
