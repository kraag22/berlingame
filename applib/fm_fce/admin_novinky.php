<?php
/**
 * @brief Funkce, která se vykoná po uložení dat do databáze.
 * 
 * Funkce uloží id_autora do DB. Ostatní data jsou automaticky
 * uložena Form managerem.
 *
 * @param $post odkaz na kopii postu
 * @param $last_id  id vloženého řádku
 * 
 * @return false v případě, že nedojde k chybě
 * @return popis chyby, pokud k nějaké dojde
 */
function novinky_after_db(&$post, $last_id) {
	global $db , $users_class, $LANGUAGE;

	$zmena_db = "";
	
 	$zmena_db[] = "`datum`=NOW()";
 	$zmena_db[] = "`autor`='" . $users_class->user_id() . "'";
 	
 	// nastavení správných cest do db, nastavení data přidání a případně pořadí
	if (is_array($zmena_db)) {
		$zmena_db = implode(",", $zmena_db);
		$db->Query("UPDATE `novinky` 
				  	SET $zmena_db
				  	WHERE id='" . $last_id . "'");
	}
	// vše v pořádku
	return false;
}

?>