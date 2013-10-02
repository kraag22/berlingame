<?php
$SKIN_SUBDIR = "svet";
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
		
        $text= 'frame_menu.php?section=intro';
        $form = new textElement($text);
        $page->add_element($form, 'menu');

        $text= 'frame_akcni_menu.php?section=intro';
        $form = new textElement($text);
        $page->add_element($form, 'popis');

        $text= 'frame_mapa.php?section=intro';
        $form = new textElement($text);
        $page->add_element($form, 'mapa');



$page->set_headline($TEXT["description"]);
$page->set_title($TEXT["title"]);
$page->document_author = $TEXT["author"];
$page->copyright = $TEXT["copyright"];


$page->add_private_text($TEXT["footer"], 'footer');

$page->print_page();

// vlozi ukoncovaci skript, ktery ukonci pripojeni k databazi, ...
finalize();


?>