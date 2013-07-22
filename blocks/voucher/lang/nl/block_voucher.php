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

$string['form-desc:voucher_enablecron'] = 'Block CRON inschakelen';
$string['form-desc:voucher_enabledebug'] = 'Block debugging inschakelen';
$string['form-desc:voucher_debugemail'] = 'Block debugging email adres';

$string['redirect_in'] = 'Automatisch verwijzen in ';
$string['seconds'] = 'seconden';

// Headers
$string['heading:administration'] = 'Beheer';
$string['heading:generatevouchers'] = 'Voucher genereren';
$string['heading:inputvouchers'] = 'Voucher invoeren';

$string['heading:voucher_type'] = 'Type voucher';
$string['heading:input_voucher'] = 'Voucher invoeren';
$string['heading:input_cohorts'] = 'Selecteer cohorten';
$string['heading:input_course'] = 'Selecteer cursus';
$string['heading:general_settings'] = 'Laatste instellingen';

// Errors
$string['error:nopermission'] = 'U heeft geen toestemming om dit te doen';
$string['error:required'] = 'Dit is een verplicht veld.';
$string['error:numeric_only'] = 'Dit veld is een verplicht numeriek veld.';
$string['error:invalid_email'] = 'Dit e-mail adres is ongeldig.';
$string['error:invalid_voucher_code'] = 'U heeft een ongeldig voucher code ingevuld.';
$string['error:voucher_already_used'] = 'Deze voucher is al gebruikt.';
$string['error:unable_to_enrol'] = 'Een error is opgetreden tijdens het inschrijven in een nieuwe cursus. Neem contact op met support.';
$string['error:missing_course'] = 'De cursus die aan de Voucher is gelinkt bestaat niet meer. Neem contact op met support.';
$string['error:cohort_sync'] = 'Een error is opgetreden tijdens het synchroniseren van de cohortes. Neem contact op met support.';
$string['error:plugin_disabled'] = 'De cohort_sync plugin staat uit. Neem contact op met support.';
$string['error:missing_cohort'] = 'De cohort(en) die aan deze Voucher gelinkt is bestaat niet meer. Neem contact op met support.';
$string['error:missing_group'] = 'De groep(en) die aan deze Voucher gelinkt is bestaat niet meer. Neem contact op met support.';

$string['error:wrong_code_length'] = 'Vul een getal tussen 6 en 32 in.';

// Success strings
$string['success:voucher_used'] = 'Voucher gebruikt - U kunt nu uw nieuwe cursus(en) in';

// URL texts
$string['url:generate_vouchers'] = 'Genereer Voucher';
$string['url:input_voucher'] = 'Voucher invoeren';

// Form Labels
$string['label:voucher_type'] = 'Genereer gebaseerd op';
$string['label:voucher_email'] = 'E-mail adres';
$string['label:voucher_amount'] = 'Aantal vouchers';
$string['label:type_course'] = 'Cursus';
$string['label:type_cohorts'] = 'Cohort(s)';

$string['label:voucher_connect_course'] = 'Cursus(sen) toevoegen';
$string['label:voucher_connect_course_help'] = 'Selecteer de cursussen die aan de cohort moeten worden toegevoegd.
    <br /><b><i>Let op: </i></b>Als er al deelnemers aan die cohort toegevoegd zijn worden deze ook in de cursussen ingeschreven!';
$string['label:connected_courses'] = 'Toegevoegde cursus(sen)';
$string['label:no_courses_connected'] = 'Er zijn nog geen cursussen toegevoegd aan deze cohort.';

$string['label:voucher_groups'] = 'Groep(en) toevoegen';
$string['label:voucher_groups_help'] = 'Selecteer hier de groepen waar uw gebruikers in toegevoegd moeten worden zodra ze worden ingeschreven bij de cursus.';
$string['label:no_groups_selected'] = 'Er zijn nog geen groepen aan deze cursus toegevoegd.';

$string['label:generate_pdfs'] = 'Genereer losse PDF\'s';
$string['label:generate_pdfs_help'] = 'Hier kunt u aangeven of u alle vouchers in 1 bestand of iedere voucher een apart bestand wilt ontvangen.';

$string['label:cohort'] = 'Cohort';
$string['label:voucher_code'] = 'Voucher Code';
$string['label:voucher_code_help'] = 'Het voucher code is de unieke code die aan iedere voucher is toegekend. U vindt dit code op uw voucher.';
$string['label:enter_voucher_code'] = 'Vul hier uw Voucher code in';

// Labels for already selected stuffz
$string['label:selected_groups'] = 'Geselecteerde groep(en)';
$string['label:selected_course'] = 'Geselecteerde cursus';
$string['label:selected_cohort'] = 'Geselecteerde cohort(en)';

// help texts
$string['label:voucher_type_help'] = 'De vouchers worden gebaseerd op een cursus of een of meer cohorts.';
$string['label:voucher_email_help'] = 'Dit is het e-mail adres waar de gegenereerde vouchers naar toe gestuurd worden.';
$string['label:voucher_amount_help'] = 'Het aantal vouchers dat gegenereerd zal worden.';

// buttons
$string['button:next'] = 'Volgende';
$string['button:save'] = 'Genereer Vouchers';
$string['button:submit_voucher_code'] = 'Invoeren';

// view strings
$string['view:generate_voucher:title'] = 'Genereer Voucher';
$string['view:generate_voucher:heading'] = 'Genereer Voucher';

$string['view:input_voucher:title'] = 'Voucher invoeren';
$string['view:input_voucher:heading'] = 'Voucher invoeren';

$string['course'] = 'cursus';
$string['cohort'] = 'cohort';

$string['vouchers_sent'] = 'Uw vouchers zijn gegenereerd. Binnen enkele minuten ontvangt u een email bericht met de voucher(s) in de bijlage.';

$string['voucher_code_length'] = 'Code lengte';
$string['voucher_code_length_desc'] = 'Aantal karakters voor het voucher code (minimaal 6).';

$string['pdf_generated'] = 'De Vouchers zijn aan dit email bericht toegevoegd als PDF bestanden.<br /><br />';

$string['default-voucher-page-template'] = '
<p style="font-weight: bold;">Moodle Voucher</p><br/><br/>
<p>Hierbij ontvang je de voucher om je in te schrijven voor cursussen op de digitale leeromgeving.</p><br/><br/>
<p><table style="width:100%"><tr><td style="border:1px solid #000000; font-weight: bold; vertical-align: middle; text-align: center;">Dit is je vouchercode: {vouchercode}</td></tr></table></p>
<p></p>
<p>Volg de volgende stappen:</p><br/>
<ol>
<li>Open Internet Explorer</li>
<li>Ga naar de website: <a href="moodle.menno.extern.ds.office.sebsoft.nl">moodle.menno.blabla.nl</a></li>
<li>Log in met je gebruikelijke gegevens</li>
<li>Klik in het blok Voucher op de link "Voucher invoeren"</li>
<li>Vul het voucher code in (let op deze is hoofdlettergevoelig)</li>
<li>Je bent nu ingeschreven op de cursussen die bij deze voucher horen.</li>
</ol><br/><br/>
<p>Veel leerplezier gewenst!</p>';

$string['voucher_mail_content'] = '
    Hallo{$a->str_name},<br /><br />
    U ontvangt dit bericht omdat er zojuist nieuwe Moodle Vouchers aangemaakt zijn. De Vouchers zijn in de bijlage van dit bericht toegevoegd.<br /><br />
    Met vriendelijke groet,<br /><br />
    Moodle';
$string['voucher_mail_subject'] = 'Moodle Voucher generated';
