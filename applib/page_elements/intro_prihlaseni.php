<?php if (!defined('BoB')) exit();
	require_once($DIR_CONFIG . "formulare.php");
	require_once($DIR_CONFIG . "konstanty.php");
	require_once($DIR_CONFIG . "lib.php");
	
	global $CONST;
	
	//test zda je hrac prihlasen a nema rozehranou hru
	if(!$users_class->is_logged()){
		$page->redir('index.php');
	}
	

	if ($fm_class->catched('prihlaseni'))
  		{
  			global $page;
			$page->redir('intro.php?section=prihlaseni_symboly');
  		}
	
	$text = "<div class=\"horni_text\">";
	// HORNI TEXT
	$text .= 'Vyberte si scénář, do kterého se chcete přihlásit.'; 
	$text .= '</div>'; 
	
	$query = "SELECT prihlasil FROM `users_hrac` where id_user='".$users_class->user_id()."'";
	$res = $db->Query($query);
	$row = $db->GetFetchAssoc( $res );
	//casove omezeni
	$cas = date("G");
	if ($cas >= $CONST['max_cas_prihlaseni_do_ligy']){
		$text .= "Přihlášení do scénáře je možné jen do 22:00. Můžete se přihlásit zítra.";
	}
	//omezeni logovani na 1x denne
	else if( $row['prihlasil']!=0){
		$text .= "Přihlásit se můžete jen jednou denně. Musíte počkat do zítra.";
	}
	else{
		$text .= $fm_class->create_form('prihlaseni', null, null, $page);
	}
	
       
	$form = new textElement($text);
	$page->add_element($form, 'obsah');
?>