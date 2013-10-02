<?php

	//funkce pro naplneni formularu
	require_once($DIR_CONFIG . "akce.php");
	
	//tooltips jednotky
	require_once($DIR_LIB . "page_elements/FR_AM_tooltips.php");
	
	//ZISKANI INFORMACI O ZEMI - SITUACE HNED PO PREPOCTU
	$query = "SELECT * FROM `zeme_vcera_view` where (id_zeme = '$bezpecny_id')
			and(id_ligy = '$bezpecny_id_ligy')";
	$res = $db->Query($query);
	if (! $zeme = $db->GetFetchAssoc($res)){
		$error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                        'DB_ERROR',
                        "Sekci FR_AM_info nebyla naleznena zem k dane lize a ID",null);
	}

	$bg = $DIR_SKINS . "default/frame_akcni_menu/zem/akcni_menu_cizi_bg.jpg";
  	$text = '<div style="background: url('.$bg.') 0px 0px no-repeat; width: 277px; height: 724px;">';
 	
  	$text .= '<img src="./skins/default/frame_akcni_menu/zem/n_text6.png" id="n_text6"  />';
  	$text .= '<img src="./skins/default/frame_akcni_menu/zem/n_text7.png" id="n_text7"  />';
  	$text .= '<img src="./skins/default/frame_akcni_menu/zem/n_text8.png" id="n_text8"  />';
  	
  //	$text .= '<img src="./skins/default/frame_akcni_menu/zem/dalekohled.png" id="dalekohled"  />';
  	$text .= '<img src="./skins/default/frame_akcni_menu/zem/infrastruktura.png" id="infrastruktura"  />';
  	$text .= '<img src="./skins/default/frame_akcni_menu/zem/helma.png" id="helma"  />';
  	$text .= '<img src="./skins/default/frame_akcni_menu/zem/okraj.png" id="okraj"  />';
  	  	
  	//vyber obrazku jednotek
  	if (isset($zeme['id_vlastnik'])){
  		$query = "SELECT `strana` FROM in_game_hrac where id_hrac='${zeme['id_vlastnik']}'";
  		$res2 = $db->Query($query);
		$str = $db->GetFetchRow($res2);
		if ($str[0]=="us"){
			$strana = "";
		}
		else{
			$strana = "sssr_";
		}
  	}
  	else{
  		$strana = "nazi_";
  	}  	
  	$text .= '<img src="./skins/default/frame_akcni_menu/zem/'.$strana.'pechota.png" id="pechota"  />';
  	$text .= '<img src="./skins/default/frame_akcni_menu/zem/'.$strana.'tanky.png" id="tanky"  />';
  	
  	$text .= '<img src="./skins/default/frame_akcni_menu/zem/'.$strana.'velky_symbol.png" id="velky_symbol"  />';
  	
  	
	$text .= "<div class=\"nazev_cizy_zeme\" title=\"Název země\">${zeme['nazev']}</div>\n";
  	$text .= "<div class=\"body_vlivu_cizi_zem\" title=\"Body vlivu\">${zeme['body_vlivu']}</div>\n";
	$text .= "<div class=\"infrastr_cizi_zem\" title=\"Infrastruktura\">${zeme['inf_now']}%</div>";
  
	$text .= "<div id=\"obrazek_hrace_cizi_zem\">\n";
	$text .= "</div>\n";
	
	$tooltip_general = "";
	
	if (isset($zeme['id_vlastnik'])){
		$text .= "<div id=\"info_strana\">\n";
		$query = "SELECT `login` FROM users_sys where id='${zeme['id_vlastnik']}'";
			$res = $db->Query($query);
			$login = $db->GetFetchRow($res);
		$text .= "Pod kontrolou:";
		$text .= '<a href="frame_akcni_menu.php?section=info_hrac&id_hrac='.$zeme['id_vlastnik'].'" >';
		$text .= "${login[0]}";
		$text .= '</a>';
		//test zda zobrazit ODESLAT POSTU
		if($users_class->is_logged()){
			$text .= '<br />
			  	<a href="posta.php?section=2&amp;hrac='.$login[0].'" target="_blank"  >
			  	<img src="./skins/default/frame_akcni_menu/zem/napis_postu_male.png" id="napis_postu_male" title="Napsat zprávu" alt="Napsat zprávu"  />
			  	</a>';
			/*$text .= " <a href=\"posta.php?section=2
						&amp;hrac=${login[0]}\" target=\"_blank\">Napsat</a><br />\n";*/			
		}
		else{
			$text .= "<br />\n";
		}
		
		
		$query = "SELECT `strana`,`id_aliance`, `id_general` FROM `in_game_hrac` where id_hrac='".$zeme['id_vlastnik']."'";
		$res = $db->Query($query);
		$stral = $db->GetFetchAssoc($res);

		$tooltip_general = $stral['id_general'];
		
		$text .= "Strana:";
		 if ($stral['strana']=='us'){
		 	 $text .= " Spojenci";
		 }
		 else{
		 	$text .= " Rudá armáda";
		 }
		 $text .= "<br />\n";
		if (isset($stral['aliance'])){
			$text .= "" . $stral['aliance'] ."<br />\n";
		}
	}
	else{
		$text .= "<div id=\"info_strana_neutral\">\n";
		$text .= "V oblasti se pohybují neorganizované skupiny německých vojáků s 
				odhadovanou obranou: <strong>".
		$zeme['sila_neutralka']."</strong>";
		//round((1 + mt_rand(5,10)/100) * VypocitejObranuZeme($bezpecny_id, $bezpecny_id_ligy))."</strong>";
		//id general neutralky je 1
		$tooltip_general = 1;
	}
	$text .= "</div>\n";
	
	// HLASENI O JEDNOTKACH V OBLASTI
	$text .= "<div id=\"info_jednotky\" >
				Podle hlášení tvé rozvědky nejsou tyto informace 100% spolehlivé.
			</div>";
	
	$text .= "<div id=\"pechota_cizi_zem\" onmouseover=\"stm(Text[1],Style[1])\" onmouseout=\"htm()\" ><div id=\"inside_text\">${zeme['pechota']}</div></div>";
	$text .= "<div id=\"tanky_cizi_zem\" onmouseover=\"stm(Text[2],Style[1])\" onmouseout=\"htm()\" ><div id=\"inside_text\">${zeme['tanky']}</div></div>";
				 	
	// BONUSY SEKTORU nebo CHYBOVOU HLASKU
	$text .= "<div id=\"bonusy_sektoru_cizi_zem\" >\n";
	if( strlen( $chyba ) > 3 ){
		$text .= '<span class="error">' . $chyba . '</span>';
	}
	else{
		$text .= VratBonusyZeme($bezpecny_id);
	}
	$text .= "</div>\n";
	// VLIVY PUSOBICI NA SEKTOR
	$text .= "<div id=\"vlivy_cizi_zem\">";
	$text .= VratVlivyNaZemi($bezpecny_id, $bezpecny_id_ligy);
	$text .= "</div>\n";
	
	//PODPORA Z DOMOVA
	$text .= '<img src="./skins/default/frame_akcni_menu/zem/n_text1.png" id="n_text1"  />';
	
	//test zda muzu zobrazit
	$error = ZobrazitFormulareSPodporou( $bezpecny_id_ligy );
	if ($error=='ano'){
		
		$text .= '<img src="./skins/default/frame_akcni_menu/zem/n_text2.png" id="n_text2"  />';
	
		$text .= "<form method=\"post\">\n";
		
		$text .= '<select id="podpora_neutralka" name="akce" size="1">';
		$text .= NaplnPodporaZDomova( $users_class->user_id() );
		$text .= '</select>';
		
		$text .= '<br />';
		
		$text .= '<input id="podpora_naplanovani_utoku_neutralka" type="submit" name="rozkaz_podpora" value="" />
				  </form>';
	}
	else{
		$text .= '<div class="info_cizi_podpora_chyba">
					'.$error.'
			  </div>';
	}
	//LETECKE AKCE
	$text .= '<img src="./skins/default/frame_akcni_menu/zem/n_text3.png" id="n_text3"  />';
	//test zda je muzu zobrazit
	$error = ZobrazitFormulareSLetectvem( $_SESSION['id_ligy'] );
	if ($error=='ano'){
			//$text .= '<img src="./skins/default/frame_akcni_menu/zem/n_text4.png" id="n_text4"  />';
			$text .= '<img src="./skins/default/frame_akcni_menu/zem/n_text5.png" id="n_text5"  />';
			$text .= '<form method="post">';
	
			$text .= '<select id="letectvo_typy_utoku_neutralka" name="akce" size="1">';
			$text .= NaplnLeteckeTypyUtoku();
			$text .= '</select>';
			
			$text .= '<br />';
			
			/*$text .= '<select id="letectvo_sila_neutralka" name="sila" size="1">';
			$text .= NaplnSilyLeteckychUtoku( $users_class->user_id() );
			$text .= '</select>';*/
			
			$text .= '<br />';
			$text .= '<input id="letectvo_naplanovani_utoku_neutralka" type="submit" name="rozkaz_letectvo" value="" />
					  </form>';
	}
	else{
		$text .= '<div class="info_cizi_letectvo_chyba">
					'.$error.'
			  </div>';
	}
	
    $form = new textElement($text);
    $page->add_element($form, 'obsah');

    $form = new textElement(TooltipJednotkyCiziZem($tooltip_general), null);
    $page->add_element($form, 'tooltip');
?>