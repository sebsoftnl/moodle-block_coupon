<?php

/*
 * File: block_voucher.php
 * Encoding: UTF-8
 * @package voucher
 * 
 * @Version 1.0.0
 * @Since 11-jul-2013
 * @copyright Sebsoft.nl
 * @author Menno de Ridder <menno@sebsoft.nl>
 */

//DEFAULT
$string['blockname'] = 'Voucher';
$string['pluginname'] = 'Voucher';

$string['form-desc:voucher_enablecron'] = 'Enable block CRON';
$string['form-desc:voucher_enabledebug'] = 'Enable block debugging';
$string['form-desc:voucher_debugemail'] = 'Block debugging email address';

$string['redirect_in'] = 'Auto-redirecting in ';
$string['seconds'] = 'seconds';

// Headers
$string['heading:administration'] = 'Manage';
$string['heading:generatevouchers'] = 'Generate vouchers';
$string['heading:inputvouchers'] = 'Input Voucher';

$string['heading:voucher_type'] = 'Type of voucher';
$string['heading:input_voucher'] = 'Input voucher';
$string['heading:general_settings'] = 'Last settings';
$string['heading:input_cohorts'] = 'Select cohorts';
$string['heading:input_course'] = 'Select course';

// Errors
$string['error:nopermission'] = 'You have no permission to do this';
$string['error:required'] = 'This field is required.';
$string['error:numeric_only'] = 'This field must be numeric.';
$string['error:invalid_email'] = 'Please enter a valid email adress.';
$string['error:invalid_voucher_code'] = 'You have entered an invalid voucher code.';
$string['error:voucher_already_used'] = 'The voucher with this code has already been used.';
$string['error:unable_to_enrol'] = 'An error occured while trying to enrol you in the new course. Please contact support.';
$string['error:missing_course'] = 'The course linked to this voucher does not exist anymore. Please contact support.';
$string['error:cohort_sync'] = 'An error occured while trying to synchronize the cohorts. Please contact support.';
$string['error:plugin_disabled'] = 'The cohort_sync plugin has been disabled. Please contact support.';
$string['error:missing_cohort'] = 'The cohort(s) linked to this voucher does not exist anymore. Please contact support.';
$string['error:missing_group'] = 'The group(s) linked to this voucher does not exist anymore. Please contact support.';

$string['error:wrong_code_length'] = 'Please enter a number between 6 and 32.';

// Success strings
$string['success:voucher_used'] = 'Voucher used - You can now access the course(s)';

// URL texts
$string['url:generate_vouchers'] = 'Generate Voucher';
$string['url:input_voucher'] = 'Input Voucher';

// Form Labels
$string['label:voucher_type'] = 'Generate based on';
$string['label:voucher_email'] = 'Email address';
$string['label:voucher_amount'] = 'Amount of vouchers';
$string['label:type_course'] = 'Course';
$string['label:type_cohorts'] = 'Cohort(s)';

$string['label:voucher_connect_course'] = 'Add course(s)';
$string['label:voucher_connect_course_help'] = 'Select all courses you wish to add to the cohort.
    <br /><b><i>Note: </i></b>All users who are already enrolled in this cohort will also be enrolled in the selected courses!';
$string['label:connected_courses'] = 'Connected course(s)';
$string['label:no_courses_connected'] = 'There are no courses connected to this cohort.';

$string['label:voucher_course'] = 'Course';
$string['label:voucher_cohorts'] = 'Cohort(s)';

$string['label:cohort'] = 'Cohort';
$string['label:voucher_code'] = 'Voucher Code';
$string['label:voucher_code_help'] = 'The voucher code is the unique code which is linked to each individual voucher. You can find this code on your voucher.';
$string['label:enter_voucher_code'] = 'Please enter your voucher code here';

// Labels for already selected stuffz
$string['label:selected_groups'] = 'Selected group(s)';
$string['label:selected_course'] = 'Selected course';
$string['label:selected_cohort'] = 'Selected cohort(s)';

// help texts
$string['label:voucher_type_help'] = 'The Vouchers will be generated based on either the course or one or more cohorts.';
$string['label:voucher_email_help'] = 'This is the email address the generated vouchers will be send to.';
$string['label:voucher_amount_help'] = 'This is the the amount of vouchers that will be generated.';

$string['label:voucher_cohorts_help'] = 'Select the one or more cohorts your users will be enrolled in.';
$string['label:voucher_course_help'] = 'Select the course your users will be enrolled in.';

$string['label:voucher_groups'] = 'Add group(s)';
$string['label:voucher_groups_help'] = 'Select the groups you wish your users to be enrolled in upon enrolment in the course.';
$string['label:no_groups_selected'] = 'There are no groups connected to this course yet.';

// generate pdfs
$string['label:generate_pdfs'] = 'Generate seperate PDF\'s';
$string['label:generate_pdfs_help'] = 'You can select here if you want to receive your vouchers in either a single file or each voucher in a saperate PDF file.';

// buttons
$string['button:next'] = 'Next';
$string['button:save'] = 'Generate Vouchers';
$string['button:submit_voucher_code'] = 'Submit Voucher';

// view strings
$string['view:generate_voucher:title'] = 'Generate Voucher';
$string['view:generate_voucher:heading'] = 'Generate Voucher';

$string['view:input_voucher:title'] = 'Input Voucher';
$string['view:input_voucher:heading'] = 'Input Voucher';

$string['course'] = 'course';
$string['cohort'] = 'cohort';

$string['voucher_code_length'] = 'Code length';
$string['voucher_code_length_desc'] = 'Amount of characters of the voucher code (minimum of 6).';

$string['use_supportuser'] = 'Use support user';
$string['use_supportuser_desc'] = 'Send Vouchers to support user by default';

$string['pdf_generated'] = 'The vouchers have been attached to this email in PDF files.<br /><br />';


$string['voucher_mail_content'] = '
    Hello{$a->str_name},<br /><br />
    You receive this email because new vouchers have recently been generated. The vouchers are added to the attachments of this e-mail.<br /><br />
    With kind regards,<br /><br />
    Your friendly neighbourhood Moodle Site';
$string['voucher_mail_subject'] = 'Moodle Voucher generated';

$string['vouchers_sent'] = 'Your voucher(s) has/have been generated. Within several minutes you will receive an email with the voucher(s) in the attachment.';

$string['default-voucher-page-template'] = '
<p style="font-weight: bold;">Moodle Voucher</p><br/><br/>
<p>Hereby you receive the voucher with which you can subscribe for your course(s) in the digital learning environment.</p><br/><br/>
<p><table style="width:100%"><tr><td style="border:1px solid #000000; font-weight: bold; vertical-align: middle; text-align: center;">This is your voucher code: {vouchercode}</td></tr></table></p>
<p></p>
<p>Please follow the instructions below:</p><br/>
<ol>
<li>Open an internet browser</li>
<li>Go to the following website: <a href="{site_url}">{site_name}</a></li>
<li>Click - in the Voucher block - on the url "Input Voucher"</li>
<li>Enter the voucher code provided in this document (please note: This code is case-sensitive)</li>
<li>You are now subscribed and can enter your course(s).</li>
</ol><br/><br/>
<p>Happy learning!</p>';

$string['pdf:titlename'] = 'Moodle Voucher';
$string['pdf-meta:title'] = 'Moodle Voucher';
$string['pdf-meta:subject'] = 'Moodle Voucher';
$string['pdf-meta:keywords'] = 'Moodle Voucher';
