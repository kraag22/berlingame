<?php
	//menu letectva
	require_once($DIR_LIB . "page_elements/FR_AM_menu_letectvo.php");
	
	//soubor s funkcemi pro vojsko
	require_once($DIR_CONFIG . "boj.php");
	
	//soubor s funkcemi pro ekonomiku
	require_once($DIR_CONFIG . "ekonomika.php");
	
	//zpracovani formularu
	require_once($DIR_CONFIG . "formulare.php");
	
	require_once($DIR_CONFIG . "lib.php");
	
	$menu_footer = $DIR_SKINS . "default/frame_akcni_menu/letectvo_menu_foot.jpg";
  $bg = $DIR_SKINS . "default/frame_akcni_menu/letectvo_bg.jpg";
  $text = '<div style="background: url('.$bg.') 0px 4px no-repeat; width: 277px; height:700px;">';
  $text .= "\n";
	$text .= '<img src="'.$menu_footer.'" alt="maska menu" style="position: absolute; top: 24px; left: 0px;" /><br />';
	
	$text .= '<a href="frame_mapa.php?section=napoveda&amp;page=letectvo" target="mapa" title="Jak na letectvo?" >
	<img src="'.$DIR_SKINS .'default/frame_akcni_menu/napoveda.png" alt="Jak na letectvo?" style="border:0px;position: absolute; top: 7px; left: 250px;" /></a>';
  
	$text .= "\n";
	
	//zpracovani formulare
	$err = ZpracujNavyseniRozpusteniLetectva();
	
	//vyber typu obrazku
	switch(VratGeneralaHrace()){
		case 2:
			//spojenci
			$let = "obrazek_let2_us";
			break;
		case 3:
			//soveti
			$let = "obrazek_let2_sssr";
			break;
		
	}
	$text .= "<div id=\"$let\">";
	if(isset($err)){
		$text .= "<div id=\"let_bud_chyba\">$err</div>";
	}
	$text .= "</div>";
	

	$text .= "<div id=\"letectvo_budovani\">\n";
	// letecka sila
	$id_user = $users_class->user_id();
	$res = $db->Query("SELECT letecka_sila FROM in_game_hrac WHERE id_hrac='$id_user'");
	$row = $db->GetFetchRow( $res );
	$letecka_sila = $row[0];
	$text .= "<div id=\"letectvo_letecka_sila\">". $letecka_sila . "</div>\n";
	
	// staveni letadel

	$text .= '<form action="" method="post">';
	$text .= "<div id=\"letectvo_cena_sily\"><span class=\"all_body_vlivu\">". CenaBoduLeteckeSily($letecka_sila) . "</span></div>\n";
	$text .= '<input id="letectvo_navyseni" type="text" name="pocet" size="1">';
	$text .= '<input id="letectvo_navyseni_povolat" type="submit" name="navyseni" value=" "><br />
				</form>';
	//<input type="submit" name="rozpusteni" value=" ">
	
	$text .= "<br />";
	$text .= "<div id=\"letectvo_zprava\">";
	$text .= "Rozsah síly akcí : ".round($letecka_sila / $CONST["MINIMUM_LS"])." - ". $letecka_sila . "<br />";
	$pocet_letist = PocetLetist( $id_user );
	$pocet_hangaru = PocetHangaru( $id_user );
	$text .= "Počet letišť : " . $pocet_letist . "<br />";
	$text .= "Počet hangárů : " . $pocet_hangaru . "<br />";
	$text .= "Náklady na provoz letišť : ". ($pocet_letist * $CONST["PROVOZ_LETISTE"]) . "<br />";
	$text .= "Naplánováno "
			. AktualniPocetLeteckychUtoku( $id_user ) . "
akcí z dnešního limitu " 
			. MaximalniPocetLeteckychUtoku( $id_user ) ."<br />";

	//maximul LS podle poctu letist
	$maxLS = MaximalniLeteckaSila( $id_user );
	$text .= '<span ';
	if ($maxLS <= $letecka_sila){
		$text .= 'style="color:red;"';
	}
	$text .= '>';
	$text .= "Maximální možná letecká síla : $maxLS</span><br />";		
	$text .= "</div>\n";
	
  $text .= "</div>\n";
	$text .= "</div>\n";
	$form = new textElement($text, null);
    $page->add_element($form, 'obsah');	
?>
