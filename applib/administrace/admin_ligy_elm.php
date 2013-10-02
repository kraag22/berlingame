<?php

	require_once($DIR_CONFIG . "konstanty.php");

class listOfLigy extends listElement {
	
	function listOfLigy($db, $page) {
		global $LANGUAGE, $auth;
		
		$columns = array (
		
		array(
		  		'name' => "id",
		  		'value' => '{id}',
		  		'sort' => 'id'
		  	),
		 array(
		  		'name' => "nazev",
		  		'value' => '{nazev}',
		  		'sort' => 'nazev'
		  	),
		  	array(
		  		'name' => "typ",
		  		'value' => '{typ}',
		  		'sort' => 'typ'
		  	),
		  	array(
		  		'name' => "stav",
		  		'value' => '{stav}',
		  		'sort' => 'stav'
		  	),
		  	array(
		  		'name' => "dohrano",
		  		'value' => '{dohrano}',
		  		'sort' => 'dohrano'
		  	),
		  	array(
		  		'name' => "registrace",
		  		'value' => '{registrace}',
		  		'sort' => 'registrace'
		  	),
		  	array(
		  		'name' => "odehranych_dnu",
		  		'value' => '{odehranych_dnu}',
		  		'sort' => 'odehranych_dnu'
		  	),
		  	array(
		  		'name' => "max_pocet_hracu",
		  		'value' => '{max_pocet_hracu}',
		  		'sort' => 'max_pocet_hracu'
		  	),
		  	array(
		  		'name' => "rocni_obdobi",
		  		'value' => '{rocni_obdobi}',
		  		'sort' => 'rocni_obdobi'
		  	) 
		);
		
		if ($auth->authorised_to('admin_ligy')) {
			$columns[] = 
				array(
					'name' => '',
					'value' => '<a href="admin.php?section=ligy&amp;page=edit&amp;id={id}">'
					.	'Upravit </a>'
				);
		}
		
		if ($auth->authorised_to('admin_ligy')) {
			$columns[] = array(
				'name' => '',
				'value' => '<a href="admin.php?section=ligy&amp;page=list&amp;action=obrat_aktiv&amp;id={id}"'
				. ' onclick="javascript:return confirm(\'Opravdu chcete obrátit? Stav ligy bude vymazan.\')">'
				. ' Obrať aktivaci</a>'
			);
		}
		
		parent::listElement($db, $page, "Seznam scénářů",
			"ligy",
			$columns, 'admin_ligy_list');
			
	
	}
	
	
}

function DeaktivujLigu($id_ligy){
	global $db, $CONST;
	
	//vymazani starych dat
	$query = "DELETE FROM in_game_hlaseni WHERE id_ligy='$id_ligy'";
	$db->DbQuery( $query );
	
	$query = "DELETE FROM in_game_letecke_akce WHERE id_ligy='$id_ligy'";
	$db->DbQuery( $query );
	
	$query = "DELETE FROM in_game_podpora WHERE id_ligy='$id_ligy'";
	$db->DbQuery( $query );
	
	$query = "DELETE FROM in_game_pruchody WHERE id_ligy='$id_ligy'";
	$db->DbQuery( $query );
	
	$query = "DELETE FROM in_game_stavby WHERE id_ligy='$id_ligy'";
	$db->DbQuery( $query );
	
	$query = "DELETE FROM in_game_utoky WHERE id_ligy='$id_ligy'";
	$db->DbQuery( $query );
	
	$query = "DELETE FROM in_game_vlivy_letecke_akce WHERE id_ligy='$id_ligy'";
	$db->DbQuery( $query );
	
	$query = "DELETE FROM in_game_vlivy_podpora WHERE id_ligy='$id_ligy'";
	$db->DbQuery( $query );
	
	$query = "DELETE FROM in_game_hrac WHERE id_liga='$id_ligy'";
	$db->DbQuery( $query );
	
	//reset neutralek
	$query = "DELETE FROM in_game_zeme WHERE id_ligy='$id_ligy'";
	$db->DbQuery( $query );
	
	$query = "DELETE FROM in_game_vcera_zeme WHERE id_ligy='$id_ligy'";
	$db->DbQuery( $query );	
	
	$query = "UPDATE ligy SET stav = 'inactive'	WHERE id='$id_ligy'";
	$db->DbQuery( $query );
	
}

function AktivujLigu( $id_ligy ){
	global $db, $CONST;
	
	for ($i=1; $i <=150; $i++){
		$query = "insert  into `in_game_zeme`
		(`id_zeme`,`id_ligy`,`id_vlastnik`,`infrastruktura_now`,
		`pechota`,`tanky`,`letadla`,`sila_neutralka`) values 
		('$i','$id_ligy',NULL,
		${CONST['NEUTRALKA_INFRASTRUKTURA']},
		${CONST['NEUTRALKA_PECHOTA']},
		${CONST['NEUTRALKA_TANKY']},0,${CONST['NEUTRALKA_SILA_1_DEN']})";
		$db->DbQuery( $query );
		
		$query = "insert  into `in_game_vcera_zeme`
		(`id_zeme`,`id_ligy`,`id_vlastnik`,`infrastruktura_now`,
		`pechota`,`tanky`,`letadla`,`sila_neutralka`) values 
		('$i','$id_ligy',NULL,
		${CONST['NEUTRALKA_INFRASTRUKTURA']},
		${CONST['NEUTRALKA_PECHOTA']},
		${CONST['NEUTRALKA_TANKY']},0,${CONST['NEUTRALKA_SILA_1_DEN']})";
		$db->DbQuery( $query );
	}
	
	//reset ukazatelu ligy
	$query = "UPDATE ligy SET registrace = 'ano', dohrano = 'ne', 
				odehranych_dnu = 1, stav='active'
			WHERE id='$id_ligy'";
	$db->DbQuery( $query );
	
}

?>