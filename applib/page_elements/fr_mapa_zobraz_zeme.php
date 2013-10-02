<?php

require_once($DIR_CONFIG . "mapa.php");

/**
 * @brief funkce vrati html kod, ktery zobrazi zem podle predanych parametru
 *
 * @param $id_zeme - ID zeme, ktera se zobrazuje (ID z tabulky zeme)
 * @param $id - ID zeme, ktera se zobrazuje (pro potreby designu)
 * @param $nazev - nazev zeme, ktera se zobrazuje
 * @param $strana - hodnata urcuje zda je mapa ovladana SSSR, USA, nazi nebo neutralni
 * @param $highlight - urcuje, jestli zvyraznit danu zem nebo nikoliv
 * @param $hracovy_zeme - pole, ktere obsahuje ID cisla zemi, ktere hrac vlastni
 * @param $podklad_x - x -ova souradnice podbarveni zeme
 * @param $podklad_y - y -ova souradnice podbarveni zeme
 * @param $symbol_x - x -ova souradnice, ze ktere se da spocitat umisteni symbolu,
 *                                      letiste a bunkru
 * @param $symbol_y - y -ova souradnice, ze ktere se da spocitat umisteni symbolu,
 *                                      letiste a bunkru
 * @param $subquest_x - x -ova souradnice umisteni subquestu na mape
 * @param $subquest_y - y -ova souradnice umisteni subquestu na mape
 * @param $hrdina_x - x -ova souradnice umisteni hrdiny na mape
 * @param $hrdina_y - y -ova souradnice umisteni hrdiny na mape
 * @param $sipky - pole souradnic vsech sipek zobrazovane zeme
 * @param $skin_dir - adresar ve kterem jsou obrazky
 * @param $letiste - typ letiste, ktery je v zemi postaven
 * @param $bunkr - typ bunkru, ktery je v zemi postaven
 * @param $subquest - id subquestu. Pokud zadny neni je neciselny typ
 * @param $hridna - id hrdiny. Pokud zadny neni je neciselny typ
 *
 */
function ZobrazZem($id_zeme, $id, $nazev, $strana, $highlight, $hracovy_zeme, $podklad_x, $podklad_y,
                                        $symbol_x, $symbol_y, $subquest_x, $subquest_y, $veleni_x = 0,
                                        $veleni_y = 0, $sipky, $skin_dir, $letiste, $bunkr, $subquest = null,
                                         $veleni = false, $symbol, $parametry){
//pokud neni zem berlin
if( $id_zeme != 45){
	$nazev = $nazev . " [Pěchota:". $parametry['pechota'] . ", Tanky:". $parametry['tanky'] ."]";	
}                                       	

$zeme = "";
$highlightString = ($highlight > 0) ? "highlighted/".$id : "prazdno";
$zeme .='<img src="'.$skin_dir.'zeme/prazdno.png" id="img_c'.$id.'" alt="" style="position:absolute;left: '.$podklad_x.'px; top: '.$podklad_y.'px;" />
<img src="'.$skin_dir.'zeme/' . $highlightString . '.png" id="img_c'.$id.'_highlight" alt="" style="position:absolute;left: '.$podklad_x.'px; top: '.$podklad_y.'px;" />
<img src="'.$skin_dir.'zeme/prazdno.png" id="img_c'.$id.'_over" alt="" style="position:absolute;left: '.$podklad_x.'px; top: '.$podklad_y.'px;" />
<div id="c' . $id . '"  onclick="clickLand(\''.$id.'\'); " onmouseover="mouseOverLand(\''.$id.'\',1); return false;" onmouseout="mouseOverLand(\''.$id.'\',0); return false;">';

$zeme .= '<a href="frame_akcni_menu.php?section=info&amp;id='.$id_zeme.'" target="am">';
switch($strana){
        case "us":
                $zeme .= ' <img  src="'.$skin_dir.'zeme/symbols/'.$symbol.'" style="position:absolute;left: '.($symbol_x+2).'px; top:'.($symbol_y+2).'px;z-index:101;"  alt="'.$nazev.'" title="'.$nazev.'" /> </a>'."\n";
                break;
        case "sssr":
                $zeme .= ' <img  src="'.$skin_dir.'zeme/symbols/'.$symbol.'" style="position:absolute;left: '.$symbol_x.'px; top:'.$symbol_y.'px;z-index:101;"  alt="'.$nazev.'" title="'.$nazev.'" /> </a>' ."\n";
                break;
        case "nazi":
                $zeme .= ' <img  src="'.$skin_dir.'zeme/symbols/nazi.png" style="position:absolute;left: '.$symbol_x.'px; top:'.$symbol_y.'px;z-index:101;"  alt="'.$nazev.'" title="'.$nazev.'" /> </a>'."\n";
                break;
        default:
                $zeme .= ' <img  src="'.$skin_dir.'zeme/symbols/neutral.png" style="position:absolute;left: '.$symbol_x.'px; top:'.$symbol_y.'px;z-index:101;"  alt="'.$nazev.'" title="'.$nazev.'" /></a>'."\n";

}
$zeme .= '<a href="frame_akcni_menu.php?section=vystavba&amp;id='.$id_zeme.'" target="am">';
switch($letiste){
        case "polni_letiste":
                $zeme .= '<img  src="'.$skin_dir.'zeme/polni_letiste.png" style="position:absolute;left: '.($symbol_x - 8) .'px; top:'.($symbol_y + 17) .'px;z-index:100;"  alt="'.$nazev.'" title="'.$nazev.'" /></a>'."\n";
                break;
        case "letiste":
                $zeme .= '<img  src="'.$skin_dir.'zeme/letiste.png" style="position:absolute;left: '.($symbol_x - 8) .'px; top:'.($symbol_y + 17) .'px;z-index:100;"  alt="'.$nazev.'" title="'.$nazev.'" /></a>'."\n";
                break;
        case "hangar":
                $zeme .= '<img  src="'.$skin_dir.'zeme/hangar.png" style="position:absolute;left: '.($symbol_x - 8) .'px; top:'.($symbol_y + 17) .'px;z-index:100;"  alt="'.$nazev.'" title="'.$nazev.'" /></a>'."\n";
                break;
        case "brigada":
                $zeme .= '<img  src="'.$skin_dir.'zeme/brigada.png" style="position:absolute;left: '.($symbol_x - 8) .'px; top:'.($symbol_y + 17) .'px;z-index:100;"  alt="'.$nazev.'" title="'.$nazev.'" /></a>'."\n";
                break;
        default:
                $zeme .= '<img  src="'.$skin_dir.'zeme/no_icon.png" style="position:absolute;left: '.($symbol_x - 8) .'px; top:'.($symbol_y + 17) .'px;z-index:100;"  alt="'.$nazev.'" title="'.$nazev.'" /></a>'."\n";
}

$zeme .= '<a href="frame_akcni_menu.php?section=obrana&amp;id='.$id_zeme.'" target="am">';
if($bunkr=="bunkr"){
	switch($strana){
	        case "us":
	                $zeme .= '<img  src="'.$skin_dir.'zeme/bunkr_us.png" style="position:absolute;left: '.($symbol_x + 16) .'px; top:'.($symbol_y + 19) .'px;z-index:100;"  alt="'.$nazev.'" title="'.$nazev.'" /></a>'."\n";
	                break;
	        case "sssr":
	                $zeme .= '<img  src="'.$skin_dir.'zeme/bunkr_sssr.png" style="position:absolute;left: '.($symbol_x + 16) .'px; top:'.($symbol_y + 19) .'px;z-index:100;"  alt="'.$nazev.'" title="'.$nazev.'" /></a>'."\n";
	                break;
	        default:
	                $zeme .= '<img  src="'.$skin_dir.'zeme/no_icon.png" style="position:absolute;left: '.($symbol_x + 16) .'px; top:'.($symbol_y + 19) .'px;z-index:100;"  alt="'.$nazev.'" title="'.$nazev.'" /></a>'."\n";
	}
}
else{
	$zeme .= '<img  src="'.$skin_dir.'zeme/no_icon.png" style="position:absolute;left: '.($symbol_x + 16) .'px; top:'.($symbol_y + 19) .'px;z-index:100;"  alt="'.$nazev.'" title="'.$nazev.'" /></a>'."\n";
}

if (is_numeric($subquest)){
        $zeme .= '<a href="#"> <img  src="'.$skin_dir.'subquesty/'.$subquest.'.png" style="position:absolute;left: '.$subquest_x.'px; top:'.$subquest_y.'px;" /></a>'."\n";
}

if ($veleni){
	$obr = 'vv_' . $strana;
	$adr = '<a href="frame_akcni_menu.php?section=veleni&amp;id='.$id_zeme.'" target="am">';
}
else{
	$obr = 'no_icon';
	$adr = '<a href="frame_akcni_menu.php?section=obrana&amp;id='.$id_zeme.'" target="am">';
}
$x = $veleni_x == 0 ? ($symbol_x + 15) : ($veleni_x);
$y = $veleni_y == 0 ? ($symbol_y - 10) : ($veleni_y);
$zeme .= $adr;
$zeme .= '<img  src="'.$skin_dir.'zeme/'.$obr.'.png" style="position:absolute;left: '.$x.'px; top:'.$y.'px;" alt="'.$nazev.'" title="'.$nazev.'" />
</a>'."\n";

$zeme .='</div>

';
return $zeme;
}

function nakresliSipky($skin_dir, $id_zeme, $center_x, $center_y, $sousede)
{
        global $db;
        $text = "";
        //$user_id = $users_class->user_id();
        //$id_ligy = isset($_SESSION["id_ligy"])?$_SESSION["id_ligy"]:-1;
        foreach ($sousede as $value) {
                $query = "SELECT angle, dist FROM sousede WHERE zeme1 = '$id_zeme' AND zeme2 = '$value'";
//      echo($query);
                $res = $db->Query($query);
                $poloha = $db->GetFetchAssoc( $res );
                $delta_x = ceil($poloha["dist"] * cos(deg2rad($poloha["angle"])));
                $delta_y = ceil($poloha["dist"] * sin(deg2rad($poloha["angle"])));
                $c_sipky = 360 - ceil($poloha["angle"]/10)*10;
                $new_x = $center_x + $delta_x;
                $new_y = $center_y - $delta_y;
                $text .= '<a href="frame_akcni_menu.php?section=utok&amp;zeme_odkud='.$id_zeme.'&amp;zeme_kam='.$value.'" target="am"><img src="'.$skin_dir.'zeme/arrows/'.$c_sipky.'.png" alt="attack pointer" style="position:absolute; left: '.$new_x.'px; top: '.$new_y.'px;" /></a>
';
                //echo('<a href="utok, vole"><img src="'.$skin_dir.'zeme/arrows/'.$c_sipky.'.png" alt="attack pointer" style="position:absolute; left: '.$new_x.'; top: '.$new_y.' " /></a>');
        }
        return $text;
}



function zobrazSipky($skin_dir, $user_id, $id_ligy)
{
        global $db;
        //global $users_class;
        $text = "";
        //$user_id = $users_class->user_id();
        //$id_ligy = isset($_SESSION["id_ligy"])?$_SESSION["id_ligy"]:-1;

        $query = "SELECT id_zeme, nazev, id_vlastnik, pos_x, pos_y, center_x, center_y, strana 
                FROM `zeme_view` AS zv JOIN `in_game_hrac` AS igh ON zv.id_vlastnik=id_hrac 
                WHERE pos_x IS NOT NULL AND zv.id_ligy = '$id_ligy' AND id_vlastnik = '$user_id';";
//      echo($query);
        $highlight = 0;
        $res = $db->Query($query);
        while ($zeme = $db->GetFetchAssoc( $res ))
        {
                //echo("moje krajiny: zeme id: " . $zeme["id_zeme"]. "; nazev: " . $zeme["nazev"] . "; strana: " . $zeme["strana"]. "<br />\n" );
                $sousede = zemeNaKtereMuzuUtocitPrimo( $zeme["id_zeme"], $user_id, $id_ligy );
                //print_r($sousede);
                $text .= '<div id="arr'.$zeme["id_zeme"].'" style="display:none;">';
                $text .= "\n";
                $text .= nakresliSipky($skin_dir, $zeme["id_zeme"], $zeme["center_x"], $zeme["center_y"], $sousede);
                $text .= '</div>';
                $text .= "\n";
        }
        return $text;
}

function StavZemeNaMape( $id_zeme, $id_ligy, $id_vlastnik){
	global $db;

//pocet tanku a pechoty
	$query = "SELECT *
    	FROM `in_game_vcera_zeme` 
        WHERE id_zeme='$id_zeme'
              and id_ligy='$id_ligy';";
    $res1 = $db->Query($query);
    $roww = $db->GetFetchAssoc( $res1 );
    $pole['pechota'] = $roww['pechota'];
    $pole['tanky'] = $roww['tanky'];
    
//LETISTE
    $query = "SELECT id_stavby
    	FROM `in_game_stavby` 
        WHERE id_zeme='$id_zeme'
              and id_ligy='$id_ligy'
              and 
              (id_stavby=1 or id_stavby=2 or id_stavby=24)
              ;";
    $res2 = $db->Query($query);
    
    $pole['letiste']= "neni";
    while ($stav = $db->GetFetchRow( $res2 )){
        if ($stav[0]==1){
        	$pole['letiste']= "letiste";
        	break;	
        }
   		else if($stav[0]==24){
        	$pole['letiste']= "brigada";
        }
        else if($stav[0]==2){
        	$pole['letiste']= "polni_letiste";
        }		
    }
    
//BUNKRY
    $query = "SELECT id_stavby
    	FROM `in_game_stavby` 
        WHERE id_zeme='$id_zeme'
              and id_ligy='$id_ligy'
              and 
              (id_stavby=11 )
              ;";
    $res2 = $db->Query($query);
    
    $pole['bunkr']= "neni";
    while ($stav = $db->GetFetchRow( $res2 )){
        if ($stav[0]==11){
        	$pole['bunkr']= "bunkr";	
        }	
    }
    
//VELENI
	$query = "SELECT id_stavby
    	FROM `in_game_stavby` 
        WHERE id_zeme='$id_zeme'
              and id_ligy='$id_ligy'
              and 
              (id_stavby=15 )
              ;";
    $res2 = $db->Query($query);
    
    $pole['veleni']= false;
    if ($stav = $db->GetFetchRow( $res2 )){
        	$pole['veleni']= true;
    }

//SUBQUESTY
  /*  $query = "SELECT id_quest
    	FROM `in_game_questy` 
        WHERE id_zeme='$id_zeme'
              and id_ligy='$id_ligy'
              ;";
    $res2 = $db->Query($query);
    */
    $pole['subquest']= "neni";
   /* if ($stav = $db->GetFetchRow( $res2 )){
        	$pole['subquest']= $stav[0];	
    }*/
    
//SYMBOL
//jen pro hraci ovladane zeme
if (isset($id_vlastnik)){
	$query = "SELECT symbol, strana
    	FROM `in_game_hrac` 
        WHERE id_hrac='$id_vlastnik'
              ;";
    $res3 = $db->Query($query);
    
	if ($stav = $db->GetFetchAssoc( $res3 )){
	    	if($stav['strana']=="sssr"){
	    		$pole['symbol']= "s";
	    	}
			if($stav['strana']=="us"){
	    		$pole['symbol']= "u";
	    	}    	
		 $pole['symbol'] .= $stav['symbol'] . ".png";	
	}
}
else{
	$pole['symbol'] = "nenihrac";
}
	
	return $pole;
}

/*
 * funkce vrati html kod, ktery zobrazi vsechny zeme na mapu
 */
function ZobrazZeme( $skin_dir ){

        //nejprve zobraz zeme, ktere nekdo v teto lize vlastni
        global $db;
        global $users_class;
        $text = "";
        $user_id = $users_class->user_id();
        $id_ligy = isset($_SESSION["id_ligy"])?$_SESSION["id_ligy"]:-1;
        $query = "SELECT id_zeme,zv.id_ligy, nazev, id_vlastnik, pos_x, pos_y, center_x, center_y, strana 
                FROM `zeme_view` AS zv JOIN `in_game_hrac` AS igh ON zv.id_vlastnik=id_hrac 
                WHERE pos_x IS NOT NULL AND zv.id_ligy = '$id_ligy';";
//      echo($query);
        $highlight = 0;
        $res = $db->Query($query);
        while ($zeme = $db->GetFetchAssoc( $res ))
        {
        		$pole = StavZemeNaMape($zeme["id_zeme"], $zeme["id_ligy"],$zeme["id_vlastnik"]);
                //echo("moje krajiny: zeme id: " . $zeme["id_zeme"]. "; nazev: " . $zeme["nazev"] . "; strana: " . $zeme["strana"]. "<br />\n" );
                $highlight = ($zeme["id_vlastnik"] == $user_id)?1:0;
                $text .= ZobrazZem($zeme["id_zeme"], $zeme["id_zeme"], $zeme["nazev"], $zeme["strana"], $highlight, '',$zeme["pos_x"], $zeme["pos_y"], $zeme["center_x"], $zeme["center_y"], $zeme["center_x"] - 20, $zeme["center_y"] - 25, $zeme["veleni_x"], $zeme["veleni_y"],'',$skin_dir, $pole['letiste'], $pole['bunkr'],$pole["subquest"],$pole['veleni'],$pole['symbol'], $pole);
        }
        
        //a pak neutralni/jeste neobsazene zeme
        $query = "SELECT zv.id_zeme, nazev,id_ligy, id_vlastnik, pos_x, pos_y, center_x, center_y
                FROM `zeme_view` AS zv 
                WHERE pos_x IS NOT NULL AND id_vlastnik IS NULL AND zv.id_ligy = '$id_ligy';";
        $res = $db->Query($query);
        while ($zeme = $db->GetFetchAssoc( $res ))
        {		
        		$pole = StavZemeNaMape($zeme["id_zeme"], $zeme["id_ligy"],$zeme["id_vlastnik"]);
        		
                //echo("ostatne krajiny: zeme id: " . $zeme["id_zeme"]. "; nazev: " . $zeme["nazev"] . "; strana: <br />\n" );
                $text .= ZobrazZem($zeme["id_zeme"],$zeme["id_zeme"], $zeme["nazev"], "neutral", 0, '',$zeme["pos_x"], $zeme["pos_y"], $zeme["center_x"], $zeme["center_y"], $zeme["center_x"] - 20, $zeme["center_y"] - 25, $zeme["veleni_x"], $zeme["veleni_y"],'',$skin_dir, $pole['letiste'], $pole['bunkr'],$pole["subquest"],$pole['veleni'],$pole['symbol'], $pole);
        }
        $text .= zobrazSipky($skin_dir, $user_id, $id_ligy);
return $text;

}


function ZobrazMapu( $skin_dir, $id_ligy ){
	global $users_class, $db, $DIR_INC;
	
	$text = "";
	
	//nacteni struktury ze souboru
	$filename = $DIR_INC . $id_ligy . ".php";
	$file = fopen( $filename, 'rb' );
	$obsah = fread($file, filesize($filename));
	fclose($file);
	$pole_zemi = unserialize( $obsah );
	
	//prihlaseny uzivatel
	$user_id = $users_class->user_id();
	if( $user_id != -1){
		unset($pole_zemi[$user_id]);
		
		//zobrazeni aktualniho stavu hracovych zemi
        $query = "SELECT id_zeme,zv.id_ligy, nazev, id_vlastnik, pos_x, pos_y, center_x, center_y, strana 
                FROM `zeme_view` AS zv JOIN `in_game_hrac` AS igh ON zv.id_vlastnik=id_hrac 
                WHERE pos_x IS NOT NULL AND zv.id_ligy = '$id_ligy' AND id_hrac='$user_id';";
        $res = $db->Query($query);
        while ($zeme = $db->GetFetchAssoc( $res ))
        {
        		$pole = StavZemeNaMape($zeme["id_zeme"], $zeme["id_ligy"],$zeme["id_vlastnik"]);
                //echo("moje krajiny: zeme id: " . $zeme["id_zeme"]. "; nazev: " . $zeme["nazev"] . "; strana: " . $zeme["strana"]. "<br />\n" );
                $highlight = ($zeme["id_vlastnik"] == $user_id)?1:0;
                $text .= ZobrazZem($zeme["id_zeme"], $zeme["id_zeme"], $zeme["nazev"], $zeme["strana"], $highlight, '',$zeme["pos_x"], $zeme["pos_y"], $zeme["center_x"], $zeme["center_y"], $zeme["center_x"] - 20, $zeme["center_y"] - 25, 0, 0,'',$skin_dir, $pole['letiste'], $pole['bunkr'],$pole["subquest"],$pole['veleni'],$pole['symbol'], $pole);
        }
        //zobrazeni sipek
        $text .= zobrazSipky($skin_dir, $user_id, $id_ligy);
	}
	
	//vsichni neprihlaseni hraci
	foreach( $pole_zemi as $hracova ){
		foreach( $hracova as $zem ){
			$text .= $zem;
		}
	}
	return $text;
}

function GenerujMapu( $id_ligy ){
	    //nejprve zobraz zeme, ktere nekdo v teto lize vlastni
        global $db, $DIR_INC, $DIR_SKINS;
        $mapa = array();
        $skin_dir = $DIR_SKINS. "default/frame_mapa/";

        $query = "SELECT id_zeme,zv.id_ligy, nazev, id_vlastnik, pos_x, pos_y, center_x, center_y, strana 
                FROM `zeme_view` AS zv JOIN `in_game_hrac` AS igh ON zv.id_vlastnik=id_hrac 
                WHERE pos_x IS NOT NULL AND zv.id_ligy = '$id_ligy';";

        $res = $db->Query($query);
        while ($zeme = $db->GetFetchAssoc( $res ))
        {
        		$pole = StavZemeNaMape($zeme["id_zeme"], $zeme["id_ligy"],$zeme["id_vlastnik"]);
                $highlight = 0;
                $mapa[$zeme["id_vlastnik"]][$zeme["id_zeme"]] = ZobrazZem($zeme["id_zeme"], $zeme["id_zeme"], $zeme["nazev"], $zeme["strana"], $highlight, '',$zeme["pos_x"], $zeme["pos_y"], $zeme["center_x"], $zeme["center_y"], $zeme["center_x"] - 20, $zeme["center_y"] - 25, 0, 0,'',$skin_dir, $pole['letiste'], $pole['bunkr'],$pole["subquest"],$pole['veleni'],$pole['symbol'], $pole);
        }
        
        //a pak neutralni/jeste neobsazene zeme
        $query = "SELECT zv.id_zeme, nazev,id_ligy, id_vlastnik, pos_x, pos_y, center_x, center_y
                FROM `zeme_view` AS zv 
                WHERE pos_x IS NOT NULL AND id_vlastnik IS NULL AND zv.id_ligy = '$id_ligy';";
        $res = $db->Query($query);
        while ($zeme = $db->GetFetchAssoc( $res ))
        {		
        		$pole = StavZemeNaMape($zeme["id_zeme"], $zeme["id_ligy"],$zeme["id_vlastnik"]);
        		
                //echo("ostatne krajiny: zeme id: " . $zeme["id_zeme"]. "; nazev: " . $zeme["nazev"] . "; strana: <br />\n" );
                 $mapa[-1][$zeme["id_zeme"]] = ZobrazZem($zeme["id_zeme"],$zeme["id_zeme"], $zeme["nazev"], "neutral", 0, '',$zeme["pos_x"], $zeme["pos_y"], $zeme["center_x"], $zeme["center_y"], $zeme["center_x"] - 20, $zeme["center_y"] - 25, 0, 0,'',$skin_dir, $pole['letiste'], $pole['bunkr'],$pole["subquest"],$pole['veleni'],$pole['symbol'], $pole);
        }
        //$text .= zobrazSipky($skin_dir, $user_id, $id_ligy);
	
	$file = fopen( $DIR_INC . $id_ligy . '.php', 'wb' );
	fwrite($file, serialize($mapa) );
	fclose($file);
}

?>