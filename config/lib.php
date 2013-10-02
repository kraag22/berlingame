<?php

require_once($DIR_CONFIG . "konstanty.php");

require_once($DIR_CONFIG . "lib.php");
	
require_once($DIR_CONFIG . "boj.php");

require_once($DIR_CONFIG . "formulare.php");

require_once($DIR_CONFIG . "statistiky.php");

/**
 * vrati rozdil v ms
 *
 * @param $start - od jakeho casu se merilo
 * @return napr. 32.3ms
 */
function VratRozdilMikro( $start ){
	
	return round((microtime(true)-$start) * 10000) / 10;
}

/**
 * Funkce vrati povoleni pro hrace, zda muze v dane lize provest danou akci - utok, let, pod,...
 *
 * @param $plan - co chce hrac provest - utok, akce, plan...
 * @param $id_ligy
 * @param $error - navratova hlaska, ktera se hraci zobrazi
 * @param $zdroj - id hrace, ktery utok pacha
 * @param $cil - id zeme nebo cile - v zavislosti na planu
 * @param $id_akce 
 * @return boolean
 */
function JePovolenaAkce( $plan, $id_ligy, &$error, $id_autor = NULL, $cil = NULL, $id_akce = NULL){
	global $db, $CONST, $pole_skodlive_letectvo, $pole_skodliva_podpora;
	
	$query = "SELECT typ FROM ligy WHERE id='$id_ligy'";
	$res = $db->Query( $query );
	if( $row = $db->GetFetchAssoc( $res ) ){
		$typ = $row['typ'];
	}
	else{
		return false;
	}
	
	//omezeni zatim jen v teamove lize
	if( $typ != 'team'){
		return true;
	}
	
	switch($plan){
		case 'utok':
			//cil je id_zeme_kam
			if (JsouHraciNaStejneStrane( $id_autor, MajitelZeme( $cil, $id_ligy ))){
				//zakaz
				$error = 'Nelze útočit na spoluhráče';
				return false;
			}
			break;
		case 'letecka_akce':
			//cil je autor
			if ($id_autor!=$cil && JsouHraciNaStejneStrane( $id_autor, $cil)){
				//zakaz planovani nebezpecnych akci na spoluhrace
				if( in_array( $id_akce, $pole_skodlive_letectvo) ){
					$error = 'Tuto akci nemůžete naplánovat na spoluhráče';
					return false;
				}
			}
			break;
		case 'podpora_akce':
			//cil je autor
			if ($id_autor!=$cil && JsouHraciNaStejneStrane( $id_autor, $cil)){
				//zakaz planovani nebezpecnych akci na spoluhrace
				if( in_array( $id_akce, $pole_skodliva_podpora) ){
					$error = 'Tuto akci nemůžete naplánovat na spoluhráče';
					return false;
				}
			}
			break;
		case 'pruchod':
			//cil je majitel zeme, ve ktere chci zadat
			if (!JsouHraciNaStejneStrane( $id_autor, $cil)){
				$error = 'Nemůžete žádat o přesun soupeře';
				return false;
			}
			break;
	}
	
	return true;
}

function JsouHraciNaStejneStrane( $id_hrac, $id_cil ){
	
	//jestli je nektery z hracu neutralka :)
	if ( !isset($id_hrac) || !isset($id_cil) ){
		return false;
	}
	
	return (VratAtributHrace( 'strana', $id_hrac) == VratAtributHrace( 'strana', $id_cil));
}

function VratAtributHrace( $atribut, $id_hrac ){
	global $users_class, $db;
	
	$query = "SELECT $atribut FROM in_game_hrac WHERE id_hrac='$id_hrac'";
	$res = $db->Query( $query );
	if( $row = $db->GetFetchRow( $res ) ){
		return $row[0];
	}
	else{
		return false;
	}
}

function VratPoradiHracu( $id_ligy ){
	global $db;
	$poradi = array();
	
	$query = "SELECT usi.id, usi.login, x.suma, igh.strana 
			FROM users_sys AS usi 
			JOIN (SELECT id_vlastnik, SUM(body_vlivu) AS suma 
				FROM zeme_view GROUP BY id_vlastnik ) AS x
			JOIN in_game_hrac AS igh 
			ON usi.id=x.id_vlastnik AND igh.id_hrac=usi.id
			WHERE igh.id_liga='$id_ligy' ORDER BY x.suma DESC";
	$res = $db->Query( $query );
			
	while( $row = $db->GetFetchAssoc( $res ) ){
		$poradi[$row['id']] = $row; 
	}
	return $poradi;
}

function MaxJednotekCoLzePostavit( $id_zeme, $id_user = null ){
	global  $users_class, $db;
	
	$suma = 0;
	
	if (!isset($id_user)){
		$id_user = $users_class->user_id();
	}
	
	$id_ligy = JeUzivatelVLize( $id_user );
	
	$query = "SELECT lze_povolat, povolano FROM in_game_zeme WHERE 
			id_zeme='$id_zeme' AND id_ligy='$id_ligy'";
	$res = $db->Query( $query );
	$in = $db->GetFetchAssoc( $res );
	
	return ($in['lze_povolat'] - $in['povolano']);
}

function VratGeneralaHrace( $id_user = null ){
	global  $users_class, $db;
		
	if (!isset($id_user)){
		$id_user = $users_class->user_id();
	}
	
	$query = "SELECT id_general FROM in_game_hrac WHERE id_hrac='".$id_user."'";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	
	return $row[0];
}

function VratLSProZavreni( $id_ligy ){
	global $db, $CONST;
	
	$query = "SELECT typ FROM ligy WHERE id='".$id_ligy."'";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	
	switch( $row[0] ){
		case 'team':
			return $CONST['LIGA_LETECKA_SILA_PRO_ZAVRENI_TEAM'];
			break;
		case 'deathmatch':
			return $CONST['LIGA_LETECKA_SILA_PRO_ZAVRENI_DEATHMATCH'];
			break;
		default:
			return $CONST['LIGA_LETECKA_SILA_PRO_ZAVRENI_DEATHMATCH'];
			break;
	}	
}

function VratProcenaLeteckeSily( $id_ligy,  $id_user = null ){
	global  $users_class, $db, $CONST;
	
	if (!isset($id_user)){
		$id_user = $users_class->user_id();
	}
	
	if (JeUzivatelVLize( $id_user )!= $id_ligy){
		return 0;
	}
	
	
	
	$query = "SELECT letecka_sila FROM in_game_hrac WHERE id_hrac='".$id_user."'";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	
	$procenta = floor($row[0] * 100 / VratLSProZavreni($id_ligy));
	if ($procenta>100){
		$procenta = 100;
	}
	return $procenta;	
}

function MaHracovaStranaSektory( $id_ligy, $id_user = null ){
	global  $users_class, $db, $CONST;
	
	if (!isset($id_user)){
		$id_user = $users_class->user_id();
	}
	
	if (JeUzivatelVLize( $id_user )!= $id_ligy){
		return false;
	}
	
	$query = "SELECT strana, id_liga FROM in_game_hrac WHERE id_hrac='".$id_user."'";
	$res = $db->Query( $query );
	$str = $db->GetFetchRow( $res );
	
	$strana = $str[0];
	$id_ligy = $str[1];
	
	$query = "SELECT id_hrac FROM in_game_hrac WHERE 
			strana='$strana' AND id_liga='$id_ligy'";
	$res = $db->Query( $query );
	
	$query = "SELECT count(*) FROM zeme_view WHERE 
			id_ligy='$id_ligy' AND id_vlastnik IN (";

	$prvni = 0;
	while ($hraci = $db->GetFetchAssoc( $res )){
		if ($prvni == 0){
			$prvni = 1;
		}
		else{
			$query .= ",";
		}
		$query .= $hraci['id_hrac'];		
	}
	
	$query .= ")";
		
	$res = $db->Query( $query );
	$vys = $db->GetFetchRow( $res );
	 
	if ($vys[0] >= $CONST['LIGA_SEKTORY_PRO_ZAVRENI_TEAM']){
		return true;
	}
	return false;	

}

function MaHracZdroje( $id_ligy, $id_user = null ){
	global  $users_class, $db, $CONST;
	
	if (!isset($id_user)){
		$id_user = $users_class->user_id();
	}
	
	if (JeUzivatelVLize( $id_user )!= $id_ligy){
		return false;
	}
	
	$query = "SELECT * FROM in_game_hrac WHERE id_hrac='$id_user'";
	$res = $db->Query( $query );
	if ( $row = $db->GetFetchAssoc( $res ) ){
		if( $row['suroviny'] >= $CONST['LIGA_SUROVINY_PRO_ZAVRENI'] && 
			$row['palivo']>= $CONST['LIGA_PALIVO_PRO_ZAVRENI'] ){
				return true;
			}
	}
	else{
		//hrac nema rozehranou hru
		return false;
	}
	
	return false;
}

function MaHracPrestiz( $id_ligy, $id_user = null ){
	global  $users_class, $db, $CONST;
	
	if (!isset($id_user)){
		$id_user = $users_class->user_id();
	}
	
	if (JeUzivatelVLize( $id_user )!= $id_ligy){
		return false;
	}
	
	//pokud je hracova strana v lize sama, prestiz se rusi
	$query = "SELECT * FROM in_game_hrac WHERE id_liga='$id_ligy' AND strana <> (
			SELECT strana FROM in_game_hrac WHERE id_hrac='$id_user')";
	$res = $db->Query( $query );
	if (!$row = $db->GetFetchRow( $res ) ){
		return true;
	}
	
	$query = "SELECT prestiz FROM in_game_hrac WHERE id_hrac='$id_user'";
	$res = $db->Query( $query );
	if ( $row = $db->GetFetchAssoc( $res ) ){
		if( $row['prestiz'] >= $CONST['LIGA_PRESTIZ_PRO_ZAVRENI_TEAM']){
			return true;
		}
		else{
			return false;	
		}
	}
	else{
		//hrac nema rozehranou hru
		return false;
	}
	
	return false;
}

function MaHracKontrolu( $id_ligy, $id_user = null, $bv_pro_zavreni ){
	global  $users_class, $db, $CONST;
	
	if (!isset($id_user)){
		$id_user = $users_class->user_id();
	}
	
	if (JeUzivatelVLize( $id_user )!= $id_ligy){
		return false;
	}
	
	$query = "SELECT * FROM zeme_view WHERE id_vlastnik='$id_user'";
	$res = $db->Query( $query );
	
	$suma = 0;
	while ($row = $db->GetFetchAssoc( $res )){
		$suma += BodyVlivuZaKolo( $row['id_zeme'], $id_ligy );
	}
	
	//stab rozvedky
	if( MaHracStavbu(23, $id_user) ){
		$suma += $CONST["STAVBY_STAB_BV"];
	}
	 
	if ($suma >= $bv_pro_zavreni){
		return true;
	}
	return false;	

}

function SplnujeHracPodminkyProVyhru( $id_ligy, $id_user, $prepocet = false ){
	global $CONST, $db;
	
	$query = "SELECT typ FROM ligy WHERE id='$id_ligy'";
	$res = $db->Query( $query );
	$tp = $db->GetFetchAssoc( $res );
	
	if( $tp['typ']=='team' ){
		$p = VratPodminkyTeam( $id_ligy, $id_user, $prepocet );
	}
	else {
		$p = VratPodminkyDM( $id_ligy, $id_user, $prepocet );
	}
	//echo "liga: $id_ligy user: $id_user <br> ";
	//var_dump ( $p );

	//TEST ZDA HRAC VYHRAL
	$vyhra = "ne";
	for ($i = 1; $i<= $p['pocet_podminek']; $i++){
		if($p["${i}o"] == "ano"){
			$vyhra = "ano";
		}
		else{
			$vyhra = "ne";
			break;
		}
	}
	
	//VYHRA
	if( $vyhra=="ano" ){
		return true;
	}
	$param['id_hrac'] = $id_user;
	add_to_global_hlaseni( $id_ligy, "neuspesny_utok_na_berlin", $param);
	
	return false;
}

function VratPodminkyDM( $id_ligy, $id_user = null, $prepocet = false ){
	global $CONST;
	
	$podminky['pocet_podminek'] = 3;
	
	//letecka sila
	//echo "letecka sila : " . VratProcenaLeteckeSily( $id_ligy, $id_user ) . "<br>";
	if (VratProcenaLeteckeSily( $id_ligy, $id_user )==100){
		$podminky['1o'] = "ano";
	}
	else{
		$podminky['1o'] = "ne";
	}
	$podminky['1t'] = "Letecká nadvláda";
	$podminky['1tip'] = "Vybuduj leteckou sílu o velikosti ".$CONST['LIGA_LETECKA_SILA_PRO_ZAVRENI_DEATHMATCH']."<br />TIP: Budování letecké síly se provádí skrz tlačítko Letectvo na horní liště";
		
	//kontrola nad sektory
	if (MaHracKontrolu( $id_ligy, $id_user, $CONST['LIGA_BV_PRO_ZAVRENI'] )){
		$podminky['2o'] = "ano";
	}
	else{
		$podminky['2o'] = "ne";
	}
	$podminky['2t'] = "Rozhodující vliv";
	$podminky['2tip'] = "Získej a udrž si přijmy bodů vlivu ".$CONST['LIGA_BV_PRO_ZAVRENI']." za tah. <br />TIP: Aktuální příjem bodů vlivu se dozvíš ve Statistikách / Osobní statistika";
	
	//dostatek zdroju
	if ($prepocet){
		$podminky['3o'] = "ano";
	}
	else{
		if(MaHracZdroje($id_ligy, $id_user)){
			$podminky['3o'] = "ano";
		}
		else{
			$podminky['3o'] = "ne";
		}
	}	
	$podminky['3t'] = "Dostatek zdrojů";
	$podminky['3tip'] = "Nashromáždi ".$CONST['LIGA_SUROVINY_PRO_ZAVRENI']." zásob a ".$CONST['LIGA_PALIVO_PRO_ZAVRENI']." paliva pro rozhodující útok na samotný Berlín";
		
	/*$p['4o'] = "ne";
	$p['4t'] = "testt";*/
	return $podminky;
}

function VratPodminkyTeam( $id_ligy, $id_user = null, $prepocet = false ){
	global $CONST;
	
	$podminky['pocet_podminek'] = 4;
	
	//letecka sila
	if (VratProcenaLeteckeSily( $id_ligy, $id_user )==100){
		$podminky['1o'] = "ano";
	}
	else{
		$podminky['1o'] = "ne";
	}
	$podminky['1t'] = "Letecká nadvláda";
	$podminky['1tip'] = "Vybuduj leteckou sílu o velikosti ".$CONST['LIGA_LETECKA_SILA_PRO_ZAVRENI_TEAM']."<br />TIP: Budování letecké síly se provádí skrz tlačítko Letectvo na horní liště";
		
	//kontrola nad sektory
	if (MaHracKontrolu( $id_ligy, $id_user, $CONST['LIGA_BV_PRO_ZAVRENI_TEAM'] )){
		$podminky['2o'] = "ano";
	}
	else{
		$podminky['2o'] = "ne";
	}
	$podminky['2t'] = "Rozhodující vliv";
	$podminky['2tip'] = "Získej a udrž si přijmy bodů vlivu ".$CONST['LIGA_BV_PRO_ZAVRENI_TEAM']." za tah. <br />TIP: Aktuální příjem bodů vlivu se dozvíš ve Statistikách / Osobní statistika";
	
	//kontrola sektoru stranou
	if (MaHracovaStranaSektory( $id_ligy, $id_user )){
		$podminky['3o'] = "ano";
	}
	else{
		$podminky['3o'] = "ne";
	}
	$podminky['3t'] = "Absolutní převaha";
	$podminky['3tip'] = "Tvá strana musí získat a udržet plnou kontrolu nad {$CONST['LIGA_SEKTORY_PRO_ZAVRENI_TEAM']} herními sektory. <br />TIP: Aktuální stav se dozvíš ve Statistikách / Týmové statistiky";
			
	//body prestize
	if(MaHracPrestiz($id_ligy, $id_user)){
			$podminky['4o'] = "ano";
	}
	else{
			$podminky['4o'] = "ne";
	}
			
	$podminky['4t'] = "Bojová efektivita";
	$podminky['4tip'] = "Získej a udrž si {$CONST['LIGA_PRESTIZ_PRO_ZAVRENI_TEAM']} bodů prestiže.";
	

	return $podminky;
}


function VratPodminkyTrenink( $id_ligy ){
	global $CONST;
	$podminky['pocet_podminek'] = 1;
	
	$podminky['1o'] = "ne";
	$podminky['1t'] = "Tréninkový scénář";
	$podminky['1tip'] = "Scénář skončí po ".$CONST['LIGA_KONEC_TRENINGU']." odehraných dnech. Tento scénář nemá konkrétní cíl, jeho účelem je seznámit se s hrou či vyzkoušet si nové taktiky.";
	
/*	$p['3o'] = "ano";
	$p['3t'] = "testt";
	$p['4o'] = "ne";
	$p['4t'] = "testt";*/
	return $podminky;
}

function VypisVolneSymboly( $id_ligy, $strana ){
	global $db;
	$pocet_vlajek = 0;
	if ($strana == 'us'){
		$strana_kratka = 'u';
		$hrac_strana = 'us';
	}
	else{
		$strana_kratka = 's';
		$hrac_strana = 'sssr';
	}
	
	$text = "";
	$text .= "<table><tr>";
	
	$query = "SELECT cislo FROM symboly as s WHERE strana='$strana_kratka' AND s.cislo NOT IN 
			( select symbol from in_game_hrac where id_liga='$id_ligy' and strana='$hrac_strana')";

	$res = $db->Query( $query );
	while ($row = $db->GetFetchAssoc( $res )){
	$text .= "<td>";
	
	$text .= '<a href="intro.php?section=prihlaseni_symboly&id_symbol='.$row['cislo'].'" border="0">';
	$text .= '<img  src="./skins/default/frame_mapa/zeme/symbols/'.$strana_kratka.$row['cislo'].'.png" border="0" />';
	$text .= '</a>';
	$text .= "</td>";
	$pocet_vlajek++;
	}

	//pokud se nezobrazi zadna vlajka, zobrazim vsechny
	if ($pocet_vlajek ==0 ){
		$query = "SELECT cislo FROM symboly WHERE strana='$strana_kratka'";
		$res = $db->Query( $query );
		while ($row = $db->GetFetchAssoc( $res )){
		$text .= "<td>";
		
		$text .= '<a href="intro.php?section=prihlaseni_symboly&id_symbol='.$row['cislo'].'" border="0">';
		$text .= '<img  src="./skins/default/frame_mapa/zeme/symbols/'.$strana_kratka.$row['cislo'].'.png" border="0" />';
		$text .= '</a>';
		$text .= "</td>";
		$pocet_vlajek++;
		}
	}
	
	$text .= "</tr></table>";
	
	
	
	return $text;
}

/**
 * odhlasi hrace na vlastni zadost z rozehraneho scenare.
 * prida umisteni
 *
 * @param unknown_type $id_hrac
 */
function OdhlasHraceZeScenare( $id_hrac = null, $zapsat_statistiky = true){
	global  $users_class, $db, $CONST;

	if (!isset( $id_hrac )){
		$id_hrac = $users_class->user_id();
	}	
	
	$id_ligy = JeUzivatelVLize( $id_hrac );
	if(!$id_ligy){
		return;
	}
	
	$query = "SELECT * FROM ligy WHERE id='$id_ligy'";
	$res = $db->Query( $query );
	$row = $db->GetFetchAssoc( $res );
	$odehranych_dnu = $row['odehranych_dnu'];
	$dohrano = $row['dohrano'];
	$trening = $row['typ']  == 'trening';
	
	//zapis posledni umisteni v lize 
	if($dohrano!='ano' && $odehranych_dnu>1 && !$trening){
		$strana = VratAtributHrace('strana', $id_hrac);
		$datum = date("Y-m-d H:i:s");
		$query = "SELECT count(*) FROM in_game_hrac WHERE id_liga='$id_ligy'";
		$res = $db->Query( $query );
		$um = $db->GetFetchRow( $res );
		$umisteni = $um[0];
		
		// odehranych dnu 0 - odlogoval se sam
		$query = "INSERT INTO users_vyhry (id_user, id_ligy, datum, odehranych_dnu, umisteni, strana) VALUES
				('$id_hrac', '$id_ligy', '$datum', 0,'$umisteni', '$strana');";
		$db->DbQuery( $query );
	}
	
	$query = "DELETE FROM in_game_utoky WHERE id_vlastnik = '$id_hrac'";
	$db->DbQuery( $query );
	
	$query = "DELETE FROM in_game_podpora WHERE id_autor = '$id_hrac'";
	$db->DbQuery( $query );
	
	$query = "DELETE FROM in_game_letecke_akce WHERE id_autor = '$id_hrac'";
	$db->DbQuery( $query );
	
	$query = "DELETE FROM in_game_stavby WHERE id_ligy='$id_ligy' AND 
			id_zeme IN (SELECT id_zeme FROM in_game_zeme WHERE id_vlastnik='$id_hrac')";
	$db->DbQuery( $query );
	
	$query = "DELETE FROM in_game_hrac WHERE id_hrac = '$id_hrac'";
	$db->DbQuery( $query );
	
	//vypocet novych hodnot odlogovanych zemi
	$infra = $odehranych_dnu + 11;
	$pechota = $CONST['NEUTRALKA_PECHOTA'] + 4 * ($odehranych_dnu - 1);
	$tanky = $CONST['NEUTRALKA_TANKY'] + $odehranych_dnu - 1;
	$sn = ($pechota + $tanky ) * 4 + 2;
	
	$query = "UPDATE in_game_zeme SET id_vlastnik=NULL, infrastruktura_now='$infra', 
			pechota='$pechota', pechota_odeslano=0, tanky='$tanky', tanky_odeslano=0,
			povolano=0, oprava_infrastruktury=0, vojenska_policie=0, sila_neutralka='$sn',
			pvo_stanice=0, lze_povolat=0
		WHERE id_vlastnik = '$id_hrac'";
	$db->DbQuery( $query );
	
	$query = "UPDATE in_game_vcera_zeme SET id_vlastnik=NULL, pechota='$pechota', tanky='$tanky',
		infrastruktura_now='$infra', sila_neutralka='$sn' WHERE id_vlastnik = '$id_hrac'";
	$db->DbQuery( $query );
	
	if($dohrano!='ano' && $zapsat_statistiky){
		stat_odhlaseni( $id_hrac );
	}
}

function ZakodujPrizpevek( $text ){
	
	$text = sqlsafe( $text );
	$text = str_replace('\r\n','!s!',$text);
	$text = str_replace('\n','!s!',$text);
	return $text;
}

function RozkodujPrizpevek( $text ){
	$text = htmlsafe( $text );
	$text = str_replace('!s!','<br />', $text);
	return $text;
}

?>