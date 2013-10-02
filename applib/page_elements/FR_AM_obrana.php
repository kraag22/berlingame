<?php
	//soubor s funkcemi pro vojsko
	require_once($DIR_CONFIG . "boj.php");
	
	require_once($DIR_CONFIG . "lib.php");
	
	//soubor s funkcemi pro zpracovani formularu
	require_once($DIR_CONFIG . "formulare.php");
	
	//menu hracovy zeme
	require_once($DIR_LIB . "page_elements/FR_AM_menu_hracova_zem.php");
	
	//tooltips jednotky
	require_once($DIR_LIB . "page_elements/FR_AM_tooltips.php");

	$error_p = "";
	$error_t = "";
	
	$error_p = ZpracujPovolaniOdvolaniPechoty($id_zeme);
	
	$error_t = ZpracujPovolaniOdvolaniTanku($id_zeme);
	
	$menu_footer = $DIR_SKINS . "default/frame_akcni_menu/obrana/jedn_obrana_menu_foot.jpg";
  $bg = $DIR_SKINS . "default/frame_akcni_menu/obrana/jedn_obrana_bg.jpg";
  $text = '<div style="background: url('.$bg.') 0px 4px no-repeat; width: 277px; height: 696px;">';
  $text .= "\n";
	$text .= '<img src="'.$menu_footer.'" alt="maska menu" style="position: absolute; top: 24px; left: 0px;" /><br />';
	
	//napoveda
  	$text .= '<a href="frame_mapa.php?section=napoveda&amp;page=obrana" target="mapa" title="Jak na jednotky ?" >
	<img src="'.$DIR_SKINS .'default/frame_akcni_menu/napoveda.png" alt="Jak na jednotky ?" style="border:0px;position: absolute; top: 232px; left: 245px;" /></a>';
  
	
	
	$text .= "\n";
	
	//vyber typu obrazku
	switch(VratGeneralaHrace()){
		case 2:
			//spojenci
			$id_pech = "obrazek_pechota_us";
			$id_tan = "obrazek_tanky_us";
			break;
		case 3:
			//soveti
			$id_pech = "obrazek_pechota_sssr";
			$id_tan = "obrazek_tanky_sssr";
			break;
		
	}
	
	//tooltipy pres velke obrazky
	$text .= "<div id=\"$id_pech\" onmouseover=\"stm(Text[1],Style[1])\" onmouseout=\"htm()\">";
		//ZPRACOVANI CHYB
		if ($error_p != ""){
			$text .= '<div id="chyba">'.$error_p.'</div>';
		}
	$text .= "</div>\n";
	$text .= "<div id=\"$id_tan\" onmouseover=\"stm(Text[2],Style[1])\" onmouseout=\"htm()\">";
		//ZPRACOVANI CHYB
		if ($error_t != ""){
			$text .= '<div id="chyba">'.$error_t.'</div>';
		}
	$text .= "</div>\n";
	
	//PECHOTA	
	$cena = VratAtributyJednotek("pechota", null, $id_zeme);
	$text .= "<div id=\"pech_povolavaci_cena\"><span class=\"all_suroviny\">". $cena['suroviny'] . "</span></div>\n";
	$text .= "<div id=\"pech_zold_sur_tah\"><span class=\"all_suroviny\">". $cena['zold_suroviny'] . "</span></div>\n";
	$text .= "<div id=\"pech_zold_pal_tah\"><span class=\"all_palivo\">". $cena['zold_palivo'] . "</span></div>\n";
	$text .= "<div id=\"pechota_v_sektoru\">" . PocetPechotyVSektoru( $id_zeme ) . "</div>\n";
	
	// Staveni pechoty
		$text .= '<form action="" method="post">';
		$text .= '<input type="text" name="p_pocet" id="obrana_pechota_pocet" size="1" value="0" />';

	// TANKY
	$cena = VratAtributyJednotek("tanky", null, $id_zeme);
	$text .= "<div id=\"tank_povolavaci_cena\"><span class=\"all_suroviny\">". $cena['suroviny'] . "</span></div><br />";
	$text .= "<div id=\"tank_zold_sur_tah\"><span class=\"all_suroviny\">". $cena['zold_suroviny'] . "</span></div><br />";
	$text .= "<div id=\"tank_zold_pal_tah\"><span class=\"all_palivo\">". $cena['zold_palivo'] . "</span></div><br />";
	$text .= "<div id=\"tanky_v_sektoru\">" . PocetTankuVSektoru( $id_zeme ) . "</div><br />";
	
	// maximum povolatelnych jednotek
	$text .= "<div id=\"maximum_jednotek\">Lze povolat <span id=\"max_jednotek\">" 
			. MaxJednotekCoLzePostavit( $id_zeme ) . "</span> jednotek</div>";
	
	// Staveni tanku
	$text .= '<input type="text" name="t_pocet" id="obrana_tanky_pocet" size="1" value="0" />';
	$povolat_btn = $DIR_SKINS . "default/frame_akcni_menu/povolat_btn.jpg";
	$odvolat_btn = $DIR_SKINS . "default/frame_akcni_menu/odvolat_btn.jpg";

	$text .= '<input type="submit" name="akce_povolat" id="obrana_povolat_btn" value="" /><br />
					<input type="submit" name="akce_odvolat" id="obrana_odvolat_btn" value="" />
          </form>';
	
	// OBRANA
	$text .= "<span id=\"pozemni_obrana\">";
	$pom = VypocitejObranuZeme($id_zeme, $_SESSION['id_ligy'],true);
	$text .= $pom['text'];
	$text .= "</span>";
	
	//PVO obrana
	$query = "SELECT pvo_stanice FROM `in_game_zeme`
	 where (id_zeme='$id_zeme')and
	 (id_ligy='".sqlsafe($_SESSION['id_ligy'])."')";
	
	$res = $db->Query( $query );
	$stan_bon = $db->GetFetchRow($res);
	$sila = $stan_bon[0] * $CONST["PVO_STANICE_BONUS"];
	
	//je postavena PVO brigada?
	if (MaHracStavbuVZemi(24,$id_zeme,$_SESSION['id_ligy'])){
		$sila *= $CONST["STAVBY_BRIGADA"];
	}
	
	//zda je postavena stavba vojenska policie
		if (MaHracStavbuVZemi(18,$id_zeme,$_SESSION['id_ligy'])){
			$sila += $CONST["STAVBY_FLAK_BONUS"];
		}
	$text .= "<span id=\"protivzdusna_obrana\">";
	$text .= "Obrana: $sila";	
	$text .= "</span>";
	
	// VOJENSKA POLICIE
	$query = "SELECT vojenska_policie FROM `in_game_zeme`
	 where (id_zeme='$id_zeme')and
	 (id_ligy='".sqlsafe($_SESSION['id_ligy'])."')";
	
	$res = $db->Query( $query );
	$policie = $db->GetFetchRow($res);
	$sila = $policie[0] * $CONST["POLICEJNI_STANICE_BONUS"];
	
	//zda je postavena stavba vojenska policie
		if (MaHracStavbuVZemi(20,$id_zeme,$_SESSION['id_ligy'])){
			$sila += $CONST["STAVBY_VOJENSKA_POLICIE_BONUS"];
		}
	
	$text .= "<span id=\"sila_kontrarozvedky\">${policie[0]} Policejních stanovišť =
			$sila% síla </span></div>";
  				
			
    $form = new textElement($text, null);
    $page->add_element($form, 'obsah');	
    
    $form = new textElement(TooltipJednotky($id_zeme), null);
    $page->add_element($form, 'tooltip');
?>
