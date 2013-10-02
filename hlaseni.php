<?php
$SKIN_SUBDIR = "hlaseni";
define('BoB', 1);

// vlozi soubor s konfiguracnim nastavenim unikatnim pro tento server
require_once("config/config.php");
require_once($DIR_CONFIG . "text.php");

// include basic functions and classes
require_once("$DIR_CORE/main.php");

/*if (!$auth->authorised_to('module_')) {
        global $error;
        $error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                        'NO_ACCES_GRANTED',
                        "Uživatel \"" . $users_class->user_login() . "\" se dostal do modulu,
                         ke kterému nemá práva",null);
}*/
$page->set_skin_subdir("$SKIN_SUBDIR");

require_once($DIR_LIB . "page_elements/page_element.php");


	switch(array_get_index_safe('section', $_GET)) {
        case 'globalni' :
            require($DIR_LIB . "hlaseni/globalni.php");
            $styl = "globalni";
            break;
        case 'akce' :
        	require($DIR_LIB . "hlaseni/akce.php");
        	$styl = "akce";
        	break;
        case 'boj' :	
        	require($DIR_LIB . "hlaseni/boj.php");
        	$styl = "boj";
        	break;
        default:
        	require($DIR_LIB . "hlaseni/globalni.php");
        	$styl = "globalni";
        	break;
	}
 
	require($DIR_LIB . "hlaseni/menu.php");

$page->set_headline($TEXT["description"]);
$page->set_title($TEXT["title"]);
$page->document_author = $TEXT["author"];
$page->copyright = $TEXT["copyright"];


$page->add_private_text($TEXT["footer"], 'footer');

$page->print_page();

// vlozi ukoncovaci skript, ktery ukonci pripojeni k databazi, ...
finalize();


?>