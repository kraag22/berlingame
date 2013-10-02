<?php

/**
 * @file intro.php
 * @brief Úvodní obrazovka po přihlášení
 *
 */

$SKIN_SUBDIR = "intro";
define('BoB', 1);


// vlozi soubor s konfiguracnim nastavenim unikatnim pro tento server
require_once("config/config.php");
require_once("${DIR_CONFIG}text.php");

// include basic functions and classes
require_once("$DIR_CORE/main.php");

$page->set_skin_subdir($SKIN_SUBDIR);

require_once("${DIR_LIB}page_elements/page_element.php");
require_once("${DIR_LIB}page_elements/intro_common.php");

switch(request('section')) {
        case 'prihlaseni' :
                require_once("${DIR_LIB}page_elements/intro_prihlaseni.php");
                break;
        case 'prihlaseni_symboly' :
                require_once("${DIR_LIB}page_elements/intro_prihlaseni_symboly.php");
                break;
        case 'nastaveni' :
                require_once("${DIR_LIB}page_elements/intro_nastaveni.php");
                break;      
        default:
               require_once("${DIR_LIB}page_elements/intro.php");
                break;
}

		

$page->set_headline($TEXT["description"]);
$page->set_title($TEXT["title"]);
$page->document_author = $TEXT["author"];
$page->copyright = $TEXT["copyright"];

$page->print_page();

// vlozi ukoncovaci skript, ktery ukonci pripojeni k databazi, ...
finalize();

?>
