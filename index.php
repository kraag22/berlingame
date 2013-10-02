<?php

/**
 * @file index.php
 * @brief Hlavní stránka, která se stará o přihlášení
 */

$SKIN_SUBDIR = "index";

define("BoB", 1);

//FIXME v celem projektu pridat v kazdem souboru test na existenci BoB!!!!

// vlozi soubor s konfiguracnim nastavenim unikatnim pro tento server
require_once("config/config.php");
require_once($DIR_CONFIG . "text.php");

// include basic functions and classes
require_once("$DIR_CORE/main.php");

if (!$auth->authorised_to('module_index')) {
        global $error;
        $error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                        'NO_ACCES_GRANTED',
                        "Uživatel \"" . $users_class->user_login() . "\" se dostal do modulu,
                         ke kterému nemá práva",null);
}

$page->set_skin_subdir($SKIN_SUBDIR);

require_once("${DIR_LIB}page_elements/login_bar.php");
require_once("${DIR_LIB}page_elements/page_element.php");
require_once("${DIR_LIB}page_elements/main_menu.php");
require_once("${DIR_LIB}page_elements/side_menu.php");
require_once("${DIR_LIB}page_elements/I_novinky.php");

if( date('G') > 12 ){
	if( is_file($secondlock) ){
		unlink($secondlock);
	}
}

$main_menu = new main_menuElement("skins/default/main_menu");
$main_menu->add_menuitem("Vstoupit", "intro.php", "vstoupit.jpg");
$main_menu->add_menuitem("Nápověda", "index.php?section=napoveda", "napoveda.jpg");
$main_menu->add_menuitem("Novinky", "berlinske_novinky.php", "novinky.jpg", "_blank");
$main_menu->add_menuitem("Forum", "forum.php", "forum2.jpg", "_blank");  
$main_menu->add_menuitem("Kontakt", "index.php?section=kontakt", "kontakt.jpg");
$main_menu->add_menuitem("Partneři", "index.php?section=partneri", "partneri.jpg");

$page->add_element($main_menu,'main_menu');

//login bar
$login_bar = new login_barElement($db,$page,"skins/default/login_form");


$side_menu = new side_menuElement("skins/default/login_form");

//nastaveni poctu registovanych hracu
$hr = $db->Query("SELECT count(*) FROM users_sys");
$row = $db->GetFetchRow( $hr );
$pocet = $row[0];
$page->add_element( new textElement($pocet),'registrovani_hraci');

//test prichodu noveho uzivatele pres referenci
$users_class->reference_set_cookie();

if(array_get_index_safe('not_logged', $_GET)){
	$form = new textElement( 'alert("Byl jste dlouho neaktivní.Systém vás přesunul na titulní stránku.");' );
	$page->add_element($form, 'js_not_logged');
}

$page->add_element($login_bar,'sidebar');
switch(array_get_index_safe('section', $_GET)) {
	case 'kontakt':
        require($DIR_LIB . "index/kontakt.php");
		break;
	case 'novinky':
        require($DIR_LIB . "index/novinky.php");
		break;
	case 'napoveda':
        require($DIR_LIB . "index/napoveda.php");
		break;
	case 'uvod':
        require($DIR_LIB . "index/uvod.php");
		break;
	case 'pravidla':
        require($DIR_LIB . "index/pravidla.php");
		break;
	case 'partneri':
        require($DIR_LIB . "index/partneri.php");
		break;
	case 'betatest':
        require($DIR_LIB . "index/betatest.php");
		break;
	case 'tutorial':
        require($DIR_LIB . "index/tutorial.php");
		break;
	case 'tutorial1':
        require($DIR_LIB . "index/tutorial1.php");
		break;
	case 'tutorial2':
        require($DIR_LIB . "index/tutorial2.php");
		break;
	case 'tutorial3':
        require($DIR_LIB . "index/tutorial3.php");
		break;
	case 'tutorial4':
        require($DIR_LIB . "index/tutorial4.php");
		break;
	case 'tutorial5':
        require($DIR_LIB . "index/tutorial5.php");
		break;
	default:
		//obsah titulky
		require_once("${DIR_LIB}index/index.php");
		//dulezita oznameni
		$page->add_element($side_menu,'sidemenu');
		
		//test prohlizece
		$pole = Array();
	 	$pole['fileName'] = $DIR_SCRIPTS . 'identifikace_prohlizece.js';
		$page->add_script($pole);
		$page->add_private_text('<body onLoad="identifikace();">', 'identifikace');
}

$page->set_headline($TEXT["description"]);
$page->set_title($TEXT["title"]);
$page->document_author = $TEXT["author"];
$page->copyright = $TEXT["copyright"];

$page->add_private_text($TEXT["footer"], 'footer');

$page->print_page();

// vlozi ukoncovaci skript, ktery ukonci pripojeni k databazi, ...
finalize();

?>
