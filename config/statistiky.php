<?php
require_once($DIR_CONFIG . "konstanty.php");

/*
 pocet utoku - pocet utoku na hrace
`pocet_akci_podpora_pozitivni` - pocet pozitivnich akci
`pocet_akci_podpora_negativni` - pocet negativnich
`pocet_akci_podpora_uspesny` - pocet uspesnych negativnich
`pocet_akci_podpora_uspesny_na_hrace` - pocet uspesnych negativnich na hrace
`pocet_akci_letectvo_pozitivni` - pocet pozitivnich akci - vsech
`pocet_akci_letectvo_negativni` - pocet negativnich - vsech
`pocet_akci_letectvo_uspesny` - pocet uspesnych negativnich - vsech
`pocet_akci_letectvo_uspesny_na_hrace` - pocet uspesnych negativnich na hrace - jen na hrace
 */

function stat_prihlaseni( $id_hrac, $typ, $strana ){
	global $db; 
	$typ = "hranych_" . $typ;
	$strana = "her_s_" . $strana;
	
	$sql = "UPDATE users_stat SET $typ = $typ + 1, $strana = $strana + 1 ";
	$sql .= " WHERE id_user='$id_hrac' ";
	$db->DbQuery( $sql );
}

function stat_odhlaseni( $id_hrac ){
	global $db; 
	
	$sql = "UPDATE users_stat SET opustenych_lig = opustenych_lig + 1 ";
	$sql .= " WHERE id_user='$id_hrac' ";
	$db->DbQuery( $sql );
}

function stat_zniceny_hrac( $id_hrac ){
	global $db; 
	
	$sql = "UPDATE users_stat SET znicen_v_ligach = znicen_v_ligach + 1 ";
	$sql .= " WHERE id_user='$id_hrac' ";
	$db->DbQuery( $sql );
}

function stat_akce_load(){
	$pole = array();
	return $pole;
}

function stat_akce( &$pole, $id_hrac, $uspesnost, $id_akce, $typ, $obrance = null ){
	global $pole_skodlive_letectvo, $pole_skodliva_podpora;

	if( $typ == 'letectvo' ){
		$pole_skodlive = $pole_skodlive_letectvo;
	}
	else{
		$pole_skodlive = $pole_skodliva_podpora;
	}
	
	if( in_array( $id_akce, $pole_skodlive)){
		$index = 'pocet_akci_'.$typ.'_negativni';
	}
	else{
		$index = 'pocet_akci_'.$typ.'_pozitivni';
	}
	
	if(!isset($pole[$id_hrac][$index])){
		$pole[$id_hrac][$index] = 1; 
	}
	else{
		$pole[$id_hrac][$index]++;
	}
	 
	if( $uspesnost == 'uspech' && in_array( $id_akce, $pole_skodlive) ){
		if(!isset($pole[$id_hrac]['pocet_akci_'.$typ.'_uspesny'])){
			$pole[$id_hrac]['pocet_akci_'.$typ.'_uspesny'] = 1; 
		}
		else{
			$pole[$id_hrac]['pocet_akci_'.$typ.'_uspesny']++;
		}
		
		//uspesnost akci na hrace
		if(isset($obrance) && $obrance > 0){
			if(!isset($pole[$id_hrac]['pocet_akci_'.$typ.'_uspesny_na_hrace'])){
				$pole[$id_hrac]['pocet_akci_'.$typ.'_uspesny_na_hrace'] = 1; 
			}
			else{
				$pole[$id_hrac]['pocet_akci_'.$typ.'_uspesny_na_hrace']++;
			}
		}
	}
}

function stat_akce_save( $pole ){
	global $db;
	foreach( $pole as $key => $prvek ){
		$zmeneno = array();
		$sql = "UPDATE users_stat SET ";
		foreach( $prvek as $nazev => $cislo ){
			$zmeneno[] =  " $nazev = $nazev + $cislo ";
		}
		$sql .= implode(",", $zmeneno);
		$sql .= " WHERE id_user='$key' ";
		$db->DbQuery( $sql );
	}
	
}

?>