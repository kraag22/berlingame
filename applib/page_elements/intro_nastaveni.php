<?php if (!defined('BoB')) exit();
	require_once($DIR_CONFIG . "formulare.php");
	require_once($DIR_CONFIG . "konstanty.php");
	require_once($DIR_CONFIG . "lib.php");
	global $page, $users_class;
	
	if(isset($_REQUEST['odhlaseni'])){
		if ($_REQUEST['odhlaseni']=='ano'){
			OdhlasHraceZeScenare();
			$page->redir( $DIRECTORY . "intro.php" );
		}
	}
	
	if (isset($_POST['heslo'])){
			$return = ZpracujZmenuHesla();
	}
	
	$id_ligy = JeUzivatelVLize();

	$text = "";
	
	$text .= "<div class=\"horni_text\">";
	// HORNI TEXT
	$text .= 'Osobní nastavení'; 
	$text .= '</div>'; 
	
	if(isset($return)){
		$text .= '<span class="oznameni">'.$return.'</span>';
	}
	
	$text .= "<div class=\"dolni_text\">";
	if ( $id_ligy ){
		$text .= "<a href=\"".$DIRECTORY."intro.php?section=prihlaseni_symboly\">
			Změnit symbol</a> <br />";
		$text .= "<a href=\"".$DIRECTORY."intro.php?section=nastaveni&amp;odhlaseni=ano\"
		onclick=\"javascript:return confirm('Opravdu se chcete odhlásit?.')\">
			Odhlásit ze scénáře.</a> Tato volba Vás odhlásí z rozehraného scénáře.<br />";
	}
	
	//zmena hesla
	$text .= '<a href="#" onclick=\'$("#zmena_hesla").show();\'>Změnit heslo</a><br/>';
	$text .= FormularZmenaHesla();
	
	$text .= '<br />';
	$text .= '<br />';
	$text .= 'Pokud chcete pomoci s rozšířením berlingame, tak zkuste sehnat nové hráče. Pokud je
			přivedete přes následující odkaz - v budoucnu vás čeká příjemná odměna.<br />';
	$text .= '<strong>www.berlingame.cz/index.php?'.$users_class->reference_vygeneruj().'</strong><br />';
	
	$text .= '<br /><br /><br />
	Na tomto odkazu naleznete <a href="http://www.berlingame.cz/include/export.xml" target="_blank"> 
	XML export</a> veřejně dostupných dat. Aktualizují se každý přepočet
	a umožní vám snadný import do jakéhokoliv skriptu.';
	
	$text .= '</div>';
	
	
	$form = new textElement($text);
	$page->add_element($form, 'obsah');

?>