<?php
	//soubor s funkcemi pro vojsko
	require_once($DIR_CONFIG . "boj.php");

function NaplnPodporaZDomova( $id_user ){
	global $db;
	
	$optiony = '';

	$res = $db->Query("SELECT * FROM podpora_z_domova ORDER BY nazev ");	
	while( $krok = $db->GetFetchAssoc( $res )){
			$optiony .= '<option value="'.$krok['id'].'">&nbsp;&nbsp;&nbsp;&nbsp;'
			.$krok['nazev'].' <span class=\"all_body_vlivu\">('.$krok['cena'].')</span></option>';
	}
	return $optiony;
}

function NaplnCiloveHrace( $id_ligy ){
	global $db;
	$id_ligy = sqlsafe( $id_ligy );
	
	$optiony = "";
	$res = $db->Query("SELECT * FROM in_game_hrac AS igh JOIN users_sys AS us 
					ON us.id=igh.id_hrac 
					WHERE id_liga='$id_ligy' ORDER BY login");
	
	while( $krok = $db->GetFetchAssoc( $res ) ){

			$optiony .= '<option value="'.$krok['id_hrac'].'">'.$krok['login'].'</option>';
	}

	return $optiony;
}

function NaplnLeteckeTypyUtoku(){
	global $db;
	
	$optiony = '';
	$res = $db->Query("SELECT * FROM letecke_akce ORDER BY nazev ");
			
	while( $krok = $db->GetFetchAssoc( $res )){
			$optiony .= '<option value="'.$krok['id'].'">'
			.$krok['nazev'].' (<span class=\"all_suroviny\">'.$krok['cena_suroviny'].'</span>/<span class=\"all_palivo\">'.$krok['cena_palivo'].'</span>)</option>';
	}

	return $optiony;	
}
 
function NaplnSilyLeteckychUtoku( $user_id){
	global $db, $users_class;
	//vytvoreni pole s radky listboxu
	$pole[0] = '<option value="1">I.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;cena x 1</option>';
 	$pole[1] = '<option value="2">II.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;cena x 1.5</option>';
 	$pole[2] = '<option value="3">III.&nbsp;&nbsp;&nbsp;&nbsp;cena x 2</option>';
 	$pole[3] = '<option value="4">IV.&nbsp;&nbsp;&nbsp;cena x 2.5</option>';
	$optiony = "";
 	
	//ziskani letecke sily hrace
	$id_user = $users_class->user_id();
	$res = $db->Query("SELECT letecka_sila FROM in_game_hrac WHERE id_hrac='$id_user'");
	$row = $db->GetFetchRow( $res );
	$letecka_sila = $row[0];

	$max = MaxSilaLeteckehoUtoku( $letecka_sila );
	for ($i=0; $i<$max; $i++ ){
	 	$optiony .= $pole[$i];
	}
	return $optiony;
 }
 
 
 
 
 
 
 
?>