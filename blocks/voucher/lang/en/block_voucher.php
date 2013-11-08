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

// Capabilities
$string['voucher:addinstance'] = 'Add a new Voucher block';
$string['voucher:administration'] = 'Administrate the Voucher block';
$string['voucher:generatevouchers'] = 'Generate a new voucher';
$string['voucher:inputvouchers'] = 'Use a voucher to subscribe';
$string['voucher:myaddinstance'] = 'Add a new Voucher block to the My Moodle page';


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
$string['heading:input_groups'] = 'Select groups';
$string['heading:imageupload'] = 'Upload image';

$string['heading:info'] = 'Info';

$string['heading:csvForm'] = 'CSV settings';
$string['heading:amountForm'] = 'Amount settings';

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
$string['error:voucher_amount_too_high'] = 'You are only allowed to generate {$a->max_vouchers} at one given time.';
$string['error:alternative_email_required'] = 'If you have checked \'use alternative email\' this field is required.';
$string['error:alternative_email_invalid'] = 'If you have checked \'use alternative email\' this field should contain a valid email address.';

$string['error:wrong_code_length'] = 'Please enter a number between 6 and 32.';
$string['error:no_vouchers_submitted'] = 'None of your vouchers have been used yet.';

$string['error:wrong_image_size'] = 'The uploaded background does not have the required size. Please upload an image with a ratio of 210 mm by 297 mm.';

$string['error:moodledata_not_writable'] = 'Your moodledata/voucher_logos folder is not writable. Please fix your permissions.';

$string['error:wrong_doc_page'] = 'You are trying to access a page that does not exist.';

// Success strings
$string['success:voucher_used'] = 'Voucher used - You can now access the course(s)';
$string['success:uploadimage'] = 'Your new voucher image has been uploaded.';


// URL texts
$string['url:generate_vouchers'] = 'Generate Voucher';
$string['url:api_docs'] = 'API Documentation';
$string['url:uploadimage'] = 'Change voucher image';
$string['url:input_voucher'] = 'Input Voucher';
$string['url:view_reports'] = 'View reports';
$string['url:view_unused_vouchers'] = 'View unused vouchers';

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

$string['label:alternative_email'] = 'Alternative email';
$string['label:alternative_email_desc'] = 'Send vouchers by default to this email address.';
$string['label:alternative_email_help'] = 'The email address the vouchers will be sent to.';

$string['label:use_alternative_email'] = 'Use alternative email';
$string['label:use_alternative_email_desc'] = 'When checked it will by default use the email address provided in the Alternative email field.';
$string['label:use_alternative_email_help'] = 'When checked the vouchers will be send to the provided alternative email address. Ohterwise the vouchers
    will be send to the user generating the vouchers.';

$string['label:max_vouchers'] = 'Maximum vouchers';
$string['label:max_vouchers_desc'] = 'Amount of vouchers that can be created in one time.';

$string['label:voucher_code_length'] = 'Code length';
$string['label:voucher_code_length_desc'] = 'Amount of characters of the voucher code.';


// Labels for already selected stuffz
$string['label:selected_groups'] = 'Selected group(s)';
$string['label:selected_course'] = 'Selected course';
$string['label:selected_cohort'] = 'Selected cohort(s)';

$string['label:api_enabled'] = 'Enable API';
$string['label:api_enabled_desc'] = 'The Voucher API grants the possibility to generate vouchers from an external system.';
$string['label:api_user'] = 'API User';
$string['label:api_user_desc'] = 'The username that can be used to generate a voucher using the API.';
$string['label:api_password'] = 'API Password';
$string['label:api_password_desc'] = 'The password that can be used to generate a voucher using the API.';
$string['label:generate_pdfs'] = 'Generate seperate PDF\'s';

// info labels
$string['label:info_desc'] = 'The information shown above the form.';
$string['label:info_voucher_type'] = 'Information on page: Select voucher type';
$string['label:info_voucher_course'] = 'Information on page: Select course';
$string['label:info_voucher_cohorts'] = 'Information on page: Select cohorts';
$string['label:info_voucher_course_groups'] = 'Information on page: Select course groups';
$string['label:info_voucher_cohort_courses'] = 'Information on page: Cohort courses';
$string['label:info_voucher_confirm'] = 'Information on page: Confirm voucher';
$string['label:info_imageupload'] = 'Information on page: Upload image';

// help texts
$string['label:voucher_type_help'] = 'The Vouchers will be generated based on either the course or one or more cohorts.';
$string['label:voucher_email_help'] = 'This is the email address the generated vouchers will be send to.';
$string['label:voucher_amount_help'] = 'This is the the amount of vouchers that will be generated. Please use this field OR the field recipients, not both.';
$string['label:voucher_cohorts_help'] = 'Select the one or more cohorts your users will be enrolled in.';
$string['label:voucher_course_help'] = 'Select the course your users will be enrolled in.';
$string['label:voucher_groups'] = 'Add group(s)';
$string['label:voucher_groups_help'] = 'Select the groups you wish your users to be enrolled in upon enrolment in the course.';
$string['label:no_groups_selected'] = 'There are no groups connected to this course yet.';
$string['label:image'] = 'Voucher background';
$string['label:image_desc'] = 'Background to be placed in the generated vouchers';
$string['label:current_image'] = 'Current Voucher background';
$string['label:generate_pdfs_help'] = 'You can select here if you want to receive your vouchers in either a single file or each voucher in a saperate PDF file.';

// buttons
$string['button:next'] = 'Next';
$string['button:save'] = 'Generate Vouchers';
$string['button:submit_voucher_code'] = 'Submit Voucher';

// view strings
$string['view:generate_voucher:title'] = 'Generate Voucher';
$string['view:generate_voucher:heading'] = 'Generate Voucher';

$string['view:reports:heading'] = 'Voucher Reports';
$string['view:reports:title'] = 'Voucher Reports';

$string['view:input_voucher:title'] = 'Input Voucher';
$string['view:input_voucher:heading'] = 'Input Voucher';

$string['course'] = 'course';
$string['cohort'] = 'cohort';

$string['missing_config_info'] = 'Put your extra information here - to be set up in the global configuration of the block.';
$string['pdf_generated'] = 'The vouchers have been attached to this email in PDF files.<br /><br />';


$string['voucher_mail_content'] = '
    Hello{$a->to_name},<br /><br />
    You receive this email because new vouchers have recently been generated. The vouchers are added to the attachments of this e-mail.<br /><br />
    With kind regards,<br /><br />
    {$a->from_name}';
$string['voucher_mail_subject'] = 'Moodle Voucher generated';

$string['vouchers_sent'] = 'Your voucher(s) has/have been generated. Within several minutes you will receive an email with the voucher(s) in the attachment.';

$string['default-voucher-page-template-main'] = 'With this voucher you can activate access to the e-learning module. You have 90 days of access to this module.

Please use the following voucher code to activate access.

{voucher_code}';
$string['default-voucher-page-template-botleft'] = '1. Sign up at {site_url}
2. You will receive an email with the confirmation url. Click on the url to activate your account.
3. Enter your voucher code in the Moodle Voucher block
4. Happy learning!';
$string['default-voucher-page-template-botright'] = '1. Log in at {site_url}
2. Enter your voucher code in the Moodle Voucher block
3. Happy learning!';

//$string['default-voucher-page-template-main'] = '
//<p style="font-weight: bold;">Moodle Voucher</p><br/><br/>
//<p>Hereby you receive the voucher with which you can subscribe for your course(s) in the digital learning environment.</p><br/><br/>
//<p><table style="width:100%"><tr><td style="border:1px solid #000000; font-weight: bold; vertical-align: middle; text-align: center;">This is your voucher code: {vouchercode}</td></tr></table></p>
//<p></p>
//<p>Please follow the instructions below:</p><br/>
//<ol>
//<li>Open an internet browser</li>
//<li>Go to the following website: <a href="{site_url}">{site_url}</a></li>
//<li>Click - in the Voucher block - on the url "Input Voucher"</li>
//<li>Enter the voucher code provided in this document (please note: This code is case-sensitive)</li>
//<li>You are now subscribed and can enter your course(s).</li>
//</ol><br/><br/>
//<p>Happy learning!</p>';


$string['pdf:titlename'] = 'Moodle Voucher';
$string['pdf-meta:title'] = 'Moodle Voucher';
$string['pdf-meta:subject'] = 'Moodle Voucher';
$string['pdf-meta:keywords'] = 'Moodle Voucher';

$string['error:sessions-expired'] = 'Your session has been expired. Please try again.';

$string['report:status_not_started'] = 'Course not started yet';
$string['report:status_started'] = 'Course started';
$string['report:status_completed'] = 'Course completed';

$string['report:dateformat'] = '%d-%m-%Y %H:%M:%S'; 
$string['report:dateformatymd'] = '%d-%m-%Y';

$string['report:heading:username'] = 'Username';
$string['report:heading:coursename'] = 'Course name';
$string['report:heading:coursetype'] = 'Course type';
$string['report:heading:status'] = 'Status';
$string['report:heading:datestart'] = 'Startdate';
$string['report:heading:datecomplete'] = 'Date completed';
$string['report:heading:grade'] = 'Grade';

$string['report:owner'] = 'Owner';
$string['report:senddate'] = 'Send date';
$string['report:enrolperiod'] = 'Owner';
$string['report:voucher_code'] = 'Subscription code';
$string['report:cohorts'] = 'Cohort';
$string['report:issend'] = 'Is send';
$string['report:immediately'] = 'Immediately';
$string['report:for_user'] = 'Planned user';

$string['str:mandatory'] = 'Mandatory or smthing';
$string['str:optional'] = 'Optional or smthing';

$string['label:voucher_recipients'] = 'Recipients';
$string['error:recipients-extension'] = 'You can only upload .csv files.';
$string['error:voucher_amount-recipients-both-set'] = 'Please specify a number of vouchers to generate OR a csv list of recipients.';

$string['label:voucher_recipients_help'] = 'With this field you can upload a csv file with users. Please use this field OR the field voucher amount, not both.';
$string['label:voucher_recipients_txt'] = 'Recipients';
$string['label:voucher_recipients_txt_help'] = 'In this field you can make your final changes to the uploaded csv file.';
$string['error:voucher_amount-recipients-both-unset'] = 'Either this field or the field Recipients must be set.';

$string['download-sample-csv'] = 'Download sample CSV file';

$string['label:email_body'] = 'Email message';
$string['label:email_body_help'] = 'The email message that will be send to the recipients of the vouchers.';
$string['label:redirect_url'] = 'Redirect URL';
$string['label:redirect_url_help'] = 'The destination users will be send to after entering their voucher code.';

$string['label:enrolment_period'] = 'Enrolment period';
$string['label:enrolment_period_help'] = 'Period (in days) the user will be enrolled in the courses. If set to 0 no end will be issued.';

$string['label:date_send_vouchers'] = 'Send date';
$string['label:date_send_vouchers_help'] = 'Date the vouchers will be send to the recipient(s).';

$string['label:showform'] = 'Generate using';
$string['showform-csv'] = 'csv';
$string['showform-amount'] = 'amount';

$string['error:recipients-unknown-user'] = 'One of the users is not a Moodle User. Please correct this.';
$string['error:recipients-max-exceeded'] = 'Your csv file has exceeded the maximum of 10.000 voucher users. Please limit it.';
$string['error:recipients-invalid'] = 'The users could not be validated. Are you sure you entered the right columns and seperator?';
$string['error:recipients-empty'] = 'Please enter at least one user.';
