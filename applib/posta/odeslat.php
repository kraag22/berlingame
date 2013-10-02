<?php if (!defined('BoB')) exit();

/**
 * @file odeslat.php
 * @brief Pošta uživatele - zpracování formuláře
 */


function posta_odeslat($db) {

	global $db, $users_class,$POSTA_DORUCENA;

	$id_hrac = $users_class->user_id(); 

	$obsah = ZakodujPrizpevek($_REQUEST['obsah']);
	$predmet = sqlsafe($_REQUEST['predmet']);
	$komu = sqlsafe($_REQUEST['komu']);

	$res = $db->query("SELECT id FROM users_sys WHERE login='$komu'");
	
	$text = "<div class=\"posta_dorucena\">\n";
	//uzivatel je platny
	if($row = $db->GetFetchRow( $res )){
		$komu_id = $row[0];
	}
	else{
		//uzivatel je neplatny
		$text .= "<div>Takovýto uživatel neexistuje.</div>\n";
		$text .= "</div>\n";
		return new textElement($text);
	}
	
	$query = "INSERT INTO posta (id_autor, id_prijemce,obsah, predmet, cas, precteno)  VALUES
		('$id_hrac', '$komu_id', \"$obsah\",\"$predmet\", now(),0)";
	$res = $db->Query( $query );


	$text .= "<div>Vaše zpráva byla odeslána.</div>\n";
	$text .= "<div><a href=\"posta.php?section=$POSTA_DORUCENA\">Návrat zpět na přehled.</a></div>\n";
	$text .= "</div>\n";
    	return new textElement($text);

}
