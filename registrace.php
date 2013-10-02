<?php
$SKIN_SUBDIR = "index";

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
$page->set_skin_subdir( $SKIN_SUBDIR );

require_once("${DIR_LIB}page_elements/login_bar.php");
require_once("${DIR_LIB}page_elements/page_element.php");
require_once("${DIR_LIB}page_elements/main_menu.php");

if ($users_class->is_logged() ){
         $page->redir('intro.php');
}
$main_menu = new main_menuElement("skins/default/main_menu");
$main_menu->add_menuitem("Vstoupit", "intro.php", "vstoupit.jpg");
$main_menu->add_menuitem("Nápověda", "index.php?section=napoveda", "napoveda.jpg");
$main_menu->add_menuitem("Novinky", "berlinske_novinky.php", "novinky.jpg", "_blank");
$main_menu->add_menuitem("Forum", "forum.php", "forum2.jpg", "_blank");  
$main_menu->add_menuitem("Kontakt", "index.php?section=kontakt", "kontakt.jpg");
$main_menu->add_menuitem("Partneři", "index.php?section=partneri", "partneri.jpg");

$page->add_element($main_menu,'main_menu');

//nastaveni poctu registovanych hracu
$hr = $db->Query("SELECT count(*) FROM users_sys");
$row = $db->GetFetchRow( $hr );
$pocet = $row[0];
$page->add_element( new textElement($pocet),'registrovani_hraci');

switch(request('section')) {
        case 'novacek' :
                require("${DIR_LIB}registrace/reg_novacek.php");
                break;
        case 'veteran':
                require("${DIR_LIB}registrace/reg_veteran.php");
                break;
        default:
                $text= '<div class="reg_prvni">
                <a href="registrace.php?section=novacek" class="reg_odkaz">Registrace</a>
                 - Jednoduchá registrace, zabere max 20 sekund!! <br />
                 </div>';

              /* $text .= ' <a href="registrace.php?section=veteran">Jsem veterán</a> - Lze vyplnit všechny volby,
                ale očekává se alespoň minimální znalost hry.<br />

                ';*/

                $form = new textElement($text);
                $page->add_element($form, 'obsah');
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
