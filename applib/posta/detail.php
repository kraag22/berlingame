<?php if (!defined('BoB')) exit();

/**
 * @file detial.php
 * @brief Pošta uživatele - dorucene zprávy
 *
 */
require_once("${DIR_CONFIG}formulare.php");



function posta_detail($db, $id_zpravy, $section = NULL) {
	global $users_class, $PAGE_SIZE, $db, $POSTA_DORUCENA, $POSTA_NAPSAT;
	
	$id_zpravy = sqlsafe( $id_zpravy );
	$id_hrac = $users_class->user_id(); 

	$page = (int) request("page", 0);

	$text = "<div class=\"posta_dorucena\">\n";
	
	$query = "
		SELECT p.*, u.login
		FROM posta p LEFT JOIN users_sys u ON  p.id_autor = u.id  
		WHERE p.id = '$id_zpravy'";

	$res = $db->Query( $query );

	if ($row = $db->GetFetchAssoc( $res )){
		//kontrola prav ke cteni zpravy
		if( !($row['id_autor'] == $id_hrac || $row['id_prijemce'] == $id_hrac) ){
			return new textElement('Nemáte práva ke čtení této zprávy.');
		}
		
		$al_nazev = "(žádná aliance)";
		$id_al = JeUzivatelVAlianci($row['id_autor']);
		if ($id_al > 0) {
			$al = VratAlianci($id_al);
			if ($al)
				$al_nazev =  $al['nazev'];
		}//if
		
		// pokud je hrac i prijemce zpravy, nastavi se zprava na prectenou
		if($id_hrac == $row['id_prijemce']){
			$query = "UPDATE posta SET precteno = 1 where id='$id_zpravy'";
			$db->DbQuery($query);
		}
	
		$text .= "<div class=\"post_detail\">\n";
		$img = get_icon_info($db, $row['id_prijemce'], "default"); //TODO: zadat default ikonu
		$text .= "<div class=\"post_icon\"><img src=\"${img['url']}\" ${img['attr']} alt=\"${row['login']}\" /></div>\n";
		$text .= "<div class=\"post_header\">\n";
		$text .= "  <span class=\"post_sender\">${row['login']}</span>\n";
		//$text .= "  <span class=\"post_aliance\">${al_nazev}</span>\n";
		$text .= "  <span class=\"post_time\">".timestamp_iso_cz($row['cas'])."</span>\n";
		//tlacitko odpovedet
		if ( $section == $POSTA_DORUCENA){
			$text .= "  <span class=\"post_respond\">
				<a href=\"posta.php?section=$POSTA_NAPSAT&amp;hrac=${row['login']}&amp;re=".urlencode($row['predmet'])."\">
				Odpovědět</a></span>\n";	
		}
		
		$text .= "</div>\n";
		$text .= "Předmět: <span class=\"post_predmet\">${row['predmet']}</span>\n";
		$obsah = RozkodujPrizpevek( $row['obsah'] );
		$text .= "<div class=\"post_body\">$obsah\n</div>\n";
		
		$text .= "</div>\n";

	}//if

	
	$text .= "</div>\n";
	
    return new textElement($text);

}

?>