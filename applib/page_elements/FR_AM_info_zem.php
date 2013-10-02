<?php
	//ekonomicke funkce
	require_once($DIR_CONFIG . "ekonomika.php");
	//funkce pro pruchody
	require_once($DIR_CONFIG . "boj.php");
	//funkce pro zpracovavani formularu
	require_once($DIR_CONFIG . "formulare.php");
	
	if(!isset($_GET['id'])){
        $error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                        'ID_NOT_SET',
                        "Sekci FR_AM_info nebylo predano zadne ID",null);	
	}
	if(!isset($_SESSION['id_ligy'])){
        $error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                        'ID_NOT_SET',
                        "Sekci FR_AM_info nebylo predano zadne ID_ligy ze session",null);	
	}
	$_SESSION['id_zeme'] = sqlsafe($_GET['id']);
	$bezpecny_id = sqlsafe($_GET['id']);
	$bezpecny_id_ligy = sqlsafe($_SESSION['id_ligy']);
	if ($users_class->is_logged()){
		$id_user = $users_class->user_id();
	}
	else{
		$id_user = -1;
	}
	
	
	if (isset($_REQUEST['action'])){
		switch($_REQUEST['action']){
			case 'del_pruchod':
				SmazPruchod();
				break;
			case 'schval_pruchod':
				SchvalPruchod();
				break;
			case 'opravit':
				OpravInfrastrukturu( $bezpecny_id, $bezpecny_id_ligy);
				
				//prekresleni menu po nakupu
				$printer = new textPrinter();
			    $form = new textElement("OnLoad=\"top.menu.document.location.href='frame_menu.php';\"", $printer);
			    $page->add_element($form, 'refresh');
				break;
		}
	}
	$chyba = '';
	//ZPRACOVAVANI FORMULARU
	$chyba .= ZpracujPozdavekNaPruchod( $bezpecny_id );
	$chyba .= ZpracujNaplanovanouLeteckouAkciNeutralka( $bezpecny_id );
	$chyba .= ZpracujNaplanovanouPodporuNeutralka( $bezpecny_id );
	//FIXME dodelat vypisovani chybovych hlasek
	//ZISKANI INFORMACI O ZEMI
	$query = "SELECT * FROM `zeme_view` where (id_zeme = '$bezpecny_id')
			and(id_ligy = '$bezpecny_id_ligy')";
	$res = $db->Query($query);
	if (! $zeme = $db->GetFetchAssoc($res)){
		$error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                        'DB_ERROR',
                        "Sekci FR_AM_info nebyla naleznena zem k dane lize a ID",null);
	}
	
	// Berlin vs. zbytek zemi
	if($zeme['nazev']=="Berlin"){
		require_once($DIR_LIB . "page_elements/FR_AM_info_berlin.php");
	}
	else{
		//CIZI vs. HRACOVA ZEM
		if ($zeme['id_vlastnik'] == $id_user){
			require_once($DIR_LIB . "page_elements/FR_AM_info_hracova_zem.php");
		}
		else{
			require_once($DIR_LIB . "page_elements/FR_AM_info_cizi_zem.php");
		}	
	}
?>