<?php
	require_once($DIR_CONFIG . "boj.php");
	require_once($DIR_CONFIG . "lib.php");
	
function TooltipVystavba($id_zeme, $id_ligy){
	global $db, $CONST;
	
	$text = "\n";
	
	$query = "SELECT * FROM `stavby`";
	$ress = $db->Query( $query );
	
	while($naz = $db->GetFetchAssoc( $ress )){
		$text .= 'Text['.$naz['id'].']=["'.$naz['nazev'].'","Cena zásoby: <span class=\"all_suroviny\">'.$naz['cena_suroviny'].'</span>,Cena palivo: <span class=\"all_palivo\">'.$naz['cena_palivo'].'</span><br />'.$naz['popis'].'"]'."\n";	
	}
    
	//stanice PVO a MP
	$pl = CenaNavyseniPolicejniStanice( $id_zeme, $id_ligy, true );
	if ($pl['pocet']== 10){
		$zas_mp = 'Je postaven maximální počet stanic.';
	}
	else{
		$zas_mp = 'Cena další stanice: <span class=\"all_suroviny\">'.$pl['cena'].'</span> zásob';
	}
	
	$pl = CenaNavyseniPVOStanice( $id_zeme, $id_ligy, true );
	if ($pl['pocet']== 10){
		$zas_pvo = 'Je postaven maximální počet stanic.';
	}
	else{
		$zas_pvo = 'Cena další stanice: <span class=\"all_suroviny\">'.$pl['cena'].'</span> zásob';
	}
	
	$text .= 'Text[100]=["Stanice vojenské policie","'.$zas_mp.'<br /><span style=\"color:red;\">Efekt stanice: +'.$CONST["POLICEJNI_STANICE_BONUS"].'% síla kontrarozvědky</span>"]'."\n";
	$text .= 'Text[101]=["Stanice protiletadlové obrany","'.$zas_pvo.'<br /><span style=\"color:red;\">Efekt stanoviště: +'.$CONST["PVO_STANICE_BONUS"].' protivzdušná obrana</span>"]'."\n";
		
    $text .= 'Style[1]=["white","white","#0d0e0c","#4e554d","","skins/default/frame_akcni_menu/tooltip.png","","","","","","","","",240,"",2,2,10,10,"","","","",""]'."\n";        
	return $text;
}

function TooltipJednotky( $id_zeme = null ){
	global $db;
	
	$text = "\n";
	$id_general = VratGeneralaHrace();
	switch($id_general){
		case 2:
			//spojenci
			$pechota = "Pěchotní družstvo";
			$tanky = "Střední tank M4 Sherman";
			break;
		case 3:
			//soveti
			$pechota = "Pěchotní družstvo";
			$tanky = "Střední tank T-34";
			break;
		
	}
	
	$param = VratAtributyJednotek("pechota", $id_general, $id_zeme);
	$text .= 'Text[1]=["'.$pechota.'","Cena suroviny: <span class=\"all_suroviny\">'.$param['suroviny'].'</span><br />Útok: '.$param['utok'].'<br />Obrana: '.$param['obrana'].'<br />"]'."\n";
	$param = VratAtributyJednotek("tanky", $id_general, $id_zeme);
	$text .= 'Text[2]=["'.$tanky.'","Cena suroviny: <span class=\"all_suroviny\">'.$param['suroviny'].'</span><br />Útok: '.$param['utok'].'<br />Obrana: '.$param['obrana'].'<br />"]'."\n";	
    
    $text .= 'Style[1]=["white","white","#0d0e0c","#4e554d","","skins/default/frame_akcni_menu/tooltip.png","","","","","","","","",150,"",2,2,10,10,"","","","",""]'."\n";        
	
	return $text;
}
function TooltipJednotkyCiziZem($tooltip_general){
	global $db;
	
	$text = "\n";

	switch($tooltip_general){
		case 1:
			//nemci
			$pechota = "Pěchotní družstvo";
			$tanky = "Střední tank Panther";
			break;
		case 2:
			//spojenci
			$pechota = "Pěchotní družstvo";
			$tanky = "Střední tank M4 Sherman";
			break;
		case 3:
			//soveti
			$pechota = "Pěchotní družstvo";
			$tanky = "Střední tank T-34";
			break;
		
	}
	
	$param = VratAtributyJednotek("pechota", $tooltip_general);
	$text .= 'Text[1]=["'.$pechota.'","Útok: '.$param['utok'].'<br />Obrana: '.$param['obrana'].'<br />"]'."\n";
	$param = VratAtributyJednotek("tanky", $tooltip_general);
	$text .= 'Text[2]=["'.$tanky.'","Útok: '.$param['utok'].'<br />Obrana: '.$param['obrana'].'<br />"]'."\n";	
    
    $text .= 'Style[1]=["white","white","#0d0e0c","#4e554d","","skins/default/frame_akcni_menu/tooltip.png","","","","","","","","",150,"",2,2,10,10,"","","","",""]'."\n";        
	
	return $text;	
	
}

function TooltipBerlin( $id_ligy, $podminky ){
	global $db;
	
	$text = "\n";

	$text .= 'Text[1]=["Letecká síla","Dosahujeme <span style=\"color:gold;\">'.VratProcenaLeteckeSily( $id_ligy ).'%</span> letecké síly potřebné k úspěšnému útoku na Berlín.<br />"]'."\n";

	for ($i = 1; $i<= $podminky['pocet_podminek']; $i++){
		$text .= 'Text['.($i+5).']=["Podmínka","'. $podminky[$i.'tip'] . '"]'."\n";
	}	
	
    $text .= 'Style[1]=["white","white","#0d0e0c","#4e554d","","skins/default/frame_akcni_menu/tooltip.png","","","","","","","","",150,"",2,2,10,10,"","","","",""]'."\n";        
	
	return $text;
}

function TooltipProfilBody(){
	global $db;
	
	$text = "\n";

	$text .= 'Text[1]=["Získání bodů","<span style=\"color:gold;\">Vítězné body lze získat za:</span><br /> ';
	$text .= 'Výhra v DM scénáři: + 10 bodů<br />';
	$text .= 'Druhé místo v DM scénáři: + 7 bodů<br />';
	$text .= 'Třetí místo v DM scénáři: + 4 body<br />';
	$text .= 'Výhra v týmovém scénáři: + 8 bodů<br />';
	$text .= 'Účastník vítězného týmu: + 5 bodů<br />';
	$text .= '"]'."\n";

	
    $text .= 'Style[1]=["white","white","#0d0e0c","#4e554d","","skins/default/frame_akcni_menu/tooltip.png","","","","","","","","",280,"",2,2,10,10,"","","","",""]'."\n";        
	
	return $text;
}

?>