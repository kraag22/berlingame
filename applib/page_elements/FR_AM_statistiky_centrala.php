<?php
//soubor s funkcemi pro vojsko
	require_once($DIR_CONFIG . "ekonomika.php");
	
	//soubor s funkcemi pro zpracovani formularu
	require_once($DIR_CONFIG . "formulare.php");
	
	//vraceni atributu jednotek
	require_once($DIR_CONFIG . "boj.php");
	
	// funkce statistik
	require_once($DIR_LIB . "page_elements/FR_AM_statistika_common.php");
	
	$id_hrac = $users_class->user_id(); 
	
	//test zda je uzivatel prihlasen
	$hrac_ligy = false;
	
	$id_ligy = $_SESSION['id_ligy'];
	
	if ($users_class->is_logged()){
		if ($lig = JeUzivatelVLize()){
			if ($id_ligy == $lig)
			{
				$hrac_ligy = true;
			}
		}
	}
	$text = '<div id="statistiky">';
	//MENU
	$text .= PrintMenu( array_get_index_safe('page', $_GET), $id_ligy);
	
	if($hrac_ligy){	
		
		//SUBMENU			
		$text .= PrintSubMenu( array_get_index_safe('subpage', $_GET) );
		
		//OBSAH
		switch( array_get_index_safe('subpage', $_GET) ){
			case 'utoky':
				$text .= '<div id="stat_obsah_utoky">';
				$text .= SeznamUtokuHrace( $id_hrac );
				$text .= '</div>';
				break;
			case 'zisky':
				//FALLTHROUGHT
			default:
				$text .= '<div id="stat_obsah">';
				$text .= SeznamVydelkuZemi( $id_hrac );
				$text .= '</div>';
		}
		
		
		//FOOTER
		$text .= '<div id="stat_footer">';
		$text .= '</div>';
	
		$text .= '</div>';
	}

	
    $form = new textElement($text);
    $page->add_element($form, 'obsah');	
?>