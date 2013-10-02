<?php

	if(!isset($_GET['id_hrac'])){
        $error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                        'ID_NOT_SET',
                        "Sekci FR_AM_info_hrac nebylo predano zadne ID hrace",null);	
	}
	$id_hrac = sqlsafe($_GET['id_hrac']);
	
	$query = "SELECT login, account_created, last_activity, strana, odehranych_dnu 
			FROM users_sys AS us JOIN users_hrac AS uh JOIN in_game_hrac AS igh 
			ON uh.id_user=us.id AND igh.id_hrac=us.id
			WHERE us.id='$id_hrac'";
	$res = $db->Query( $query );
	if (!$hrac = $db->GetFetchAssoc( $res )){
		 $error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                        'ID_NOT_SET',
                        "Hrac nenalezen v DB",null);
	}
	
	$bg = $DIR_SKINS . "default/frame_akcni_menu/info_hrac/pozadi_nahore.jpg";
	$text = '<div style="background: url('.$bg.') 0px 0px no-repeat; width: 277px; height:253px;">';
	$text .= "\n";
	
	
	$text .= "<div id=\"info_hrac_okraj\">\n";
	$text .= '<img src="./skins/default/frame_akcni_menu/info_hrac/h_okraj.png" />';
 	$text .= "</div>\n";
 	
 	$text .= '<img src="./skins/default/frame_akcni_menu/info_hrac/frontove_zkusenosti.png" id="ih_frontove_zkusenosti"/>';
 	
 	$text .= "<div id=\"ih_napis_postu\">\n";
 	//test zda zobrazit ODESLAT POSTU
	if($users_class->is_logged()){
		$text .= "<a href=\"posta.php?section=2&amp;hrac=${hrac['login']}\" 
	 	target=\"_blank\" title=\"Napsat zprávu\" >";
		$text .= '<img src="./skins/default/frame_akcni_menu/info_hrac/napis_postu.png" border=\"0\" />';
	 	$text .= '</a>';
	}
	$text .= "</div>\n";
 	
 	$text .= "<div id=\"ih_text1\">\n";
 	$text .= "<a href=\"profil.php?id_hrac=$id_hrac\" target=\"_blank\">\n";
	$text .= $hrac['login'];
	$text .= "</a>\n";
 	$text .= "</div>\n";
 	
 	$text .= "<div id=\"ih_text2\">\n";
	$text .= 'Strana: ';
	if ($hrac['strana']=='us'){
		 	$text .= "Spojenci";
		 }
		 else{
		 	$text .= "Rudá armáda";
		 }
 	$text .= "</div>\n";
 	
 	$text .= "<div id=\"ih_text3\">\n";
 	//prevod data
 	$dat = explode(' ', timestamp_iso_cz($hrac['last_activity']));
 	list( $hod, $min, $sec) = explode(':', $dat[1]);
	$text .= 'Naposledy on-line: ' . $dat[0] . " " . $hod.  ":" . $min;
 	$text .= "</div>\n";

 	$text .= "<div id=\"ih_text4\">\n";
	$text .= 'Celkem odehráno dnů: ' . $hrac['odehranych_dnu'];
 	$text .= "</div>\n";
 	
 	$text .= "<div id=\"ih_text5\">\n";
 	$dat = explode(' ', timestamp_iso_cz($hrac['account_created']));
	$text .= 'Profil založen dne: '. $dat[0];
 	$text .= "</div>\n";
 	
 	$text .= "<div id=\"ih_velky_symbol\">\n";
	$symbol = "default_fotka.png";

	$text .= '<a href="profil.php?id_hrac='.$id_hrac.'" target="_blank">
	<img src="./skins/default/frame_akcni_menu/info_hrac/'.$symbol.'" border="0" />
	</a>';
 	$text .= "</div>\n";
 	
 	$text .= "</div>\n";
 	
	$text .= "\n";

	// seznam vyher
	$text .= "<div id=\"vyhry_nahore\">\n";
	$text .= "</div>\n";
	
	$text .= "<div id=\"vyhry\">\n";
	
	$query = " SELECT l.nazev, l.typ, uv.team_vyhra, uv.umisteni FROM ligy AS l JOIN users_vyhry AS uv 
			ON l.id=uv.id_ligy
			WHERE
			uv.id_user='$id_hrac' AND (uv.umisteni=1 OR uv.team_vyhra='ano')";
	
	$res = $db->Query( $query );
	while ($row = $db->GetFetchAssoc( $res )){
		if($row['umisteni']==1){
			$text .= "Výhra ve scénáři: " . $row['nazev']."<br />";
		}
		else if($row['team_vyhra']=='ano'){
			$text .= "Člen vítězné strany ve : " . $row['nazev']."<br />";
		}
	}
	//$text .= "<br />";	

	$text .= "</div>\n";
	
	
	$text .= "<div id=\"vyhry_dole\">\n";
	$text .= "</div>\n";
	
	$form = new textElement($text, null);
    $page->add_element($form, 'obsah');	

?>