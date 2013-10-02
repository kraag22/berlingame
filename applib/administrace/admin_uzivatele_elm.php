<?php
	require_once($DIR_CONFIG . "konstanty.php");

class listOfUzivatele extends listElement {
	
	function listOfUzivatele($db, $page) {
		global $LANGUAGE, $auth;
		
		$columns = array (
		
		array(
		  		'name' => "id",
		  		'value' => '{id}',
		  		'sort' => 'id'
		  	),
		array(
		  		'name' => "login",
		  		'value' => '{login}',
		  		'sort' => 'login'
		  	),
		array(
		  		'name' => "scénář",
		  		'value' => '{nazev}',
		  		'sort' => 'nazev'
		  	),  
		array(
		  		'name' => "IP registrace",
		  		'value' => '{ip_adresa_registrace}',
		  		'sort' => 'ip_adresa_registrace'
		  	),
		array(
		  		'name' => "IP posledni",
		  		'value' => '{ip_adresa_posledni}',
		  		'sort' => 'ip_adresa_posledni'
		  	),
		array(
		  		'name' => "cas registrace",
		  		'value' => '{account_created}',
		  		'sort' => 'account_created'
		  	),
		array(
		  		'name' => "cas posledni",
		  		'value' => '{last_login_time}',
		  		'sort' => 'last_login_time'
		  	),
		array(
					'name' => '',
					'value' => '<a href="posta.php?section=2&hrac={login}" target="_blank">'
					.	'Poslat zprávu</a>'
			)
		);
				
		if ($auth->authorised_to('admin_odloguj_hrace')) {
			$columns[] = array(
				'name' => '',
				'value' => '<a href="admin.php?section=uzivatele&amp;page=list&amp;action=odloguj&amp;id={id}"'
				. ' onclick="javascript:return confirm(\'Opravdu hráče odlogovat? Bude vymazán ze scénáře.\')">'
				. ' Odlogovat</a>'
			);
		}
		
		$columns[] = array(
				'name' => "multacil",
		  		'value' => '{multak}',
		  		'sort' => 'multak'
			);
		
		$sql = "(
			SELECT us.*, l.nazev
			FROM users_sys us INNER JOIN in_game_hrac igh ON us.id=igh.id_hrac
			JOIN ligy l ON l.id=igh.id_liga
			) as x
		";
		
		parent::listElement($db, $page, "Seznam hráčů",
			$sql,
			$columns, 'admin_ligy_list');
			
	
	}
	
	
}

?>