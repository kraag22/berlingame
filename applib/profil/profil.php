<?php

//tooltip
require_once($DIR_LIB . "page_elements/FR_AM_tooltips.php");

if (!empty($_GET['id_hrac'])){
	$id_hrac = sqlsafe($_GET['id_hrac']);
}
else if(!empty($_POST['search_player'])){
	$login = sqlsafe($_POST['search_player']);
	
	$query = "SELECT id FROM users_sys WHERE login='$login'";
	$res = $db->Query( $query );
	if ($row = $db->GetFetchAssoc( $res )){
		$id_hrac = $row['id'];
	}
	else{
		$texty['input_value'] = 'Neexistující hráč';
	}
}
else{
	$texty['input_value'] = 'Neexistující hráč';
}

//pokud neni platne ID hrace, tak nic nehledam
if(isset($id_hrac)&&is_numeric($id_hrac)){
	//zadna chybova hlaska
	$texty['input_value'] = '';
	
	//nacitani dat
	$query = "SELECT * FROM users_sys AS us JOIN users_hrac AS uh JOIN users_stat AS us_stat
	ON us.id=uh.id_user AND us.id=us_stat.id_user
	 WHERE us.id='$id_hrac'";
	$res = $db->Query( $query );
	$data_hrac = $db->GetFetchAssoc( $res );
	
	$texty['nick'] = $data_hrac['login'];
	
	$dat = explode(' ', timestamp_iso_cz($data_hrac['account_created']));
	$texty['zalozen'] = $dat[0];
	
	$texty['odehranych_dnu'] = $data_hrac['odehranych_dnu'];
	
	
	$texty['statistiky'] = '';
	//prevod data
 	$dat = explode(' ', timestamp_iso_cz($data_hrac['last_activity']));
 	list( $hod, $min, $sec) = explode(':', $dat[1]);
	$texty['statistiky'] .= 'Naposledy on-line: ' . $dat[0] . " " . $hod.  ":" . $min . "<br />";
	
	//aktualne hraje
	$query = "SELECT l.id, l.nazev FROM in_game_hrac AS igh JOIN ligy AS l
	ON igh.id_liga=l.id
	 WHERE igh.id_hrac='$id_hrac'";
	$res = $db->Query( $query );
	if($row = $db->GetFetchAssoc( $res )){
		$texty['statistiky'] .= 'Aktuálně hraje: <a href="info.php?id_scenare='.$row['id'].'" target="_blank" style="color:#33CC33;">' 
		. $row['nazev'] . "</a><br />";
	}
	else{
		$texty['statistiky'] .= 'Aktuálně nehraje žádný scénář <br />';
	}
	
	$texty['statistiky'] .= "<br />";
	//DM vyhry
	$query = "SELECT count(*) FROM users_vyhry 
		WHERE id_user='$id_hrac' AND team_vyhra='ne' AND umisteni=1 AND strana IS NOT NULL";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	
	$texty['statistiky'] .= '<span class="vyrazne">Počet výher v DM scénářích: ' . $row[0] . "x</span>";
	
	//DM beta vyhry
	$query = "SELECT count(*) FROM users_vyhry 
		WHERE id_user='$id_hrac' AND team_vyhra='ne' AND umisteni=1 AND strana IS NULL";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	
	if( $row[0] != 0 ){
		$texty['statistiky'] .= " (+beta " .$row[0]. "x)" ;
	}
	$texty['statistiky'] .= "<br />";
	
	//DM vyhry
	$query = "SELECT count(*) FROM users_vyhry WHERE id_user='$id_hrac' AND team_vyhra='ne' AND umisteni=2";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	
	$texty['statistiky'] .= '<span class="vyrazne">Druhé místo v DM scénářích: ' . $row[0] . "x</span><br />";
	
	//DM vyhry
	$query = "SELECT count(*) FROM users_vyhry WHERE id_user='$id_hrac' AND team_vyhra='ne' AND umisteni=3";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	
	$texty['statistiky'] .= '<span class="vyrazne">Třetí místo v DM scénářích: ' . $row[0] . "x</span><br />";

	$texty['statistiky'] .= "<br />";
	//team vyhry
	$query = "SELECT count(*) FROM users_vyhry WHERE id_user='$id_hrac' AND team_vyhra='ano' AND umisteni=1";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	
	$texty['statistiky'] .= '<span class="vyrazne">Počet výher v týmových scénářích: ' . $row[0] . "x</span><br />";
	
	//team členství
	$query = "SELECT count(*) FROM users_vyhry WHERE id_user='$id_hrac' AND team_vyhra='ano' AND umisteni<>1";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	
	$texty['statistiky'] .= '<span class="vyrazne">Účastník vítězného týmu: ' . $row[0] . "x</span><br />";
	
	$texty['statistiky'] .= "<br />";
	
	$pocet = $data_hrac['hranych_team'] + $data_hrac['hranych_deathmatch'] + $data_hrac['hranych_elite_dm'];
	$texty['statistiky'] .= '<span class="vyrazne">Počet dohraných scénářů: ' . $pocet . "</span><br />";
	$texty['statistiky'] .= 'V průběhu scénáře poražen: ' . $data_hrac['znicen_v_ligach'] . "x<br />";
	$texty['statistiky'] .= 'Počet vzdaných scénářů: ' . $data_hrac['opustenych_lig'] . "<br />";
	$texty['statistiky'] .= "<br />";
	
	$query = "SELECT count(*) FROM users_vyhry WHERE id_user='$id_hrac'  AND strana='us'";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	$texty['statistiky'] .= 'Počet her za spojence: ' . $row[0] . "x<br />";
	
	$query = "SELECT count(*) FROM users_vyhry WHERE id_user='$id_hrac'  AND strana='sssr'";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	$texty['statistiky'] .= 'Počet her za rudou armádu: ' . $row[0] . "x<br />";
	
	$query = "SELECT count(*) FROM users_vyhry WHERE id_user='$id_hrac'  AND strana='us' AND umisteni=1";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	$texty['statistiky'] .= 'Počet výher za spojence: ' . $row[0] . "x<br />";
	
	$query = "SELECT count(*) FROM users_vyhry WHERE id_user='$id_hrac'  AND strana='sssr' AND umisteni=1";
	$res = $db->Query( $query );
	$row = $db->GetFetchRow( $res );
	$texty['statistiky'] .= 'Počet výher za rudou armádu: ' . $row[0] . "x<br />";
	$texty['statistiky'] .= "<br />";
	
	$texty['statistiky'] .= 'Počet útoků na hráče/němce: ' . $data_hrac['pocet_utoku']."/" . $data_hrac['pocet_utoku_neutralka']. "<br />";
	
	if( $data_hrac['pocet_utoku'] == 0 ){
		$suma = '-';
	}
	else{
		$suma = round($data_hrac['pocet_uspesnych_utoku']* 100 / $data_hrac['pocet_utoku']). "%";
	}
	$texty['statistiky'] .= 'Úspěšnost útoků na hráče: ' . $suma . "<br />";
	
	
	$texty['statistiky'] .= 'Počet obran: ' . $data_hrac['pocet_obran']."<br />";
	
	
	if( $data_hrac['pocet_obran'] == 0 ){
		$suma = '-';
	}
	else{
		$suma = round($data_hrac['pocet_uspesnych_obran']* 100 / $data_hrac['pocet_obran']). "%";
	}
	$texty['statistiky'] .= 'Úspěšnost obran: ' .$suma."<br />";

	
	$texty['statistiky'] .= 'Naplánoval akcí vrchního velení: ' 
	.(round(($data_hrac['pocet_akci_podpora_pozitivni']+$data_hrac['pocet_akci_podpora_negativni'])/10)*10)."<br />";
	$texty['statistiky'] .= 'Naplánoval leteckých akcí: ' 
	.(round(($data_hrac['pocet_akci_letectvo_pozitivni']+$data_hrac['pocet_akci_letectvo_negativni'])/10)*10)."<br />";
	$texty['statistiky'] .= "<br />";
	
	$query = "select uh.id_user as id, u.login, uh.".T_VITEZNE_BODY."
	FROM users_sys as u inner join users_hrac as uh ON uh.id_user=u.id
	order by uh.".T_VITEZNE_BODY." DESC, uh.odehranych_dnu";
	$res = $db->Query( $query );
	$i = 0;
	$bv=0;
	while ($row = $db->GetFetchAssoc( $res )){
		$i++;
		if( $row['id'] != $id_hrac){
			continue;
		}
		else{
			$bv = $row[T_VITEZNE_BODY];
			break;
		}
	}
	
	$texty['statistiky'] .= 'Celkové pořadí úspěšnosti: <a href="zebricky.php" target="_blank" style="color:#EE2F37;"><strong>' . $i . ".</strong></a><br />";
	$texty['statistiky'] .= "<span onmouseover=\"stm(Text[1],Style[1])\" onmouseout=\"htm()\">";
	$texty['statistiky'] .= '<u>Celkem bodů</u></span>:';
	$texty['statistiky'] .= ' <a href="zebricky.php" target="_blank" style="color:#EE2F37;"><strong>' . $bv . "</strong></a><br />";
	$texty['statistiky'] .= "<br />";
	
} // is_numeric( $id_hrac )

$form = new textElement(TooltipProfilBody());
$page->add_element($form, 'tooltip');	

foreach( $texty as $key => $text ){
	$form = new textElement( $text );       
	$page->add_element($form, $key);
}
?>