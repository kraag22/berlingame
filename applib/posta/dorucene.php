<?php if (!defined('BoB')) exit();

/**
 * @file dorucene.php
 * @brief Pošta uživatele - dorucene zprávy
 *
 */
require_once("${DIR_CONFIG}formulare.php");

function posta_dorucene($db) {

	global $users_class, $PAGE_SIZE, $POSTA_DORUCENA;

	$id_hrac = $users_class->user_id(); 

	$page = (int) request("page", 0);

	$text = "<div class=\"posta_dorucena\">\n";
	
	$query = "
		SELECT p.*, u.login
		FROM posta p LEFT JOIN users_sys u ON  p.id_autor = u.id  
		WHERE p.id_prijemce = '$id_hrac'	
		ORDER BY p.cas DESC
		LIMIT " . ($page*$PAGE_SIZE) . "," . ($PAGE_SIZE+1);

	$res = $db->Query( $query );

	$count = 0;
	
	while ($row = $db->GetFetchAssoc( $res )){

		if ((++$count) == ($PAGE_SIZE+1)) { // poslední prvek slouží jen k indikaci zda existují další
			break;
		}
		
		$al_nazev = "(žádná aliance)";
		$id_al = JeUzivatelVAlianci($row['id_autor']);
		if ($id_al > 0) {
			$al = VratAlianci($id_al);
			if ($al)
				$al_nazev =  $al['nazev'];
		}//if
		
		
		$text .= "<div class=\"post\">\n";
		//$img = get_icon_info($db, $row['id_prijemce'], "default"); //TODO: zadat default ikonu
		//$text .= "<div class=\"post_icon\"><img src=\"${img['url']}\" ${img['attr']} alt=\"${row['login']}\" /></div>\n";
		$text .= "<div class=\"post_header\">\n";
		$text .= "<a href=\"posta.php?section=$POSTA_DORUCENA&amp;detail=${row['id']}\"";
		if($row['precteno']==0){
			$text .= 'class="neprecteno"';
		}
		$text .= ">";
		$text .= "  <span class=\"post_sender\">${row['login']}</span>\n";
		//$text .= "  <span class=\"post_aliance\">${al_nazev}</span>\n";
		$text .= "  <span class=\"post_time\">".timestamp_iso_cz($row['cas'])."</span>\n";
		$text .= "  <span class=\"post_predmet\">${row['predmet']}</span>\n";
		$text .= "</a>";
		$text .= "</div>\n";
		//$text .= "<div class=\"post_body\">${row['obsah']}\n</div>\n";
		
		$text .= "</div>\n";

	}//while

	$text .= "<hr />";
	//footer (page navigation)
	$text .= "<div class=\"navigation\">\n";
	if ($page > 0) {
		$text .= "<a href=\"posta.php?section=$POSTA_DORUCENA&amp;page=" . ($page - 1) . "\">novější</a>\n";
	}
	else  {
		$text .= "<span class=\"disabled\">novější</span>\n";
	}

	$text .= " (" . ($page+1) . ") \n";

	if ($count == ($PAGE_SIZE+1)) {
		$text .= "<a href=\"posta.php?section=$POSTA_DORUCENA&amp;page=" . ($page + 1) . "\">starší</a>\n";
	} 
	else {
		$text .= "<span class=\"disabled\">starší</span>\n";
	}

	$text .= "</div>\n";

	
	$text .= "</div>\n";
	
    return new textElement($text);

}

?>