<?php
require_once( $DIR_LIB ."administrace/admin_uzivatele_elm.php");

require_once($DIR_CONFIG . "lib.php");

if (isset($_GET['action'])) {
  switch ($_GET['action']) {
  	 case 'odloguj':
  	 	if ($auth->authorised_to('admin_odloguj_hrace')) {
  	 		OdhlasHraceZeScenare(sqlsafe($_GET['id']));
  	 		
  	 		$sql = 'UPDATE users_sys SET multak=multak + 1 WHERE id=\''.sqlsafe($_GET['id'])."'";
  	 		$db->DbQuery($sql);
  	 	}
     break;  
  }
  }
switch ($_GET['page']) {
  case 'list':
  	//FALLTROUGHT
  default:
    $rk_list = new listOfUzivatele($db,$page);
    
    $filter_array = array (1 => array('db_col' => 'nazev', 'name' => "<b>Scénář</b>"),);
    $rk_list->add_filter(new all_values_row_filter('vyber_hodnotu', $filter_array));
    $rk_list->set_page_length( 50 );
    $page->add_element($rk_list,'list_scn');
    break;	
}
?>