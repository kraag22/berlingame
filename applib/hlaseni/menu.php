<?php
$o1 = "odkaz1";
if ($styl=='globalni'){
	$o1 .= "_akt";
}
$o2 = "odkaz2";
if ($styl=='akce'){
	$o2 .= "_akt";
}
$o3 = "odkaz3";
if ($styl=='boj'){
	$o3 .= "_akt";
}

$menu = '
<ul id="hlaseni_menu">
	<li id="'.$o1.'">
	<a href="hlaseni.php?section=globalni" ></a>
	</li>
	<li id="'.$o2.'">
	<a href="hlaseni.php?section=akce" ></a>
	</li>
	<li id="'.$o3.'">
	<a href="hlaseni.php?section=boj" ></a>
	</li>
</ul>

';

$form = new textElement($menu, null);
$page->add_element($form, 'menu');	



$id_ligy = $_SESSION['id_ligy'];
$res2 = $db->Query("SELECT * FROM `ligy` where id='$id_ligy'");
$liga = $db->GetFetchAssoc( $res2 );
$titulek = $liga['nazev'] . ", herní den " . $liga['odehranych_dnu'];

$form = new textElement($titulek);
$page->add_element($form, 'titulek');
?>