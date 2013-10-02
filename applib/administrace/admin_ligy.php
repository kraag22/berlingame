<?php
require_once( $DIR_LIB ."administrace/admin_ligy_elm.php");
if (isset($_GET['action'])) {
  switch ($_GET['action']) {
  	
  	 case 'obrat_aktiv':
  	 	$bezpecne_id = sqlsafe($_GET['id']);
  	 	
  	 	$db->Query("SELECT * FROM `ligy`
									WHERE `id`='$bezpecne_id'");
		
		$row = $db->GetFetchAssoc();
		
		if ( $row['stav']=='inactive'){
			AktivujLigu($bezpecne_id);
		}
		else{
			DeaktivujLigu($bezpecne_id);	
		}
		

     break;  
  }
  }
switch ($_GET['page']) {
  case 'list':
    $rk_list = new listOfLigy($db,$page);
    $page->add_element($rk_list,'list_scn');
    break;
  case 'add':
  	// pokud je formular uz vyplnen, presmeruji se na seznam log
  	if ($fm_class->catched('admin_ligy'))
  		{
  			global $page;
			$page->redir('admin.php?section=ligy&page=list');
  		}
	    $printer = new textPrinter();
	    $form = new textElement($fm_class->create_form('admin_ligy', null, null, $page) , $printer);
	    $page->add_element($form, 'admin_ligy');
    break;	
    
   case 'edit':
    	if ($fm_class->catched('admin_ligy'))
  		{
  			global $page;
			$page->redir('admin.php?section=ligy&page=list');
  		}
  
		$bezpecne_id = sqlsafe($_GET['id']);
		
		$db->Query("SELECT * FROM `ligy`
									WHERE `id`='$bezpecne_id'");
		
		$row = $db->GetFetchAssoc();
		
		$printer = new textPrinter();
	    $form = new textElement( $fm_class->create_form(
						'admin_ligy', $row, null, $page), $printer);
	    $page->add_element($form, 'admin_ligy');       
    break;
}
?>