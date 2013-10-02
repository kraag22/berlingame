<?php
	require_once($DIR_CONFIG . "text.php");
	require_once($DIR_CONFIG . "konstanty.php");
	
function add_to_global_hlaseni($id_ligy, $typ, $parametry = null){
	global $db, $TEXT, $CONST;
	
	$cas = 0;
	//inicializace dat
	$db_nazev_sektoru = "";
	$db_udalost = "";
	$db_uspesnost = "";
	$db_utocnik = "";
	$db_efekt = "";
	$db_ztraty_tanky = 0;
	$db_ztraty_pechota = 0;
	
	$nadpis = "";
	$obsah = "";
	
	
	switch ($typ)
	{
		case 'odlog_pro_necinnost':
			$query = "SELECT login FROM users_sys WHERE id='".$parametry['id_hrac']."'";
			$res5 = $db->Query( $query );
			$login = $db->GetFetchAssoc( $res5 ); 
			
			$nadpis = "Porážka velitele";
			$obsah .= "Velitel <span class=\"zvyrazneni\">".$login['login']."</span> byl pro nečinnost zbaven velení. Jeho neaktivita přesáhla ".$CONST['dni_pro_odlognuti']." dny.";
			break;
		case 'porazeny_velitel':
			$query = "SELECT login FROM users_sys WHERE id='".$parametry['id_hrac']."'";
			$res5 = $db->Query( $query );
			$login = $db->GetFetchAssoc( $res5 ); 
			
			$nadpis = "Porážka velitele";
			$obsah .= "Velitel <span class=\"zvyrazneni\">".$login['login']."</span> byl poražen. Protivníci obsadili všechny jeho sektory.";
			break;
		case 'liga_trening_dohrana':
			$nadpis .= "Konec hry";
			$obsah .= "Tréninkový scénář skončil. Délka hraní je omezena na "
			. $CONST['LIGA_KONEC_TRENINGU'] . " dnů.";
			break;
		case 'neuspesny_utok_na_berlin':
			$query = "SELECT login FROM users_sys WHERE id='".$parametry['id_hrac']."'";
			$res5 = $db->Query( $query );
			$login = $db->GetFetchAssoc( $res5 ); 
			
			$nadpis .= "Pokus o konec hry";
			$obsah .= "Veliteli <span class=\"zvyrazneni\">".$login['login']."</span> se nepodařilo obsadit Berlín. Výsadková operace skončila neúspěchem.";
			break;
		case 'liga_team_dohrana':
			$query = "SELECT us.login, igh.strana FROM users_sys AS us JOIN in_game_hrac AS igh ON us.id=igh.id_hrac 
				WHERE us.id='".$parametry['id_hrac']."'";
			$res5 = $db->Query( $query );
			$lg = $db->GetFetchAssoc( $res5 ); 
			
			$nadpis .= "Scénář dohraný";
			$obsah .= "<span style=\"color:limeGreen\">Velké vítězství</span><br />
Dnešní den se navždy zapíše do dějin, dnes v brzkých ranních 
hodinách po těžkých bojích v ulicích Berlína se jednotkám 
velitele <span style=\"color:gold;\">".$lg['login']."</span> podařilo úspěšně dosáhnout 
klíčových bodů města.
Díky hrdinskému odhodlání a zejména pak vydatné podpoře 
jeho bratrů ve zbrani se tak tímto končí druhá světová válka v Evropě.";
			break;
		case 'prestiz':
			$nadpis .= "Změny prestiže";
			$obsah .= "Po důkladném vyhodnocení dnešních vojenských aktivit
			na bojišti přehodnotil generální štáb pohled na některé
			velitele.";
			
			$obsah .= "<ul>";
			foreach($parametry as $id => $data){
				if( $data['zmena']!=0 ){
					$obsah .= "<li>";
					$obsah .= $data['login']." <strong>";
					if ($data['zmena']>0){ $obsah .= "+".$data['zmena'];}else{ $obsah .= $data['zmena'];}
					$obsah .= "</strong></li>";
				}
			}
			$obsah .= "</ul>";
			
			break;
		case 'snizena_LS':
			$query = "SELECT login FROM users_sys WHERE id='".$parametry['id_hrac']."'";
			$res5 = $db->Query( $query );
			$login = $db->GetFetchAssoc( $res5 ); 
			
			$nadpis .= "Nedostatek letišť";
			$obsah .= "Velitel ".$login['login']." přišel o některá svá letiště. Část jeho letectva
				byla zničena.";
			break;
			
	}
	
	//vkladani dat do DB
	$query = "INSERT INTO `in_game_hlaseni`
				(`id_hrac`,`id_ligy`,`skupina`,`obsah`,`cas`,`nadpis`,`nazev_sektoru`,
				`udalost`,`uspesnost`,`utocnik`,`efekt`,`ztraty_tanky`,`ztraty_pechota`) 
				VALUES 
				(1,
				 '$id_ligy',
				 'globalni',
				 '$obsah',
				 '$cas',
				 '$nadpis',
				 '$db_nazev_sektoru',
				'$db_udalost',
				'$db_uspesnost',
				'$db_utocnik',
				'$db_efekt',
				'$db_ztraty_tanky',
				'$db_ztraty_pechota');
			";
	$db->DbQuery( $query );
	
	
}	
	
/**
 * funkce prida hraci hlaseni
 *
 * @param  $id_user - hrac, kteremu se hlaseni prida
 * @param  $typ - typ hlaseni
 * @param  $parametry - parametry nutne k vytvoreni hlaseni
 */	
function add_to_hlaseni($id_user, $typ, $parametry = null){
	global $db, $TEXT, $CONST;
	//pokud je uzivatel neutralni sektor, hlaseni se nevypisuje
	if ($id_user== null || $id_user==0){
		return;
	}
	$id_ligy = JeUzivatelVLize( $id_user );
	if(!$id_ligy){
		//hrac neni v lize prihlasen. Nevytvarime hlaseni
		return;
	}
	$cas = 0;
	
	//inicializace dat
	$db_nazev_sektoru = "";
	$db_udalost = "";
	$db_uspesnost = "";
	$db_utocnik = "";
	$db_efekt = "";
	$db_ztraty_tanky = 0;
	$db_ztraty_pechota = 0;
	
	$nadpis = "";
	$obsah = "";
	switch ($typ)
	{
	case 'uspesny_presun':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme_odkud']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			
			$nadpis .= $row['nazev'] . " - úspěšný přesun";
			$obsah .= "Velitel sektoru " . $row['nazev'] . " vás informuje, že jednotky pod jeho velením se úspěšně přesunuly do sektoru: ";
			
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme_kam']."'";
			$res = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res );
			
			$query = "SELECT login FROM users_sys WHERE id='".$parametry['id_vlastnik']."'";
			$res = $db->Query( $query );
			$row3 = $db->GetFetchRow( $res );
			
			$obsah .= $row2['nazev'] . "<br />";

			$skupina = "boj";
			$db_nazev_sektoru = $row2['nazev'];
			$db_udalost = $TEXT["uspesny_presun"];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $row3[0];			
			break;
		case 'nemozny_presun':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme_odkud']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			
			$nadpis .= $row['nazev'] . " - Neúspěšný přesun";
			$obsah .= "Velitel sektoru " . $row['nazev'] . " vás informuje, že jednotky pod jeho velením se nebyly schopny přesunout do sektoru: ";
			
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme_kam']."'";
			$res = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res );
			
			$obsah .= $row2['nazev'] . "<br />";
			
			$query = "SELECT login FROM users_sys WHERE id='".$parametry['id_vlastnik']."'";
			$res = $db->Query( $query );
			$row3 = $db->GetFetchRow( $res );

			$skupina = "boj";
			$db_nazev_sektoru = $row2['nazev'];
			$db_udalost = $TEXT["nemozny_presun"];
			$db_uspesnost = $TEXT["neuspech"];
			$db_utocnik = $row3[0];
			break;
		case 'utok_na_svou_zem':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme_odkud']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			
			$query = "SELECT login FROM users_sys WHERE id='".$parametry['id_vlastnik']."'";
			$res = $db->Query( $query );
			$row3 = $db->GetFetchRow( $res );
			
			$nadpis .= $row['nazev'] . " - Neplatný útok";
			//$obsah .= "Velitel sektoru " . $row['nazev'] . " vás informuje, že jednotky pod jeho velením nebyly schopny dobýt sektor: ";
			$obsah .= "Veliteli, naše jednotky díky chybě v komunikaci omylem zaútočily na sektor pod naší kontrolou. Díky rychlé reakci našich důstojníků byl omyl včas rozpoznán a nedošlo tak ke zbytečným ztrátám na životech.";

			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme_kam']."'";
			$res = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res );			
			
			$skupina = "boj";
			$db_nazev_sektoru = $row2['nazev'];
			$db_udalost = $TEXT["utok_na_svou_zem"];
			$db_uspesnost = $TEXT["neuspech"];
			$db_utocnik = $row3[0];
			break;
		case 'neuspesny_utok':		
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme_odkud']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			
			$query = "SELECT login FROM users_sys WHERE id='".$parametry['id_vlastnik']."'";
			$res = $db->Query( $query );
			$row3 = $db->GetFetchRow( $res );
			
			$nadpis .= $row['nazev'] . " - Neúspěšný útok";
			//$obsah .= "Velitel sektoru " . $row['nazev'] . " vás informuje, že jednotky pod jeho velením nebyly schopny dobýt sektor: ";
			$obsah .= "Pane, naše jednotky pronikly hlavní obranou línií nepřátelského sektoru, bohužel však neměly dost sil k překonání opěrných bodů nepřítele a ten své výhody dokázal využít. V následném protiútoku se mu podařilo zničit jádro našeho útoku. Útok tak již neměl naději na úspěch.";

			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme_kam']."'";
			$res = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res );
			
			//$obsah .= $row2['nazev'] . "<br />";
			
			//$obsah .= "Do útoku se zapojilo"; 
			//$obsah .= $parametry['pechota'] ." čet pěchoty a"; 
			//$obsah .= $parametry['tanky'] ." tanků. V boji padlo";
			//$obsah .= $parametry['ztraty_pechota'] ." čet a bylo zničeno"; 
			//$obsah .= $parametry['ztraty_tanky'] ." tanků.";
	
			$skupina = "boj";
			$db_nazev_sektoru = $row2['nazev'];
			$db_udalost = $TEXT["neuspesny_utok"];
			$db_uspesnost = $TEXT["neuspech"];
			$db_utocnik = $row3[0];
			$db_ztraty_tanky = $parametry['ztraty_tanky'];
			$db_ztraty_pechota = $parametry['ztraty_pechota'];
			break;
		case 'uspesny_utok':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme_odkud']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			
			$query = "SELECT login FROM users_sys WHERE id='".$parametry['id_vlastnik']."'";
			$res = $db->Query( $query );
			$row3 = $db->GetFetchRow( $res );
			
			$nadpis .= $row['nazev'] . " - Vítězství";
			//$obsah .= "Velitel sektoru " . $row['nazev'] . " vás informuje, že jednotky pod jeho velením prorazily obranné postavení nepřítele v sektoru: ";
			$obsah .= "Pane naše jednotky úspěšně pronikly hlavní obrannou línií nepřítele a následně si probojovali cestu ke klíčovým bodům sektoru. Naše odhodlání a rychlost postupu nepřítele zaskočila a ten začal ve zmatku opouštět své pozice. Vítězství je naše.";
			$db_efekt .= "Efekt: Sektor je pod naší kontrolou";
					
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme_kam']."'";
			$res = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res );
			
			//$obsah .= $row2['nazev'] . "<br />";
			
			//$obsah .= "Do útoku se zapojilo"; 
			//$obsah .= $parametry['pechota'] ." čet pěchoty a"; 
			//$obsah .= $parametry['tanky'] ." tanků. V boji padlo";
			//$obsah .= $parametry['ztraty_pechota'] ." čet a bylo zničeno"; 
			//$obsah .= $parametry['ztraty_tanky'] ." tanků.";
	
			$skupina = "boj";
			$db_nazev_sektoru = $row2['nazev'];
			$db_udalost = $TEXT["uspesny_utok"];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $row3[0];
			$db_ztraty_tanky = $parametry['ztraty_tanky'];
			$db_ztraty_pechota = $parametry['ztraty_pechota'];
			break;
		case 'neuspesna_obrana':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme_kam']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );

			$query = "SELECT login FROM users_sys WHERE id='". MajitelZeme($parametry['id_zeme_odkud'],$parametry['id_ligy'])."'";
			$res = $db->Query( $query );
			$row3 = $db->GetFetchRow( $res );
			
			$nadpis .= $row['nazev'] . " - Porážka";
			//$obsah .= "Veliteli sektoru " . $row['nazev'] . " se podařilo ustoupit a předat zprávu o útoku nepřátelských jednotek z : ";
			
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme_odkud']."'";
			$res = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res );
			
			//$obsah .= $row2['nazev'] . "<br />";
			
			//$obsah .= " Naše obranné postavení bylo proraženo a sektor již není pod naší kontrolou. Sektor byl bráněn silou ". $parametry['obrana'];
			$obsah .= "Pane, náš sektor se stal v noci na dnešek cílem soustředěné dělostřelecké palby s následným masivním útokem protivníkových sil. Přeze všechnu naší snahu se nám nepodařilo obranou linii udržet. Přeživší ustoupili do předem připravených pozic v okolních sektorech.";
			$db_efekt .= "Efekt: Ztráta sektoru";
	
			$skupina = "boj";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["neuspesna_obrana"];
			$db_uspesnost = $TEXT["neuspech"];
			$db_utocnik = $row3[0];
			$db_ztraty_tanky = $parametry['ztraty_tanky'];
			$db_ztraty_pechota = $parametry['ztraty_pechota'];
			break;
		case 'uspesna_obrana':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme_kam']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			
			$query = "SELECT login FROM users_sys WHERE id='". MajitelZeme($parametry['id_zeme_odkud'],$parametry['id_ligy'])."'";
			$res = $db->Query( $query );
			$row3 = $db->GetFetchRow( $res );
			
			$nadpis .= $row['nazev'] . " - Úspěšná obrana";
			//$obsah .= "Velitel sektoru " . $row['nazev'] . " informuje o útoku nepřátelských sil z : ";
			
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme_odkud']."'";
			$res = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res );
			
			//$obsah .= $row2['nazev'] . "<br />";
			
			//$obsah .= " Naše jednoty útok odrazily. Sektor byl bráněn silou ". $parametry['obrana'];
			$obsah .= "Pane náš sektor se stal v brzkých ranních hodinách cílem  útoku protivníkových pozemních sil. Jen díky vytrvalému odhodlání našich mužů udržet naše pozice byl sektor za cenu mnoha ztrát ubráněn.";
			
			$skupina = "boj";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["uspesna_obrana"];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $row3[0];
			$db_ztraty_tanky = $parametry['ztraty_tanky'];
			$db_ztraty_pechota = $parametry['ztraty_pechota'];
			break;	
		case 'zabiti_pri_presunu':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme_vysadek']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			
			$query = "SELECT login FROM users_sys WHERE id='". MajitelZeme($parametry['id_zeme_vysadek'],$parametry['id_ligy'])."'";
			$res = $db->Query( $query );
			$row3 = $db->GetFetchRow( $res );
			
			$nadpis .= $row['nazev'] . " - Aktivita speciálních jednotek";
			$obsah .= "Velitel sektoru " . $row['nazev'] . " informuje o útoku zvláštních jednotek. Ve zprávě stojí, útok na naší přesouvající se kolonu, přišli jsme o ". $parametry['ztrata_pechota'] ." čet pěchoty a ". $parametry['ztrata_tanky'] ." tanků.";
			$obsah .= "Pane, ve zdejší lokaci byli ve zvýšené míře zaznamenány záškodnické akce na naše přesouvající se jednotky. Soudě dle nočního přeletu letadla, rychlosti a způsobu boje odhadujem, že se jednalo o útok nepřátelských výsadkářů.";
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["zabiti_pri_presunu"];
			$db_uspesnost = $TEXT["neuspech"];
			$db_utocnik = $parametry['utocnik'];
			$db_ztraty_tanky = $parametry['ztrata_tanky'];
			$db_ztraty_pechota = $parametry['ztrata_pechota'];
			break;
		case 'zabiti_pri_utoku':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme_BLP']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			
			$nadpis .= $row['nazev'] . " - Aktivita speciálních jednotek";
			$obsah .= "V průběhu útoku na sektor " . $row['nazev'] . " byly naše obrněné jednotky napadené nepřátelským letectvem. Bylo zničeno ". $parametry['ztrata_tanky'] ." tanků, které se tak již plně nestihly zapojit do boje.";
	
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["zabiti_pri_utoku"];
			$db_uspesnost = $TEXT["neuspech"];
			$db_utocnik = $parametry['utocnik'];
			$db_ztraty_tanky = $parametry['ztrata_tanky'];
			break;
////////////////////////////////////////////////////////////			
//////////////////// PODPORA
//////////////////////////////////////////////////////////
		case 'podpora_neprosla':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_cil']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_autor']."'";
			$res6 = $db->Query( $query );
			$aut = $db->GetFetchAssoc( $res6 );
			$query = "SELECT * FROM podpora_z_domova WHERE id='".$parametry['id_typ_podpory']."'";
			$res3 = $db->Query( $query );
			$row3 = $db->GetFetchAssoc( $res3 );
			
			$nadpis .= $row['nazev'] . " - neúspěch";
			$obsah .= "Akce ". $row3['nazev'] ." byla neúspěšná. Kontrarozvědka";
			$obsah .= " hráče ".$row2['login']." jí překazila. ";
	
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $row3['nazev'];
			$db_uspesnost = $TEXT["neuspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'podpora_cizi_ubranena':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_autor']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			$query = "SELECT * FROM podpora_z_domova WHERE id='".$parametry['id_typ_podpory']."'";
			$res3 = $db->Query( $query );
			$row3 = $db->GetFetchAssoc( $res3 );
			
			$nadpis .= $row['nazev'] . " - úspěch";
			$obsah .= "Kontrarozvědka hlásí, že úspěšně odrazila nepřátelskou akci.";
	
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $row3['nazev'];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'podpora_vysadek':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme_vysadek']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			
			$nadpis .= $row['nazev'] . " - Výsadek";
			$obsah .= "Požadované letadlo s výsadkem na palubě úspěšně dosáhlo cílové zóny. Výsadkový oddíl hlásí úspěšný seskok a započal monitorování sektoru a přípravné sabotážní akce.";
			$db_efekt .= "Efekt: Ztráty protivníkových jednotek při přesunech 20 %. (pěchota: ". $parametry['pechota'] .",";
			$db_efekt .= "tanky:". $parametry['tanky'].")";
	
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["podpora_vysadek"];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'podpora_vysadek_cil':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme_vysadek']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			
			$nadpis .= $row['nazev'] . " - Výsadek";
			$obsah .= "Pane, ve zdejší lokaci byli ve zvýšené míře zaznamenány záškodnické akce na naše přesouvající se jednotky. Soudě dle nočního přeletu letadla, rychlosti a způsobu boje odhadujem, že se jednalo o útok nepřátelských výsadkářů.";
			$db_efekt .= "Efekt: Ztráty jednotek při přesunech 20 %.";
	
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["podpora_vysadek"];
			$db_uspesnost = $TEXT["neuspech"];
			$db_utocnik = $parametry['utocnik'];
			$db_ztraty_tanky = $parametry['tanky'];
			$db_ztraty_pechota = $parametry['pechota'];
			break;
		case 'podpora_pocasi':
			$nadpis .= " Pocasi";
			$obsah .= "Meteorologicke stanice zjistili pocasi na nasledujici den ";
			$skupina = "akce";
			$db_udalost = "pocasi";
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'podpora_ocernujici_kampan':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_autor']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			
			$nadpis .= $row['nazev'] . " očerňující kampaň";
			$obsah .= "Našim hochům z rozvědky se podařilo infiltrovat takticky nepříliš významnou složku nepřátelského vrchního velitelství, i přes zdánlivou nevýznamnost nám však tato akce přinesla své ovace. Pomocí dezinformací a falešených hlášení se nám podařilo na čas zdiskreditovat velení tohoto sektoru."; 
			$db_efekt .= "Efekt: Příjmy Bodů vlivu tohoto sektoru pro tento den jsou poloviční";
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["podpora_ocernujici_kampan"];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'podpora_ocernujici_kampan_negativni':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_autor']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			
			$nadpis .= $row['nazev'] . " očerňující kampaň";
			$obsah .= "Pane z hlavního stanu dnes zazněli připomínky k velení zdějšího sektoru, konkrétně o neschopnosti zdějšího vrchního velitele a jeho přímých podřízených. Velení z toho vyvozuje důsledky. Máme podezření, že se jednalo o nepřátelskou dezinformační akci namířenou proti nám.";
			$db_efekt .= "Efekt: Příjmy Bodů vlivu sektoru pro tento den o 1 nižší";
			
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["podpora_ocernujici_kampan"];
			$db_uspesnost = $TEXT["neuspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'podpora_propaganda':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_cil']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			
			$nadpis .= $row['nazev'] . " propaganda";
			$obsah .= "Našim sympatizantům na ústředí se v očích vojenských plánovačů podařilo vyzdvihnout důležitost tohoto sektoru na bojišti. Tvůj vliv a oblíbenost u velení se dočasně zvýšili.";
			$db_efekt .= "Efekt: Příjmy bodů vlivu sektoru pro tento den jsou o 1 vyšší";
			
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["podpora_propaganda"];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'podpora_propaganda_negativni':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_autor']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			
			$nadpis .= $row['nazev'] . " podpora propaganda";
			//$obsah .= "Souper - ".$row2['login']." provedl progapagandu v nasem sektoru";
			$obsah .= "Našim sympatizantům na ústředí se v očích vojenských plánovačů podařilo vyzdvihnout důležitost tohoto sektoru na bojišti. Tvůj vliv a oblíbenost u velení se dočasně zvýšili.";
			$db_efekt .= "Efekt: Příjmy bodů vlivu sektoru pro tento den jsou o 1 vyšší";
			
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["podpora_propaganda"];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'podpora_zasobovani_autor':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_autor']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			
			$nadpis .= $row['nazev'] . " podpora letecké zásobovaní";
			$obsah .= "Shození požadovaných zásob nad cílovou oblastí proběhlo úspěšně. Zásoby byli roztříděny a jsou na cestě do skladů."; 
			$db_efekt .= "Efekt: Příjmy zásob sektoru po tento den jsou o 40% a zisky paliva o 20% vyšší";
			
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["podpora_zasobovani"];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'podpora_zasobovani_cil':
			//asi zbytecne
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_autor']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			
			$nadpis .= $row['nazev'] . " podpora letecke zasobovani";
			//$obsah .= "Souper - ".$row2['login']." provedl letecke zasobovani v nasem sektoru";
			$obsah .= "Shození požadovaných zásob nad cílovou oblastí proběhlo úspěšně. Zásoby byli roztříděny a jsou na cestě do skladů."; 
			$db_efekt .= "Efekt: Příjmy zásob sektoru po tento den jsou o 40% a zisky paliva o 20% vyšší";
						
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["podpora_zasobovani"];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'podpora_pruzkum_autor':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_autor']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			
			$nadpis .= $row['nazev'] . " podpora pruzkum";
			//$obsah .= "Nasi vojaci provedli pruzkum na uzemi hrace -".$row2['login'];
			$obsah .= "Veliteli náš průzkum se vrátil z cílového sektoru s přesnými nákresy nepřátelského obraného postavení včetně vytipovaní jeho nejslabších míst. Naši hoši odvedli vynikající práci.";
			$db_efekt .= "Efekt: Obrana cílového sektoru je nyní o 15 % slabší";
			
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["podpora_pruzkum"];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'podpora_pruzkum_cil':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_autor']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			
			$nadpis .= $row['nazev'] . " podpora pruzkum";
			$obsah .= "Pane, naše hlídka nám podala zatím neověřené informace o pohybu doposud blíže nespecifikované malé skupinky nepřátel. Zatím je to jen dohad, ale mohlo by se jednat o nepřátelský průzkum našeho obranného postavení. ";

			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["podpora_pruzkum"];
			$db_uspesnost = $TEXT["neuspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'podpora_podminovani_cil_neprovedeno':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			
			$nadpis .= $row['nazev'] . " podpora_podminovani";
			$obsah .= "Nepřátelské jednotky, které se chystaly podminovat naše zařízení, neměly šanci. Byly okamžitě zatčeny.";
			
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["podpora_podminovani"];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'podpora_podminovani_neprovedeno':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			
			$nadpis .= $row['nazev'] . " podpora_podminovani";
			$obsah .= "Akce není možná. Nemáme pod svým velením ženijní brigádu, nebo cílový sektor již není pod naší kontrolou.";
			
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["podpora_podminovani"];
			$db_uspesnost = $TEXT["neuspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'podpora_podminovani_autor':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_autor']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			
			$nadpis .= $row['nazev'] . " podpora podminovani";
			$obsah .= "Pane, ženisté dokončili podminování důležitých objektů a zařízení. V případě ztráty sektoru je tak zničíme dřív než se dostanou do rukou nepřítele.";
			$db_efekt .= "Efekt: V případě dnešní ztráty sektoru, budou všechny stavby zničeny";
			
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["podpora_podminovani"];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'podpora_maskovani_autor':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_autor']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			
			$nadpis .= $row['nazev'] . " podpora maskovani";
			//$obsah .= "Nasi vojaci provedli maskovani na uzemi hrace -".$row2['login'];
			$obsah .= "V očekávání případných nepřátelských náletů vydalo velení sektoru pokyny k urychlenému maskování našich pozic. Veškeré maskovací práce byly provedeny včas a podle plánu. Identifikaci našich pozic ze vzduchu jsme zredukovali na minimum.";
			$db_efekt .= "Efekt: Sníží o 10 % šanci na zničení letiště a tanky mají o 20 % nižší ztráty";
			
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["podpora_maskovani"];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'podpora_maskovani_cil':
			//zbytecne?
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_autor']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			
			$nadpis .= $row['nazev'] . " podpora maskovani";
			//$obsah .= "Hrac - ".$row2['login']." provedl maskovani v nasem sektoru";
			$obsah .= "V očekávání případných nepřátelských náletů vydalo velení sektoru pokyny k urychlenému maskování našich pozic. Veškeré maskovací práce byly provedeny včas a podle plánu. Identifikaci našich pozic ze vzduchu jsme tak zredukovali na minimum.";
			$db_efekt .= "Efekt: Sníží o 10 % šanci na zničení letiště a tanky mají o 20 % nižší ztráty";
			
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["podpora_maskovani"];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'podpora_utok_sniperu_autor':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_autor']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			
			$nadpis .= $row['nazev'] . " podpora utok_sniperu";
			//$obsah .= "Nasi vojaci provedli utok_sniperu na uzemi hrace -".$row2['login'];
			$obsah .= "Našim sniperům se podařilo nepozorovaně proniknout za nepřátelskou linii zdejšího sektoru a úspěšně splnit svou misi.";
			$db_efekt .= "Efekt: Všechny útoky ze zdejšího sektoru jsou pro dnešní den o 30 % slabší";
			
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["podpora_utok_sniperu"];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'podpora_utok_sniperu_cil':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_autor']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			
			$nadpis .= $row['nazev'] . " podpora utok_sniperu";
			//$obsah .= "Hrac - ".$row2['login']." udelal utok_sniperu v nasem sektoru";
			$obsah .= "Pane, do našeho sektoru pronikl nepřátelský oddíl sniperů. Dřív než jsme stihli lokalizovat jejich polohu, podařilo se jim zaútočit na cíle z řad našich důstojníků a v nastalém zmatku beze ztrát ustoupit. Velení sektoru je momentálně dezorganizované.";
			$db_efekt .= "Efekt: Všechny útoky vyslané ze zdejšího sektoru budou tento den o 30 % slabší";
			
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["podpora_utok_sniperu"];
			$db_uspesnost = $TEXT["neuspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'podpora_sabotaz_autor':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_autor']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			
			$nadpis .= $row['nazev'] . " podpora sabotaz";
			$obsah .= "Štáb rozvědky hlásí úspěch sabotážní mise. Podařilo se nám narušit zdejší hlavní zásobovací síť. Je sice jen otázkou času než se jim podaří zásobování v plné míře obnovit, ale prozatím je přísun válečného materiálu z tohoto sektoru nižší.";
			$db_efekt .= "Efekt: Příjmy zásob a paliva jsou o 30% nižší";
			
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["podpora_sabotaz"];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'podpora_sabotaz_cil':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_autor']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			
			$nadpis .= $row['nazev'] . " podpora sabotaz";

			$obsah .= "Pane, velitel zdejšího sektoru hlasí citelné narušení zásobovací sítě. Z analýzy kontrarozvědky vyplývá, že se jednalo o dobře připravenou sabotáž.";
			$db_efekt .= "Efekt: Příjmy zásob a paliva jsou o 30% nižší";
			
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["podpora_sabotaz"];
			$db_uspesnost = $TEXT["neuspech"];
			$db_utocnik = $parametry['utocnik'];
			break;	
		case 'podpora_infiltrace_autor':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_autor']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			
			$nadpis .= $row['nazev'] . " podpora infiltrace";
			$obsah .= "Pane, štáb rozvědky hlásí úspěšný průnik našich agentů do velení zdejšího sektoru. Dá se očekávat, že naši chlapci splní co se od nich očekává a jejich velitelé budou na čas pěkně zmatení a odříznutí od centrálních rozkazů.";
			$db_efekt .= "Efekt: Příjmy bodů vlivu v sektoru jsou nulové";
			
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["podpora_infiltrace"];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'podpora_infiltrace_cil':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_autor']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			
			$nadpis .= $row['nazev'] . " podpora infiltrace";

			$obsah .= "Pane, z velitelství zdejšího sektoru hlásí nesrovnalosti v rozkazech a žádají o jejich ověření. Tamní situace je při nejmenším zmatená, ke všemu jsme se sektorem ztratili veškeré spojení.";
			$db_efekt .= "Efekt: Příjmy bodů vlivu v sektoru jsou nulové";
			
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["podpora_infiltrace"];
			$db_uspesnost = $TEXT["neuspech"];
			$db_utocnik = $parametry['utocnik'];
			break;			
// LETECKE AKCE
			
		case 'letecka_akce_stihaci_hlidka_autor':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_autor']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
		
			if(strlen($row2['login'])==0){
				$row2['login'] = 'Německé síly';
			}
			
			$nadpis .= $row['nazev'] . " letecka akce - Stíhací hlídka";
			$obsah .= "Naše stíhačky úspěšně dorazili do cílové zóny velitele ".$row2['login']." a zahájili hlídku nad svěřenou oblastí. Případní narušitelé budou sestřeleni.";
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["letecka_akce_stihaci_hlidka"];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'letecka_akce_stihaci_hlidka_cil':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
					
			$nadpis .= $row['nazev'] . " letecka akce - Stíhací hlídka";
			$obsah .= "Stíhačky úspěšně dorazily do cílové zóny a zahájily hlídku nad svěřenou oblastí. Případní narušitelé budou sestřeleni.";
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["letecka_akce_stihaci_hlidka"];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
			
		case 'letecka_akce_takticke_bombardovani_autor':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			
			$nadpis .= $row['nazev'];
			$obsah .= "Naše bombardéry úspěšně dosáhly cílového prostoru a provedly masivní bombardování hlavních prvků pozemního opevnění.<br />Byly zničeny následující stavby: ";
			if ($parametry['stavba_11']=='zbourana'){
				$obsah .= "Bunkr, ";
			}
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["letecka_akce_takticke_bombardovani"];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'letecka_akce_takticke_bombardovani_cil':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			
			$nadpis .= $row['nazev'];
			$obsah .= "Náš sektor se stal cílem náletu nepřátelského letectva, které se zaměřilo na naší hlavní obranou línii.<br />Byly zničeny následující stavby: ";
			if ($parametry['stavba_11']=='zbourana'){
				$obsah .= "Bunkr, ";
			}
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["letecka_akce_takticke_bombardovani"];
			$db_uspesnost = $TEXT["neuspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
	
		case 'letecka_akce_cilene_bombardovani_autor':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			
			$nadpis .= $row['nazev'];
			$obsah .= "Provedli jsme preventivní úder na předpokládaná stanoviště zdejší protivzdušné obrany.  Zároveň byli zasaženy sekundární cíle vytipované naší rozvědkou. Mise hodnocená jako úspěšná.<br />Byly zničeny následující stavby: ";
			if ($parametry['stavba_18']=='zbourana'){
				$obsah .= "Stanoviště PVO, ";
			}
			if ($parametry['stavba_20']=='zbourana'){
				$obsah .= "Policejní stanice, ";
			}
			if ($parametry['stavba_voj_pol']=='zbourana'){
				$obsah .= "Vojenská policie, ";
			}
			if ($parametry['stavba_flak']=='zbourana'){
				$obsah .= "Flak, ";
			}
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["letecka_akce_cilene_bombardovani"];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'letecka_akce_cilene_bombardovani_cil':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			
			$nadpis .= $row['nazev'];
			$obsah .= "Naše oblast se stala cílem náletu nepřátelského letectva.<br />Byly zničeny následující stavby: ";
			if ($parametry['stavba_18']=='zbourana'){
				$obsah .= "Flak, ";
			}
			if ($parametry['stavba_20']=='zbourana'){
				$obsah .= "Policejní stanice, ";
			}
			if ($parametry['stavba_voj_pol']=='zbourana'){
				$obsah .= "Vojenská policie, ";
			}
			if ($parametry['stavba_flak']=='zbourana'){
				$obsah .= "Flak, ";
			}
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["letecka_akce_cilene_bombardovani"];
			$db_uspesnost = $TEXT["neuspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'letecka_akce_nalet_na_letiste_autor':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_cil']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			
			$nadpis .= $row['nazev'] . " letecka akce - Nálet na letiste";
			$obsah .= "Naší bombardovací letce se podařilo úspěšně proniknout a následně bombardovat místo předpokládaného letiště velitele ".$row2['login'];
			$obsah .= "<br />Byly zničeny následující stavby: ";			
			if ($parametry['stavba_1']=='zbourana'){
				$obsah .= "Letiště, ";
			}
			if ($parametry['stavba_2']=='zbourana'){
				$obsah .= "Polní letiště, ";
			}
			if ($parametry['stavba_3']=='zbourana'){
				$obsah .= "Hangár, ";
			}
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["letecka_akce_nalet_na_letiste"];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'letecka_akce_nalet_na_letiste_cil':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_autor']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			
			$nadpis .= $row['nazev'] . " letecka akce - Nálet na letiste";
			$obsah .= "Naše letiště ve zdejším sektoru se stalo cílem nepřátelkého náletu. Útok byl rychlý a nečekaný, letiště dostalo několik přímých zásahů včetně okolního vybavení a letadel.";
			$obsah .= "Byly zničeny následující stavby: ";
			if ($parametry['stavba_1']=='zbourana'){
				$obsah .= "Letiště, ";
			}
			if ($parametry['stavba_2']=='zbourana'){
				$obsah .= "Polní letiště, ";
			}
			if ($parametry['stavba_3']=='zbourana'){
				$obsah .= "Hangár, ";
			}
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["letecka_akce_nalet_na_letiste"];
			$db_uspesnost = $TEXT["neuspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'letecka_akce_nalet_na_komunikace_autor':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_cil']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			
			$nadpis .= $row['nazev'] . " letecka akce - Nálet na komunikace";
			$obsah .= "Našim pilotům se podařilo dosáhnout cílové zóny a následně provést nálet na předem vytyčené cíle velitele - ".$row2['login'];
			$obsah .= "<br />Bylo zničeno ".$parametry['ztrata_inf']."  body infrastruktury.";
	
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["letecka_akce_nalet_na_komunikace"];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'letecka_akce_nalet_na_komunikace_cil':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_autor']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			
			$nadpis .= $row['nazev'] . " letecka akce - Nálet na komunikace";
			$obsah .= "Pane naše oblast se stala cílem protívníkových bombardérů, jejich primárním cílem byla naše dopravní síť. Přišli jsme o několik silničních i železničních mostů a tunelů. Naše logistická síť se zde zpomalí.<br />Způsobené škody: - ".$parametry['ztrata_inf']." body infrastruktury sektoru";
			
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["letecka_akce_nalet_na_komunikace"];
			$db_uspesnost = $TEXT["neuspech"];
			$db_utocnik = $parametry['utocnik'];
			break;	
		case 'letecka_akce_bombardovani_autor':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_cil']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			
			if(strlen($row2['login'])==0){
				$row2['login'] = 'Německé síly';
			}
			
			$nadpis .= $row['nazev'] . " letecka akce - strategicke bombardovani";
			$obsah .= "Našim pilotům se podařilo dosáhnout cílové zóny a následně provést nálet na předem vytyčené cíle velitele ".$row2['login'].".";
			$obsah .= "<br />";
	
			$obsah .= "Byly zničeny následující stavby: ";			
			if ($parametry['stavba_4']=='zbourana'){
				$obsah .= "Zásobovací sklad, ";
			}
			if ($parametry['stavba_5']=='zbourana'){
				$obsah .= "Muniční sklad, ";
			}
			if ($parametry['stavba_6']=='zbourana'){
				$obsah .= "Tankovací stanice, ";
			}
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["letecka_akce_bombardovani"];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'letecka_akce_bombardovani_cil':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			
			$nadpis .= $row['nazev'] . " letecka akce - strategicke bombardovani";
			$obsah .= "Naše oblast se stala cílem náletu nepřátelského letectva, přes veškerou naší snahu jsme jim v tom nebyli schopni zabránit. ";
			$obsah .= "Byly zničeny následující stavby: ";			
			if ($parametry['stavba_4']=='zbourana'){
				$obsah .= "Zásobovací sklad, ";
			}
			if ($parametry['stavba_5']=='zbourana'){
				$obsah .= "Muniční sklad, ";
			}
			if ($parametry['stavba_6']=='zbourana'){
				$obsah .= "Tankovací stanice, ";
			}
			
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["letecka_akce_bombardovani"];
			$db_uspesnost = $TEXT["neuspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'letecka_akce_blizka_podpora_autor':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_autor']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			
			$nadpis .= $row['nazev'] . " letecka akce - blizka_podpora";
			$obsah .= "Na naší žádost byla do oblasti přivolána letecká podpora, která se zaměřila na nepřátelské obrněné jednotky. Mise byla hodnocená jako úspěšná.<br />Podařilo se nám zničit ".$parametry['tanky_zniceno']." tanků.";
			
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["letecka_akce_blizka_podpora"];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'letecka_akce_blizka_podpora_cil':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_autor']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			
			$nadpis .= $row['nazev'] . " letecka akce - blizka_podpora";
			$obsah .= "Na naší žádost byla do oblasti přivolána letecká podpora, která se zaměřila na nepřátelské obrněné jednotky. Mise byla hodnocená jako úspěšná.<br />Podařilo se zničit ".$parametry['tanky_zniceno']." tanků.";
			
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["letecka_akce_blizka_podpora"];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'letecka_akce_stremhlavy_nalet_autor':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_cil']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			
			if(strlen($row2['login'])==0){
				$row2['login'] = 'Německé síly';
			}
			$nadpis .= $row['nazev'] . " letecka akce - nalet";
			$obsah .= "Na naší žádost byla do oblasti velitele ".$row2['login']." vyslána podpůrná letka s cílem eliminovat případné protivníkovy pozemní jednotky. Mise byla hodnocená jako úspěšná.<br />Podařilo se nám zničit ".$parametry['ztraty_pechota']." čet pěchoty a ".$parametry['ztraty_tanky']." tanků.";
			
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["letecka_akce_nalet"];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		case 'letecka_akce_stremhlavy_nalet_cil':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM users_sys WHERE id='".$parametry['id_autor']."'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			
			$nadpis .= $row['nazev'] . " letecka akce - nalet";
			$obsah .= "Náš sektor se stal cílem náletu protivníkových bitevních stíhaček a bombardérů. Jejich primárním cílem se staly naše pozemní jednotky, i přes snahu naši pozemní protiletadlové obrany a sporadického vzdušného krytí jsme nebyli schopni se jim dostatečně ubránit.<br />Přišli jsme o: ".$parametry['ztraty_pechota']." pěších čet a ".$parametry['ztraty_tanky']." tanků.";
			
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $TEXT["letecka_akce_nalet"];
			$db_uspesnost = $TEXT["neuspech"];
			$db_utocnik = $parametry['utocnik'];
			$db_ztraty_tanky = $parametry['ztraty_tanky'];
			$db_ztraty_pechota = $parametry['ztraty_pechota'];
			break;
		case 'letecka_akce_autor_neuspech':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM letecke_akce WHERE id='".$parametry['id_typ_letecke_akce']."'";
			$res3 = $db->Query( $query );
			$row3 = $db->GetFetchAssoc( $res3 );
			
			$nadpis .= $row['nazev'] . " - neúspěch";
			$obsah .= "Akce ". $row3['nazev'] ." byla neúspěšná. Koncentrace zdejší protiletadlové obrany je zde příliš vysoká. ";
	
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $row3['nazev'];
			$db_uspesnost = $TEXT["neuspech"];
			$db_utocnik = $parametry['utocnik'];
			
			break;
		case 'letecka_akce_cil_uspech':
			$query = "SELECT * FROM zeme WHERE id='".$parametry['id_zeme']."'";
			$res = $db->Query( $query );
			$row = $db->GetFetchAssoc( $res );
			$query = "SELECT * FROM letecke_akce WHERE id='".$parametry['id_typ_letecke_akce']."'";
			$res3 = $db->Query( $query );
			$row3 = $db->GetFetchAssoc( $res3 );
			
			$nadpis .= $row['nazev'] . " letecka akce ";
			$obsah .= " Bylo zaznamenáno narušení zdejšího vzdušného prostoru, místní protiletadlová obrana se však s nepřítelem vypořádala dřív než se dostal ke svým cílům .";
			$skupina = "akce";
			$db_nazev_sektoru = $row['nazev'];
			$db_udalost = $row3['nazev'];
			$db_uspesnost = $TEXT["uspech"];
			$db_utocnik = $parametry['utocnik'];
			break;
		//GLOBALNI
		
		case 'liga_dohrana':
			$query = "SELECT * FROM users_sys WHERE id='$id_user'";
			$res2 = $db->Query( $query );
			$row2 = $db->GetFetchAssoc( $res2 );
			
			$query = "SELECT * FROM ligy WHERE id='".$parametry['id_ligy']."'";
			$res3 = $db->Query( $query );
			$row3 = $db->GetFetchAssoc( $res3 );
			
			$nadpis .= " Vítěztví ";
			$obsah .= "Dnešní den se navždy zapíše do dějin, dnes v brzkých ranních hodinách po  
těžkých bojích v ulicích Berlína se jednotkám velitele <span style=\"color:gold;\">".$row2['login']."</span> 
podařilo úspěšně dosáhnout klíčových bodů města. 
Díky tomuto obrovskému vítězství se tak tímto končí druhá světová válka v Evropě";
			$skupina = "globalni";
			//$db_nazev_sektoru = $row['nazev'];
			//$db_udalost = $row3['nazev'];
			//$db_uspesnost = $TEXT["uspech"];
			//$db_utocnik = $parametry['utocnik'];
			break;
		
		default:
			$nadpis .= "uknown";
			$obsah .="NEZNAME HLASENI - kontaktujte prosím administrátory";
			$skupina = "globalni";
			break;
	}
	
	//vkladani dat do DB
	$query = "INSERT INTO `in_game_hlaseni`
				(`id_hrac`,`id_ligy`,`skupina`,`obsah`,`cas`,`nadpis`,`nazev_sektoru`,
				`udalost`,`uspesnost`,`utocnik`,`efekt`,`ztraty_tanky`,`ztraty_pechota`) 
				VALUES 
				('$id_user',
				 '$id_ligy',
				 '$skupina',
				 '$obsah',
				 '$cas',
				 '$nadpis',
				 '$db_nazev_sektoru',
				'$db_udalost',
				'$db_uspesnost',
				'$db_utocnik',
				'$db_efekt',
				'$db_ztraty_tanky',
				'$db_ztraty_pechota');
			";
	$db->DbQuery( $query );
	
}
?>