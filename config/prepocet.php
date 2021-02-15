<?php
/**
 * funkce spoustene jen pri prepoctu
 */
	require_once($DIR_CONFIG . "boj.php");
	require_once($DIR_CONFIG . "lib.php");
	require_once($DIR_CONFIG . "formulare.php");
	require_once($DIR_CONFIG . "prepocet_hlaseni.php");
	require_once($DIR_CONFIG . "prepocet_lib.php");
	require_once($DIR_CONFIG . "stavby.php");
	require_once($DIR_CONFIG . "konstanty.php");
	require_once($DIR_CONFIG . "statistiky.php");
	require_once($DIR_LIB . "page_elements/fr_mapa_zobraz_zeme.php");

function VyhodnotPodporuZDomova( $id_ligy ){
	global $db, $CONST, $pole_skodliva_podpora;
	
	//statistiky pole
	$stat_mem = stat_akce_load();
	
	$query = "SELECT * FROM in_game_podpora WHERE id_ligy=$id_ligy";
	$res_hlavni = $db->Query( $query );
	while ( $row = $db->GetFetchAssoc( $res_hlavni )){
		$param['id_zeme'] = $row['id_zeme'];
		$param['id_autor'] = $row['id_autor'];
		$param['id_cil'] = $row['id_cil'];
		$param['id_ligy'] = $id_ligy;
		//login utocnika
		$query = "SELECT login FROM users_sys WHERE id=".$row['id_autor'];
		$res7 = $db->Query( $query );
		$log = $db->GetFetchRow( $res7 );
		$param['utocnik'] = $log[0];
		
		//test zda podpora projde prez policejni stanice
		$query = "SELECT vojenska_policie FROM in_game_zeme WHERE 
					id_zeme=".$row['id_zeme']." AND id_ligy=$id_ligy";

		$res2 = $db->Query( $query );
		$pol = $db->GetFetchRow( $res2 );

		$policie = $pol[0] * $CONST["POLICEJNI_STANICE_BONUS"];
		
		
		//zda je postavena stavba vojenska policie
		if (MaHracStavbuVZemi(20,$row['id_zeme'],$id_ligy)){
			$policie += $CONST["STAVBY_VOJENSKA_POLICIE_BONUS"];
		}
		
		
		$nahoda = mt_rand(0,100);
		//echo '.';
		//pokud neni majitelem ciove zeme 
		if (!JeZemHrace($row['id_zeme'],$row['id_autor'], $id_ligy)){
			//nebo to neni akce, ktera projde vzdy
			if (in_array($row['id_typ_podpory'], $pole_skodliva_podpora)){
				//nebo neprehodi procenta, podpora neprojde
				//echo $nahoda . "," . $policie . "<br>";
				if($nahoda < $policie){
					$param['id_typ_podpory']= $row['id_typ_podpory'];
					add_to_hlaseni($row['id_autor'],"podpora_neprosla",$param);
					add_to_hlaseni($row['id_cil'],"podpora_cizi_ubranena",$param);
					
					//statistiky
					stat_akce($stat_mem,$row['id_autor'],'neuspech',$row['id_typ_podpory'],'podpora');
					continue;
				}
			}
		}
		
		//statistiky
		stat_akce($stat_mem,$row['id_autor'],'uspech',$row['id_typ_podpory'],'podpora',$row['id_cil']);
		
		switch ($row['id_typ_podpory']){
			case 1:
				//FIXME dodelat vyhodnoceni pocasi
				$query = "INSERT INTO in_game_vlivy_podpora(id_zeme, id_ligy, id_podpora)
							VALUES 
							(".$row['id_zeme'].",
							".$row['id_ligy'].",
							".$row['id_typ_podpory'].")
							";
				$db->DbQuery( $query );
				add_to_hlaseni($row['id_autor'],"podpora_pocasi",$param);
				break;
			case 2:
				//VYSADEK - zabiji presouvane vojaky
				$query = "SELECT * FROM in_game_utoky 
						WHERE id_ligy=$id_ligy AND typ='presun' AND 
							((id_zeme_odkud=".$row['id_zeme'].")or
							(id_zeme_kam=".$row['id_zeme']."))";
				$res2 = $db->Query( $query );
				$sum_pechota = 0;
				$sum_tanky = 0;
				//prochazeni presunu sektorem
				while ( $utok = $db->GetFetchAssoc( $res2 )){
					$pechota = $utok['pechota'];
					$tanky = $utok['tanky'];
					$id = $utok['id'];
					$param['ztrata_pechota'] = ceil( $pechota * $CONST["PODPORA_VYSADEK_pechota"] );
					$param['ztrata_tanky'] = ceil( $tanky * $CONST["PODPORA_VYSADEK_tanky"] );
					$param['id_zeme_vysadek'] = $row['id_zeme'];
					$sum_pechota += $param['ztrata_pechota'];
					$sum_tanky += $param['ztrata_tanky'];
					
					//add_to_hlaseni($utok['id_vlastnik'],"zabiti_pri_presunu",$param);

					//zeme ze ktere se utocilo
					$query = "UPDATE `in_game_utoky` SET 
									`pechota` = ".($pechota - $param['ztrata_pechota']).",
									`tanky` = ".($tanky - $param['ztrata_tanky'])."
									 WHERE id=$id
									;";
					$db->DbQuery( $query );
					
				}
				//hlaseni hrace ktery vysadek provedl
				$param['pechota'] = $sum_pechota;
				$param['tanky'] = $sum_tanky;
				$param['id_zeme_vysadek'] =$row['id_zeme'];
				add_to_hlaseni($row['id_autor'],"podpora_vysadek",$param);
				if ($row['id_autor'] != $row['id_cil']){
					add_to_hlaseni($row['id_cil'],"podpora_vysadek_cil",$param);
				}
				break;
			case 3:
				//OCERNUJICI KAMPAN
				$query = "INSERT INTO in_game_vlivy_podpora(id_zeme, id_ligy, id_podpora)
							VALUES 
							(".$row['id_zeme'].",
							".$row['id_ligy'].",
							".$row['id_typ_podpory'].")
							";
				$db->DbQuery( $query );
				add_to_hlaseni($row['id_autor'],"podpora_ocernujici_kampan",$param);
				if ($row['id_autor'] != $row['id_cil']){
					add_to_hlaseni($row['id_cil'],"podpora_ocernujici_kampan_negativni",$param);
				}
				break;
			case 4:
				//PROPAGANDA
				$query = "INSERT INTO in_game_vlivy_podpora(id_zeme, id_ligy, id_podpora)
							VALUES 
							(".$row['id_zeme'].",
							".$row['id_ligy'].",
							".$row['id_typ_podpory'].")
							";
				$db->DbQuery( $query );
				add_to_hlaseni($row['id_autor'],"podpora_propaganda",$param);
				if ($row['id_autor'] != $row['id_cil']){
					add_to_hlaseni($row['id_cil'],"podpora_propaganda_negativni",$param);
				}
				break;
			case 5:
				//ZASOBOVANI ZE VZDUCHU
				$query = "INSERT INTO in_game_vlivy_podpora(id_zeme, id_ligy, id_podpora)
							VALUES 
							(".$row['id_zeme'].",
							".$row['id_ligy'].",
							".$row['id_typ_podpory'].")
							";
				$db->DbQuery( $query );
				add_to_hlaseni($row['id_autor'],"podpora_zasobovani_autor",$param);
				if ($row['id_autor'] != $row['id_cil']){
					add_to_hlaseni($row['id_cil'],"podpora_zasobovani_cil",$param);
				}
				break;
			case 6:
				// PRUZKUM
				$query = "INSERT INTO in_game_vlivy_podpora(id_zeme, id_ligy, id_podpora, param1)
							VALUES 
							(".$row['id_zeme'].",
							".$row['id_ligy'].",
							".$row['id_typ_podpory'].",
							'dnes')
							";
				$db->DbQuery( $query );
				add_to_hlaseni($row['id_autor'],"podpora_pruzkum_autor",$param);
				if ($row['id_autor'] != $row['id_cil']){
					add_to_hlaseni($row['id_cil'],"podpora_pruzkum_cil",$param);
				}
				break;
			case 7:
				// PODMINOVANI
				
				//ma stavbu zenijni brigada v dane zemi?
				$query = "SELECT count(*) FROM in_game_stavby WHERE
							id_stavby=21 AND id_zeme=".$row['id_zeme']." AND
							id_ligy=".$row['id_ligy'];
				$res = $db->Query( $query );
				$zenisti = $db->GetFetchRow( $res );
				if ($zenisti[0]>0 && 
					JeZemHrace($row['id_zeme'], $row['id_autor'], $row['id_ligy'])){
					//vyhodnoceni akce
					$query = "INSERT INTO in_game_vlivy_podpora(id_zeme, id_ligy, id_podpora)
							VALUES 
							(".$row['id_zeme'].",
							".$row['id_ligy'].",
							".$row['id_typ_podpory'].")
							";
					$db->DbQuery( $query );
					add_to_hlaseni($row['id_autor'],"podpora_podminovani_autor",$param);
				}
				else{
					//akce se neprovede
					add_to_hlaseni($row['id_autor'],"podpora_podminovani_neprovedeno",$param);
				}
				//hlaseni pro cizyho hrace
				if ($row['id_autor'] != $row['id_cil']){
					add_to_hlaseni($row['id_cil'],"podpora_podminovani_cil_neprovedeno",$param);
				}
				
				break;
			case 8:
				//MASKOVANI
				$query = "INSERT INTO in_game_vlivy_podpora(id_zeme, id_ligy, id_podpora)
							VALUES 
							(".$row['id_zeme'].",
							".$row['id_ligy'].",
							".$row['id_typ_podpory'].")
							";
				$db->DbQuery( $query );
				add_to_hlaseni($row['id_autor'],"podpora_maskovani_autor",$param);
				if ($row['id_autor'] != $row['id_cil']){
					add_to_hlaseni($row['id_cil'],"podpora_maskovani_cil",$param);
				}
				break;
			case 9:
				//UTOK SNIPERU
				$query = "INSERT INTO in_game_vlivy_podpora(id_zeme, id_ligy, id_podpora)
							VALUES 
							(".$row['id_zeme'].",
							".$row['id_ligy'].",
							".$row['id_typ_podpory'].")
							";
				$db->DbQuery( $query );
				add_to_hlaseni($row['id_autor'],"podpora_utok_sniperu_autor",$param);
				if ($row['id_autor'] != $row['id_cil']){
					add_to_hlaseni($row['id_cil'],"podpora_utok_sniperu_cil",$param);
				}
				break;
			case 10:
				//SABOTAZ
				$query = "INSERT INTO in_game_vlivy_podpora(id_zeme, id_ligy, id_podpora)
							VALUES 
							(".$row['id_zeme'].",
							".$row['id_ligy'].",
							".$row['id_typ_podpory'].")
							";
				$db->DbQuery( $query );
				add_to_hlaseni($row['id_autor'],"podpora_sabotaz_autor",$param);
				if ($row['id_autor'] != $row['id_cil']){
					add_to_hlaseni($row['id_cil'],"podpora_sabotaz_cil",$param);
				}
				break;
			case 11:
				//INFILTRACE
				$query = "INSERT INTO in_game_vlivy_podpora(id_zeme, id_ligy, id_podpora)
							VALUES 
							(".$row['id_zeme'].",
							".$row['id_ligy'].",
							".$row['id_typ_podpory'].")
							";
				$db->DbQuery( $query );
				add_to_hlaseni($row['id_autor'],"podpora_infiltrace_autor",$param);
				if ($row['id_autor'] != $row['id_cil']){
					add_to_hlaseni($row['id_cil'],"podpora_infiltrace_cil",$param);
				}
				break;
			default:
				//FIXME sem by se to nemelo dostat
				echo "nerozpoznana podpora!!";
				$query = "INSERT INTO in_game_vlivy_podpora(id_zeme, id_ligy, id_podpora)
							VALUES 
							(".$row['id_zeme'].",
							".$row['id_ligy'].",
							".$row['id_typ_podpory'].")
							";
				$db->DbQuery( $query );
				break;
			
		}

	}//while	
	
	//vymazani podpory
	$query = "DELETE FROM in_game_podpora WHERE id_ligy=".$id_ligy;
	$db->DbQuery( $query );
	
	//ulozeni statistik
	stat_akce_save( $stat_mem );
	
	return "OK";
}

function VyhodnotLeteckeAkce( $id_ligy ){
	global $db, $CONST;
		
	//statistiky pole
	$stat_mem = stat_akce_load();
	
	$query = "SELECT * FROM in_game_letecke_akce WHERE id_ligy=$id_ligy order by id_typ_letecke_akce";
	$query = "select * from in_game_letecke_akce where id_typ_letecke_akce=1 and id_ligy=$id_ligy
		union
			select * from in_game_letecke_akce where id_typ_letecke_akce=8 and id_ligy=$id_ligy
		union(
			select * from in_game_letecke_akce where id_typ_letecke_akce<>8 and id_typ_letecke_akce<>1 
			and id_ligy=$id_ligy order by id_typ_letecke_akce
		)";
	$res_jinde_nepouzitelne = $db->Query( $query );
	while ( $row = $db->GetFetchAssoc( $res_jinde_nepouzitelne )){
		$param['id_zeme'] = $row['id_zeme'];
		$param['id_autor'] = $row['id_autor'];
		$param['id_cil'] = $row['id_cil'];
		
		//login utocnika
		$query = "SELECT login FROM users_sys WHERE id=".$row['id_autor'];
		$res7 = $db->Query( $query );
		$log = $db->GetFetchRow( $res7 );
		$param['utocnik'] = $log[0];
		
		//test zda letecka akce projde pres PVO
		$PVO = VypocitejPVOZeme( $row['id_zeme'], $id_ligy ); 
		
		//sila je nahodna v intervalu 1/2 az 1 z letecke sily pronasobene silou 
		//naplanovane akce 
		$nahoda = $row['sila'];
		echo $nahoda.",".$PVO . "<br>";
		
		// na sve zeme projde vzdy
		if (!JeZemHrace($row['id_zeme'],$row['id_autor'], $id_ligy)){
			//stihaci hlidka projde vzdy
			if($row['id_typ_letecke_akce'] != 1){
			if($row['id_typ_letecke_akce'] != 6){
			//blizka letecka podpora projde vzdy
				if ($nahoda<= $PVO){
					$param['id_typ_letecke_akce']= $row['id_typ_letecke_akce'];
					add_to_hlaseni($row['id_autor'],"letecka_akce_autor_neuspech",$param);
					add_to_hlaseni($row['id_cil'],"letecka_akce_cil_uspech",$param);
					
					//statistiky
					stat_akce($stat_mem,$row['id_autor'],'neuspech',$row['id_typ_letecke_akce'],'letectvo');
					continue;
				}
			}
			}
		}
		echo "<br />".$row['id_typ_letecke_akce'] . "-";
		//statistiky
		stat_akce($stat_mem,$row['id_autor'],'uspech',$row['id_typ_letecke_akce'],'letectvo',$row['id_cil']);
		switch ($row['id_typ_letecke_akce']){
			case 1:
				//stihaci hlidka
				$query = "INSERT INTO in_game_vlivy_letecke_akce(id_zeme, id_ligy, id_letecke_akce,sila)
							VALUES 
							(".$row['id_zeme'].",
							".$row['id_ligy'].",
							".$row['id_typ_letecke_akce'].",
							".$row['sila'].")
							";
				$db->DbQuery( $query );
				$param['sila_akce'] = $row['sila'];
				add_to_hlaseni($row['id_autor'],"letecka_akce_stihaci_hlidka_autor",$param);
				if ($row['id_autor'] != $row['id_cil']){
					add_to_hlaseni($row['id_cil'],"letecka_akce_stihaci_hlidka_cil",$param);
				}
				break;
			case 2:
				//takticke bombardovani
				//ziskam z DB bunkr, ktery muzu zbourat
				$query = "SELECT id, id_stavby FROM in_game_stavby WHERE
							id_zeme =".$row['id_zeme']." and id_ligy=".$row['id_ligy'] . "
							and(id_stavby=11)";
				$res3 = $db->Query( $query );
								
				//vynulovani predavanych parametru
				$param['stavba_11']='zustava';
				while ($stavby = $db->GetFetchAssoc( $res3 )){
					$nahoda = mt_rand(0,100);
					//test zda se stavba znici
					if ($nahoda >= $CONST["LETECKA_AKCE_takticke_bombardovani_sance"]){
						$param['stavba_'.$stavby['id_stavby']] = "zbourana";
						
						//bunkr
						if ($stavby['id_stavby'] == 11){
							$query = "DELETE FROM in_game_stavby WHERE id=".$stavby['id'];
							$db->DbQuery($query);
						}
					}
				}
				
				add_to_hlaseni($row['id_autor'],"letecka_akce_takticke_bombardovani_autor",$param);
				if ($row['id_autor'] != $row['id_cil']){
					add_to_hlaseni($row['id_cil'],"letecka_akce_takticke_bombardovani_cil",$param);
				}				
				break;
			case 3:
				//nalet na letiste
				//ziskam z DB stavby ktere muzu zbourat
				$query = "SELECT id, id_stavby FROM in_game_stavby WHERE
							id_zeme =".$row['id_zeme']." and id_ligy=".$row['id_ligy'] . "
							and( 
							(id_stavby=1)or( id_stavby=2)or( id_stavby=3)
							)
							";
				$res3 = $db->Query( $query );
				//vynulovani predavanych parametru
				$param['stavba_1']='zustava';$param['stavba_2']='zustava';$param['stavba_3']='zustava';
				while ($stavby = $db->GetFetchAssoc( $res3 )){
					$nahoda = mt_rand(0,100);						
					$sance = $CONST["LETECKA_AKCE_nalet_na_letiste_sance"];
					
					//test zda se stavba znici
					if ($nahoda >= $sance){
						$param['stavba_'.$stavby['id_stavby']] = "zbourana";
						$query = "DELETE FROM in_game_stavby WHERE id=".$stavby['id'];
						$db->DbQuery($query);
					}
				}
				//pokud se znici letiste, snizim LS
				if ( $param['stavba_1'] == 'zbourana' ){
					$add_ls = $CONST["STAVBY_letiste_add_ls"];
					$db->DbQuery( "UPDATE in_game_hrac SET letecka_sila = letecka_sila - $add_ls 
							WHERE id_hrac = " . $row['id_cil'] );
				}
				
				add_to_hlaseni($row['id_autor'],"letecka_akce_nalet_na_letiste_autor",$param);
				if ($row['id_autor'] != $row['id_cil']){
					add_to_hlaseni($row['id_cil'],"letecka_akce_nalet_na_letiste_cil",$param);
				}	
				break;
			case 4:
				//nalet na komunikace
				$query = "SELECT infrastruktura_now,id FROM in_game_zeme WHERE
							id_zeme =".$row['id_zeme']." and id_ligy=".$row['id_ligy'];
				$res4 = $db->Query( $query );
				$inf = $db->GetFetchRow( $res4 );
				
				$infrastruktura = $inf[0];
				$id_zeme_unikatni = $inf[1];
				
				$nova_infra = $infrastruktura - $CONST["LETECKA_AKCE_nalet_na_komunikace_sance"];
				if ( $nova_infra <= 0 ){
					if ( $infrastruktura <= 0 ){
						$ztrata_inf = 0;
					}
					else{
						$ztrata_inf = $infrastruktura;
					}
					$nova_infra = 0;
				}
				else{
					$ztrata_inf = $CONST["LETECKA_AKCE_nalet_na_komunikace_sance"];
				}
				$param['ztrata_inf'] = $ztrata_inf;
				
				//zeme ze ktere se utocilo
				$query = "UPDATE `in_game_zeme` SET 
									`infrastruktura_now` = ".$nova_infra."
									 WHERE id=$id_zeme_unikatni
									;";
				$db->DbQuery( $query );
					
				add_to_hlaseni($row['id_autor'],"letecka_akce_nalet_na_komunikace_autor",$param);
				if ($row['id_autor'] != $row['id_cil']){
					add_to_hlaseni($row['id_cil'],"letecka_akce_nalet_na_komunikace_cil",$param);
				}	
				break;
			case 5:
				//strategicke bombardovani
				//ziskam z DB stavby ktere muzu zbourat
				$query = "SELECT id, id_stavby FROM in_game_stavby WHERE
							id_zeme =".$row['id_zeme']." and id_ligy=".$row['id_ligy'] . "
							and( 
							(id_stavby=4)or( id_stavby=5)or( id_stavby=6)
							)
							";
				$res5 = $db->Query( $query );
				//vynulovani predavanych parametru
				$param['stavba_4']='zustava';$param['stavba_5']='zustava';$param['stavba_6']='zustava';
				while ($stavby = $db->GetFetchAssoc( $res5 )){
					$nahoda = mt_rand(0,100);
					//test zda se stavba znici
					if ($nahoda <= $CONST["LETECKA_AKCE_strategicke_bombardovani_sance"]){
						$param['stavba_'.$stavby['id_stavby']] = "zbourana";
						$query = "DELETE FROM in_game_stavby WHERE id=".$stavby['id'];
						$db->DbQuery($query);
					}
				}
				
				add_to_hlaseni($row['id_autor'],"letecka_akce_bombardovani_autor",$param);
				if ($row['id_autor'] != $row['id_cil']){
					add_to_hlaseni($row['id_cil'],"letecka_akce_bombardovani_cil",$param);
				}	
				break;
			case 6:
				//blizka letecka podpora
				$query = "SELECT * FROM in_game_utoky 
						WHERE id_ligy=$id_ligy AND typ='utok' AND 
							id_zeme_kam=".$row['id_zeme']."";
				$res2 = $db->Query( $query );
				
				$sum_tanky = 0;
				//prochazeni utoku na sektor
				while ( $utok = $db->GetFetchAssoc( $res2 )){
					$tanky = $utok['tanky'];
					$id = $utok['id'];
					$param['ztrata_tanky'] = ceil( $tanky * $CONST["LETECKA_AKCE_BLIZKA_LETECKA_PODPORA_TANKY_UCINEK"] );
					$param['id_zeme_BLP'] = $row['id_zeme'];
					$sum_tanky += $param['ztrata_tanky'];
					$param['id_ligy'] = $id_ligy;
					
					add_to_hlaseni($utok['id_vlastnik'],"zabiti_pri_utoku",$param);

					//FIXME ZMENIT SILU UTOKU
					$zmensena_sila = ZmenseniUtoku(0, $param['ztrata_tanky'],$utok['sila'],$utok['id_vlastnik']);
					//zmena utoku
					$query = "UPDATE `in_game_utoky` SET 
									`tanky` = ".($tanky - $param['ztrata_tanky']).",
									`sila` = $zmensena_sila
									 WHERE id=$id
									;";
					$db->DbQuery( $query );
					
				}
				//hlaseni hrace ktery BLP provedl
				$param['tanky_zniceno'] = $sum_tanky;
				$param['id_zeme_vysadek'] =$row['id_zeme'];
				add_to_hlaseni($row['id_autor'],"letecka_akce_blizka_podpora_autor",$param);
				if ($row['id_autor'] != $row['id_cil']){
					add_to_hlaseni($row['id_cil'],"letecka_akce_blizka_podpora_cil",$param);
				}

				break;
			case 7:
				//stremhlavy nalet
				$query = "SELECT pechota, tanky, id FROM in_game_zeme WHERE
							id_zeme =".$row['id_zeme']." and id_ligy=".$row['id_ligy'];
				$res7 = $db->Query( $query );
				$jednotky = $db->GetFetchRow( $res7 );
				
				$pechota = $jednotky[0];
				$tanky = $jednotky[1];
				$id_zeme_unikatni = $jednotky[2];
				$vliv_t = 1;
				$vliv_p = 1;
				//vyhodnoceni vlivu maskovani
				$query = "SELECT count(*) FROM in_game_vlivy_podpora WHERE 
						id_podpora=8 and 
						id=$id_zeme_unikatni";
				$res1 = $db->Query( $query );
				$maskovani = $db->GetFetchRow( $res1 );
				
				if ($maskovani[0] > 0){
					$vliv_t -= $CONST["PODPORA_maskovani_tanky"];
				}
				
				//efekt stavby zemljanka
				if (MaHracStavbuVZemi(14,$row['id_zeme'],$row['id_ligy'])){
					$vliv_p *= $CONST["STAVBY_ZEMLJANKA_PVO_BONUS"];
				}
				
				//efekt stavby Kryt
				if (MaHracStavbuVZemi(12,$row['id_zeme'],$row['id_ligy'])){
					$vliv_p *= $CONST["STAVBY_KRYT_PVO_BONUS"];
				}

				$ztraty_pechota = floor ($pechota * $CONST["LETECKA_AKCE_stremhlavy_nalet_PECHOTA_UCINEK"] * $vliv_p);
				$ztraty_tanky = floor ($tanky * $CONST["LETECKA_AKCE_stremhlavy_nalet_TANKY_UCINEK"] * $vliv_t);

				$param['ztraty_pechota'] = $ztraty_pechota;
				$param['ztraty_tanky'] = $ztraty_tanky;
				
				//zeme ze ktere se utocilo
					$query = "UPDATE `in_game_zeme` SET 
									`pechota` = ".($pechota - $ztraty_pechota).",
									`tanky` = ".($tanky - $ztraty_tanky)."
									 WHERE id=$id_zeme_unikatni
									;";
					$db->DbQuery( $query );

				add_to_hlaseni($row['id_autor'],"letecka_akce_stremhlavy_nalet_autor",$param);
				if ($row['id_autor'] != $row['id_cil']){
					add_to_hlaseni($row['id_cil'],"letecka_akce_stremhlavy_nalet_cil",$param);
				}
				break;
			case 8:
				//cilene bombardovani
				//ziskam z DB stavby ktere muzu zbourat
				$query = "SELECT id, id_stavby FROM in_game_stavby WHERE
							id_zeme =".$row['id_zeme']." and id_ligy=".$row['id_ligy'] . "
							and( 
							(id_stavby=20)or( id_stavby=18)
							)
							";
				$res3 = $db->Query( $query );
				
				//zisteni poctu stanic
				$query = "SELECT * FROM in_game_zeme WHERE id_zeme =".$row['id_zeme']." and id_ligy=".$row['id_ligy'];
				$res2 = $db->Query( $query );
				$stanice = $db->GetFetchAssoc( $res2 );
				
				//vynulovani predavanych parametru
				$param['stavba_18']='zustava';$param['stavba_20']='zustava';
				$param['stavba_voj_pol']='zustava';$param['stavba_flak']='zustava';
				while ($stavby = $db->GetFetchAssoc( $res3 )){
					$nahoda = mt_rand(0,100);
					//test zda se stavba znici
					if ($nahoda >= $CONST["LETECKA_AKCE_cilene_bombardovani_sance"]){
						$param['stavba_'.$stavby['id_stavby']] = "zbourana";
						
						//MP stanice
						if ($stavby['id_stavby'] == 20){
							if($stanice['vojenska_policie']>0){
								$query = "UPDATE  in_game_zeme SET vojenska_policie = vojenska_policie - 1
									WHERE id_zeme =".$row['id_zeme']." and id_ligy=".$row['id_ligy'];
								$db->DbQuery($query);
							}
							//pokud tam nejsou stanice, neda se zadna zbourat
							else{
								//vojenska policie
								$query = "DELETE FROM in_game_stavby WHERE id_stavby=20 AND 
									id_zeme=".$row['id_zeme']." AND id_ligy=".$row['id_ligy'];
								$db->DbQuery($query);
								$param['stavba_voj_pol'] = "zbourana";
								//stanice znicena nebyla
								$param['stavba_'.$stavby['id_stavby']] = "zustava";
							}
						}
						//PVO stanoviste
						if ($stavby['id_stavby'] == 18){
							if($stanice['pvo_stanice']>0){
								$query = "UPDATE  in_game_zeme SET pvo_stanice = pvo_stanice - 1
									WHERE id_zeme =".$row['id_zeme']." and id_ligy=".$row['id_ligy'];
								$db->DbQuery($query);
							}
							//pokud tam nejsou stanice, neda se zadna zbourat
							else{
								// flak
								$query = "DELETE FROM in_game_stavby WHERE id_stavby=18 AND 
									id_zeme=".$row['id_zeme']." AND id_ligy=".$row['id_ligy'];
								$db->DbQuery($query);
								
								$param['stavba_flak'] = "zbourana";
								
								//stanice znicena nebyla
								$param['stavba_'.$stavby['id_stavby']] = "zustava";
							}
						}						
					}
				}
				
				add_to_hlaseni($row['id_autor'],"letecka_akce_cilene_bombardovani_autor",$param);
				if ($row['id_autor'] != $row['id_cil']){
					add_to_hlaseni($row['id_cil'],"letecka_akce_cilene_bombardovani_cil",$param);
				}				
				break;
			default:
				//FIXME sem by se to nemelo dostat
				$query = "INSERT INTO in_game_vlivy_podpora(id_zeme, id_ligy, id_podpora)
							VALUES 
							(".$row['id_zeme'].",
							".$row['id_ligy'].",
							".$row['id_typ_podpory'].")
							";
				$db->DbQuery( $query );
				break;
			
		}

	}//while		
	
	
	//vymazani let akci
	$query = "DELETE FROM in_game_letecke_akce WHERE id_ligy=".$id_ligy;
	$db->DbQuery( $query );
	
	//ulozeni statistik
	stat_akce_save( $stat_mem );
	return "OK";
}

/**
 * Funkce vypocita nove sily neutralnich zemi v lize
 *
 * @param unknown_type $id_ligy
 * @return chybova hlaska nebo OK. Je nasledne zapsan do souboru
 */
function NoveSilyNeutralek( $id_ligy ){
	global $db;
	
	//pokud je nulty den, nepridavam silu neutralkam
	$query = "SELECT odehranych_dnu FROM ligy WHERE id=$id_ligy ";
	$ress = $db->Query( $query );
	$od = $db->GetFetchAssoc( $ress );
	if ($od['odehranych_dnu']==0){
		return "OK";
	}
	
	$query = "SELECT * FROM in_game_zeme WHERE id_ligy=$id_ligy and id_vlastnik IS NULL";
	$res_jinde_nepouzitelne = $db->Query( $query );
	$db->DbQuery( "START TRANSACTION;" );
	while ( $zem = $db->GetFetchAssoc( $res_jinde_nepouzitelne )){
		$id_zeme = $zem['id_zeme'];
		$pechota = mt_rand( 1, 6);
		$tanky = mt_rand( 1, 3);
		$query = "UPDATE `in_game_zeme` SET 
					pechota = pechota + $pechota,
					tanky = tanky + $tanky
					 WHERE id_zeme=$id_zeme and id_ligy=$id_ligy
					;";
		$db->DbQuery( $query );
		
		//zkreslena obrana neutralky
		$obrana = round((1 + mt_rand(5,10)/100) * VypocitejObranuZeme($id_zeme, $id_ligy));
		$query = "UPDATE `in_game_zeme` SET 
					sila_neutralka =  $obrana
					 WHERE id_zeme=$id_zeme and id_ligy=$id_ligy
					;";
		$db->DbQuery( $query );
		
	}
	$db->DbQuery( "COMMIT;" );
	
	return "OK";
}

function EfektyNaHracePredPrepoctem( $id_ligy ){
	global $db, $CONST;
	
	//pridani kol
	$query = "SELECT * FROM in_game_hrac WHERE 
			id_liga=". $id_ligy;
	$res2 = $db->Query( $query );
	$db->DbQuery( "START TRANSACTION;" );
	while ($hrac = $db->GetFetchAssoc( $res2 )){	
		$akt_maximalni = $hrac['akt_max_pocet_kol'];
		$akt_odehrano = $hrac['akt_pocet_kol'];
		
		$query = "SELECT * FROM generalove WHERE 
				id=". $hrac['id_general'];
		$res8 = $db->Query( $query );
		$kola = $db->GetFetchAssoc( $res8 );
		
		$maximalni =  $kola['max_pocet_kol'];
		
	
		if ( $akt_odehrano == $akt_maximalni){
			$new_maximalni = $maximalni;
		}
		else{
			$new_maximalni = $akt_maximalni - $akt_odehrano + $maximalni;
		}
		
		//omezeni maximalniho poctu kol, ktera muze hrac mit
		if ($new_maximalni > $CONST['maximalni_pocet_kol']){
			$new_maximalni = $CONST['maximalni_pocet_kol'];
		}
		
		$query = "UPDATE `in_game_hrac` SET 
					`akt_pocet_kol` = 0,
					`akt_max_pocet_kol` = $new_maximalni,
					`kol_odehrano` = 0				 
					 WHERE id_hrac=".$hrac['id_hrac']."
					;";
		$db->DbQuery( $query );
	}
	$db->DbQuery( "COMMIT;" );
	
	//vymazani mrtvych hracu
	$query = "SELECT id_hrac FROM in_game_hrac WHERE id_liga=$id_ligy AND id_hrac NOT IN 
			(SELECT id_vlastnik FROM in_game_zeme WHERE id_ligy=$id_ligy AND id_vlastnik IS NOT NULL);";
	$res2 = $db->Query( $query );
	while ($row = $db->GetFetchAssoc( $res2 )){
		OdhlasHraceZeScenare( $row['id_hrac'] );
	}
	
	return "OK";
}
 
function EfektyNaVsechnyHrace(){
	global $db;
	
	$query = "DELETE FROM in_game_vcera_stat";
	$res = $db->DbQuery( $query );
	
	$query = "INSERT INTO in_game_vcera_stat  
			(select * from users_stat)";
	$res = $db->DbQuery( $query );
	
	
	$query = "UPDATE `users_hrac` SET 
					`prihlasil` = 0;";
	$db->DbQuery( $query );
	
	SpocitejVitezneBody();
	
	return "OK";
}

function SpocitejVitezneBody(){
	global $db, $CONST;
	$rok = AKTUALNI_ROK;
	$query = "SELECT uh.id_user, dmv.dm_vyhra, dmd.dm_druhy, dmt.dm_treti, tv.team_vyhra,
		tc.team_clenstvi
	FROM users_hrac uh
	LEFT JOIN (SELECT id_user, count(*) as dm_vyhra FROM users_vyhry 
			WHERE team_vyhra='ne' AND umisteni=1 AND YEAR(datum) = ".$rok." group by id_user ) dmv 
		ON dmv.id_user=uh.id_user
	LEFT JOIN (SELECT id_user, count(*) as dm_druhy FROM users_vyhry 
			WHERE team_vyhra='ne' AND umisteni=2 AND YEAR(datum) = ".$rok." group by id_user ) dmd 
		ON dmd.id_user=uh.id_user 
	LEFT JOIN (SELECT id_user, count(*) as dm_treti FROM users_vyhry 
			WHERE team_vyhra='ne' AND umisteni=3 AND YEAR(datum) = ".$rok." group by id_user ) dmt 
		ON dmt.id_user=uh.id_user	 
	LEFT JOIN (SELECT id_user, count(*) as team_vyhra FROM users_vyhry 
			WHERE team_vyhra='ano' AND umisteni=1 AND YEAR(datum) = ".$rok." group by id_user) tv 
		ON tv.id_user=uh.id_user
	LEFT JOIN (SELECT id_user, count(*) as team_clenstvi FROM users_vyhry 
			WHERE team_vyhra='ano' AND umisteni<>1  AND YEAR(datum) = ".$rok." group by id_user) tc 
		ON tc.id_user=uh.id_user";
	$res = $db->Query( $query );
	$db->DbQuery( "START TRANSACTION;" );
	while( $row = $db->GetFetchAssoc( $res ) ){
		$vb = 0;
		$vb += $CONST["VB_dm_vyhra"] * $row['dm_vyhra'];
		$vb += $CONST["VB_dm_druhy"] * $row['dm_druhy'];
		$vb += $CONST["VB_dm_treti"] * $row['dm_treti'];
		$vb += $CONST["VB_team_vyhra"] * $row['team_vyhra'];
		$vb += $CONST["VB_team_clenstvi"] * $row['team_clenstvi'];
		
		$db->DbQuery( "UPDATE users_hrac SET ".T_VITEZNE_BODY."=$vb WHERE id_user=".$row['id_user'] );
	}
	$db->DbQuery( "COMMIT;" );
	
}

function EfektyNaHrace( $id_ligy ){
	global $db, $CONST;
	
	//zvyseni poctu odehranych dnu a obnoveni moznosti prihlaseni
	$query = "SELECT us.id as id, igh.id as igh_id FROM 
			in_game_hrac AS igh JOIN users_sys AS us 
			ON us.id=igh.id_hrac
 			WHERE igh.id_liga=$id_ligy AND 
 			TO_DAYS(us.last_activity) >= TO_DAYS(NOW() - INTERVAL 1 DAY);";
	
	$res2 = $db->Query( $query );
	$db->DbQuery( "START TRANSACTION;" );
	while ($hrac = $db->GetFetchAssoc( $res2 )){
		
		$query = "UPDATE `users_hrac` SET 
					`odehranych_dnu` = `odehranych_dnu` + 1
					 WHERE id_user=${hrac['id']};";
		$db->DbQuery( $query );
	
	}
	$db->DbQuery( "COMMIT;" );
	
	//vypocitani prestize jen pro team ligy
	$query = "SELECT typ FROM ligy WHERE id=$id_ligy";
	$res7 = $db->Query( $query );
	$row = $db->GetFetchAssoc( $res7 );
	if($row['typ']=='team'){
		$prestiz = array();
		$query = "SELECT * FROM in_game_hrac WHERE id_liga=$id_ligy";
		$res3 = $db->Query( $query );
		while ($row = $db->GetFetchAssoc( $res3 )){
			 $hrac = ZpracujPrestiz( $row['id_hrac'] );
			 if(!empty($hrac)){
			 	$prestiz[$hrac['id']] = $hrac;
			 }
		}
		
		add_to_global_hlaseni($id_ligy, 'prestiz', $prestiz);	
	}

	//snizeni LS na maximum co povoli letiste
	$query = "SELECT id_hrac, letecka_sila FROM in_game_hrac WHERE id_liga=$id_ligy";
	$res3 = $db->Query( $query );
	$db->DbQuery( "START TRANSACTION;" );
	while ($hrac = $db->GetFetchAssoc( $res3 )){
		//zjisteni maxLS
		$maxLS = MaximalniLeteckaSila($hrac['id_hrac']);
		//pokud je LS vetsi nez maximum, snizim
		if ($hrac['letecka_sila'] > $maxLS){
			$query = "UPDATE `in_game_hrac` SET	`letecka_sila` = $maxLS WHERE id_hrac=${hrac['id_hrac']};";
			$db->DbQuery( $query );
			
			add_to_global_hlaseni($id_ligy, 'snizena_LS', $hrac);
		}
		
		//polovina BV zmizi
		$query = "UPDATE `in_game_hrac` SET `body_vlivu` = ROUND(`body_vlivu` / 2)		 
					 WHERE id_hrac=${hrac['id_hrac']};";
		$db->DbQuery( $query );
		
	}
	$db->DbQuery( "COMMIT;" );
	
	//pridani mrtvych hracu do globalniho hlaseni
	$query = "SELECT id_hrac FROM in_game_hrac WHERE id_liga=$id_ligy AND id_hrac NOT IN 
			(SELECT id_vlastnik FROM in_game_zeme WHERE id_ligy=$id_ligy AND id_vlastnik IS NOT NULL);";
	$res2 = $db->Query( $query );
	while ($row = $db->GetFetchAssoc( $res2 )){
		add_to_global_hlaseni($id_ligy, 'porazeny_velitel', $row);
		//statistika zniceneho hrace
		stat_zniceny_hrac( $row['id_hrac'] );
		OdhlasHraceZeScenare( $row['id_hrac'], false);
	}
	
	//odlogovani nehrajicich hracu ze scenare
	// datediff vrati cislo, ktere udava pocet prepoctu, ktere hrac neodehral.
	$query = "SELECT igh.id_hrac FROM in_game_hrac AS igh JOIN users_sys AS us 
		ON us.id=igh.id_hrac
		WHERE datediff( NOW() - interval 1 hour, us.last_login_time ) >=".$CONST['dni_pro_odlognuti'];
	$query .= " AND igh.id_liga=$id_ligy ";
	$query .= " AND us.id IN (SELECT id_vlastnik FROM in_game_zeme WHERE id_ligy=$id_ligy AND id_vlastnik IS NOT NULL);";
	$res2 = $db->Query( $query );
	while ($row = $db->GetFetchAssoc( $res2 )){
		add_to_global_hlaseni($id_ligy, 'odlog_pro_necinnost', $row);
		
		OdhlasHraceZeScenare( $row['id_hrac'] );
	}
	
	return "OK";
}

/**
 * Funkce zkopiruje do tabulek in_game_vcera stav ligy po prepoctu
 *
 * @param unknown_type $id_ligy
 * @return chybova hlaska nebo OK. Je nasledne zapsan do souboru
 */
function KopirovaniStavuLigy( $id_ligy ){
	global $db;
	//smazani starych dat
	$query = "DELETE FROM in_game_vcera_zeme WHERE id_ligy=$id_ligy";
	$res = $db->DbQuery( $query );
	
	$query = "INSERT INTO in_game_vcera_zeme  
			(select * from in_game_zeme where id_ligy=$id_ligy)";
	$res = $db->DbQuery( $query );
	
	$query = "DELETE FROM in_game_vcera WHERE id_ligy=$id_ligy";
	$res = $db->DbQuery( $query );
	
	$query = "INSERT INTO in_game_vcera  
			(select id, id_hrac, letecka_sila, id_liga from in_game_hrac where id_liga=$id_ligy)";
	$res = $db->DbQuery( $query );
			
	//vygenerujeme mapu
	GenerujMapu( $id_ligy );
	
	return "OK";
}


function ResetPromenychUZemi( $id_ligy ){
	global $db;
	
	//vynulovani poctu oprav infrastruktury, povolano
	$query = "UPDATE in_game_zeme SET oprava_infrastruktury = 0, povolano = 0,
	lze_povolat = 0
			WHERE id_ligy=$id_ligy";
	$res = $db->DbQuery( $query );
		
	return "OK";
}

function ZpracovaniStavuLigy( $id_ligy ){
	global $db, $CONST;
	
	// pricteni poctu odehranych dnu u aktivnich lig
	$query = "UPDATE ligy SET odehranych_dnu = odehranych_dnu + 1 
		where id = $id_ligy AND stav='active'";
	$res = $db->DbQuery( $query );
	
	
	$query = "SELECT * FROM ligy WHERE 
			id=". $id_ligy;
	$res2 = $db->Query( $query );
	$lig = $db->GetFetchAssoc( $res2 );
	
	//test zda je liga dohrana a spusteni nove
	if($lig['dohrano']=='ano'){
		RestartLigy( $id_ligy );
		return " Restart Ligy - OK";
	}
	
	//povoleni registrace
	if( strpos($lig['typ'], 'elite_') !== false  || $lig['typ']=='team'|| $lig['typ']=='deathmatch'){
		$dny_reg_do = $CONST['LIGA_ELITE_REGISTRACE_DO'];
	}
	else
	{
		$dny_reg_do = $CONST['LIGA_REGISTRACE_DO'];
	}
	
	if( $lig['odehranych_dnu']> $dny_reg_do ){
		$registrace = "ne";
	}
	else{
		$registrace = "ano";
	}
	
	$dohrano = 'ne';

    // neni zadny hrac, ukonci hru
    $query = "SELECT count(*) FROM in_game_hrac WHERE id_liga=". $id_ligy;
    $res1 = $db->Query( $query );
    $hraci = $db->GetFetchRow( $res1 );
    if ($registrace == 'ne' && $hraci[0] == 0) {
        $dohrano = 'ano';
        add_to_global_hlaseni($id_ligy,"liga_ukoncena_necinost");
    }

	switch( $lig['typ'] ){
		case 'trening':
			if ($lig['odehranych_dnu']> $CONST['LIGA_KONEC_TRENINGU']){
					$dohrano = 'ano';
					add_to_global_hlaseni($id_ligy,"liga_trening_dohrana");
				}
			break;
		
		case 'elite_dm':
			//FALLTHROUGHT
		case 'deathmatch':
			//zavreni DM - dnes nekdo?
			$query = "SELECT * FROM in_game_utoky WHERE 
					id_zeme_kam=45 and id_ligy=". $id_ligy. " ORDER BY random DESC ";
			$res5 = $db->Query( $query );
			//splnuje podminky pro vyhru?
			if ($zav = $db->GetFetchAssoc( $res5 )){ 
				if ( SplnujeHracPodminkyProVyhru( $id_ligy, $zav['id_vlastnik'], true )){
					$dohrano = 'ano';
					$param['id_ligy'] = $id_ligy;
					add_to_hlaseni($zav['id_vlastnik'],"liga_dohrana",$param);
					ZapisHraciVyhru($zav['id_vlastnik'], $id_ligy, $lig['odehranych_dnu']);
				}
			}
			break;
		case 'team':
			//zavreni teamu
			$query = "SELECT * FROM in_game_utoky WHERE 
					id_zeme_kam=45 and id_ligy=". $id_ligy;
			$res5 = $db->Query( $query );
			//splnuje podminky pro vyhru?
			if ($zav = $db->GetFetchAssoc( $res5 )){ 
				if ( SplnujeHracPodminkyProVyhru( $id_ligy, $zav['id_vlastnik'], true, 'team' )){
					$dohrano = 'ano';
					$param['id_hrac'] = $zav['id_vlastnik'];
					add_to_global_hlaseni($id_ligy,"liga_team_dohrana",$param);
					ZapisHraciVyhru($zav['id_vlastnik'], $id_ligy, $lig['odehranych_dnu'], 'team');
				}
			}
			break;
	}
	
	//ziska sql pro upravu pocasi
	$pocasi = PocasiSql($lig);
	
	//vynulovani poctu oprav infrastruktury, povolano
	$query = "UPDATE ligy SET registrace = '$registrace', dohrano = '$dohrano', $pocasi
			WHERE id=$id_ligy";
	$res = $db->DbQuery( $query );
		
	return "OK";	
}

?>