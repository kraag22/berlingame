<?php
	require_once("${DIR_LANG}napoveda.php");
	require_once($DIR_LIB . "hlaseni/common.php");

	global $HELP;
	
	$skin_dir = $DIR_SKINS. $page->skin . "/frame_mapa_napoveda/";
	$text = "";
	$text .= '<div class="napoveda">'; 
	$text .= '<div class="napoveda_obsah">'; 
	$text .= '<a href="frame_mapa.php?section=intro" target="mapa" title="Zavřít nápovědu" >
	<img src="'.$DIR_SKINS .'default/frame_mapa_napoveda/kriz.gif" alt="Zavřít nápovědu" style="border:0px;position: absolute; top: 17px; left: 665px;" /></a>';
  
	switch(array_get_index_safe('page', $_GET)) {
		case 'letectvo' :
			$text .= $HELP['LETECTVO'];
	        break;
	    case 'letectvo_typ':
	        $text .= $HELP['LETECTVO_TYP'];
	        break;
	    case 'letectvo_sila':
	        $text .= $HELP['LETECTVO_SILA'];
	        break;
	    case 'veleni' :
			$text .= $HELP['VELENI'];
	        break;
	    case 'veleni_akce':
	        $text .= $HELP['VELENI_AKCE'];
	        break;
	    case 'takticky_presun':
	        $text .= $HELP['TAKTICKY_PRESUN'];
	        break;
	    case 'berlin':
	        $text .= $HELP['BERLIN'];
	        break;
	    case 'obrana':
	        $text .= $HELP['OBRANA'];
	        break;
	    case 'utok':
	        $text .= $HELP['UTOK'];
	        break;
	    case 'pocasi':
	    	for($i=1; $i<15; $i++){
	    		$text .= '<a name="'.$i.'"></a>';
	    		$text .= VratPocasi(null, $i);
	    		$text .= '<br /><br /><br />';
	    	}
	        break;
	    case 'statistika_zeme':
	        $text .= $HELP['STATISTIKA_ZEME'];
	        break;
	    case 'vystavba':
	        $text .= $HELP['VYSTAVBA'];
	        break;
	}
	
	
	$text .= '</div>';
	$text .= '</div>';
	
	$form = new textElement($text);
	$page->add_element($form, 'obsah');
?>