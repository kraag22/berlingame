<?php

/**
 * @file intro.php
 * @brief Úvodní obrazovka po přihlášení
 *
 */

$SKIN_SUBDIR = "info";
define('BoB', 1);


// vlozi soubor s konfiguracnim nastavenim unikatnim pro tento server
require_once("config/config.php");
require_once("${DIR_CONFIG}text.php");
require_once($DIR_CONFIG . "konstanty.php");

// include basic functions and classes
require_once("$DIR_CORE/main.php");

$page->set_skin_subdir($SKIN_SUBDIR);

require_once("${DIR_LIB}page_elements/page_element.php");

//ziskani ID ligy a vlozeni do sessions
	if(isset($_GET['id_scenare']) && is_numeric($_GET['id_scenare'])){
		$_SESSION['id_ligy'] = sqlsafe($_GET['id_scenare']);
	}
	else{
		if(!isset($_SESSION['id_ligy'])){
			//pokud se ID nepredalo a neni vyplnene, tak se presmeruji na index
			$page->redir('index.php?not_logged=1');
		}
	}

switch(request('section')) {
        case 'scenar' :
                //FALLTROUGHT      
        default:
               require_once("${DIR_LIB}info/scenar.php");
                break;
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