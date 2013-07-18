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
$string['label:connected_courses'] = 'Connected course(s)';
$string['label:no_courses_connected'] = 'There are no courses connected to this cohort.';

$string['label:voucher_course'] = 'Course';
$string['label:voucher_cohorts'] = 'Cohort(s)';

$string['label:cohort'] = 'Cohort';
$string['label:voucher_code'] = 'Voucher Code';

// Labels for already selected stuffz
$string['label:selected_groups'] = 'Selected group(s)';
$string['label:selected_course'] = 'Selected course';
$string['label:selected_cohort'] = 'Selected cohort(s)';

// help texts
$string['label:voucher_type_help'] = 'The Vouchers will be generated based on either the course or one or more cohorts.';
$string['label:voucher_email_help'] = 'This is the email address the generated vouchers will be send to.<br />By default it takes the email address from the plugin configuration.';
$string['label:voucher_amount_help'] = 'This is the the amount of vouchers that will be generated.';

$string['label:voucher_cohorts_help'] = 'Select the one or more cohorts your users will be enrolled in.';
$string['label:voucher_course_help'] = 'Select the course your users will be enrolled in.';

$string['label:add_groups'] = 'Add group(s)';
$string['label:no_groups_selected'] = 'There are no groups connected to this course yet.';

// generate pdfs
$string['label:generate_pdfs'] = 'Generate loose PDF\'s';

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

$string['use_supportuser'] = 'Use support user';
$string['use_supportuser_desc'] = 'Send Vouchers to support user by default';

$string['pdf_generated'] = 'The vouchers have been attached to this email in PDF files.<br /><br />';

$string['default-voucher-page-template'] = '<p>{store_name}<br/>Winkelnummer: {store_number}</p>
<p style="font-weight: bold;">Welkom bij Jumbo!</p><br/><br/>
<p>Hierbij ontvang je de voucher om je aan te melden op de digitale leeromgeving van Jumbo. 
Tijdens je registratie op de digitale leeromgeving heb je de vouchercode nodig om je aan te melden.</p><br/><br/>
<p><table style="width:100%"><tr><td style="border:1px solid #000000; font-weight: bold; vertical-align: middle; text-align: center;">Dit is je vouchercode: {vouchercode}</td></tr></table></p>
<p></p>
<p>Volg de volgende stappen:</p><br/>
<ol>
<li>Open Internet Explorer</li>
<li>Ga naar de website: <a href="http://winkel.jumboleerplein.nl">winkel.jumboleerplein.nl</a></li>
<li>Klik onderaan in het blok Aanmelden op "Nieuw account maken"</li>
<li>Vul de vouchercode in (let op deze is hoofdlettergevoelig)</li>
<li>Volg de stappen</li>
<li>Je bent nu op de startpagina. Kies onder Mijn cursussen de cursus die je wilt volgen.</li>
</ol><br/><br/>
<p>Veel leerplezier gewenst!</p>';
