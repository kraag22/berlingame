<?php

/**
 *  funkce vrati vsechny sousedy dane zeme
 * Muze vracet i sousedy zeme + sousedy, ke kterym se dostane pres nejakeho souseda.
 *
 * @param $id_zeme - id zeme, jejiz sousedy budeme vracet
 * @param $pruchody - nepovinny parametr. Pokud je 1, vrati i sousedy se vzdalenosti +1
 *
 * @return pole - funkce vraci pole vsech sousedu 
 * @return chyba - pokud jsou spatne zadane vstupni parametry
 */
function VratVsechnySousedy($id_zeme, $pruchody=null)
{
  global $db;
  $first_neighbours = array();
  $second_neighbours = array();
  $id_zeme = sqlsafe ( $id_zeme);
  
  $res = $db->Query("SELECT * FROM `sousede` where zeme1='". $id_zeme ."';");
	while ($sousede = $db->GetFetchAssoc( $res ))
	{
	 array_push($first_neighbours,array($sousede["zeme2"],0)); 
  }
  
  
  if($pruchody != null)
  {
      $query = "SELECT DISTINCT s2.zeme2 AS second_neighbour 
      FROM sousede AS s1 JOIN sousede AS s2 ON s1.zeme2=s2.zeme1 
      WHERE s1.zeme1='" . $id_zeme . "' AND s2.zeme2 NOT IN 
      (SELECT zeme2 FROM sousede 
      WHERE zeme1='" . $id_zeme . "') AND s2.zeme2!='" . $id_zeme . "';";
      $res = $db->Query($query);
	    while ($sousede2 = $db->GetFetchAssoc( $res ))
	    {
	       array_push($second_neighbours,array($sousede2["second_neighbour"],1)); 
      }
      $output = array_merge($first_neighbours,$second_neighbours);
      return $output;
  }
  else
  {
    return $first_neighbours;
  }
}
/**
 * Funkce vrati vsechny zeme do kterych se da utocit
 *
 * Vypisi se i volne pruchody, ale musi byt platne.
 * 
 * @param $id_zeme - id zeme ze ktere se bude utocit
 * @param $id_liga - liga ve ktere zem je
 * @return pole sousedu
 */
function VratVsechnyCiloveZemeProUtok( $id_zeme, $id_ligy ){
  global $db;
  $id_ligy = sqlsafe ( $id_ligy);
  $id_zeme = sqlsafe ( $id_zeme);
  $first_neighbours = array();
  $second_neighbours = array();
  
  $res = $db->Query("SELECT * FROM `sousede` where zeme1='". $id_zeme ."';");
	while ($sousede = $db->GetFetchAssoc( $res ))
	{
	 array_push($first_neighbours,array($sousede["zeme2"],0)); 
  }
  
      $query = "SELECT DISTINCT s2.zeme2 AS second_neighbour 
      			FROM sousede AS s1 JOIN sousede AS s2 ON s1.zeme2=s2.zeme1 
      			WHERE s1.zeme1='" . $id_zeme . "' 
      			AND s2.zeme2 NOT IN (SELECT zeme2 FROM sousede WHERE zeme1='" . $id_zeme . "') 
      			AND s2.zeme2!='" . $id_zeme . "'
      			AND EXISTS(
      			SELECT * FROM in_game_pruchody WHERE id_ligy=$id_ligy AND platnost=1 AND (
					(id_zeme_kam='$id_zeme'and id_zeme_odkud = s2.zeme1)
					OR
					(id_zeme_odkud='$id_zeme'and id_zeme_kam =  s2.zeme1)
					)
      			)
      			;";
      $res = $db->Query($query);
	    while ($sousede2 = $db->GetFetchAssoc( $res ))
	    {
	       		array_push($second_neighbours,array($sousede2["second_neighbour"],1));
      }
      $output = array_merge($first_neighbours,$second_neighbours);
      return $output;
}

/**
 *  funkce vrati sousedy dane zeme
 * Muze vracet i sousedy zeme + sousedy, ke kterym se dostane pres nejakeho souseda.
 *
 * @param $id_zeme - id zeme, jejiz sousedy budeme vracet
 * @param $id_pruchozi_zeme - nepovinny parametr. Pokud je v nem zadane id zeme, zkontroluje se, zda je to soused zeme $id_zeme. Pokud ne, vrati chybu.
 *
 * @return pole - funkce vraci pole sousedu ( id -  sousedni zeme, pruchod - 0 nebo 1 podle toho jestli je to primy soused nebo soused pres pruchod  ). Kazda zem je vracena jen jednou. I kdyz sousedi pres pruchod i primo (priznak je 0 (primy soused) pokud muze byt zeme sousedni pres pruchod i primo)
 * @return chyba - pokud jsou spatne zadane vstupni parametry
 */
function VratSousedy($id_zeme, $id_pruchozi_zeme=null)
{
  global $db;
  $first_neighbours = array();
  $second_neighbours = array();
  $id_zeme = sqlsafe($id_zeme);
  
  $res = $db->Query("SELECT * FROM `sousede` where zeme1='". $id_zeme ."';");
	while ($sousede = $db->GetFetchAssoc( $res ))
	{
	 array_push($first_neighbours,array($sousede["zeme2"],0)); 
  }
  
  
  if($id_pruchozi_zeme != null)
  {
    if(in_array(array($id_pruchozi_zeme,0), $first_neighbours))
    {
      $query = "SELECT DISTINCT s2.zeme2 AS second_neighbour 
      FROM sousede AS s1 JOIN sousede AS s2 ON s1.zeme2=s2.zeme1 
      WHERE s1.zeme1='" . $id_zeme . "' AND s2.zeme2 NOT IN 
      (SELECT zeme2 
      FROM sousede 
      WHERE zeme1='" . $id_zeme . "') AND s2.zeme2!='" . $id_zeme . "' AND s1.zeme2='" . $id_pruchozi_zeme . "';";
      $res = $db->Query($query);
	    while ($sousede2 = $db->GetFetchAssoc( $res ))
	    {
	       array_push($second_neighbours,array($sousede2["second_neighbour"],1)); 
      }
      $output = array_merge($first_neighbours,$second_neighbours);
      return $output;
      
    }
    else
    {
      return "chyba"; //todo: aka chyba?
    }
  }
  else
  {
    return $first_neighbours;
  }
}

function zemeNaKtereMuzuUtocitPrimo( $id_zeme, $id_user, $id_ligy )
{
  global $db;
  $neighbours = array();
  
  $query = "SELECT zeme1, zeme2, angle, dist, center_x, center_y FROM sousede JOIN zeme ON zeme1 = id 
    WHERE zeme1 = '$id_zeme' AND zeme2 NOT IN (
                SELECT id_zeme FROM `zeme_view` AS zv JOIN `in_game_hrac` AS igh ON zv.id_vlastnik=id_hrac 
                WHERE pos_x IS NOT NULL AND zv.id_ligy = '$id_ligy' AND id_vlastnik = '$id_user');";
                
  $res = $db->Query($query);
  while ($sousede = $db->GetFetchAssoc( $res ))
  {
   $neighbours[] = $sousede["zeme2"];
  }
  //print_r($neighbours);
  return $neighbours;

                  
};

function VratBonusyZeme( $id_zeme ){
	global $db;
	$bonusy = "";
	
	$id_zeme = sqlsafe( $id_zeme );
	
	$query = "SELECT * FROM zeme WHERE id='" . $id_zeme."'";
	$res = $db->Query( $query );
	$zeme = $db->GetFetchAssoc( $res );
	
	if($zeme['bonus_obrana']!=0){
		$bonusy .= $zeme['bonus_obrana_popis'] ." Obrana: ". $zeme['bonus_obrana'] . "%<br />";
	}
	if($zeme['bonus_pvo_obrana']!=0){
		$bonusy .= $zeme['bonus_pvo_obrana_popis'] ." PVO: ". $zeme['bonus_pvo_obrana'] . "%<br />";	
	}
	if($zeme['bonus_palivo']!=0){
		$bonusy .= $zeme['bonus_palivo_popis'] ." Zisk paliva: ". $zeme['bonus_palivo'] . "%<br />";
	}
	if($zeme['bonus_suroviny']!=0){
		$bonusy .= $zeme['bonus_suroviny_popis'] ." Zisk surovin: ". $zeme['bonus_suroviny'] . "%<br />";
	}
	
	return $bonusy;
}

function VratVlivyNaZemi( $id_zeme, $id_ligy ){
	global $db, $DIR_SKINS;
	$vlivy = "";
	$dir = $DIR_SKINS . "default/frame_akcni_menu/";
	$id_zeme = sqlsafe( $id_zeme );
	
	$query = "SELECT DISTINCT p.nazev FROM in_game_vlivy_podpora as igv JOIN 
			podpora_z_domova as p ON p.id=igv.id_podpora where 
			igv.id_zeme='$id_zeme' AND igv.id_ligy='$id_ligy'";
	$res = $db->Query( $query );
	while ($vliv = $db->GetFetchAssoc( $res )){
		$vlivy .= "<img src=\"${dir}dalekohled.png\" alt\"obrazek_podpory\"
		title=\"".$vliv['nazev']."\"/>";
	}	
	
	$query = "SELECT DISTINCT p.nazev FROM in_game_vlivy_letecke_akce as igv JOIN 
			letecke_akce as p ON p.id=igv.id_letecke_akce where 
			igv.id_zeme='$id_zeme' AND igv.id_ligy='$id_ligy'";
	$res = $db->Query( $query );
	while ($vliv = $db->GetFetchAssoc( $res )){
		$vlivy .= "<img src=\"${dir}dalekohled.png\" alt\"obrazek_let_akce\"
		title=\"".$vliv['nazev']."\"/>";
	}	
	
	return $vlivy;
}
/**
* Function helps in generating js images for default/frame_mapa/skript.js
*/
function generate_js_imgs()
{
  global $db;
  $query = "SELECT id, pos_x, pos_y FROM `zeme` WHERE pos_x IS NOT NULL;";
  $res = $db->Query($query);
	while ($polohy = $db->GetFetchAssoc( $res ))
	{
	 $str = 'obrazky["' . $polohy["id"] .'"] = new Image;';
	 $str .= "\n";
	 $str .= 'obrazky["' . $polohy["id"] .'"].src = dir + "highlighted/' . $polohy["id"] .'.png";';
	 $str .= "\n";
	 $str .= 'obrazky["' . $polohy["id"] .'_over"] = new Image;';
	 $str .= "\n";
	 $str .= 'obrazky["' . $polohy["id"] .'_over"].src = dir + "over/' . $polohy["id"] .'.png";';
	 $str .= "\n";
	 echo($str); 
  }
}

?>
