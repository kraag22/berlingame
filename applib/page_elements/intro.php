<?php if (!defined('BoB')) exit();

	//zda se maji zobrazit scenare
	$zobrazit = true;
	//liga, kterou ma hrac rozehranou
	$rozehrana_liga = 0;
	$text = "<div class=\"horni_text\">";
	// HORNI ODKAZ
	if($users_class->is_logged()){
		
		$res = $db->Query("SELECT igh.id_liga, us.login FROM 
				`in_game_hrac` AS igh JOIN users_sys AS us 
				ON igh.id_hrac=us.id where igh.id_hrac='{$users_class->user_id()}'");
		// hrajici hrac
		if ($hrac = $db->GetFetchAssoc( $res )){		
			
			$text .= "${hrac['login']} : Máte rozehraný scénář ";
			
			$res2 = $db->Query("SELECT * FROM `ligy` where id='${hrac['id_liga']}'");
       		$liga = $db->GetFetchAssoc( $res2 );
       	
       		$text .= $liga['nazev'] . ".<br />";
       		
       		$text .= "<a href=\"hlaseni.php?id_scenare=${hrac['id_liga']}\">Vstoupit do hry</a>";
       		$rozehrana_liga = $hrac['id_liga'];
			}
		// nehrajici prihlaseny hrac
		else{
			
			$res3 = $db->Query("SELECT login FROM users_sys WHERE 
							id='{$users_class->user_id()}'");
			// hrajici hrac
			$hrac = $db->GetFetchAssoc( $res3 );
			
			$text .= "${hrac['login']} : Nemáte rozehraný žádný scénář.<br />";
			$text .= "<a href=\"${DIRECTORY}intro.php?section=prihlaseni\">Přihlašte se do scénáře</a>";
			$zobrazit = false;
		}
	}
	//neprihlaseny uzivatel
	else{
		$text .= 'Chcete-li začít hrát, ';
		$text .= "<a href=\"registrace.php\">registrujte se.</a> <br />";
		$text .= '<span>Pokud se chcete jen podívat, vyberte si některý z následující
		 scénářů.</span>';
	}
	
	$text .= '</div>'; 

	//MENU
	if($users_class->is_logged()){
		$text .= "<div class=\"menu\">";
	    $text .= "<a href=\"".$DIRECTORY."intro.php?section=nastaveni\">osobní nastavení</a> |";
	    $text .= "<a href=\"".$DIRECTORY."posta.php\">pošta</a> |";
	    $text .= "<a href=\"".$DIRECTORY."forum.php\">fóra</a> |";
	    if ($auth->authorised_to('admin_novinky')) {
	    	$text .= "<a href=\"".$DIRECTORY."admin.php\">administrace</a> |";
	    }
	    $text .= "<a href=\"".$DIRECTORY."index.php?section=logout\">odhlášení</a> ";
	    $text .= '</div>'; 
	}
	
	//SCENARE
	if($zobrazit){
		$text .= "<div class=\"scenare\">";
	    $text .= "<table>"; 
	       
	    $res = $db->Query("SELECT * FROM `ligy` where stav='active' AND typ<>'trening'");
	    while( $liga = $db->GetFetchAssoc( $res )){
			$text .= "<tr><td><img src=\"".$DIRECTORY."skins/default/intro/".$liga['typ'].".png\" width=\"170\" height=\"135\" alt=\"obrazek\"/></td>";
	    	$text .= "<td id=\"druhy\">";
	    		if( $rozehrana_liga == $liga['id'] ){
	    			$text .= "<a href=\"hlaseni.php?id_scenare=${liga['id']}\">";
	    		}
	    		else{
	    			$text .= '<a href="info.php?id_scenare='.$liga['id'].'">';
	    		}
	    		$text .= "<b>${liga['nazev']}</a> (";
	    			
	    	switch( $liga['typ'] ){
	    		case "elite_dm":
	    			$text .= "elitní deathmatch";
	    		break;
	    		case "deathmatch":
	    			$text .= "deathmatch";
	    		break;
	    		case "team":
	    			$text .= "týmová hra";
	    		break;
	    		default:
	    			$text .= $liga['typ'];
	    	}		
	    		
	    	$text .= "</b>";
	    	
	    	//odkaz na info o typu scenare
	    	$text .= '<a href="info.php?id_scenare='.$liga['id'].'"><img src="'.$DIRECTORY.
	    		'/skins/default/frame_akcni_menu/napoveda.png" width="13" height="12" border="0" title="Informace o scénáři" /></a><b>)</b>';
	    	
	    	$text .= "<br /> 
	    		<span style=\"font-size: 12pt;\">
	    		Hraje se ${liga['odehranych_dnu']} dnů.<br /> ";
	    	// vypis stavu scenare
	    	if($liga['dohrano']=='ano'){
	    		$text .= "<strong>Scénář se dnes dohrál.</strong>";
	    	}
	    	//nedohrany scenar
	    	else{
	    		//lze se do nej prihlasit
		    	if ($liga['registrace']=='ano'){
		    		$text .= "Scénář začíná, lze se přihlásit do hry (denně do 22:00).<br />";
		    		
		    		$query = "SELECT count(*) FROM in_game_hrac WHERE id_liga='${liga['id']}'";
		    		$res2 = $db->Query( $query );
		    		$ah = $db->GetFetchRow( $res2 );
		    		
		    		$text .= "Hraje ".$ah[0]." z ".$liga['max_pocet_hracu']."ti možných hráčů.";
		    	}	
		    	// rozehrany - nelze se prihlasit
		    	else{
		    		$text .= "Scénář je rozehraný, nelze se přihlásit do hry.";
		    	}	
	    	}
	    	
	    	$text .= "</span>
	    		</td></tr>";
	    } 
		$text .= '</table>';
		
		//treningove ligy
		$text .= "Tréninkové scénáře:";
		$text .= "<table>";
		$res = $db->Query("SELECT * FROM `ligy` where stav='active' AND typ='trening'");
	    while( $liga = $db->GetFetchAssoc( $res )){
	    	$text .= "<tr><td>";
	    	
	    		if( $rozehrana_liga == $liga['id'] ){
	    			$text .= "<a href=\"hlaseni.php?id_scenare=${liga['id']}\">";
	    		}
	    		else{
	    			$text .= '<a href="info.php?id_scenare='.$liga['id'].'">';
	    		}
	    		$text .= "<b>${liga['nazev']} (trénink)</b>
	    		</a> </td><td>
	    		<span style=\"font-size: 12pt;\">
	    		Hraje se ${liga['odehranych_dnu']} dnů. ";
	    	// vypis stavu scenare
	    	if($liga['dohrano']=='ano'){
	    		$text .= "<strong>Scénář se dnes dohrál.</strong>";
	    	}
	    	else{
	    		//lze se do nej prihlasit
		    	if ($liga['registrace']=='ano'){
			    		
		    		$query = "SELECT count(*) FROM in_game_hrac WHERE id_liga='${liga['id']}'";
		    		$res2 = $db->Query( $query );
		    		$ah = $db->GetFetchRow( $res2 );
		    		
		    		$text .= "Hraje ".$ah[0]." z ".$liga['max_pocet_hracu']."ti možných hráčů.";
		    	}	
		    	// rozehrany - nelze se prihlasit
		    	else{
		    		$text .= "Scénář je rozehraný.";
		    	}	
	    	}
	    	$text .= "</td></tr>";
		}
		
		$text .= '</table>';
		
	$text .= '</div>'; 
	}
	else{
		$text .= '<div style="margin:20px;">
		Přihlašte se do scénáře kliknutím na nadpis.
		</div>'; 
	}    
	
	//JS pro google adwords konverzi a sklik
	if(isset($_GET['registrace'])){
		$text .= $GOOGLE_ADWORDS;
		$text .= $SKLIK_KONVERZE;
	}
	
	$form = new textElement($text);
	$page->add_element($form, 'obsah');
?>