<?php
$o1 = "odkaz1";
if ($styl==$POSTA_DORUCENA){
	$o1 .= "_akt";
}
$o2 = "odkaz2";
if ($styl==$POSTA_NAPSAT){
	$o2 .= "_akt";
}
$o3 = "odkaz3";
if ($styl==$POSTA_ODESLANA){
	$o3 .= "_akt";
}

$menu = '
<ul id="posta_menu">
	<li id="'.$o1.'">
	<a href="posta.php?section='.$POSTA_DORUCENA.'" ></a>
	</li>
	<li id="'.$o2.'">
	<a href="posta.php?section='.$POSTA_NAPSAT.'" ></a>
	</li>
	<li id="'.$o3.'">
	<a href="posta.php?section='.$POSTA_ODESLANA.'" ></a>
	</li>
</ul>

';

$form = new textElement($menu, null);
$page->add_element($form, 'menu');	
?>