<?php
require_once( $DIR_LIB ."administrace/admin_novinky_elm.php");
if (isset($_GET['action'])) {
  switch ($_GET['action']) {
  	
  	 case 'del_nov':
  	 	$bezpecne_id = sqlsafe($_GET['id']);
		$query = "DELETE FROM `novinky` where (id='".$bezpecne_id."')";
		$res = $db->Query($query);

     break;  
  }
  }
switch ($_GET['page']) {
  case 'list_nov':
    $rk_list = new listOfNovinky($db,$page);
    $page->add_element($rk_list,'list_nov');
    break;
  case 'add_nov':
  	// pokud je formular uz vyplnen, presmeruji se na seznam log
  	if ($fm_class->catched('admin_novinky'))
  		{
  			global $page;
			$page->redir('admin.php?section=novinky&page=list_nov');
  		}
	    $printer = new textPrinter();
	    $form = new textElement($fm_class->create_form('admin_novinky', null, null, $page) , $printer);
	    $page->add_element($form, 'admin_novinky');
    break;	
    
   case 'edit_nov':
    	if ($fm_class->catched('admin_novinky'))
  		{
  			global $page;
			$page->redir('admin.php?section=novinky&page=list_nov');
  		}
  
		$bezpecne_id = sqlsafe($_GET['id']);
		
		$db->Query("SELECT * FROM `novinky`
									WHERE `id`='$bezpecne_id'");
		
		$row = $db->GetFetchAssoc();
		
		$printer = new textPrinter();
	    $form = new textElement( $fm_class->create_form(
						'admin_novinky', $row, null, $page), $printer);
	    $page->add_element($form, 'edit_nov');       
    break;
}
?>