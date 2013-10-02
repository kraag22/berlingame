<?php
$menu = "
  <div id=\"menu\"> \n
  	<ul id=\"menu2\"> \n 
  			<li id=\"odkaz1\"><a href=\"frame_akcni_menu.php?section=info&amp;id=${_SESSION['id_zeme']}\" target=\"am\"><span>Statistika sektoru</span></a></li> \n
  			<li id=\"odkaz2\"><a href=\"frame_akcni_menu.php?section=vystavba&amp;id=${_SESSION['id_zeme']}\" target=\"am\"><span>Výstavba</span></a></li> \n
  			<li id=\"odkaz3\"><a href=\"frame_akcni_menu.php?section=obrana&amp;id=${_SESSION['id_zeme']}\" target=\"am\"><span>Jednotky &amp; Obrana</span></a></li> \n
  			<li id=\"odkaz4\"><a href=\"frame_akcni_menu.php?section=utok&amp;id=${_SESSION['id_zeme']}\" target=\"am\"><span>Poslat útok</span></a></li> \n
  	</ul> \n
  </div> \n
";

$form = new textElement($menu, null);
$page->add_element($form, 'menu');	
?>
