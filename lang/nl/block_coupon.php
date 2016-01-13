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
 * Language file for block_coupon, NL
 *
 * File         block_coupon.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      Menno de Ridder <menno@sebsoft.nl>
 * @author      R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// Capabilities.
$string['coupon:addinstance'] = 'Voeg een nieuw Coupon blok toe';
$string['coupon:administration'] = 'Beheer het Moodle Coupon blok';
$string['coupon:generatecoupons'] = 'Genereer een nieuwe Moodle Coupon';
$string['coupon:inputcoupons'] = 'Gebruik een Coupon';
$string['coupon:myaddinstance'] = 'Voeg een nieuw Coupon blok toe aan de Mijn Moodle pagina';
$string['coupon:viewreports'] = 'Toon Coupon rapportages (voor mijn coupons)';
$string['coupon:viewallreports'] = 'Toon Coupon rapportages (voor alle coupons)';
$string['error:sessions-expired'] = 'Uw sessie is verlopen.';
$string['promo'] = 'Coupon plugin voor Moodle';
$string['promodesc'] = 'Deze plugin is geschreven door Sebsoft Managed Hosting & Software Development
(<a href=\'http://www.sebsoft.nl/\' target=\'_new\'>http://sebsoft.nl</a>).<br /><br />
{$a}<br /><br />';

// DEFAULT.
$string['blockname'] = 'Coupon';
$string['pluginname'] = 'Coupon';

// Headers.
$string['heading:administration'] = 'Beheer';
$string['heading:generatecoupons'] = 'Coupon genereren';
$string['heading:inputcoupons'] = 'Coupon invoeren';
$string['heading:label_instructions'] = 'Instructies';
$string['heading:coupon_type'] = 'Type coupon';
$string['heading:input_coupon'] = 'Coupon invoeren';
$string['heading:general_settings'] = 'Laatste instellingen';
$string['heading:input_cohorts'] = 'Selecteer cohorten';
$string['heading:input_course'] = 'Selecteer cursus';
$string['heading:input_groups'] = 'Selecteer groepen';
$string['heading:imageupload'] = 'Upload afbeelding';
$string['heading:info'] = 'Informatie';
$string['heading:csvForm'] = 'CSV instellingen';
$string['heading:amountForm'] = 'Aantal instellingen';
$string['heading:manualForm'] = 'Manuele instellingen';

// Errors.
$string['error:nopermission'] = 'U heeft geen toestemming om dit te doen';
$string['error:required'] = 'Dit is een verplicht veld.';
$string['error:numeric_only'] = 'Dit veld is een verplicht numeriek veld.';
$string['error:invalid_email'] = 'Dit e-mail adres is ongeldig.';
$string['error:invalid_coupon_code'] = 'U heeft een ongeldige couponcode ingevuld.';
$string['error:coupon_already_used'] = 'Deze coupon is al gebruikt.';
$string['error:coupon_reserved'] = 'Deze coupon is gereserveerd voor een andere gebruiker.';
$string['error:unable_to_enrol'] = 'Een error is opgetreden tijdens het inschrijven in een nieuwe cursus. Neem contact op met support.';
$string['error:missing_course'] = 'De cursus die aan de Coupon is gelinkt bestaat niet meer. Neem contact op met support.';
$string['error:cohort_sync'] = 'Een error is opgetreden tijdens het synchroniseren van de cohortes. Neem contact op met support.';
$string['error:plugin_disabled'] = 'De cohort_sync plugin staat uit. Neem contact op met support.';
$string['error:missing_cohort'] = 'De cohort(en) die aan deze Coupon gelinkt is bestaat niet meer. Neem contact op met support.';
$string['error:missing_group'] = 'De groep(en) die aan deze Coupon gelinkt is bestaat niet meer. Neem contact op met support.';
$string['error:coupon_amount_too_high'] = 'Vul een aantal in tussen de {$a->min} en {$a->max}.';
$string['error:alternative_email_required'] = 'Als \'Gebruik alternatief e-mail adres\' is geselecteerd is dit veld verplicht.';
$string['error:alternative_email_invalid'] = 'Als \'Gebruik alternatief e-mail adres\' is geselecteerd moet hier een valide email adres worden ingevuld.';
$string['error:course-not-found'] = 'De cursus kon niet gevonden worden.';
$string['error:course-coupons-not-copied'] = 'De coupon cursussen zijn niet correct overgekopieerd naar de nieuwe coupon_courses tabel. Neem contact op met support.';
$string['error:wrong_code_length'] = 'Vul een getal tussen 6 en 32 in.';
$string['error:no_coupons_submitted'] = 'Er zijn nog geen coupons ingediend.';
$string['error:wrong_image_size'] = 'De afbeelding heeft niet de juiste afmetingen. Probeer het opnieuw met een afbeelding met een verhouding van 210 mm bij 297 mm.';
$string['error:moodledata_not_writable'] = 'U heeft geen schrijfrechten op moodledata/coupon_logos. Pas uw rechten aan.';
$string['error:wrong_doc_page'] = 'U probeert een pagina te bereiken die niet bestaat.';

// Success strings.
$string['success:coupon_used'] = 'Coupon gebruikt - U kunt nu uw nieuwe cursus(en) in';
$string['success:uploadimage'] = 'Uw nieuwe couponafbeelding is geupload.';

// URL texts.
$string['url:generate_coupons'] = 'Genereer Coupon';
$string['url:api_docs'] = 'API Documentatie';
$string['url:uploadimage'] = 'Wijzig coupon achtergrond';
$string['url:input_coupon'] = 'Coupon invoeren';
$string['url:view_reports'] = 'Bekijk rapporten';
$string['url:view_unused_coupons'] = 'Bekijk ongebruikte coupons';

// Form Labels.
$string['label:coupon_type'] = 'Genereer gebaseerd op';
$string['label:coupon_email'] = 'E-mail adres';
$string['label:coupon_amount'] = 'Aantal coupons';
$string['label:type_course'] = 'Cursus';
$string['label:type_cohorts'] = 'Cohort(s)';
$string['label:coupon_connect_course'] = 'Cursus(sen) toevoegen';
$string['label:coupon_connect_course_help'] = 'Selecteer de cursussen die aan de cohort moeten worden toegevoegd.
    <br /><b><i>Let op: </i></b>Als er al deelnemers aan die cohort toegevoegd zijn worden deze ook in de cursussen ingeschreven!';
$string['label:connected_courses'] = 'Toegevoegde cursus(sen)';
$string['label:no_courses_connected'] = 'Er zijn nog geen cursussen toegevoegd aan deze cohort.';
$string['label:coupon_courses'] = 'Cursus(sen)';
$string['label:coupon_courses_help'] = 'Selecteer hier de cursussen waar uw studenten op ingeschreven dienen te worden.';
$string['label:coupon_cohorts'] = 'Cohort(en)';
$string['label:cohort'] = 'Cohort';
$string['label:coupon_code'] = 'Coupon Code';
$string['label:coupon_code_help'] = 'De couponcode is de unieke code die aan iedere coupon is toegekend. U vindt deze code op uw coupon.';
$string['label:enter_coupon_code'] = 'Vul hier uw Coupon code in';
$string['label:alternative_email'] = 'Alternatief e-mail adres';
$string['label:alternative_email_help'] = 'Coupons worden standaard naar dit e-mail adres gestuurd indien het veld \'Gebruik alternatief e-mail\' aangevinkt staat.';
$string['label:use_alternative_email'] = 'Gebruik alternatief e-mail adres';
$string['label:use_alternative_email_help'] = 'Indien aangevinkt wordt het e-mail adres uit \'Alternatief e-mail adres\' standaard gebruikt bij het aanmaken van een coupon.';
$string['label:max_coupons'] = 'Maximum coupons';
$string['label:max_coupons_desc'] = 'Aantal coupons dat in 1 keer aangemaakt kan worden.';
$string['label:coupon_code_length'] = 'Code length';
$string['label:coupon_code_length_desc'] = 'Aantal karakters van de Couponcode.';

$string['label:selected_groups'] = 'Geselecteerde groep(en)';
$string['label:selected_courses'] = 'Geselecteerde cursussen';
$string['label:selected_cohort'] = 'Geselecteerde cohort(en)';
$string['label:api_enabled'] = 'Activeer API';
$string['label:api_enabled_desc'] = 'Met de Coupon API kan men een coupon genereren van een extern systeem.';
$string['label:api_user'] = 'API gebruiker';
$string['label:api_user_desc'] = 'De gebruiker waarmee men de coupon API kan gebruiken.';
$string['label:api_password'] = 'API Password';
$string['label:api_password_desc'] = 'Het wachtwoord waarmee men de coupon API kan gebruiken.';
$string['label:generate_pdfs'] = 'Genereer losse PDF\'s';
$string['label:generate_pdfs_help'] = 'Hier kunt u aangeven of u alle coupons in 1 bestand of iedere coupon een apart bestand wilt ontvangen.';
$string['label:info_desc'] = 'De informatie die getoond wordt bovenaan het formulier.';
$string['label:info_coupon_type'] = 'Informatie op pagina: Selecteer coupon type';
$string['label:info_coupon_course'] = 'Informatie op pagina: Selecteer course';
$string['label:info_coupon_cohorts'] = 'Informatie op pagina: Selecteer coupon cohorten';
$string['label:info_coupon_course_groups'] = 'Informatie op pagina: Selecteer cursus groepen';
$string['label:info_coupon_cohort_courses'] = 'Informatie op pagina: Cohort cursussen';
$string['label:info_coupon_confirm'] = 'Informatie op pagina: Bevestig coupon';
$string['label:info_imageupload'] = 'Informatie op pagina: Upload afbeelding';
$string['label:image'] = 'Coupon achtergrond';
$string['label:image_desc'] = 'Achtergrond bij de coupon';
$string['label:current_image'] = 'Huidige coupon achtergrond';
$string['label:coupon_groups'] = 'Groep(en) toevoegen';
$string['label:coupon_groups_help'] = 'Selecteer hier de groepen waar uw gebruikers in toegevoegd moeten worden zodra ze worden ingeschreven bij de cursussen.';
$string['label:no_groups_selected'] = 'Er zijn nog geen groepen aan deze cursus toegevoegd.';
$string['label:coupon_type_help'] = 'De coupons worden gebaseerd op een cursus of een of meer cohorts.';
$string['label:coupon_email_help'] = 'Dit is het e-mail adres waar de gegenereerde coupons naar toe gestuurd worden.';
$string['label:coupon_amount_help'] = 'Het aantal coupons dat gegenereerd zal worden. U kunt dit veld OF het Ontvangers veld gebruiken, niet beiden.';
$string['label:coupon_cohorts_help'] = 'Selecteer een of meer cohort(en) waarin gebruikers zullen worden toegewezen.';
$string['label:coupon_courses_help'] = 'Selecteer een of meer cursus(sen) waarop gebruikers worden ingeschreven.';
// Buttons.
$string['button:next'] = 'Volgende';
$string['button:save'] = 'Genereer Coupons';
$string['button:submit_coupon_code'] = 'Invoeren';

// View strings.
$string['view:generate_coupon:title'] = 'Genereer Coupon';
$string['view:generate_coupon:heading'] = 'Genereer Coupon';
$string['view:reports:heading'] = 'Rapportage - Voortgang voor coupons';
$string['view:reports:title'] = 'Rapportage - Voortgang voor coupons';
$string['view:reports-unused:title'] = 'Rapportage - Ongebruikte coupons';
$string['view:reports-unused:heading'] = 'Rapportage - Ongebruikte coupons';
$string['view:reports-used:title'] = 'Rapportage - Gebruikte coupons';
$string['view:reports-used:heading'] = 'Rapportage - Gebruikte coupons';
$string['view:api:heading'] = 'Coupon API';
$string['view:api:title'] = 'Coupon API';
$string['view:api_docs:heading'] = 'Coupon API Documentatie';
$string['view:api_docs:title'] = 'Coupon API Documentatie';
$string['view:input_coupon:title'] = 'Coupon invoeren';
$string['view:input_coupon:heading'] = 'Coupon invoeren';
$string['view:uploadimage:title'] = 'Coupon achtergrond uploaden';
$string['view:uploadimage:heading'] = 'Een nieuwe coupon achtergrond uploaden';
$string['course'] = 'cursus';
$string['cohort'] = 'cohort';
$string['missing_config_info'] = 'Plaats hier uw extra informatie - in te stellen in de globale configuratie van het blok.';
$string['pdf_generated'] = 'De Coupons zijn aan dit email bericht toegevoegd als PDF bestanden.<br /><br />';
$string['and'] = 'en';

$string['coupons_sent'] = 'Uw coupons zijn aangemaakt. Binnen enkele minuten ontvangt u een email bericht met de coupons in de bijlage.';
$string['coupons_ready_to_send'] = 'Uw coupons zijn aangemaakt en zullen worden verstuurd op het door u opgegeven moment.<br />
    U ontvangt een e-mail bevestiging zodra alle coupons verstuurd zijn.';

// Report.
$string['report:status_not_started'] = 'Cursus nog niet gestart';
$string['report:status_started'] = 'Cursus gestart';
$string['report:status_completed'] = 'Cursus afgerond';
$string['report:dateformat'] = '%d-%m-%Y %H:%M:%S';
$string['report:dateformatymd'] = '%d-%m-%Y';
$string['report:heading:user'] = 'Gebruiker';
$string['report:heading:coursename'] = 'Cursus naam';
$string['report:heading:coursetype'] = 'Cursus type';
$string['report:heading:status'] = 'Status';
$string['report:heading:datestart'] = 'Start datum';
$string['report:heading:datecomplete'] = 'Datum afgerond';
$string['report:heading:grade'] = 'Cijfer';
$string['report:owner'] = 'Eigenaar';
$string['report:senddate'] = 'Verstuurdatum';
$string['report:enrolperiod'] = 'Inschrijf periode';
$string['report:coupon_code'] = 'Coupon code';
$string['report:cohorts'] = 'Cohort';
$string['report:issend'] = 'Is verstuurd';
$string['report:immediately'] = 'Onmiddellijk';
$string['report:for_user_email'] = 'Ingepland voor';
$string['str:mandatory'] = 'Mandatory';
$string['str:optional'] = 'Optional';

$string['download-sample-csv'] = 'Download voorbeeld csv';
$string['pdf:titlename'] = 'Moodle Coupon';
$string['pdf-meta:title'] = 'Moodle Coupon';
$string['pdf-meta:subject'] = 'Moodle Coupon';
$string['pdf-meta:keywords'] = 'Moodle Coupon';
$string['error:sessions-expired'] = 'Je sessie is verlopen. Probeer aub opnieuw.';
$string['label:coupon_recipients'] = 'Ontvangers';
$string['error:recipients-extension'] = 'U kunt alleen .csv bestanden uploaden.';
$string['error:coupon_amount-recipients-both-set'] = 'Maak alstublieft een keuze uit aantal te genereren coupons OF een csv van ontvangers.';
$string['label:coupon_recipients_help'] = 'Met dit veld kunt u een csv lijst met gebruikers als ontvangers van de coupons uploaden.';
$string['label:coupon_recipients_txt'] = 'Ontvangers';
$string['label:coupon_recipients_txt_help'] = 'In dit veld kunt u de laatste aanpassingen aan het csv bestand doen.';
$string['error:coupon_amount-recipients-both-unset'] = 'Dit veld of het veld Ontvangers moet gevuld zijn.';
$string['label:email_body'] = 'Email bericht';
$string['label:email_body_help'] = 'Het email bericht dat wordt verstuurd naar de ontvangers van de coupons.';
$string['label:redirect_url'] = 'Doorstuur adres';
$string['label:redirect_url_help'] = 'De locatie waar gebruikers naar toe worden gestuurd na het invullen van hun coupon code.';
$string['label:enrolment_period'] = 'Inschrijvingsperiode';
$string['label:enrolment_period_help'] = 'Inschrijvingsperiode (in dagen). Indien 0 ingevuld wordt er geen uitschrijvingsdatum geregistreerd.';
$string['label:date_send_coupons'] = 'Verzenddatum';
$string['label:date_send_coupons_help'] = 'Datum dat de coupons naar de ontvanger(s) verstuurd worden.';
$string['label:showform'] = 'Opties';
$string['showform-csv'] = 'Ik wil coupons aanmaken door een CSV met ontvangers te uploaden';
$string['showform-manual'] = 'Ik wil coupons aanmaken door manueel de ontvangers op te geven';
$string['showform-amount'] = 'Ik wil een arbitrair aantal coupons aanmaken';
$string['error:recipients-max-exceeded'] = 'Uw bestand is over de maximum aantal regels van 10.000. Limiteer het aantal gebruikers svp.';
$string['error:recipients-columns-missing'] = 'Uw bestand kon niet gevalideerd worden. Controleer svp of de juiste kolommen en scheidingsteken gebruikt zijn.';
$string['error:recipients-invalid'] = 'Uw bestand kon niet gevalideerd worden. Controleer svp of de juiste kolommen en scheidingsteken gebruikt zijn.';
$string['error:recipients-empty'] = 'Vul minstens 1 gebruiker in svp.';
$string['error:recipients-email-invalid'] = 'Het e-mailadres {$a->email} is geen correct e-mailadres. Corrigeer dit eerst in het csv bestand.';
$string['coupon_recipients_desc'] = 'De volgende kolommen dienen aanwezig te zijn in de CSV, ongeacht volgorde: E-mail, Gender, Name.<br/>
Voor elke persoon in de CSV zal er een coupon worden gegenereerd en naar zijn/haar e-mailadres worden gestuurd.<br/>
Houdt er aub rekening mee dat de coupons <i>niet</i> direct worden gegegereerd, maar worden verwerkt door een a-synchroon achtergrondproces (taak).<br/>
Dit is omdat het process van genereren van coupons behoorlijk intensief kan zijn, met name voor een groter aantal ontvangers.';
$string['report:download-excel'] = 'Download ongebruikte coupons';

$string['page:generate_coupon.php:title'] = 'Genereer coupons';
$string['page:generate_coupon_step_two.php:title'] = 'Genereer coupons';
$string['page:generate_coupon_step_three.php:title'] = 'Genereer coupons';
$string['page:generate_coupon_step_four.php:title'] = 'Genereer coupons';
$string['page:generate_coupon_step_five.php:title'] = 'Genereer coupons';
$string['page:unused_coupons.php:title'] = 'Ongebruikte coupons';
$string['th:owner'] = 'Eigenaar';
$string['th:senddate'] = 'Verzenddatum';
$string['th:enrolperiod'] = 'Inschrijvingsduur';
$string['th:submission_code'] = 'Aanmeldcode';
$string['th:cohorts'] = 'Cohort';
$string['th:groups'] = 'Groep(en)';
$string['th:course'] = 'Cursus';
$string['th:issend'] = 'Verzonden?';
$string['th:immediately'] = 'Direct';
$string['th:for_user_email'] = 'Ingepland voor';

$string['tab:wzcoupons'] = 'Genereer coupon(s)';
$string['tab:wzcouponimage'] = 'Template afbeelding';
$string['tab:apidocs'] = 'API Documentatie';
$string['tab:report'] = 'Voortgangsrapportage';
$string['tab:unused'] = 'Ongebruikte coupons';
$string['tab:used'] = 'Gebruikte coupons';
$string['task:sendcoupons'] = 'Ingeplande coupons versturen';

// Mails.
$string['confirm_coupons_sent_subject'] = 'Alle coupons zijn verzonden';
$string['confirm_coupons_sent_body'] = '
Hallo,<br /><br />

Bij dezen informeren wij u graag dat de coupons die u op {$a->timecreated} heeft gemaakt verzonden zijn.<br /><br />

Met vriendelijke groet,<br /><Br />

Moodle administrator';

$string['days_access'] = '{$a} dagen';
$string['unlimited_access'] = 'onbeperkt';
$string['default-coupon-page-template-main'] = 'Met deze e-learning coupon activeert u de toegang tot een e-learningmodule. U heeft {accesstime} toegang tot uw module.

Gebruik onderstaande toegangscode om uw coupon te activeren.

Toegangscode: {coupon_code}';
$string['default-coupon-page-template-botleft'] = '<ol>
<li>Meld u aan bij {site_url}</li>
<li>U ontvangt direct een e-mail met de bevestigingslink. Klik op deze link om uw account te activeren.</li>
<li>Vul uw toegangscode in het Moodle Coupon blok.</li>
<li>Veel leerplezier!</li>
</ol>';
$string['default-coupon-page-template-botright'] = '<ol>
<li>Log in bij {site_url}</li>
<li>Vul uw toegangscode in het Moodle Coupon blok.</li>
<li>Veel leerplezier!</li>
</ol>';

$string['coupon_mail_content'] = '
Beste {$a->to_name},<br /><br />

U ontvangt dit bericht omdat er zojuist nieuwe Coupons zijn gegenereered. De coupons zijn toegevoegd in de bijlage van dit email bericht.<br /><br />

Met vriendelijke groet,<br /><br />

{$a->from_name}';

$string['coupon_mail_csv_content'] = '
Beste ##to_gender## ##to_name##,<br /><br />

Onlangs heeft u zich ingeschreven voor onze opleidingen ##course_fullnames##. Tijdens de opleidingen heeft u toegang tot onze Online Leeromgeving: ##site_name##.<br /><br />

In deze omgeving vindt u naast de lesmateriaal ook de mogelijkheid tot netwerken met uw medecursisten. Deze opleiding start met een aantal voorbereidingsopdrachten, wij willen u vriendelijk verzoeken deze uiterlijk 3 (werk)dagen voor aanvang te bekijken. Zowel u, als de docent, kan zich dan goed voorbereiden op de opleiding.<br /><br />

De lesstof zelf zal uiterlijk 4 werkdagen voor aanvang van de lesdag voor u toegankelijk zijn. Het kan zijn dat op verzoek van de docent eventuele stukken pas ná of op de lesdag beschikbaar gesteld wordt. U ziet dit in de leeromgeving. Tijdens de bijeenkomsten ontvangt u geen gedrukt lesmateriaal, wij adviseren u daarom om een laptop en/of tablet mee te nemen.<br /><br />

Bijgaand treft u de toegangcoupon. Deze coupon is persoonlijk en uniek, en zorgt ervoor dat u toegang krijgt tot uw omgeving van uw opleiding. Lees de instructies op de coupon goed.<br /><br />

Indien u vragen heeft over het aanmaken van een account of problemen ondervindt, kunt u via de site contact zoeken met de helpdesk. Is er geen medewerker direct beschikbaar, laat dan uw naam, mailadres en telefoonnummer achter dan nemen zij z.s.m. contact met u op.<br /><br />

Wij wensen u een leerzame opleiding toe.<br /><br />

Met vriendelijke groet,<br /><br />

##site_name##';

$string['coupon_mail_csv_content_cohorts'] = '
Beste ##to_gender## ##to_name##,<br /><br />

Onlangs heeft u zich ingeschreven voor **HANDMATIG INVULLEN**, tijdens de opleiding heeft u toegang tot onze Online Leeromgeving: ##site_name##.<br /><br />

In deze omgeving vindt u naast de lesmateriaal ook de mogelijkheid tot netwerken met uw medecursisten. Deze opleiding start met een aantal voorbereidingsopdrachten, wij willen u vriendelijk verzoeken deze uiterlijk 3 (werk)dagen voor aanvang te bekijken. Zowel u, als de docent, kan zich dan goed voorbereiden op de opleiding.<br /><br />

De lesstof zelf zal uiterlijk 4 werkdagen voor aanvang van de lesdag voor u toegankelijk zijn. Het kan zijn dat op verzoek van de docent eventuele stukken pas ná of op de lesdag beschikbaar gesteld wordt. U ziet dit in de leeromgeving. Tijdens de bijeenkomsten ontvangt u geen gedrukt lesmateriaal, wij adviseren u daarom om een laptop en/of tablet mee te nemen.<br /><br />

Bijgaand treft u de toegangcoupon. Deze coupon is persoonlijk en uniek, en zorgt ervoor dat u toegang krijgt tot uw omgeving van uw opleiding. Lees de instructies op de coupon goed.<br /><br />

Indien u vragen heeft over het aanmaken van een account of problemen ondervindt, kunt u via de site contact zoeken met de helpdesk. Is er geen medewerker direct beschikbaar, laat dan uw naam, mailadres en telefoonnummer achter dan nemen zij z.s.m. contact met u op.<br /><br />

Wij wensen u een leerzame opleiding toe.<br /><br />

Met vriendelijke groet,<br /><br />

##site_name##';

$string['coupon_mail_subject'] = 'Moodle Coupon aangemaakt';
$string['th:action'] = 'Actie(s)';
$string['action:coupon:delete'] = 'Coupon verwijderen';
$string['action:coupon:delete:confirm'] = 'Weet je zeker dat je deze coupon wilt verwijderen? Dit kan niet ongedaan worden gemaakt!';
$string['coupon:deleted'] = 'Coupon is verwijderd';

$string['textsettings'] = 'Tekst instellingen';
$string['textsettings_desc'] = 'Hier kun je teksten ingeven die door diverse pagina\'s binnen de coupon wizard worden gebruikt';
$string['task:cleanup'] = 'Automatisch verwijderen ongebruikte coupons';
$string['tasksettings'] = 'Taak instellingen';
$string['tasksettings_desc'] = '';
$string['label:enablecleanup'] = 'Automatisch verwijderen ongebruikte coupons?';
$string['label:enablecleanup_help'] = 'Vink deze optie aan als je automatisch ongebruikte coupons wilt laten verwijderen';
$string['label:cleanupage'] = 'Maximum leeftijd?';
$string['label:cleanupage_help'] = 'Voer de maximum leeftijd in van de ongebruikte coupon voordat deze automatisch zal worden verwijderd';

$string['coupon:send:fail'] = 'Verzenden van e-mail mislukt! Reden: {$a}';
$string['view:errorreport:heading'] = 'Rapport - Coupon fouten';
$string['view:errorreport:title'] = 'Rapport - Coupon fouten';
$string['report:heading:coupon'] = 'Coupon';
$string['report:heading:errortype'] = 'Type';
$string['report:heading:errormessage'] = 'Foutmelding';
$string['report:heading:timecreated'] = 'Datum';
$string['report:heading:action'] = 'Actie(s)';
$string['action:error:delete'] = 'Foutmelding verwijderen';
$string['tab:errors'] = 'Foutrapportage';