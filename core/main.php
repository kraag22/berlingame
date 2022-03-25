<?php
/**
 * @file
 * @brief Inicializace frameworku a interface pro práci s ním.
 * @author Michal Podzimek, Tomáš Pop, Jiří Toušek, Martin Bartušek, Jan Cipra
 *
 * Include tohoto souboru zajistí inicializaci modulů frameworku a
 * vytvoří globální objekty pro práci s nimi (viz níže).
 *
 * @addtogroup g_framework Framework
 * @{
 * @brief Framework poskytující základní funkcionalitu aplikace.
 *
 * Obsahuje moduly poskytující základní funkcionalitu aplikace -
 * nastavení aplikace, přístup k DB, autentizaci a autorizaci uživatelů,
 * práci s databází uživatelů, vykreslování stránky, práce s formuláři aj.
 *
 *
 */
//TODO lepsi obecny popis frameworku
//TODO soupis souboru, ktere jsou soucasti frameworku (neni to jen /core)


date_default_timezone_set('Europe/Prague');


// --------------------- globalni promenne -------------------------------------

/* Odkaz na objekt DbLib - interface pro přístup k DB. */
$db = NULL;

/* Objekt sloužící ke zpracování chybových hlášení aplikace. */
$error = NULL;

/* Objekt reprezentující databázi uživatelů
    a poskytující data o aktuálně přihlášeném uživateli. */
$users_class = NULL;

/* Objekt poskytující informace o právech aktuálně přihlášeného uživatele. */
$auth = NULL;

/* Mechanismus pro vytváření formulářů. */
$fm_class = NULL;

/* Mechanismus pro vykreslování stránky. */
$page = NULL;



// ----------------- konec definic globalnich promennych -----------------------




error_reporting(E_ALL);

// pri prepoctu ladenku nechcu
if (isset($prepocet_override) && $prepocet_override) {
	if(!$DEBUG){
		error_reporting(E_ALL & ~E_NOTICE);
	}
}
else{
	// aktivovani ladenky
	require_once($DIR_LIBRARY . "nette/Debug.php");
	require_once($DIR_LIBRARY . "nette/exceptions.php");
	Debug::enable(!$DEBUG, $DIR_LOG . 'php_' . date('y-m-d') . '.log');
	Debug::$strictMode = FALSE;

	// profilovani
	if (isset($ENABLE_PROFILER) && $ENABLE_PROFILER) {
		Debug::enableProfiler();
	}
}

// Initialize session data
session_start();





/* ========================================================================== */


/**
 * @brief Ukončovací funkce.
 *
 * Funkce zajistí korektní ukončení běhu skriptu. Řízení se z funkce
 * nikdy nevrátí.
 *
 * @param $status 0, prázdný řetězec nebo null při úspěchu, ostatní hodnoty
 *                      znamenají chybový kód (tento parametr má stejný význam jako parametr
 *                      status funkce exit, které je také na konci předán)
 */
function finalize($status = 0) {
        global $db, $ENABLE_PROFILER;

        if ($status) {
                /* aplikace skoncila chybou */

        } else {
                /* aplikace skoncila korektne */

        }

		if ($ENABLE_PROFILER && class_exists('Debug')) {
			Debug::$counters['Query count'] = $db->GetQueryCount();
		}

        if ($db->IsConnected()) {
                $db->CloseConnection();
        }

        exit($status);
}



/* ========================================================================== */



// --------- Načtení jazykových proměnných použitých v core  -------------------

// jazykove konstanty pro core
require_once($DIR_LANG . "core.php");

// jazykove konstanty pro vsechny stranky
require_once($DIR_LANG . "all_pages.php");

// chybova hlaseni aplikace
require_once($DIR_LANG . "errors.php");



// ------------------ app_mail funkce ------------------------------------------
//FIXME maily
/* muze byt nutna pri zpracovani error_handleru */
//require_once($DIR_CONFIG . 'app_mail.php');


// ------------- Inicializace mechanismu chybových hlášení  --------------------

require_once("$DIR_CORE/error.php");

/** Objekt sloužící ke zpracování chybových hlášení aplikace. */
$error = new Error();

// ------------------ Inicializace zakladnich knihoven  ------------------------

// include functions library
require_once("$DIR_CORE/lib.php");
require_once("$DIR_CORE/files.php");
// include database library
require_once("$DIR_CORE/dblib.php");

// include various tools
require_once("$DIR_CORE/tools.php");


// funkce, ktera na serveru, kde je zapnuta direktiva register
// globals zrusi jeji vysledek
unregister_globals();



// Inicializace DB

/** Odkaz na objekt DbLib - interface pro přístup k DB. */
$db = new DbLibMySQL();
$db->init($DB_SERVER, $DB_PORT, $DB_USER, $DB_PASS, $DB_DATABASE);
$db->OpenConnection();



// include library with users logging, ... functions.
require_once("$DIR_CORE/users.php");

/** Objekt reprezentující databázi uživatelů
    a poskytující data o aktuálně přihlášeném uživateli. */
$users_class = new Users($db);


// ---------------- prihlaseni / odhlaseni a autentizace ----------------------

// zjisteni jestli byly odeslany prihlasovaci udaje
$prave_prihlasen = false;
if (array_key_exists('fm_login_form', $_POST)) {
  if (!$users_class->log_user(sqlsafe(trim($_POST['lf_login'])),
                sqlsafe(trim($_POST['lf_pwd'])))) {
  // FIXME predelat na nejake globalni chybove hlasky, az budou
        //echo $_ERR['desc']."<br />";
        ;
  }
  else{
		$prave_prihlasen = true;
  }
}







// ----------------------- autorizace -----------------------------------------

// vlozi soubor s knihovnou pro autorizaci
require_once("$DIR_CORE/auth.php");

/** Objekt poskytující informace o právech aktuálně přihlášeného uživatele. */
$auth = new Authorisation($db, $users_class->user_login());



// -------------- inicializace vykreslovaciho mechanismu ----------------------

require_once("$DIR_CORE/page.php");

/** Mechanismus pro vykreslování stránky. */
$page = new Page();





// --------------------- inicializace form manageru ---------------------------

// include library with creating forms functions.
require_once("$DIR_CORE/form_manager.php");

/** Mechanismus pro vytváření formulářů. */
$fm_class = new Form_manager($db);

// odchyceni odeslanych formularu
$_POST = $fm_class->form_catch($_POST);


// list_element
require_once("$DIR_CORE/list_element.php");

require_once($DIR_CONFIG . "konstanty.php");

// logout user if $_GET['logout'] is 1
if ( array_key_exists('section', $_REQUEST) && ($_REQUEST['section'] == 'logout')) {
	$users_class->logout_user();
	$page->redir('./index.php');
}

//pokud se hrac prihlasil
if($prave_prihlasen){
	$page->redir('./intro.php');
}


$GOOGLE_ANALYTICS = <<<CODE
<!-- Plausible -->
<script defer data-domain="berlingame.cz" src="https://plausible.kraag22.com/js/plausible.js"></script>
CODE;

// $GOOGLE_ANALYTICS = '<script>
//   (function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){
//   (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
//   m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
//   })(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');

//   ga(\'create\', \'UA-48831479-1\', \'berlingame.cz\');
//   ga(\'send\', \'pageview\');

// </script>';

$GOOGLE_ADWORDS = '<!-- Google Code for registrace Conversion Page -->
<script language="JavaScript" type="text/javascript">
<!--
var google_conversion_id = 1037045425;
var google_conversion_language = "cs";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "fhM_CMnWpgEQsZ3A7gM";
//-->
</script>
<script language="JavaScript" src="http://www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<img height="1" width="1" border="0" src="http://www.googleadservices.com/pagead/conversion/1037045425/?label=fhM_CMnWpgEQsZ3A7gM&amp;guid=ON&amp;script=0"/>
</noscript>';

$SKLIK_KONVERZE = '<!-- Měřicí kód Sklik.cz -->
<iframe width="113" height="14" frameborder="0" scrolling="no"
 src="http://out.sklik.cz/c/199161443/c.html?format=kremova">
</iframe>';

/**
 * @}
 */
?>
