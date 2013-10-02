<?php
$SKIN_SUBDIR = "frame_akcni_menu";
define('BoB', 1);



// vlozi soubor s konfiguracnim nastavenim unikatnim pro tento server
require_once("config/config.php");
require_once($DIR_CONFIG . "text.php");
require_once($DIR_CONFIG . "formulare.php");


// include basic functions and classes
require_once("$DIR_CORE/main.php");

/*if (!$auth->authorised_to('module_player')) {
        global $error;
        $error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                        'NO_ACCES_GRANTED',
                        "Uživatel \"" . $users_class->user_login() . "\" se dostal do modulu,
                         ke kterému nemá práva",null);
}*/
$page->set_skin_subdir("$SKIN_SUBDIR");

require_once($DIR_LIB . "page_elements/page_element.php");
$sekce = array_get_index_safe('section', $_GET);

if(!$users_class->is_logged()){
	// pokud se quest nekouka na intro, musi se koukat na info
	if ($sekce!="intro" && $sekce!="statistika"){
		$sekce = "info";
	}
}

if(isset($_REQUEST['id'])){
	$id_zeme = sqlsafe($_REQUEST['id']);
	$_SESSION['id_zeme'] = $id_zeme;
}
else{
	//pri prvnim prihlaseni v session nic neni
	if(isset($_SESSION['id_zeme'])){
		$id_zeme = sqlsafe($_SESSION['id_zeme']);
	}

}
	
switch($sekce) {
		case 'intro' :
				//prihlasenemu hraci se zobrazi centrala
				if($users_class->is_logged() && JeUzivatelVLize(NULL, $_SESSION['id_ligy'])){
					$page->redir('frame_akcni_menu.php?section=statistika&page=centrala');
				}
				else {
					require($DIR_LIB . "page_elements/FR_AM_intro.php");
				}
                break;
        case 'info_hrac' :
                require($DIR_LIB . "page_elements/FR_AM_info_hrac.php");
                break;        
        case 'info' :
                require($DIR_LIB . "page_elements/FR_AM_info.php");
                break;
        case 'statistika':
                require($DIR_LIB . "page_elements/FR_AM_statistika.php");
                break;
        case 'vystavba':
        		if (!JeZemHrace($_SESSION['id_zeme'], null, $_SESSION['id_ligy'])){
        			$page->redir('frame_akcni_menu.php?section=info&id='.$_SESSION['id_zeme'].'');
        		}
                require($DIR_LIB . "page_elements/FR_AM_vystavba.php");
                break;
        case 'obrana':
				if (!JeZemHrace($_SESSION['id_zeme'], null, $_SESSION['id_ligy'])){
        			$page->redir('frame_akcni_menu.php?section=info&id='.$_SESSION['id_zeme'].'');
        		}
                require($DIR_LIB . "page_elements/FR_AM_obrana.php");
                break;
        case 'utok':
				if (!JeZemHrace($_SESSION['id_zeme'], null, $_SESSION['id_ligy'])){
        			$page->redir('frame_akcni_menu.php?section=info&id='.$_SESSION['id_zeme'].'');
        		}
                require($DIR_LIB . "page_elements/FR_AM_utok.php");
                break;
/*        case 'seznam_utoku':
                require($DIR_LIB . "page_elements/FR_AM_seznam_utoku.php");
                break;*/
        case 'veleni':
                require($DIR_LIB . "page_elements/FR_AM_veleni.php");
                break;
        case 'letectvo':
                require($DIR_LIB . "page_elements/FR_AM_letectvo.php");
                break;
        default:
        		$page->redir('frame_akcni_menu.php?section=statistika&page=centrala');
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