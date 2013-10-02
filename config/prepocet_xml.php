<?php

/**
 * Funkce vygeneruje XML data. Data jsou verejna a jsou vytvorena hned po prepoctu
 * @return unknown_type
 */
function XMLexport(){
	global $db,$DIR_INC;
	
	$xmltext = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<berlingame></berlingame>";
	$xml = simplexml_load_string($xmltext);
	
	$res = $db->Query("select * from ligy where stav='active'");
	while( $row = $db->GetFetchAssoc( $res ) ){

		xml_scenar( $xml->addChild("scenar"), $row);
	}
	
	$xml->asXML($DIR_INC.'export.xml');
	
	return 'OK';
}

function xml_scenar( $scenar, $row ){
	global $db;
	
	$scenar->addChild("id",$row['id']);
	$scenar->addChild("nazev",$row['nazev']);
	$scenar->addChild("typ",$row['typ']);
	$scenar->addChild("rocni_obdobi",$row['rocni_obdobi']);
	$scenar->addChild("pocasi",$row['id_pocasi_dnes']);
	$scenar->addChild("odehranych_dnu",$row['odehranych_dnu']);
	$scenar->addChild("registrace",$row['registrace']);
	$scenar->addChild("dohrano",$row['dohrano']);
	
	$sektory = $scenar->addChild("sektory");
	
	$res = $db->Query("select * from in_game_zeme where id_ligy='".$row['id']."'");
	while( $row_sektor = $db->GetFetchAssoc( $res ) ){
		xml_sektor( $sektory->addChild("sektor"), $row_sektor );
	}
	
	$hraci = $scenar->addChild("hraci");
	
	$res = $db->Query("select igh.*, us.login, us.last_activity from in_game_hrac AS igh JOIN users_sys AS us ON
			igh.id_hrac=us.id
			where igh.id_liga='".$row['id']."'");
	while( $row_hrac = $db->GetFetchAssoc( $res ) ){
		xml_hrac( $hraci->addChild("hrac"), $row_hrac );
	}
	
	
	$akce_podpory = $scenar->addChild("akce_podpory");
	
	$res = $db->Query("select distinct id_podpora, id_zeme from in_game_vlivy_podpora where id_ligy='".$row['id']."'");
	while( $row_podpora = $db->GetFetchAssoc( $res ) ){
		xml_podpora( $akce_podpory->addChild("podpora"), $row_podpora );
	}
	
	$stavby = $scenar->addChild("stavby");
	
	$res = $db->Query("select * from in_game_stavby where id_ligy='".$row['id']."'
	and id_stavby in (1,2,11,15,24) order by id_zeme");
	while( $row_stavba = $db->GetFetchAssoc( $res ) ){
		xml_stavba( $stavby->addChild("sektor"), $row_stavba );
	}
}

function xml_sektor( $sektor, $row_sektor ){
	global $db;
	
	$sektor->addChild("id", $row_sektor['id_zeme']);
	$sektor->addChild("majitel", $row_sektor['id_vlastnik']);
	$sektor->addChild("infra", $row_sektor['infrastruktura_now']);
	$sektor->addChild("pechota", $row_sektor['pechota']);
	$sektor->addChild("tanky", $row_sektor['tanky']);
}

function xml_hrac( $hrac, $row_hrac ){
	global $db;
	
	$hrac->addChild("id", $row_hrac['id_hrac']);
	$hrac->addChild("jmeno", $row_hrac['login']);
	$hrac->addChild("strana", $row_hrac['strana']);
	$hrac->addChild("naposledy_online", $row_hrac['last_activity']);
	$hrac->addChild("id_znak", $row_hrac['symbol']);
	$hrac->addChild("letecka_sila", round($row_hrac['letecka_sila'] * 20 / 300) * 5);
}

function xml_podpora( $podpora, $row_podpora){
	global $db;
	
	$podpora->addChild("id", $row_podpora['id_podpora']);
	$podpora->addChild("sektor", $row_podpora['id_zeme']);
}

function xml_stavba( $sektor, $row_stavba){
	global $db;
	
	$sektor->addChild("id_sektor", $row_stavba['id_zeme']);
	$sektor->addChild("id_stavba", $row_stavba['id_stavby']);
}

