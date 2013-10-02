<?php

/**
 * vrati grafiku s popisem budto aktualni pocasi v lize, nebo toho se zadanym ID
 *
 * @param unknown_type $id_ligy
 * @param unknown_type $id_pocasi
 * @return unknown
 */
function VratPocasi( $id_ligy = null, $id_pocasi = null ){
	global $db, $TEXT;
	$pocasi = '';
	if(!empty($id_ligy)){
		$id_ligy = sqlsafe($id_ligy);
		$query = "SELECT p.id, p.image, p.nazev FROM ligy l JOIN pocasi p ON l.id_pocasi_dnes=p.id WHERE l.id='$id_ligy'";
	}
	else{
		$id_ligy = sqlsafe($id_ligy);
		$query = "SELECT p.id, p.image, p.nazev FROM pocasi p WHERE p.id='$id_pocasi'";
	}
	$res = $db->Query( $query );
	
	$row = $db->GetFetchAssoc( $res );
	$filename = $row['image'];
	$alt = $row['nazev'];
	$id_pocasi = $row['id'];

	$pocasi .= '<div class="pocasi">';
	if(!empty($id_ligy)){
		$pocasi .= '<img src="./skins/default/hlaseni/ramecek.png" width="48" height="48"
			alt="" class="ramecek"/>';
	}
	$pocasi .= '<img src="./skins/default/pocasi/'.$filename.'" width="34" height="20"
		 alt="'.$alt.'" title="'.$alt.'" class="ikona"/>';
	$pocasi .= '<div class="pocasi_text">
		<span class="zelena">';
	if(!empty($id_ligy)){
		$pocasi .= 'Hlášení z ústředí ohledně počasí na dnešní den: ';
	}
	else{
		$pocasi .= $alt;
	}
	$pocasi .= '</span><br />
		'.$TEXT['pocasi_'.$id_pocasi]['popis'].'<br />
		<span class="cervena">Efekt:</span> - '.$TEXT['pocasi_'.$id_pocasi]['efekt'].'
		</div>
		</div>';
	
	return $pocasi;
}

function VratObrazekBoj( $nazev ){
	global $TEXT;
	
	switch($nazev){
		case $TEXT["uspesny_utok"]:
			$ob = "ob_uspesny_utok.jpg";
			break;
		case $TEXT["neuspesny_utok"]:
			$ob = "ob_neuspesny_utok.jpg";
			break;
		case $TEXT["uspesny_presun"]:
			$ob = "ob_presun.jpg";
			break;
		case $TEXT["uspesna_obrana"]:
			$ob = "ob_ubraneny.jpg";
			break;
		case $TEXT["neuspesna_obrana"]:
			$ob = "ob_ztrata.jpg";
			break;
		default:
			$ob = "ob_default.png";
	}
    
    return $ob;
}

function VratObrazekAkce( $nazev ){
	global $TEXT;
	
	switch($nazev){
		case $TEXT["letecka_akce_stihaci_hlidka"]:
			$ob = "ob_stihaci_hlidka.jpg";
			break;
		case $TEXT["letecka_akce_takticke_bombardovani"]:
			$ob = "ob_takticke_bombardovani.jpg";
			break;
		case $TEXT["letecka_akce_cilene_bombardovani"]:
			$ob = "ob_cilene_bombardovani.jpg";
			break;
		case $TEXT["letecka_akce_nalet_na_letiste"]:
			$ob = "ob_nalet_na_letiste.jpg";
			break;
		case $TEXT["letecka_akce_nalet_na_komunikace"]:
			$ob = "ob_nalet_na_infra.jpg";
			break;
		case $TEXT["letecka_akce_bombardovani"]:
			$ob = "ob_strateg_bombardovani.jpg";
			break;
		case $TEXT["letecka_akce_blizka_podpora"]:
			$ob = "ob_blizka_let_pod.jpg";
			break;
		case $TEXT["letecka_akce_nalet"]:
			$ob = "ob_stremhlavy_nalet.jpg";
			break;
		case $TEXT["podpora_vysadek"]:
			$ob = "ob_vysadek.jpg";
			break;
		case $TEXT["podpora_ocernujici_kampan"]:
			$ob = "ob_ocernovacka.jpg";
			break;
		case $TEXT["podpora_propaganda"]:
			$ob = "ob_propaganda.jpg";
			break;
		case $TEXT["podpora_zasobovani"]:
			$ob = "ob_zasobovani.jpg";
			break;
		case $TEXT["podpora_pruzkum"]:
			$ob = "ob_pruzkum.jpg";
			break;
		case $TEXT["podpora_podminovani"]:
			$ob = "ob_podminovani.jpg";
			break;
		case $TEXT["podpora_maskovani"]:
			$ob = "ob_maskovani.jpg";
			break;
		case $TEXT["podpora_utok_sniperu"]:
			$ob = "ob_sniperi.jpg";
			break;
		case $TEXT["podpora_sabotaz"]:
			$ob = "ob_sabotaz.jpg";
			break;
		case $TEXT["podpora_infiltrace"]:
			$ob = "ob_infiltrace.jpg";
			break;
		default:
			$ob = "ob_default.png";
	}
    
    return $ob;
}

?>