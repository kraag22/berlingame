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
		$text .= PrintSubMenuPorovnani( $id_ligy, array_get_index_safe('subpage', $_GET) );
		
		//OBSAH
		switch( array_get_index_safe('subpage', $_GET) ){
			case 'jednotky':
				$text .= '<div id="por_obsah_jednotky">';
				$text .= SeznamPorovnaniJednotky( $id_ligy );
				$text .= '</div>';
				break;
			case 'ls':
				$text .= '<div id="stat_obsah_por_sektory">';
				$text .= SeznamPorovnaniLS( $id_ligy );
				$text .= '</div>';
				break;
			case 'bv':
				$text .= '<div id="stat_obsah_por_sektory">';
				$text .= SeznamPorovnaniBV( $id_ligy );
				$text .= '</div>';
				break;
			case 'sektory':
				//FALLTHROUGHT
			default:
				$text .= '<div id="stat_obsah_por_sektory">';
				$text .= SeznamPorovnaniSektory( $id_ligy );
				$text .= '</div>';
		}
		
		
		//FOOTER
		$text .= '<div id="stat_footer">';
		$text .= '</div>';
	
		$text .= '</div>';


	
    $form = new textElement($text);
    $page->add_element($form, 'obsah');	
?>