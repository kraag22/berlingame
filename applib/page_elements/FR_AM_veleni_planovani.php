<?php
	//menu podpora z domova
	//require_once($DIR_LIB . "page_elements/FR_AM_menu_velitelstvi.php");
	
	//funkce pro naplneni formularu
	require_once($DIR_CONFIG . "akce.php");
	
	//funkce pro zpracovani formularu
	require_once($DIR_CONFIG . "formulare.php");
	
	require_once($DIR_CONFIG . "stavby.php");
	
	//test zda je postaveno vrchni velitelstvi
	$chyba = '';
	if(MaHracStavbu(15)){
		//zpracovani formulare
		$chyba = ZpracujNaplanovanouPodporu();
	}
	if(isset($_REQUEST['akce']) && $_REQUEST['akce']!=-1){
		//predani parametru a presmerovani na stranku s vyberem zemi
		$page->redir("frame_akcni_menu.php?section=veleni&page=vyber_zemi&akce=".$_REQUEST['akce']."&cil=".$_REQUEST['cil']);
	}
	
	$bg = $DIR_SKINS . "default/frame_akcni_menu/veleni/veleni_zadost.jpg";
  	$text = '<div style="background: url('.$bg.') 0px 0px no-repeat; width: 277px; height:180px;">';
	
  	//NADPISY
  	$text .= '<img src="./skins/default/frame_akcni_menu/veleni/text_zadost.png" id="text1"  />';
  	$text .= '<img src="./skins/default/frame_akcni_menu/veleni/text_vel.png" id="text2"  />';
  	
  	//napoveda
  	$text .= '<a href="frame_mapa.php?section=napoveda&amp;page=veleni" target="mapa" title="Jak na podporu?" >
	<img src="'.$DIR_SKINS .'default/frame_akcni_menu/napoveda.png" alt="Jak na podporu?" style="border:0px;position: absolute; top: 7px; left: 250px;" /></a>';
  
  	$text .= '<a href="frame_mapa.php?section=napoveda&amp;page=veleni_akce" target="mapa" title="Přehled typů podpory" >
	<img src="'.$DIR_SKINS .'default/frame_akcni_menu/napoveda.png" alt="Přehled typů podpory" style="border:0px;position: absolute; top: 35px; left: 105px;" /></a>';
  

	//test zda je postaveno vrchni velitelstvi
	$vysledek = ZobrazitFormulareSPodporou( $_SESSION['id_ligy'] );
	if($vysledek=='ano'){
		//nadpisy
		$text .= '<img src="./skins/default/frame_akcni_menu/veleni/text_podpurne_akce.png" id="text3"  />';
  		$text .= '<img src="./skins/default/frame_akcni_menu/veleni/text_cilovy_protivnik.png" id="text4"  />';
		
		$text .= "<form method=\"post\">\n";
	
		$text .= "<div style=\"position:absolute;top:51px;left:15px;\">
		<select name=\"akce\" size=\"1\" id=\"podpora_akce\">\n";
		$text .= NaplnPodporaZDomova( $users_class->user_id() );
		$text .= "\n</select>\n</div>\n";
		
		
		$text .= "<div style=\"position:absolute;top:96px;left:15px;\">
		<select name=\"cil\" size=\"1\" id=\"podpora_cil\">\n";
		$text .= NaplnCiloveHrace( $_SESSION['id_ligy']);
		$text .= "\n</select></div>\n";
		$text .= '<input id="podpora_naplanovani_utoku" type="submit" value="" name="rozkaz"/>';
		$text .= '</form>';	
	}
	else{
		$text .= '<div class="veleni_chyba">
				'.$vysledek.'
			  </div>';
	}
	
	$text .= "</div>\n";
	
	// SEZNAM AKCI
	$text .= "<div id=\"veleni_akce_menu\">\n";
	$text .= "</div>\n";
	
	$text .= "<div id=\"veleni_akce_obsah\">\n";
	
	//chyba
//	$text .= "<div id=\"veleni_obrazek\">";
	if( strlen($chyba) > 3 ){
		$text .= "<div id=\"veleni_obrazek_chyba\">$chyba</div>";
	}
	//$text .= "</div>\n";
		
	$id_hrac = $users_class->user_id();
	$query = "SELECT zv.nazev, pzd.nazev
			FROM in_game_podpora as igp JOIN 
			zeme_view as zv JOIN
			podpora_z_domova as pzd 
			ON pzd.id=igp.id_typ_podpory and igp.id_zeme=zv.id_zeme 
			and zv.id_ligy='".$_SESSION['id_ligy']."' 
			WHERE
			igp.id_autor='$id_hrac'
			ORDER BY zv.nazev";
	
	$res = $db->Query( $query );
	while ($row = $db->GetFetchRow( $res )){
	$text .= $row[0]." - ".$row[1]."<br />";	
	}
	$text .= "</div>\n";
	
	$text .= "<div id=\"veleni_akce_footer\">\n";
	$text .= "</div>\n";
	
	
	
	$form = new textElement($text, null);
    $page->add_element($form, 'obsah');	
    
    //aktualizace menu - vzdy kvuli presmerovani z neutralek
    $printer = new textPrinter();
	$form = new textElement("OnLoad=\"top.menu.document.location.href='frame_menu.php';\"", $printer);
	$page->add_element($form, 'refresh');
?>