<?php

	if ($fm_class->catched('registrace_novacek'))
  		{
			if (isset($_SESSION['rn_nick']) && isset($_SESSION['rn_heslo'])){
				$users_class->log_user($_SESSION['rn_nick'], $_SESSION['rn_heslo']);
			}
  			
			$page->redir($DIRECTORY . 'intro.php?registrace=ano');
  		}
  		

	$text = '<div class="reg_prvni">';
	$text .= '<strong>Předím než začnete hrát, bychom vám rádi doporučili 
nahlédnutí do <a href="index.php?section=napoveda">nápovědy</a>. Jde o obrázkové tutoriály, které 
prolétnete za pár minut. </strong>';
	$text .= $fm_class->create_form('registrace_novacek', null, null, $page);
	$text .= '<strong>Svou registrací potvrzujete, že jste si přečetl(a) <a href="index.php?section=pravidla" target="_blank">
	pravidla chování ve hře</a> a 
	budete se jimi řídit.</strong>';
	$text .= '</div>';

    $form = new textElement( $text );
    $page->add_element($form, 'obsah');
?>