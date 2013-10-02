<?php
	//menu podpora z domova
	//require_once($DIR_LIB . "page_elements/FR_AM_menu_velitelstvi.php");
	
	//funkce pro zpracovani formularu
	require_once($DIR_CONFIG . "formulare.php");
	
	//skript na oznaceni/odznaceni checkboxu
	$pole = Array();
 	$pole['fileName'] = $DIR_SCRIPTS . 'checkboxy.js';
	$page->add_script($pole);
	
	$bg = $DIR_SKINS . "default/frame_akcni_menu/veleni/veleni_vyber_bg.jpg";
	  	$text = '<div style="background: url('.$bg.') 0px 0px no-repeat; width: 276px; height:723px;">';
  	
	  	$text .= '<img src="./skins/default/frame_akcni_menu/veleni/podpora_text2.png" id="text5"  />';
  		$text .= '<img src="./skins/default/frame_akcni_menu/veleni/podpora_text1.png" id="text6"  />';
	
	//test zda se odeslal formular planovani akce
	if(isset($_REQUEST['akce']) && isset($_REQUEST['cil'])){
		$akce = sqlsafe($_REQUEST['akce']);
		$cil = sqlsafe($_REQUEST['cil']);
		$_SESSION['akce'] = $akce;
		$_SESSION['cil'] = $cil;

		$id_liga_autor = JeUzivatelVLize();
		$id_liga_cil = JeUzivatelVLize( $cil );
		
		//jen pokud jsou ve stejne lize
		if ( $id_liga_autor == $id_liga_cil ){

			$res = $db->Query("SELECT id_zeme, nazev FROM zeme_view 
								WHERE id_vlastnik='$cil' ORDER BY nazev");
			
			$text .= '<form method="post" action="frame_akcni_menu.php?section=veleni">';
			//naplneni zemi
			$i=0;
			$text .= "<div class=\"veleni_zeme\">\n";
			while ($row = $db->GetFetchRow( $res )){
				$text .= "<input type='checkbox' name='$i' value='${row[0]}' checked /> ";
				$text .= $row[1] . "<br />";
				$i++;
			}
			
			$text .= '<input type="text" name="last_id" value="'. $i .'" style="visibility: hidden" />';
			
			$text .= '<div id="podpora_vyber_zemi_button_div">
				<input id="podpora_vyber_zemi_button" type="submit" value="" name="vyber_zemi"/>
				</div>';
			$text .= '<br /><a href="#" onClick="OznacVse();"> Označ vše</a> / 
					<a href="#" onClick="ZrusVse();">Zruš vše</a>';
			
			$text .= '</div></form>';	
			
		}
			
	}
	
	$form = new textElement($text, null);
    $page->add_element($form, 'obsah');	
	
?>