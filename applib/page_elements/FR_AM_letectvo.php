<?php
	switch(array_get_index_safe('page', $_GET)) {
        case 'akce' :
            require($DIR_LIB . "page_elements/FR_AM_letectvo_akce.php");
            break;
        case 'budovani' :
        	require($DIR_LIB . "page_elements/FR_AM_letectvo_budovani.php");
        	break;
        case 'poslat_utok' :
        	require($DIR_LIB . "page_elements/FR_AM_letectvo_poslat_utok.php");
        	break;
        case 'vyber_zemi' :
        	require($DIR_LIB . "page_elements/FR_AM_letectvo_vyber_zemi.php");
        	break;	
        default:
        	require($DIR_LIB . "page_elements/FR_AM_letectvo_budovani.php");
        	break;
	}
?>