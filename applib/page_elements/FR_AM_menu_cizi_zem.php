<?php
$menu = "
  <div id=\"menu\"> \n
  	<ul id=\"menu2\"> \n 
  			<li id=\"odkaz1\"><a href=\"frame_akcni_menu.php?section=info&amp;id=${_SESSION['id_zeme']}\" target=\"am\"><span>Statistika sektoru</span></a></li> \n
  	</ul> \n
  </div> \n
";

$printer = new textPrinter();
$form = new textElement($menu, $printer);
$page->add_element($form, 'menu');	
?>
