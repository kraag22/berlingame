<?php
	//zde se spocita kliknuti dalsiho kola
	
	//ekonomicke funkce
	require_once($DIR_CONFIG . "ekonomika.php");

	//vraceni atributu jednotek
	require_once($DIR_CONFIG . "boj.php");
	
	$id_hrac = $users_class->user_id();
	
	$query = "SELECT akt_pocet_kol, akt_max_pocet_kol, suroviny, palivo
			,body_vlivu
			FROM in_game_hrac
			WHERE id_hrac='$id_hrac'";
	$res = $db->Query( $query );
	$row = $db->GetFetchAssoc( $res );
	
	//TODO zmknout tabulku?
	if ($row['akt_pocet_kol'] < $row['akt_max_pocet_kol']){
		$akt_pocet_kol = $row['akt_pocet_kol'] + 1;
		$suroviny = $row['suroviny'];
		$palivo = $row['palivo'];
		$body_vlivu = $row['body_vlivu'];
		
		$suroviny += CelkoveSurovinyZaKolo( $id_hrac );
		$palivo += CelkovePalivoZaKolo( $id_hrac );
		$body_vlivu += CelkoveBodyVlivuZaKolo( $id_hrac );
		
		//odecteni zoldu jednotek
		$query = "SELECT sum(pechota + pechota_odeslano), sum(tanky + tanky_odeslano) 
					FROM in_game_zeme  WHERE id_vlastnik='$id_hrac'";
		$res = $db->Query( $query );
		$row = $db->GetFetchRow( $res );
		
		$pole = VratAtributyJednotek("pechota");
		$suroviny -= $row[0] * $pole["zold_suroviny"];
		$palivo -= $row[0] * $pole["zold_palivo"];
		
		$pole = VratAtributyJednotek("tanky");
		$suroviny -= $row[1] * $pole["zold_suroviny"];
		$palivo -= $row[1] * $pole["zold_palivo"];
		
		// ulozeni dat
		$query = "UPDATE `in_game_hrac` SET 
				`akt_pocet_kol`= $akt_pocet_kol, 
				`suroviny` = $suroviny,
				`palivo` = $palivo,
				`kol_odehrano` = `kol_odehrano` + 1,
				`body_vlivu` = $body_vlivu					 
				 WHERE id_hrac='$id_hrac'
				;";
		$db->DbQuery( $query );
	
	
	//infrastruktura
	
	ZpracovaniEfektuZenijniBrigadyNaInfrAPovolanavniJednotekHrace( $id_hrac );

	}//konec testu zda provest kolo
	
	
	//prekresleni AM po zmene
	$form = new textElement("OnLoad=\"top.am.document.location.href='frame_akcni_menu.php';\"", null);
	$page->add_element($form, 'refresh');
	
?>