<?php

function AddHrac2IncludeMap($id_hrac, $zeme_pole, $id_ligy){
	global $db,$users_class, $DIR_CONFIG, $CONST, $LANGUAGE, $DIR_LIB, $DIR_SKINS, $DIR_INC;
	require_once($DIR_LIB . "page_elements/fr_mapa_zobraz_zeme.php");

		//nacteni struktury ze souboru
		$filename = $DIR_INC . $id_ligy . ".php";
		$file = fopen( $filename, 'rb' );
		$obsah = fread($file, filesize($filename));
		fclose($file);
		$mapa = unserialize( $obsah );
	
        $skin_dir = $DIR_SKINS. "default/frame_mapa/";

        $query = "SELECT id_zeme,zv.id_ligy, nazev, id_vlastnik, pos_x, pos_y, center_x, center_y, strana 
                FROM `zeme_view` AS zv JOIN `in_game_hrac` AS igh ON zv.id_vlastnik=id_hrac 
                WHERE pos_x IS NOT NULL AND zv.id_ligy = '$id_ligy' AND
                 id_zeme in (".implode(',',$zeme_pole).") AND id_vlastnik='$id_hrac';";

        $res = $db->Query($query);
        while ($zeme = $db->GetFetchAssoc( $res )){
        		unset($mapa[-1][$zeme["id_zeme"]]);
        		$pole = StavZemeNaMape($zeme["id_zeme"], $zeme["id_ligy"],$zeme["id_vlastnik"]);
                $highlight = 0;
                $mapa[$zeme["id_vlastnik"]][$zeme["id_zeme"]] = ZobrazZem($zeme["id_zeme"], $zeme["id_zeme"], $zeme["nazev"], $zeme["strana"], $highlight, '',$zeme["pos_x"], $zeme["pos_y"], $zeme["center_x"], $zeme["center_y"], $zeme["center_x"] - 20, $zeme["center_y"] - 25, $zeme["center_x"] + 25, $zeme["center_y"] - 40,'',$skin_dir, $pole['letiste'], $pole['bunkr'],$pole["subquest"],$pole['veleni'],$pole['symbol'], $pole);
        }
        
        $file = fopen( $DIR_INC . $id_ligy . '.php', 'wb' );
		fwrite($file, serialize($mapa) );
		fclose($file);
	
}

function priradZeme( $id_hrac, $id_ligy, $typ, $infra, $strana ){
	 global $db,$users_class, $CONST;
	 
	//vypocet jednotek po prilogovani
	$query = "SELECT odehranych_dnu FROM ligy WHERE id='$id_ligy'";
	$res = $db->Query($query);
	$od = $db->GetFetchRow( $res );
	
	$tanky = 1 + $od[0];
	$pechota = 11 + 2*$od[0];
	$infra += 3*$od[0];
	
	 //vyber zemi k logovani
 	if( $strana == 'us'){
	 	$zeme_pro_log = $CONST['zeme_pro_log_us'];
 	}
 	else{
 		$zeme_pro_log = $CONST['zeme_pro_log_sssr'];
 	}

 	//FIXME HACK PRO TURNAJ
	 if($id_ligy>=22){
	 	$zeme_pro_log = ' 50,110 ';
	 }
	 
	 //nalezeni vhodne zeme pro log
	 $query = "SELECT id_zeme FROM in_game_zeme 
	 			WHERE id_ligy='$id_ligy' AND id_vlastnik IS NULL
				AND id_zeme IN ($zeme_pro_log)
	 			ORDER BY RAND() LIMIT 1";
	 $res = $db->Query($query);
	 
	 $zeme = array();
	 if ($row = $db->GetFetchRow( $res )){
	 	$zeme[] = $row[0];
	 }
	 else{
	 	//pokud neni volny nektery z defaultnich sektoru, priradim jakykoliv krom berlina
	 	$query = "SELECT id_zeme FROM in_game_zeme 
	 			WHERE id_ligy='$id_ligy' AND id_vlastnik IS NULL AND id_zeme<>45
	 			ORDER BY RAND() LIMIT 1";
	 	$res = $db->Query($query);
	 	$row = $db->GetFetchRow( $res );
	 	$zeme[] = $row[0];
	 }
	 
	 
	 //pokud je trenink, tak vybereme jeste X dalsich sousedni sektory
	 if ($typ == "trening"){
		$query = "select zeme2 as id from sousede as s join in_game_zeme as igz on
		s.zeme2=igz.id_zeme where zeme1='$zeme[0]' and igz.id_ligy='$id_ligy' and igz.id_vlastnik is null
		order by rand() limit ".$CONST['pocet_zeme_trenink'];
	 	$res = $db->Query($query);
	 	while( $row = $db->GetFetchAssoc( $res ) ){
	 		$zeme[] = $row['id'];
	 	}
	 }
	 
	 //prideleni zeme hraci
	 $query = "UPDATE in_game_zeme SET id_vlastnik = $id_hrac , tanky = $tanky, pechota = $pechota, infrastruktura_now=$infra
	 			WHERE id_ligy='$id_ligy' AND id_zeme IN (".implode(',', $zeme).")";
	  $db->DbQuery($query);
	  
	  //zmena i stavu "vcera", aby byla mapa aktualni
	 $query = "UPDATE in_game_vcera_zeme SET id_vlastnik = $id_hrac , tanky = $tanky, pechota = $pechota, infrastruktura_now=$infra
	 			WHERE id_ligy='$id_ligy' AND id_zeme IN (".implode(',', $zeme).")";
	  $db->DbQuery($query);
			
	  //pridani hrace do jiz vygenerovane mapy
	  AddHrac2IncludeMap($id_hrac, $zeme, $id_ligy);

	  
	foreach( $zeme as $id_zeme ){	  
		  //postaveni stavby zasobovaci sklad
		  $query = "INSERT INTO `in_game_stavby`  
				( `id_zeme`, `id_ligy`,`id_stavby`,`id_vlastnik`) VALUES
				('$id_zeme',
				'$id_ligy'
				, 4,
				'$id_hrac'
				);";
		$db->DbQuery( $query );
		
		//postaveni stavby zenijni brigada ve vycvikovem scenari	 
		 if ($typ=="trening"){
		 	$query = "INSERT INTO `in_game_stavby`  
				( `id_zeme`, `id_ligy`,`id_stavby`,`id_vlastnik`) VALUES
				('$id_zeme',
				'$id_ligy'
				, 21,
				'$id_hrac'
				);";
			$db->DbQuery( $query );
		 }
	}
	 
}


function prihlaseni($myPost){
  global $db,$users_class, $DIR_CONFIG, $CONST, $LANGUAGE, $DIR_LANG;
  
  require_once($DIR_CONFIG . "konstanty.php");
  require_once($DIR_LANG . "posta.php");
  require_once($DIR_CONFIG . "lib.php");
  require_once($DIR_CONFIG . "statistiky.php");
  
  	$id_hrac = $users_class->user_id();	
	$id_strana = sqlsafe($myPost['strana']);
	//defaultni symbol
	$symbol = 1;
	$id_ligy = sqlsafe($myPost['scenar']);

	//test zda je uzivatel prihlasen
	if(!$users_class->is_logged()){
		return "Nejste přihlášen. Přihlašte se na titulní stránce.";
	}
	
	if (!is_numeric($id_strana)||
		!is_numeric($symbol)||
		!is_numeric($id_ligy)){
			return "Chybně vyplněný formulář.";
		}	
		
	//prihlasuje se dnes hrac poprve?
		$query = "SELECT * FROM `users_hrac` where id_user='".$id_hrac."'";
		$res = $db->Query($query);
		$row = $db->GetFetchAssoc( $res );
		
		if($row['prihlasil']!=0){
			return "Dnes jste se do scénáře už přihlašoval. Znovu můžete až zítra.";
		}
	//ulozeni vsech users_hrac dat do promenne	
	$users_hrac_data = $row;	
			
	//test zda je liga jeste volna - tedy neni max hracu
		$query = "SELECT max_pocet_hracu, registrace, typ FROM `ligy` where id='".$id_ligy."'";
		$res = $db->Query($query);
		$row = $db->GetFetchRow( $res );
		$max_hracu = $row[0];
		$registrace = $row[1];
		$typ = $row[2];
		
		$query = "SELECT count(*) FROM in_game_hrac WHERE id_liga='".$id_ligy."'";
		$res2 = $db->Query($query);
		$mh = $db->GetFetchRow( $res2 );
		//test zda je max hracu v lize
		if ($mh[0]>=$max_hracu){
			return "Scénář už je zaplněn. Zkuste se přihlásit do jiného.";
		}
		//je liga otevrena pro registraci?	
		if ( $registrace != 'ano' ){
			return "Scénář je již rozehraný.";
		}
		//ma hrac pravo vstoupit do elitniho scenare?
		if( $typ == 'elite_dm' || $typ == 'team'){
			$query = "SELECT * FROM users_hrac WHERE id_user='$id_hrac'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			if( $row['odehranych_dnu'] < 7 )
			{
			return "Do elitního scénáře mohou vstoupit pouze hráči se 7 a více odehranými dny.";	
			}
		}
		
	//omezeni casu
	$cas = date("G");
	//echo $cas . "," . $CONST['max_cas_prihlaseni_do_ligy'];
	if ($cas >= $CONST['max_cas_prihlaseni_do_ligy']){
		return "Přihlášení do scénáře je možné jen do ".$CONST['max_cas_prihlaseni_do_ligy'].":00.";
	}
		
	if ($id_strana!=1){
		$strana = "sssr";
		$id_general = 3;
		$start_suroviny = $CONST['START_SSSR_suroviny'];
		$start_palivo = $CONST['START_SSSR_palivo'];
		$start_bv = $CONST['START_SSSR_bv'];
		$infra = $CONST['START_SSSR_infra'];
		//nacteni max poctu kol
		$query = "SELECT max_pocet_kol FROM generalove WHERE id='".$id_general."'";
		$rs = $db->Query( $query );
		$rw = $db->GetFetchAssoc( $rs );
		$start_kola = $rw['max_pocet_kol'];
	}
	else{
		$strana = "us";
		$id_general = 2;
		$start_suroviny = $CONST['START_US_suroviny'];
		$start_palivo = $CONST['START_US_palivo'];
		$start_bv = $CONST['START_US_bv'];
		$infra = $CONST['START_US_infra'];
		
		//nacteni max poctu kol
		$query = "SELECT max_pocet_kol FROM generalove WHERE id='".$id_general."'";
		$rs = $db->Query( $query );
		$rw = $db->GetFetchAssoc( $rs );
		$start_kola = $rw['max_pocet_kol'];
	}
	
	if( $typ == 'trening'){
		$start_suroviny = $CONST['START_trening_suroviny'];
		$start_palivo = $CONST['START_trening_palivo'];
		$start_bv = $CONST['START_trening_bv'];
	}
	
	//test zda hraje uz 10 hracu
	$query = "SELECT count(*) FROM in_game_hrac WHERE strana='$strana' AND id_liga='$id_ligy'";
	$res = $db->Query($query);
	$csl = $db->GetFetchRow( $res );
	
	if( $csl[0] >= 10 ){
		return "Za stranu $strana hraje už maximum hráčů. Vyber si jinou.";
	}
	
	$query = "SELECT count(*) FROM in_game_hrac WHERE strana<>'$strana' AND id_liga='$id_ligy'";
	$res = $db->Query($query);
	$opos = $db->GetFetchRow( $res );
	
	//vypocet hracu po prilogovani v teamovem scenari
	if( $typ == 'team'){
		if($csl[0] >= ( $opos[0] + 2 ) ){
			return "Za stranu $strana hraje velká převaha hráčů. Počkej až se přihlásí více soupeřů.";
		}
	}
	
	//maximum 10 hracu za jednu stranu
	
	stat_prihlaseni( $id_hrac, $typ, $strana );
			
	//test zda hrac je v nejakem scenari
	$res = $db->Query("SELECT * FROM 
				`in_game_hrac` WHERE id_hrac='$id_hrac'");
	
	if ($hrac = $db->GetFetchRow( $res )){
		return "Máte rozehranou hru. Nemůžete se přihlásit.";	
	}
		
	$query = "insert  into `in_game_hrac` (`id_hrac`, `id_general`, `id_liga`, 
		`id_hrdina`, `strana`, `symbol`,`suroviny`,`palivo`,`body_vlivu`,
			`akt_max_pocet_kol`) VALUES 
		('$id_hrac','$id_general','$id_ligy',
		0,'".$strana."', '$symbol', '$start_suroviny', '$start_palivo', '$start_bv',
		'$start_kola')";
	
	 $db->DbQuery($query);
	 
	 //priradi hraci zeme v lize
	 priradZeme( $id_hrac, $id_ligy, $typ, $infra, $strana );
	 
	 //oznaceni prihlaseni  
	$query = "UPDATE users_hrac SET prihlasil = 1 WHERE id_user = '$id_hrac'";
	$db->DbQuery( $query );
	 
	//poslani uvodni posty jen hracum co maji mene nez 10 odehranych dnu
	if($users_hrac_data['odehranych_dnu']<$CONST['max_dny_zasilani_uvodni_zpravy']){
		$posta = ZakodujPrizpevek( $LANGUAGE['prvni_posta'] );
		$query = "INSERT INTO posta (id_autor, id_prijemce, obsah, predmet, cas) VALUES
		 		(1, '$id_hrac', '$posta', 'Uvítání a pár rad do začátku', now());";
		$db->DbQuery( $query );
	}
	  
}

function napln_strana(){

	//pridani uzivatele stredoskolak
	$options[] = Array ( 0 => 1, 1 => "Spojenci" );
	$options[] = Array ( 0 => 2, 1 => "Rudá armáda" );

	return $options;
}

function napln_symbol(){
/*	global $db;

	$query = "Select `id`,`nazev` from skoly order by nazev";
	$db->Query($query);
	//pridani uzivatele stredoskolak
	$options[] = Array ( 0 => 0, 1 => "Střední škola" );
	while ($row = $db->GetFetchRow())
	{ 
	$options[] = $row;
	}*/
	$options[] = Array ( 0 => 1, 1 => 1 );
	$options[] = Array ( 0 => 2, 1 => 2 );
	$options[] = Array ( 0 => 3, 1 => 3 );
	$options[] = Array ( 0 => 4, 1 => 4 );
	$options[] = Array ( 0 => 5, 1 => 5 );
	$options[] = Array ( 0 => 6, 1 => 6 );
	return $options;
}

function napln_scenar(){
	global $db;

	$query = "SELECT id, nazev, max_pocet_hracu FROM `ligy` where registrace='ano' AND stav='active'";
	$res = $db->Query($query);

	while ($row = $db->GetFetchAssoc( $res ))
	{ 
		$query = "SELECT count(*) FROM in_game_hrac WHERE id_liga='".$row['id']."'";
		$res2 = $db->Query($query);
		$mh = $db->GetFetchRow( $res2 );
		//test zda jeste neni maximum hracu v lize
		if ($mh[0]<$row['max_pocet_hracu']){
			$options[] = array(0 => $row['id'], 1 => $row['nazev']);
		}
	}
	if(!isset($options)){
		$options[] = array(0 => "b", 1 => "Není žádný volný scénář");
	}
	
	return $options;
}

?>