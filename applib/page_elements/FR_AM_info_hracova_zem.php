<?php
	// INFO - STATISTIKA SEKTORU
	//global $DIR_LIB, $DIR_CONFIG, $db
	//menu hracovy zeme
	require_once($DIR_LIB . "page_elements/FR_AM_menu_hracova_zem.php");
  
  $menu_footer = $DIR_SKINS . "default/frame_akcni_menu/stat_sektoru_menu_foot.jpg";
  $bg = $DIR_SKINS . "default/frame_akcni_menu/zem/stat_sekt_bg.jpg";
  $text = '<div style="background: url('.$bg.') 0px 6px no-repeat; width: 277px; height: 700px;">';
  $text .= "\n";
	$text .= '<img src="'.$menu_footer.'" alt="maska menu" style="position: absolute; top: 24px; left: 0px;" /><br />';
	
	
	$text .= '<img src="./skins/default/frame_akcni_menu/zem/h_text1.png" id="h_text1"  />';
    $text .= '<img src="./skins/default/frame_akcni_menu/zem/h_pozadavek_presun.png" id="h_pozadavek_presun"  />';
  	
	$text .= '<img src="./skins/default/frame_akcni_menu/zem/h_helma.png" id="h_helma" title="Body vlivu" />';
  	$text .= '<img src="./skins/default/frame_akcni_menu/zem/h_okraj.png" id="h_okraj"  />';
  
  	$text .= '<img src="./skins/default/frame_akcni_menu/zem/h_infra.png" id="h_infra" title="Infrastruktura" />';
  	$text .= '<img src="./skins/default/frame_akcni_menu/zem/h_suroviny.png" id="h_suroviny" title="Zásoby" />';
  	$text .= '<img src="./skins/default/frame_akcni_menu/zem/h_palivo.png" id="h_palivo" title="Palivo" />';
  	$text .= '<img src="./skins/default/frame_akcni_menu/zem/h_bv_male.png" id="h_bv_male" title="Body vlivu" />';
	
  	//napoveda
  	$text .= '<a href="frame_mapa.php?section=napoveda&amp;page=takticky_presun" target="mapa" title="Co je to Taktický přesun ?" >
	<img src="'.$DIR_SKINS .'default/frame_akcni_menu/napoveda.png" alt="Co je to Taktický přesun ?" style="border:0px;position: absolute; top: 450px; left: 245px;" /></a>';
  
  	//napoveda
  	$text .= '<a href="frame_mapa.php?section=napoveda&amp;page=statistika_zeme" target="mapa" title="Význam ikon" >
	<img src="'.$DIR_SKINS .'default/frame_akcni_menu/napoveda.png" alt="Význam ikon" style="border:0px;position: absolute; top: 200px; left: 245px;" /></a>';
  
	
	//hodnoty surovin
	$text .= "<span class=\"nazev_zeme\" title=\"Název země\">${zeme['nazev']}</span>\n";
  $text .= "<span class=\"body_vlivu\" title=\"Body vlivu\">${zeme['body_vlivu']}</span>\n";
	$text .= "<div class=\"infrastr\" title=\"Infrastruktura\">${zeme['inf_now']}%</div>";

	//zda je postavena stavba zenijni utvar
	if (MaHracStavbuVZemi(21,$bezpecny_id,$_SESSION['id_ligy'])){
		if ($zeme['inf_now'] < 100){	
			$text .= "<div title=\"Cena opravy infrastrutury.\" id=\"cena_opravy\">
			<a href=\"frame_akcni_menu.php?section=info&amp;id=".$_SESSION['id_zeme']."&amp;action=opravit\" title=\"Dotovat opravu infrastruktury\">opravit</a> 
			<span class=\"all_suroviny\">5%</span> za <span class=\"all_suroviny\">" 
			. CenaOpravyInfrastruktury( $zeme['id_zeme'], $_SESSION['id_ligy']) . "</span> 
			zásob</div>";	
		}
		$text .= "<br />\n";
	}
	//hlaseni pokud nema zenijni brigadu
	else{
		$text .= "<div title=\"Cena opravy infrastrutury.\" id=\"cena_opravy\">
		<span class=\"chyba\">Není postavena<br /> Ženijní brigáda.</span>
		</div>";	
		$text .= "<br />\n";
	}
    

	$text .= "<div id=\"zasoby_kolo\" title=\"Suroviny\"><span class=\"all_suroviny\">" . SurovinyZaKolo( $bezpecny_id ) . "</span></div>\n";
	$text .= "<div id=\"palivo_kolo\" title=\"Palivo\"><span class=\"all_palivo\">" . PalivoZaKolo( $bezpecny_id ) . "</span></div>\n";
	$text .= "<div id=\"bvlivu_kolo\" title=\"Body vlivu\"><span class=\"all_body_vlivu\">" . BodyVlivuZaKolo( $bezpecny_id ) . "</span></div>\n"; 
	
	// BONUSY SEKTORU nebo CHYBY
	$text .= "<div id=\"bonusy_sektoru\" title=\"Bonusy sektoru\">\n";
	if( strlen( $chyba ) > 3 ){
		$text .= '<span class="error">' . $chyba . '</span>';
	}
	else{
		$text .= VratBonusyZeme($bezpecny_id);
	}
	$text .= "</div>\n";
	
	// VOLNE PRUCHODY
	//$text .= "<span id=\"zadat_pruchod\">Požádat o průchod sousední zem</span><br />";
	
		// pozadani o pruchod
		$text .= '<form action="" method="post" id="zadat_pruchod_frm">';
		$text .= "\n";
		$text .= '<select name="pruchody" size="1" id="hracova_zem_pruchody">';
		$text .= "\n";
		$text .= NaplnPruchody( $bezpecny_id, $bezpecny_id_ligy );
		$text .= '</select>';
		$pruchod_over_btn = $DIR_SKINS . "default/frame_akcni_menu/pozadat_over_btn.jpg";
		$text .= '<input type="submit" alt="Požádat" title="Požádat o průchod" name="OK" value="" id="pozadat_btn"/></form><br />';
    $text .= "<br />\n";	
	$text .= "<div id=\"zadosti\">"; 
	//"Žádosti o průchod:<br />"; 
	//cizi zadosti do zeme
		$query = "SELECT id_zeme_odkud,nazev, id_zeme_kam FROM in_game_pruchody as igp 
				JOIN zeme as z where 
				id_zeme_kam='$bezpecny_id' and id_ligy='$bezpecny_id_ligy' and
				igp.id_zeme_odkud=z.id and igp.platnost=0";
		$res = $db->Query( $query );
		while ($row = $db->GetFetchRow( $res )){
			if ($row[0] == $bezpecny_id){
				$id2 = $row[2];
			}
			else{
				$id2 = $row[0];
			}
			$text .= "Žádost o zajištění přesunu přes " .$row[1] . "
			<a href=\"frame_akcni_menu.php?section=info&amp;action=schval_pruchod&amp;id_do=$id2&amp;id=$bezpecny_id\">POVOLIT</a> 
			<a href=\"frame_akcni_menu.php?section=info&amp;action=del_pruchod&amp;id_do=$id2&amp;id=$bezpecny_id\">ZAMÍTNOUT</a><br />";
		}
	//me zadosti ze zeme	
	$query = "SELECT id_zeme_odkud,nazev, id_zeme_kam FROM in_game_pruchody as igp 
				JOIN zeme as z where 
				id_zeme_odkud='$bezpecny_id' and id_ligy='$bezpecny_id_ligy' and
				igp.id_zeme_kam=z.id and igp.platnost=0";
		$res = $db->Query( $query );
		while ($row = $db->GetFetchRow( $res )){
			if ($row[0] == $bezpecny_id){
				$id2 = $row[2];
			}
			else{
				$id2 = $row[0];
			}
			$text .= "Žádáme o zajištění přesunu přes ".$row[1] . "
			<a href=\"frame_akcni_menu.php?section=info&amp;action=del_pruchod&amp;id_do=$id2&amp;id=$bezpecny_id\">STÁHNOUT</a><br />";
		}	
	//	$text .= "</span><br />";
	//platne pruchody
//	$text .= "<span id=\"platne_pruchody\">Platné průchody přes země:<br />"; 
		$query = "SELECT id_zeme_odkud,nazev, id_zeme_kam FROM in_game_pruchody as igp 
				JOIN zeme as z where 
				id_ligy='$bezpecny_id_ligy' and
				(	(id_zeme_kam='$bezpecny_id' and igp.id_zeme_odkud=z.id)
					or
					(id_zeme_odkud='$bezpecny_id' and igp.id_zeme_kam=z.id)
					) 
				and igp.platnost=1";
		$res = $db->Query( $query );
		while ($row = $db->GetFetchRow( $res )){
			if ($row[0] == $bezpecny_id){
				$id2 = $row[2];
			}
			else{
				$id2 = $row[0];
			}
			$text .= "Taktický přesun přes " . $row[1] . " možný
			<a href=\"frame_akcni_menu.php?section=info&amp;action=del_pruchod&amp;id_do=$id2&amp;id=$bezpecny_id\">ZRUSIT</a><br />";
		}	
		
	$text .= "</div>\n";
	
	// VLIVY PUSOBICI NA SEKTOR
	$text .= "<div id=\"vlivy_hrac\">";
	$text .= VratVlivyNaZemi($bezpecny_id, $bezpecny_id_ligy);
	$text .= "</div>\n";
	
	$text .= "</div>\n";

    $form = new textElement($text, null);
    $page->add_element($form, 'obsah');	
?>
