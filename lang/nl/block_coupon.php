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
 * Language file for block_coupon
 *
 * File         block_coupon.php
 * Encoding     UTF-8
 *
 * @package     block_coupon
 *
 * @copyright   Sebsoft.nl
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['action:coupon:delete'] = 'Coupon verwijderen';
$string['action:coupon:delete:confirm'] = 'Weet je zeker dat je deze coupon wilt verwijderen? Dit kan niet ongedaan worden gemaakt!';
$string['action:coursegrouping:delete'] = 'Groepering verwijderen';
$string['action:coursegrouping:details'] = 'Details bekijken';
$string['action:coursegrouping:edit'] = 'Groepering bewerken';
$string['action:error:delete'] = 'Foutmelding verwijderen';
$string['addcohortstoexisting'] = 'Sitegroepen toevoegen aan coupon? Indien niet aangevinkt worden alle sitegroepen eerst ontkoppeld.';
$string['addcoursestoexisting'] = 'Cursussen toevoegen aan coupon? Indien niet aangevinkt worden alle cursussen eerst ontkoppeld.';
$string['and'] = 'en';
$string['batchidselect'] = 'Batch ID';
$string['blockname'] = 'Coupon';
$string['button:continue'] = 'Doorgaan';
$string['button:next'] = 'Volgende';
$string['button:save'] = 'Genereer Coupons';
$string['button:submit_coupon_code'] = 'Invoeren';
$string['choose:courses:explain'] = 'Kies hieronder de cursus(sen) waarop je je wenst aan te melden.<br/>
Met deze coupon kun je maximaal {$a->maxamount} cursus(sen) kiezen.';
$string['cleanup:confirm:confirmmessage'] = 'Ja, ik wil de coupons met deze opties verwijderen';
$string['cleanup:confirm:header'] = 'Bevestig a.u.b. het verwijderen van coupons met de volgende opties';
$string['clientref'] = 'Klant referentie';
$string['cohort'] = 'cohort';
$string['confirm_coupons_sent_body'] = '
Hallo,<br /><br />

Bij deze informeren wij u graag dat de coupons die u op {$a->timecreated} met batchnummer {$a->batchid} heeft gemaakt verzonden zijn.<br /><br />

Met vriendelijke groet,<br /><br />

Moodle administrator';
$string['confirm_coupons_sent_subject'] = 'Alle coupons zijn verzonden';
$string['coupon:addinstance'] = 'Voeg een nieuw Coupon blok toe';
$string['coupon:administration'] = 'Beheer het Moodle Coupon blok';
$string['coupon:claim:wronguser'] = 'Deze gepersonaliseerde coupon is <i>niet</i> door jou te claimen';
$string['coupon:cleanup:heading'] = 'Coupons opschonen';
$string['coupon:cleanup:info'] = 'Gebruik dit formulier om coupons te verwijderen uit het systeem.<br/>
<b>Waarschuwing:</b> Dit proces <i>verwijdert</i> coupons uit het systeem, er is geen enkele manier om de verwijderde coupons terug te halen wanneer dit proces afgerond is';
$string['coupon:coursegrouping:heading'] = 'Cursusgroepering configureren';
$string['coupon:deleted'] = 'Coupon is verwijderd';
$string['coupon:extendenrol'] = 'Inschrijvingsverlenging coupons';
$string['coupon:extendenrolments'] = 'Coupons genereren tbv verlenging cursus aanmelding';
$string['coupon:extenrol:summary'] = 'Coupon type: {$a->coupontype}<br/>
Aantal te genereren coupons: {$a->amount}<br/>
Gebruikte achtergrond voor coupon(s): {$a->logo}<br/>
Coupons gegenereerd door: {$a->owner}<br/>
Geselecteerde cursus(sen): {$a->courses}<br/>
Verlengingsperiode: {$a->duration}<br/>
Verzend coupon(s) op: {$a->senddate}<br/>
Verzend coupon(s) naar: {$a->recipient}<br/><br/>
Email-body: {$a->emailbody}<br/>
';
$string['coupon:generatecoupons'] = 'Genereer een nieuwe Moodle Coupon';
$string['coupon:inputcoupons'] = 'Gebruik een Coupon';
$string['coupon:myaddinstance'] = 'Voeg een nieuw Coupon blok toe aan de Mijn Moodle pagina';
$string['coupon:send:fail'] = 'Verzenden van e-mail mislukt! Reden: {$a}';
$string['coupon:senddate:instant'] = 'Direct';
$string['coupon:timeframe'] = 'Type';
$string['coupon:type'] = 'Type';
$string['coupon:type:all'] = 'Alle';
$string['coupon:type:cohort'] = 'Sitegroep aanmelding';
$string['coupon:type:course'] = 'Cursusaanmelding';
$string['coupon:type:coursegrouping'] = 'Cursusgroepering aanmelding';
$string['coupon:type:enrolext'] = 'Aanmeldingsverlenging';
$string['coupon:used'] = 'Verwijderen';
$string['coupon:used:all'] = 'Alle coupons';
$string['coupon:used:no'] = 'Enkel ongebruikte coupons';
$string['coupon:used:yes'] = 'Enkel gebruikte coupons';
$string['coupon:user:heading'] = 'Gebruikersconfiguratie voor {$a->firstname} {$a->lastname}';
$string['coupon:user:info'] = 'Gebruik onderstaand formulier om aan te geven welke opties en voor welke cursussen de gebruiker coupons kan/mag aanvragen';
$string['coupon:viewallreports'] = 'Toon Coupon rapportages (voor alle coupons)';
$string['coupon:viewreports'] = 'Toon Coupon rapportages (voor mijn coupons)';
$string['coupon_mail_content'] = '<p>Beste {$a->fullname},</p>
<p>U ontvangt dit bericht omdat er zojuist nieuwe Coupons zijn gegenereered.<br/>
De coupons zijn als download beschikbaar gemaakt binnen de leeromgeving.<br /><br />
Klik a.u.b {$a->downloadlink} om je coupons op te halen.</p>
<p>Met vriendelijke groet,<br /><br />
{$a->signoff}</p>';
$string['coupon_mail_csv_content'] = '
Beste ##to_gender## ##to_name##,<br /><br />

Onlangs heeft u zich ingeschreven voor onze opleidingen ##course_fullnames##. Tijdens de opleidingen heeft u toegang tot onze Online Leeromgeving: ##site_name##.<br /><br />

In deze omgeving vindt u naast de lesmateriaal ook de mogelijkheid tot netwerken met uw medecursisten. Deze opleiding start met een aantal voorbereidingsopdrachten, wij willen u vriendelijk verzoeken deze uiterlijk 3 (werk)dagen voor aanvang te bekijken. Zowel u, als de docent, kan zich dan goed voorbereiden op de opleiding.<br /><br />

De lesstof zelf zal uiterlijk 4 werkdagen voor aanvang van de lesdag voor u toegankelijk zijn. Het kan zijn dat op verzoek van de docent eventuele stukken pas ná of op de lesdag beschikbaar gesteld wordt. U ziet dit in de leeromgeving. Tijdens de bijeenkomsten ontvangt u geen gedrukt lesmateriaal, wij adviseren u daarom om een laptop en/of tablet mee te nemen.<br /><br />

De code waarmee je je kunt aanmelden is: ##submission_code##<br/><br/>

Deze coupon is persoonlijk en uniek, en zorgt ervoor dat u toegang krijgt tot uw omgeving van uw opleiding. Lees de instructies op de coupon goed.<br /><br />

Indien u vragen heeft over het aanmaken van een account of problemen ondervindt, kunt u via de site contact zoeken met de helpdesk. Is er geen medewerker direct beschikbaar, laat dan uw naam, mailadres en telefoonnummer achter dan nemen zij z.s.m. contact met u op.<br /><br />

Wij wensen u een leerzame opleiding toe.<br /><br />

Met vriendelijke groet,<br /><br />

##site_name##';
$string['coupon_mail_csv_content_cohorts'] = '
Beste ##to_gender## ##to_name##,<br /><br />

Onlangs heeft u zich ingeschreven voor **HANDMATIG INVULLEN**, tijdens de opleiding heeft u toegang tot onze Online Leeromgeving: ##site_name##.<br /><br />

In deze omgeving vindt u naast de lesmateriaal ook de mogelijkheid tot netwerken met uw medecursisten. Deze opleiding start met een aantal voorbereidingsopdrachten, wij willen u vriendelijk verzoeken deze uiterlijk 3 (werk)dagen voor aanvang te bekijken. Zowel u, als de docent, kan zich dan goed voorbereiden op de opleiding.<br /><br />

De lesstof zelf zal uiterlijk 4 werkdagen voor aanvang van de lesdag voor u toegankelijk zijn. Het kan zijn dat op verzoek van de docent eventuele stukken pas ná of op de lesdag beschikbaar gesteld wordt. U ziet dit in de leeromgeving. Tijdens de bijeenkomsten ontvangt u geen gedrukt lesmateriaal, wij adviseren u daarom om een laptop en/of tablet mee te nemen.<br /><br />

De code waarmee je je kunt aanmelden is: ##submission_code##<br/><br/>

Deze coupon is persoonlijk en uniek, en zorgt ervoor dat u toegang krijgt tot uw omgeving van uw opleiding. Lees de instructies op de coupon goed.<br /><br />

Indien u vragen heeft over het aanmaken van een account of problemen ondervindt, kunt u via de site contact zoeken met de helpdesk. Is er geen medewerker direct beschikbaar, laat dan uw naam, mailadres en telefoonnummer achter dan nemen zij z.s.m. contact met u op.<br /><br />

Wij wensen u een leerzame opleiding toe.<br /><br />

Met vriendelijke groet,<br /><br />

##site_name##';
$string['coupon_mail_extend_content'] = 'Beste ##to_gender## ##to_name##,<br /><br />

Je bent aangemeld voor onze training ##course_fullnames## en hebt een optie tot verlenging gekregen.
Je hebt reeds toegang tot onze Online Leeromgeving: ##site_name##.<br /><br />
Je verlenging is voor ##extensionperiod##.<br /><br />

Je kunt de coupon voor verlenging in de bijlage vinden. Deze coupon is gepersonaliseerd en uniek, en zal verlenging verlenen tot de aangegeven cursus(sen).
Lees aub de instructies op de coupon goed.<br /><br />

Wanneer je vragen hebt of of andere vragen hebt, kun je de helpdesk contacteren.
Informatie kan gevonden worden op onze leeromgeving.
Indien er niemand beschibaar is om je vraag te beantwoorden, laat dan a.u.b. je naam, e-mailadres en telefoonnummer achter en we zullen zo snel mogelijk proberen te reageren.<br /><br />

Met vriendelijke groet,<br /><br />

##site_name##';
$string['coupon_mail_subject'] = 'Moodle Coupon aangemaakt';
$string['coupon_notification_content'] = '<p>Je ontvangt dit bericht omdat er zojuist nieuwe coupons zijn gegenereered.<br/>
Je zou een email moeten hebben ontvangen met de details en download link.<br />
Je kunt ook direct de coupon(s) downloaden door {$a->downloadlink} te klikken.</p>
';
$string['coupon_notification_subject'] = 'Coupons generated!';
$string['coupon_recipients_desc'] = 'De volgende kolommen dienen aanwezig te zijn in de CSV, ongeacht volgorde: E-mail, Gender, Name.<br/>
Voor elke persoon in de CSV zal er een coupon worden gegenereerd en naar zijn/haar e-mailadres worden gestuurd.<br/>
Houdt er aub rekening mee dat de coupons <i>niet</i> direct worden gegegereerd, maar worden verwerkt door een a-synchroon achtergrondproces (taak).<br/>
Dit is omdat het process van genereren van coupons behoorlijk intensief kan zijn, met name voor een groter aantal ontvangers.';
$string['coupons:cleaned'] = '{$a} coupons zijn opgeschoond / verwijderd.';
$string['coupons_generated'] = '<p>Uw coupon(s) zijn aangemaakt.<br/>
Je zou een email moeten ontvangen met de link om de coupons te downloaden.<br/>
Je kunt er ook voor kiezen de coupons direct te downloaden door {$a} te klikken.</p>.';
$string['coupons_generated_codes_only'] = '<p>Uw couponcode(s) zijn aangemaakt.<br/>
Je krijgt hierover geen email melding aangezien je hebt gekozen alleen de codes te genereren<br/>
Je kunt het overzicht voor (on)gebruikte coupons gebruiken in combinatie met een filter op de batch ID om een overzicht van de gegenereerde couponcodes te downloaden</p>.';
$string['coupons_ready_to_send'] = 'Uw coupons zijn aangemaakt en zullen worden verstuurd op het door u opgegeven moment.<br />
U ontvangt een e-mail bevestiging zodra alle coupons verstuurd zijn.';
$string['coupons_sent'] = 'Uw coupons zijn aangemaakt. Binnen enkele minuten ontvangt u een email bericht met de coupons in de bijlage.';
$string['course'] = 'cursus';
$string['coursegrouping'] = 'Cursusgroepering';
$string['coursegrouping-details'] = 'Cursusgroepering details';
$string['days_access'] = '{$a} dagen';
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
$string['default-coupon-page-template-main'] = 'Met deze coupon activeert u de toegang tot de volgende e-learningmodule(s):<br/>
{courses}<br/>
U heeft {accesstime} toegang tot uw module.<br/><br/>
Gebruik onderstaande toegangscode om uw coupon te activeren<br/>
Toegangscode: {coupon_code}';
$string['delete:coursegrouping:confirmmessage'] = 'Ik wil deze groepering verwijderen';
$string['delete:coursegrouping:header'] = 'Bevestig verwijderen van cursusgroepering';
$string['delete:coursegrouping:successmsg'] = 'Coursegrouping successfully deleted';
$string['delete:request:confirmmessage'] = 'Ja, ik wil deze aanvraag verwijderen';
$string['delete:request:header'] = 'Verwijder mijn couponaanvraag';
$string['delete:request:title'] = 'Verwijder mijn couponaanvraag';
$string['delete:requestuser:confirmmessage'] = 'Ja, ik wil de aanvraagrechten voor deze gebruiker verwijderen';
$string['delete:requestuser:description'] = 'Dit zal de rechten voor het aanvragen van coupons verwijderen voor <b>{$a->firstname} {$a->lastname}</b>.<br/>
Dit proces is onomkeerbaar maar je kunt het account altijd weer toevoegen aan de lijst van toegestane gebruikers en de instellingen opnieuw ingeven.';
$string['delete:requestuser:header'] = 'Verwijder gebruikerrechten tbv couponaanvragen';
$string['download-sample-csv'] = 'Download voorbeeld csv';
$string['downloadcoupons:buttontext'] = 'Klik a.u.b. hier om je download te starten';
$string['downloadcoupons:text'] = '<div>Je kunt nu je coupons downloaden door op de onderstaande link te klikken.<br/>
Houd er a.u.b. rekening mee dat dit slechts <i>één</i> maal mogelijk is<br/>
Zodra je de coupons hebt gedownload, wordt het relevante bestand verwijderd.<br/>
{$a}
</div>';
$string['enrolperiod:extension'] = 'voor een lengte van {$a}';
$string['enrolperiod:indefinite'] = '<i>Onbeperkt</i>';
$string['err:batchid'] = 'Batch naam bestaat al. Kies een andere of laat dit veld leeg';
$string['err:choose:atleastone'] = 'Kies aub een cursus';
$string['err:choose:maxamount'] = 'Je mag maximaal {$a} cursus(sen) kiezen';
$string['err:codesize:left'] = 'Code lengte fout: voor {$a->want} coupons van {$a->size} karakters hebben we slechts {$a->left} codes over (met de huidige karakterset)!';
$string['err:coupon:generic'] = 'Er is iets foutgegaan. Neem aub contant op met de systeembeheerder';
$string['err:download-not-exists'] = 'Het archief dat je wilt downloaden betaat niet meer<br/>
Waarschijnlijk heb je deze al een keer gedownload en is deze daarna verwijderd.<br/>
Wanneer je er zeker van bent dat je zelf de aangemaakte coupons <i>niet</i> hebt gedownload, neem dan aub contact op met de systeembeheerder.';
$string['err:myrequests:finalized'] = 'Dit coupon verzoek is reeds gefinaliseerd.';
$string['err:not-a-requestuser'] = 'Je hebt onvoldoende rechten om deze pagina te bezoeken';
$string['error:already-enrolled-in-cohorts'] = 'Je bent al in alle sitegroepen aangemeld';
$string['error:already-enrolled-in-courses'] = 'Je bent al in alle cursussen aangemeld';
$string['error:alternative_email_invalid'] = 'Als \'Gebruik alternatief e-mail adres\' is geselecteerd moet hier een valide email adres worden ingevuld.';
$string['error:alternative_email_required'] = 'Als \'Gebruik alternatief e-mail adres\' is geselecteerd is dit veld verplicht.';
$string['error:cohort_sync'] = 'Een error is opgetreden tijdens het synchroniseren van de cohortes. Neem contact op met support.';
$string['error:coupon:generator'] = 'Er hebben zich fouten voorgedaan tijdens het genereren:<br/>{$a}';
$string['error:coupon_already_used'] = 'Deze coupon is al gebruikt.';
$string['error:coupon_amount-recipients-both-set'] = 'Maak alstublieft een keuze uit aantal te genereren coupons OF een csv van ontvangers.';
$string['error:coupon_amount-recipients-both-unset'] = 'Dit veld of het veld Ontvangers moet gevuld zijn.';
$string['error:coupon_amount_too_high'] = 'Vul een aantal in tussen de {$a->min} en {$a->max}.';
$string['error:coupon_reserved'] = 'Deze coupon is gereserveerd voor een andere gebruiker.';
$string['error:course-coupons-not-copied'] = 'De coupon cursussen zijn niet correct overgekopieerd naar de nieuwe coupon_courses tabel. Neem contact op met support.';
$string['error:course-not-found'] = 'De cursus kon niet gevonden worden.';
$string['error:grouping-not-found'] = 'Groepering niet gevonden';
$string['error:invalid_coupon_code'] = 'U heeft een ongeldige couponcode ingevuld.';
$string['error:invalid_email'] = 'Dit e-mail adres is ongeldig.';
$string['error:missing_cohort'] = 'De cohort(en) die aan deze Coupon gelinkt is bestaat niet meer. Neem contact op met support.';
$string['error:missing_course'] = 'De cursus die aan de Coupon is gelinkt bestaat niet meer. Neem contact op met support.';
$string['error:missing_group'] = 'De groep(en) die aan deze Coupon gelinkt is bestaat niet meer. Neem contact op met support.';
$string['error:moodledata_not_writable'] = 'U heeft geen schrijfrechten op moodledata/coupon_logos. Pas uw rechten aan.';
$string['error:myrequests:user'] = 'Je mag dit verzoek niet namens een andere uitvoeren';
$string['error:no-more-course-choices'] = 'Er zijn geen cursussen meer waaruit je kunt selecteren.<br/>
Het lijkt erop dat je al bent aangemeld in alle cursussen waar deze coupon voor geldt.<br/>
<br/>Als je denkt dat dit niet klopt, neem dan a.u.b. contact op met de systeembeheerder.';
$string['error:no_coupons_submitted'] = 'Er zijn nog geen coupons ingediend.';
$string['error:nopermission'] = 'U heeft geen toestemming om dit te doen';
$string['error:numeric_only'] = 'Dit veld is een verplicht numeriek veld.';
$string['error:plugin_disabled'] = 'De cohort_sync plugin staat uit. Neem contact op met support.';
$string['error:recipients-columns-missing'] = 'Uw bestand kon niet gevalideerd worden. Controleer svp of de juiste kolommen en scheidingsteken gebruikt zijn.<br/>
De volgende kolommen <i>moeten</i> in dezelfde benaming in de eerste rij aanwezig zijn: {$a}';
$string['error:recipients-email-invalid'] = 'Het e-mailadres {$a->email} is geen correct e-mailadres. Corrigeer dit eerst in het csv bestand.';
$string['error:recipients-empty'] = 'Vul minstens 1 gebruiker in svp.';
$string['error:recipients-extension'] = 'U kunt alleen .csv bestanden uploaden.';
$string['error:recipients-invalid'] = 'Uw bestand kon niet gevalideerd worden. Controleer svp of de juiste kolommen en scheidingsteken gebruikt zijn.';
$string['error:recipients-max-exceeded'] = 'Uw bestand is over de maximum aantal regels van 10.000. Limiteer het aantal gebruikers svp.';
$string['error:required'] = 'Dit is een verplicht veld.';
$string['error:sessions-expired'] = 'Je sessie is verlopen. Probeer aub opnieuw.';
$string['error:unable_to_enrol'] = 'Een error is opgetreden tijdens het inschrijven in een nieuwe cursus. Neem contact op met support.';
$string['error:validate-courses'] = 'Course validation errors:
{$a}';
$string['error:validate-groupings'] = 'Groeperingsvalidatie fouten:<br/>{$a}';
$string['error:wrong_code_length'] = 'Vul een getal tussen 6 en 32 in.';
$string['error:wrong_doc_page'] = 'U probeert een pagina te bereiken die niet bestaat.';
$string['error:wrong_image_size'] = 'De afbeelding heeft niet de juiste afmetingen. Probeer het opnieuw met een afbeelding met een verhouding van 210 mm bij 297 mm.';
$string['extendaccess'] = '{$a} extra';
$string['extendenrol:abort-no-users'] = 'Fout: er zijn geen gebruikers gevonden waarvoor de aanmelding kan worden verlengd<br/>
Alle gebruikers hebben reeds een permanente aanmelding of er zijn geen gebruikers gevnden met een (manuele) aanmelding voor deze cursus(sen).';
$string['findcohortcourses:noselectionstring'] = 'Nog geen selectie gemaakt';
$string['findcohorts:noselectionstring'] = 'Geen sitegroep(en) geselecteerd';
$string['findcohorts:placeholder'] = '... selecteer sitegroep(en) ...';
$string['findcourses'] = 'Toegestane cursussen';
$string['findcourses:noselectionstring'] = 'Geen cursus(sen) geselecteerd';
$string['findcourses:placeholder'] = '... selecteer cursus(sen) ...';
$string['findcourses_help'] = 'De geselecteerde / toegevoegde cursussen zijn de enige die de gebruiker zal kunnen aangeven om coupons voor te genereren<br/>
Houd er rekening mee dat je dus één of meer cursussen <i>moet</i> selecteren. Het is niet mogelijk om dit veld leeg te laten met als gevolg dat alle cursussen door de gebruiker aan te geven zijn';
$string['findusers:noselectionstring'] = 'geen gebruiker geselecteerd';
$string['findusers:placeholder'] = '... selecteer gebruiker ...';
$string['forcelogo_exp'] = '<i>Wanneer het selecteren van een logo is uitgeschakeld voor deze gebruiker, <b>moet</b> je aangeven of en welk logo er standaard zal worden toegepast voor alle coupons van deze gebruiker</i>';
$string['forcerole_exp'] = '<i>Wanneer het selecteren van een rol is uitgeschakeld voor deze gebruiker, <b>moet</b> je aangeven of en welke rol er standaard zal worden toegepast voor alle coupons van deze gebruiker</i>';
$string['generator:export:mail:body'] = 'Beste {$a->fullname},<br /><br />
Je ontvangt deze email omdat er nieuwe coupons zijn gegenereerd.<br/>
De coupons kunnen gedownload worden vanaf {$a->downloadlink} (vereist inloggen in Moodle).<br />
Let er aub op dat deze link slechts 1 maal kan worden gebruikt. Wanneer de coupons zijn gedownload, is deze link niet langer bruikbaar.<br />
Met vriendelijke groet,<br /><br />
{$a->signoff}';
$string['generator:export:mail:subject'] = 'Coupons klaar voor download';
$string['heading:administration'] = 'Beheer';
$string['heading:amountForm'] = 'Aantal instellingen';
$string['heading:cohortandvars'] = 'Selecteer coupon variabelen, sitegroep(en) en aanmeldingsvariabelen';
$string['heading:cohortlinkcourses'] = 'Koppel cursus(sen) aan sitegroep(en)';
$string['heading:coupon_type'] = 'Type coupon';
$string['heading:courseandvars'] = 'Selecteer coupon variabelen, cursus(sen) en aanmeldingsvariabelen';
$string['heading:coursegroupingandvars'] = 'Selecteer coupon variabelen, cursusgroepering en aanmeldingsvariabelen';
$string['heading:coursegroups'] = 'Koppel gekozen cursussen aan cursusgroepen';
$string['heading:csvForm'] = 'CSV instellingen';
$string['heading:general_settings'] = 'Laatste instellingen';
$string['heading:generatecoupons'] = 'Coupon genereren';
$string['heading:generatormethod'] = 'Selecteer hoe je de coupons wilt genereren';
$string['heading:imageupload'] = 'Upload afbeelding';
$string['heading:info'] = 'Informatie';
$string['heading:input_cohorts'] = 'Selecteer cohort(en)';
$string['heading:input_coupon'] = 'Coupon invoeren';
$string['heading:input_course'] = 'Selecteer cursus(sen)';
$string['heading:input_groups'] = 'Selecteer groepen';
$string['heading:inputcoupons'] = 'Coupon invoeren';
$string['heading:label_instructions'] = 'Instructies';
$string['heading:manualForm'] = 'Manuele instellingen';
$string['here'] = 'hier';
$string['import:voucher:confirm'] = 'Importeer gegevens uit block_voucher';
$string['import:voucher:desc'] = 'Importeer alle beschikbare en relevante gegevens uit block_voucher.<br/>
We zullen proberen om alle vouchers, gekoppelde cursus-/sitegroepgegevens, email templates en PDF templates te importeren,
hoewel we <i>niet</i> kunnen garanderen dat dit resulteert in een volle import zonder foutieve gegevens.<br/>
Desondanks zal de import van vouchers en daarbij horende koppelingen waarschijnlijk geen problemen geven.';
$string['knowncourses'] = 'Bekende cursussen';
$string['label:alternative_email'] = 'Alternatief e-mail adres';
$string['label:alternative_email_help'] = 'Coupons worden standaard naar dit e-mail adres gestuurd indien het veld \'Gebruik alternatief e-mail\' aangevinkt staat.';
$string['label:api_enabled'] = 'Activeer API';
$string['label:api_enabled_desc'] = 'Met de Coupon API kan men een coupon genereren van een extern systeem.';
$string['label:api_password'] = 'API Password';
$string['label:api_password_desc'] = 'Het wachtwoord waarmee men de coupon API kan gebruiken.';
$string['label:api_user'] = 'API gebruiker';
$string['label:api_user_desc'] = 'De gebruiker waarmee men de coupon API kan gebruiken.';
$string['label:batchid'] = 'Batch naam';
$string['label:batchid_help'] = 'Je kunt hier een (unieke) naam opgeven voor de groepering.<br/>
Het opgeven van een batchnaam kan je later helpen een groep gegenereerde coupons te identificeren.<br/>
Wanneer je geen naam opgeeft wordt automatisch een batchnaam gegenereerd';
$string['label:buttonclass'] = 'Knop/link klasse';
$string['label:buttonclass_desc'] = 'Kies knop/link klasse; dit heeft impact op hoe de links/knoppen in het blok worden getoond';
$string['label:cleanupage'] = 'Maximum leeftijd?';
$string['label:cleanupage_help'] = 'Voer de maximum leeftijd in van de ongebruikte coupon voordat deze automatisch zal worden verwijderd';
$string['label:cohort'] = 'Cohort';
$string['label:connected_courses'] = 'Toegevoegde cursus(sen)';
$string['label:coupon_amount'] = 'Aantal coupons';
$string['label:coupon_amount_help'] = 'Het aantal coupons dat gegenereerd zal worden. U kunt dit veld OF het Ontvangers veld gebruiken, niet beiden.';
$string['label:coupon_code'] = 'Coupon Code';
$string['label:coupon_code_help'] = 'De couponcode is de unieke code die aan iedere coupon is toegekend. U vindt deze code op uw coupon.';
$string['label:coupon_code_length'] = 'Code length';
$string['label:coupon_code_length_help'] = 'Aantal karakters van de Couponcode.';
$string['label:coupon_cohorts'] = 'Cohort(en)';
$string['label:coupon_cohorts_help'] = 'Selecteer een of meer cohort(en) waarin gebruikers zullen worden toegewezen.';
$string['label:coupon_connect_course'] = 'Cursus(sen) toevoegen';
$string['label:coupon_connect_course_help'] = 'Selecteer de cursussen die aan de cohort moeten worden toegevoegd.
    <br /><b><i>Let op: </i></b>Als er al deelnemers aan die cohort toegevoegd zijn worden deze ook in de cursussen ingeschreven!';
$string['label:coupon_courses'] = 'Cursus(sen)';
$string['label:coupon_courses_help'] = 'Selecteer een of meer cursus(sen) waarop gebruikers worden ingeschreven.';
$string['label:coupon_email'] = 'E-mail adres';
$string['label:coupon_email_help'] = 'Dit is het e-mail adres waar de gegenereerde coupons naar toe gestuurd worden.';
$string['label:coupon_groups'] = 'Groep(en) toevoegen';
$string['label:coupon_groups_help'] = 'Selecteer hier de groepen waar uw gebruikers in toegevoegd moeten worden zodra ze worden ingeschreven bij de cursussen.';
$string['label:coupon_recipients'] = 'Ontvangers';
$string['label:coupon_recipients_help'] = 'Met dit veld kunt u een csv lijst met gebruikers als ontvangers van de coupons uploaden.';
$string['label:coupon_recipients_txt'] = 'Ontvangers';
$string['label:coupon_recipients_txt_help'] = 'In dit veld kunt u de laatste aanpassingen aan het csv bestand doen.';
$string['label:coupon_role'] = 'Rol';
$string['label:coupon_role_help'] = 'Selecteer hier de rol waarmee de coupons worden geconfigureerd of laat leeg voor de ingestelde standaardwaarde (normaliter student).';
$string['label:coupon_type'] = 'Genereer coupon(s) voor';
$string['label:coupon_type_help'] = 'De coupons worden gebaseerd op een cursus of een of meer cohorts.';
$string['label:current_image'] = 'Huidige coupon achtergrond';
$string['label:date_send_coupons'] = 'Verzenddatum';
$string['label:date_send_coupons_help'] = 'Datum dat de coupons naar de ontvanger(s) verstuurd worden.';
$string['label:defaultlogo'] = 'Standaard logo';
$string['label:defaultlogo_help'] = 'Selecteer het logo dat voor deze gebruiker wordt geforceerd voor alle coupons';
$string['label:defaultrole'] = 'Standaard rol';
$string['label:defaultrole_help'] = 'Dit is de standaardrol die gebruikers toegewezen zullen krijgen wanneer ze een coupon claimen';
$string['label:displayinputhelp'] = 'Toon coupon invoer hulp';
$string['label:displayinputhelp_help'] = 'Vind deze optie aan om een begeleidende tekst aan ingelogde eindgebruikers te tonen boven het coupon invoerveld.';
$string['label:displayregisterhelp'] = 'Toon registratie hulp';
$string['label:displayregisterhelp_help'] = 'Vink deze optie aan om aan de niet ingelogde gebruiker een begeleidende tekst te tonen zodat duidelijk is dat ze een nieuw account kunnen aanmaken met behulp van de link in het blok.';
$string['label:email_body'] = 'Email bericht';
$string['label:email_body_help'] = 'Het email bericht dat wordt verstuurd naar de ontvangers van de coupons.';
$string['label:enablecleanup'] = 'Automatisch verwijderen ongebruikte coupons?';
$string['label:enablecleanup_help'] = 'Vink deze optie aan als je automatisch ongebruikte coupons wilt laten verwijderen';
$string['label:enablemycouponsforru'] = '"mijn voortgangsrapportage" inschakelen?';
$string['label:enablemycouponsforru_help'] = 'Hiermee wordt de tab/tabel ten behoeve van de voortgangsrapportage voor coupons die behoren tot een couponverzoek gebruiker inzichtelijk gemaakt.';
$string['label:enrolment_period'] = 'Inschrijvingsperiode';
$string['label:enrolment_period_help'] = 'Inschrijvingsperiode (in dagen). Indien 0 ingevuld wordt er geen uitschrijvingsdatum geregistreerd.';
$string['label:enrolment_perioddefault'] = 'Standaard lengte voor aanmelding';
$string['label:enter_coupon_code'] = 'Vul hier uw Coupon code in';
$string['label:extendperiod'] = 'Duur aanmeldingsverlenging';
$string['label:extendperiod:desc'] = 'Configureer de optionele aanmeldingsverlenging hieronder. Indien <i>niet</i> aangevinkt of aangemerkt met waarde 0, zal de aanmelding permanent worden';
$string['label:extendusers:desc'] = 'Selecteer een of meer gebruiker(s).<br/>
Enkel gebruikers met een <i>manuele</i> aanmelding die tevens een einddatum bevatten zullen getoond worden.';
$string['label:font'] = 'Lettertype voor PDF';
$string['label:font_help'] = 'Selecteer het lettertype dat gebruikt wordt bij het genereren van de PDF. De standaardinstelling is meestal voldoende.<br/>
Wanneer je echter speciale tekens nodig hebt (cyrillisch, arabisch, farsi, etc), kun je hier een ander font aangeven dat wordt gebruikt.
';
$string['label:forcelogo'] = 'Geforceerd logo';
$string['label:forcelogo_help'] = 'Selecteer het logo dat geforceerd wordt voor alle coupons voor deze gebruiker';
$string['label:forcerole'] = 'Geforceerde rol';
$string['label:forcerole_help'] = 'Selecteer de rol die geforceerd wordt voor alle coupons voor deze gebruiker';
$string['label:generate_pdfs'] = 'Genereer losse PDF\'s';
$string['label:generate_pdfs_help'] = 'Hier kunt u aangeven of u alle coupons in 1 bestand of iedere coupon een apart bestand wilt ontvangen.';
$string['label:generatecodesonly'] = 'Alleen codes genereren';
$string['label:generatecodesonly_help'] = 'Wanneer je deze optie inschakelt, worden er geen PDFs gemaakt en geen emails verstuurd!';
$string['label:groupingselectactiveonly'] = 'Alleen active aanmeldingen?';
$string['label:groupingselectactiveonly_help'] = 'Wanneer niet aangevinkt, worden alle cursussen waaruit men kan kiezen gecontroleerd op aanmelding, ook niet actieve aanmeldingen.<br/>
Dit betekent dat zelfs inactieve en reeds verlopen cursusaanmeldingen meetellen in de cursussen waaruit men kan kiezen bij het claimen van een coupon.<br/>
Alle cursussen waaruit een persoon kan kiezen worden gecompenseerd voor diegene waar men al in is aangemeld.';
$string['label:image'] = 'Coupon achtergrond';
$string['label:image_desc'] = 'Achtergrond bij de coupon';
$string['label:info_coupon_cohort_courses'] = 'Informatie op pagina: Cohort cursussen';
$string['label:info_coupon_cohorts'] = 'Informatie op pagina: Selecteer coupon cohorten';
$string['label:info_coupon_confirm'] = 'Informatie op pagina: Bevestig coupon';
$string['label:info_coupon_course'] = 'Informatie op pagina: Selecteer course';
$string['label:info_coupon_course_groups'] = 'Informatie op pagina: Selecteer cursus groepen';
$string['label:info_coupon_type'] = 'Informatie op pagina: Selecteer coupon type';
$string['label:info_desc'] = 'De informatie die getoond wordt bovenaan het formulier.';
$string['label:info_imageupload'] = 'Informatie op pagina: Upload afbeelding';
$string['label:logo'] = 'Coupon logo/achtergrond';
$string['label:mailusers'] = 'Verzend coupons via e-mail naar de geselecteerde personen.';
$string['label:max_coupons'] = 'Maximum coupons';
$string['label:max_coupons_desc'] = 'Aantal coupons dat in 1 keer aangemaakt kan worden.';
$string['label:no_courses_connected'] = 'Er zijn nog geen cursussen toegevoegd aan deze cohort.';
$string['label:no_groups_selected'] = 'Er zijn nog geen groepen aan deze cursus toegevoegd.';
$string['label:personalsendpdf'] = 'Verzend PDF bij persoonlijke coupons?';
$string['label:personalsendpdf_help'] = 'Indien ingeschakeld zal dit een PDF met de coupon meesturen als bijlage.<br/>
Merk aub op dat wanneer deze optie niet in ingeschakeld, de e-mail voor ontvangers van persoonlijke coupons minimaal een <i>coupon code</i> veld of template variabele moet bevatten.<br/>
Als deze template variabele mist en er wordt geen PDF meegestuurd, zal de ontvanger ook niet weten welke coupon code ingevuld moet worden.
';
$string['label:redirect_url'] = 'Doorstuur adres';
$string['label:redirect_url_help'] = 'De locatie waar gebruikers naar toe worden gestuurd na het invullen van hun coupon code.';
$string['label:renderqrcode'] = 'Gebruik QR Code?';
$string['label:renderqrcode_help'] = 'Gebruik deze optie om een QR code wel of niet in de PDF op te nemen.';
$string['label:selected_cohort'] = 'Geselecteerde cohort(en)';
$string['label:selected_courses'] = 'Geselecteerde cursussen';
$string['label:selected_groups'] = 'Geselecteerde groep(en)';
$string['label:seperatepersonalcoupontab'] = 'Aparte tab toevoegen voor persoonlijke coupons?';
$string['label:seperatepersonalcoupontab_help'] = 'Indien ingeschakeld zal dit een eigen tab toevoegen naast de gebruikte/ongebruikte coupons tabs.<br/>
De gebruikte/ongebruikte coupons tabs zullen poer definitie altijd aanwezig zijn en gepersonaliseerde coupons zullen <i>altijd</i> beschikbaar zijn op die tabs.<br/>
Deze instelling heeft dus inhoudelijk geen effect op de gebruikte/ongebruikte coupons tabs.
';
$string['label:showform'] = 'Opties';
$string['label:type_cohorts'] = 'Aanmelding op sitegroep(en)';
$string['label:type_course'] = 'Cursusaanmelding';
$string['label:type_coursegrouping'] = 'Cursusgroepering (kies X uit Y aanmeldingen in cursus)';
$string['label:use_alternative_email'] = 'Gebruik alternatief e-mail adres';
$string['label:use_alternative_email_help'] = 'Indien aangevinkt wordt het e-mail adres uit \'Alternatief e-mail adres\' standaard gebruikt bij het aanmaken van een coupon.';
$string['label:useloginlayoutonsignup'] = 'Gebruik \'login\' pagina layout op interne aanmeldpagina?';
$string['label:useloginlayoutonsignup_help'] = 'Indien ingeschakeld, zal dit de standaard \'login\' pagina layout gebruoiken op de interne aanmeldpagina.<br/>
Dit betekent dat alle headers en footers niet getoond worden, en de pagina enkel het aanmeldformulier zelf bevat.';
$string['label:users'] = 'Gebruiker(s)';
$string['logo:default'] = 'Standaard logo';
$string['logo:none'] = 'Gebruik geen logo';
$string['logomanager:desc'] = 'Gebruik de logomanager hieronder om logos te beheren die gebruikt kunnen worden in uw coupon PDFs.<br/>
Let op wat voor afbeeldingen je upload!<br/>
Je <i>zou</i> enkel afbeeldingen van 300 DPI op A4 formaat (2480 x 3508 pixels) moeten uploaden.<br/>
<i>Elk</i> ander formaat afbeelding zal meer dan waarschijnlijk leiden tot ongewenste resultaten.
';
$string['messageprovider:coupon_notification'] = 'Coupons gegenereerd notificatie';
$string['messageprovider:coupon_task_notification'] = 'Persoonlijke coupons verzonden notificatie';
$string['missing_config_info'] = 'Plaats hier uw extra informatie - in te stellen in de globale configuratie van het blok.';
$string['notify:already_enrolled_in_course_list'] = 'Je bent al aangemeld in de cursus(s) en: {$a}';
$string['notify:already_enrolled_in_courses'] = 'Je bent al aangemeld';
$string['notify:now_enrolled_in_courses'] = 'Je bent nu aangemeld in de cursus(sen): {$a}';
$string['numcourses'] = 'Maximum aantal te selecteren cursussen';
$string['othersettings'] = 'Overige instellingen / opties';
$string['page:generate_coupon.php:title'] = 'Genereer coupons';
$string['page:generate_coupon_step_five.php:title'] = 'Genereer coupons';
$string['page:generate_coupon_step_four.php:title'] = 'Genereer coupons';
$string['page:generate_coupon_step_three.php:title'] = 'Genereer coupons';
$string['page:generate_coupon_step_two.php:title'] = 'Genereer coupons';
$string['page:unused_coupons.php:title'] = 'Ongebruikte coupons';
$string['pdf-meta:keywords'] = 'Moodle Coupon';
$string['pdf-meta:subject'] = 'Moodle Coupon';
$string['pdf-meta:title'] = 'Moodle Coupon';
$string['pdf:titlename'] = 'Moodle Coupon';
$string['pdf_generated'] = 'De Coupons zijn aan dit email bericht toegevoegd als PDF bestanden.<br /><br />';
$string['pluginname'] = 'Coupon';
$string['preview-pdf'] = 'PDF Preview';
$string['privacy:metadata:block_coupon'] = 'Het coupon blok slaat coupon/voucher codes op en eventuele koppelingen met gebruikers die een code hebben geclaimed';
$string['privacy:metadata:block_coupon:claimed'] = 'Of de coupon geclaimed is';
$string['privacy:metadata:block_coupon:email_body'] = 'Inhoud van verzOnden email, indien gegeven';
$string['privacy:metadata:block_coupon:for_user_email'] = 'Emailadres van persoon waar de coupon naar verzonden is, indien gegeven';
$string['privacy:metadata:block_coupon:for_user_gender'] = 'Geslacht van persoon waar de coupon naar verzonden is, indien gegeven';
$string['privacy:metadata:block_coupon:for_user_name'] = 'Naam van persoon waar de coupon naar verzonden is, indien gegeven';
$string['privacy:metadata:block_coupon:roleid'] = 'Rol ID die toegewezen wordt/is';
$string['privacy:metadata:block_coupon:submission_code'] = 'Coupon aanmeldcode';
$string['privacy:metadata:block_coupon:timecreated'] = 'Tijdstip waarop coupon is aangemaakt';
$string['privacy:metadata:block_coupon:timeexpired'] = 'Tijdstip waarop coupon verloopt';
$string['privacy:metadata:block_coupon:timemodified'] = 'Tijdstip waarop coupon is gewijzigd';
$string['privacy:metadata:block_coupon:userid'] = 'De primaire database sleutel van de Moodle gebruiker';
$string['promo'] = 'Coupon plugin voor Moodle';
$string['promodesc'] = 'Deze plugin is geschreven door Sebsoft Managed Hosting & Software Development
(<a href=\'http://www.sebsoft.nl/\' target=\'_new\'>http://sebsoft.nl</a>).<br /><br />
{$a}<br /><br />';
$string['recipient:none'] = 'Geen';
$string['recipient:selected:users'] = 'Geselecteerde deelnemers';
$string['remove-count'] = 'Dit zal <i>{$a}</i> coupon(s) verwijderen';
$string['removecourse'] = 'Cursus \'{$a}\' verwijderen uit opties';
$string['report:cohorts'] = 'Cohort';
$string['report:coupon_code'] = 'Coupon code';
$string['report:dateformat'] = '%d-%m-%Y %H:%M:%S';
$string['report:dateformatymd'] = '%d-%m-%Y';
$string['report:download-excel'] = 'Download ongebruikte coupons';
$string['report:enrolperiod'] = 'Inschrijf periode';
$string['report:for_user_email'] = 'Ingepland voor';
$string['report:for_user_name'] = 'Naam ontvanger';
$string['report:heading:action'] = 'Actie(s)';
$string['report:heading:cohortname'] = 'Sitegroep(en)';
$string['report:heading:coupon'] = 'Coupon';
$string['report:heading:couponcode'] = 'Gebruikte code';
$string['report:heading:coursename'] = 'Cursus naam';
$string['report:heading:coursetype'] = 'Cursus type';
$string['report:heading:datecomplete'] = 'Datum afgerond';
$string['report:heading:datestart'] = 'Start datum';
$string['report:heading:errormessage'] = 'Foutmelding';
$string['report:heading:errortype'] = 'Type';
$string['report:heading:grade'] = 'Cijfer';
$string['report:heading:iserror'] = 'Is fout?';
$string['report:heading:status'] = 'Status';
$string['report:heading:timecreated'] = 'Datum';
$string['report:heading:type'] = 'Type';
$string['report:heading:user'] = 'Gebruiker';
$string['report:immediately'] = 'Onmiddellijk';
$string['report:issend'] = 'Is verstuurd';
$string['report:owner'] = 'Eigenaar';
$string['report:senddate'] = 'Verstuurdatum';
$string['report:status_completed'] = 'Cursus afgerond';
$string['report:status_not_started'] = 'Cursus nog niet gestart';
$string['report:status_started'] = 'Cursus gestart';
$string['report:timeexpired'] = 'Verloopt';
$string['request:accept:content'] = '<p>Beste {$a->fullname}</p>,
<p>Je ontvangt dit bericht omdat je aangevraagde coupons zijn gegenereerd.<br/>
De coupons zijn beschikbaar ter download op de leeromgeving.<br /><br />
de coupons direct te downloaden door {$a->downloadlink} te klikken</p>{$a->custommessage}
<p>With kind regards,<br /><br />
{$a->signoff}</p>';
$string['request:accept:custommessage'] = '<p>The following remark has been added for you: {$a}</p>';
$string['request:accept:heading'] = 'Dit coupon verzoek honoreren';
$string['request:accept:subject'] = 'Aanvraag tot genereren van coupons gehonoreerd.';
$string['request:adduser:heading'] = 'Voeg een gebruiker toe die coupon verzoeken kan indienen';
$string['request:adduser:info'] = 'Selecteer hieronder een gebruik die het wordt toegestaan verzoeken in te dienen om coupons te laten genereren.<br/>
Je kunt in de dropdown typen om te zoeken.<br/>
Wanneer je de gebruiker hebt geselecteerdm klik dan op doorgaan. Je wordt vervolgens omgeleid naar de pagina waar je de rest van de opties voor deze gebruiker kunt configureren.
';
$string['request:coupons'] = 'Coupons aanvragen';
$string['request:deny:heading'] = 'Dit coupon verzoek afwijzen';
$string['request:deny:subject'] = 'Aanvraag tot genereren van coupons afgewezen.';
$string['request:info'] = 'Aanvraag voor {$a->amount} coupons';
$string['request:message'] = 'Bericht aan aanvrager';
$string['request:sendmessage'] = 'Informeer de aanvrager?';
$string['requestusersettings'] = 'Instellingen coupon verzoeken';
$string['requestusersettings_desc'] = 'Dit onderdeel heeft een aantal instellingen waarmee de gebruikersinterface van de gebruikers voor coupon verzoeken kunnen worden gelimiteerd.<br/>
De instellingen zijn specifiek ingezet om eventuele GDPR/AVG problemen te voorkomen.';
$string['required:atleastonecohortorcourse'] = 'Minimaal 1 cursus of cohort dient te worden ingegeven!';
$string['searchcourses:desc'] = 'Cursussen worden gezocht op basis van de volgende velden:
<ul>
    <li>Korte naam</li>
    <li>Volledige naam</li>
    <li>ID-nummer</li>
</ul>';
$string['select:logo'] = 'Selecteer template logo';
$string['select:logo:desc'] = 'Selecteer een template logo.<br/>Deze wordt enkel gebruikt indien er PDFs worden gegenereerd.';
$string['showform-amount'] = 'Ik wil een arbitrair aantal coupons aanmaken';
$string['showform-csv'] = 'Ik wil coupons aanmaken door een CSV met ontvangers te uploaden';
$string['showform-manual'] = 'Ik wil coupons aanmaken door manueel de ontvangers op te geven';
$string['signup:login'] = 'Ik heb al een account en wil inloggen';
$string['signup:success'] = 'Je hebt je ingeschreven en zal nu worden herleid naar de login pagina.<br/>
Valideer aub dat je meteen toegang hebt tot een of meer curssussen nadat je bent ingelogd.';
$string['str:coursegroupings:add'] = 'Nieuwe groepering toevoegen';
$string['str:inputhelp'] = 'Gebruik onderstaand invoerveld om toegang tot cursussen te krijgen indien je een coupon code hebt ontvangen';
$string['str:mandatory'] = 'Mandatory';
$string['str:optional'] = 'Optional';
$string['str:request:add'] = 'Coupons aanvragen';
$string['str:request:adduser'] = 'Gebruiker toevoegen';
$string['str:request:cohortcoupons'] = 'Sitegroep coupons aanvragen';
$string['str:request:coursecoupons'] = 'Cursus coupons aanvragen';
$string['str:request:details'] = 'Details couponverzoek';
$string['str:signuphelp'] = 'Gebruik onderstaande link om een nieuw account <i>met</i> een coupon code te maken wanneer je er nog geen hebt';
$string['success:coupon_used'] = 'Coupon gebruikt - U kunt nu uw nieuwe cursus(en) in';
$string['success:uploadimage'] = 'Uw nieuwe couponafbeelding is geupload.';
$string['tab:apidocs'] = 'API Documentatie';
$string['tab:cleaner'] = 'Opschoning';
$string['tab:cpmycoupons'] = 'Mijn coupons';
$string['tab:downloadbatchlist'] = 'Batch archieven';
$string['tab:errors'] = 'Foutrapportage';
$string['tab:listrequests'] = 'Mijn verzoeken';
$string['tab:maillog'] = 'E-mail log';
$string['tab:personalcoupons'] = 'Gepersonaliseerde coupons';
$string['tab:report'] = 'Voortgangsrapportage';
$string['tab:requests'] = 'Coupon verzoeken';
$string['tab:requestusers'] = 'Gebruikers voor couponverzoeken';
$string['tab:unused'] = 'Ongebruikte coupons';
$string['tab:used'] = 'Gebruikte coupons';
$string['tab:wzcoupongroupings'] = 'Beheer cursusgroeperingen';
$string['tab:wzcouponimage'] = 'Template afbeelding';
$string['tab:wzcoupons'] = 'Genereer coupon(s)';
$string['task:cleanup'] = 'Automatisch verwijderen ongebruikte coupons';
$string['task:sendcoupons'] = 'Ingeplande coupons versturen';
$string['task:unenrolcohorts'] = 'Verlopen coupon aanmeldingen voor cohorten verwijderen';
$string['tasksettings'] = 'Taak instellingen';
$string['tasksettings_desc'] = '';
$string['textsettings'] = 'Tekst instellingen';
$string['textsettings_desc'] = 'Hier kun je teksten ingeven die door diverse pagina\'s binnen de coupon wizard worden gebruikt';
$string['th:action'] = 'Actie(s)';
$string['th:batchid'] = 'Batch';
$string['th:claimedon'] = 'Geclaimed op';
$string['th:cohorts'] = 'Cohort';
$string['th:course'] = 'Cursus';
$string['th:enrolperiod'] = 'Inschrijvingsduur';
$string['th:for_user_email'] = 'Ingepland voor';
$string['th:fullname'] = 'Volledige naam';
$string['th:groups'] = 'Groep(en)';
$string['th:immediately'] = 'Direct';
$string['th:issend'] = 'Verzonden?';
$string['th:owner'] = 'Eigenaar';
$string['th:roleid'] = 'Rol';
$string['th:senddate'] = 'Verzenddatum';
$string['th:submission_code'] = 'Aanmeldcode';
$string['th:tid'] = 'Tijd ID';
$string['th:timecreated'] = 'Aangemaakt op';
$string['th:usedby'] = 'Gebruikt door';
$string['timeafter'] = 'Gemaakt na';
$string['timebefore'] = 'Gemaakt voor';
$string['unlimited_access'] = 'onbeperkt';
$string['url:api_docs'] = 'API Documentatie';
$string['url:couponsignup'] = 'Meld aan met een coupon';
$string['url:generate_coupons'] = 'Genereer Coupon';
$string['url:input_coupon'] = 'Coupon invoeren';
$string['url:managelogos'] = 'Beheer coupon logos';
$string['url:uploadimage'] = 'Wijzig coupon achtergrond';
$string['url:view_reports'] = 'Bekijk rapporten';
$string['url:view_unused_coupons'] = 'Bekijk ongebruikte coupons';
$string['userconfig:allowselectenrolperiod'] = 'Selecteren van duur van aanmelding toestaan';
$string['userconfig:allowselectlogo'] = 'Selecteren van coupon logo toestaan';
$string['userconfig:allowselectqr'] = 'Selecteren van QR code insluiten toestaan';
$string['userconfig:allowselectrole'] = 'Selecteren van rol toestaan';
$string['userconfig:allowselectseperatepdf'] = 'Selecteren van mogelijkheid tot genereren van losse PDF bestanden toestaan';
$string['userconfig:default'] = 'Standaardinstelling';
$string['userconfig:renderqrcode:default'] = 'Insluiten van QR code standaard inschakelen';
$string['userconfig:seperatepdf:default'] = 'Genereren van losse PDFs standaard inschakelen';
$string['view:api:heading'] = 'Coupon API';
$string['view:api:title'] = 'Coupon API';
$string['view:api_docs:heading'] = 'Coupon API Documentatie';
$string['view:api_docs:title'] = 'Coupon API Documentatie';
$string['view:cleanup:heading'] = 'Coupons opschonen';
$string['view:cleanup:title'] = 'Coupons opschonen';
$string['view:coursegroupings:admin:heading'] = 'Beheer cursusgroeperingen';
$string['view:coursegroupings:admin:title'] = 'Cursusgroeperingen';
$string['view:download:heading'] = 'Download je coupons';
$string['view:download:title'] = 'Download coupons';
$string['view:downloadbatches:title'] = 'Beschikbare batcharchieven';
$string['view:errorreport:heading'] = 'Rapport - Coupon fouten';
$string['view:errorreport:title'] = 'Rapport - Coupon fouten';
$string['view:extendenrolment:heading'] = 'Coupon: aanmeldingsverlengingen';
$string['view:extendenrolment:title'] = 'Coupon: aanmeldingsverlengingen';
$string['view:extendenrolment_step1:heading'] = 'Verleng aanmelding: selecteer cursus(sen)';
$string['view:extendenrolment_step1:title'] = 'Verleng aanmelding: selecteer cursus(sen)';
$string['view:extendenrolment_step2:heading'] = 'Verleng aanmelding: selecteer gebruiker(s)';
$string['view:extendenrolment_step2:title'] = 'Verleng aanmelding: selecteer gebruiker(s)';
$string['view:extendenrolment_step3:heading'] = 'Verleng aanmelding: bevestigen';
$string['view:extendenrolment_step3:title'] = 'Verleng aanmelding: bevestigen';
$string['view:generate_coupon:heading'] = 'Genereer Coupon';
$string['view:generate_coupon:title'] = 'Genereer Coupon';
$string['view:generator:cohort:heading'] = 'Genereer cohort coupon(s)';
$string['view:generator:cohort:title'] = 'Genereer cohort coupon(s)';
$string['view:generator:course:heading'] = 'Genereer cursus coupon(s)';
$string['view:generator:course:title'] = 'Genereer cursus coupon(s)';
$string['view:generator:coursegroupings:heading'] = 'Coupons tbv cursusgroepering genereren';
$string['view:generator:coursegroupings:title'] = 'generator tbv cursusgroepering(en)';
$string['view:input_coupon:heading'] = 'Coupon invoeren';
$string['view:input_coupon:title'] = 'Coupon invoeren';
$string['view:reports-maillog:heading'] = 'E-mail log';
$string['view:reports-maillog:title'] = 'E-mail log';
$string['view:reports-personal:heading'] = 'Rapportage - Gepersonaliseerde Coupons';
$string['view:reports-personal:title'] = 'Rapportage - Gepersonaliseerde Coupons';
$string['view:reports-unused:heading'] = 'Rapportage - Ongebruikte coupons';
$string['view:reports-unused:title'] = 'Rapportage - Ongebruikte coupons';
$string['view:reports-used:heading'] = 'Rapportage - Gebruikte coupons';
$string['view:reports-used:title'] = 'Rapportage - Gebruikte coupons';
$string['view:reports:heading'] = 'Rapportage - Voortgang voor coupons';
$string['view:reports:title'] = 'Rapportage - Voortgang voor coupons';
$string['view:request:heading'] = 'Coupons aanvragen';
$string['view:request:title'] = 'Coupons aanvragen';
$string['view:requests:admin:heading'] = 'Coupon verzoeken administratie';
$string['view:requests:admin:title'] = 'Coupon verzoeken administratie';
$string['view:selectcourses:heading'] = 'Kies cursus(sen) om op aan te melden';
$string['view:selectcourses:title'] = 'Kies cursus(sen)';
$string['view:uploadimage:heading'] = 'Een nieuwe coupon achtergrond uploaden';
$string['view:uploadimage:title'] = 'Coupon achtergrond uploaden';
$string['view:userrequest:heading'] = 'Mijn coupon aanvragen';
$string['view:userrequest:title'] = 'Mijn coupon aanvragen';
$string['with-names'] = 'Met de volgende namen of identifiers';
