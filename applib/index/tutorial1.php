<?php

$max_tut = 4;
$tut_num = 1;
$nazev_tut = 'Ekonomika';

if(!isset($_GET['page'])){
	$page_get="";
}
else{
	$page_get = array_get_index_safe('page', $_GET);
}

switch( $page_get ) {
	case 'tut1':
        require($DIR_LIB . "tutorial/1tut1.php");
		break;
	case 'tut2':
        require($DIR_LIB . "tutorial/1tut2.php");
		break;
	case 'tut3':
        require($DIR_LIB . "tutorial/1tut3.php");
		break;
	case 'tut4':
        require($DIR_LIB . "tutorial/1tut4.php");
		break;
	case 'tut5':
        require($DIR_LIB . "tutorial/1tut5.php");
		break;
	default:
		require_once("${DIR_LIB}tutorial/1tut1.php");
}
?>