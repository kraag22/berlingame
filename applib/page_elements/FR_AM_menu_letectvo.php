<?php
$menu = "
  <div id=\"menu_letectvo\">
    <ul id=\"menu_letectvo2\">
    	<li id=\"let_anchor1\"><a href=\"frame_akcni_menu.php?section=letectvo&amp;page=budovani\" target=\"am\"><span>Budování letectva</span></a></li>\n
    	<li id=\"let_anchor2\"><a href=\"frame_akcni_menu.php?section=letectvo&amp;page=poslat_utok\" target=\"am\"><span>Poslat letecký útok</span></a></li>\n 
  	</ul>
	</div>\n
";

$form = new textElement($menu, null);
$page->add_element($form, 'menu');	
?>
