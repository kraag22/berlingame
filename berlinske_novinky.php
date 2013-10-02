<?php
$SKIN_SUBDIR = "berlinske_novinky";
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

/*if(!isset($_REQUEST['vydani'])){
	$vydani ='
	<map name="starsi">
<area href="berlinske_novinky.php?vydani=0" shape="rect" coords="0, 0, 150, 100">
</map>
	<img src="skins/default/berlinske_novinky/berlinske_novinky_1'
	.'.png" width="948" height="774" usemap="#starsi" border="0" />';
}
else{
	$vydani ='<img src="skins/default/berlinske_novinky/berlinske_novinky_0'
	.'.png" width="947" height="928" />';
}*/

if(!isset($_REQUEST['vydani'])){
	$cislo = 8;

}
else{
	$cislo = htmlsafe($_REQUEST['vydani']);
	if ($cislo < 0) $cislo =0;
}

	$vydani ='
	<map name="starsi">
<area href="berlinske_novinky.php?vydani='.($cislo-1).'" shape="rect" coords="0, 0, 150, 100">
<area href="http://www.boj-o-berlin.cz" shape="rect" target="top" coords="540, 670, 700, 878">
<area href="http://www.berlingame.net" shape="rect" target="top" coords="700, 670, 945, 878">
</map>
	<img src="skins/default/berlinske_novinky/berlinske_novinky_'.$cislo.''
	.'.png"  usemap="#starsi" border="0" />';



$page->add_private_text( $vydani, 'vydani');

$page->set_headline($TEXT["description"]);
$page->set_title($TEXT["title"]);
$page->document_author = $TEXT["author"];
$page->copyright = $TEXT["copyright"];


$page->add_private_text($TEXT["footer"], 'footer');

$page->print_page();

// vlozi ukoncovaci skript, ktery ukonci pripojeni k databazi, ...
finalize();


?>