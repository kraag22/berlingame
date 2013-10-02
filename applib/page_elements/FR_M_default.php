<?php
// vytvareni elementu
	require_once($DIR_CONFIG . "formulare.php");
	
	//skript na deaktivaci posty
	$pole = Array();
 	$pole['fileName'] = $DIR_SCRIPTS . 'posta_v_menu.js';
	$page->add_script($pole);
	
	
	$id_ligy = sqlsafe($_SESSION['id_ligy']);
	if (!is_numeric($id_ligy)){
		$error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                        'NO_ACCES_GRANTED',
                        "Neni zadana liga",null);
	}
	
	// test zda je uzivatel prihlasen
	$hrac_ligy = false;
	if ($users_class->is_logged()){
		if ($lig = JeUzivatelVLize()){
			if ($id_ligy == $lig)
			{
				$hrac_ligy = true;
			}
		}
	}
	
	//POCASI
	$query = "SELECT p.nazev, p.id, p.image FROM pocasi as p JOIN ligy as l ON p.id=l.id_pocasi_dnes 
				WHERE l.id='$id_ligy';";
	$res = $db->Query( $query );
	$pcs = $db->GetFetchAssoc( $res );
	$pocasi = '<a href="frame_mapa.php?section=napoveda&page=pocasi#'.$pcs['id'].'" target="mapa">
		<img src="skins/default/pocasi/'.$pcs['image'].'"';
	$pocasi .= ' alt="'.$pcs['nazev'].'" title="'.$pcs['nazev'].'" width="34" height="20" border="0" /></a>';
    $form = new textElement($pocasi);
    $page->add_element($form, 'pocasi');
	
    if ($hrac_ligy){
	    //SUROVINY
	    $query = "SELECT suroviny, palivo, body_vlivu, akt_pocet_kol, id_general, akt_max_pocet_kol
	    			 FROM in_game_hrac 
					WHERE id_hrac='".$users_class->user_id()."'";
		$res = $db->Query( $query ); 
		$row = $db->GetFetchAssoc( $res );
		
	    $form = new textElement($row['suroviny'], null);
	    $page->add_element($form, 'suroviny');
	    $palivo = $row['palivo'];
	    $body_vlivu = $row['body_vlivu'];
	    $akt_pocet_kol = $row['akt_pocet_kol'];
	    $akt_max_pocet_kol = $row['akt_max_pocet_kol'];
	    $id_general = $row['id_general'];
	  
	    //PALIVO
	    $form = new textElement($palivo);
	    $page->add_element($form, 'palivo');
	    
	    //BODY_VLIVU
	    $form = new textElement($body_vlivu);
	    $page->add_element($form, 'body_vlivu');
	
	    //POCET KOL
	    $text = "Kola " . $akt_pocet_kol . " z " . $akt_max_pocet_kol;
	    
	    //AKTUALNI POCET KOL
	    $form = new textElement($text);
	    $page->add_element($form, 'pocet_kol');
    }
    
    //BUTTONY V MENU
	if($hrac_ligy){
			$text = '<li id="odkaz1"><a href="frame_menu.php?action=dalsi_kolo"><span>další tah</span></a></li>
  			<li id="odkaz2"><a href="frame_akcni_menu.php?section=veleni" target="am"><span>vrchní velení</span></a></li>
  			<li id="odkaz3"><a href="frame_akcni_menu.php?section=letectvo" target="am"><span>velitel letectva</span></a></li>
  			<li id="odkaz4"><a href="#black_market"><span>černý trh</span></a></li>
  			<li id="odkaz5"><a href="frame_akcni_menu.php?section=statistika&page=centrala" target="am"><span>statistiky</span></a></li>
  			<li id="odkaz6"><a href="hlaseni.php" target="_blank"><span>denní hlášení</span></a></li>
  			<li id="odkaz7"><a href="forum.php" target="_blank"><span>fóra</span></a></li>
  			<li id="odkaz8"><a href="index.php?section=logout" target="_top"><span>odhlásit</span></a></li>
  			<li id="odkaz9"><a href="posta.php" target="_blank" title="pošta" onclick="deaktivace()">';
			
  			//test zda ma dorucenou postu
  			$query = "SELECT count(*) FROM posta WHERE precteno=0 AND id_prijemce='".$users_class->user_id()."'";
  			$r = $db->Query( $query );
  			$ro = $db->GetFetchRow( $r );
  			if ( $ro[0]>0 ){
				$text .= '<img src="skins/default/frame_menu/akt_posta.gif" alt="aktivni pošta" border="0" id="akt_posta" />';
  			}
  			else{
  				$text .= '<img src="skins/default/frame_menu/no_icon.gif" alt="aktivni pošta" border="0" id="akt_posta" />';
  			}
			$text .= '</a></li>';
		
	}
	else{
			$text = '<li id="odkaz1"><a href="#"><span>další tah</span></a></li>
  			<li id="odkaz2"><a href="#"><span>vrchní velení</span></a></li>
  			<li id="odkaz3"><a href="#"><span>velitel letectva</span></a></li>
  			<li id="odkaz4"><a href="#"><span>černý trh</span></a></li>
  			<li id="odkaz5"><a href="frame_akcni_menu.php?section=statistika&page=porovnani" target="am"><span>statistiky</span></a></li>
  			<li id="odkaz6"><a href="hlaseni.php" target="_blank"><span>denní hlášení</span></a></li>
  			<li id="odkaz7"><a href="forum.php" target="_blank"><span>fóra</span></a></li>
  			<li id="odkaz8"><a href="index.php?section=logout" target="_top"><span>odhlásit</span></a></li>';
		
	}

	$form = new textElement($text, null);
    $page->add_element($form, 'buttony');
?>