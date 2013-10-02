<?php
	//ekonomicke funkce
	require_once($DIR_CONFIG . "formulare.php");

function MaHracStavbu( $id_stavby, $id_user = null ){
	global $users_class, $db;
	
	if(!isset($id_user)){
		$id_user = $users_class->user_id();
	}
	
	$query = "SELECT count(*) FROM `in_game_stavby`
		 where (id_vlastnik='".$id_user."') and 
		 (id_ligy='".JeUzivatelVLize($id_user)."') and 
		 (id_stavby='$id_stavby')";
		$res2 = $db->Query( $query );
		$vysledek = $db->GetFetchRow($res2);
		if($vysledek[0]>0){
			return true;
		}	
	return false;
}

function MaHracStavbuVZemi( $id_stavby, $id_zeme, $id_ligy = null ){
	global $users_class, $db;
	
	if($id_ligy == null){
		$id_ligy = JeUzivatelVLize();
	}

	$query = "SELECT count(*) FROM `in_game_stavby`
		 where (id_zeme='$id_zeme') and 
		 (id_ligy='$id_ligy') and 
		 (id_stavby='$id_stavby')";
		$res2 = $db->Query( $query );
		$vysledek = $db->GetFetchRow($res2);
		if($vysledek[0]>0){
			return true;
		}	
	return false;
}

function PridejPVOStanici( $id_zeme, $id_ligy){
	global $db, $users_class, $CONST;
	$id_zeme = sqlsafe( $id_zeme );
	$id_ligy = sqlsafe( $id_ligy );
	$id_user = $users_class->user_id();
	
	if (!JeZemHrace($id_zeme, null, $id_ligy)){
		return;
	}
	
	if( !MaHracStavbuVZemi(18, $id_zeme, $id_ligy)){	
		return;
	}
	
	$query = "SELECT pvo_stanice FROM `in_game_zeme` 
			WHERE id_zeme='$id_zeme' and id_ligy='$id_ligy'";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	$pocet = $row[0];
	if ($pocet >= $CONST['MAX_PVO_STANIC']){
		return;
	} 
	
	if (!ZaplatSuroviny(CenaNavyseniPVOStanice( $id_zeme, $id_ligy ))){
		return;
	}
		
	$query = "UPDATE `in_game_zeme` SET `pvo_stanice`= 
			 `pvo_stanice` + 1 where id_zeme='$id_zeme' and id_ligy='$id_ligy';";
	$db->DbQuery( $query );
}

function PridejPolicejniStanici( $id_zeme, $id_ligy){
	global $db, $users_class, $CONST;
	$id_zeme = sqlsafe( $id_zeme );
	$id_ligy = sqlsafe( $id_ligy );
	$id_user = $users_class->user_id();
	
	if (!JeZemHrace($id_zeme, null, $id_ligy)){
		return;
	}
	
	if( !MaHracStavbuVZemi(20, $id_zeme, $id_ligy)){	
		return;
	}
	
	$query = "SELECT vojenska_policie FROM `in_game_zeme` 
			WHERE id_zeme='$id_zeme' and id_ligy='$id_ligy'";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	$pocet = $row[0];
	if ($pocet >= $CONST['MAX_POL_STANIC']){
		return;
	} 
	
	if (!ZaplatSuroviny(CenaNavyseniPolicejniStanice( $id_zeme, $id_ligy ))){
		return;
	}
		
	$query = "UPDATE `in_game_zeme` SET `vojenska_policie`= 
			`vojenska_policie` + 1 where id_zeme='$id_zeme' and id_ligy='$id_ligy'";
	$db->DbQuery( $query );
}

function MuzePostavit($id_stavba, $id_zeme, $id_ligy, $id_user){
	global $db, $users_class, $CONST;
	
	//test zda je spravna strana
	$stavby_us = explode(',', $CONST["STAVBY_US"]);
	$stavby_sssr = explode(',', $CONST["STAVBY_SSSR"]);
	if( in_array($id_stavba, $stavby_us) || in_array($id_stavba, $stavby_sssr) ){
		$query = "SELECT strana FROM `in_game_hrac` WHERE   
			 id_hrac='$id_user';";
		$res = $db->Query( $query );
		$row = $db->GetFetchAssoc( $res );
		if(!in_array($id_stavba, ${'stavby_'.$row['strana']}) ){
			return false;
		}
	}
	
	//ma dostatecnou infrastrukturu
	if($CONST['stavby_omezeni_infra'][$id_stavba] != 0){
		$query = "SELECT infrastruktura_now FROM `in_game_zeme`
		 where (id_ligy='$id_ligy') and 
		 (id_zeme='$id_zeme')";
		$res2 = $db->Query( $query );
		$vysledek = $db->GetFetchRow($res2);
		if($vysledek[0]<$CONST['stavby_omezeni_infra'][$id_stavba]){
			return false;
		}
	}
	
	//test zda jsou stavby ktere muze mit hrac jen jednou uz postaveny
	if(($id_stavba == 9)||($id_stavba == 15)||($id_stavba == 23)){
		$query = "SELECT count(*) FROM `in_game_stavby`
		 where (id_vlastnik='$id_user') and 
		 (id_ligy='$id_ligy') and 
		 (id_stavby='$id_stavba')";
		$res2 = $db->Query( $query );
		$vysledek = $db->GetFetchRow($res2);
		if($vysledek[0]>0){
			return false;
		}
	}
	
	//test zda jsou postaveny prerekvizity staveb
	if(($id_stavba == 3)||($id_stavba == 13)){
		if ($id_stavba == 3){
			//pro hangar je nutne nejake letiste
			$id_prereq1 = 1;
			$id_prereq2 = 2;
		}
		if ($id_stavba == 13){
			//pro minove pole je nutna zenijni brigada
			$id_prereq1 = 21;
			$id_prereq2 = 21;
		}
		$query = "SELECT count(*) FROM `in_game_stavby`
		 where (id_zeme='$id_zeme') and 
		 (id_ligy='$id_ligy') and 
		 ((id_stavby='$id_prereq1')or(id_stavby='$id_prereq2'))";
		$res2 = $db->Query( $query );
		$vysledek = $db->GetFetchRow($res2);
		if($vysledek[0]==0){
			return false;
		}
	}
	
	//test zda nejsou postaveny vylucujici stavby
	if($id_stavba == 24 || $id_stavba == 1 || $id_stavba == 2){
		if ($id_stavba == 24){
			//pro brigadu nesmi byt postaveno letiste
			$id_prereq1 = 1;
			$id_prereq2 = 2;
		}

		if ($id_stavba == 1 || $id_stavba == 2){
			//pro brigadu nesmi byt postavena PVO brigada
			$id_prereq1 = 24;
			$id_prereq2 = 24;
		}
		
		$query = "SELECT count(*) FROM `in_game_stavby`
		 where (id_zeme='$id_zeme') and 
		 (id_ligy='$id_ligy') and 
		 ((id_stavby='$id_prereq1')or(id_stavby='$id_prereq2'))";
		
		$res3 = $db->Query( $query );
		$vysledek = $db->GetFetchRow($res3);
		if($vysledek[0]>0){
			return false;
		}
	}
	
	return true;
}
	
function PostavStavbu($id_zeme, $id_stavby){
	global $db, $users_class, $CONST;
	$id_stavby = sqlsafe( $id_stavby );
	$id_zeme = sqlsafe( $id_zeme );
	$id_user = $users_class->user_id();
	$id_ligy = JeUzivatelVLize( $id_user );

	if( !MuzePostavit($id_stavby, $id_zeme, $id_ligy, $id_user) ){
		return;
	}
	
	if (!JeZemHrace($id_zeme, null, $id_ligy)){
		return;
	}
	
	//STAVBA JE UZ POSTAVENA?
	$query = "SELECT * FROM `in_game_stavby` where (id_zeme='".$id_zeme."') and 
		 (id_ligy='$id_ligy')and (id_stavby='$id_stavby')";
	$res2 = $db->Query( $query );
	if ($stavba = $db->GetFetchAssoc($res2) ){
		return;
	}
	
	$cena = CenaPostaveniStavby( $id_stavby );

	if (!ZaplatSurovinyAPalivo($cena["suroviny"], $cena["palivo"])){
		return "Nemáte dostatek zdrojů";
	}
	
	//vlozeni dat do DB
	$query = "INSERT INTO `in_game_stavby`  
			( `id_zeme`, `id_ligy`,`id_stavby`,`id_vlastnik`) VALUES
			('$id_zeme',
			'" .JeUzivatelVLize( $id_user )."'
			, '$id_stavby',
			'$id_user'
			);";
	$db->DbQuery( $query );
	
	//pokud je stavba letiste, zvysi se LS hrace
	if( $id_stavby == 1){
		$add_ls = $CONST["STAVBY_letiste_add_ls"];
		$db->DbQuery( "UPDATE in_game_hrac SET letecka_sila = letecka_sila + $add_ls 
					WHERE id_hrac = '$id_user';" );
	}
}

function ZbourejStavbu($id_zeme, $id_stavby){
	global $db, $users_class, $CONST;
	
	$id_stavby = sqlsafe( $id_stavby );
	$id_zeme = sqlsafe( $id_zeme );
	$id_user = $users_class->user_id();
	$id_ligy = JeUzivatelVLize( $id_user );
	
	if (!JeZemHrace($id_zeme, null, $id_ligy)){
		return;
	}
	
	//test zda hrac stavbu ma postavenou
	$query = "SELECT * FROM `in_game_stavby` WHERE   
			 id_zeme='$id_zeme' and
			 id_ligy='".JeUzivatelVLize( $id_user )."' and
			 id_stavby='$id_stavby';";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	$pocet = $row[0];
	if( $pocet<1 ){
		return;
	}
	
	//vlozeni dat do DB
	$query = "DELETE FROM `in_game_stavby` WHERE   
			 id_zeme='$id_zeme' and
			 id_ligy='".JeUzivatelVLize( $id_user )."' and
			 id_stavby='$id_stavby';";

	$db->DbQuery( $query );
	
	//pokud je stavba letiste, snizi se LS hrace
	if( $id_stavby == 1){
		$add_ls = $CONST["STAVBY_letiste_add_ls"];
		$db->DbQuery( "UPDATE in_game_hrac SET letecka_sila = letecka_sila - $add_ls 
					WHERE id_hrac = '$id_user';" );
	}
	
	//pokud je to zarizeni, dostane zdroje zpet
	$query = "SELECT * FROM stavby WHERE typ='zarizeni' AND id='$id_stavby'";
	$res = $db->Query( $query );
	if( $row = $db->GetFetchAssoc( $res ) ){
		//pridej suroviny
		$db->DbQuery( "UPDATE in_game_hrac SET 
					suroviny = suroviny + ".round( $row['cena_suroviny'] * $CONST['STAVBY_POMER_ZBOURANI'] ).", 
					palivo = palivo + ".round( $row['cena_palivo'] * $CONST['STAVBY_POMER_ZBOURANI'] )."
					WHERE id_hrac = '$id_user';" );
	}
}

function RadekStavba($id_zeme, $id_stavba, $left, $top){
	global $db, $users_class;
	$id_user = $users_class->user_id();
	
	$id_stavba = sqlsafe($id_stavba);
	$id_zeme = sqlsafe($id_zeme);
	
	$query = "SELECT id_liga FROM `in_game_hrac` WHERE id_hrac='$id_user'";
	$ress = $db->Query( $query );
	$naz = $db->GetFetchRow( $ress );
	$id_ligy = $naz[0];
	 
	
	$query = "SELECT nazev FROM `stavby` WHERE id='$id_stavba'";
	$ress = $db->Query( $query );
	$naz = $db->GetFetchRow( $ress );
	$nazev = $naz[0];
		
	$vypnuta = !MuzePostavit($id_stavba, $id_zeme, $id_ligy, $id_user);
	
	//test staveb jen pro urcite strany
	if(($id_stavba == 14)||($id_stavba == 16)||($id_stavba == 22)){
		if ($id_stavba == 14){
			//zemljanka je jen pro sssr
			$strana = "sssr";
		}
		if ($id_stavba == 16){
			// katusa je jen pro sssr
			$strana = "sssr";
		}
		if ($id_stavba == 22){
			// MASH je jen pro us
			$strana = "us";
		}
		$query = "SELECT strana FROM `in_game_hrac`
		 where (id_hrac='$id_user')";
		$res2 = $db->Query( $query );
		$vysledek = $db->GetFetchRow($res2);
		if($vysledek[0]!=$strana){
			$vypnuta = true;
		}
	}
	
	
	$query = "SELECT * FROM `in_game_stavby`
		 where (id_zeme='".$id_zeme."') and 
		 (id_ligy='$id_ligy')and 
		 (id_stavby='$id_stavba')";
	
	$res2 = $db->Query( $query );
	
	$posun = 78;
	
	$text = "<div style=\"position: absolute; left:${left}px; top: ${top}px;\" onmouseover=\"stm(Text[$id_stavba],Style[1])\" onmouseout=\"htm()\">";
	
	//STAVBA JE UZ POSTAVENA
	if ($stavba = $db->GetFetchAssoc($res2) ){
		$text .= '<img src="./skins/default/frame_akcni_menu/vystavba/'.$id_stavba.'_p.png" width="74" height="28" alt="" />';
		$text .= "</div>
			<div style=\"position: absolute; left:".($left+$posun)."px; top:${top}px;\" >
		
				<a class=\"vystavba_akce\" href=\"frame_akcni_menu.php?section=vystavba&amp;action=zbourej
				&amp;id_zeme=".$id_zeme."&amp;
				id_stavby=".$id_stavba."\">";
		$text .= '<img src="./skins/default/frame_akcni_menu/vystavba/odstranit.png" width="51" height="28" alt="" class="vystavba_obrazek" />';
		$text .= "</a>";
		
	}
	//STAVBA NENI POSTAVENA
	else{
		//specialni pripady staveb, kdy je staveni vypnuto
		if($vypnuta){
			$text .= '<img src="./skins/default/frame_akcni_menu/vystavba/'.$id_stavba.'_d.png" width="74" height="28" alt="" />';
			$text .= "</div><div style=\"position: absolute; left:".($left+$posun)."px; top:${top}px;\" >";
			$text .= '<img src="./skins/default/frame_akcni_menu/vystavba/neaktivni.png" width="51" height="28" alt="" />';
		}
		//stavba opravdu neni postavena a muzu ji postavit
		else{
			$text .= '<img src="./skins/default/frame_akcni_menu/vystavba/'.$id_stavba.'.png" width="74" height="28" alt="" />';
			$text .= "</div><div style=\"position: absolute; left:".($left+$posun)."px; top:${top}px;\" >";
			
			$text .= "<a class=\"vystavba_akce\" href=\"frame_akcni_menu.php?section=vystavba&amp;action=pos
				tav&amp;id_zeme=".$id_zeme."&amp;
				id_stavby=".$id_stavba."\">";
			$text .= '<img src="./skins/default/frame_akcni_menu/vystavba/postavit.png" width="51" height="28" alt="" class="vystavba_obrazek" />
			</a>';	
				
		}
	}
	
	$text .= "</div>";
	return $text;
}

/**
 *
 * @param int $id_hrac - pokud neni zadan vezme se aktualni
 * @return pocet surovin za kolo
 */
function ZpracovaniEfektuZenijniBrigadyNaInfrAPovolanavniJednotekHrace( $id_hrac = null ){
	global  $users_class, $db, $CONST;
	
	if (!isset($id_user)){
		$id_user = $users_class->user_id();
	}

	//seznam vsech zemi
	$query = "SELECT * FROM in_game_zeme WHERE id_vlastnik='$id_user'";
	$seznam_res = $db->Query( $query );
	
	while ($zeme = $db->GetFetchAssoc( $seznam_res )){
		//zda je postavena zenijni brigada
		if(!MaHracStavbuVZemi(21,$zeme['id_zeme'],$zeme['id_ligy'])){
			continue;
		}
		
		//zvyseni infrastruktury v konkretni zemi
		if ($zeme['infrastruktura_now']<25){
			$zvyseni = $CONST['STAVBY_ZENIJNI_BRIGADA_POD_25'];
		}
		else if ($zeme['infrastruktura_now']<50){
			$zvyseni = $CONST['STAVBY_ZENIJNI_BRIGADA_POD_50'];
		}
		else if ($zeme['infrastruktura_now']<75){
			$zvyseni = $CONST['STAVBY_ZENIJNI_BRIGADA_POD_75'];
		}
		else{
			$zvyseni = $CONST['STAVBY_ZENIJNI_BRIGADA_POD_100'];
		}
		
		//zvyseni moznych povolanych jednotek
		if($zeme['infrastruktura_now'] <10){
			$prirustek = 1;
		}
		else if($zeme['infrastruktura_now'] <=25){
			$prirustek = 2;
		}
		else if($zeme['infrastruktura_now'] <=50){
			$prirustek = 3;
		}
		else if($zeme['infrastruktura_now'] <=75){
			$prirustek = 4;
		}
		else if($zeme['infrastruktura_now'] <=85){
			$prirustek = 5;
		}
		else{
			$prirustek = 6;
		}
		
		$query = "UPDATE in_game_zeme SET infrastruktura_now = 
						infrastruktura_now + ".$zvyseni.",lze_povolat = 
						lze_povolat + ".$prirustek."
						WHERE id_zeme='".$zeme['id_zeme']."' 
						AND id_ligy='".$zeme['id_ligy']."'";
		$db->DbQuery( $query );
	}

}

?>