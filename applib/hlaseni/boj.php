<?php
	//funkce
	require_once($DIR_CONFIG . "formulare.php");
	require_once($DIR_LIB . "hlaseni/common.php");
 	
 	$pole = Array();
 	$pole['fileName'] = $DIR_SCRIPTS . 'hlaseni.js';
	$page->add_script($pole);
		
	$text = "<div class=\"hlaseni_obsah\">";
	//MENICI SE OBRAZEK NAPRAVO
	$text .= '<img src="./skins/default/hlaseni/ob_default.png" id="obrazek" alt="obr" />';
	
	//NADPISY
	$text .= '<img src="./skins/default/hlaseni/nazev_sektoru.png" id="text1" alt="obr" />';
	$text .= '<img src="./skins/default/hlaseni/udalost.png" id="text2" alt="obr" />';
	$text .= '<img src="./skins/default/hlaseni/uspesnost.png" id="text3" alt="obr" />';
	$text .= '<img src="./skins/default/hlaseni/utocnik.png" id="text4" alt="obr" />';
	$text .= '<img src="./skins/default/hlaseni/nadpis.png" id="text5" alt="obr" />';
	$text .= '<img src="./skins/default/hlaseni/ztraty.png" id="text6" alt="obr" />';
	
	// SEZNAM HLASENI
	$text .= '<div id="kratke_hlaseni" >'; 
	$id_hrac = $users_class->user_id();
	//$id_ligy = JeUzivatelVLize( $id_hrac );
	$id_ligy = $_SESSION['id_ligy'];
	$res = $db->Query("SELECT nazev_sektoru,udalost,uspesnost,utocnik,obsah,
					ztraty_pechota, ztraty_tanky, efekt FROM `in_game_hlaseni` 
			where id_ligy='$id_ligy' and skupina='boj' and id_hrac='$id_hrac'");
		   
		$text .= "<table id=\"tb\">";
	    while( $row = $db->GetFetchRow( $res )){
	    	//jaky ob_obrazek se ma zobrazit po kliknuti
	    	$ob = VratObrazekBoj( $row[1] );
	    		    	
	    	//detail hlaseni
	    	$detail = $row[4];
	    	//efekt
	    	$detail .="<br /><span class=efekt>" . $row[7] ."</span>";
	    	
	    	//ztraty
	    	$ztraty = "Pěchota:${row[5]} Tanky:${row[6]}";
	    	
	    	$text .= "<tr onclick=\"kliknuti('".$ob."','".$detail."','".$ztraty."')\">";
	    	$text .= "<td id=\"td1\">". $row[0] ."</td>";
	    	$text .= "<td id=\"td2\">". $row[1] ."</td>";
	    	$text .= "<td id=\"td3\">". $row[2] ."</td>";
	    	$text .= "<td id=\"td4\">". $row[3] ."</td>";
	    	$text .= "</tr>";
	    }
		$text .= "</table>"; 
	$text .= '</div>';
	
	
	// DETAIL JEDNOHO HLASENI
	$text .= '<div id="detail_hlaseni" >';
	 
	$text .= '</div>';
	
	// ZTRATY U DETAILU HLASENI
	$text .= '<div id="ztraty_hlaseni" >';
	 
	$text .= '</div>';
	
	//buttony
	$text .= '<a id="button_mapa" href="svet.php" > <span style="display:none;">mapa</span> </a>';
	$text .= '<a id="button_forum" href="forum.php" target="_blank" > <span style="display:none;">forum</span> </a>';
	$text .= '<a id="button_zpet" href="intro.php" > <span style="display:none;">zpet</span> </a>';        
	  
	
	$element = new textElement($text, NULL);
	$page->add_element($element, 'obsah');
?>