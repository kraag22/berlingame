<?php

	//soubor s funkcemi staveb
	require_once($DIR_CONFIG . "stavby.php");
	//menu hracovy zeme
	require_once($DIR_LIB . "page_elements/FR_AM_menu_hracova_zem.php");
	//tooltips vystavba
	require_once($DIR_LIB . "page_elements/FR_AM_tooltips.php");
	

	if (isset($_REQUEST['action'])){
		switch($_REQUEST['action']){
			case 'postav':
				PostavStavbu($_REQUEST['id_zeme'], $_REQUEST['id_stavby']);
				break;
			case 'zbourej':
				ZbourejStavbu($_REQUEST['id_zeme'], $_REQUEST['id_stavby']);
				break;
			case 'add_police':
				PridejPolicejniStanici($_REQUEST['id_zeme'], sqlsafe($_SESSION['id_ligy']));
				break;
			case 'add_pvo':
				PridejPVOStanici($_REQUEST['id_zeme'], sqlsafe($_SESSION['id_ligy']));
				break;
		}
		//prekresleni menu po zmene
	    $form = new textElement("OnLoad=\"top.menu.document.location.href='frame_menu.php';\"", null);
	    $page->add_element($form, 'refresh');
	}
	
	$menu_footer = $DIR_SKINS . "default/frame_akcni_menu/vystavba_menu_foot.jpg";
  $bg = $DIR_SKINS . "default/frame_akcni_menu/vystavba/vystavba_bg.jpg";

  $text = '<div style="background: url('.$bg.') 0px 4px no-repeat; width: 277px; height: 790px;">';
  $text .= "\n";
	$text .= '<img src="'.$menu_footer.'" alt="maska menu" style="position: absolute; top: 24px; left: 0px;" /><br />';
	
	$text .= "\n";
	
	//napoveda
  	$text .= '<a href="frame_mapa.php?section=napoveda&amp;page=vystavba" target="mapa" title="Jak na výstavbu ?" >
	<img src="'.$DIR_SKINS .'default/frame_akcni_menu/napoveda.png" alt="Jak na výstavbu ?" style="border:0px;position: absolute; top: 36px; left: 255px;" /></a>';
  
	
	//zjisteni z jake je hrac strany
	$query = "SELECT strana FROM in_game_hrac WHERE id_hrac='".$users_class->user_id()."'";
	$ress = $db->Query( $query );
	$strana = $db->GetFetchRow( $ress ); 
	
	// menu
	$text .= '<img src="./skins/default/frame_akcni_menu/vystavba/text_vystavba.png" id="vystavba_nadpis"  />';
	
	$text .= "<div class=\"vystavba_mrizka\">\n";
	$text .= "<div class=\"vystavba_inside\">\n";

//	for ($i = 1; $i<=15; $i++){
		$i = 3; $j = 4; $posun_i = 134; $posun_j = 33;
		$text .= RadekStavba($id_zeme,4, $i , $j);
		$text .= RadekStavba($id_zeme,17, $i+$posun_i , $j);
		$j += $posun_j;
		$text .= RadekStavba($id_zeme,5, $i , $j);
			if ($strana[0]=='us'){
				$text .= RadekStavba($id_zeme,22, $i+$posun_i , $j);
			}
			else{
				$text .= RadekStavba($id_zeme,16, $i+$posun_i , $j);
			}
		
		$j += $posun_j;
		$text .= RadekStavba($id_zeme,6, $i , $j);
		$text .= RadekStavba($id_zeme,18, $i+$posun_i , $j);
		$j += $posun_j;
		$text .= RadekStavba($id_zeme,7, $i , $j);
		$text .= RadekStavba($id_zeme,21, $i+$posun_i , $j);
		$j += $posun_j;
		$text .= RadekStavba($id_zeme,8, $i , $j);
		$text .= RadekStavba($id_zeme,20, $i+$posun_i , $j);
		$j += $posun_j;
		$text .= RadekStavba($id_zeme,9, $i , $j);
		$text .= RadekStavba($id_zeme,24, $i+$posun_i , $j);
		$j += $posun_j;
		$j++;
		$text .= RadekStavba($id_zeme,10, $i , $j);
		$j += $posun_j;
		$text .= RadekStavba($id_zeme,19, $i , $j);
		$j += $posun_j;
		$j++;
		$text .= RadekStavba($id_zeme,13, $i , $j);
		$j += $posun_j;
		$text .= RadekStavba($id_zeme,11, $i , $j);
		$j += $posun_j;
		$j++;
		if ($strana[0]=='us'){
				$text .= RadekStavba($id_zeme,12, $i , $j);
			}
			else{
				$text .= RadekStavba($id_zeme,14, $i , $j);
			}
		$j += $posun_j;
		$text .= RadekStavba($id_zeme,2, $i , $j);
		$j += $posun_j;
		$text .= RadekStavba($id_zeme,3, $i , $j);
		$j += $posun_j;
		$text .= RadekStavba($id_zeme,1, $i , $j);
		$j += $posun_j;
		$text .= RadekStavba($id_zeme,15, $i , $j);
		$j += $posun_j;
		$text .= RadekStavba($id_zeme,23, $i , $j);
		
		$text .= "";
//		if ($i < 8){
//			$text .= RadekStavba($id_zeme,$i+15, 10,30);
//		}

//	}
	
	$text .= "</div> \n";
	$text .= "</div>\n";	
	
	// VOJENSKA POLICIE
	
	$text .= '<img src="./skins/default/frame_akcni_menu/vystavba/text_mp.png" id="vystavba_nadpis2"  />';
	
	
	$query = "SELECT vojenska_policie FROM `in_game_zeme`
	 where (id_zeme='$id_zeme')and
	 (id_ligy='".sqlsafe($_SESSION['id_ligy'])."')
	 ";
	
	$res = $db->Query( $query );
	$policie = $db->GetFetchRow($res);
	
	//test zda ma hrac stavbu vojenska policie
	if( MaHracStavbuVZemi(20, $id_zeme, $_SESSION['id_ligy']) ){	
		$text .= "<div class=\"vystavba_mp\" onmouseover=\"stm(Text[100],Style[1])\" onmouseout=\"htm()\">\n";
		$text .= "<div id=\"vystavba_obal_one_mp\" >\n";
		for ($i = 1; $i <= $policie[0];$i++){
			$text .= '<img src="./skins/default/frame_akcni_menu/vystavba/one_mp.png" id="vystavba_one_mp" width="21" height="22"  />';
		}
		$text .= "</div>\n";	
		$text .= "</div>\n";	
		
		$text .= "<a class=\"vystavba_mp_postavit\" href=\"frame_akcni_menu.php?section=vystavba&amp;action=add_police&amp;id_zeme=$id_zeme\">
				<span style=\"display: none;\"> postavit MP</span>
				</a>";
	}
	else{
		$text .= "<div class=\"vystavba_chyba\">\n";
		$text .= "Nemáte postavenou stavbu vojenská policie";
		$text .= "</div>\n";
	}		
			
	
	// PVO - FLAK
	
	$text .= '<img src="./skins/default/frame_akcni_menu/vystavba/text_pvo.png" id="vystavba_nadpis3"  />';
	
	
	$query = "SELECT pvo_stanice FROM `in_game_zeme`
	 where (id_zeme='$id_zeme')and
	 (id_ligy='".sqlsafe($_SESSION['id_ligy'])."')
	 ";
	
	$res = $db->Query( $query );
	$pvo_res = $db->GetFetchRow($res);
	
	//test zda ma hrac stavbu flak
	if( MaHracStavbuVZemi(18, $id_zeme, $_SESSION['id_ligy']) ){	
		$text .= "<div class=\"vystavba_pvo\" onmouseover=\"stm(Text[101],Style[1])\" onmouseout=\"htm()\">\n";
		$text .= "<div id=\"vystavba_obal_one_pvo\" >\n";
		for ($i = 1; $i <= $pvo_res[0];$i++){
			$text .= '<img src="./skins/default/frame_akcni_menu/vystavba/one_pvo.png" id="vystavba_one_pvo" width="21" height="22"  />';
		}
		$text .= "</div>\n";	
		$text .= "</div>\n";	
		
		$text .= "<a href=\"frame_akcni_menu.php?section=vystavba&amp;action=add_pvo&amp;id_zeme=$id_zeme\" class=\"vystavba_pvo_postavit\">
				<span style=\"display: none;\"> postavit MP</span>
				</a>";
	}
	else{
		$text .= "<div class=\"vystavba_chyba2\">\n";
		$text .= "Nemáte postavenou stavbu flak";
		$text .= "</div>\n";
	}		
	
	$text .= "</div>\n";
	
    $form = new textElement($text, null);
    $page->add_element($form, 'obsah');	

    $form = new textElement(TooltipVystavba($id_zeme, $_SESSION['id_ligy']), null);
    $page->add_element($form, 'tooltip');	
?>
