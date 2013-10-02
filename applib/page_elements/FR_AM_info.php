<?php
	//HRAC vs. ZEM
	if (isset($_GET['id_hrac'])){
		require_once($DIR_LIB . "page_elements/FR_AM_info_hrac.php");
	}
	else{
		require_once($DIR_LIB . "page_elements/FR_AM_info_zem.php");
	}	
	
?>