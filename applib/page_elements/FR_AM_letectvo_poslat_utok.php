<?php
	//menu letectva
	require_once($DIR_LIB . "page_elements/FR_AM_menu_letectvo.php");
	
	//funkce pro naplneni formularu
	require_once($DIR_CONFIG . "akce.php");
	
	require_once($DIR_CONFIG . "lib.php");
	
	if(isset($_REQUEST['rozkaz'])){
		//predani parametru a presmerovani na stranku s vyberem zemi
		$page->redir("frame_akcni_menu.php?section=letectvo&page=vyber_zemi&akce="
		.$_REQUEST['akce']."&cil=".$_REQUEST['cil']."");
	}
	
	//zpracovani formulare
	$err = ZpracujNaplanovanouLeteckouAkci();
	
	$menu_footer = $DIR_SKINS . "default/frame_akcni_menu/poslat_utok_menu_foot.jpg";
	$bg = $DIR_SKINS . "default/frame_akcni_menu/letectvo_poslat_utok_bg.jpg";
	$text = '<div style="background: url('.$bg.') 0px 4px no-repeat; width: 277px; height:400px;">';
	$text .= "\n";
	$text .= '<img src="'.$menu_footer.'" alt="maska menu" style="position: absolute; top: 24px; left: 0px;" /><br />';
	
	$text .= '<a href="frame_mapa.php?section=napoveda&amp;page=letectvo_typ" target="mapa" title="Přehled typů útoků" >
	<img src="'.$DIR_SKINS .'default/frame_akcni_menu/napoveda.png" alt="Přehled typů útoků" style="border:0px;position: absolute; top: 168px; left: 90px;" /></a>';
  
	$text .= "\n";
	
	//vyber typu obrazku
	switch(VratGeneralaHrace()){
		case 2:
			//spojenci
			$let = "obrazek_let1_us";
			break;
		case 3:
			//soveti
			$let = "obrazek_let1_sssr";
			break;
		
	}
	$text .= "<div id=\"$let\">";
	if(isset($err)){
		$text .= "<div id=\"let_posl_chyba\">$err</div>";
	}
	$text .= "</div>";
	
    $text .= "<div id=\"letectvo_utok\">\n";
	$text .= '<form method="post">';
	
	$menu_popisek_palivo = $DIR_SKINS . "default/frame_akcni_menu/letectvo/zas-palivo.png";
	$text .= '<img src="'.$menu_popisek_palivo.'" alt="" style="position: absolute; top: 168px; left: 160px;" /><br />';
	
	
	$text .= '<select id="letectvo_typy_utoku" name="akce" size="1">';
	$text .= NaplnLeteckeTypyUtoku();
	$text .= '</select>';
	
	$text .= '<br />';
	
	$text .= '<select id="letectvo_cil" name="cil" size="1">';
	$text .= NaplnCiloveHrace( $_SESSION['id_ligy']);
	$text .= '</select>';
	
	$text .= '<br />';
	
	$text .= '<br />';
	
	//TESTY
	$error = ZobrazitFormulareSLetectvem( $_SESSION['id_ligy'] );
	if ($error=='ano'){
		$text .= '<input id="letectvo_naplanovani_utoku" type="submit" name="rozkaz" value="" />';
	}
	else{
		$text .= '<div class="letectvo_chyba">
					'.$error.'
			  </div>';
	}
	
	$text .= ' </form><br />';
	$text .= "</div>\n";
		
	//LETADLA NA CESTE K CILUM
	$text .= "<div id=\"letadla_na_ceste_menu\">\n";
	$text .= "</div>\n";
	
	$text .= "<div id=\"letadla_na_ceste\">\n";
	
	$id_hrac = $users_class->user_id();
	$query = "SELECT zv.nazev, la.nazev, igl.sila
			FROM in_game_letecke_akce as igl JOIN 
			zeme_view as zv JOIN
			letecke_akce as la 
			ON la.id=igl.id_typ_letecke_akce and igl.id_zeme=zv.id_zeme
			and zv.id_ligy='".$_SESSION['id_ligy']."'
			WHERE
			igl.id_autor='$id_hrac'
			ORDER BY zv.nazev";
	
	$res = $db->Query( $query );
	while ($row = $db->GetFetchRow( $res )){
	$text .= $row[0]." : ".$row[1]."<br />";	
	}
	
	$text .= "</div>\n";
	
	$text .= "<div id=\"letadla_na_ceste_footer\">\n";
	$text .= "</div>\n";
	
	$text .= "</div>\n";

	$form = new textElement($text, null);
    $page->add_element($form, 'obsah');

    //aktualizace menu - vzdy kvuli presmerovani z neutralek
    $printer = new textPrinter();
	$form = new textElement("OnLoad=\"top.menu.document.location.href='frame_menu.php';\"", $printer);
	$page->add_element($form, 'refresh');
?>
