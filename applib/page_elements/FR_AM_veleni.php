<?php
	
	switch( array_get_index_safe('page', $_GET) ) {
        case 'planovani' :
        	require($DIR_LIB . "page_elements/FR_AM_veleni_planovani.php");
        	break;
        case 'vyber_zemi' :
        	require($DIR_LIB . "page_elements/FR_AM_veleni_vyber_zemi.php");
        	break;
        default:
        	require($DIR_LIB . "page_elements/FR_AM_veleni_planovani.php");
        	break;
	}
?>