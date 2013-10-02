<?php
	switch( array_get_index_safe('page', $_GET) ) {
        case 'centrala':
	        require_once($DIR_LIB . "page_elements/FR_AM_statistiky_centrala.php");
	        break;
        case 'porovnani' :
			require_once($DIR_LIB . "page_elements/FR_AM_statistiky_porovnani.php");
	        break;
        	break;
        case 'tym_mod' :
			require_once($DIR_LIB . "page_elements/FR_AM_statistiky_tym_mod.php");
	        break;
        	break;
        case 'vyber_zemi' :

        	break;
        default:
        	require_once($DIR_LIB . "page_elements/FR_AM_statistiky_centrala.php");
	        break;
        break;
	}
?>