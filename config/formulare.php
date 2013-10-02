<?php
	//ekonomicke funkce
	require_once($DIR_CONFIG . "ekonomika.php");
	
	//bojove funkce
	require_once($DIR_CONFIG . "boj.php");
	require_once($DIR_CONFIG . "lib.php");
	
/**
 * Funkce vraci id ligy ve ktere je hrac prihlasen
 *
 * @param $id_user
 * @param $id_liga
 * @param $tym - testuje zda je liga tymova. Pokud neni vrati 0
 * @return vraci false(0) pokud zadana liga neodpovida lize ve ktere hrac opravdu je
 * @return -1 pokud hrac neni v zadne lize
 * */
function JeUzivatelVLize( $id_user = NULL, $id_liga = NULL, $tym = NULL){
	global  $users_class, $db;
	
	if (!isset($id_user)){
		$id_user = $users_class->user_id();
	}
	
	$query = "SELECT * FROM in_game_hrac WHERE 
			id_hrac='".$id_user."'";
	
	$res = $db->Query( $query );
	if (!$row = $db->GetFetchAssoc( $res )){
		return false;
	}
	
	if (!isset($id_liga)){
		$id_liga = $row['id_liga'];
	}
	
	//test zda je liga tymova
	if( $tym ){
		$query = "SELECT typ FROM ligy WHERE 
				id='". $id_liga."'";
		
		$r = $db->Query( $query );
		$tp = $db->GetFetchAssoc( $r );
		if( $tp['typ'] != 'team' ){
			return false;
		}
	}
	
	if ( $id_liga == $row['id_liga'] ){
		return $id_liga;
	}
	else{
		return false;
	}
	
}

/**
 * Funkce vraci id aliance ve ktere je hrac prihlasen
 *
 * @param $id_user
 * @param $id_liga
 * @return vraci 0 pokud zadana aliance neodpovida nebo hrac neni v zadne alianci
 */
function JeUzivatelVAlianci( $id_user = NULL, $id_liga = NULL){
	global  $users_class, $db;
	
	if (!isset($id_user)){
		$id_user = $users_class->user_id();
	}
	
	$query = "SELECT * FROM in_game_hrac WHERE 
			id_hrac='". $id_user."'";
	
	$res = $db->Query( $query );
	$row = $db->GetFetchAssoc( $res );
	
	if (!isset($id_liga)){
		$id_liga = $row['id_aliance'];
	}
	
	if ( $id_liga == $row['id_aliance'] ) {
		return $id_liga;
	}
	else{
		return 0;
	}
	
}

/**
 * Funkce vraci aliance podle id, nebo alianci, ve ktere je hrac prihlasen
 *
 * @param $id_alaince
 * @return vraci FALSE pokud zadana aliance neodpovida
 */
function VratAlianci( $id_aliance = NULL){
	global  $users_class, $db;
	
	if (!isset($id_aliance)){
		$id_aliance = JeUzivatelVAlianci();
	}
	
	$query = "SELECT * FROM aliance 
			WHERE id='". (int)$id_aliance."'";
	$res = $db->Query( $query );
	return $db->GetFetchAssoc( $res );
	
}

/**
 * vrati hrace, kteremu patri zem
 *
 * @param $id - id zeme (atribut id_zeme v in_game_zeme)
 * @param $id - id ligy (atribut id_ligy v in_game_zeme)
 * @return id vlastnika zeme.
 */
function MajitelZeme( $id_zeme, $id_ligy ){
	global  $db, $error;
	$id_zeme = sqlsafe( $id_zeme );
	$id_ligy = sqlsafe( $id_ligy );
	
	$query = "SELECT id_vlastnik FROM in_game_zeme WHERE 
			id_zeme='$id_zeme' and id_ligy='$id_ligy'";
	
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	$majitel = $row[0];
/*	if (!is_numeric( $majitel )){
		$error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                        'zem',
                        "Zem:$id_zeme, liga: $id_ligy nema majitele" ,null);
	}*/
	if (!is_numeric( $majitel )){
		$majitel = null;
	}
	return $majitel;
}

function JeZemHrace( $id_zeme, $id_user = NULL, $id_liga = NULL){
	global  $users_class, $db, $error;
	
	if (!isset($id_user)){
		$id_user = $users_class->user_id();
	}
	
	if (!is_numeric( $id_liga )){
		$error->add_error($error->ERROR_LEVEL_ERROR, __FILE__, __LINE__,
                        'NO_ID_LIGY',
                        "Funkci JeZemHrace nebyl predan ID ligy. Bezpecnostni dira",null);
		return false;
	}
	
	$query = "SELECT * FROM in_game_hrac WHERE 
			id_hrac='". $id_user."'";
	
	$res = $db->Query( $query );
	$row = $db->GetFetchAssoc( $res );
	
	if (!isset($id_liga)){
		$id_liga = $row['id_liga'];
	}
	
	$query = "SELECT count(*) FROM in_game_zeme WHERE 
					id_vlastnik='".$id_user."' and 
					id_ligy='".$id_liga."' and
					id_zeme='".$id_zeme."'
					";
	$res = $db->Query( $query );
	$test = $db->GetFetchRow( $res );
	if($test[0]==0){
		return false;		
	}else{
		return true;
	}	
}

function ZpracujPovolaniOdvolaniPechoty( $id_zeme = null ){
	global $users_class, $db, $page;
	
	if(isset($_REQUEST['p_pocet']) && ($_REQUEST['p_pocet'])!=0){
		//$akce = sqlsafe($_REQUEST['akce']);
		if (isset($_REQUEST['akce_povolat'])){
			$akce = "Povolat";
		}
		if (isset($_REQUEST['akce_odvolat'])){
			$akce = "Odvolat";
		}
		
		$pocet = sqlsafe($_REQUEST['p_pocet']);
		
		
		$id_liga = JeUzivatelVLize();
		if (!JeZemHrace( $_SESSION['id_zeme'], null, $id_liga )){
			return "Hrac zem nevlastni";
		}
		if (!is_numeric( $pocet ) || $pocet <= 0){
			return "Zadejte jen kladne cislo";
		}
		
		if( $akce == 'Povolat'){
			$query = "SELECT pechota, povolano FROM in_game_zeme WHERE id_zeme='".$_SESSION['id_zeme']."' 
			and id_ligy='$id_liga'";
			$res = $db->Query( $query );
			$row = $db->GetFetchRow( $res );	
			
			//test zda nevycerpal limit povolanych jednotek
			if ($pocet > MaxJednotekCoLzePostavit($_SESSION['id_zeme'])){
				return "Vyčerpal jste limit povolávaných jednotek.";
			}
			
			$povolano = $row[1] + $pocet;
			
			$pole = VratAtributyJednotek("pechota", null, $id_zeme);
			if (ZaplatSuroviny($pocet * $pole['suroviny'])){
				$nove = $row[0] + $pocet;
			}
			else{
				return "Nemáte dostatek surovin";
			}
			
		}
		else if( $akce == 'Odvolat'){
			
			$query = "SELECT pechota, povolano FROM in_game_zeme WHERE id_zeme='".$_SESSION['id_zeme']."' 
			and id_ligy='$id_liga'";
			$res = $db->Query( $query );
			$row = $db->GetFetchRow( $res );	
		
			$povolano = $row[1];
			
			if ($row[0] < $pocet){
				$nove = 0;
			}
			else{
				$nove = $row[0] - $pocet;
			}
		} 
		
			//vlozeni dat do DB
			$query = "UPDATE in_game_zeme SET pechota = '$nove', povolano = '$povolano'
					 WHERE id_zeme ='".$_SESSION['id_zeme']."' and
					 id_ligy='$id_liga'";
			$db->DbQuery( $query );
			
			//prekresleni menu po nakupu
			$printer = new textPrinter();
		    $form = new textElement("OnLoad=\"top.menu.document.location.href='frame_menu.php';\"", $printer);
		    $page->add_element($form, 'refresh');	
		}	
}

function ZpracujPovolaniOdvolaniTanku( $id_zeme = null ){
	global $users_class, $db, $page;
	
	if(isset($_REQUEST['t_pocet']) && ($_REQUEST['t_pocet'])!=0){
		//$akce = sqlsafe($_REQUEST['akce']);
		$pocet = sqlsafe($_REQUEST['t_pocet']);
		if (isset($_REQUEST['akce_povolat'])){
			$akce = "Povolat";
		}
		if (isset($_REQUEST['akce_odvolat'])){
			$akce = "Odvolat";
		}
		
		$id_liga = JeUzivatelVLize();
		if (!JeZemHrace( $_SESSION['id_zeme'], null, $id_liga )){
			return "Hrac zem nevlastni";
		}
		if (!is_numeric( $pocet ) || $pocet <= 0){
			return "Zadejte jen kladne cislo";
		}
		
		if( $akce == 'Povolat'){			
			$query = "SELECT tanky, povolano FROM in_game_zeme WHERE id_zeme='".$_SESSION['id_zeme']."'
			and id_ligy='$id_liga'";
			$res = $db->Query( $query );
			$row = $db->GetFetchRow( $res );	
			
			//test zda nevycerpal limit povolanych jednotek
			if ($pocet > MaxJednotekCoLzePostavit($_SESSION['id_zeme'])){
				return "Vyčerpal jste limit povolávaných jednotek.";
			}
			
			$povolano = $row[1] + $pocet;
			
			$pole = VratAtributyJednotek("tanky", null, $id_zeme);
			if (ZaplatSurovinyAPalivo($pocet * $pole['suroviny'], $pocet * $pole['palivo'])){
				$nove = $row[0] + $pocet;
			}
			else{
				return "Nemáte dostatek surovin";;
			}
			
		}
		else if( $akce == 'Odvolat'){
			
			$query = "SELECT tanky, povolano FROM in_game_zeme WHERE id_zeme='".$_SESSION['id_zeme']."' 
			and id_ligy='$id_liga'";
			$res = $db->Query( $query );
			$row = $db->GetFetchRow( $res );	
		
			$povolano = $row[1];
			
			if ($row[0] < $pocet){
				$nove = 0;
			}
			else{
				$nove = $row[0] - $pocet;
			}
		} 
		
			//vlozeni dat do DB
			$query = "UPDATE in_game_zeme SET tanky = '$nove', povolano = '$povolano' 
					WHERE id_zeme ='".$_SESSION['id_zeme']."' and
					 id_ligy='$id_liga'";
			$db->DbQuery( $query );
			
			//prekresleni menu po nakupu
			$printer = new textPrinter();
		    $form = new textElement("OnLoad=\"top.menu.document.location.href='frame_menu.php';\"", $printer);
		    $page->add_element($form, 'refresh');	
		}	
}

function ZpracujPozdavekNaPruchod( $id_zeme ){
	global $users_class, $db;
	if(isset($_REQUEST['pruchody'])){
		$bezpecny_id_pruchozi = $_REQUEST['pruchody'];

		if( in_array( array($bezpecny_id_pruchozi, 0) , VratSousedy($id_zeme) ) ){
			
			//TODO neptam jestli je v aktualni lize, ale to by nemuselo vadit
			$id_liga = JeUzivatelVLize();
			if ( $id_liga == 0 ){
				return;
			}
			
			if( JeZemHrace( $bezpecny_id_pruchozi, null, $id_liga )){
				$platnost = 1;	
			}
			else{
				//nejsou stejneho hrace 
				$platnost = 0;	
			}
			
			//test zda je akce povolena
			$chyba = '';
			if (!JePovolenaAkce('pruchod', $id_liga, $chyba, MajitelZeme( $id_zeme, $id_liga ), MajitelZeme( sqlsafe($bezpecny_id_pruchozi), $id_liga ))){
				return $chyba;
			}			
			
			//vlozeni dat do DB
			$query = "INSERT INTO `in_game_pruchody`  
					( `id_zeme_odkud`, `id_zeme_kam`, `id_ligy`,`platnost`) VALUES
					('$id_zeme', '$bezpecny_id_pruchozi',
					'".$id_liga."'
					, '$platnost'
					);";
			$db->DbQuery( $query );
		}
		
	}	
}

function ZpracujNaplanovanouPodporu(){
	global $users_class, $db, $page;
	
	//test zda byl odeslan formular
	if(isset($_REQUEST['vyber_zemi'])){
		
		
		//prochazeni vsech odeslanych checkboxu
		for($i=0;$i < $_REQUEST["last_id"];$i++){
			if (isset($_REQUEST["$i"])){
				$id_zeme = sqlsafe($_REQUEST["$i"]);
				
				$akce = sqlsafe($_SESSION['akce']);
				$user_id = $users_class->user_id();
				$id_ligy = JeUzivatelVLize( $user_id );
				$cil = MajitelZeme( $id_zeme, $id_ligy );
				
				//test zda je akce povolena
				$chyba = '';
				if (!JePovolenaAkce('podpora_akce', $id_ligy, $chyba, $user_id, $cil, $akce)){
					return $chyba;
				}
				
				//test na sabotaz - je nutny stab rozvedky
				if( ($akce == 10 || $akce == 11 ) && !MaHracStavbu(23) ){
					return 'Akci lze naplánovat pouze pokud máte postaven štáb rozvědky.';
				}
				
				$query = "SELECT cena FROM podpora_z_domova WHERE id='$akce'";
				$res = $db->Query( $query );
				$row = $db->GetFetchRow( $res );
				
				//test zaplaceni ceny
				if (ZaplatBodyVlivu( $row[0] )){					
					//vlozeni dat do DB
					$query = "INSERT INTO `in_game_podpora`  
							( `id_autor`, `id_cil`, `id_zeme`, `id_ligy`,`id_typ_podpory`) VALUES
							('$user_id', '$cil', '$id_zeme', '$id_ligy', '$akce'
							);";
					$db->DbQuery( $query );
				}
				
			}
		}
		
		//prekresleni menu po nakupu
		$printer = new textPrinter();
	    $form = new textElement("OnLoad=\"top.menu.document.location.href='frame_menu.php';\"", $printer);
	    $page->add_element($form, 'refresh');
		
	}
}

function ZpracujNaplanovanouPodporuNeutralka( $id_zeme ){
	global $users_class, $db, $page, $error;
	
	if(!is_numeric($id_zeme)){
		$error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                        'ID_NOT_SET',
                        "Zpracovavani naplanovane podpory se nezdarilo - nebylo predano ID zeme",null);
	}
	
	//test zda byl odeslan formular
	if(isset($_REQUEST['rozkaz_podpora'])){
					
		$akce = sqlsafe($_REQUEST['akce']);

		
		$query = "SELECT cena FROM podpora_z_domova WHERE id='$akce'";
		$res = $db->Query( $query );
		$row = $db->GetFetchRow( $res );
		
		$user_id = $users_class->user_id();
		$id_ligy = JeUzivatelVLize( $user_id );

		$cil = MajitelZeme( $id_zeme, $id_ligy );
			if(!is_numeric($cil)){
				$cil = 0;
			}
		
		//test na sabotaz - je nutny stab rozvedky
		if( ($akce == 10 || $akce == 11 ) && !MaHracStavbu(23) ){
			return 'Akci lze naplánovat pouze pokud máte postaven štáb rozvědky.';
		}	
			
		//test zda je akce povolena
		$chyba = '';
		if (!JePovolenaAkce('podpora_akce', $id_ligy, $chyba, $user_id, $cil, $akce)){
			return $chyba;
		}
		
		//test zaplaceni ceny
		if (ZaplatBodyVlivu( $row[0] )){
			//vlozeni dat do DB
			$query = "INSERT INTO `in_game_podpora`  
					( `id_autor`, `id_cil`, `id_zeme`, `id_ligy`,`id_typ_podpory`) VALUES
					('$user_id', '$cil', '$id_zeme', '$id_ligy', '$akce'
					);";
			$db->DbQuery( $query );
			
			$page->redir("frame_akcni_menu.php?section=veleni");
		}
						
	}
	
	//prekresleni menu po nakupu
	$printer = new textPrinter();
    $form = new textElement("OnLoad=\"top.menu.document.location.href='frame_menu.php';\"", $printer);
    $page->add_element($form, 'refresh');
		
	
}

function ZpracujNaplanovanouLeteckouAkci(){
	global $users_class, $db, $page, $CONST, $ne_letectvo_pocasi;
	$user_id = $users_class->user_id();
	$id_ligy = JeUzivatelVLize( $user_id );
	$vysledek = "OK";
	
	//test zda byl odeslan formular
	if(isset($_REQUEST['vyber_zemi'])){		
		//testovani limitu na letecke akce
		$sum = 0;
		for($i=0;$i < $_REQUEST["last_id"];$i++){
			if (isset($_REQUEST["$i"])){
				$sum++;
			}
		}
		//test zda pocasi umoznuje pouziti letectva
		$res = $db->Query("SELECT id_pocasi_dnes FROM ligy WHERE id='$id_ligy'");
		$row = $db->GetFetchRow( $res );
		$id_pocasi = $row[0];
		if(in_array($id_pocasi,$ne_letectvo_pocasi)){
			return 'Letectvo je v dnešním počasí nepoužitelné.';
		}
		//test zda je naplanovanych akci vice nez je mozne
		if ($sum > 
			(MaximalniPocetLeteckychUtoku( $user_id ) - 
			AktualniPocetLeteckychUtoku( $user_id ))){
				return "Vyčerpal jste všechny možné letecké útoky";
			}
		//prochazeni vsech odeslanych checkboxu
		for($i=0;$i < $_REQUEST["last_id"];$i++){
			if (isset($_REQUEST["$i"])){
				$id_zeme = sqlsafe($_REQUEST["$i"]);
				
				$akce = sqlsafe($_SESSION['akce']);
				
				$query = "SELECT letecka_sila FROM in_game_hrac WHERE id_hrac='".$user_id."'";
				$res4 = $db->Query( $query );
				$row4 = $db->GetFetchRow( $res4 );
				$letecka_sila = $row4[0];
				
				$sila = mt_rand($letecka_sila / $CONST["MINIMUM_LS"],$letecka_sila);
				
				//pocasi ktere zvysuje silu akci
				if($id_pocasi == 2){
					$sila *= $CONST['pocasi_jasno_letecky_bonus'];
				}
				
				$query = "SELECT cena_suroviny,cena_palivo FROM letecke_akce WHERE id='$akce'";
				$res = $db->Query( $query );
				$row = $db->GetFetchRow( $res );
				
				$cil = MajitelZeme( $id_zeme, $id_ligy );
					
				//test zda je akce povolena
				$error = '';
				if (!JePovolenaAkce('letecka_akce', $id_ligy, $error, $user_id, $cil, $akce)){
					return $error;
				}
				
				//test zaplaceni ceny
				$s = $row[0];
				$p = $row[1];
				if (ZaplatSurovinyAPalivo( $s, $p)){					
					//vlozeni dat do DB
					$query = "INSERT INTO `in_game_letecke_akce`  
							( `id_autor`, `id_cil`, `id_zeme`, `id_ligy`,`id_typ_letecke_akce`,`sila`) VALUES
							('$user_id', '$cil', '$id_zeme', '$id_ligy', '$akce', '$sila'
							);";
					$db->DbQuery( $query );
				}
				else{
					$vysledek = "malo_zdroju";
				}			
			}
		}
		
		//prekresleni menu po nakupu
	    $form = new textElement("OnLoad=\"top.menu.document.location.href='frame_menu.php';\"");
	    $page->add_element($form, 'refresh');
		
			//test, zda se naplanovaly vsechny akce
		if ( $vysledek=='malo_zdroju' ){
			return "Nedostatek surovin - některé akce se nenaplánovaly";
		}
	}
}

function ZpracujNaplanovanouLeteckouAkciNeutralka( $id_zeme){
	global $users_class, $db, $page, $error, $CONST;
	$user_id = $users_class->user_id();
	
	if(!is_numeric($id_zeme)){
		$error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                        'ID_NOT_SET',
                        "Zpracovavani naplanovane letecke akce se nezdarilo - nebylo predano ID zeme",null);
	}
	
	//test zda byl odeslan formular
	if(isset($_REQUEST['rozkaz_letectvo'])){

		//test zda je naplanovanych akci vice nez je mozne
		if (1 > 
			(MaximalniPocetLeteckychUtoku( $user_id ) - 
			AktualniPocetLeteckychUtoku( $user_id ))){
				return "Vyčerpal jste všechny dnešní akce.<br />";
			}
				
		$akce = sqlsafe($_REQUEST['akce']);
		
		$query = "SELECT cena_suroviny,cena_palivo FROM letecke_akce WHERE id='$akce'";
		$res = $db->Query( $query );
		$row = $db->GetFetchRow( $res );
		
		$query = "SELECT letecka_sila FROM in_game_hrac WHERE id_hrac='".$user_id."'";
		$res4 = $db->Query( $query );
		$row4 = $db->GetFetchRow( $res4 );
		$letecka_sila = $row4[0];
		
		$sila = mt_rand($letecka_sila / $CONST["MINIMUM_LS"],$letecka_sila);
		$id_ligy = JeUzivatelVLize( $user_id );
		$cil = MajitelZeme( $id_zeme, $id_ligy );
		
		//test zda je akce povolena
		$chyba = '';
		if (!JePovolenaAkce('letecka_akce', $id_ligy, $chyba, $user_id, $cil, $akce)){
			return $chyba;
		}
		
		//test zaplaceni ceny
		if (ZaplatSurovinyAPalivo( $row[0], $row[1] )){
			// test zda neni zem neutralka
			if(!is_numeric($cil)){
				$cil = 0;
			}
			//vlozeni dat do DB
			$query = "INSERT INTO `in_game_letecke_akce`  
					( `id_autor`, `id_cil`, `id_zeme`, `id_ligy`,`id_typ_letecke_akce`,`sila`) VALUES
					('$user_id', '$cil', '$id_zeme', '$id_ligy', '$akce', '$sila'
					);";
			$db->DbQuery( $query );
			$page->redir("frame_akcni_menu.php?section=letectvo&page=poslat_utok");
		}
				
		
		}
		
		//prekresleni menu po nakupu
		$printer = new textPrinter();
	    $form = new textElement("OnLoad=\"top.menu.document.location.href='frame_menu.php';\"", $printer);
	    $page->add_element($form, 'refresh');

}


function ZpracujOdvolaniUtoku(){
	global $users_class, $db, $page;
	//prochazeni vsech odeslanych checkboxu
	for($i=0;$i < 5;$i++){
		if (isset($_REQUEST["$i"])){
			AM_OdvolaniUtoku( $_REQUEST["$i"] );
		}
	}
}

function ZpracujNavyseniRozpusteniLetectva(){
	global $users_class, $db, $page;
	
	if(isset($_REQUEST['navyseni'])||isset($_REQUEST['rozpusteni'])){
		if(isset($_REQUEST['navyseni'])){
			$akce = "Navýšit";
		}
		else{
			$akce = "Rozpustit";
		}
		$pocet = sqlsafe($_REQUEST['pocet']);
		$id_hrac = $users_class->user_id();
		
		
		$id_liga = JeUzivatelVLize();
		if (!is_numeric( $pocet ) || $pocet <= 0){
			return "Zadejte jen kladné číslo";
			
		}
		
		$maxLS = MaximalniLeteckaSila( $id_hrac );
		
		$query = "SELECT letecka_sila FROM in_game_hrac WHERE id_hrac='$id_hrac'";
		$res = $db->Query( $query );
		$row = $db->GetFetchRow( $res );
		$letecka_sila = $row[0];
		
		if( $akce == 'Navýšit'){
			if ( $letecka_sila + $pocet > $maxLS ){
				return "Maximální letecká síla je: $maxLS";
			}
					
			for($i = 0; $i < $pocet; $i++){
				//TODO optimalizovat
				if (!ZaplatBodyVlivu( CenaBoduLeteckeSily( $letecka_sila + $i ))){
					break;
				}
			}
			$nove = $i;
			
		}
		else if( $akce == 'Rozpustit'){
			if ($pocet > $letecka_sila){
				$nove = - $letecka_sila;
			}
			else{
				$nove = - $pocet;	
			}
		}
		else{
			$nove = 0;
		}
		
		//vlozeni dat do DB
		$query = "UPDATE in_game_hrac SET letecka_sila = letecka_sila + $nove WHERE 
				 id_hrac='$id_hrac'";
		$db->DbQuery( $query );
		
		//prekresleni menu po nakupu
		$printer = new textPrinter();
	    $form = new textElement("OnLoad=\"top.menu.document.location.href='frame_menu.php';\"", $printer);
	    $page->add_element($form, 'refresh');	
		}	
	
}

function OpravInfrastrukturu( $id_zeme, $id_ligy ){
	global $db, $CONST;
	$query = "SELECT infrastruktura_now 
				FROM in_game_zeme WHERE id_zeme='$id_zeme' and id_ligy='$id_ligy'";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	$inf_now = $row[0];

	//originalni infrastruktura
	$query = "SELECT infrastruktura_original FROM zeme WHERE id='$id_zeme'";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	$inf_max = $row[0];

	if ( $inf_now == $inf_max ){
		return;
	}

	if (!JeZemHrace($id_zeme, null, $id_ligy)){
		return;
	}
	
	if (!ZaplatSuroviny(CenaOpravyInfrastruktury( $id_zeme, $id_ligy ))){
		return;
	}
	
	$cilova = $inf_now + $CONST["PROCENTA_OPRAVENE_INFRASTRUKTURY"];
	if ($inf_now + $CONST["PROCENTA_OPRAVENE_INFRASTRUKTURY"] >$inf_max){
		$cilova = $inf_max;
	}
	
	$query = "UPDATE `in_game_zeme` SET infrastruktura_now = $cilova,
	oprava_infrastruktury = oprava_infrastruktury + 1
	WHERE
		id_zeme='$id_zeme' and id_ligy='$id_ligy'";
	$db->DbQuery($query);
}

function SmazPruchod(){
	global $users_class, $db;
	
	if(isset($_REQUEST['id_do'])){
		$bezpecny_id_kam = sqlsafe($_REQUEST['id_do']);
	}
	if(isset($_REQUEST['id'])){
		$bezpecny_id_odkud = sqlsafe($_REQUEST['id']);
	}
	if (!isset($bezpecny_id_odkud)||!isset($bezpecny_id_kam)){
		return;
	}
	
	$id_user = $users_class->user_id();
	
	$query = "SELECT count(*) FROM zeme_view WHERE (
			(id_zeme='$bezpecny_id_kam')
			or
			(id_zeme='$bezpecny_id_odkud'))
			and(id_vlastnik='$id_user')
			and(id_ligy='".$_SESSION['id_ligy']."')";
	
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	if ($row[0]==0){
		return;
	}
	
	$query = "DELETE FROM in_game_pruchody WHERE (id_ligy=".$_SESSION['id_ligy'].")and
	((id_zeme_odkud= '$bezpecny_id_odkud' and id_zeme_kam= '$bezpecny_id_kam')
	or
	(id_zeme_odkud= '$bezpecny_id_kam' and id_zeme_kam= '$bezpecny_id_odkud')
	)";
	$db->DbQuery( $query );
	
}

function SchvalPruchod(){
		global $users_class, $db;
	//TODO bezpecnost
	if(isset($_REQUEST['id_do'])){
		$bezpecny_id_kam = sqlsafe($_REQUEST['id_do']);
	}
	if(isset($_REQUEST['id'])){
		$bezpecny_id_odkud = sqlsafe($_REQUEST['id']);
	}
	if (!isset($bezpecny_id_odkud)||!isset($bezpecny_id_kam)){
		return;
	}
	
	$id_user = $users_class->user_id();
	
	$query = "SELECT count(*) FROM zeme_view WHERE (
			(id_zeme='$bezpecny_id_kam')
			or
			(id_zeme='$bezpecny_id_odkud'))
			and(id_vlastnik='$id_user')
			and(id_ligy='".$_SESSION['id_ligy']."')";
	
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	if ($row[0]==0){
		return;
	}
	
	$query = "UPDATE in_game_pruchody SET platnost=1 WHERE (id_ligy='".$_SESSION['id_ligy']."')and
	((id_zeme_odkud= '$bezpecny_id_odkud' and id_zeme_kam= '$bezpecny_id_kam')
	or
	(id_zeme_odkud= '$bezpecny_id_kam' and id_zeme_kam= '$bezpecny_id_odkud')
	)";
	$db->DbQuery( $query );
	
	
	
}
?>