<?php
	//menu hracovy zeme
	require_once($DIR_LIB . "page_elements/FR_AM_menu_hracova_zem.php");
	
	//soubor s funkcemi pro vojsko
	require_once($DIR_CONFIG . "boj.php");
	
	//soubor s funkcemi pro zpracovani formularu
	require_once($DIR_CONFIG . "formulare.php");
	
	//tooltips jednotky
	require_once($DIR_LIB . "page_elements/FR_AM_tooltips.php");
	
	require_once($DIR_CONFIG . "lib.php");
	
	if(isset($_REQUEST['id'])){
		$id_zeme = sqlsafe($_REQUEST['id']);
		$_SESSION['id_zeme'] = $id_zeme;
	}
	else{
		$id_zeme = sqlsafe($_SESSION['id_zeme']);	
	}
	
	//zpracovavani kliknuti na sipku na mape
	if(isset($_REQUEST['zeme_kam'])){
		$id_zeme_kam = sqlsafe($_REQUEST['zeme_kam']);
	}
	else{
		$id_zeme_kam = 0;	
	}
	
	$error = "<span class=\"chyba\">";
	$is_error = false;
	$is_info = false;
	//zpracovavani buttonu
	if(isset($_REQUEST['t_pocet'])){
		$attrb = AM_RozkazKUtoku($id_zeme, $_REQUEST['cil'], $_REQUEST['p_pocet'], $_REQUEST['t_pocet'], $_REQUEST['typ_utoku'],true);
		$err = $attrb['error'];
		$info = $attrb['informace'];
		$is_info = true;
		if (strlen($err)>3){
			$error .= $err . "<br />";
			$is_error = true;
		}	
	}
	
	//zpracovavani buttonu
	if(isset($_REQUEST['0'])||isset($_REQUEST['1'])||isset($_REQUEST['2'])||isset($_REQUEST['3'])||isset($_REQUEST['4'])){
		$err = "";
		$err .= ZpracujOdvolaniUtoku();
		if (strlen($err)>3){
			$error .= $err . "<br />";
			$is_error = true;
		}		
	}
	$error .= "</span>";

	$menu_footer = $DIR_SKINS . "default/frame_akcni_menu/utoky_menu_foot.jpg";
  $bg = $DIR_SKINS . "default/frame_akcni_menu/utoky_bg.jpg";
  $text = '<div style="background: url('.$bg.') 0px 4px no-repeat; width: 277px; height:700px;">';
  $text .= "\n";
	$text .= '<img src="'.$menu_footer.'" alt="maska menu" style="position: absolute; top: 24px; left: 0px;" /><br />';
	
		//napoveda
  	$text .= '<a href="frame_mapa.php?section=napoveda&amp;page=utok" target="mapa" title="Jak poslat útok?" >
	<img src="'.$DIR_SKINS .'default/frame_akcni_menu/napoveda.png" alt="Jak poslat útok?" style="border:0px;position: absolute; top: 260px; left:255px;" /></a>';
  
  
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
	$text .= "<div id=\"$id_pech\" onmouseover=\"stm(Text[1],Style[1])\" onmouseout=\"htm()\"></div>\n";
	$text .= "<div id=\"$id_tan\" onmouseover=\"stm(Text[2],Style[1])\" onmouseout=\"htm()\"></div>\n";
	
	
	//PECHOTA
	$pocet_pechoty = PocetPechotyVSektoru( $id_zeme );
	$text .= "<span id=\"pechota_sekt\">";
	$text .= $pocet_pechoty;
				/*"(+". PocetPechotyVSektoru( $id_zeme, null, true ) .")
				";*/
	$text .= "</span>";
	// edit box u pechoty
	$text .= '<form action="" method="post">';
	$text .= '<input type="text" name="p_pocet" size="1" value="'.$pocet_pechoty.'" id="pechota_pocet">';
	$text .= "</td></tr>";

	// TANKY
	$pocet_tanku = PocetTankuVSektoru( $id_zeme );
	$text .= "<span id=\"tanky_sekt\">";
	$text .= $pocet_tanku;
				/*(+". PocetTankuVSektoru( $id_zeme, null, true ) .")*/
	$text .= "</span>";
	
	// Staveni tanku
		$text .= '<input type="text" name="t_pocet" size="1" value="'.$pocet_tanku.'" id="tanky_pocet">';
	
	
	//vyber zeme
	$text .= "\n<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />";
		$text .= '<select name="cil" size=1" id="cil_utoku_sel">';
		$text .= NaplnCiloveZemeProUtok( $id_zeme, $_SESSION['id_ligy'], $id_zeme_kam);
		$text .= '</select>';
	
	//vyber utoku
	$text .= "\n";
		$text .= '<select name="typ_utoku" size=1" id="typ_utoku_sel">';
		$text .= '<option value="utok">útok</option>';
		$text .= '<option value="presun">přesun</option>';
		$text .= '</select>';
		$utok_btn = $DIR_SKINS . "default/frame_akcni_menu/rozkaz_utok_btn2.jpg";
		$text .= '<input type="image" src="'.$utok_btn.'" alt="Útok" title="Rozkaz k útoku" name="rozkaz" value="Rozkaz k útoku" id="utok_btn"/></form><br />';
    
	//PODROBNOSTI UTOKU

	$text .= "<div id=\"podrobnosti_utoku\">";
	if ( $is_error ){
		$text .= $error;	
	}
	if ( $is_info ){
		$text .= $info;	
	}
	else{
		$text .= VratUtokBezBonusu($id_zeme, $_SESSION['id_ligy']);
	}
	$text .= "</div>";
	
	//SEZNAM UTOKU
    $id_hrac = $users_class->user_id();
	$id_ligy = JeUzivatelVLize($id_hrac);
	$id_zeme = sqlsafe($_SESSION['id_zeme']);
	$query = "SELECT z.nazev, igu.id,igu.pechota, igu.tanky, igu.sila, igu.typ
			 FROM in_game_utoky as igu JOIN zeme as z ON z.id=igu.id_zeme_kam WHERE
			igu.id_zeme_odkud='$id_zeme' and igu.id_ligy='$id_ligy'";
	
	$res = $db->Query( $query );
	$i = 0;
	$text .= '<form method="post" action="frame_akcni_menu.php?section=utok">';
	$text .= "<span id=\"seznam_utoku\">";
	while ($row = $db->GetFetchAssoc( $res )){
		$text .= "<input type='checkbox' name='$i' value='${row['id']}' /> ";
		if($row['typ']=="presun"){
			$text .= $row['nazev']."(přesun: ".$row['pechota']."pěch. a ".$row['tanky']." tan.)<br />";
		}
		else{
			$text .= $row['nazev']."(".$row['pechota']."pěch.+".$row['tanky']." tan.=útok: ".$row['sila'].")<br />";
		}
			
		$i++; if ($i == 5){break;}
	}
	$text .= "</span>";
	//TLACITKO ODVOLAT UTOK
		
  	$odvolat_btn = $DIR_SKINS . "default/frame_akcni_menu/rozkaz_utok_btn3.jpg";
	$text .= '<input type="image" src="'.$odvolat_btn.'" alt="Odvolat útok" title="Odvolat útok" name="odvolat" value="Odvolat útok" id="odvolat_btn"/></form><br /></div>';	

    $form = new textElement($text, null);
    $page->add_element($form, 'obsah');	 

    $form = new textElement(TooltipJednotky(), null);
    $page->add_element($form, 'tooltip');
?>
