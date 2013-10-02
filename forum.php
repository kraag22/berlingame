<?php
/**
 * @file forum.php
 * @brief Forum
 */

$SKIN_SUBDIR = "forum";
define("BoB", 1);

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

require_once("${DIR_LIB}page_elements/page_element.php");
require_once("${DIR_LIB}page_elements/side_menu.php");
require_once("${DIR_LIB}forum/common.php");
require_once("{$DIR_CONFIG}formulare.php");

//OBSAH FORA
$id_vlakna = (int) request('section', $VLAKNO_HLAVNI);
$page->add_element(zobraz_forum($page, $db, $id_vlakna), 'obsah');
$page->add_element(zobraz_odesilaci_formular($id_vlakna), 'formular');

//nadpis fora
	$forumNazev = nazevFora($id_vlakna);
	$text = $forumNazev;
	$page->add_element(new textElement($text), 'nadpis');

//MENU
$side_menu = new side_menuElement("skins/default/forum");
$side_menu->add_menuitem("Hlavní fórum", "forum.php?section=$VLAKNO_HLAVNI", "button_hlavni.png");

	//zatim nepouzite
	/*
	$f_aliance = JeUzivatelVAlianci();
	if ($f_liga > 0) 
		$side_menu->add_menuitem("Ligové fórum", "forum.php?section=" . ($VLAKNA_LIGY+$f_liga) , "button_ligove.png");
	if ($f_aliance > 0)
		$side_menu->add_menuitem("Alianční fórum", "forum.php?section=" . ($VLAKNA_ALIANCE+$f_aliance) , "button_aliancni.png");
*/
$side_menu->add_menuitem("Dotazy a návrhy", "forum.php?section=$VLAKNO_DOTAZY" , "button_dotazy.png");

if ($users_class->is_logged() ){
	//ma videt tymove forum?
	$f_liga = VlaknoVTymoveLize();
	if ($f_liga > 0){
		$strana = VratAtributHrace('strana', $users_class->user_id);
		$side_menu->add_menuitem("Týmové fórum", "forum.php?section=" . ($VLAKNA_TYMY + $f_liga) , "button_tymove_$strana.png");
	}
}	


$page->add_element($side_menu,'main_menu');


$page->set_headline($TEXT["description"]);
$page->set_title($TEXT["title"]);
$page->document_author = $TEXT["author"];
$page->copyright = $TEXT["copyright"];

$page->add_private_text($TEXT["footer"], 'footer');

$page->print_page();

// vlozi ukoncovaci skript, ktery ukonci pripojeni k databazi, ...
finalize();

