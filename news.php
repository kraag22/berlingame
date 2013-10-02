<?php

/**
 * @file intro.php
 * @brief Úvodní obrazovka po přihlášení
 *
 */

$SKIN_SUBDIR = "index_sub";
define('BoB', 1);


// vlozi soubor s konfiguracnim nastavenim unikatnim pro tento server
require_once("config/config.php");
require_once("${DIR_CONFIG}text.php");

// include basic functions and classes
require_once("$DIR_CORE/main.php");

if (!$auth->authorised_to('module_news')) {
        global $error;
        $error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                        'NO_ACCES_GRANTED',
                        "Uživatel \"" . $users_class->user_login() . "\" se dostal do modulu,
                         ke kterému nemá práva",null);
}

$page->set_skin_subdir($SKIN_SUBDIR);

require_once("{$DIR_LIB}page_elements/login_bar.php");
require_once("${DIR_LIB}page_elements/page_element.php");
require_once("{$DIR_LIB}page_elements/main_menu.php");

$main_menu = new main_menuElement("skins/default/main_menu");

$main_menu->add_menuitem("Vstoupit", "intro.php", "vstoupit.jpg");
$main_menu->add_menuitem("Úvod", "uvod.php", "uvod.jpg");
$main_menu->add_menuitem("Nápověda", "help.php", "napoveda.jpg");
$main_menu->add_menuitem("Novinky", "news.php", "novinky.jpg");
$main_menu->add_menuitem("Forum", "forum.php", "forum2.jpg");  
if ($users_class->is_logged() ){
	$main_menu->add_menuitem("Pošta", "posta.php", "posta2.jpg");
}
$main_menu->add_menuitem("Kontakt", "index.php?section=kontakt", "kontakt.jpg");
$page->add_element($main_menu,'main_menu');

	$text= '<h1>Novinky</h1>
	
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech  
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 

	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech tech 
	';
       
        $form = new textElement($text, NULL);
        $page->add_element($form, 'obsah');

	$login_bar = new login_barElement($db,$page,"skins/default/login_form");
	$page->add_element($login_bar,'sidebar');

$page->set_headline($TEXT["description"]);
$page->set_title($TEXT["title"]);
$page->document_author = $TEXT["author"];
$page->copyright = $TEXT["copyright"];


$page->add_private_text($TEXT["footer"], 'footer');

$page->print_page();

// vlozi ukoncovaci skript, ktery ukonci pripojeni k databazi, ...
finalize();

?>
