<?php

$max_tut = 5;
$nazev_tut = 'Úvod';

if(!isset($_GET['page'])){
	$page_get="";
}
else{
	$page_get = array_get_index_safe('page', $_GET);
}

switch( $page_get ) {
	case 'tut1':
        require($DIR_LIB . "tutorial/tut1.php");
		break;
	case 'tut2':
        require($DIR_LIB . "tutorial/tut2.php");
		break;
	case 'tut3':
        require($DIR_LIB . "tutorial/tut3.php");
		break;
	case 'tut4':
        require($DIR_LIB . "tutorial/tut4.php");
		break;
	case 'tut5':
        require($DIR_LIB . "tutorial/tut5.php");
		break;
	default:
		require_once("${DIR_LIB}tutorial/tut0.php");
}
?>