<?php

/**
 * @file posta.php
 * @brief Pošta uživatele
 */

$SKIN_SUBDIR = "posta";
define("BoB", 1);


// vlozi soubor s konfiguracnim nastavenim unikatnim pro tento server
require_once("config/config.php");
require_once("${DIR_CONFIG}text.php");
require_once("${DIR_CONFIG}lib.php");

// include basic functions and classes
require_once("$DIR_CORE/main.php");

if (!$auth->authorised_to('module_posta')) {
        global $error;
        $error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                        'NO_ACCES_GRANTED',
                        "Uživatel \"" . $users_class->user_login() . "\" se dostal do modulu,
                         ke kterému nemá práva",null);
}

$page->set_skin_subdir($SKIN_SUBDIR);

require_once("${DIR_LIB}posta/common.php");
require_once("${DIR_LIB}page_elements/login_bar.php");
require_once("${DIR_LIB}page_elements/page_element.php");
//require_once("${DIR_LIB}page_elements/main_menu.php");
//require_once("${DIR_LIB}page_elements/side_menu.php");

switch (request('section')) {

	case $POSTA_ODESLANI:
		require_once("${DIR_LIB}posta/odeslat.php");
		$page->add_element(posta_odeslat($db), 'obsah');
		$styl = $POSTA_NAPSAT;
		
		break;	
	case $POSTA_NAPSAT:
		require_once("${DIR_LIB}posta/napsat.php");
		$page->add_element(posta_napsat(), 'obsah');
		$styl = $POSTA_NAPSAT;
		break;	

	case $POSTA_OBSAH:
		require_once("${DIR_LIB}posta/obsah.php");
		$page->add_element(posta_obsah(), 'obsah');
		break;	
	case $POSTA_ODESLANA;
		//zobrazi konkretni zpravu
			if(is_numeric(request("detail"))){
				require_once("${DIR_LIB}posta/detail.php");
				$page->add_element(posta_detail($db,request("detail")), 'obsah');
			}
			//zobrazi seznam zprav
			else{
				require_once("${DIR_LIB}posta/odeslane.php");
				$page->add_element(posta_odeslane($db), 'obsah');
			}
		$styl = $POSTA_ODESLANA;
		break;
	case $POSTA_DORUCENA:
		// FALLTHROUGH

	default:
		//zobrazi konkretni zpravu
		if(is_numeric(request("detail"))){
			require_once("${DIR_LIB}posta/detail.php");
			$page->add_element(posta_detail($db,request("detail"), $POSTA_DORUCENA), 'obsah');
		}
		//zobrazi seznam zprav
		else{
			require_once("${DIR_LIB}posta/dorucene.php");
			$page->add_element(posta_dorucene($db), 'obsah');
		}
		
		$styl = $POSTA_DORUCENA;
		break;	

}

require($DIR_LIB . "posta/menu.php");

/*$side_menu = new side_menuElement("skins/default/posta");
$side_menu->add_menuitem("Doručené", "posta.php?section=$POSTA_DORUCENA" , "dorucene.jpg");
$side_menu->add_menuitem("Napsat", "posta.php?section=$POSTA_NAPSAT", "napsat.jpg");
$page->add_element($side_menu,'sidemenu');*/

$page->set_headline($TEXT["description"]);
$page->set_title($TEXT["title"]);
$page->document_author = $TEXT["author"];
$page->copyright = $TEXT["copyright"];

//$page->add_private_text($TEXT["uvitani_titulka"], 'uvitani_titulka');
$page->add_private_text($TEXT["footer"], 'footer');

$page->print_page();

// vlozi ukoncovaci skript, ktery ukonci pripojeni k databazi, ...
finalize();

?>
