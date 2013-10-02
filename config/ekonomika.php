<?php
	// fce je uzivatel v lize
	require_once($DIR_CONFIG . "formulare.php");
	
	require_once($DIR_CONFIG . "stavby.php");
	
	require_once($DIR_CONFIG . "konstanty.php");
	
/**
 * funkce vrati suroviny, ktere vydela zem za jeden klik
 *
 * @param int $id_zeme - id zeme
 */
function SurovinyZaKolo( $id_zeme ){
	global  $users_class, $db, $CONST;
	
	$id_zeme = sqlsafe ( $id_zeme );
	$suma = 0;
	
	if (!isset($id_user)){
		$id_user = $users_class->user_id();
	}
	$id_ligy = JeUzivatelVLize( $id_user );
	
	//zasobovaci sklad
	$query = "SELECT * FROM in_game_stavby WHERE id_zeme='$id_zeme' and
				id_ligy='$id_ligy' and id_stavby=4";
	$res = $db->Query( $query );
	if ($sklad = $db->GetFetchAssoc( $res )){
		$suma += $CONST["STAVBY_zasobovaci_sklad"];
	}
	
	//municni sklad
	$query = "SELECT * FROM in_game_stavby WHERE id_zeme='$id_zeme' and
				id_ligy='$id_ligy' and id_stavby=5";
	$res = $db->Query( $query );
	if ($sklad = $db->GetFetchAssoc( $res )){
		$suma += $CONST["STAVBY_municni_sklad"];
	}
	
	//zeleznicni prekladiste
	$query = "SELECT * FROM in_game_stavby WHERE id_zeme='$id_zeme' and
				id_ligy='$id_ligy' and id_stavby=8";
	$res = $db->Query( $query );
	if ($sklad = $db->GetFetchAssoc( $res )){
		$suma += $CONST["STAVBY_zeleznicni_prekladiste_suroviny"];
	}
	
	//centralni skladiste
	if (MaHracStavbu(9)){
		$suma = round($suma * $CONST["STAVBY_centralni_skladiste"]);
	}
	
	//vyhodnoceni vlivu  zasobovani
	$query = "SELECT count(*) FROM in_game_vlivy_podpora WHERE 
			id_podpora=5 and id_zeme='$id_zeme' and id_ligy='$id_ligy'";
	$res1 = $db->Query( $query );
	$zasobovani = $db->GetFetchRow( $res1 );

	$vliv = 0;
	if($zasobovani[0]>0){
		$vliv = $CONST["PODPORA_zasobovani_ze_vzduchu"];
	}else {
		$vliv = 1;
	}
	
	//vyhodnoceni vlivu sabotaze
	$query = "SELECT count(*) FROM in_game_vlivy_podpora WHERE 
			id_podpora=10 and id_zeme='$id_zeme' and id_ligy='$id_ligy'";
	$res1 = $db->Query( $query );
	$sb = $db->GetFetchRow( $res1 );
	if($sb[0]>0){
		$sabotaz = 1 - $CONST["PODPORA_SABOTAZ"];
	}else {
		$sabotaz = 1;
	}
	
	//infrastruktura
	$infrastruktura = VratBonusInfrastruktury($id_zeme, $id_ligy);
	
	//bonus sektoru
	$query = "SELECT bonus_suroviny FROM zeme WHERE id='$id_zeme'";
	$res = $db->Query( $query );
	if ($zdroje = $db->GetFetchAssoc( $res )){
		$vliv += $zdroje['bonus_suroviny'] / 100;
	}
	
	return floor($suma * $vliv * $infrastruktura * $sabotaz);
}

/**
 * funkce vrati palivo, ktere vydela zem za jeden klik
 *
 * @param int $id_zeme - id zeme
 */
function PalivoZaKolo( $id_zeme ){
	global  $users_class, $db, $CONST;
	
	$id_zeme = sqlsafe ( $id_zeme );
	$suma = 0;
	
	if (!isset($id_user)){
		$id_user = $users_class->user_id();
	}
	$id_ligy = JeUzivatelVLize( $id_user );
	
	//tankovaci stanice
	$query = "SELECT * FROM in_game_stavby WHERE id_zeme='$id_zeme' and
				id_ligy='$id_ligy' and id_stavby=6";
	$res = $db->Query( $query );
	if ($sklad = $db->GetFetchAssoc( $res )){
		$suma += $CONST["STAVBY_tankovaci_stanice"];
	}
	
	//sklad pohonych hmot
	$query = "SELECT * FROM in_game_stavby WHERE id_zeme='$id_zeme' and
				id_ligy='$id_ligy' and id_stavby=7";
	$res = $db->Query( $query );
	if ($sklad = $db->GetFetchAssoc( $res )){
		$suma += $CONST["STAVBY_sklad_pohonych_hmot"];
	}
	
	//zeleznicni prekladiste
	$query = "SELECT * FROM in_game_stavby WHERE id_zeme='$id_zeme' and
				id_ligy='$id_ligy' and id_stavby=8";
	$res = $db->Query( $query );
	if ($sklad = $db->GetFetchAssoc( $res )){
		$suma += $CONST["STAVBY_zeleznicni_prekladiste_palivo"];
	}
	
	//centralni skladiste
	if (MaHracStavbu(9)){
		$suma = round($suma * $CONST["STAVBY_centralni_skladiste"]);
	}
	
	//vyhodnoceni vlivu letecke zasobovani
	$query = "SELECT count(*) FROM in_game_vlivy_podpora WHERE 
			id_podpora=5 and id_zeme='$id_zeme' and id_ligy='$id_ligy'";
	$res1 = $db->Query( $query );
	$zasobovani = $db->GetFetchRow( $res1 );

	if($zasobovani[0]>0){
		$vliv = $CONST["PODPORA_zasobovani_ze_vzduchu_palivo"];
	}else {
		$vliv = 1;
	}
	
	//vyhodnoceni vlivu sabotaze
	$query = "SELECT count(*) FROM in_game_vlivy_podpora WHERE 
			id_podpora=10 and id_zeme='$id_zeme' and id_ligy='$id_ligy'";
	$res1 = $db->Query( $query );
	$sb = $db->GetFetchRow( $res1 );
	if($sb[0]>0){
		$sabotaz = 1 - $CONST["PODPORA_SABOTAZ"];
	}else {
		$sabotaz = 1;
	}
	
	//infrastruktura
	$infrastruktura = VratBonusInfrastruktury($id_zeme, $id_ligy);

	
	//bonus sektoru
	$query = "SELECT bonus_palivo FROM zeme WHERE id='$id_zeme'";
	$res = $db->Query( $query );

	if ($zdroje = $db->GetFetchAssoc( $res )){
		$vliv += $zdroje['bonus_palivo'] / 100;
	}
	
	return floor( $suma * $infrastruktura * $vliv * $sabotaz);
}

/**
 * funkce vrati body vlivu, ktere vydela zem za jeden klik
 *
 * @param int $$id_zeme - id zeme
 */
function BodyVlivuZaKolo( $id_zeme, $id_ligy = null ){
	global $db, $CONST;
	$id_zeme = sqlsafe( $id_zeme ); 
	if( !isset($id_ligy ) ){
		$id_ligy = JeUzivatelVLize();
	}
	
	//vyhodnoceni vlivu infiltrace
	$query = "SELECT count(*) FROM in_game_vlivy_podpora WHERE 
			id_podpora=11 and id_zeme='$id_zeme' and id_ligy='$id_ligy'";
	$res1 = $db->Query( $query );
	$inf = $db->GetFetchRow( $res1 );
	
	//infiltrace vynuluje prijmy ze sektoru
	if($inf[0]>0){
		return 0;
	}

	//vyhodnoceni vlivu propaganda
	$query = "SELECT count(*) FROM in_game_vlivy_podpora WHERE 
			id_podpora=4 and id_zeme='$id_zeme' and id_ligy='$id_ligy'";
	$res1 = $db->Query( $query );
	$propaganda = $db->GetFetchRow( $res1 );
	
	//vyhodnoceni vlivu ocernujici kampan
	$query = "SELECT count(*) FROM in_game_vlivy_podpora WHERE 
			id_podpora=3 and id_zeme='$id_zeme' and id_ligy='$id_ligy'";
	$res1 = $db->Query( $query );
	$kampan = $db->GetFetchRow( $res1 );
	
	//porovnani
	$vliv = 0;
	if($propaganda[0]>$kampan[0]){
		$vliv = $CONST["PODPORA_PROPAGANDA"];
	}else if($propaganda[0]<$kampan[0]){
		$vliv = $CONST["PODPORA_ocernujici_kampan"];
	}
	
	//ziskani bodu vlivu
	$query = "SELECT body_vlivu FROM zeme WHERE id='$id_zeme'";
	$resZ = $db->Query( $query );
	
	if ($body = $db->GetFetchAssoc( $resZ )){
		return round ($body['body_vlivu'] + $vliv);
	}
	else{
		return 0;
	}
}

/**
 * funkce vrati suroviny, ktere vydela hrac ze vsech zemi za 1 kolo
 *
 * @param int $id_hrac - pokud neni zadan vezme se aktualni
 * @return pocet surovin za kolo
 */
function CelkoveSurovinyZaKolo( $id_hrac = null ){
	global  $users_class, $db;
	
	if (!isset($id_hrac)){
		$id_hrac = $users_class->user_id();
	}
	
	$id_ligy = JeUzivatelVLize( $id_hrac );
	
	$suma = 0;
	
	$query = "SELECT * FROM in_game_zeme WHERE id_vlastnik='$id_hrac'";
	$seznam_res = $db->Query( $query );
	
	while ($zeme = $db->GetFetchAssoc( $seznam_res )){
		$suma += SurovinyZaKolo( $zeme['id_zeme'] );		
	}
	
	$pocasi_vliv = ProvozPocasi( $id_ligy );
	$suma = floor( $suma * $pocasi_vliv );
	
	$let = ProvozLetist( $id_hrac );
	
	$kz = KolapsVZasobovani( $id_hrac );
	$kolaps = floor ($suma * $kz );
	
	$stab = ProvozStab( $id_hrac, $kolaps);
	//echo $kz ."<br>";
	//echo  round($suma);
	return $suma - $kolaps - $let - $stab;
}

/**
 * funkce vrati palivo,ktere vydela hrac ze vsech zemi za 1 klik
 *
 * @param int $id_hrac - pokud neni zadan vezme se aktualni
 * @return pocet paliva za kolo
 */
function CelkovePalivoZaKolo( $id_hrac = null ){
	global  $users_class, $db;
	
	if (!isset($id_hrac)){
		$id_hrac = $users_class->user_id();
	}
	
	$id_ligy = JeUzivatelVLize( $id_hrac );
	
	$suma = 0;
	
	$query = "SELECT * FROM in_game_zeme WHERE id_vlastnik='$id_hrac'";
	$seznam_res = $db->Query( $query );
	
	while ($zeme = $db->GetFetchAssoc( $seznam_res )){
		$suma += PalivoZaKolo( $zeme['id_zeme'] );		
	}
	
	$pocasi_vliv = ProvozPocasi( $id_ligy );
	$suma = floor( $suma * $pocasi_vliv );
	
	$kz = KolapsVZasobovani( $id_hrac );
	
	return floor($suma * (1 - $kz));
}

/**
 * funkce vrati body vlivu, ktere vydela hrac ze vsech zemi za 1 klik
 *
 * @param int $id_hrac - pokud neni zadan vezme se aktualni
 * @return pocet body vlivu za kolo
 */
function CelkoveBodyVlivuZaKolo( $id_hrac = null ){
	global  $users_class, $db, $CONST;
	
	if (!isset($id_user)){
		$id_user = $users_class->user_id();
	}
	
	// jestli hrac nema VV, tak nic neziska
	if ( !MaHracStavbu(15) ){
		return 0;
	}
	
	$suma = 0;
	
	// stab rozvedky
	if ( MaHracStavbu(23) ){
		$suma = $CONST["STAVBY_STAB_BV"];
	}
	
	$query = "SELECT * FROM in_game_zeme WHERE id_vlastnik='$id_user'";
	$seznam_res = $db->Query( $query );
	
	while ($zeme = $db->GetFetchAssoc( $seznam_res )){
		$suma += BodyVlivuZaKolo( $zeme['id_zeme'] );		
	}
	return $suma;
}

/**
 * funkce vrati cenu opravy infrastruktury
 *
 * @param int $id_zeme - obyc
 * @param int $id_ligy 
 * @return int - pocet surovin potrebnych na opravu
 */
function CenaOpravyInfrastruktury( $id_zeme, $id_ligy ){
	global $db;
	
	$suma = 0;
	
	$query = "SELECT infrastruktura_now, oprava_infrastruktury FROM 
			in_game_zeme WHERE id_zeme='$id_zeme' and
				id_ligy='$id_ligy'";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	$inf = $row[0];
	$oprava = $row[1];
	

	if($inf <25){
		$suma = ( floor( $inf / 5 ) ) * 200;
		if ($suma == 0) {$suma = 150;}
	}
	else if($inf <50){
		$suma = ( floor( $inf / 5 ) - 5 ) * 700 + 1000;
	}
	else if($inf <75){
		$suma = ( floor( $inf / 5 ) - 10 ) * 2000 + 4500;
	}
	else {
		$suma = ( floor( $inf / 5 ) - 15 ) * 7000 + 14500;
	}

	$suma = $suma * ( 1 + $oprava * 5 ); 
	return $suma;
}

/**
 * funkce vrati cenu stavby
 *
 * @param int $id_stavby - id stavby u ktere chceme vratit cenu
 * @return pole s polozkami suroviny, palivo nebo false
 */
function CenaPostaveniStavby( $id_stavby ){
	global $db, $error;
	$id_stavby = sqlsafe( $id_stavby );
	
	$query = "SELECT cena_suroviny,cena_palivo FROM stavby 
				WHERE id='$id_stavby'";
	$res = $db->Query( $query );
	if ($cena = $db->GetFetchRow( $res )){
		$pole["suroviny"] = $cena[0];
		$pole["palivo"] = $cena[1];
		return $pole;
	}
	else{
		$error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                        'NOT_IN_DB', "Stavba nebyla nalezena v DB",null);
	}
	
	
}

function CenaNavyseniPolicejniStanice( $id_zeme, $id_ligy, $vrat_pocet = false ){
	global $db, $users_class, $CONST;
	$cena = 0;
	$query = "SELECT vojenska_policie FROM `in_game_zeme` 
			WHERE id_zeme='$id_zeme' and id_ligy='$id_ligy'";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	
	//pokud je stanic mene nez 5, jsou o 30% levnejsi
	if($row[0]<5){
		$cena = round(500 * ($row[0] + 1) * 0.7);
	}
	else{
		$cena = 500 * ($row[0] + 1);
	}
	
	if ($vrat_pocet){
		$pole = array();
		$pole['pocet'] = $row[0];
		$pole['cena'] = $cena;
		return $pole;
	}
	else{
		return $cena;	
	}
}

function CenaNavyseniPVOStanice( $id_zeme, $id_ligy, $vrat_pocet = false ){
	global $db, $users_class, $CONST;
	$cena = 0;
	$query = "SELECT pvo_stanice FROM `in_game_zeme` 
			WHERE id_zeme='$id_zeme' and id_ligy='$id_ligy'";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );	
	
	$cena = 500 * ($row[0] + 1);
	
	if ($vrat_pocet){
		$pole = array();
		$pole['pocet'] = $row[0];
		$pole['cena'] = $cena;
		return $pole;
	}
	else{
		return $cena;	
	}
	
}

/**
 * Vrati cenu zvyseni letecke sily o 1 bod
 *
 * @param $letecka_sila - soucasna letecka sila hrace
 * @return cena v BV 
 */
function CenaBoduLeteckeSily( $letecka_sila ){
	$letecka_sila = sqlsafe( $letecka_sila );
	//$p = ceil( $letecka_sila * $letecka_sila / 10);
	//$p = ceil( sqrt($letecka_sila) * 2 + 4);
	$p = ceil( sqrt($letecka_sila) + 11);
	return $p; 
}


function ZaplatSurovinyAPalivo($suroviny, $palivo, $id_user = NULL){
	global  $users_class, $db, $error;
	$suroviny = sqlsafe( $suroviny );
	$palivo = sqlsafe( $palivo );
	
	if (!is_numeric( $suroviny ) || $suroviny < 0){
        $error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                        'WRONG_TYPE', "Suroviny neni kladne cislo",null);
		return false;
	}
	
	if (!is_numeric( $palivo ) || $palivo < 0){
        $error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                        'WRONG_TYPE', "Palivo neni kladne cislo",null);
		return false;
	}
	
	if (!isset($id_user)){
		$id_user = $users_class->user_id();
	}
	
	$query = "SELECT * FROM in_game_hrac WHERE id_hrac='$id_user'";
	$user = $db->Query( $query );
	if ($vysledek = $db->GetFetchAssoc( $user )){
		// ma zdroje
		if ((($vysledek['suroviny']>= $suroviny)||($suroviny == 0)) 
		&& (($vysledek['palivo']>= $palivo)||( $palivo == 0))){
			$zustatek_suroviny = $vysledek['suroviny'] - $suroviny;
			$zustatek_palivo = $vysledek['palivo'] - $palivo;
			
			$query = "UPDATE `in_game_hrac` SET 
					`suroviny`= '$zustatek_suroviny',
					`palivo`= '$zustatek_palivo'				 
					 WHERE id_hrac='$id_user'
					;";
			$db->DbQuery( $query );
			return true;
		}
		//nema zdroje
		else{
			return false;
		}
	}
	else{
		return false;
	}

	return false; //sem by se nikdy nemelo dostat
	
	
}

function ZaplatSuroviny($cena ,$id_user = NULL){
	global  $users_class, $db, $error;
	$cena = sqlsafe( $cena );
	if (!is_numeric( $cena ) || $cena < 0){
        $error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                        'WRONG_TYPE', "Suroviny neni kladne cislo",null);
		return false;
	}
	
	
	
	if (!isset($id_user)){
		$id_user = $users_class->user_id();
	}
	$query = "SELECT * FROM in_game_hrac WHERE id_hrac='$id_user'";
	$user = $db->Query( $query );
	if ($vysledek = $db->GetFetchAssoc( $user )){
		// ma suroviny
		if ($vysledek['suroviny']>= $cena){
			$zustatek = $vysledek['suroviny'] - $cena;
			
			$query = "UPDATE `in_game_hrac` SET 
					`suroviny`= '$zustatek'			 
					 WHERE id_hrac='$id_user'
					;";
			$db->DbQuery( $query );
			return true;
		}
		//nema suroviny
		else{
			return false;
		}
	}
	else{
		return false;
	}

	return false; //sem by se nikdy nemelo dostat
	
}

function ZaplatPalivo($cena ,$id_user = NULL){
	global  $users_class, $db, $error;
	$cena = sqlsafe( $cena );
	if (!is_numeric( $cena ) || $cena < 0){
        $error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                        'WRONG_TYPE', "Palivo neni kladne cislo",null);
		return false;
	}
	
	if (!isset($id_user)){
		$id_user = $users_class->user_id();
	}
	$query = "SELECT * FROM in_game_hrac WHERE id_hrac='$id_user'";
	$user = $db->Query( $query );
	if ($vysledek = $db->GetFetchAssoc( $user )){
		// ma palivo
		if ($vysledek['palivo']>= $cena){
			$zustatek = $vysledek['palivo'] - $cena;
			
			$query = "UPDATE `in_game_hrac` SET 
					`palivo`= '$zustatek'				 
					 WHERE id_hrac='$id_user'
					;";
			$db->DbQuery( $query );
			return true;
		}
		//nema palivo
		else{
			return false;
		}
	}
	else{
		return false;
	}

	return false; //sem by se nikdy nemelo dostat
	
}

function ZaplatBodyVlivu($cena ,$id_user = NULL){
	global  $users_class, $db, $error;
	$cena = sqlsafe( $cena );
	if (!is_numeric( $cena ) || $cena < 0){
        $error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                        'WRONG_TYPE', "Body vlivu neni kladne cislo",null);
		return false;
	}
	
	if (!isset($id_user)){
		$id_user = $users_class->user_id();
	}
	$query = "SELECT * FROM in_game_hrac WHERE id_hrac='$id_user'";

	$user = $db->Query( $query );
	if ($vysledek = $db->GetFetchAssoc( $user )){
		// ma BV
		if ($vysledek['body_vlivu']>= $cena){
			$zustatek = $vysledek['body_vlivu'] - $cena;
			
			$query = "UPDATE `in_game_hrac` SET 
					`body_vlivu`= '$zustatek'				 
					 WHERE id_hrac='$id_user'
					;";
			$db->DbQuery( $query );
			return true;
		}
		//nema body_vlivu
		else{
			return false;
		}
	}
	else{
		return false;
	}

	return false; //sem by se nikdy nemelo dostat
	
}

function ProvozStab( $id_hrac, $kolaps ){
	global $db, $CONST;
	
	if (MaHracStavbu( 23 )){
		return $kolaps;
	}
	//pokud nema postavenou, neni provoz zadny
	return 0;
}

function ProvozLetist( $id_hrac ){
	global $db, $CONST;
	
	$query = "SELECT count(igz.id_zeme) 
		FROM in_game_zeme AS igz JOIN in_game_stavby AS igs ON 
		igz.id_zeme = igs.id_zeme AND igz.id_ligy=igs.id_ligy
	 WHERE igz.id_vlastnik='$id_hrac' AND (igs.id_stavby=1 OR igs.id_stavby=2)";
	$res = $db->Query( $query );
	$s = $db->GetFetchRow( $res );
	
	return $s[0] * $CONST["PROVOZ_LETISTE"];
}

function ProvozPocasi( $id_ligy ){
	global $db, $CONST, $pocasi_zisky;
	
	$res = $db->Query("SELECT id_pocasi_dnes FROM ligy WHERE id='$id_ligy'");
	$row = $db->GetFetchRow( $res );
	$id_pocasi = $row[0];
	
	return $pocasi_zisky[$id_pocasi];
}

function KolapsVZasobovani( $id_user ){
	global $db;
	$postih = 0; 
	
	$query = "SELECT count(*) FROM in_game_zeme WHERE id_vlastnik='$id_user'";
	$user = $db->Query( $query );
	$s = $db->GetFetchRow( $user );
	$suma = $s[0];
	if ($suma>5){
		$postih = $suma / 100; 
	}
	
	if ($postih>1){
		$postih = 1;
	}
	
	return $postih;
	
}

function VratBonusInfrastruktury($id_zeme, $id_ligy){
global  $users_class, $db, $CONST;
	
	$id_zeme = sqlsafe ( $id_zeme );
	$id_ligy = sqlsafe ( $id_ligy );

	//infrastruktu
	$query = "SELECT infrastruktura_now FROM in_game_zeme WHERE 
			id_zeme='$id_zeme' and id_ligy='$id_ligy'";
	$res1 = $db->Query( $query );
	$inf = $db->GetFetchRow( $res1 );
	
	//z infrastruktury vypocitam bonus kterym pronasobim zisky 
	if ($inf[0] != 0){
		$infrastruktura = $inf[0] / 100 ;
	}
	else{
		$infrastruktura = 0;
	}
	
	//vracim rozsah 0,5-1,5
	return 0.5 + $infrastruktura;
}
?>