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

$string['action:coupon:delete'] = 'Supprimer le coupon';
$string['action:coupon:delete:confirm'] = 'Voulez-vous vraiment supprimer ce coupon? Ça ne peut pas être annulé!';
$string['action:error:delete'] = 'Supprimer l\'erreur';
$string['and'] = 'and';
$string['blockname'] = 'Coupon';
$string['button:next'] = 'Suivant';
$string['button:save'] = 'Générer des coupons';
$string['button:submit_coupon_code'] = 'Soumettre un coupon';
$string['cleanup:confirm:confirmmessage'] = 'Oui, je veux supprimer les coupons avec ces options';
$string['cleanup:confirm:header'] = 'Veuillez confirmer les options de nettoyage suivantes';
$string['cohort'] = 'cohorte';
$string['confirm_coupons_sent_body'] = '
Bonjour,<br /><br />

Nous aimerions vous informer que tous les coupons créés par vous sur {$ a-> timecreated} ont été envoyés.<br /><br />

Cordialement,<br /><br />

Moodle administrator';
$string['confirm_coupons_sent_subject'] = 'Tous les coupons ont été envoyés';
$string['coupon:addinstance'] = 'Ajouter un nouveau bloc Coupon';
$string['coupon:administration'] = 'Gérer le bloc Coupon';
$string['coupon:deleted'] = 'Le coupon a été supprimé';
$string['coupon:generatecoupons'] = 'Générer un nouveau coupon';
$string['coupon:inputcoupons'] = 'Utiliser un coupon pour vous abonner';
$string['coupon:myaddinstance'] = 'Ajouter un nouveau bloc de coupon à ma page Mon Moodle';
$string['coupon:send:fail'] = 'Échec de l\'envoi du courrier électronique! Raison: {$a}';
$string['coupon:viewallreports'] = 'Afficher les rapports sur les coupons (pour tous les coupons)';
$string['coupon:viewreports'] = 'Afficher les rapports sur les coupons (pour mes coupons)';
$string['coupon_mail_content'] = '
Cher {$a->fullname},<br /><br />

Vous recevez ce message car il y a eu de nouveaux coupons de générés. Les coupons ont été ajoutés dans la pièce jointe à ce message.<br /><br />

Cordialement,<br /><br />

{$a->signoff}';
$string['coupon_mail_csv_content'] = '
Cher ##to_gender## ##to_name##,<br /><br />

Vous venez d\'être inscrit à notre formation ##course_fullnames##.
Pendant le cours, vous aurez accès à notre environnement d\'apprentissage en ligne: ##site_name##.<br /><br />

Dans cet environnement, en dehors des matériels de cours, vous aurez la possibilité de réseauter avec vos collègues..
Le cours débutera avec un certain nombre de travaux de préparation, nous vous prions de jeter un coup d\'oeil
au plus tard 3 jours ouvrables avant le début du cours..
Vous et l\'enseignant pourrez alors vous préparer avant le début du cours.<br /><br />

Tous les documents du cours seront accessibles pour vous, au plus tard 4 jours avant le début du cours. Il peut toujours arriver que l\'enseignant demande des matériaux supplémentaires à ajouter ultérieurement, par exemple
Après une séance physique. Si cela se produit, vous pourrez le voir dans l\'environnement d\'apprentissage
Pendant les réunions, vous ne recevrez aucun matériel de leçon imprimé, nous vous conseillons d\'apporter un ordinateur portable et / ou une tablette.<br /><br />

Vous trouverez le coupon pour entrer dans le cours ci-joint. Ce coupon est personnel et unique, et donne accès aux cours appropriés pour votre éducation.
Veuillez lire attentivement les instructions sur le coupon.<br /><br />

Si vous avez des questions concernant la création d\'un compte ou si vous rencontrez d\'autres problèmes, vous pouvez contacter le helpdesk.
Des informations peuvent être trouvées sur l\'environnement d\'apprentissage.
Lorsque personne n\'est disponible pour répondre à votre question, veuillez laisser votre nom, adresse e-mail et numéro de téléphone et nous vous rappellerons dès que possible.<br /><br />

Nous vous souhaitons bonne chance pour votre formation.<br /><br />

Cordialement,<br /><br />

##site_name##';
$string['coupon_mail_csv_content_cohorts'] = '
Cher ##to_gender## ##to_name##,<br /><br />

Vous venez d\'être inscrit à notre formation**PLEASE FILL IN MANUALLY**.
Pendant le cours, vous aurez accès à notre environnement d\'apprentissage en ligne: ##site_name##.<br /><br />

Dans cet environnement, en dehors du matériel de cours, vous aurez la possibilité de réseauter avec d\'autres étudiants.
Le cours débutera avec un certain nombre de travaux de préparation, nous vous prions de leurs jeter un coup d\'oeil
au plus tard 3 jours avant le début du cours.
Vous et l\'enseignant pouvez alors vous préparer décemment pour le cours.<br /><br />

Tous les documents du cours seront accessibles pour vous, au plus tard 4 jours avant le début du cours.
Il peut toujours arriver que l\'enseignant demande des travaux supplémentaires à ajouter ultérieurement, par exemple
après une séance physique. Si cela se produit, vous pourrez le voir dans l\'environnement d\'apprentissage
Pendant les réunions, vous ne recevrez aucun matériel de leçon imprimé, nous vous conseillons d\'apporter un ordinateur portable et / ou une tablette.<br /><br />

Vous trouverez le coupon pour entrer dans le cours ci-joint. Ce coupon est personnel et unique, et donne accès aux cours appropriés pour votre éducation.
Veuillez lire attentivement les instructions sur le coupon.<br /><br />

Si vous avez des questions concernant la création d\'un compte ou si vous rencontrez d\'autres problèmes, vous pouvez contacter le helpdesk.
Vous trouverez de l\\information sur l\'environnement d\'apprentissage.
Lorsque personne n\'est disponible pour répondre à votre question, veuillez laisser votre nom, adresse e-mail et numéro de téléphone derrière et nous vous répondrons dès que possible.<br /><br />

Nous vous souhaitons bonne chance dans votre formation.<br /><br />

Cordialement,<br /><br />

##site_name##';
$string['coupon_mail_subject'] = 'Moodle Coupon généré';
$string['coupon_recipients_desc'] = 'Les colonnes suivantes doivent être présentes dans le CSV téléchargé, indépendamment de l\'ordre: E-mail, Sexe, Nom.<br/>
Pour chaque personne donnée dans le CSV, un coupon est généré et envoyé par courrier électronique à l\'utilisateur.<br/>
S\'il vous plaît prendre note que ces coupons seront créés a-synchrone par une tâche en arrière-plan; <I> pas </ i> instantanément.
C\'est parce que le processus de génération de coupons peut être assez long, surtout pour une grande quantité d\'utilisateurs.';
$string['coupons_ready_to_send'] = 'Votre coupon (s) a / ont été générés et seront envoyés à la date inscrite.<br />
    Vous recevrez un e-mail de confirmation lorsque tous les coupons ont été envoyés.';
$string['coupons_sent'] = 'Votre coupon (s) ont été générés. En quelques minutes, vous recevrez un email avec les coupons dans les pièces jointes.';
$string['course'] = 'cours';
$string['days_access'] = '{$a} jours ';
$string['default-coupon-page-template-botleft'] = '<ol>
<li>Inscrivez-vous {site_url}.</li>
<li>Vous recevrez un courriel avec l\'url de confirmation. Cliquez sur l\'url pour activer votre compte.</li>
<li>Une fois connecté avec votre compte, cliquer sur le lien "Formation/Cours" dans la barre de menu.</li>
<li>Passer à la deuxième étape.</li>
</ol>';
$string['default-coupon-page-template-botright'] = '<ol>
<li>Dans la page identifiée à votre nom, vous retrouverez un bloc appelé « COUPON » à gauche de votre écran.</li>
<li>Entrez votre code promo dans la zone prévu à cet effet.</li>
<li>L\'équipe de LANICCO vous souhaite une excellente session d\'étude!</li>
</ol>';
$string['default-coupon-page-template-main'] = 'Avec ce coupon, vous pouvez activer l\'accès au module e-learning. Vous avez un accès de "{accesstime}" à ce module.

Veuillez utiliser le code suivant pour activer votre accès.

{coupon_code}';
$string['download-sample-csv'] = 'Télécharger un exemple de fichier CSV';
$string['enrolperiod:indefinite'] = '<i>Indefinite</i>';
$string['error:alternative_email_invalid'] = 'Si vous avez coché \'utiliser un autre courriel\', ce champ doit contenir une adresse e-mail valide.';
$string['error:alternative_email_required'] = 'Si vous avez coché \'utiliser un autre courriel\', ce champ est obligatoire.';
$string['error:cohort_sync'] = 'Une erreur s\'est produite lors de la tentative de synchronisation des cohortes. Veuillez contacter le support.';
$string['error:coupon_already_used'] = 'Le coupon avec ce code a déjà été utilisé.';
$string['error:coupon_amount-recipients-both-set'] = 'Veuillez spécifier un nombre de coupons pour générer OU une liste csv de destinataires.';
$string['error:coupon_amount-recipients-both-unset'] = 'Ce champ ou le champ Destinataires doivent être définis.';
$string['error:coupon_amount_too_high'] = 'Veuillez entrer un montant entre{$a->min} et {$a->max}.';
$string['error:coupon_reserved'] = 'Le coupon avec ce code a été réservé pour un autre utilisateur.';
$string['error:course-coupons-not-copied'] = 'Une erreur s\'est produite lors de la tentative de copie de coupon-cours dans la nouvelle table coupon_cours. Veuillez contacter le support.';
$string['error:course-not-found'] = 'Le cours n\'a pas pu être trouvé.';
$string['error:invalid_coupon_code'] = 'Vous avez entré un code de coupon non valide.';
$string['error:invalid_email'] = 'S\'il vous plaît, mettez une adresse courriel valide.';
$string['error:missing_cohort'] = 'La cohorte liée à ce coupon n\'existe plus. Veuillez contacter le support.';
$string['error:missing_course'] = 'Le cours lié à ce coupon n\'existe plus. Veuillez contacter le support.';
$string['error:missing_group'] = 'Le (s) groupe (s) lié (s) à ce coupon n\'existe plus. Veuillez contacter le support.';
$string['error:moodledata_not_writable'] = 'Votre dossier moodledata / coupon_logos n\'est pas accessible en écriture. Veuillez corriger vos permissions.';
$string['error:no_coupons_submitted'] = 'Aucun de vos coupons n\'a encore été utilisé.';
$string['error:nopermission'] = 'Vous n\'avez pas la permission de faire ceci';
$string['error:numeric_only'] = 'Ce champ doit être numérique.';
$string['error:plugin_disabled'] = 'Le plugin cohort_sync a été désactivé. Veuillez contacter le support.';
$string['error:recipients-columns-missing'] = 'Impossible de valider le fichier. Êtes-vous sûr d\'avoir saisi les bonnes colonnes et le séparateur?';
$string['error:recipients-email-invalid'] = 'L\'adresse e-mail {$ a-> email} n\'est pas valide. Veuillez le corriger dans le fichier csv.';
$string['error:recipients-empty'] = 'Veuillez entrer au moins un utilisateur.';
$string['error:recipients-extension'] = 'Vous ne pouvez télécharger que des fichiers .csv.';
$string['error:recipients-invalid'] = 'Impossible de valider le fichier. Êtes-vous sûr d\'avoir saisi les colonnes et le séparateur?';
$string['error:recipients-max-exceeded'] = 'Votre fichier csv a dépassé le maximum de 10.000 coupons. Veuillez le limiter.';
$string['error:required'] = 'Ce champ est requis.';
$string['error:sessions-expired'] = 'Votre session a expiré. Veuillez réessayer.';
$string['error:unable_to_enrol'] = 'Une erreur s\'est produite lors de la tentative de vous inscrire dans le nouveau cours. Veuillez contacter le support.';
$string['error:wrong_code_length'] = 'Veuillez saisir un nombre entre 6 et 32.';
$string['error:wrong_doc_page'] = 'Vous essayez d\'accéder à une page qui n\'existe pas.';
$string['error:wrong_image_size'] = 'Le fond téléchargé n\'a pas la taille requise. Veuillez télécharger une image avec un rapport de 210 mm par 297 mm.';
$string['heading:administration'] = 'Gérer';
$string['heading:amountForm'] = 'Paramètres des montants';
$string['heading:coupon_type'] = 'Type de coupon';
$string['heading:csvForm'] = 'Paramètres CSV';
$string['heading:general_settings'] = 'Derniers réglages';
$string['heading:generatecoupons'] = 'Générer des coupons';
$string['heading:imageupload'] = 'Télécharger l\'image';
$string['heading:info'] = 'Info';
$string['heading:input_cohorts'] = 'Sélectionner des cohortes';
$string['heading:input_coupon'] = 'Coupon d\'entrée';
$string['heading:input_course'] = 'Sélectionnez un cours';
$string['heading:input_groups'] = 'Sélectionner des groupes';
$string['heading:inputcoupons'] = 'Coupon d\'entrée';
$string['heading:label_instructions'] = 'Instructions';
$string['heading:manualForm'] = 'Réglages manuels';
$string['label:alternative_email'] = 'Email alternatif';
$string['label:alternative_email_help'] = 'Envoyer des coupons par défaut à cette adresse e-mail.';
$string['label:api_enabled'] = 'Activer l\'API';
$string['label:api_enabled_desc'] = 'L\'API Coupon permet de générer des coupons à partir d\'un système externe.';
$string['label:api_password'] = 'Mot de passe API';
$string['label:api_password_desc'] = 'Le mot de passe qui peut être utilisé pour générer un coupon à l\'aide de l\'API.';
$string['label:api_user'] = 'Utilisateur API';
$string['label:api_user_desc'] = 'Le nom d\'utilisateur qui peut être utilisé pour générer un coupon à l\'aide de l\'API.';
$string['label:cleanupage'] = 'Age maximum?';
$string['label:cleanupage_help'] = 'Entrez l\'âge maximum d\'un coupon inutilisé avant qu\'il ne soit supprimé';
$string['label:cohort'] = 'Cohorte';
$string['label:connected_courses'] = 'Cours connecté(s)';
$string['label:coupon_amount'] = 'Nombre de coupons';
$string['label:coupon_amount_help'] = 'C\'est le nombre de coupons qui seront générés. Veuillez utiliser ce champ OU les destinataires des champs, pas les deux.';
$string['label:coupon_code'] = 'Code Coupon ';
$string['label:coupon_code_help'] = 'Le code coupon est le code unique qui est lié à chaque coupon individuel. Vous pouvez trouver ce code sur votre coupon.';
$string['label:coupon_code_length'] = 'Longueur du code';
$string['label:coupon_code_length_desc'] = 'Nombre de caractères du code promo.';
$string['label:coupon_cohorts'] = 'Cohorte(s)';
$string['label:coupon_cohorts_help'] = 'Sélectionnez la ou les cohortes auxquelles vos utilisateurs seront inscrits.';
$string['label:coupon_connect_course'] = 'Ajouter un cours(s)';
$string['label:coupon_connect_course_help'] = 'Sélectionnez tous les cours que vous souhaitez ajouter à la cohorte.
    <br /><b><i>Note: </i></b>Tous les utilisateurs qui sont déjà inscrits dans cette cohorte seront également inscrits dans les cours sélectionnés!';
$string['label:coupon_courses'] = 'Cours(s)';
$string['label:coupon_courses_help'] = 'Sélectionnez les cours auxquels vos utilisateurs seront inscrits.';
$string['label:coupon_email'] = 'Adresse e-mail';
$string['label:coupon_email_help'] = 'C\'est l\'adresse e-mail à laquelle les coupons générés seront envoyés.';
$string['label:coupon_groups'] = 'Ajouter un groupe(s)';
$string['label:coupon_groups_help'] = 'Sélectionnez les groupes auxquels vous souhaitez que vos utilisateurs soient inscrits lors de l\'inscription aux cours.';
$string['label:coupon_recipients'] = 'Destinataires';
$string['label:coupon_recipients_help'] = 'Avec ce champ, vous pouvez télécharger un fichier csv avec les utilisateurs.';
$string['label:coupon_recipients_txt'] = 'Destinataires';
$string['label:coupon_recipients_txt_help'] = 'Dans ce champ, vous pouvez apporter vos modifications finales au fichier csv téléchargé.';
$string['label:coupon_type'] = 'Générer à partir de';
$string['label:coupon_type_help'] = 'Les coupons seront générés en fonction du cours ou d\'une ou plusieurs cohortes.';
$string['label:current_image'] = 'Arrière-plan du Coupon actuel';
$string['label:date_send_coupons'] = 'Date d\'envoi';
$string['label:date_send_coupons_help'] = 'Date à laquelle les coupons seront envoyés au destinataire(s).';
$string['label:email_body'] = 'Message électronique';
$string['label:email_body_help'] = 'Le message électronique qui sera envoyé aux destinataires des coupons.';
$string['label:enablecleanup'] = 'Activer le nettoyage des coupons inutilisés?';
$string['label:enablecleanup_help'] = 'Cochez cette option pour nettoyer automatiquement (supprimer) les coupons inutilisés';
$string['label:enrolment_period'] = 'Période d\'inscription';
$string['label:enrolment_period_help'] = 'Période (en jours) l\'utilisateur sera inscrit dans les cours. Si elle est définie sur 0, aucune fin ne sera émise.';
$string['label:enter_coupon_code'] = 'Veuillez entrer votre code de coupon ici';
$string['label:generate_pdfs'] = 'Generate seperate PDF\'s';
$string['label:generate_pdfs_help'] = 'Vous pouvez sélectionner ici si vous souhaitez recevoir vos coupons en un seul fichier ou chaque coupon dans un fichier PDF séparé.';
$string['label:image'] = 'Arrière-plan du Coupon';
$string['label:image_desc'] = 'Arrière-plan devant être placé dans les coupons générés';
$string['label:info_coupon_cohort_courses'] = 'Informations sur la page: Cours de cohorte';
$string['label:info_coupon_cohorts'] = 'Informations sur la page: Sélection des cohortes';
$string['label:info_coupon_confirm'] = 'Information sur la page: Confirmer le coupon';
$string['label:info_coupon_course'] = 'Informations sur la page: Sélectionnez un cours';
$string['label:info_coupon_course_groups'] = 'Informations sur la page: Sélectionner les groupes de cours';
$string['label:info_coupon_type'] = 'Informations sur la page: Sélectionner le type de coupon';
$string['label:info_desc'] = 'Les informations ci-dessus.';
$string['label:info_imageupload'] = 'Informations sur la page: Télécharger l\'image';
$string['label:max_coupons'] = 'Coupons maximum';
$string['label:max_coupons_desc'] = 'Nombre des coupons pouvant être créés en une seule fois.';
$string['label:no_courses_connected'] = 'Il n\'y a pas de cours rattachés à cette cohorte.';
$string['label:no_groups_selected'] = 'Il n\'y a encore aucun groupe connecté à ces cours.';
$string['label:redirect_url'] = 'URL de redirection';
$string['label:redirect_url_help'] = 'Les utilisateurs de destination seront envoyés après avoir saisi leur code de coupon.';
$string['label:selected_cohort'] = 'Cohorte sélectionnée(s)';
$string['label:selected_courses'] = 'Cours choisis';
$string['label:selected_groups'] = 'Groupe sélectionné(s)';
$string['label:showform'] = 'Options générateur';
$string['label:type_cohorts'] = 'Cohorte(s)';
$string['label:type_course'] = 'Cours';
$string['label:use_alternative_email'] = 'Utiliser un autre courriel';
$string['label:use_alternative_email_help'] = 'Lorsqu\'il est coché, il utilisera par défaut l\'adresse électronique fournie dans le champ Autre courriel.';
$string['missing_config_info'] = 'Mettez vos informations supplémentaires ici - pour être mis en place dans la configuration globale du bloc.';
$string['page:generate_coupon.php:title'] = 'Générer des coupons';
$string['page:generate_coupon_step_five.php:title'] = 'Générer des coupons';
$string['page:generate_coupon_step_four.php:title'] = 'Générer des coupons';
$string['page:generate_coupon_step_three.php:title'] = 'Générer des coupons';
$string['page:generate_coupon_step_two.php:title'] = 'Générer des coupons';
$string['page:unused_coupons.php:title'] = 'Coupons inutilisés';
$string['pdf-meta:keywords'] = 'Coupon Moodle';
$string['pdf-meta:subject'] = 'Coupon Moodle';
$string['pdf-meta:title'] = 'Coupon Moodle';
$string['pdf:titlename'] = 'Coupon Moodle';
$string['pdf_generated'] = 'Les coupons ont été joints à ce courriel dans des fichiers PDF.<br /><br />';
$string['pluginname'] = 'Coupon';
$string['promo'] = 'Coupon plugin pour Moodle';
$string['promodesc'] = 'This plugin is written by Sebsoft Managed Hosting & Software Development
(<a href=\'http://www.sebsoft.nl/\' target=\'_new\'>http://sebsoft.nl</a>).<br /><br />
{$a}<br /><br />';
$string['remove-count'] = 'Cela supprimera les <i>{$a}</i> coupon(s).';
$string['report:cohorts'] = 'Cohorte';
$string['report:coupon_code'] = 'Code d\'abonnement';
$string['report:dateformat'] = '%d-%m-%Y %H:%M:%S';
$string['report:dateformatymd'] = '%d-%m-%Y';
$string['report:download-excel'] = 'Télécharger les coupons inutilisés';
$string['report:enrolperiod'] = 'Propriétaire';
$string['report:for_user_email'] = 'Prévu pour';
$string['report:heading:action'] = 'Action(s)';
$string['report:heading:coupon'] = 'Coupon';
$string['report:heading:coursename'] = 'Nom du cours';
$string['report:heading:coursetype'] = 'Type de cours';
$string['report:heading:datecomplete'] = 'Date d\'achèvement';
$string['report:heading:datestart'] = 'Date de début';
$string['report:heading:errormessage'] = 'Erreur';
$string['report:heading:errortype'] = 'Type';
$string['report:heading:grade'] = 'niveau';
$string['report:heading:status'] = 'Statut';
$string['report:heading:timecreated'] = 'Date';
$string['report:heading:user'] = 'Utilisateur';
$string['report:immediately'] = 'Immédiatement';
$string['report:issend'] = 'Est envoyer';
$string['report:owner'] = 'Propriétaire';
$string['report:senddate'] = 'Date d\'envoi';
$string['report:status_completed'] = 'Cours terminé';
$string['report:status_not_started'] = 'Cours non commencé';
$string['report:status_started'] = 'Cours commencé';
$string['showform-amount'] = 'Je veux créer une quantité arbitraire de coupons';
$string['showform-csv'] = 'Je souhaite créer des coupons à l\'aide d\'un fichier CSV avec destinataires';
$string['showform-manual'] = 'Je souhaite configurer manuellement les destinataires';
$string['str:mandatory'] = 'Obligatoire';
$string['str:optional'] = 'Optionnel';
$string['success:coupon_used'] = 'Coupon utilisé - Vous pouvez maintenant accéder au cours(s)';
$string['success:uploadimage'] = 'Votre nouvelle image de coupon a été téléchargée.';
$string['tab:apidocs'] = 'API Docs';
$string['tab:errors'] = 'Rapports d\'erreurs';
$string['tab:report'] = 'Rapport d\'avancement';
$string['tab:unused'] = 'Coupons inutilisés';
$string['tab:used'] = 'Coupons utilisés';
$string['tab:wzcouponimage'] = 'Image du modèle';
$string['tab:wzcoupons'] = 'Générer un coupon(s)';
$string['task:cleanup'] = 'Nettoyage des vieux coupons non utilisés';
$string['task:sendcoupons'] = 'Envoyer des coupons planifiés';
$string['tasksettings'] = 'Paramètres de la tâche';
$string['tasksettings_desc'] = '';
$string['textsettings'] = 'Paramètres de texte';
$string['textsettings_desc'] = 'Ici, vous pouvez configurer des textes personnalisés qui seront affichés dans différents écrans d\'assistant pour le générateur de coupons';
$string['th:action'] = 'Action(s)';
$string['th:cohorts'] = 'Cohorte';
$string['th:course'] = 'Cours';
$string['th:enrolperiod'] = 'Période d\'inscription';
$string['th:for_user_email'] = 'Prévu pour';
$string['th:groups'] = 'Groupe(s)';
$string['th:immediately'] = 'Immédiatement';
$string['th:issend'] = 'Envoyé?';
$string['th:owner'] = 'Propriétaire';
$string['th:senddate'] = 'Date d\'envoi';
$string['th:submission_code'] = 'Code d\'abonnement';
$string['unlimited_access'] = 'illimité';
$string['url:api_docs'] = 'Documentation API';
$string['url:generate_coupons'] = 'Générer un coupon';
$string['url:input_coupon'] = 'Coupon d\'entrée';
$string['url:uploadimage'] = 'Modifier l\'image du coupon';
$string['url:view_reports'] = 'Afficher les rapports';
$string['url:view_unused_coupons'] = 'Afficher les coupons inutilisés';
$string['view:api:heading'] = 'Coupon API';
$string['view:api:title'] = 'Coupon API';
$string['view:api_docs:heading'] = 'Coupon API Documentation';
$string['view:api_docs:title'] = 'Coupon API Documentation';
$string['view:errorreport:heading'] = 'Rapport - erreurs de coupons';
$string['view:errorreport:title'] = 'Rapport - erreurs de coupons';
$string['view:generate_coupon:heading'] = 'Generate Coupon';
$string['view:generate_coupon:title'] = 'Générer un coupon';
$string['view:input_coupon:heading'] = 'Coupon d\'entrée';
$string['view:input_coupon:title'] = 'Coupon d\'entrée';
$string['view:reports-unused:heading'] = 'Report - Coupons inutilisés';
$string['view:reports-unused:title'] = 'Report - Coupons inutilisés';
$string['view:reports-used:heading'] = 'Rapport - Coupons usagés';
$string['view:reports-used:title'] = 'Rapport - Coupons usagés';
$string['view:reports:heading'] = 'Rapport - Progression basée sur les coupons';
$string['view:reports:title'] = 'Rapport - Progression basée sur les coupons';
$string['view:uploadimage:heading'] = 'Importer un nouvel arrière-plan de coupon';
$string['view:uploadimage:title'] = 'Télécharger l\'arrière-plan de coupon';
$string['with-names'] = 'Avec les noms ou identifiants suivants';
