<?php
	//funkce
	require_once($DIR_CONFIG . "formulare.php");
	require_once($DIR_LIB . "hlaseni/common.php");

	//ziskani ID ligy a vlozeni do sessions
	if(isset($_GET['id_scenare']) && is_numeric($_GET['id_scenare'])){
		$_SESSION['id_ligy'] = sqlsafe($_GET['id_scenare']);
	}
	else{
		if(!isset($_SESSION['id_ligy'])){
			//pokud se ID nepredalo a neni vyplnene, tak se presmeruji na index
			$page->redir('index.php');
		}
	}
	
	
	$text = "<div class=\"hlaseni_obsah_globalni\">";
	
	$text .= '<div id="kratke_hlaseni_globalni" >'; 
	$id_ligy = $_SESSION['id_ligy'];
	
	$text .= VratPocasi( $id_ligy );
	
	$res = $db->Query("SELECT obsah, nadpis FROM `in_game_hlaseni` 
			where id_ligy='$id_ligy' and skupina='globalni'");
	       while( $row = $db->GetFetchRow( $res )){
	       	$text .= "<div class=\"hlaseni_glob_zprava\">" . $row[0] . "</div>";
	       } 
	
	$text .= '</div>';
	
	//buttony
	$text .= '<a id="button_mapa" href="svet.php" > <span style="display:none;">mapa</span> </a>';
	$text .= '<a id="button_forum" href="forum.php" target="_blank" > <span style="display:none;">forum</span> </a>';
	$text .= '<a id="button_zpet" href="intro.php" > <span style="display:none;">zpet</span> </a>';        
	       
	
	       
	$element = new textElement($text, NULL);
	$page->add_element($element, 'obsah');

?>