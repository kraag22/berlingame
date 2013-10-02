<?php
global $CONST;
//spolecne vlastnostni vsech scenaru
if(@is_numeric($_GET['id_scenare'])){
	$id = $_GET['id_scenare'];
}
else{
	$id = 1;
}
$texty['vstoupit'] = 'svet.php';

$query = "SELECT * FROM ligy JOIN (
SELECT count(*) as akt_hracu FROM in_game_hrac WHERE id_liga='$id') as ak
WHERE id='$id'";
$res = $db->Query( $query );
$row = $db->GetFetchAssoc( $res );

$texty['nazev'] = $row['nazev'];
$texty['obdobi'] = $row['rocni_obdobi'];
$texty['typ_z_db'] = $row['typ'];
$texty['herni_den'] = $row['odehranych_dnu'];
$texty['akt_poc_hracu'] = $row['akt_hracu'] . ' z ' . $row['max_pocet_hracu'];

switch(@$row['typ']){
	case 'deathmatch':
		$texty['podminky_vstup'] = '<ul><li>Scénář je přístupný všem hráčům.</li>
		<li>Přihlášení do hry je možné pouze první '.$CONST['LIGA_ELITE_REGISTRACE_DO'].' herní dny.</li></ul>';
		$texty['podminky_vyhra'] = '<ul>
			<li>LETECKÁ PŘEVAHA<br/>Vybuduj leteckou sílu o velikosti '.$CONST['LIGA_LETECKA_SILA_PRO_ZAVRENI_DEATHMATCH'].'.</li>
			<li>ROZHODUJÍCÍ VLIV<br/>Získej a udrž si příjmy bodu vlivu '.$CONST['LIGA_BV_PRO_ZAVRENI'].' za tah.</li>
			<li>DOSTATEK ZDROJŮ<br/>Nashromáždi '.$CONST['LIGA_SUROVINY_PRO_ZAVRENI'].' zásob a '.$CONST['LIGA_PALIVO_PRO_ZAVRENI'].' paliva pro rozhodující útok
			 na samotný Berlín.</li>
			</ul>';
		$texty['typ'] = 'Deathmatch';
		$texty['uvod'] = 'Deathmatch scénář, cílem tohoto typu scénáře je prosadit se jako jednotlivec 
		oproštěn od spolupráce s ostatními. Spolehnout se v tomto scénáři můžeš jen a jen na sebe a své 
		strategické schopnosti. Je to typ scénáře s minimem pravidel, který ti tak dává značnou volnost 
		konání. Nerozlišuješ zde mezi spojenci a sověty, každý je zde nepřítel a všichni se snaží získat 
		vítězství ve hře pro sebe. Jediné co tě zde bude zajímat jsou podmínky výhry. Jakou cestou jich 
		dosáhneš, je na tobě.<br />
		Jsi typ co věří hlavně ve své schopnosti? Chceš rychlou a přímočarou hru bez zbytečné diplomacie?
		 Pak je	tento scénář určen hlavně pro tebe..';
		break;
	case 'elite_dm':
		$texty['podminky_vstup'] = '<ul><li>Nutno mít odehráno alespoň 7 dnů.</li>
			<li>Přihlášení do hry je možné pouze '.$CONST['LIGA_ELITE_REGISTRACE_DO'].'. herní den.</li></ul>';
		$texty['podminky_vyhra'] = '<ul>
			<li>LETECKÁ PŘEVAHA<br/>Vybuduj leteckou sílu o velikosti '.$CONST['LIGA_LETECKA_SILA_PRO_ZAVRENI_DEATHMATCH'].'.</li>
			<li>ROZHODUJÍCÍ VLIV<br/>Získej a udrž si příjmy bodu vlivu '.$CONST['LIGA_BV_PRO_ZAVRENI'].' za tah.</li>
			<li>DOSTATEK ZDROJŮ<br/>Nashromáždi '.$CONST['LIGA_SUROVINY_PRO_ZAVRENI'].' zásob a '.$CONST['LIGA_PALIVO_PRO_ZAVRENI'].' paliva pro rozhodující útok
			 na samotný Berlín.</li>
			</ul>';
		$texty['typ'] = 'Elitní deathmatch';
		$texty['uvod'] = 'Deathmatch scénář, cílem tohoto typu scénáře je prosadit se jako jednotlivec 
		oproštěn od spolupráce s ostatními. Spolehnout se v tomto scénáři můžeš jen a jen na sebe a své 
		strategické schopnosti. Je to typ scénáře s minimem pravidel, který ti tak dává značnou volnost 
		konání. Nerozlišuješ zde mezi spojenci a sověty, každý je zde nepřítel a všichni se snaží získat 
		vítězství ve hře pro sebe. Jediné co tě zde bude zajímat jsou podmínky výhry. Jakou cestou jich 
		dosáhneš, je na tobě.<br />
		Jsi typ co věří hlavně ve své schopnosti? Chceš rychlou a přímočarou hru bez zbytečné diplomacie?
		 Pak je	tento scénář určen hlavně pro tebe..';
		break;
	case 'team':
		$texty['podminky_vstup'] = '<ul><li>Nutno mít odehráno alespoň 7 dnů.</li>
			<li>Přihlášení do hry je možné pouze '.$CONST['LIGA_ELITE_REGISTRACE_DO'].'. herní den.</li></ul>';
		$texty['podminky_vyhra'] = '<ul>
			<li>LETECKÁ PŘEVAHA<br/>Vybuduj leteckou sílu o velikosti '.$CONST['LIGA_LETECKA_SILA_PRO_ZAVRENI_TEAM'].'.</li>
			<li>ROZHODUJÍCÍ VLIV<br/>Získej a udrž si příjmy bodu vlivu '.$CONST['LIGA_BV_PRO_ZAVRENI'].' za tah.</li>
			<li>DOSTATEK ZDROJŮ<br/>Nashromáždi '.$CONST['LIGA_SUROVINY_PRO_ZAVRENI'].' zásob a '.$CONST['LIGA_PALIVO_PRO_ZAVRENI'].' paliva pro rozhodující útok
			 na samotný Berlín.</li>
			 <li>BOJOVÁ EFEKTIVITA<br/>Získej a udrž si '.$CONST['LIGA_PRESTIZ_PRO_ZAVRENI_TEAM'].' bodů prestiže.</li>
			</ul>';
		$texty['typ'] = 'Týmový scénář';
		$texty['uvod'] = 'Jedná se o typ scénáře, ve kterém je hlavní důraz kladen (jak už název 
sám praví) na týmovou spolupráci. Scénář sám vychází z historického 
základu okořeněného prvkem alternativní reality. Vyberte si jednu ze 
dvou bojujících stran a ponořte se do víru událostí posledních dnů 
války v Evropě. Bude se historie opakovat a nad Berlínem zavlaje zástava
rudé armády? Nebo bude předstihnutá vlajkou lemovanou hvězdy a pruhy?
Dohodnou se obě mocnosti kdo první vstoupí do Berlína? Nebo vidina 
vítězství a prohlubující se ideologické rozdíly dají promluvit zbraním?
Splní se německému velení sen o rozkolu v řadách spojenců? Stanou 
tak spojenci proti sobě v tváří v tvář? Staň se jednou ze dvou bojujích 
stran a rozhodni, kudy se bude historie ubírat.<br />
  Na každé straně bojuje 10 hráčů, jejich cílem je společně
obsadit srdce Třetí říše dřív než druhá strana. Způsob jakým toho dosáhou
je jen na nich. Hra má několik zákonitostí. Předně není omezena počtem 
dnů, můžete tedy hrát neomezeně dlouho, končí tehdy, je-li obsazen Berlín. 
Členové stejné národnosti spolu nemohou bojovat, jsou členy jednoho týmu 
a jen na základě společné taktiky mohou dosáhnout kýženého cíle. Dohoda 
jejich společné strategie a postupu je zde krom standartní pošty rozšířena 
i o společné fórum, kam mají přístup jen členové stejného týmu. Některé 
podmínky pro výhru se váží na celý tým, to znamená, aby jsi vyhrál jako 
jednotlivec, musíš udělat i něco proto, aby měl tvůj tým navrch.<br />
  Láká tě týmový duch? Máš organizační schopnosti? Je na tebe
spolehnutí? Chceš zažít jinou dimenzi bojů? Je-li tomu tak pak
je tento typ scénáře to pravé pro tebe.';
		break;
	default:
		//trenink
		$texty['podminky_vstup'] = '<ul><li>Scénář je přístupný všem hráčům.</li>
		<li>Přihlášení do hry je možné pouze první '.$CONST['LIGA_REGISTRACE_DO'].' herní dny.</li></ul>';
		$texty['podminky_vyhra'] = '<ul><li>Žádné.</li></ul>';
		$texty['typ'] = 'Tréninkový scénář';
		$texty['uvod'] = 'Tréninkový scénář je speciální herní mód, který byl navržen zejména
					pro nové hráče. Jeho cílem je seznámit s hrou, jejími pravidly a zákonitostmi. Nemá 
					proto žádný konkrétní výherní cíl. Scénář trvá vždy '.$CONST['LIGA_KONEC_TRENINGU'].' 
					dní a po jejich uplynutí se opět 
					provede nový restart. Po stanovený počet dní si tak hráč může vyzkoušet různé herní 
					postupy, které následně může uplatnit v některém z "ostrých" scénářů. K rychlejšímu 
					pochopení a ukázce co hra nabízí, je zde Oproti ostatním scénářům několik odlišností.
					 Hráč začíná s více sektory pod svým velením, má ve svých skladech více zásob a paliva
					  a má už postavená některá zařízení. Herní principy jsou však zachovány.';	
}

foreach( $texty as $key => $text ){
	$form = new textElement( $text );       
	$page->add_element($form, $key);
}

?>