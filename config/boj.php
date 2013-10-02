<?php
	//funkce pro sousedy
	require_once($DIR_CONFIG . "mapa.php");
	require_once($DIR_CONFIG . "konstanty.php");
	require_once($DIR_CONFIG . "akce.php");
	
	
function Nahoda(){
	
	return rand();
}	

/**
 * Vrati pole s hodnotami jednotek.
 *
 * @param  $jednotky - bud tanky nebo pechota
 * @param  $id_general - pokud neni zadan, vezme se ten aktualniho hrace
 */
function VratAtributyJednotek( $jednotky, $id_general = null, $id_zeme = null ){
	global $db, $users_class, $error, $CONST;
	if (!isset($id_general) || isset($id_zeme) ){
		$query = "SELECT id_general, id_liga FROM in_game_hrac WHERE id_hrac='".$users_class->user_id()."'";
		$res = $db->Query( $query );
		$row = $db->GetFetchRow( $res );		
		$id_general = $row[0];
		$id_ligy = $row[1];
	}
	
	$sleva_pechota = 0;
	$sleva_tanky = 0;
	if(isset( $id_zeme )){
		// polni nemocnice
		if (MaHracStavbuVZemi(10,$id_zeme,$id_ligy)){
			$sleva_pechota += $CONST["STAVBY_POLNI_NEMOCNICE_SLEVA_PECHOTA"];
		}
		// tylova opravna
		if (MaHracStavbuVZemi(19,$id_zeme,$id_ligy)){
			$sleva_tanky += $CONST["STAVBY_TYLOVE_OPRAVNY_SLEVA_TANKY"];
		}
		// mash
		if (MaHracStavbuVZemi(22,$id_zeme,$id_ligy)){
			$sleva_pechota += $CONST["STAVBY_MASH_SLEVA_PECHOTA"];
		}
	}

	$query = "SELECT * FROM generalove WHERE id='$id_general'";
	$res = $db->Query( $query );
	$row = $db->GetFetchAssoc( $res );

	switch ($jednotky)
	{
		case 'tanky':
			$pole["utok"] = $row['utok_tanky'];
			$pole["obrana"] = $row['obrana_tanky'];	
			$pole["pvo"] = $row['pvo_tanky'];
			$pole["suroviny"] = $row['suroviny_tanky'] - $sleva_tanky;
			$pole["palivo"] = $row['palivo_tanky'];
			$pole["zold_suroviny"] = $row['zold_suroviny_tanky'];
			$pole["zold_palivo"] = $row['zold_palivo_tanky'];
			break;
		case 'pechota':
			$pole["utok"] = $row['utok_pechota'];
			$pole["obrana"] = $row['obrana_pechota'];	
			$pole["pvo"] = $row['pvo_pechota'];
			$pole["suroviny"] = $row['suroviny_pechota'] - $sleva_pechota;
			$pole["palivo"] = $row['palivo_pechota'];
			$pole["zold_suroviny"] = $row['zold_suroviny_pechota'];
			$pole["zold_palivo"] = $row['zold_palivo_pechota'];
			break;
	}	

	return $pole;
	
}

/**
 * Vrati pocet postavene pechoty
 *
 * @param int $id_zeme - zeme ve ktere chceme vedet pocet pechoty
 * @param int $id_ligy - liga ve ktere zem je. Pokud je nevyplnena, pouzije se
 * zeme aktualne prihlaseneho uzivatele
 * @param int @odeslane - pokud je vyplnene na true, vrati pocet odeslanych jednotek
 * @return pocet pechoty/false
 */
function PocetPechotyVSektoru( $id_zeme, $id_ligy = null, $odeslane = null ){
	global $db, $users_class;
	
	if(!isset($id_zeme)){
		return false;
	}
	
	if(!isset($id_ligy)){
		$query = "SELECT id_liga FROM in_game_hrac WHERE id_hrac='".$users_class->user_id()."'";
		$res = $db->Query( $query );
		$row = $db->GetFetchRow( $res );		
		$id_ligy = $row[0];
	}
	
	$user_id = $users_class->user_id();
	
	$id_zeme = sqlsafe( $id_zeme );
	if ($odeslane){
		$query = "SELECT pechota_odeslano FROM in_game_zeme WHERE id_zeme='$id_zeme' 
			and id_ligy='$id_ligy'";
	}
	else{
		$query = "SELECT pechota FROM in_game_zeme WHERE id_zeme='$id_zeme'
			and id_ligy='$id_ligy'";
	}
		$res = $db->Query( $query );
		$row = $db->GetFetchRow( $res );	
	
	return $row[0];
}
	
/**
 * Vrati pocet postavene tanku
 *
 * @param int $id_zeme - zeme ve ktere chceme vedet pocet tanku
 * @param int $id_ligy - liga ve ktere zem je. Pokud je nevyplnena, pouzije se
 * zeme aktualne prihlaseneho uzivatele
 * @param int @odeslane - pokud je vyplnene na true, vrati pocet odeslanych jednotek
 * @return pocet tanku/false
 */
function PocetTankuVSektoru( $id_zeme, $id_ligy = null, $odeslane = null ){
	global $db, $users_class;
	
	if(!isset($id_zeme)){
		return false;
	}
	
	if(!isset($id_ligy)){
		$query = "SELECT id_liga FROM in_game_hrac WHERE id_hrac='".$users_class->user_id()."'";
		$res = $db->Query( $query );
		$row = $db->GetFetchRow( $res );		
		$id_ligy = $row[0];
	}
	
	$user_id = $users_class->user_id();
	
	$id_zeme = sqlsafe( $id_zeme );
	if ($odeslane){
		$query = "SELECT tanky_odeslano FROM in_game_zeme WHERE id_zeme='$id_zeme' 
			and id_ligy='$id_ligy'";
	}
	else{
		$query = "SELECT tanky FROM in_game_zeme WHERE id_zeme='$id_zeme' 
			and id_ligy='$id_ligy'";
	}
	
		$res = $db->Query( $query );
		$row = $db->GetFetchRow( $res );	
	
	return $row[0];
}
	
/**
 * Pocita snizeni utoku pokud je hrac v minusu se zisky
 *
 * @param  $id_zeme jejiz vlastnik utok posila
 * @param  $id_ligy
 * @param  $id_vlastnik zadava se jen pri volani z prepoctu - aby byl vlastnik aktualni
 * @return pole - vysledkem se pronasobi utok
 */
function OmezeniZeZapornychZdroju( $id_zeme, $id_ligy, $id_vlastnik = null ){
	global $db, $CONST; 
	$text = "Nedostatek zdrojů";
	$pechota = 1;
	$tanky = 1;
	
	if(!isset($id_vlastnik)){
		$query = "SELECT id_vlastnik FROM in_game_zeme WHERE 
				id_zeme='$id_zeme' and id_ligy='$id_ligy'";
		$res1 = $db->Query( $query );
		$vl = $db->GetFetchRow( $res1 );
		$id_vlastnik = $vl[0];
	}
	
	if(!isset($id_vlastnik)){
		$attrb['snizeni_pechota'] = $pechota;
		$attrb['snizeni_tanky'] = $tanky;
		$attrb['text'] = $text;
		return $attrb;
	}
	
	$query = "SELECT * FROM in_game_hrac WHERE 
			id_hrac='$id_vlastnik'";
	$res1 = $db->Query( $query );
	$zisky = $db->GetFetchAssoc( $res1 );
	
	if ($zisky['suroviny'] < 0){
		$pechota = 1 + $zisky['suroviny'] / 2000;	
	}
	
	if ( $zisky['palivo'] < 0){
		$tanky = 1 + $zisky['palivo'] / 1000;
		if ($zisky['suroviny'] < 0){
			$tanky += $zisky['suroviny'] / 1000;
		}
	}
	
	if ($pechota < 0){
		$pechota = 0;
	}
	
	if ($tanky < 0){
		$tanky = 0;
	}

	$attrb['snizeni_pechota'] = $pechota;
	$attrb['snizeni_tanky'] = $tanky;
	$attrb['text'] = $text;

	return $attrb;	
}

/**
 * Vypocita silu utoku z danych parametru
 *
 * @param  $pechota
 * @param  $tanky
 * @param  $id_ligy
 * @param  $id_zeme_odkud
 * @param  $id_zeme_kam
 * @param $informace - pokud je true, funkce vraci dvouprvkove pole - s cislem a
 * a informacema a utoku
 * 
 * @return int - sila utoku
 */
function VypocitejSiluUtoku($pechota, $tanky, $id_ligy, $id_zeme_odkud, $id_zeme_kam, $informace = false){
	global $db, $CONST; 
	$text = "<table>";
	$suma = 0;
	
	//sila utocicich jednotek
	$atrb_tanky = VratAtributyJednotek("tanky");
	$atrb_pechota = VratAtributyJednotek("pechota");
	$pechota = sqlsafe( $pechota );
	$tanky = sqlsafe( $tanky );
	$id_ligy = sqlsafe( $id_ligy );
	$id_zeme_odkud = sqlsafe( $id_zeme_odkud );
	$id_zeme_kam = sqlsafe( $id_zeme_kam );
	
	$suma += $atrb_pechota['utok'] * $pechota;
	$suma += $atrb_tanky['utok'] * $tanky;
	$text .= "<tr><td>Útok pěchoty a tanků</td><td>" . $suma . "</td></tr>";
	
	//omezeni vlivem zapornych zdroju
	$at = OmezeniZeZapornychZdroju( $id_zeme_odkud, $id_ligy );
	
	$z_p = floor($atrb_pechota['utok'] * $pechota * (1 - $at['snizeni_pechota']));
	$z_t = floor($atrb_tanky['utok'] * $tanky * (1 - $at['snizeni_tanky']));
	
	if(($z_p + $z_t) > 0){
	$text .= "<tr><td>Nedostatek zdrojů</td><td>-" . ($z_p + $z_t) . "</td></tr>";
	$suma -= $z_p + $z_t;
	}
	
	$tanky_bonus = 0;
	$baterie = 0;
	$katusa = 0;
	$sniperi_bonus = 0;
	$pruchod_bonus = 0;
	$vliv = 1;
	//efekt tanku
	$vliv *= 1 + $tanky * $CONST["UCINEK_TANKU_UTOK"] / 100;
	$tanky_bonus = round( $suma * $tanky * $CONST["UCINEK_TANKU_UTOK"] / 100 );
	
	//vypisu pouze nenulovy bonus
	if ($tanky_bonus != 0){
		$text .= "<tr><td>Útočný bonus tanků</td><td>" . 
		round( $suma * $tanky * $CONST["UCINEK_TANKU_UTOK"] / 100 )
		. "</td></tr>";
	}
	//efekt stavby delostrelecka baterie
	if (MaHracStavbuVZemi(17,$id_zeme_odkud,$id_ligy)){
		$vliv += $CONST["STAVBY_DELOSTRELECKA_BATERIE_BONUS"];
		$baterie = round( $suma * $CONST["STAVBY_DELOSTRELECKA_BATERIE_BONUS"] );
		$text .= "<tr><td>Dělostřelecká baterie</td><td>" . 
		round( $suma * $CONST["STAVBY_DELOSTRELECKA_BATERIE_BONUS"] )
		. "</td></tr>";
	}
	
	//efekt stavby katusa
	if (MaHracStavbuVZemi(16,$id_zeme_odkud,$id_ligy)){
		$vliv += $CONST["STAVBY_KATUSA_BONUS"];
		$katusa = round( $suma * $CONST["STAVBY_KATUSA_BONUS"] );
		$text .= "<tr><td>Kaťuša</td><td>" . 
		round( $suma * $CONST["STAVBY_KATUSA_BONUS"] )
		. "</td></tr>";
	}
	
	//vyhodnoceni vlivu utok sniperu
	$query = "SELECT count(*) FROM in_game_vlivy_podpora WHERE 
			id_podpora=9 and id_zeme='$id_zeme_odkud' and id_ligy='$id_ligy'";
	$res1 = $db->Query( $query );
	$sniperi = $db->GetFetchRow( $res1 );
	
	if($sniperi[0]>0){
		$vliv -= $CONST["PODPORA_SNIPERI_BONUS"];
		$sniperi_bonus = round( $suma * $CONST["PODPORA_SNIPERI_BONUS"] );
		$text .= "<tr><td>Útok sniperů</td><td>-" . 
		round( $suma * $CONST["PODPORA_SNIPERI_BONUS"] )
		. "</td></tr>";
	}
	
	//vyhodnoceni utoku pres pruchod
	$res34 = $db->Query("SELECT * FROM `sousede` where 
		(zeme1='$id_zeme_odkud' and zeme2='$id_zeme_kam') or
		(zeme2='$id_zeme_odkud' and zeme1='$id_zeme_kam')
		");
	if (!$sousede = $db->GetFetchAssoc( $res34 ))
	{
		
		$pruchod_bonus = round( $suma * $CONST["POSTIH_UTOK_PRES_PRUCHOD"] );
		$text .= "<tr><td>Taktický přesun</td><td>-" . 
		round( $suma * $CONST["POSTIH_UTOK_PRES_PRUCHOD"] )
		. "</td></tr>";
	}

	$celkem = $suma + $tanky_bonus + $baterie + $katusa - $sniperi_bonus - $pruchod_bonus;
	
	if(JeZemObklicena( $id_zeme_odkud, $id_ligy, MajitelZeme( $id_zeme_odkud, $id_ligy))){
		$text .= "<tr><td>Obklíčení</td><td>-" . 
		round( $celkem * $CONST["OBKLICENI_POSTIH_UTOK"])
		. "</td></tr>";
		
		$celkem = round( $celkem * $CONST["OBKLICENI_POSTIH_UTOK"]);
	}	
	
	$text .= "<tr><td>Celkem</td><td>" . 
			 $celkem .
			"</td></tr>";
			 
	$text .= "</table>";
	
	$attrb['info'] = $text;
	$attrb['sila'] = $celkem;
	
	if ($informace){
		return $attrb;	
	}
	else{
		return $celkem;
	}
}

/**
 * Funkce vrati cislo o kolik zvysi obranu zeme jeji sousedi. 
 *
 * @param unknown_type $id_zeme
 * @param unknown_type $id_ligy
 */
function VratPodporuObranySousednichZemi($id_zeme, $id_ligy, $id_majitel, $id_general){
	global $db, $CONST;
	$id_zeme = sqlsafe($id_zeme);
	$id_ligy = sqlsafe($id_ligy);
	
	//dobyvana zem je neutralka
	if(!isset($id_majitel)){
		return 0;
	}
	
	$bonus = 0;
	$atrb_tanky = VratAtributyJednotek("tanky", $id_general);
	$atrb_pechota = VratAtributyJednotek("pechota", $id_general);
	
	$query = "SELECT pechota, tanky
				FROM in_game_zeme
				WHERE  id_vlastnik='$id_majitel' and id_ligy='$id_ligy' and id_zeme IN 
				(
				SELECT zeme2 FROM sousede WHERE zeme1='$id_zeme'
				)";
	$res = $db->Query( $query );
	while ($row = $db->GetFetchAssoc( $res )){
		$bonus += $row['pechota'] * $atrb_pechota['obrana'];
		$bonus += $row['tanky'] * $atrb_tanky['obrana'];
		
	}		
	
	return round ($bonus * $CONST['PODPORA_SOUSEDNICH_ZEMI_DO_OBRANY']);
}

/**
 * testuje zda zem sousedi s jinou dalsi hracovou zemi. Pokud je zemi mene nez 2, tak se nepocita
 * 
 * @return true/false
 */
function JeZemObklicena( $id_zeme, $id_ligy, $id_majitel){
	global $db, $CONST;
	
	$id_zeme = sqlsafe($id_zeme);
	$id_ligy = sqlsafe($id_ligy);
	
	//jestli neni zem hrace, tak neni obklicena :)
	if(!isset($id_majitel)){
		return false;
	}
	
	$query = "SELECT count(*)
				FROM in_game_zeme
				WHERE  id_ligy='$id_ligy' and id_vlastnik='$id_majitel'";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	if( !isset($row) || $row[0]<=2 ){
		return false;
	}
	
	//zjistime typ scenare
	$query = "SELECT typ
				FROM ligy
				WHERE  id='$id_ligy'";
	$res = $db->Query( $query );
	$row = $db->GetFetchAssoc( $res );
	$typ_ligy = $row['typ'];
	if( $typ_ligy == 'team' ){
		//jestli je tymovy, zjistim si generala
		$general_majitele = VratAtributHrace( 'id_general' ,$id_majitel);
	}
	
	$query = "SELECT igz.id_vlastnik, igh.id_general 
				FROM in_game_zeme AS igz JOIN in_game_hrac AS igh ON
				igz.id_vlastnik=igh.id_hrac
				WHERE  id_ligy='$id_ligy' and id_zeme IN 
				(
				SELECT zeme2 FROM sousede WHERE zeme1='$id_zeme'
				)";
	$res = $db->Query( $query );
	
	while ($row = $db->GetFetchAssoc( $res )){
		if( $typ_ligy != "team" ){
			if( $row['id_vlastnik']==$id_majitel ){
				return false;
			}
		}
		else{
			if( $row['id_general']==$general_majitele ){
				return false;
			}
		}
		
	}		
	return true;
}

/**
 * Vypocita obranu zeme
 *
 * @param  $id_zeme - id zeme(to z tabulky zeme)
 * @param  $id_ligy
 * @return obrana zeme se vsim vsudy
 */
function VypocitejObranuZeme($id_zeme, $id_ligy, $informace = false){
	global $db, $CONST;
	$text = "<table>";
	$id_zeme = sqlsafe( $id_zeme );
	$id_ligy = sqlsafe( $id_ligy );
	$query = "SELECT distinct igz.id_vlastnik, igz.pechota, igz.tanky, igh.id_general, z.bonus_obrana
				FROM zeme AS z JOIN in_game_zeme AS igz LEFT JOIN in_game_hrac AS igh ON
				igz.id_vlastnik=igh.id_hrac AND z.id=igz.id_zeme	
				WHERE igz.id_zeme='$id_zeme' and igz.id_ligy='$id_ligy' order by id_general desc";

	$res = $db->Query( $query );
	$row = $db->GetFetchAssoc( $res );		
	$id_majitel = $row['id_vlastnik'];
	$bonus_obrana = $row['bonus_obrana'];
	$pechota = $row['pechota'];
	$tanky = $row['tanky'];
	$id_general = $row['id_general'];
	if ($id_general== null){
		//NEUTRALKY
		$id_general = 1;
	}
	$atrb_tanky = VratAtributyJednotek("tanky", $id_general);
	$atrb_pechota = VratAtributyJednotek("pechota", $id_general);
	
	$aditivni_bonus = 0;
	$vliv = 1;
	$obrana = 0;
	
	//pechota
	$obrana += $pechota * $atrb_pechota['obrana'];
	
	//tanky
	$obrana += $tanky * $atrb_tanky['obrana'];
	$text .= "<tr><td>Obrana pěchoty a tanků</td><td>" . $obrana . "</td></tr>";
	
	//omezeni vlivem zapornych zdroju
	$at = OmezeniZeZapornychZdroju( $id_zeme, $id_ligy );

	$z_p = floor($atrb_pechota['obrana'] * $pechota * ( 1 - $at['snizeni_pechota']));
	$z_t = floor($atrb_tanky['obrana'] * $tanky * ( 1 - $at['snizeni_tanky']));

	if(($z_p + $z_t) > 0){
	$text .= "<tr><td>Nedostatek zdrojů</td><td>-" . ($z_p + $z_t) . "</td></tr>";
	$obrana -= $z_p + $z_t;
	}
	
	//efekt tanku
	/*$vliv *= 1 + $tanky * $CONST["UCINEK_TANKU_OBRANA"] / 100;
	$text .= "<tr><td>Bojový efekt tanků</td><td>" . 
	round ($obrana * $tanky * $CONST["UCINEK_TANKU_OBRANA"] / 100 )
	. "</td></tr>";*/
	
	//efekt stavby minova pole
	if (MaHracStavbuVZemi(13,$id_zeme,$id_ligy)){
		$aditivni_bonus += $CONST["STAVBY_MINOVE_POLE_POMOC_DO_OBRANY"];
		$text .= "<tr><td>Minové pole</td><td>" . $CONST["STAVBY_MINOVE_POLE_POMOC_DO_OBRANY"] . "</td></tr>";
	}
	
	//efekt stavby bunkr
	if (MaHracStavbuVZemi(11,$id_zeme,$id_ligy)){
		$aditivni_bonus += $CONST["STAVBY_BUNKR_POMOC_DO_OBRANY"];
		$text .= "<tr><td>Bunkr</td><td>" . $CONST["STAVBY_BUNKR_POMOC_DO_OBRANY"] . "</td></tr>";
	}
		
	//vyhodnoceni vlivu podpora - pruzkum
	$query = "SELECT count(*) FROM in_game_vlivy_podpora WHERE 
			id_podpora=6 and id_zeme='$id_zeme' and id_ligy='$id_ligy' and param1='vcera'";
	$res1 = $db->Query( $query );
	$pruzkum = $db->GetFetchRow( $res1 );

	if($pruzkum[0]>0){
		$vliv += $CONST["PODPORA_PRUZKUM_BONUS"];
		$text .= "<tr><td>Průzkum</td><td>" . 
		round($obrana* $CONST["PODPORA_PRUZKUM_BONUS"]) 
		. "</td></tr>";
	}
	
	// bonus ze sousednich zemi
	$sousedni_zeme = VratPodporuObranySousednichZemi($id_zeme,$id_ligy,$id_majitel,$id_general);
	$text .= "<tr><td>Podpora ze sousedních sektorů</td><td>" . 
		$sousedni_zeme
		. "</td></tr>";
	
	$celkem = round ($obrana * $vliv + $aditivni_bonus + $sousedni_zeme);
	$koncove = round( $celkem * (1 + $bonus_obrana / 100));
	
	$text .= "<tr><td>Bonus sektoru</td><td>" . 
		round( $celkem * ( $bonus_obrana / 100))
		. "</td></tr>";

	if(JeZemObklicena( $id_zeme, $id_ligy, $id_majitel)){
		$text .= "<tr><td>Obklíčení</td><td>-" . 
		round( $koncove * $CONST["OBKLICENI_POSTIH_OBRANA"])
		. "</td></tr>";
		
		$koncove = round( $koncove * $CONST["OBKLICENI_POSTIH_OBRANA"]);
	}	
		
	$text .= "<tr><td>Celkem</td><td>" . 
			 $koncove .
			"</td></tr>";
			 
	$text .= "</table>";
	
	$attrb['text'] = $text;
	$attrb['cislo'] = $koncove;
	if ($informace){
		return $attrb;	
	}
	else{
		return $koncove;
	}
}

/**
 * fuknce vraci obranu dane zeme.
 *
 * @param unknown_type $id_zeme
 * @param unknown_type $id_ligy
 * @param unknown_type $informace - pokud je true, vraci i textovy popis
 * @return bud vraci jen PVO nebo pole s textem a PVO
 */
function VypocitejPVOZeme( $id_zeme, $id_ligy, $informace = false){
	global $db, $CONST;

	$id_zeme = sqlsafe( $id_zeme );
	$id_ligy = sqlsafe( $id_ligy );
	$query = "SELECT bonus_pvo_obrana FROM zeme WHERE id='$id_zeme'";

	$res = $db->Query( $query );
	$row = $db->GetFetchAssoc( $res );		
	$bonus_obrana = $row['bonus_pvo_obrana'];
	
	$vliv = 1;
	$aditivni_vliv = 0;
	$obrana = 0;
	
	$query = "SELECT pvo_stanice FROM `in_game_zeme`
	 where (id_zeme='$id_zeme')and
	 (id_ligy='$id_ligy')";
	
	$res5 = $db->Query( $query );
	$stan_bon = $db->GetFetchRow($res5);
	$obrana = $stan_bon[0] * $CONST["PVO_STANICE_BONUS"];

	//je postavena PVO brigada?
	if (MaHracStavbuVZemi(24,$id_zeme,$id_ligy)){
		$obrana *= $CONST["STAVBY_BRIGADA"];
	}
	
	//efekt stavby FLAK - protiletadlova dela
	if (MaHracStavbuVZemi(18,$id_zeme,$id_ligy)){
		$aditivni_vliv += $CONST["STAVBY_FLAK_BONUS"];
	}
		
	//vyhodnoceni vlivu stihaci hlidka
	$query = "SELECT sila FROM in_game_vlivy_letecke_akce WHERE 
			id_letecke_akce=1 and id_zeme='$id_zeme' and id_ligy='$id_ligy'
			order by sila DESC";
	$res1 = $db->Query( $query );
	$sila_t = $db->GetFetchRow( $res1 );

	if(isset($sila_t[0])){
		//vliv stihaci hlidky je 49-70% LS
		$aditivni_vliv += ceil( $sila_t[0] * 0.7 );
	}
	$celkem = round ($obrana * $vliv) + $aditivni_vliv;
	
	$koncove = round( $celkem * (1 + $bonus_obrana / 100));
	
	//zastarale, skoro jiste zbytecne(ale mozna to nekde takove volani ocekava)
	$attrb['text'] = "";
	$attrb['cislo'] = $koncove;

	if ($informace){
		return $attrb;
	}
	else{
		return $celkem;
	}
	
}

function NaplnPruchody( $id_zeme, $id_ligy ){
	global $db, $users_class;
	
	$optiony = "";
	$id_hrac = $users_class->user_id();
	$res_hl = $db->Query("SELECT zeme2 FROM `sousede` where zeme1='". $id_zeme ."' AND
			zeme2 NOT IN (SELECT id_zeme FROM in_game_zeme 
					WHERE id_vlastnik='$id_hrac');");
	while ($krok = $db->GetFetchRow( $res_hl )){
		$test = 0;
		$query = "SELECT nazev FROM zeme where id='$krok[0]'";
		$res = $db->Query( $query );
		$row = $db->GetFetchRow( $res );	
		
		$query = "SELECT * FROM in_game_pruchody where id_ligy='$id_ligy' AND
			(
			(id_zeme_odkud='".$id_zeme."' and id_zeme_kam='".$krok[0]."')
			or
			(id_zeme_kam='".$id_zeme."' and id_zeme_odkud='".$krok[0]."')
			)
			";

		$res = $db->Query( $query );
		$test = $db->GetFetchRow( $res );	
		if ($test!=0){
			continue;
		}
		
		$optiony .= '<option value="'.$krok[0].'">'.$row[0].'</option>';	
	}

	return $optiony;
}

/**
 * Fuknce vrati string optionu pro naplneni FORM na odeslani utoku
 *
 * @param  $id_zeme
 * @param  $id_liga
 * @param  $id_zeme_kam - Kdera zem ma byt predvybrana
 * @return unknown
 */
function NaplnCiloveZemeProUtok( $id_zeme, $id_liga, $id_zeme_kam = 0){
	global $db;
	
	$array = VratVsechnyCiloveZemeProUtok( $id_zeme, $id_liga );
	$optiony = "";
	
	while( $krok = array_pop($array) ){
		$test = 0;
		$query = "SELECT nazev FROM zeme where id='$krok[0]'";
		$res = $db->Query( $query );
		$row = $db->GetFetchRow( $res );	
		
		//predvyplneni		
		if( $id_zeme_kam == $krok[0]){
			$optiony .= '<option selected value="'.$krok[0].'">'.$row[0].'</option>';
		}
		else{
			$optiony .= '<option value="'.$krok[0].'">'.$row[0].'</option>';
		}
	}

	return $optiony;
}


function AM_OdvolaniUtoku($id_utok){
	global $users_class, $db;
	$id_utok = sqlsafe( $id_utok );
	$id_hrac = $users_class->user_id();
	
	$query = "SELECT * FROM in_game_utoky WHERE id='$id_utok'";
	$res = $db->Query( $query );
	
	if ($row = $db->GetFetchAssoc( $res )){
		//naplneni promenych
		$id_zeme_odkud = $row['id_zeme_odkud'];
		$id_ligy = $row['id_ligy'];
		$pechota = $row['pechota'];
		$tanky = $row['tanky'];
		
		//smazani utoku
		$query = "DELETE FROM in_game_utoky WHERE id='$id_utok'";
		$db->DbQuery( $query );
		
		//vraceni jednotek :)
		$query = "SELECT pechota, tanky, pechota_odeslano, tanky_odeslano FROM in_game_zeme WHERE id_zeme='$id_zeme_odkud' 
		and id_ligy='$id_ligy'";
		$res2 = $db->Query( $query );
		$row2 = $db->GetFetchRow( $res2 );	
		
		$puvodni_pechota = $row2[0] + $pechota;
		$puvodni_tanky = $row2[1] + $tanky;
		$pechota_odeslano = $row2[2] - $pechota;
		$tanky_odeslano = $row2[3] - $tanky;
		
		$query = "UPDATE `in_game_zeme` SET 
					 
					`pechota`= '$puvodni_pechota', 
					`tanky` = '$puvodni_tanky',
					`pechota_odeslano` = '$pechota_odeslano',
					`tanky_odeslano` = '$tanky_odeslano'					 
					 WHERE id_zeme='$id_zeme_odkud' and id_ligy='$id_ligy'
					;";
		$db->DbQuery( $query );
	}
}


function AM_RozkazKUtoku($id_zeme_odkud, $id_zeme_kam, $pechota, $tanky, $typ, $informace = false){
	global $users_class, $db;
		
	$pole['error'] = "";
	$pole['informace'] = "";
	
	$id_zeme_odkud = sqlsafe( $id_zeme_odkud );
	$id_zeme_kam = sqlsafe( $id_zeme_kam );
	$pechota = sqlsafe( $pechota );
	$tanky = sqlsafe( $tanky );
	$typ = sqlsafe( $typ );
	$id_hrac = $users_class->user_id();
	
	$id_ligy = JeUzivatelVLize( $id_hrac );
	
	if (!is_numeric($pechota)||$pechota<0||!is_numeric($tanky)||$tanky<0){
		$pole['error'] = "neplatný počet jednotek";
		return $pole;
	}
	
	$error = '';
	if (!JePovolenaAkce($typ, $id_ligy, $error, $id_hrac, $id_zeme_kam )){
		$pole['error'] = $error;
		return $pole;
	}
	
	//test platnosti cilove zeme pro utok
	$pole = VratVsechnyCiloveZemeProUtok($id_zeme_odkud, $id_ligy);
	$platny = false;
	while( $krok = array_pop($pole) ){
		if( $id_zeme_kam == $krok[0]){
			$platny = true;
			break;
		}
	}
	
	if (!$platny){
		$pole['error'] = "Cílová zem pro útok není platná";
		return $pole;
	}
	
	// overeni poctu jednotek
	$query = "SELECT pechota, tanky, pechota_odeslano, tanky_odeslano FROM in_game_zeme WHERE id_zeme='$id_zeme_odkud' 
		and id_ligy='$id_ligy'";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );	
	if ($row[0] < $pechota){
		$pechota = $row[0];
	}
	if ($row[1] < $tanky){
		$tanky = $row[1];
	}
	
	//test utoku bez jednotek
	if ($pechota == 0 && $tanky == 0){
		return;
	}
	
	$puvodni_pechota = $row[0] - $pechota;
	$puvodni_tanky = $row[1] - $tanky;
	$pechota_odeslano = $row[2] + $pechota;
	$tanky_odeslano = $row[3] + $tanky;
	
	$attrb = VypocitejSiluUtoku($pechota, $tanky, $id_ligy, $id_zeme_odkud, $id_zeme_kam, $informace);
	$sila = $attrb['sila'];
	$pole['informace'] = $attrb['info'];
	
	$random = Nahoda();
	
	// zapsani utoku do DB
	$query = "INSERT INTO `in_game_utoky`  
					( `id_zeme_odkud`, `id_zeme_kam`, `id_ligy`,`id_vlastnik`,
					`pechota`, `tanky`, `sila`, `typ`, `random`) VALUES
					('$id_zeme_odkud', '$id_zeme_kam', '$id_ligy', '$id_hrac',
					 '$pechota', '$tanky', '$sila', '$typ','$random'
					);";
	$db->DbQuery( $query );
	
	// upraveni stavu jednotek v zemi_odkud
	$query = "UPDATE `in_game_zeme` SET 
					 
					`pechota`= '$puvodni_pechota', 
					`tanky` = '$puvodni_tanky',
					`pechota_odeslano` = '$pechota_odeslano',
					`tanky_odeslano` = '$tanky_odeslano'					 
					 WHERE id_zeme='$id_zeme_odkud' and id_ligy='$id_ligy'
					;";
	$db->DbQuery( $query );
	$pole['error'] = "";
	return $pole;
}

function VratUtokBezBonusu($id_zeme, $id_ligy){
	global $db, $CONST; 
	
	$id_zeme = sqlsafe( $id_zeme );
	$id_ligy = sqlsafe( $id_ligy );
	
	$text = "<table>";
	$suma = 0;
	
	$query = "SELECT pechota, tanky FROM in_game_zeme 
			WHERE id_zeme='$id_zeme' AND id_ligy='$id_ligy'";
	$res = $db->Query( $query );
	$at = $db->GetFetchAssoc( $res );
	
	//sila utocicich jednotek
	$atrb_tanky = VratAtributyJednotek("tanky");
	$atrb_pechota = VratAtributyJednotek("pechota");
	
	$suma += $atrb_pechota['utok'] * $at['pechota'];
	$suma += $atrb_tanky['utok'] * $at['tanky'];
	$text .= "<tr><td>Útok pěchoty a tanků </td><td>" . $suma . "</td></tr>";

	$text .= "</table>";
	return $text;
}

/**
 * Pouziti u AM, test zda zobrazit listboxy pro zobrazeni zadani letecke akce.
 *
 */
function ZobrazitFormulareSLetectvem( $id_liga ){
	global $db, $users_class, $ne_letectvo_pocasi;
	
	if (!$users_class->is_logged()){
		return 'Nepřihlášený uživatel.';	
	}
	
	if (!JeUzivatelVLize($users_class->user_id(), $id_liga)){
		return 'Nejste přihlášen v tomto scénáři.';
	}
	
	$user_id = $users_class->user_id();
	$res = $db->Query("SELECT letecka_sila FROM in_game_hrac WHERE id_hrac='$user_id'");
	$row = $db->GetFetchRow( $res );
	$letecka_sila = $row[0];
	
	//pocasi
	$res = $db->Query("SELECT id_pocasi_dnes FROM ligy WHERE id='$id_liga'");
	$row = $db->GetFetchRow( $res );
	$id_pocasi = $row[0];
	
	if(in_array($id_pocasi,$ne_letectvo_pocasi)){
		return 'Letectvo je v dnešním počasí nepoužitelné.';
	}
	else if (MaximalniPocetLeteckychUtoku( $user_id )<=
			AktualniPocetLeteckychUtoku( $user_id )){
		return 'Vyčerpal jste maximalní počet dnešních útoků.';			
	}
	else if($letecka_sila==0){
		return 'Nemáte leteckou sílu potřebnou k provedení útoku.';
	}
	else{
		return 'ano';
	}
	
}

/**
 * Pouziti u AM, test zda zobrazit listboxy pro zobrazeni zadani podpory.
 *
 */
function ZobrazitFormulareSPodporou( $id_liga){
	global $users_class;
	
	if (!$users_class->is_logged()){
		return 'Nepřihlášený uživatel.';	
	}
	
	if (!JeUzivatelVLize($users_class->user_id(), $id_liga)){
		return 'Nejste přihlášen v tomto scénáři.';
	}
	
	if(MaHracStavbu(15)){
		return 'ano';
	}
	else{
		return 'Nemáte postavenou stavbu vrchní velitelství, která je nutná
				pro plánování podpory z domova.';

	}
}


function MaxSilaLeteckehoUtoku( $letecka_sila ){
	if ($letecka_sila < 10){
		return 1;	
	}
	else if ($letecka_sila < 20){
		return 2;
	}
	else if ($letecka_sila < 30){
		return 3;
	}
	else {
		return 4;
	} 
}


function PocetLetist( $id_hrac ){
	global $db;
	// 1 a 2 jsou id staveb letist 
	$res = $db->Query( "SELECT count( DISTINCT igs.id_zeme) FROM in_game_stavby AS igs JOIN
			in_game_zeme AS igz ON igs.id_zeme=igz.id_zeme AND igz.id_ligy=igs.id_ligy
			 AND igz.id_vlastnik='$id_hrac'
			WHERE id_stavby=1 or id_stavby=2" );
	$row = $db->GetFetchRow( $res );
	return $row[0];
}

function PocetHangaru( $id_hrac ){
	global $db;
	// 3 jsou id hangaru
	$res = $db->Query( "SELECT count( DISTINCT igs.id_zeme) FROM in_game_stavby AS igs JOIN
			in_game_zeme AS igz ON igs.id_zeme=igz.id_zeme AND igz.id_ligy=igs.id_ligy
			 AND igz.id_vlastnik='$id_hrac'
			WHERE id_stavby=3" );
	$row = $db->GetFetchRow( $res );
	return $row[0];
}

/**
 * Pocet naplanovanych leteckych utoku tento den
 *
 * @param $id_hrac
 * @return int
 */
function AktualniPocetLeteckychUtoku( $id_hrac ){
	global $db;
	$res = $db->Query( "SELECT count( id_autor) FROM in_game_letecke_akce
						WHERE id_autor='$id_hrac'");
	$row = $db->GetFetchRow( $res );
	return $row[0];	
}

function MaximalniPocetLeteckychUtoku( $id_hrac ){
	global $db, $CONST, $omezeni_let_akci;
	$id_ligy = JeUzivatelVLize( $id_hrac );
	
	$suma = 0;
	//letiste
	$res = $db->Query( "SELECT count( DISTINCT igs.id_zeme) FROM in_game_stavby AS igs JOIN
			in_game_zeme AS igz ON igs.id_zeme=igz.id_zeme AND igz.id_vlastnik='$id_hrac'
			AND igz.id_ligy=igs.id_ligy
			WHERE id_stavby=1" );
	$row = $db->GetFetchRow( $res );
	$suma += $CONST["STAVBY_letiste"] * $row[0];
	
	//polni letiste
	$res = $db->Query( "SELECT count( DISTINCT igs.id_zeme) FROM in_game_stavby AS igs JOIN
			in_game_zeme AS igz ON igs.id_zeme=igz.id_zeme AND igz.id_vlastnik='$id_hrac'
			AND igz.id_ligy=igs.id_ligy
			WHERE id_stavby=2" );
	$row = $db->GetFetchRow( $res );
	$suma += $CONST["STAVBY_polni_letiste"] * $row[0];
	
	//hangar
	//letiste
	$res = $db->Query( "SELECT count( DISTINCT igs.id_zeme) FROM in_game_stavby AS igs JOIN
			in_game_zeme AS igz ON igs.id_zeme=igz.id_zeme AND igz.id_vlastnik='$id_hrac'
			AND igz.id_ligy=igs.id_ligy
			WHERE id_stavby=3" );
	$row = $db->GetFetchRow( $res );
	$suma += $CONST["STAVBY_hangar"] * $row[0];
	
	//pocasi
	$res = $db->Query("SELECT id_pocasi_dnes FROM ligy WHERE id='$id_ligy'");
	$row = $db->GetFetchRow( $res );
	$id_pocasi = $row[0];
	$suma = round($omezeni_let_akci[$id_pocasi] * $suma);
	
	return $suma;
}

/**
 * vrati maximalni LS v zavislosti na poctu letist
 *
 * @param unknown_type $id_hrac
 */
function MaximalniLeteckaSila( $id_hrac ){
	global $db, $CONST;
	
	//hangary
	$res = $db->Query( "SELECT count( DISTINCT igs.id_zeme) FROM in_game_stavby AS igs JOIN
			in_game_zeme AS igz ON igs.id_zeme=igz.id_zeme AND igz.id_vlastnik='$id_hrac'
			AND igz.id_ligy=igs.id_ligy
			WHERE id_stavby=3" );
	$row = $db->GetFetchRow( $res );
	$hangary = $row[0];
	
	//letiste
	$res = $db->Query( "SELECT count( DISTINCT igs.id_zeme) FROM in_game_stavby AS igs JOIN
			in_game_zeme AS igz ON igs.id_zeme=igz.id_zeme AND igz.id_vlastnik='$id_hrac'
			AND igz.id_ligy=igs.id_ligy
			WHERE id_stavby=1 OR id_stavby=2" );
	$row = $db->GetFetchRow( $res );
	
	return $CONST["EFEKT_1_LETISTE_NA_LS"] * $row[0] + $CONST["EFEKT_1_HANGAR_NA_LS"]* $hangary;
}

function PoslatUtokNaBerlin( $id_ligy_old, $id_hrac = null, $typ_ligy = null ){
	global $users_class, $db, $CONST;
	
	if (!isset($id_user)){
		$id_user = $users_class->user_id();
	}
	
	$id_ligy = JeUzivatelVLize( $id_user );
	
	if ($id_ligy_old != $id_ligy){
		return;
	}
	
	//jesli uz utok poslal, tak dalsi neposilat
	$query = "SELECT count(*) FROM in_game_utoky WHERE 
	id_zeme_kam=45 AND id_vlastnik='".$id_user."'";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	if($row[0]>0){
		return;
	}
	
	//v tymovem scenari se naplati za vyhru
	if(!empty($typ_ligy) && $typ_ligy!='team'){
		if (!ZaplatSurovinyAPalivo($CONST['LIGA_SUROVINY_PRO_ZAVRENI'], $CONST['LIGA_PALIVO_PRO_ZAVRENI'])){
			return;
		}
	}
	
	$random = Nahoda();
	
	// zapsani utoku do DB
	$query = "INSERT INTO `in_game_utoky`  
					( `id_zeme_odkud`, `id_zeme_kam`, `id_ligy`,`id_vlastnik`,
					`pechota`, `tanky`, `sila`, `typ`, `random`) VALUES
					(45, 45, '$id_ligy', '$id_user',
					 0, 0, 0, 'vyhra','$random');";
	$db->DbQuery( $query );
		
}

/**
 * funkce vrati cislo, o kolik se zmensi utok pri zabiti urciteho poctu jednotek.
 *
 * @param unknown_type $pechota
 * @param unknown_type $tanky
 * @return nova sila utoku - zmensena
 */
function ZmenseniUtoku($pechota, $tanky, $puvodni_sila, $id_hrac){
	global $db,$CONST;
	$nova_sila = $puvodni_sila;
	//echo "puvodni sila:".$puvodni_sila;
	//ziskani atributu jednotek
	$query = "SELECT id_general FROM in_game_hrac WHERE id_hrac='$id_hrac'";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );		
	$id_general = $row[0];

	if ($tanky > 0){
		//echo "nici se tanku:".$tanky;
		$atrb_tanky = VratAtributyJednotek("tanky" ,$id_general);
		$uc = $atrb_tanky['utok'] * $tanky * 0.85;
		$nova_sila = ($puvodni_sila - $uc) / ( 1 + ($tanky * 0.85  / 100));
	}
	if ($pechota > 0){
		$atrb_pech = VratAtributyJednotek("pechota" ,$id_general);
		$nova_sila = $puvodni_sila - ($atrb_pech['utok'] * $pechota * 0.85);
	}
	
	if ($nova_sila < 0){
		$nova_sila = 0;
	}
	//echo "nova sila:".$nova_sila."<br />";
	return round($nova_sila); 
}



?>