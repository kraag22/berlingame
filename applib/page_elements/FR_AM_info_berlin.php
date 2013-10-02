<?php
	//tooltips 
	require_once($DIR_LIB . "page_elements/FR_AM_tooltips.php");
	
	require_once($DIR_CONFIG . "lib.php");
	
	require_once($DIR_CONFIG . "boj.php");
	
	if(!isset($_SESSION['id_ligy'])){
        $error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                        'ID_NOT_SET',
                        "Sekci FR_AM_info_berlin nebylo predano zadne ID_ligy ze session",null);	
	}
	
	$id_ligy = $_SESSION['id_ligy'];
	
	$dir_berlin = $DIR_SKINS . "default/frame_akcni_menu/berlin/";
	
	$bg = $dir_berlin . "podklad.jpg";
  
	$text = '<div style="background: url('.$bg.') 0px 0px no-repeat; width: 277px; height: 723px;">';
	$text .= "\n";
	$text .= '<img src="'.$dir_berlin.'popisek.png" id="ber_popisek" />';
	$text .= '<img src="'.$dir_berlin.'znak.png" id="ber_znak" />';
	$text .= '<img src="'.$dir_berlin.'berlin.png" id="ber_berlin" />';
	$text .= '<img src="'.$dir_berlin.'text2.png" id="ber_text2" />';
	$text .= '<img src="'.$dir_berlin.'procenta.png"id="ber_procenta" onmouseover="stm(Text[1],Style[1])" onmouseout="htm()" />';
	
	//napoveda
  	$text .= '<a href="frame_mapa.php?section=napoveda&amp;page=berlin" target="mapa" title="Jak zaútočím na Berlín?" >
	<img src="'.$DIR_SKINS .'default/frame_akcni_menu/napoveda.png" alt="Jak zaútočím na Berlín?" style="border:0px;position: absolute; top: 185px; left: 130px;" /></a>';
  
	
	$text .= '<div id="ber_procenta_text" onmouseover="stm(Text[1],Style[1])" onmouseout="htm()">
				'.VratProcenaLeteckeSily( $id_ligy ).'%</div>';
	
	$text .= '<img src="'.$dir_berlin.'text1.png" alt="obr" id="ber_text1" />';
	
	// vybrani podminek pro scenar
	$query = "SELECT * FROM ligy WHERE id='$id_ligy'";
	$res = $db->Query( $query );
	$typ_l = $db->GetFetchAssoc( $res );
	if($typ_l['typ']=="trening"){
		$p = VratPodminkyTrenink( $id_ligy );
	}
	else if($typ_l['typ']=="deathmatch"||$typ_l['typ']=="elite_dm"){
		$p = VratPodminkyDM( $id_ligy );
	}
	else if($typ_l['typ']=="team"){
		$p = VratPodminkyTeam( $id_ligy );
	}
	else{
		$p['pocet_podminek'] = 0;
	}
		
	//TEST ZDA HRAC VYHRAL
	$vyhra = "ne";
	for ($i = 1; $i<= $p['pocet_podminek']; $i++){
		if($p["${i}o"] == "ano"){
			$vyhra = "ano";
		}
		else{
			$vyhra = "ne";
			break;
		}
	}
	
	//VYHRA
	if( $vyhra=="ano" ){
		$text .= '
		<a href="frame_akcni_menu.php?section=info&id=45&action=vyhra" title="Vyhrát hru">
		<img src="'.$dir_berlin.'vyhra_ano.png"  id="ber_vyhra" border="0" />
		</a>
		';
		//zpracovani vyhry
		
		if (isset($_REQUEST['action'])){
			if ($_REQUEST['action']=="vyhra"){
				PoslatUtokNaBerlin( $id_ligy, null, $typ_l['typ']);
			}
		}
	}
	else{
		$text .= '<img src="'.$dir_berlin.'vyhra.png"  id="ber_vyhra" />';
	}	
	
	// generovani podminek
	for ($i = 1; $i<= $p['pocet_podminek']; $i++){
		$text .= '<div id="ber_podminka'.$i.'" onmouseover="stm(Text['.($i+5).'],Style[1])" onmouseout="htm()" >';
		$text .= '<img src="'.$dir_berlin.$p["${i}o"] .'.png"  class="ber_zaskrtavatko" />';
			$text .= '<div class="ber_podminka_text" >' .$p["${i}t"];		
			$text .= '</div>';
		$text .= '</div>';
	}	
	

	
	$text .= '<img src="'.$dir_berlin.'cara2.png"  id="ber_cara" />';
	
	
	
	$text .= "\n";
	
	//test zda hrac odeslal utok na berlin
	if( $users_class->is_logged() ){
		$query = "SELECT count(*) FROM in_game_utoky WHERE 
		id_zeme_kam=45 AND id_vlastnik='".$users_class->user_id()."'";
		$res = $db->Query( $query );
		$row = $db->GetFetchRow( $res );
		if($row[0]>0){
			$text .= '<div id="ber_utok_poslan">Útok na Berlín byl odeslán.';
			$text .= '</div>';
		}
	}
	$text .= "</div>\n";
	
    $form = new textElement($text, null);
    $page->add_element($form, 'obsah');	
    
    $form = new textElement(TooltipBerlin( $id_ligy, $p ), null);
    $page->add_element($form, 'tooltip');
?>