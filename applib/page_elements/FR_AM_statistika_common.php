<?php

	require_once($DIR_CONFIG . "lib.php");

function SeznamVydelkuZemi( $id_hrac ){
	global $db, $DIR_SKINS, $CONST;
	$id_ligy = JeUzivatelVLize( $id_hrac );
	
	$text = "<table id=\"stat_table\"> \n";

	$text .= "<tr class=\"first\"><td class=\"prvni\">";
	//$text .= "<img alt=\"Suroviny\" title=\"Suroviny\" src=\"${DIR_SKINS}default/frame_akcni_menu/stat_text.png\" width=\"80\" height=\"18\" />";
	$text .= "Sektory";
	$text .= "</td><td class=\"druhy\">";
	$text .= "<img alt=\"Suroviny\" title=\"Suroviny\" src=\"${DIR_SKINS}default/frame_akcni_menu/statistika/stat_suroviny.png\" width=\"26\" height=\"22\" />";
	$text .= "</td><td class=\"treti\">";
	$text .= "<img alt=\"Palivo\" title=\"Palivo\" src=\"${DIR_SKINS}default/frame_akcni_menu/statistika/stat_palivo.png\" width=\"25\" height=\"21\" />";
	$text .= "</td><td class=\"ctvrty\">";
	$text .= "<img alt=\"Body vlivu\" title=\"Body vlivu\" src=\"${DIR_SKINS}default/frame_akcni_menu/statistika/stat_body_vlivu.png\" width=\"33\" height=\"20\" />";
	$text .= "</td></tr>";
	$suma_suroviny = 0;
	$suma_palivo = 0;
	$suma_body_vlivu = 0;
	$query = "SELECT * FROM zeme_view WHERE id_vlastnik='$id_hrac' ORDER BY nazev";
	$res = $db->Query( $query );
	
	while ($row = $db->GetFetchAssoc( $res )){
		$text .= "<tr><td class=\"prvni\">
		<a href=\"frame_akcni_menu.php?section=info&id=${row['id_zeme']}\" class=\"sektor\">
		${row['nazev']}</a>
		</td><td class=\"druhy\"><span class=\"all_suroviny\">";
		$sur = SurovinyZaKolo( $row['id_zeme'] );
		$suma_suroviny += $sur;
		$text .= $sur;
		$text .= "</span></td><td class=\"treti\"><span class=\"all_palivo\">";
		$pal = PalivoZaKolo( $row['id_zeme'] );
		$suma_palivo += $pal;
		$text .= $pal;
		$text .= "</span></td><td class=\"ctvrty\"><span class=\"all_body_vlivu\">";
		$bv = BodyVlivuZaKolo( $row['id_zeme'] );
		$suma_body_vlivu += $bv;
		$text .= $bv;
		$text .= "</span></td></tr>";
	}
	//odecteni spotreby jednotek
	$query = "SELECT sum(pechota + pechota_odeslano), sum(tanky + tanky_odeslano) 
				FROM in_game_zeme  WHERE id_vlastnik='$id_hrac'";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	$zold_suroviny = 0;
	$zold_palivo = 0;
	$pole = VratAtributyJednotek("pechota");
	$zold_suroviny += $row[0] * $pole["zold_suroviny"];
	$zold_palivo += $row[0] * $pole["zold_palivo"];
	
	$pole = VratAtributyJednotek("tanky");
	$zold_suroviny += $row[1] * $pole["zold_suroviny"];
	$zold_palivo += $row[1] * $pole["zold_palivo"];
	
	$text .= "<tr><td class=\"prvni\">";
	$text .= "Spotřeba jednotek";
	$text .= "</td><td class=\"druhy\"><span class=\"all_suroviny\">";
	$text .= "-".$zold_suroviny;
	$text .= "</span></td><td class=\"treti\"><span class=\"all_palivo\">";
	$text .= "-".$zold_palivo;
	$text .= "</span></td><td class=\"ctvrty\"><span class=\"all_body_vlivu\">";
	$text .= "-0";
	$text .= "</span></td></tr>";
	
	//pocasi
	$pocasi_vliv = ProvozPocasi( $id_ligy );
	if( $pocasi_vliv <> 1){
		$pocasi_s = floor ($suma_suroviny * $pocasi_vliv );
		$pocasi_p = floor ($suma_palivo * $pocasi_vliv );
		
		$text .= "<tr><td class=\"prvni\">";
		$text .= "Vliv počasí";
		$text .= "</td><td class=\"druhy\"><span class=\"all_suroviny\">";
		$text .= (($pocasi_s - $suma_suroviny) > 0)? "+":'';
		$text .= $pocasi_s - $suma_suroviny;
		$text .= "</span></td><td class=\"treti\"><span class=\"all_palivo\">";
		$text .= (($pocasi_p - $suma_palivo) > 0)? "+":'';
		$text .= ($pocasi_p - $suma_palivo);
		$text .= "</span></td><td class=\"ctvrty\"><span class=\"all_body_vlivu\">";
		$text .= "0";
		$text .= "</span></td></tr>";
		
		$suma_suroviny = $pocasi_s;
		$suma_palivo = $pocasi_p;
	}
	
	//kolaps v zasobovani
	$kz = KolapsVZasobovani( $id_hrac );
	$ztraty_s = 0;
	$ztraty_p = 0;
	if( $kz != 0 ){
		$ztraty_s = floor ($suma_suroviny * $kz );
		$ztraty_p = floor ($suma_palivo * $kz );
		$text .= "<tr><td class=\"prvni\">";
		$text .= "Kolaps zásobování";
		$text .= "</td><td class=\"druhy\"><span class=\"all_suroviny\">";
		$text .= "-".$ztraty_s;
		$text .= "</span></td><td class=\"treti\"><span class=\"all_palivo\">";
		$text .= "-".$ztraty_p;
		$text .= "</span></td><td class=\"ctvrty\"><span class=\"all_body_vlivu\">";
		$text .= "-0";
		$text .= "</span></td></tr>";
	}
	
	//cena letist
	$provoz_letist = ProvozLetist( $id_hrac );
	if( $provoz_letist > 0){
		$text .= "<tr><td class=\"prvni\">";
		$text .= "Provoz letišť";
		$text .= "</td><td class=\"druhy\"><span class=\"all_suroviny\">";
		$text .= "-".$provoz_letist;
		$text .= "</span></td><td class=\"treti\"><span class=\"all_palivo\">";
		$text .= "-0";
		$text .= "</span></td><td class=\"ctvrty\"><span class=\"all_body_vlivu\">";
		$text .= "-0";
		$text .= "</span></td></tr>";
	}
	
	// stab rozvedky
	if( MaHracStavbu(23) ){
		$provoz_stab = ProvozStab( $id_hrac, $ztraty_s );
		$text .= "<tr><td class=\"prvni\">";
		$text .= "Provoz štábu";
		$text .= "</td><td class=\"druhy\"><span class=\"all_suroviny\">";
		$text .= "-".$provoz_stab;
		$text .= "</span></td><td class=\"treti\"><span class=\"all_palivo\">";
		$text .= "-0";
		$text .= "</span></td><td class=\"ctvrty\"><span class=\"all_body_vlivu\">";
		$text .= "+" . $CONST["STAVBY_STAB_BV"];
		$text .= "</span></td></tr>";
		$stab_bv = $CONST["STAVBY_STAB_BV"];
	}
	else{
		$stab_bv = 0;
		$provoz_stab = 0;
	}
	
	$text .= "<tr style=\"color: limeGreen;\"><td class=\"prvni\"> ";
	$text .= "Příjmy celkem ";
	$text .= "</b></td><td class=\"druhy\"><span class=\"all_suroviny\">";
	$text .= floor($suma_suroviny - $zold_suroviny - $ztraty_s - $provoz_letist - $provoz_stab);
	$text .= "</span></td><td class=\"treti\"><span class=\"all_palivo\">";
	$text .= floor($suma_palivo - $zold_palivo - $ztraty_p);
	$text .= "</span></td><td class=\"ctvrty\"><span class=\"all_body_vlivu\">";
	//ma hrac VV?
	if ( MaHracStavbu(15) ){
		$text .= $suma_body_vlivu + $stab_bv;
	}
	else{
		// zobrazit 0 a odkaz na napovedu
		$text .= '<a href="frame_mapa.php?section=napoveda&page=statistika_zeme" style="color: red;" target="mapa" ><strong>0</strong></a>';
	}
	
	$text .= "</span></td></tr>\n";
		
	$text .= "</table> \n";
	
	return $text;
}

function PrintMenu( $aktivni = '', $id_ligy = 0 ){
	global $db;
	$text = "";
	$css1 = 'stat1';
	$css2 = 'stat2';
	$css4 = 'stat4';
	
	//zjisteni typu ligy
	$query = "SELECT typ FROM ligy WHERE id='$id_ligy'";
	$res = $db->Query( $query );
	if( $row = $db->GetFetchAssoc( $res ) ){
		$typ_ligy = $row['typ'];
	}
	else{
		$typ_ligy = '';
	}
	
	switch($aktivni){
		case 'porovnani':
			$css2 .= '_sel';
			break;
		case 'tym_mod':
			$css4 .= '_sel';
			break;
		case 'centrala':
			//FALLTROUGHT
		default:
			$css1 .= '_sel';
	}
	
	$text .= '<div id="stat_menu">';
			
	$text .= '<div id="stat_menu_inside">';
	$text .= '<a href="frame_akcni_menu.php?section=statistika&page=centrala" id="'.$css1.'">
	<span style="display:none;">Centrala </span>
	</a>';
	$text .= '<a href="frame_akcni_menu.php?section=statistika&page=porovnani" id="'.$css2.'">
	<span style="display:none;">Porovnani souperu </span>
	</a>';
	if( $typ_ligy == 'team'){
	$text .= '<a href="frame_akcni_menu.php?section=statistika&page=tym_mod" id="'.$css4.'">
	<span style="display:none;">Tymova statistika </span>
	</a>';
	}
	$text .= '</div>';
	
	$text .= '</div>';
	return $text;
}

function PrintSubMenu( $aktivni = ''){
	$text = "";
	$href = 'frame_akcni_menu.php?section=statistika&page=centrala&subpage=';
	$css1 = 'stat_submenu_1 stat_centrala1';
	$css2 = 'stat_submenu_2 stat_centrala2';
	
	switch($aktivni){
		case 'utoky':
			$css2 .= '_sel';
			break;
		case 'zisky':
			//FALLTROUGHT
		default:
			$css1 .= '_sel';
	}
	
	$text .= '<div class="stat_submenu stat_centrala">';
	$text .= '<ul class="stat_submenu_ul">';
	
	$text .= '<li class="'.$css1.'">';
	$text .= '<a target="am" href="'.$href.'zisky">
				<span>Zisky</span>
			</a>';
	$text .= '</li>';
	
	$text .= '<li class="'.$css2.'">';
	$text .= '<a target="am" href="'.$href.'utoky">
				<span>Odeslane utoky</span>
			</a>';
	$text .= '</li>';
	
/*	$text .= '<li id="stat_submenu_3">';
	$text .= '<a target="am" href="'.$href.'zisky">
				<span>Zisky sektoru</span>
			</a>';
	$text .= '</li>';
	
	$text .= '<li id="stat_submenu_4">';
	$text .= '<a target="am" href="'.$href.'zisky">
				<span>Zisky sektoru</span>
			</a>';
	$text .= '</li>';*/

	$text .= '</ul>';
	$text .= '</div>';
	
	return $text; 
}

function PrintSubMenuPorovnani( $id_ligy, $aktivni ){
	$text = "";
	$href = 'frame_akcni_menu.php?section=statistika&page=porovnani&subpage=';
	$css1 = 'stat_submenu_1 stat_porovnani1';
	$css2 = 'stat_submenu_2 stat_porovnani2';
	$css3 = 'stat_submenu_3 stat_porovnani3';
	$css4 = 'stat_submenu_4 stat_porovnani4';
	
	switch($aktivni){
		case 'jednotky':
			$css2 .= '_sel';
			break;
		case 'ls':
			$css3 .= '_sel';
			break;
		case 'bv':
			$css4 .= '_sel';
			break;
		case 'sektory':
			//FALLTROUGHT
		default:
			$css1 .= '_sel';
		
	}
	
	$text .= '<div class="stat_submenu stat_porovnani">';
	$text .= '<ul class="stat_submenu_ul">';
	
	$text .= '<li class="'.$css1.'">';
	$text .= '<a target="am" href="'.$href.'sektory">
				<span>Pocet sektoru</span>
			</a>';
	$text .= '</li>';
	
	$text .= '<li class="'.$css2.'">';
	$text .= '<a target="am" href="'.$href.'jednotky">
				<span>Pocet jednotek</span>
			</a>';
	$text .= '</li>';
	
	$text .= '<li class="'.$css3.'">';
	$text .= '<a target="am" href="'.$href.'ls">
				<span>Zisky sektoru</span>
			</a>';
	$text .= '</li>';

	$text .= '<li class="'.$css4.'">';
	$text .= '<a target="am" href="'.$href.'bv">
				<span>BV</span>
			</a>';
	$text .= '</li>';

	$text .= '</ul>';
	$text .= '</div>';
	
	return $text; 	
}

function PrintSubMenuTymMod( $id_ligy, $aktivni = '' ){
	$text = "";
	$href = 'frame_akcni_menu.php?section=statistika&page=tym_mod&subpage=';
	$css1 = 'stat_submenu_1 stat_tym_mod1';
	$css2 = 'stat_submenu_2 stat_tym_mod2';
	$css3 = 'stat_submenu_3 stat_tym_mod3';
	
	switch($aktivni){
		case 'tym_uspesnost':
			$css2 .= '_sel';
			break;
		case 'tym_prestiz':
			$css3 .= '_sel';
			break;
		case 'tym_sestava':
			//FALLTROUGHT
		default:
			$css1 .= '_sel';
		
	}
	$text .= '<div class="stat_submenu stat_tym_mod">
	<ul class="stat_submenu_ul">';
	
	$text .= '<li class="'.$css1.'">';
	$text .= '<a target="am" href="'.$href.'tym_sestava">
	<span>Tymova sestava</span>
	</a></li>';
	
	$text .= '<li class="'.$css2.'">';
	$text .= '<a target="am" href="'.$href.'tym_uspesnost">
	<span>Uspesnost tymu</span>
	</a></li>';
	
	$text .= '<li class="'.$css3.'">';
	$text .= '<a target="am" href="'.$href.'tym_prestiz">
	<span>Prestiz hrace</span>
	</a></li>';
	
	$text .= '</ul></div>';

	return $text;
}

function SeznamUtokuHrace( $user_id = null ){
	global $db;
	$text = "";
	if(!isset( $user_id )){
		$user_id = $users_class->user_id();
	}
	$id_ligy = JeUzivatelVLize( $user_id );
	$query = "SELECT z.nazev, igu.id,igu.pechota, igu.tanky, igu.sila, igu.typ
			 FROM in_game_utoky as igu JOIN zeme as z ON z.id=igu.id_zeme_kam 
			 WHERE igu.id_ligy='$id_ligy' AND igu.id_vlastnik='$user_id' ORDER BY z.nazev";
	
	$res = $db->Query( $query );

	$text .= "<table id=\"stat_table\"> \n";
	while ($row = $db->GetFetchAssoc( $res )){
		$text .= "<tr><td>";
		if($row['typ']=="presun"){
			$text .= $row['nazev']."</td><td>(přesun: ".$row['pechota']."pěch. a ".$row['tanky']." tan.)";
		}
		else{
			$text .= $row['nazev']."</td><td>(".$row['pechota']."pěch.+".$row['tanky']." tan.=útok: ".$row['sila'].")";
		}
		$text .= "</td></tr>";
	}
	$text .= "</table> \n";
	return $text;
}

function SeznamPorovnaniJednotky( $id_ligy){
	global $db, $DIR_SKINS, $CONST;
	
	$text = "<table id=\"por_table\"> \n";

	$text .= "<tr class=\"first\"><td class=\"por_prvni\">";
	$text .= "Znak";
	$text .= "</td><td class=\"por_jednotky_druhy\">";
	$text .= "Jméno velitele";
	$text .= "</td><td class=\"por_jednotky_treti\">";
	$text .= "Pěchota,Tanky";
	$text .= "</td></tr>";	
	$suma_suroviny = 0;
	$suma_palivo = 0;
	$suma_body_vlivu = 0;
	$query = "SELECT usi.id, usi.login, x.pechota, x.tanky, x.suma, igh.symbol, igh.strana 
			FROM users_sys AS usi 
			JOIN (SELECT id_vlastnik, SUM(pechota) AS pechota , SUM(tanky) AS tanky,
				SUM(pechota + tanky) AS suma 
				FROM in_game_vcera_zeme GROUP BY id_vlastnik ) AS x
			JOIN in_game_hrac AS igh 
			ON usi.id=x.id_vlastnik AND igh.id_hrac=usi.id
			WHERE igh.id_liga='$id_ligy' ORDER BY x.suma DESC 
	";
	$res = $db->Query( $query );
	$radek = 'lichy';
	while( $row = $db->GetFetchAssoc( $res ) ){
		$text .= "<tr class=\"$radek\"><td class=\"por_prvni\">";
		//zobrazeni symbolu	
    	if($row['strana']=="sssr"){
    		$symbol = "s";
    	}
		if($row['strana']=="us"){
    		$symbol = "u";
    	}    	
	 	$symbol .= $row['symbol'] . ".png";	
		$text .= "<img src=\"./skins/default/frame_mapa/zeme/symbols/$symbol\" alt=\" \" />";
		
		$text .= "</td><td class=\"por_jednotky_druhy\">";
		$text .= $row['login'];
		$text .= "</td><td class=\"por_jednotky_treti\">";
		$text .= $row['pechota'] . "~" . $row['tanky'];
		$text .= "</td></tr>";
		
		if( $radek == 'lichy' ){
			$radek = 'sudy';
		}
		else{
			$radek = 'lichy';
		}
		
	}
	
	$text .= "</table> \n";
	return $text;
}

function SeznamPorovnaniLS( $id_ligy ){
	global $db, $DIR_SKINS, $CONST;
	
	$text = "<table id=\"por_table\"> \n";

	$text .= "<tr class=\"first\"><td class=\"por_prvni\">";
	$text .= "Znak";
	$text .= "</td><td class=\"por_druhy\">";
	$text .= "Jméno velitele";
	$text .= "</td><td class=\"por_treti\">";
	$text .= "Letecká síla";
	$text .= "</td></tr>";	
	$suma_suroviny = 0;
	$suma_palivo = 0;
	$suma_body_vlivu = 0;
	$query = "SELECT usi.id, usi.login, igv.letecka_sila, igh.symbol, igh.strana FROM users_sys AS usi 
			JOIN in_game_vcera AS igv
			JOIN in_game_hrac AS igh 
			ON usi.id=igv.id_hrac AND igh.id_hrac=usi.id AND igv.id_ligy=igh.id_liga
			WHERE igh.id_liga='$id_ligy' ORDER BY igv.letecka_sila DESC 
	";
	$res = $db->Query( $query );
	$radek = 'lichy';
	while( $row = $db->GetFetchAssoc( $res ) ){
		$text .= "<tr class=\"$radek\"><td class=\"por_prvni\">";
		//zobrazeni symbolu	
    	if($row['strana']=="sssr"){
    		$symbol = "s";
    	}
		if($row['strana']=="us"){
    		$symbol = "u";
    	}    	
	 	$symbol .= $row['symbol'] . ".png";	
		$text .= "<img src=\"./skins/default/frame_mapa/zeme/symbols/$symbol\" alt=\" \" />";
		
		$text .= "</td><td class=\"por_druhy\">";
		$text .= $row['login'];
		$text .= "</td><td class=\"por_treti\">";
		$ls = round($row['letecka_sila'] * 20 / VratLSProZavreni($id_ligy)) * 5;
		$text .= $ls."%";
		$text .= "</td></tr>";
		
		if( $radek == 'lichy' ){
			$radek = 'sudy';
		}
		else{
			$radek = 'lichy';
		}
		
	}
	
	$text .= "</table> \n";
	return $text;
}

function SeznamPorovnaniBV( $id_ligy ){
	global $db, $DIR_SKINS, $CONST;
	
	$text = "<table id=\"por_table\"> \n";

	$text .= "<tr class=\"first\"><td class=\"por_prvni\">";
	$text .= "Znak";
	$text .= "</td><td class=\"por_druhy\">";
	$text .= "Jméno velitele";
	$text .= "</td><td class=\"por_treti\">";
	$text .= "Body vlivu";
	$text .= "</td></tr>";	
	$suma_suroviny = 0;
	$suma_palivo = 0;
	$suma_body_vlivu = 0;
	$query = "SELECT usi.id, usi.login, x.suma, igh.symbol, igh.strana 
			FROM users_sys AS usi 
			JOIN (SELECT id_vlastnik, SUM(body_vlivu) AS suma 
				FROM zeme_view GROUP BY id_vlastnik ) AS x
			JOIN in_game_hrac AS igh 
			ON usi.id=x.id_vlastnik AND igh.id_hrac=usi.id
			WHERE igh.id_liga='$id_ligy' ORDER BY x.suma DESC 
	";
	$res = $db->Query( $query );
	$radek = 'lichy';
	while( $row = $db->GetFetchAssoc( $res ) ){
		$text .= "<tr class=\"$radek\"><td class=\"por_prvni\">";
		//zobrazeni symbolu	
    	if($row['strana']=="sssr"){
    		$symbol = "s";
    	}
		if($row['strana']=="us"){
    		$symbol = "u";
    	}    	
	 	$symbol .= $row['symbol'] . ".png";	
		$text .= "<img src=\"./skins/default/frame_mapa/zeme/symbols/$symbol\" alt=\" \" />";
		
		$text .= "</td><td class=\"por_druhy\">";
		$text .= $row['login'];
		$text .= "</td><td class=\"por_treti\">";
		$text .= $row['suma'];
		$text .= "</td></tr>";
		
		if( $radek == 'lichy' ){
			$radek = 'sudy';
		}
		else{
			$radek = 'lichy';
		}
		
	}
	
	$text .= "</table> \n";
	return $text;
}


function SeznamPorovnaniSektory( $id_ligy ){
	global $db, $DIR_SKINS;
	
	$text = "<table id=\"por_table\"> \n";

	$text .= "<tr class=\"first\"><td class=\"por_prvni\">";
	$text .= "Znak";
	$text .= "</td><td class=\"por_druhy\">";
	$text .= "Jméno velitele";
	$text .= "</td><td class=\"por_treti\">";
	$text .= "Sektory";
	$text .= "</td></tr>";	
	$suma_suroviny = 0;
	$suma_palivo = 0;
	$suma_body_vlivu = 0;
	$query = "SELECT usi.id, usi.login, count(*) AS pocet, igh.symbol, igh.strana FROM users_sys AS usi 
			JOIN in_game_zeme AS igz
			JOIN in_game_hrac AS igh 
			ON usi.id=igz.id_vlastnik AND igh.id_hrac=usi.id
			WHERE igz.id_ligy='$id_ligy' GROUP BY usi.id ORDER BY pocet DESC 
	";
	$res = $db->Query( $query );
	$radek = 'lichy';
	while( $row = $db->GetFetchAssoc( $res ) ){
		$text .= "<tr class=\"$radek\"><td class=\"por_prvni\">";
		//zobrazeni symbolu	
    	if($row['strana']=="sssr"){
    		$symbol = "s";
    	}
		if($row['strana']=="us"){
    		$symbol = "u";
    	}    	
	 	$symbol .= $row['symbol'] . ".png";	
		$text .= "<img src=\"./skins/default/frame_mapa/zeme/symbols/$symbol\" alt=\" \" />";
		
		$text .= "</td><td class=\"por_druhy\">";
		$text .= $row['login'];
		$text .= "</td><td class=\"por_treti\">";
		$text .= $row['pocet'];
		$text .= "</td></tr>";
		
		if( $radek == 'lichy' ){
			$radek = 'sudy';
		}
		else{
			$radek = 'lichy';
		}
		
	}
	
	$text .= "</table> \n";
	return $text;
}

function SeznamTymSestava( $id_ligy ){
	global $db, $DIR_SKINS;
	$id_ligy = sqlsafe($id_ligy);	
	$text = '<div id="tym_sestava_wrapper">';
	$text .= '<div id="tym_sestava_znaky"></div>';
	$text .= '<div id="tym_sestava_podklad">';
	$text .= '<div class="tym_sestava tym_sestava_vlevo">';
	
	$query = "SELECT id_hrac, login, strana FROM 
		users_sys AS us JOIN in_game_hrac AS igh 
		ON us.id=igh.id_hrac
		WHERE igh.id_liga='$id_ligy' AND strana='us' ORDER BY login";	
	$res = $db->Query( $query );
	
	while( $row = $db->GetFetchAssoc( $res ) ){
		if($row['strana']=='us'){
			$text .= '<div class="tym_sestava_spoluhrac">
			<a href="frame_akcni_menu.php?section=info_hrac&amp;id_hrac='.$row['id_hrac'].'">
			'.$row['login'].'</a>
			</div>';
		}
	}

	$text .= '</div>';
	$text .= '<div class="tym_sestava tym_sestava_vpravo">';
	
 	$query = "SELECT id_hrac, login, strana FROM 
		users_sys AS us JOIN in_game_hrac AS igh 
		ON us.id=igh.id_hrac
		WHERE igh.id_liga='$id_ligy' AND strana='sssr' ORDER BY login";
	$res = $db->Query( $query );
	
	while( $row = $db->GetFetchAssoc( $res ) ){
		if($row['strana']=='sssr'){
			$text .= '<div class="tym_sestava_spoluhrac">
			<a href="frame_akcni_menu.php?section=info_hrac&amp;id_hrac='.$row['id_hrac'].'">
			'.$row['login'].'</a>
			</div>';
		}
	}
$text .= '</div>';

$text .= '</div></div>';

	return $text;
}

function SeznamTymUspesnost( $id_ligy ){
	global $db, $DIR_SKINS;
	
	//vypocitani procent
	$query = "SELECT sum(x.pocet) FROM in_game_hrac AS igh JOIN
	(select id_vlastnik, count(*) as pocet from in_game_zeme group by id_vlastnik) AS x ON x.id_vlastnik=igh.id_hrac
	WHERE id_liga='$id_ligy' AND strana='us'";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	$sum_us = $row[0];
	
	$query = "SELECT sum(x.pocet) FROM in_game_hrac AS igh JOIN
	(select id_vlastnik, count(*) as pocet from in_game_zeme group by id_vlastnik) AS x ON x.id_vlastnik=igh.id_hrac
	WHERE id_liga='$id_ligy' AND strana='sssr'";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	$sum_sssr = $row[0];
	
	$query = "select count(*) from zeme ";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	$sum = $row[0];
	
	$us = round( $sum_us / $sum * 100 );
	$sssr = round( $sum_sssr / $sum * 100 );
	
	$text = '<table id="tym_uspesnost_table"><tr>
<td colspan="2" id="tym_uspesnost_nadpis">KONTROLA NAD VEŠKERÝMI HERNÍMI SEKTORY</td></tr><tr>
<td>
<div id="tym_uspesnost_left_img">
<div class="tym_uspesnost_img_txt">'.$us.'%</div>
</div>
</td>
<td>
<div id="tym_uspesnost_right_img">
<div class="tym_uspesnost_img_txt">'.$sssr.'%</div>
</div>
</td></tr>
</table> 
	';

	return $text;
}

function SeznamTymPrestiz( $id_ligy ){
	global $db, $DIR_SKINS, $CONST;
	
	$text = "<table id=\"por_table\"> \n";

	$text .= "<tr class=\"first\"><td class=\"por_prvni\">";
	$text .= "Znak";
	$text .= "</td><td class=\"por_druhy\">";
	$text .= "Jméno velitele";
	$text .= "</td><td class=\"por_treti\">";
	$text .= "Prestiž";
	$text .= "</td></tr>";	
	$query = "SELECT usi.id, usi.login, igh.symbol, igh.strana , igh.prestiz
			FROM users_sys AS usi 
			JOIN in_game_hrac AS igh 
			ON usi.id=igh.id_hrac AND igh.id_hrac=usi.id 
			WHERE igh.id_liga='$id_ligy' ORDER BY igh.prestiz DESC 
	";
	$res = $db->Query( $query );
	$radek = 'lichy';
	while( $row = $db->GetFetchAssoc( $res ) ){
		$text .= "<tr class=\"$radek\"><td class=\"por_prvni\">";
		//zobrazeni symbolu	
    	if($row['strana']=="sssr"){
    		$symbol = "s";
    	}
		if($row['strana']=="us"){
    		$symbol = "u";
    	}    	
	 	$symbol .= $row['symbol'] . ".png";	
		$text .= "<img src=\"./skins/default/frame_mapa/zeme/symbols/$symbol\" alt=\" \" />";
		
		$text .= "</td><td class=\"por_druhy\">";
		$text .= $row['login'];
		$text .= "</td><td class=\"por_treti\">";
		$text .= $row['prestiz'];
		$text .= "</td></tr>";
		
		if( $radek == 'lichy' ){
			$radek = 'sudy';
		}
		else{
			$radek = 'lichy';
		}
		
	}
	
	$text .= "</table> \n";
	return $text;
}


?>