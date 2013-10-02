<?php
//$SKIN_SUBDIR = "prepocet";
define('BoB', 1);
$prepocet_override = true;

// vlozi soubor s konfiguracnim nastavenim unikatnim pro tento server
require_once("config/config.php");
require_once("$DIR_CONFIG/text.php");

// budeme logovat volani prepoctu
$calls_file = fopen( $DIR_LOG . 'prepocet_calls.log', 'ab');
fwrite($calls_file, "\n" . date("Y-n-j g:i:s") . "\n "); 
fwrite($calls_file, "referer: ". @$_SERVER['HTTP_REFERER'] . "\n"); 

// poustet pouze s heslem
if ((!array_key_exists('heslo',$_REQUEST)) || ($_REQUEST['heslo'] != $PREPOCET_HESLO) ) {
	fwrite($calls_file, "wrong password: ". @$_REQUEST['heslo']."\n"); 
    die("nemate pravo.");
}

//funkce pro pocitani prepoctu
require_once("$DIR_CONFIG/prepocet.php");
require_once("$DIR_CONFIG/prepocet_xml.php");
require_once($DIR_CONFIG . "boj.php");

// include basic functions and classes
require_once("$DIR_CORE/main.php");
require_once("$DIR_CONFIG/lib.php");

//$page->set_skin_subdir("$SKIN_SUBDIR");

require_once($DIR_LIB . "page_elements/page_element.php");

if(is_file($lockfile)){
	fwrite($calls_file, "prepocet je jiz spusten. nejde spustit 2x najednou \n");
	die('prepocet je jiz spusten. nejde spustit 2x najednou');
}

if(is_file($secondlock)){
	fwrite($calls_file, "prepocet se nyni spustit neda - jiz probehl \n");
	die('prepocet se nyni spustit neda - jiz probehl');
}

// vytvoreni lock souboru
$lfile = fopen( $lockfile, 'w');
$fwrite = fwrite($lfile, "prepocet");
fclose($lfile);

// vytvoreni dvojnasobne ochrany spusteni prepoctu
$lfile = fopen( $secondlock, 'w');
$fwrite = fwrite($lfile, "prepocet");
fclose($lfile);

// vytvoreni logoveho souboru
$myFile = date("Y-n-j_g-i-s");
$file = fopen( $DIR_LOG . $myFile, 'ab');

if ($file === false) {
	fwrite($calls_file, "nevytvoril se soubor s logy \n");
	$error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                        'file',"Nepodarilo se otevrit soubor",null);
}

$message = date("Y-n-j_g-i-s") . " Start \n";
fwrite($file, $message);

//zaloha in_game tabulek
	$start = microtime(true);
	$message = date("Y-n-j_g-i-s") . " Zalohovani in_game tabulek - ";
	fwrite($file, $message);
	require_once($DIR_LIB . "mysqldump.php");
	$message = "OK";
	$message .= " (". VratRozdilMikro($start) .")\n";
	fwrite($file, $message);

$query = "SELECT id FROM ligy WHERE stav='active'";
$res_ligy = $db->Query( $query );

while ($liga = $db->GetFetchAssoc( $res_ligy )){
		
	$id_ligy = $liga['id'];
	
	//vymazani starych hlaseni
	$message = date("Y-n-j_g-i-s") . " Liga-" . $id_ligy . " Vymazani starych tabulek ";
	fwrite($file, $message);
	$start = microtime(true);
	$query = "DELETE FROM in_game_hlaseni WHERE id_ligy=$id_ligy";
	$db->DbQuery( $query );
	//nesmaze se podpora, ktera ma platit pres prepocet. Konkretne zatim pruzkum
	$query = "DELETE FROM in_game_vlivy_podpora WHERE 
			id_ligy=$id_ligy and (param1 is null or param1!='vcera')";
	$db->DbQuery( $query );
	$query = "DELETE FROM in_game_vlivy_letecke_akce WHERE id_ligy=$id_ligy";
	$db->DbQuery( $query );
	$message = "OK";
	$message .= " (". VratRozdilMikro($start) .")\n";
	fwrite($file, $message);
	// MUSI BYT PRVNI!!!!!!!!!!!!
		
	//pridavani hernich kol
	$start = microtime(true);
	$message = date("Y-n-j_g-i-s") . " Liga-" . $id_ligy . " Efekty na hrace pred prepoctem ";
	fwrite($file, $message);
	$err = EfektyNaHracePredPrepoctem( $id_ligy );
	$message = "- " . $err;
	$message .= " (". VratRozdilMikro($start) .")\n";
	fwrite($file, $message);
	
	//vyhodnoceni podpory z domova
	$start = microtime(true);
	$message = date("Y-n-j_g-i-s") . " Liga-" . $id_ligy . " Vyhodnocovani podpory z domova  ";
	fwrite($file, $message);
	$err = VyhodnotPodporuZDomova( $id_ligy );
	$message = "- " . $err;
	$message .= " (". VratRozdilMikro($start) .")\n";
	fwrite($file, $message);

	//vyhodnoceni leteckych akci
	$start = microtime(true);
	$message = date("Y-n-j_g-i-s") . " Liga-" . $id_ligy . " Vyhodnocovani leteckych akci  ";
	fwrite($file, $message);
	$err = VyhodnotLeteckeAkce( $id_ligy );
	$message = "- " . $err;
	$message .= " (". VratRozdilMikro($start) .")\n";
	fwrite($file, $message);

	//vyhodnocovani utoku
	$start = microtime(true);
	$message = date("Y-n-j_g-i-s") . " Liga-" . $id_ligy . " Vyhodnocovani utoku ";
	fwrite($file, $message);
	require_once("${DIR_CONFIG}prepocet_lib.php");
	$liga = new LigaPrepocet( $db, $id_ligy);
	$err = $liga->VyhodnotUtoky();
	$err .= $liga->Save2DB();
	$message = "- " . $err;
	$message .= " (". VratRozdilMikro($start) .")\n";
	fwrite($file, $message);
	
	//pridani sily neutralkam
	$start = microtime(true);
	$message = date("Y-n-j_g-i-s") . " Liga-" . $id_ligy . " Pocitani nove sily neutralek ";
	fwrite($file, $message);
	$err = NoveSilyNeutralek( $id_ligy );
	$message = "- " . $err;
	$message .= " (". VratRozdilMikro($start) .")\n";
	fwrite($file, $message);
	
	//cisteni/reset zemi - napr pocet oprav infrastruktury
	$start = microtime(true);
	$message = date("Y-n-j_g-i-s") . " Liga-" . $id_ligy . " Reset promenych u zemi ";
	fwrite($file, $message);
	$err = ResetPromenychUZemi( $id_ligy );
	$message = "- " . $err;
	$message .= " (". VratRozdilMikro($start) .")\n";
	fwrite($file, $message);
		
	//efekty na hrace
	$start = microtime(true);
	$message = date("Y-n-j_g-i-s") . " Liga-" . $id_ligy . " efekty na hracich ";
	fwrite($file, $message);
	$err = EfektyNaHrace( $id_ligy );
	$message = "- " . $err;
	$message .= " (". VratRozdilMikro($start) .")\n";
	fwrite($file, $message);
	
	//zpracovani stavu ligy
	$start = microtime(true);
	$message = date("Y-n-j_g-i-s") . " Liga-" . $id_ligy . " zpracovani stavu ligy ";
	fwrite($file, $message);
	$err = ZpracovaniStavuLigy( $id_ligy );
	$message = "- " . $err;
	$message .= " (". VratRozdilMikro($start) .")\n";
	fwrite($file, $message);
	
	//vymazani vlivu stihaci hlidky
	$query = "DELETE FROM in_game_vlivy_letecke_akce WHERE id_ligy=$id_ligy and id_letecke_akce=1";
	$db->DbQuery( $query );
	//vymazani vlivu maskovani
	$query = "DELETE FROM in_game_vlivy_podpora WHERE id_ligy=$id_ligy and id_podpora=8";
	$db->DbQuery( $query );
	//vymazani vlivu podminovani
	$query = "DELETE FROM in_game_vlivy_podpora WHERE id_ligy=$id_ligy and id_podpora=7";
	$db->DbQuery( $query );
	//vyreseni vlivu pruzkum
	$query = "DELETE FROM in_game_vlivy_podpora WHERE id_ligy=$id_ligy and id_podpora=6 and param1='vcera'";
	$db->DbQuery( $query );
	$query = "UPDATE in_game_vlivy_podpora SET param1='vcera' 
			WHERE id_ligy=$id_ligy and id_podpora=6 and param1='dnes'";
	$db->DbQuery( $query );
	
	//vymazani utoku
	$query = "DELETE FROM in_game_utoky WHERE id_ligy=$id_ligy";
	$db->DbQuery( $query );
	
	//ulozeni stavu ligy po prepoctu - tabulky in_game_vcera
	// POSLEDNI VEC CO SE V LIZE POCITA!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!11
	$start = microtime(true);
	$message = date("Y-n-j_g-i-s") . " Liga-" . $id_ligy . " Kopirovani stavu ligy po prepoctu ";
	fwrite($file, $message);
	$err = KopirovaniStavuLigy( $id_ligy );
	$message = "- " . $err;
	$message .= " (". VratRozdilMikro($start) .")\n";
	fwrite($file, $message);
	
}

//efekty na vsechny hrace pro vsechny ligy najednou
$message = date("Y-n-j_g-i-s") . "Koncove efekty na vsechny hrace ";
fwrite($file, $message);
$err = EfektyNaVsechnyHrace();
$message = "- " . $err . "\n";
fwrite($file, $message);
	
$message = date("Y-n-j_g-i-s") . " Succesfully ends\n";
fwrite($file, $message);


//XML export
$message = date("Y-n-j_g-i-s") . "XML export ";
fwrite($file, $message);
$err = XMLexport();
$message = "- " . $err . "\n";
fwrite($file, $message);
$message = date("Y-n-j_g-i-s");
fwrite($file, $message);

// smaze zamek
unlink($lockfile);

fclose($calls_file);

if (fclose($file) === false) {
$error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                        'file',"Nepodarilo se zavrit soubor",null);
                }

// vlozi ukoncovaci skript, ktery ukonci pripojeni k databazi, ...
finalize();

?>