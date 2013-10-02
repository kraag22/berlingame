<?php
	//menu letectva
	require_once($DIR_LIB . "page_elements/FR_AM_menu_letectvo.php");
	
	//funkce pro zpracovani formularu
	require_once($DIR_CONFIG . "formulare.php");
	
	//skript na oznaceni/odznaceni checkboxu
	$pole = Array();
 	$pole['fileName'] = $DIR_SCRIPTS . 'checkboxy.js';
	$page->add_script($pole);
	
  $menu_footer = $DIR_SKINS . "default/frame_akcni_menu/poslat_utok_menu_foot.jpg";
  $bg = $DIR_SKINS . "default/frame_akcni_menu/letectvo_vyber_zemi_bg.jpg";
  $text = '<div style="background: url('.$bg.') 0px 4px no-repeat; width: 277px; height:700px;">';
  $text .= "\n";
	$text .= '<img src="'.$menu_footer.'" alt="maska menu" style="position: absolute; top: 24px; left: 0px;" /><br />';
	
  $text .= "\n";
  $text .= "<div id=\"letectvo_vyber_zemi\">\n";
  $text .= '<img src="./skins/default/frame_akcni_menu/letectvo_akce_text.png" id="text7"  />';
	
	//test zda se odeslal formular planovani akce
	if(isset($_REQUEST['akce'])&&isset($_REQUEST['cil'])){
		$akce = sqlsafe($_REQUEST['akce']);
		$cil = sqlsafe($_REQUEST['cil']);
		//$sila = sqlsafe($_REQUEST['sila']);
		$_SESSION['akce'] = $akce;
		$_SESSION['cil'] = $cil;
		//$_SESSION['sila'] = $sila;

		$res = $db->Query("SELECT id_zeme, nazev FROM zeme_view 
							WHERE id_vlastnik='$cil' ORDER BY nazev");
		
		$text .= '<form method="post" name="seznam" action="frame_akcni_menu.php?section=letectvo&page=poslat_utok">';
		//naplneni zemi
		$i=0;
		while ($row = $db->GetFetchRow( $res )){
			$text .= "<input type='checkbox' name='$i' value='${row[0]}' checked /> ";
			$text .= $row[1] . "<br />";
			$i++;
		}
		$text .= '<input type="text" name="last_id" value="'. $i .'" style="visibility: hidden" />';
		$text .= '<div style="margin-left:90px;">';
		$text .= '<input id="letectvo_naplanovani_utoku2" type="submit" name="vyber_zemi" value="" />';
		$text .= '	  </div>';
		$text .= '<br /><a href="#" onClick="OznacVse();"> Označ vše</a> / 
					<a href="#" onClick="ZrusVse();">Zruš vše</a>';
		$text .= '  </form>';	
			
	}
		
	
	$text .= "</div>\n";
	$text .= "</div>\n";
	
	$form = new textElement($text, null);
    $page->add_element($form, 'obsah');	
?>