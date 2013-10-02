<?php if (!defined('BoB')) exit();

/**
 * @file common.php
 * @brief Společný kód pro fóra.
 *
 */

/* identifikatory vlaken. mělo by být zachováno pořadí */
$VLAKNO_HLAVNI = 1;
$VLAKNO_DOTAZY = 2;

$VLAKNA_LIGY_MIN 	= $VLAKNA_LIGY 		= 10000;
$VLAKNA_ALIANCE_MIN = $VLAKNA_ALIANCE 	= 20000;
$VLAKNA_TYMY_MIN = $VLAKNA_TYMY			= 30000;

$VLAKNA_LIGY_MAX 	= $VLAKNA_ALIANCE - 1;
$VLAKNA_ALIANCE_MAX = $VLAKNA_TYMY - 1;
$VLAKNA_TYMY_MAX	= 40000;

  
$PAGE_SIZE = 10;

require_once("${DIR_CORE}/user_utils.php");
require_once("${DIR_CONFIG}formulare.php");
require_once("${DIR_CONFIG}lib.php");


/**
 * Zjistuje, zda je prihlaseny uzivatel autor zpravy
 *
 * @param $id_zpravy
 * @return boolean
 */
function JeAutorPrizpevku( $id_zpravy ){
	global $users_class, $db;
	
	if(!is_numeric($id_zpravy)){
		return false;
	}
	
	$query = "SELECT id_autor FROM forum WHERE id = '$id_zpravy' ";
	$res = $db->Query( $query );
	$row = $db->GetFetchAssoc( $res );		
	
	return $users_class->user_id() == $row['id_autor'];
}

/**
 * Funkce vraci id vlakna pro tymove ligy
 *
 * @param $id_user
 * @param $tym - testuje zda je liga tymova. Pokud neni vrati 0
 * @return vraci false(0) pokud hracova liga neni tymova 
 * @return -1 pokud hrac neni v zadne lize
 * */
function VlaknoVTymoveLize( $id_user = NULL ){
	global  $users_class, $db;
	
	if (!isset($id_user)){
		$id_user = $users_class->user_id();
	}
	
	$query = "SELECT * FROM in_game_hrac WHERE 
			id_hrac='$id_user'";
	
	$res = $db->Query( $query );
	if (!$row = $db->GetFetchAssoc( $res )){
		return false;
	}
	
	$id_liga = $row['id_liga'];
	
	//test tymove ligy
	$query = "SELECT typ FROM ligy WHERE 
			id='$id_liga'";
	
	$r = $db->Query( $query );
	$tp = $db->GetFetchAssoc( $r );
	if( $tp['typ'] != 'team' ){
		return false;
	}
	
	//vlakna sovetu maji o 5000 vice
	if ($row['strana']=='sssr'){
		$id_liga += 5000;
	}

	return $id_liga;
}

function nazevFora($id_vlakna)
{
	global $users_class;
	global $VLAKNO_HLAVNI, $VLAKNO_DOTAZY, $VLAKNA_LIGY_MIN, $VLAKNA_ALIANCE_MIN,$VLAKNA_TYMY_MIN;
	$text = "";
	$text .= '<img src="skins/default/forum/horni_';
	
	if ($id_vlakna == $VLAKNO_HLAVNI)
		$text .= "hlavni.png";
	if ($id_vlakna == $VLAKNO_DOTAZY)
		$text .= "dotazy.png";
		
	if ($id_vlakna >= $VLAKNA_TYMY_MIN){
		$strana = VratAtributHrace('strana', $users_class->user_id);
		$text .= "tym_$strana.png";
	}
	else if ($id_vlakna >= $VLAKNA_LIGY_MIN)
		$text .= "Ligové fórum";
	else if ($id_vlakna >= $VLAKNA_ALIANCE_MIN)
		$text .= "Alianční fórum";
	
			
	$text .= '" alt="Nadpis fora" width="830" height="53" />';
	
	return $text;
}

function forumOverPrava($db, $id_vlakna)
{
	global $VLAKNA_LIGY_MIN, $VLAKNA_LIGY_MAX; 
	global $VLAKNA_ALIANCE_MIN, $VLAKNA_ALIANCE_MAX;
	global $VLAKNA_TYMY_MIN, $VLAKNA_TYMY_MAX;
	 
	if ($id_vlakna < $VLAKNA_LIGY_MIN) 
		return true;
		
	if ($id_vlakna < $VLAKNA_LIGY_MAX) {
		$id_ligy = $id_vlakna - $VLAKNA_LIGY_MIN;
		return (JeUzivatelVLize(NULL, $id_ligy) > 0);
	}//if

	if ($id_vlakna < $VLAKNA_ALIANCE_MAX) {
		$id_ligy = $id_vlakna - $VLAKNA_ALIANCE_MIN;
		return (JeUzivatelVAlianci(NULL, $id_ligy) > 0);
	}//if
	
	if ($id_vlakna < $VLAKNA_TYMY_MAX) {
		$id_ligy = $id_vlakna - $VLAKNA_TYMY_MIN;
		return ( VlaknoVTymoveLize() == $id_ligy );
	}//if
	
	return false;
}

function zobraz_odesilaci_formular($id_vlakna){
	global $users_class;
	$text = '<form method="post">
					<input type="hidden" name="newpost" value="1" />
					<input type="hidden" name="section" value="'.$id_vlakna.'" />';
				 	
	$text .= '<textarea id="text_odeslat" name="obsah" rows="4" cols="83" ';
	if (!$users_class->is_logged()){
		$text .= "disabled >Psát do fóra mohou jen přihlášení uživatelé.";
	}
	else{
		$text .= " >";
	}
	
	$text .='</textarea>';
	//button odeslat
	$text .= '<input id="odeslat" type="submit" value=" " name="odeslat"/>';
	//button obnovit
	$text .= '<a href="forum.php?section='.$id_vlakna.'"><img width="87" height="30" alt="Obnovit" 
		src="skins/default/forum/button_obnovit.png" id="obnovit"/></a>';

	$text .= '</form>';
	
	
	return new textElement($text);
}

/**
 * Zobrazi forum.
 *
 * @param $db	spojeni s db
 * @param $id_vlakna	požadované vlákno
 */
function zobraz_forum(&$framework_page, $db, $id_vlakna) 
{

	global $users_class,$PAGE_SIZE, $auth, $DIR_SKINS;

	$id_hrac = $users_class->user_id(); 
	$page = (int) request("page", 0);

	if (!forumOverPrava($db, $id_vlakna))
	{
		$text = "<div>do tohoto fóra nemáte přístup</div>\n";
		return new textElement($text);
	}
	$text = "";
	if ($users_class->is_logged() ){

		if (empty($_REQUEST['newpost'])) {
			/*$text .= <<<EOT
				<form method="post">
					<input type="hidden" name="newpost" value="1" />
					<input type="hidden" name="section" value="$id_vlakna" />
				 	<label for="obsah">Text: </label>
					<br />
					<textarea id="obsah" name="obsah" class="forumpost"></textarea>
					<input type="submit" value="Odeslat" />
				</form>
EOT;
*/		}//if newpost
		else {
			forum_odeslat($db,$id_vlakna);
			$page = 0; // zobrazit nejnovější
		}
	}//if logged

	$del_id = 0;
	//mazani zpravy
	if(isset($_REQUEST['action']) && $_REQUEST['action']=='del' ){
		$del_id = sqlsafe( $_REQUEST['del_id'] );
		if( $auth->authorised_to('forum_delete_msg')||
			JeAutorPrizpevku($del_id) ){
			
			$db->DbQuery("DELETE FROM forum WHERE id='$del_id'");
		}
		
	}
	
	$text .= "<div class=\"posts\">\n";

	$query = "
		SELECT f.*, u.login 
		FROM forum f LEFT JOIN users_sys u ON  f.id_autor = u.id  
		WHERE f.vlakno = '$id_vlakna' 
		ORDER BY f.cas DESC
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
		$img = get_icon_info($db, $row['id_autor'], "default"); //TODO: zadat default ikonu
		$text .= "<div class=\"post_icon\">
		<img src=\"${img['url']}\" ${img['attr']} alt=\"${row['login']}\" />";
		//mazani prizpevku
		if( $auth->authorised_to('forum_delete_msg')||
			JeAutorPrizpevku($row['id']) ){
			$url = "forum.php?page=$page&amp;section=$id_vlakna&amp;action=del&amp;del_id=".$row['id'];
			$text .= "<a href=\"$url\" onclick=\"return confirm('Opravdu smazat?');\">";
			$text .= "<img src=\"${DIR_SKINS}default/allpages/kos.png\" border=\"0\" />";
			$text .= "</a>";
		}
		
		$text .= "</div>\n";
		$text .= "<div class=\"post_header\">";
		$text .= "	<span class=\"post_sender\">${row['login']}</span>\n";
		//$text .= "  <span class=\"post_aliance\">${al_nazev}</span>\n";
		$text .= " - <span class=\"post_time\">".timestamp_iso_cz($row['cas'])."</span>\n";
		$text .= "</div>\n";
		$obsah = RozkodujPrizpevek( $row['obsah'] );
		$text .= "<div class=\"post_body\">".$obsah."\n</div>\n";

		$text .= "</div>\n";

	}//while

	$text .= "</div>\n";
	
	// footer (page navigation)
	$navig = "<div class=\"navigation\">\n";
	if ($page > 0) {
		$navig .= "<a href=\"forum.php?page=" . ($page - 1) . " &amp;section=$id_vlakna\">novější</a>\n";
	}
	else  {
		$navig .= "<span class=\"disabled\">novější</span>\n";
	}

	$navig .= " (" . ($page+1) . ") \n";

	if ($count == ($PAGE_SIZE+1)) {
		$navig .= "<a href=\"forum.php?page=" . ($page + 1) . "&amp;section=$id_vlakna\">starší</a>\n";
	} 
	else {
		$navig .= "<span class=\"disabled\">starší</span>\n";
	}

	$navig .= "</div>\n";
	// pridani navigace jako elemet
	$framework_page->add_element(new textElement($navig), 'navigace');
	
    return new textElement($text);

}

function forum_odeslat($db,$id_vlakna) {

	global $users_class;

	$id_hrac = $users_class->user_id(); 

	$obsah = ZakodujPrizpevek( $_REQUEST['obsah'] );	
	
	$query = "INSERT INTO forum (id_autor, vlakno, obsah, cas)  VALUES
		('$id_hrac', \"$id_vlakna\", \"$obsah\", now())";
	$res = $db->Query( $query );


	//$text = "<div>Vaše zpráva byla odeslána.</div>\n";
	//$text .= "<div><a href=\"forum.php?section=$id_vlakna\">Poslat další zprávu.</a></div><hr />\n";
    //return $text;

}
