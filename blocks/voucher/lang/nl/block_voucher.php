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
$string['voucher:addinstance'] = 'Voeg een nieuw Voucher blok toe';
$string['voucher:administration'] = 'Beheer het Moodle Voucher blok';
$string['voucher:generatevouchers'] = 'Genereer een nieuwe Moodle Voucher';
$string['voucher:inputvouchers'] = 'Gebruik een Voucher';
$string['voucher:myaddinstance'] = 'Voeg een nieuw Voucher blok toe aan de Mijn Moodle pagina';

$string['error:sessions-expired'] = 'Uw sessie is verlopen.';

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
$string['heading:label_instructions'] = 'Instructies';
$string['heading:instructions_1'] = 'Course or Cohorts - Voer dit en dat uit om dit en dat te doen..';
// Ditjes en datjes:)
$string['heading:instructions_1.1'] = 'Course - Voer dit en dat uit om dit en dat te doen..';
$string['heading:instructions_1.2'] = 'Course - Voer dit en dat uit om dit en dat te doen..';
$string['heading:instructions_1.3'] = 'Course - Voer dit en dat uit om dit en dat te doen..';
$string['heading:instructions_2.1'] = 'Cohorts - Voer dit en dat uit om dit en dat te doen..';
$string['heading:instructions_2.2'] = 'Cohorts - Voer dit en dat uit om dit en dat te doen..';
$string['heading:instructions_2.3'] = 'Cohorts - Voer dit en dat uit om dit en dat te doen..';

$string['heading:voucher_type'] = 'Type voucher';
$string['heading:input_voucher'] = 'Voucher invoeren';
$string['heading:input_cohorts'] = 'Selecteer cohorten';
$string['heading:input_course'] = 'Selecteer cursus';
$string['heading:general_settings'] = 'Laatste instellingen';
$string['heading:imageupload'] = 'Upload afbeelding';

$string['heading:info'] = 'Informatie';

//// Info
//$string['info:voucher_type'] = 'Selecteer welk type voucher u wilt genereren.';
//$string['info:voucher_course'] = 'Selecteer welke cursus(sen) u aan uw voucher wilt koppelen.';
//$string['info:voucher_cohorts'] = 'Selecteer hier welke cohort(en) u aan uw voucher wilt koppelen.';
//$string['info:voucher_groups'] = 'Selecteer hier in welke groepen uw gebruikers ingeschreven moeten worden wanneer ze een voucher gebruiken.';
//$string['info:voucher_cohort_courses'] = 'U kunt hier een of meer cursussen aan de geselecteerde cohort(en) koppelen.';
//$string['info:voucher_confirm'] = 'Vul hier de laatste gegevens in om uw vouchers aan te maken.';
//$string['info:imageupload'] = 'Hier kunt u de achtergrond van de voucher uploaden voor gebruik in de voucher PDF bestanden. Het formaat dient .png te zijn.';

// Errors
$string['error:nopermission'] = 'U heeft geen toestemming om dit te doen';
$string['error:required'] = 'Dit is een verplicht veld.';
$string['error:numeric_only'] = 'Dit veld is een verplicht numeriek veld.';
$string['error:invalid_email'] = 'Dit e-mail adres is ongeldig.';
$string['error:invalid_voucher_code'] = 'U heeft een ongeldige vouchercode ingevuld.';
$string['error:voucher_already_used'] = 'Deze voucher is al gebruikt.';
$string['error:voucher_reserved'] = 'Deze voucher is gereserveerd voor een andere gebruiker.';
$string['error:unable_to_enrol'] = 'Een error is opgetreden tijdens het inschrijven in een nieuwe cursus. Neem contact op met support.';
$string['error:missing_course'] = 'De cursus die aan de Voucher is gelinkt bestaat niet meer. Neem contact op met support.';
$string['error:cohort_sync'] = 'Een error is opgetreden tijdens het synchroniseren van de cohortes. Neem contact op met support.';
$string['error:plugin_disabled'] = 'De cohort_sync plugin staat uit. Neem contact op met support.';
$string['error:missing_cohort'] = 'De cohort(en) die aan deze Voucher gelinkt is bestaat niet meer. Neem contact op met support.';
$string['error:missing_group'] = 'De groep(en) die aan deze Voucher gelinkt is bestaat niet meer. Neem contact op met support.';

$string['error:wrong_code_length'] = 'Vul een getal tussen 6 en 32 in.';
$string['error:no_vouchers_submitted'] = 'Er zijn nog geen vouchers ingediend.';
$string['error:voucher_amount_too_high'] = 'Vul een aantal in tussen de {$a->min} en {$a->max}.';
$string['error:alternative_email_required'] = 'Als \'Gebruik alternatief e-mail adres\' is geselecteerd is dit veld verplicht.';
$string['error:alternative_email_invalid'] = 'Als \'Gebruik alternatief e-mail adres\' is geselecteerd moet hier een valide email adres worden ingevuld.';

$string['error:moodledata_not_writable'] = 'U heeft geen schrijfrechten op moodledata/voucher_logos. Pas uw rechten aan.';
$string['error:wrong_image_size'] = 'De afbeelding heeft niet de juiste afmetingen. Probeer het opnieuw met een afbeelding met een verhouding van 210 mm bij 297 mm.';
$string['error:wrong_doc_page'] = 'U probeert een pagina te bereiken die niet bestaat.';

// Success strings
$string['success:voucher_used'] = 'Voucher gebruikt - U kunt nu uw nieuwe cursus(en) in';
$string['success:uploadimage'] = 'Uw nieuwe voucherafbeelding is geupload.';

// URL texts
$string['url:generate_vouchers'] = 'Genereer Voucher';
$string['url:api_docs'] = 'API Documentatie';
$string['url:uploadimage'] = 'Wijzig voucher achtergrond';
$string['url:input_voucher'] = 'Voucher invoeren';
$string['url:view_reports'] = 'Bekijk rapporten';
$string['url:view_unused_vouchers'] = 'Bekijk ongebruikte vouchers';

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
$string['label:voucher_code_help'] = 'De vouchercode is de unieke code die aan iedere voucher is toegekend. U vindt deze code op uw voucher.';
$string['label:enter_voucher_code'] = 'Vul hier uw Voucher code in';

// Labels for already selected stuffz
$string['label:selected_groups'] = 'Geselecteerde groep(en)';
$string['label:selected_course'] = 'Geselecteerde cursus';
$string['label:selected_cohort'] = 'Geselecteerde cohort(en)';


$string['label:alternative_email'] = 'Alternatief e-mail adres';
$string['label:alternative_email_desc'] = 'Vouchers worden standaard naar dit e-mail adres gestuurd indien het veld \'Gebruik alternatief e-mail\' aangevinkt staat.';

$string['label:use_alternative_email'] = 'Gebruik alternatief e-mail adres';
$string['label:use_alternative_email_desc'] = 'Indien aangevinkt wordt het e-mail adres uit \'Alternatief e-mail adres\' standaard gebruikt bij het aanmaken van een voucher.';

$string['label:max_vouchers'] = 'Maximum vouchers';
$string['label:max_vouchers_desc'] = 'Aantal vouchers dat in 1 keer aangemaakt kan worden.';

$string['label:voucher_code_length'] = 'Code length';
$string['label:voucher_code_length_desc'] = 'Aantal karakters van de Vouchercode.';

$string['label:image'] = 'Voucher achtergrond';
$string['label:image_desc'] = 'Achtergrond bij de voucher';

$string['label:current_image'] = 'Huidige voucher achtergrond';

// info labels
$string['label:info_desc'] = 'De informatie die getoond wordt bovenaan het formulier.';
$string['label:info_voucher_type'] = 'Informatie op pagina: Selecteer voucher type';
$string['label:info_voucher_course'] = 'Informatie op pagina: Selecteer course';
$string['label:info_voucher_cohorts'] = 'Informatie op pagina: Selecteer voucher cohorten';
$string['label:info_voucher_course_groups'] = 'Informatie op pagina: Selecteer cursus groepen';
$string['label:info_voucher_cohort_courses'] = 'Informatie op pagina: Cohort cursussen';
$string['label:info_voucher_confirm'] = 'Informatie op pagina: Bevestig voucher';
$string['label:info_imageupload'] = 'Informatie op pagina: Upload afbeelding';

// help texts
$string['label:voucher_type_help'] = 'De vouchers worden gebaseerd op een cursus of een of meer cohorts.';
$string['label:voucher_email_help'] = 'Dit is het e-mail adres waar de gegenereerde vouchers naar toe gestuurd worden.';
$string['label:voucher_amount_help'] = 'Het aantal vouchers dat gegenereerd zal worden. U kunt dit veld OF het Ontvangers veld gebruiken, niet beiden.';

$string['label:api_enabled'] = 'Activeer API';
$string['label:api_enabled_desc'] = 'Met de Voucher API kan men een voucher genereren van een extern systeem.';
$string['label:api_user'] = 'API gebruiker';
$string['label:api_user_desc'] = 'De gebruiker waarmee men de voucher API kan gebruiken.';
$string['label:api_password'] = 'API Password';
$string['label:api_password_desc'] = 'Het wachtwoord waarmee men de voucher API kan gebruiken.';

// buttons
$string['button:next'] = 'Volgende';
$string['button:save'] = 'Genereer Vouchers';
$string['button:submit_voucher_code'] = 'Invoeren';

// view strings
$string['view:generate_voucher:title'] = 'Genereer Voucher';
$string['view:generate_voucher:heading'] = 'Genereer Voucher';

$string['view:reports:heading'] = 'Voucher Rapporten';
$string['view:reports:title'] = 'Voucher Rapporten';

$string['view:input_voucher:title'] = 'Voucher invoeren';
$string['view:input_voucher:heading'] = 'Voucher invoeren';

$string['course'] = 'cursus';
$string['cohort'] = 'cohort';

$string['missing_config_info'] = 'Plaats hier uw extra informatie - in te stellen in de globale configuratie van het blok.';

$string['pdf_generated'] = 'De Vouchers zijn aan dit email bericht toegevoegd als PDF bestanden.<br /><br />';

//$string['default-voucher-page-template'] = '
//<p style="font-weight: bold;">Moodle Voucher</p><br/><br/>
//<p>Middels onderstaande voucher kunt u zich in te schrijven voor de cursus(sen) op Moodle.</p><br/><br/>
//<p><table style="width:100%"><tr><td style="border:1px solid #000000; font-weight: bold; vertical-align: middle; text-align: center;">Dit is uw vouchercode: {vouchercode}</td></tr></table></p>
//<p></p>
//<p>Volg de volgende stappen:</p><br/>
//<ol>
//<li>Open een browser</li>
//<li>Ga naar de website: <a href="{site_url}">{site_url}</a></li>
//<li>Log in met uw gebruikelijke gegevens</li>
//<li>Klik in het blok Voucher op de link "Voucher invoeren"</li>
//<li>Vul het voucher code in (let op deze is hoofdlettergevoelig)</li>
//<li>Je bent nu ingeschreven op de cursussen die bij deze voucher horen.</li>
//</ol><br/><br/>
//<p>Veel leerplezier gewenst!</p>';

$string['default-voucher-page-template-main'] = 'Met deze e-learning voucher activeert u de toegang tot een e-learningmodule. U heeft 90 dagen toegang tot uw module.

Gebruik onderstaande toegangscode om uw voucher te activeren.

Toegangscode: {voucher_code}';
$string['default-voucher-page-template-botleft'] = '<ol>
<li>Meld u aan bij {site_url}</li>
<li>U ontvangt direct een e-mail met de bevestigingslink. Klik op deze link om uw account te activeren.</li>
<li>Vul uw toegangscode in het Moodle Voucher blok.</li>
<li>Veel leerplezier!</li>
</ol>';
$string['default-voucher-page-template-botright'] = '<ol>
<li>Log in bij {site_url}</li>
<li>Vul uw toegangscode in het Moodle Voucher blok.</li>
<li>Veel leerplezier!</li>
</ol>';
//$string['default-voucher-page-template-botleft'] = '
//1. Meld u aan bij {site_url}<br />
//2. U ontvangt direct een e-mail met de bevestigingslink. Klik op deze link om uw account te activeren.<br />
//3. Vul uw toegangscode in het Moodle Voucher blok.<br />
//4. Veel leerplezier!<br />';
//$string['default-voucher-page-template-botright'] = '
//1. Log in bij {site_url}<br />
//2. Vul uw toegangscode in het Moodle Voucher blok.<br />
//3. Veel leerplezier!<br />';


$string['voucher_mail_content'] = '
    Beste {$a->to_name},<br /><br />
    U ontvangt dit bericht omdat er zojuist nieuwe Moodle Vouchers aangemaakt zijn. De Vouchers zijn in de bijlage van dit bericht toegevoegd.<br /><br />
    Met vriendelijke groet,<br /><br />
    {$a->from_name}';
// Mails
$string['voucher_mail_content'] = '
Beste {$a->to_name},<br /><br />

U ontvangt dit bericht omdat er zojuist nieuwe Vouchers zijn gegenereered. De vouchers zijn toegevoegd in de bijlage van dit email bericht.<br /><br />

Met vriendelijke groet,<br /><br />

{$a->from_name}';

$string['voucher_mail_csv_content'] = '
Beste ##to_gender## ##to_name##,<br /><br />

Onlangs heeft u zich ingeschreven voor onze opleiding ##course_fullname##, tijdens de opleiding heeft u toegang tot onze Online Leeromgeving: ##site_name##.<br /><br />

In deze omgeving vindt u naast de lesmateriaal ook de mogelijkheid tot netwerken met uw medecursisten. Deze opleiding start met een aantal voorbereidingsopdrachten, wij willen u vriendelijk verzoeken deze uiterlijk 3 (werk)dagen voor aanvang te bekijken. Zowel u, als de docent, kan zich dan goed voorbereiden op de opleiding.<br /><br />

De lesstof zelf zal uiterlijk 4 werkdagen voor aanvang van de lesdag voor u toegankelijk zijn. Het kan zijn dat op verzoek van de docent eventuele stukken pas ná of op de lesdag beschikbaar gesteld wordt. U ziet dit in de leeromgeving. Tijdens de bijeenkomsten ontvangt u geen gedrukt lesmateriaal, wij adviseren u daarom om een laptop en/of tablet mee te nemen.<br /><br />

Bijgaand treft u de toegangvoucher. Deze voucher is persoonlijk en uniek, en zorgt ervoor dat u toegang krijgt tot uw omgeving van uw opleiding. Lees de instructies op de voucher goed.<br /><br />

Indien u vragen heeft over het aanmaken van een account of problemen ondervindt, kunt u via de site contact zoeken met de helpdesk. Is er geen medewerker direct beschikbaar, laat dan uw naam, mailadres en telefoonnummer achter dan nemen zij z.s.m. contact met u op.<br /><br />

Wij wensen u een leerzame opleiding toe.<br /><br />

Met vriendelijke groet,<br /><br />

##site_name##';

$string['voucher_mail_csv_content_cohorts'] = '
Beste ##to_gender## ##to_name##,<br /><br />

Onlangs heeft u zich ingeschreven voor **HANDMATIG INVULLEN**, tijdens de opleiding heeft u toegang tot onze Online Leeromgeving: ##site_name##.<br /><br />

In deze omgeving vindt u naast de lesmateriaal ook de mogelijkheid tot netwerken met uw medecursisten. Deze opleiding start met een aantal voorbereidingsopdrachten, wij willen u vriendelijk verzoeken deze uiterlijk 3 (werk)dagen voor aanvang te bekijken. Zowel u, als de docent, kan zich dan goed voorbereiden op de opleiding.<br /><br />

De lesstof zelf zal uiterlijk 4 werkdagen voor aanvang van de lesdag voor u toegankelijk zijn. Het kan zijn dat op verzoek van de docent eventuele stukken pas ná of op de lesdag beschikbaar gesteld wordt. U ziet dit in de leeromgeving. Tijdens de bijeenkomsten ontvangt u geen gedrukt lesmateriaal, wij adviseren u daarom om een laptop en/of tablet mee te nemen.<br /><br />

Bijgaand treft u de toegangvoucher. Deze voucher is persoonlijk en uniek, en zorgt ervoor dat u toegang krijgt tot uw omgeving van uw opleiding. Lees de instructies op de voucher goed.<br /><br />

Indien u vragen heeft over het aanmaken van een account of problemen ondervindt, kunt u via de site contact zoeken met de helpdesk. Is er geen medewerker direct beschikbaar, laat dan uw naam, mailadres en telefoonnummer achter dan nemen zij z.s.m. contact met u op.<br /><br />

Wij wensen u een leerzame opleiding toe.<br /><br />

Met vriendelijke groet,<br /><br />

##site_name##';

$string['voucher_mail_subject'] = 'Moodle Voucher aangemaakt';

$string['vouchers_sent'] = 'Uw vouchers zijn aangemaakt. Binnen enkele minuten ontvangt u een email bericht met de vouchers in de bijlage.';
$string['vouchers_ready_to_send'] = 'Uw vouchers zijn aangemaakt en zullen worden verstuurd op het door u opgegeven moment.<br />
    U ontvangt een e-mail bevestiging zodra alle vouchers verstuurd zijn.';

$string['report:status_not_started'] = 'Cursus nog niet gestart';
$string['report:status_started'] = 'Cursus gestart';
$string['report:status_completed'] = 'Cursus afgerond';

$string['report:dateformat'] = '%d-%m-%Y %H:%M:%S';
$string['report:dateformatymd'] = '%d-%m-%Y';

$string['report:heading:username'] = 'Gebruikersnaam';
$string['report:heading:coursename'] = 'Cursus naam';
$string['report:heading:coursetype'] = 'Cursus type';
$string['report:heading:status'] = 'Status';
$string['report:heading:datestart'] = 'Start datum';
$string['report:heading:datecomplete'] = 'Datum afgerond';
$string['report:heading:grade'] = 'Cijfer';

$string['report:owner'] = 'Eigenaar';
$string['report:senddate'] = 'Verstuurdatum';
$string['report:enrolperiod'] = 'Inschrijf periode';
$string['report:voucher_code'] = 'Voucher code';
$string['report:cohorts'] = 'Cohort';
$string['report:issend'] = 'Is verstuurd';
$string['report:immediately'] = 'Onmiddellijk';
$string['report:for_user_email'] = 'Ingepland voor';

$string['str:mandatory'] = 'Mandatory or smthing';
$string['str:optional'] = 'Optional or smthing';

$string['label:voucher_recipients'] = 'Ontvangers';
$string['error:recipients-extension'] = 'U kunt alleen .csv bestanden uploaden.';
$string['error:voucher_amount-recipients-both-set'] = 'Maak alstublieft een keuze uit aantal te genereren vouchers OF een csv van ontvangers.';

$string['label:voucher_recipients_help'] = 'Met dit veld kunt u een csv lijst met gebruikers als ontvangers van de vouchers uploaden.';
$string['label:voucher_recipients_txt'] = 'Ontvangers';
$string['label:voucher_recipients_txt_help'] = 'In dit veld kunt u de laatste aanpassingen aan het csv bestand doen.';
$string['error:voucher_amount-recipients-both-unset'] = 'Dit veld of het veld Ontvangers moet gevuld zijn.';

$string['download-sample-csv'] = 'Download voorbeeld csv';

$string['label:email_body'] = 'Email bericht';
$string['label:email_body_help'] = 'Het email bericht dat wordt verstuurd naar de ontvangers van de vouchers.';
$string['label:redirect_url'] = 'Doorstuur adres';
$string['label:redirect_url_help'] = 'De locatie waar gebruikers naar toe worden gestuurd na het invullen van hun voucher code.';

$string['label:enrolment_period'] = 'Inschrijvingsperiode';
$string['label:enrolment_period_help'] = 'Inschrijvingsperiode (in dagen). Indien 0 ingevuld wordt er geen uitschrijvingsdatum geregistreerd.';

$string['label:date_send_vouchers'] = 'Verzenddatum';
$string['label:date_send_vouchers_help'] = 'Datum dat de vouchers naar de ontvanger(s) verstuurd worden.';

$string['label:showform'] = 'Genereer m.b.v.';
$string['showform-csv'] = 'csv';
$string['showform-manual'] = 'handmatig';
$string['showform-amount'] = 'aantal';

$string['error:recipients-max-exceeded'] = 'Uw bestand is over de maximum aantal regels van 10.000. Limiteer het aantal gebruikers svp.';
$string['error:recipients-invalid'] = 'Uw bestand kon niet gevalideerd worden. Controleer svp of de juiste kolommen en scheidingsteken gebruikt zijn.';
$string['error:recipients-empty'] = 'Vul minstens 1 gebruiker in svp.';
$string['error:recipients-email-invalid'] = 'Het e-mailadres {$a->email} is geen correct e-mailadres. Corrigeer dit eerst in het csv bestand.';

$string['voucher_recipients_desc'] = 'De volgende kolommen dienen aanwezig te zijn ongeacht volgorde: E-mail, Gender, Name';
$string['voucher_recipients_manual_desc'] = 'De volgende kolommen dienen aanwezig te zijn ongeacht volgorde: E-mail, Gender, Name';

$string['confirm_vouchers_sent_subject'] = 'Alle vouchers zijn verzonden';
$string['confirm_vouchers_sent_body'] = '
Hallo,<br /><br />

Bij dezen informeren wij u graag dat de vouchers die u op {$a->timecreated} heeft gemaakt verzonden zijn.<br /><br />

Met vriendelijke groet,<br /><Br />

Moodle administrator';