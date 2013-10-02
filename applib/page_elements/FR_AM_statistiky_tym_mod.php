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
	
	$text = '<div id="statistiky">';
		//MENU
		$text .= PrintMenu( array_get_index_safe('page', $_GET), $id_ligy);

		//SUBMENU			
		$text .= PrintSubMenuTymMod( $id_ligy, array_get_index_safe('subpage', $_GET) );
		
		//OBSAH
		switch( array_get_index_safe('subpage', $_GET) ){
			case 'tym_uspesnost':
				$text .= '<div class="tym_prazdny_div">';
				$text .= SeznamTymUspesnost( $id_ligy );
				$text .= '</div>';
				break;
			case 'tym_prestiz':
				$text .= '<div id="stat_obsah_por_sektory">';
				$text .= SeznamTymPrestiz( $id_ligy );
				$text .= '</div>';
				break;
			case 'tym_sestava':
				//FALLTHROUGHT
			default:
				$text .= '<div class="tym_prazdny_div">';
				$text .= SeznamTymSestava( $id_ligy );
				$text .= '</div>';
		}
		
		
		//FOOTER
		$text .= '<div id="stat_footer">';
		$text .= '</div>';
	
		$text .= '</div>';


	
    $form = new textElement($text);
    $page->add_element($form, 'obsah');	
?>