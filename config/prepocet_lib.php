<?php
/**
 * soubor s funkcemi vyuzivanymi behem prepoctu
 */

	require_once($DIR_CONFIG . "prepocet.php");
	require_once($DIR_CONFIG . "lib.php");

class LigaPrepocet{
	
	public $id_ligy;
	public $typ_ligy;
	private $db;
	
	private $generalove = array();
	private $hraci = array();
	
	private $zeme = array();
	
	var $hlaseni = array();
	var $utoky = array();
	var $presuny = array();
	var $stavby = array();
	
	function LigaPrepocet( $_db, $_id_ligy ){
		$this->id_ligy = $_id_ligy;
		$this->db = $_db;
		
		//nacteni infa o lize
		$query = "SELECT typ FROM ligy WHERE id=$_id_ligy";
		$res = $this->db->Query( $query );
		if( $row = $this->db->GetFetchAssoc( $res ) ){
			$this->typ_ligy = $row['typ'];
		}
		
		$this->LoadFromDb();
		
	}

	private function LoadFromDb(){		
		//nacteni dat generalu
		$query = "SELECT * FROM generalove ORDER BY id";
		$res = $this->db->Query( $query );
		while( $row = $this->db->GetFetchAssoc( $res ) ){
			$this->generalove[$row['id']] = $row;
		}
		
		//nacteni hracu
		$query = "SELECT * FROM in_game_hrac AS igh JOIN users_sys AS us ON igh.id_hrac=us.id
				WHERE igh.id_liga=".$this->id_ligy;
		$res = $this->db->Query( $query );
		while( $row = $this->db->GetFetchAssoc( $res ) ){
			//pridani statistik na pocitani utoku
			$row['pocet_utoku'] = 0;
			$row['pocet_uspesnych_utoku'] = 0;
			$row['pocet_utoku_neutralka'] = 0;
			$row['pocet_uspesnych_utoku_neutralka'] = 0;
			$row['pocet_obran'] = 0;
			$row['pocet_uspesnych_obran'] = 0;
			$row['zmeneno'] = 0;
			$this->hraci[$row['id_hrac']] = $row;
		}

		//stav zemi
		$query = "SELECT 0 as zmeneno, igz.*, z.* FROM in_game_zeme AS igz JOIN zeme AS z ON igz.id_zeme=z.id
				WHERE igz.id_ligy=".$this->id_ligy." ORDER BY z.id";
		$res = $this->db->Query( $query );
		while( $row = $this->db->GetFetchAssoc( $res ) ){
			$this->zeme[$row['id_zeme']] = $row;
		}
		
		//nacteni utoku
		$query = "SELECT * FROM in_game_utoky WHERE id_ligy=".$this->id_ligy." ORDER BY typ DESC, random";
		$res = $this->db->Query( $query );
		while( $row = $this->db->GetFetchAssoc( $res ) ){
			$this->utoky[$row['id']] = $row;
		}
		
		//nacteni staveb
		$query = "SELECT * FROM in_game_stavby WHERE id_ligy=".$this->id_ligy;
		$res = $this->db->Query( $query );
		while( $row = $this->db->GetFetchAssoc( $res ) ){
			$this->stavby[$row['id_zeme']][$row['id_stavby']] = 1;
		}
	}
	
	public function Save2DB(){
		// zeme
		$this->db->DbQuery( "START TRANSACTION;" );
		foreach( $this->zeme as $zem ){
			if( $zem['zmeneno'] == 0 ){
				continue;
			}
			
			$query = "UPDATE in_game_zeme SET ";
				if(isset($zem['id_vlastnik'])){
					$query .= "id_vlastnik = ".$zem['id_vlastnik'].",";
				}			
			
			$query .= "	pechota = ".$zem['pechota'].",
						tanky = ".$zem['tanky'].",
						pechota_odeslano = 0,
						tanky_odeslano = 0, 
						infrastruktura_now = ".$zem['infrastruktura_now']."
						WHERE id_ligy = ".$this->id_ligy." AND id_zeme=".$zem['id_zeme']." ;";
			
			$this->db->DbQuery( $query );
		}
		$this->db->DbQuery( "COMMIT;" );
		
		//hraci
		$this->db->DbQuery( "START TRANSACTION;" );
		foreach( $this->hraci as $hrac ){
			if( $hrac['zmeneno'] == 0 ){
				continue;
			}		
			
			$query = "	UPDATE users_stat SET 
						pocet_utoku = pocet_utoku + ".$hrac['pocet_utoku'].",
						pocet_uspesnych_utoku = pocet_uspesnych_utoku + ".$hrac['pocet_uspesnych_utoku'].",
						pocet_utoku_neutralka = pocet_utoku_neutralka + ".$hrac['pocet_utoku_neutralka'].",
						pocet_uspesnych_utoku_neutralka = pocet_uspesnych_utoku_neutralka + ".$hrac['pocet_uspesnych_utoku_neutralka'].",
						pocet_obran = pocet_obran + ".$hrac['pocet_obran'].",
						pocet_uspesnych_obran = pocet_uspesnych_obran + ".$hrac['pocet_uspesnych_obran']."
						WHERE id_user=".$hrac['id']." ;";
			
			$this->db->DbQuery( $query );
		}
		$this->db->DbQuery( "COMMIT;" );
		
		return ' Saved.';
	}
	
	private function MuzePresouvatJednotky( $vlastnik, $id_zeme_kam ){
		
		$zem = $this->zeme[$id_zeme_kam]['id_vlastnik'];
		
		//v tymu muze presouvat spoluhracum
		if( $this->typ_ligy == 'team'){
			//spoluhraci jednotky poslat muzu
			return $this->VratGeneralaHrace($vlastnik) == $this->VratGeneralaHrace($zem);
		}
		else{
			//jednotky se muzou presouvat jen mezi dotycnym hracem
			return $vlastnik == $zem;	
		}
		
	}
	
	private function AddPresun( $presun, $pechota, $tanky, $add_hlaseni = false, $hlaseni_text = '' ){
		$novy = array();
		
		$novy['id_zeme_kam'] = $presun['id_zeme_kam'];
		$novy['id_zeme_odkud'] = $presun['id_zeme_odkud'];
		$novy['pechota'] = $pechota;
		$novy['tanky'] = $tanky;
		$novy['id_vlastnik'] = $presun['id_vlastnik'];
		$novy['hlaseni'] = $add_hlaseni;
		$novy['hlaseni_text'] = $hlaseni_text;
		//var_dump($novy);
		$this->presuny[] = $novy;
	}

	public function VyhodnotUtoky(){
	
		foreach ( $this->utoky as $zem ){
			//echo "utok:" . $zem['id']."cil:" . $zem['id_zeme_kam']."<br>";
			//vyhra
			if ($zem['typ'] == 'vyhra'){
				continue;
				//resi se jinde
			}
			
			//nastaveni ulozeni zemi do DB
			$this->zeme[$zem['id_zeme_kam']]['zmeneno'] = 1;
			$this->zeme[$zem['id_zeme_odkud']]['zmeneno'] = 1;
		
			//presun
			if ($zem['typ'] == 'presun'){
				//muze presouvat
				if ($this->MuzePresouvatJednotky($zem['id_vlastnik'],$zem['id_zeme_kam'])){
					$this->AddPresun( $zem, $zem['pechota'], $zem['tanky'], true, 'uspesny_presun' );	
				}
				//nemuze presouvat
				else{
					//jednotky se vrati zpet do vychozi zeme
					$this->AddPresun( $zem, $zem['pechota'], $zem['tanky'], true, 'nemozny_presun' );
				}
			
			}
			//utok
			else{
				//jsou obe zeme jednoho hrace?
				echo 'v:'.$zem['id_vlastnik']."_id_zeme:".$zem['id_zeme_kam']."majitel:".$this->MajitelZeme($zem['id_zeme_kam'])."<br/>";
				if ($zem['id_vlastnik'] == $this->MajitelZeme($zem['id_zeme_kam'])){
					$this->AddPresun( $zem, $zem['pechota'], $zem['tanky'], true, 'utok_na_svou_zem' );
				}
				//zem je jineho hrace a provedu utok
				else{
					$obrana = $this->VypocitejObranuZeme($zem['id_zeme_kam']);
				echo 'obrana:'.$obrana.',utok:'.$zem['sila'].'<br/>';
					//byl utok uspesny?
					if ($zem['sila'] > $obrana){
						$this->UspesnyUtok( $zem, $obrana );
					}
					//neuspesny
					else{
						$this->NeuspesnyUtok( $zem, $obrana );
					}
				}
			}
	
		}//foreach
		
		
		//zpracovani presunu a neuspesnych utoku
		foreach( $this->presuny as $zem ){
			
			if( $zem['hlaseni_text'] == 'uspesny_presun' ){
				if ($this->MuzePresouvatJednotky($zem['id_vlastnik'],$zem['id_zeme_kam'])){
					add_to_hlaseni($zem['id_vlastnik'], "uspesny_presun", $zem);
					$this->zeme[$zem['id_zeme_kam']]['pechota'] += $zem['pechota'];
					$this->zeme[$zem['id_zeme_kam']]['tanky'] += $zem['tanky'];
				}
			}
			else{
				if ($this->MuzePresouvatJednotky($zem['id_vlastnik'],$zem['id_zeme_odkud'])){
					if( $zem['hlaseni'] ){
						add_to_hlaseni($zem['id_vlastnik'], $zem['hlaseni_text'], $zem);
					}
					$this->zeme[$zem['id_zeme_odkud']]['pechota'] += $zem['pechota'];
					$this->zeme[$zem['id_zeme_odkud']]['tanky'] += $zem['tanky'];
				}
				else{
					// jednotky zmizi
					if( $zem['hlaseni'] ){
						add_to_hlaseni($zem['id_vlastnik'], $zem['hlaseni_text'], $zem);
					}
				}
			}
		}//foreach
		return "Vyhodnoceno";
	}
		
	private function MajitelZeme( $id_zeme ){
		//echo $id_zeme ."__" .$this->zeme[$id_zeme]['id_vlastnik']."<br/>";
		return $this->zeme[$id_zeme]['id_vlastnik'];
	}
	
	private function VratGeneralaHrace( $id_user ){
		if(!isset($id_user)){
			//pokud neni zadany hrac -> neutralka -> general 1
			return 1;
		}
		return $this->generalove[$this->hraci[$id_user]['id_general']]['id'];
	}
	
	private function VratAtributyJednotek( $jednotky, $id_general ){
		$pole = array();
		switch ($jednotky)
		{
			case 'tanky':
				$pole["utok"] = $this->generalove[$id_general]['utok_tanky'];
				$pole["obrana"] = $this->generalove[$id_general]['obrana_tanky'];	
				break;
			case 'pechota':
				$pole["utok"] = $this->generalove[$id_general]['utok_pechota'];
				$pole["obrana"] = $this->generalove[$id_general]['obrana_pechota'];	
				break;
		}	

		return $pole;
	}
	
	private function MaHracStavbuVZemi( $id_stavby, $id_zeme ){
		if( isset($this->stavby[$id_zeme][$id_stavby])&&$this->stavby[$id_zeme][$id_stavby]==1){
			return true;
		}
		else{
			return false;
		}	
	}
	
	private function VratPodporuObranySousednichZemi($id_zeme, $id_majitel, $id_general){
		global $CONST;
		//dobyvana zem je neutralka
		if(!isset($id_majitel)){
			return 0;
		}
		
		$bonus = 0;
		$atrb_tanky = $this->VratAtributyJednotek("tanky", $id_general);
		$atrb_pechota = $this->VratAtributyJednotek("pechota", $id_general);
		
		$query = "SELECT pechota, tanky
					FROM in_game_zeme
					WHERE  id_vlastnik=$id_majitel and id_ligy=".$this->id_ligy." and id_zeme IN 
					(
					SELECT zeme2 FROM sousede WHERE zeme1=$id_zeme
					)";
		$res = $this->db->Query( $query );
		while ($row = $this->db->GetFetchAssoc( $res )){
			$bonus += $row['pechota'] * $atrb_pechota['obrana'];
			$bonus += $row['tanky'] * $atrb_tanky['obrana'];
			
		}		
		
		return round ($bonus * $CONST['PODPORA_SOUSEDNICH_ZEMI_DO_OBRANY']);
	}

	private function VypocitejObranuZeme( $id_zeme ){
		global $CONST;
			
		$id_majitel = $this->zeme[$id_zeme]['id_vlastnik'];
		$bonus_obrana = $this->zeme[$id_zeme]['bonus_obrana'];
		$pechota = $this->zeme[$id_zeme]['pechota'];
		$tanky = $this->zeme[$id_zeme]['tanky'];
		$id_general = $this->VratGeneralaHrace($id_majitel);
		var_dump($id_general);
		if ($id_general== null){
			//NEUTRALKY
			$id_general = 1;
		}
		$atrb_tanky = $this->VratAtributyJednotek("tanky", $id_general);
		$atrb_pechota = $this->VratAtributyJednotek("pechota", $id_general);
		
		$aditivni_bonus = 0;
		$vliv = 1;
		$obrana = 0;
		
		//pechota
		$obrana += $pechota * $atrb_pechota['obrana'];
		
		//tanky
		$obrana += $tanky * $atrb_tanky['obrana'];
		
		//omezeni vlivem zapornych zdroju
		//TODO - NEOBJEKTOVA FUNKCE
		$at = OmezeniZeZapornychZdroju( $id_zeme, $this->id_ligy, $id_majitel );
	
		$z_p = floor($atrb_pechota['obrana'] * $pechota * ( 1 - $at['snizeni_pechota']));
		$z_t = floor($atrb_tanky['obrana'] * $tanky * ( 1 - $at['snizeni_tanky']));
	
		if(($z_p + $z_t) > 0){
		$obrana -= $z_p + $z_t;
		}
		
		//efekt stavby minova pole
		if ($this->MaHracStavbuVZemi(13,$id_zeme)){
			$aditivni_bonus += $CONST["STAVBY_MINOVE_POLE_POMOC_DO_OBRANY"];
		}
		
		//efekt stavby bunkr
		if ($this->MaHracStavbuVZemi(11,$id_zeme)){
			$aditivni_bonus += $CONST["STAVBY_BUNKR_POMOC_DO_OBRANY"];
		}
			
		//vyhodnoceni vlivu podpora - pruzkum
		$query = "SELECT count(*) FROM in_game_vlivy_podpora WHERE 
				id_podpora=6 and id_zeme=$id_zeme and id_ligy=$this->id_ligy and param1='vcera'";
		$res1 = $this->db->Query( $query );
		$pruzkum = $this->db->GetFetchRow( $res1 );
	
		if($pruzkum[0]>0){
			$vliv += $CONST["PODPORA_PRUZKUM_BONUS"];
		}
		
		// bonus ze sousednich zemi
		$sousedni_zeme = $this->VratPodporuObranySousednichZemi($id_zeme,$id_majitel,$id_general);
		
		$celkem = round ($obrana * $vliv + $aditivni_bonus + $sousedni_zeme);
		$koncove = round( $celkem * (1 + $bonus_obrana / 100));
	
		//TODO Naschval se vola "neaktualni" funkce - obkliceni chceme to pred prepoctem!
		if(JeZemObklicena( $id_zeme, $this->id_ligy, $id_majitel)){
			$koncove = round( $koncove * $CONST["OBKLICENI_POSTIH_OBRANA"]);
		}	
		
		return $koncove;
	}
	
	private function UspesnyUtok( $zem, $obrana){
		global $CONST;
		
		$id_ligy = $this->id_ligy;
		$db = $this->db;
		$id_zeme_odkud = $zem['id_zeme_odkud'];
		$id_zeme_kam = $zem['id_zeme_kam'];
		$id_hrac = $zem['id_vlastnik'];
		$pechota = $zem['pechota'];
		$tanky = $zem['tanky'];
		
		//ZTRATY v utoku 
		$ztraty_p = mt_rand($CONST["MIN_ZTRATY_PECHOTA_USPESNY_UTOK"], $CONST["MAX_ZTRATY_PECHOTA_USPESNY_UTOK"]);
		$ztraty_t = mt_rand($CONST["MIN_ZTRATY_TANKY_USPESNY_UTOK"], $CONST["MAX_ZTRATY_TANKY_USPESNY_UTOK"] );
		
		//efekt stavby zemljanka
		if ($this->MaHracStavbuVZemi(14,$id_zeme_odkud)){
			$CONST["STAVBY_ZEMLJANKA_OBRANA_BONUS"];
		}
		
		//efekt stavby minove pole
		if ($this->MaHracStavbuVZemi(13,$id_zeme_kam)){
			$ztraty_p *= $CONST["STAVBY_MINOVE_POLE_ZTRATY_PECHOTA"];
			$ztraty_t *= $CONST["STAVBY_MINOVE_POLE_ZTRATY_TANKY"];
		}
		
		//efekt stavby bunkr
		if ($this->MaHracStavbuVZemi(11,$id_zeme_kam)){
			$ztraty_p *= $CONST["STAVBY_BUNKR_ZTRATY_PECHOTA"];
			$ztraty_t *= $CONST["STAVBY_BUNKR_ZTRATY_TANKY"];
		}
		
		
		$zem['ztraty_pechota'] = round($pechota * $ztraty_p / 100);
		if ($zem['ztraty_pechota'] > $pechota){
			$zem['ztraty_pechota'] = $pechota;
		}
		$zem['ztraty_tanky'] = round($tanky * $ztraty_t / 100);
		if ($zem['ztraty_tanky'] > $tanky){
			$zem['ztraty_tanky'] = $tanky;
		}
		$zem['obrana'] = $obrana;
		
		$pechota -= $zem['ztraty_pechota'];
		$tanky -= $zem['ztraty_tanky'];
		
		add_to_hlaseni($id_hrac, "uspesny_utok", $zem);
		
		// obrance prijde o vsechny jednotky
		$zem['ztraty_pechota'] = $this->zeme[$id_zeme_kam]['pechota'];
		$zem['ztraty_tanky'] = $this->zeme[$id_zeme_kam]['tanky'];
		
		$id_obrance = $this->MajitelZeme($id_zeme_kam);
		add_to_hlaseni($id_obrance, "neuspesna_obrana", $zem);	
		
		//statistiky
		$this->StatUtok($id_hrac, "uspesny_utok", $id_obrance);
		
		//zeme do ktere se utocilo
		$this->zeme[$id_zeme_kam]['pechota'] = $pechota;
		$this->zeme[$id_zeme_kam]['tanky'] = $tanky;
		$this->zeme[$id_zeme_kam]['id_vlastnik'] = $id_hrac;
		$this->zeme[$id_zeme_kam]['infrastruktura_now'] = round($this->zeme[$id_zeme_kam]['infrastruktura_now']/2 );
		
		//vyhodnoceni vlivu podminovani
		$query = "SELECT count(*) FROM in_game_vlivy_podpora WHERE 
				id_podpora=7 and id_zeme=$id_zeme_kam and id_ligy=$id_ligy";
		$res1 = $db->Query( $query );
		$podminovani = $db->GetFetchRow( $res1 );
	
		if($podminovani[0]>0){
			//zbourani staveb
			$query = "DELETE FROM in_game_stavby WHERE 
				id_zeme=$id_zeme_kam and id_ligy=$id_ligy"; 
			$db->DbQuery( $query );
			//zbourani stanic
			$query = "UPDATE in_game_zeme SET vojenska_policie=0, pvo_stanice=0 WHERE  
				id_zeme=$id_zeme_kam and id_ligy=$id_ligy"; 
			$db->DbQuery( $query );
		}
		
		//zmena vlastnika staveb + bourani nepouzitelnych
		DobytiZemeStavby( $id_zeme_kam, $id_ligy, $id_hrac ); 
		
		//zruseni platnych pruchodu
		$query = "delete from in_game_pruchody where 
			(id_zeme_odkud=$id_zeme_kam or id_zeme_kam=$id_zeme_kam) AND id_ligy=$id_ligy";
		$db->DbQuery( $query ); 
	}
	
	private function NeuspesnyUtok( $zem, $obrana ){
		global $CONST;
		
		$id_ligy = $this->id_ligy;
		$db = $this->db;
		$id_zeme_odkud = $zem['id_zeme_odkud'];
		$id_zeme_kam = $zem['id_zeme_kam'];
		$id_hrac = $zem['id_vlastnik'];
		$pechota = $zem['pechota'];
		$tanky = $zem['tanky'];
		
		//ZTRATY v utoku 
		$ztraty_p = mt_rand($CONST["MIN_ZTRATY_PECHOTA_NEUSPESNY_UTOK"], $CONST["MAX_ZTRATY_PECHOTA_NEUSPESNY_UTOK"]);
		$ztraty_t = mt_rand($CONST["MIN_ZTRATY_TANKY_NEUSPESNY_UTOK"], $CONST["MAX_ZTRATY_TANKY_NEUSPESNY_UTOK"]);
		
		//efekt stavby minove pole
		if ($this->MaHracStavbuVZemi(13,$id_zeme_kam)){
			$ztraty_p *= $CONST["STAVBY_MINOVE_POLE_ZTRATY_PECHOTA"];
			$ztraty_t *= $CONST["STAVBY_MINOVE_POLE_ZTRATY_TANKY"];
		}
		
		//efekt stavby bunkr
		if ($this->MaHracStavbuVZemi(11,$id_zeme_kam)){
			$ztraty_p *= $CONST["STAVBY_BUNKR_ZTRATY_PECHOTA"];
			$ztraty_t *= $CONST["STAVBY_BUNKR_ZTRATY_TANKY"];
		}
		
		//efekt sily utoku vs. velikosti obrany
		$podil_sily = $zem['sila'] / $obrana;
		if( $podil_sily < 0.33 ){
			$ztraty_p *= 1.5;
			$ztraty_t *= 1.5;
		}
		else if( $podil_sily > 0.66 ){
			$ztraty_p *= 0.5;
			$ztraty_t *= 0.5;
		}		
		
		$zem['ztraty_pechota'] = round($pechota * $ztraty_p / 100);
		if ($zem['ztraty_pechota'] > $pechota){
			$zem['ztraty_pechota'] = $pechota;
		}
		$zem['ztraty_tanky'] = round($tanky * $ztraty_t / 100);
		if ($zem['ztraty_tanky'] > $tanky){
			$zem['ztraty_tanky'] = $tanky;
		}
		$zem['obrana'] = $obrana;
		
		$pechota -= $zem['ztraty_pechota'];
		$tanky -= $zem['ztraty_tanky'];
		
		add_to_hlaseni($id_hrac, "neuspesny_utok", $zem);
		//vraceni zbytku utocicich jednotek
		$pom = $zem;
		$pom['id_zeme_odkud'] = $id_zeme_odkud;
		$pom['id_zeme_kam'] = $id_zeme_odkud;
		echo "<br>".$pechota .",". $tanky."<br>";
		$this->AddPresun( $pom, $pechota, $tanky );
		
		//ZTRATY V OBRANE
		//nahoda
		$ztraty_p = mt_rand($CONST["MIN_ZTRATY_PECHOTA_OBRANA"],$CONST["MAX_ZTRATY_PECHOTA_OBRANA"]);
		$ztraty_t = mt_rand($CONST["MIN_ZTRATY_TANKY_OBRANA"], $CONST["MAX_ZTRATY_TANKY_OBRANA"]);
		
		//pokud je utok prilis slaby. nejsou ztraty
		if($zem['sila']<30){
			$ztraty_p = 0;
			$ztraty_t = 0;
		}
		
		//vychozi pocty jednotek
		$pechota = $this->zeme[$id_zeme_kam]['pechota'];
		$tanky = $this->zeme[$id_zeme_kam]['tanky'];
		
		//efekt stavby zemljanka
		if ($this->MaHracStavbuVZemi(14,$id_zeme_odkud)){
			$CONST["STAVBY_ZEMLJANKA_OBRANA_BONUS"];
		}
		
		//vypocitani poctu mrtvych s omezenim preteceni
		$zem['ztraty_pechota'] = round($pechota * $ztraty_p / 100);
		if ($zem['ztraty_pechota'] > $pechota){
			$zem['ztraty_pechota'] = $pechota;
		}
		$zem['ztraty_tanky'] = round($tanky * $ztraty_t / 100);
		if ($zem['ztraty_tanky'] > $tanky){
			$zem['ztraty_tanky'] = $tanky;
		}
		
		//odecteni ztrat
		$pechota -= $zem['ztraty_pechota'];
		$tanky -= $zem['ztraty_tanky'];
		
		
		$zem['obrana'] = $obrana;
		
		$id_obrance = $this->MajitelZeme($id_zeme_kam, $id_ligy);
		add_to_hlaseni($id_obrance, "uspesna_obrana", $zem);	
		
		//statistiky
		$this->StatUtok($id_hrac, "neuspesny_utok", $id_obrance);
		
		//zeme do ktere se utocilo
		$this->zeme[$id_zeme_kam]['pechota'] = $pechota;
		$this->zeme[$id_zeme_kam]['tanky'] = $tanky;					
	}
	
	private function StatUtok($id_hrac, $typ, $id_obrance){	
		
		$this->hraci[$id_hrac]['zmeneno'] = 1;
		
		switch($typ){
			case 'uspesny_utok':
				if(!empty($id_obrance)){
					//utok na hrace
					$this->hraci[$id_hrac]['pocet_utoku']++;
					$this->hraci[$id_hrac]['pocet_uspesnych_utoku']++;
					
					//obranci pridame neuspesnou obranu
					$this->hraci[$id_obrance]['pocet_obran']++;
					$this->hraci[$id_obrance]['zmeneno'] = 1;
				}
				else{
					//utok na neutralku
					$this->hraci[$id_hrac]['pocet_utoku_neutralka']++;
					$this->hraci[$id_hrac]['pocet_uspesnych_utoku_neutralka']++;
				}
				break;
			case 'neuspesny_utok':
				if(!empty($id_obrance)){
					//utok na hrace
					$this->hraci[$id_hrac]['pocet_utoku']++;
					//obranci pridame uspesnou obranu
					$this->hraci[$id_obrance]['pocet_obran']++;
					$this->hraci[$id_obrance]['pocet_uspesnych_obran']++;
					$this->hraci[$id_obrance]['zmeneno'] = 1;
				}
				else{
					//utok na neutralku
					$this->hraci[$id_hrac]['pocet_utoku_neutralka']++;
				}
				break;
		}
	}
}

function DobytiZemeStavby( $id_zeme_kam, $id_ligy, $id_hrac ){
	global $db,$CONST;
	
	$query = "SELECT strana FROM in_game_hrac WHERE id_hrac=$id_hrac";
	$res = $db->Query( $query );
	$gen = $db->GetFetchAssoc( $res );
	$strana = $gen['strana'];
	
	$query = "SELECT * FROM in_game_stavby WHERE id_zeme=$id_zeme_kam AND id_ligy=$id_ligy";
	$res = $db->Query( $query );
	
	//asi zbytecne resi se pravdepodobne jinde
	while ($row = $db->GetFetchAssoc( $res )){
			$db->DbQuery("UPDATE in_game_stavby SET id_vlastnik=$id_hrac WHERE 
			id_zeme=$id_zeme_kam AND id_ligy=$id_ligy");
	}
	
	if( $strana == 'us' ){
		$stv_id = "STAVBY_SSSR";
	}
	else{
		$stv_id = "STAVBY_US";
	}
	
	//smazani specialnich staveb druhe strany
	$query = "DELETE FROM in_game_stavby WHERE id_zeme=$id_zeme_kam AND id_ligy=$id_ligy AND
			id_stavby IN (".$CONST[$stv_id].")";

	$res = $db->DbQuery( $query );
	
	
}

/**
 * Dle zadane psti vrati ID pocasi
 *
 * @param unknown_type $pst
 * @param unknown_type $obdobi
 * @return unknown
 */
function VratIdPocasiDlePst($pst, $obdobi){
	global $POCASI, $CONST;
	$sum = 0;

	foreach( $POCASI[$obdobi] as $procenta => $id){
		$sum += $procenta;
		$id_pocasi = $id;
		if( $pst <= $sum ){
			break;
		}
	}
	//sdfasdf
	
	return $id_pocasi;
}

/**
 * vrati prumernou pst, na ktere je soucasne pocasi
 *
 * @param unknown_type $id_pocasi_dnes
 * @param unknown_type $obdobi
 * @return unknown
 */
function VratPstPocasi($id_pocasi_dnes, $obdobi){
	global $POCASI, $CONST;
	
	$pst = 0;
	
	foreach($POCASI[$obdobi] as $rozsah => $id){
		if($id==$id_pocasi_dnes){
			$pst += $rozsah / 2;
			break;
		}
		$pst += $rozsah;
	}
	
	return round($pst);
}

/**
 * vrati zitrejsi pocasi - id
 *
 * @param unknown_type $id_pocasi_dnes
 * @param unknown_type $id_pocasi_zitra
 * @param unknown_type $obdobi
 * @return unknown
 */
function VypocitejZitrejsiPocasi($id_pocasi_dnes, $id_pocasi_zitra, $obdobi){
	global $POCASI, $CONST;
	
	$zmena_smeru = 1;
	
	if( $obdobi == 'léto' ){ $obdobi = 'leto'; }
	
	//pri stejnem pocasi se vybere smer a zvetsi sance na zmenu
	if ($id_pocasi_dnes == $id_pocasi_zitra){
		$pst = 2 * $CONST['pocasi_zmena_smeru'];
		
		if($id_pocasi_dnes == 10){
			$smer = 1;
		}
		else if ($id_pocasi_dnes == 1){
			$smer = -1;
		}
		else{
			$smer = ($id_pocasi_dnes < $id_pocasi_zitra) ? 1 : -1;
		}
	}
	else{
		$pst = $CONST['pocasi_zmena_smeru'];
		$smer = ($id_pocasi_dnes < $id_pocasi_zitra) ? 1 : -1;	
	}
	
	$n = mt_rand(0, 100);
	if( $n < $pst){
		$zmena_smeru = -1;
	}	
	
	$nahoda = mt_rand(0, $CONST['pocasi_zmena_max']);
		
	$old_pst = VratPstPocasi($id_pocasi_dnes, $obdobi); 
	$new_pst = ($smer * $zmena_smeru * $nahoda) + $old_pst;
	
	$return = VratIdPocasiDlePst($new_pst, $obdobi);
	
	return $return;
}

/**
 * Vrati sql dotaz pro UPDATE tabulky ligy, ktery nastavi spravne pocasi dnes a zitra.
 * Vygeneruje nove ID pro zitrejsi pocasi.
 *
 * @param $data - prislusny radek z tabulky ligy
 */
function PocasiSql($data){
	
	$dnes = $data['id_pocasi_zitra'];
	$zitra = VypocitejZitrejsiPocasi($data['id_pocasi_dnes'], $data['id_pocasi_zitra'], $data['rocni_obdobi']);
	
	$sql = " id_pocasi_dnes = $dnes, id_pocasi_zitra = $zitra";
	return $sql;
}

function RestartLigy( $id_ligy){
	global $db, $CONST;
	
	//ziskani typu ligy
	$query = "SELECT typ FROM ligy WHERE id=$id_ligy";
	$res = $db->Query( $query );
	$tp = $db->GetFetchAssoc( $res );	
	if( $tp['typ'] == 'team' ){
		//vymazani starych zprav v tymovem foru
		$query = "DELETE FROM forum WHERE vlakno = '".(30000 + $id_ligy)."'";
		$db->DbQuery( $query );
		$query = "DELETE FROM forum WHERE vlakno = '".(35000 + $id_ligy)."'";
		$db->DbQuery( $query );
	}
	
	//vymazani starych dat
	$query = "DELETE FROM in_game_hlaseni WHERE id_ligy=$id_ligy";
	$db->DbQuery( $query );
	
	$query = "DELETE FROM in_game_letecke_akce WHERE id_ligy=$id_ligy";
	$db->DbQuery( $query );
	
	$query = "DELETE FROM in_game_podpora WHERE id_ligy=$id_ligy";
	$db->DbQuery( $query );
	
	$query = "DELETE FROM in_game_pruchody WHERE id_ligy=$id_ligy";
	$db->DbQuery( $query );
	
	$query = "DELETE FROM in_game_stavby WHERE id_ligy=$id_ligy";
	$db->DbQuery( $query );
	
	$query = "DELETE FROM in_game_utoky WHERE id_ligy=$id_ligy";
	$db->DbQuery( $query );
	
	$query = "DELETE FROM in_game_vlivy_letecke_akce WHERE id_ligy=$id_ligy";
	$db->DbQuery( $query );
	
	$query = "DELETE FROM in_game_vlivy_podpora WHERE id_ligy=$id_ligy";
	$db->DbQuery( $query );
	
	$query = "DELETE FROM in_game_hrac WHERE id_liga=$id_ligy";
	$db->DbQuery( $query );
	
	//reset neutralek
	$query = "DELETE FROM in_game_zeme WHERE id_ligy=$id_ligy";
	$db->DbQuery( $query );
	
	$query = "DELETE FROM in_game_vcera_zeme WHERE id_ligy=$id_ligy";
	$db->DbQuery( $query );
	
	for ($i=1; $i <=150; $i++){
		$query = "insert  into `in_game_zeme`
		(`id_zeme`,`id_ligy`,`id_vlastnik`,`infrastruktura_now`,
		`pechota`,`tanky`,`letadla`,`sila_neutralka`) values 
		($i,$id_ligy,NULL,
		${CONST['NEUTRALKA_INFRASTRUKTURA']},
		${CONST['NEUTRALKA_PECHOTA']},
		${CONST['NEUTRALKA_TANKY']},0, ${CONST['NEUTRALKA_SILA_1_DEN']})";
		$db->DbQuery( $query );
		
		//in game vcera zeme se neinicializuje. provede se automaticky ve funkci
		//kopirovani stavu ligy
	}
	
	//vycisteni starych statistik
	$query = "DELETE FROM in_game_vcera	WHERE id=$id_ligy";
	$db->DbQuery( $query );
	
	//reset ukazatelu ligy
	$query = "UPDATE ligy SET registrace = 'ano', dohrano = 'ne', odehranych_dnu = 1,
			id_pocasi_dnes = 4, id_pocasi_zitra = 4	WHERE id=$id_ligy";
	$db->DbQuery( $query );
} 

/**
 * zapise vyhru vitezi + umisteni vsech ostatnich hracu
 *
 * @param unknown_type $id_hrac
 * @param unknown_type $id_ligy
 * @param unknown_type $odehrano
 */
function ZapisHraciVyhru( $id_hrac, $id_ligy, $odehrano, $typ = NULL){
	global $db;

	$datum = date("Y-m-d H:i:s");
	$strana = VratAtributHrace('strana', $id_hrac);
	$vitezny_team = '';
	
	if(!empty($typ) && $typ=='team'){
		$query = "INSERT INTO users_vyhry (id_user, id_ligy, datum, odehranych_dnu, umisteni, strana, team_vyhra) VALUES
			($id_hrac, $id_ligy, '$datum', $odehrano, 1, '$strana', 'ano');";
			$vitezny_team = $strana;
	}
	else{
		$query = "INSERT INTO users_vyhry (id_user, id_ligy, datum, odehranych_dnu, umisteni, strana) VALUES
			($id_hrac, $id_ligy, '$datum', $odehrano, 1, '$strana');";
	}
	$db->DbQuery( $query );
	
	$hraci = VratPoradiHracu( $id_ligy );
	$poradi = 2;
	foreach( $hraci as $hrac => $data ){
		//hrac co vyhral nema zadne dalsi poradi
		if($hrac == $id_hrac) continue;
		
		if(!empty($typ) && $typ=='team'){
			$vit_strana = 'ne';
			if($data['strana']==$vitezny_team){
				$vit_strana = 'ano';
			}
			$query = "INSERT INTO users_vyhry (id_user, id_ligy, datum, odehranych_dnu, umisteni, strana, team_vyhra) VALUES
			($hrac, $id_ligy, '$datum', $odehrano, $poradi, '${data['strana']}', '$vit_strana');";
		}
		else{
			$query = "INSERT INTO users_vyhry (id_user, id_ligy, datum, odehranych_dnu, umisteni, strana) VALUES
			($hrac, $id_ligy, '$datum', $odehrano, $poradi, '${data['strana']}');";
		}
		
		$db->DbQuery( $query );
		$poradi++;
	}
	
}

function ZpracujPrestiz( $id_hrac ){
	global $db;
	//TODO optimalizovat do jednoho dotazu
	$return = array();
	
	$query = "SELECT 
	us.pocet_utoku - igvs.pocet_utoku as pocet_utoku,
	us.pocet_uspesnych_utoku - igvs.pocet_uspesnych_utoku as pocet_uspesnych_utoku,
	us.pocet_utoku_neutralka - igvs.pocet_utoku_neutralka as pocet_utoku_neutralka,
	us.pocet_uspesnych_utoku_neutralka - igvs.pocet_uspesnych_utoku_neutralka as pocet_uspesnych_utoku_neutralka,
	us.pocet_akci_letectvo_negativni - igvs.pocet_akci_letectvo_negativni as pocet_akci_letectvo_negativni,
	us.pocet_akci_letectvo_uspesny_na_hrace - igvs.pocet_akci_letectvo_uspesny_na_hrace as pocet_akci_letectvo_uspesny_na_hrace,
	us.pocet_akci_letectvo_uspesny - igvs.pocet_akci_letectvo_uspesny as pocet_akci_letectvo_uspesny,
	us.pocet_akci_podpora_negativni - igvs.pocet_akci_podpora_negativni as pocet_akci_podpora_negativni,
	us.pocet_akci_podpora_uspesny - igvs.pocet_akci_podpora_uspesny as pocet_akci_podpora_uspesny,
	us.pocet_akci_podpora_uspesny_na_hrace - igvs.pocet_akci_podpora_uspesny_na_hrace as pocet_akci_podpora_uspesny_na_hrace,
	sys.login 
	FROM users_stat AS us JOIN in_game_vcera_stat AS igvs JOIN users_sys AS sys 
	ON us.id_user=igvs.id_user AND sys.id=us.id_user
	WHERE us.id_user=$id_hrac";
	$res3 = $db->Query( $query );
	
	if ($row = $db->GetFetchAssoc( $res3 )){
		//uspesne utoky na hrace
		$uspesne_utoky = $row['pocet_utoku'];
		
		$uspesne_letectvo = $row['pocet_akci_letectvo_uspesny_na_hrace'];
		$uspesne_podpora = $row['pocet_akci_podpora_uspesny_na_hrace'];
		
		//neuspesne utoky na hrace
		$neuspesne_utoky = $row['pocet_utoku'] - $row['pocet_uspesnych_utoku'];
		//negativni - uspesny = neuspesny na hrace
		$neuspesne_letectvo = $row['pocet_akci_letectvo_negativni'] - $row['pocet_akci_letectvo_uspesny'];
		$neuspesne_podpora = $row['pocet_akci_podpora_negativni'] - $row['pocet_akci_podpora_uspesny'];
		
		//vypocet zmeny prestize
		$zmena = $uspesne_utoky * 10 + $uspesne_letectvo * 3 + $uspesne_podpora - (
				$neuspesne_utoky * 12 + $neuspesne_letectvo * 2 + 0.5 * $neuspesne_podpora );
		$zmena = round($zmena);
		
		$return['id'] = $id_hrac; 
		$return['login'] = $row['login'];
		$return['zmena'] = $zmena;
	
		//ulozeni
		$query = "UPDATE in_game_hrac SET prestiz = prestiz + $zmena WHERE id_hrac=$id_hrac";
		$db->DbQuery( $query );
	}
	return $return;
}

?>