<?php
class listOfNovinky extends listElement {
	
	function listOfNovinky($db, $page) {
		global $LANGUAGE, $auth;
		
		$columns = array (
		
		array(
		  		'name' => "datum",
		  		'value' => '{datum}',
		  		'sort' => 'datum'
		  	),
		 array(
		  		'name' => "nazev",
		  		'value' => '{nazev}',
		  		'sort' => 'nazev'
		  	),
		  	array(
		  		'name' => "obsah",
		  		'value' => '{novinka}'
		  	)  	
		);
		
		if ($auth->authorised_to('admin_novinky')) {
			$columns[] = 
				array(
					'name' => '',
					'value' => '<a href="admin.php?section=novinky&amp;page=edit_nov&amp;id={id}">'
					.	'Upravit </a>'
				);
		}
		
		if ($auth->authorised_to('admin_novinky')) {
			$columns[] = array(
				'name' => '',
				'value' => '<a href="admin.php?section=novinky&amp;page=list_nov&amp;action=del_nov&amp;id={id}"'
				. ' onclick="javascript:return confirm(\'Opravdu chcete smazat?\')">'
				. ' Smazat</a>'
			);
		}
		
		parent::listElement($db, $page, "Seznam novinek",
			"novinky",
			$columns, 'admin_univerzity_list');
			
	
	}
	
	
}
?>