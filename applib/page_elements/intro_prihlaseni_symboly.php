<?php if (!defined('BoB')) exit();
	require_once($DIR_CONFIG . "formulare.php");
	require_once($DIR_CONFIG . "konstanty.php");
	require_once($DIR_CONFIG . "lib.php");
	global $page, $users_class;
	
	$id_ligy = JeUzivatelVLize();
	
	//presmerovani po odeslani
  	if ( isset($_REQUEST['id_symbol'])){
  		if(is_numeric( $_REQUEST['id_symbol'] ))
  		//nastav symbol
  		//prideleni zeme hraci
	 	$query = "UPDATE in_game_hrac SET symbol = '".sqlsafe($_REQUEST['id_symbol'])."'
	 			WHERE id_hrac='". $users_class->user_id()."'";
	 	$db->DbQuery($query);
  		
  		$page->redir('info.php?id_scenare='.$id_ligy);
  	}		
			

	$text = "";
	
	$text .= "<div class=\"horni_text\">";
	// HORNI TEXT
	$text .= 'Vyberte si symbol, který označí Vaše území.'; 
	$text .= '</div>'; 
	
	$query = "SELECT strana FROM in_game_hrac WHERE id_hrac='". $users_class->user_id()."'";
	$res2 = $db->Query( $query );
	$row = $db->GetFetchAssoc( $res2 );
	$strana = $row['strana'];
	
	// vlajky
	$text .= "<div class=\"dolni_text\">";
	$text .= VypisVolneSymboly( $id_ligy, $strana );
	$text .= '</div>'; 

	
	$form = new textElement($text);
	$page->add_element($form, 'obsah');
?>