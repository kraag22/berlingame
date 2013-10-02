<?php
date_default_timezone_set('Europe/Prague');

//o prepoctu ktereho dne se vypne moznost registrace?
$CONST['LIGA_REGISTRACE_DO'] = 2;// jen trenink
$CONST['LIGA_ELITE_REGISTRACE_DO'] = 1; // vse ostatni

//hodina do ktere se da prihlasit do ligy
$CONST['max_cas_prihlaseni_do_ligy'] = 22;

//let sila potrebna pro zavreni
$CONST['LIGA_LETECKA_SILA_PRO_ZAVRENI_DEATHMATCH'] = 300;
$CONST['LIGA_LETECKA_SILA_PRO_ZAVRENI_TEAM'] = 300;

//pocet BV nutny k zavreni
$CONST['LIGA_BV_PRO_ZAVRENI'] = 100;
$CONST['LIGA_BV_PRO_ZAVRENI_TEAM'] = 70;

//pocet zdroju nutny k zavreni
$CONST['LIGA_SUROVINY_PRO_ZAVRENI'] = 100000;
$CONST['LIGA_PALIVO_PRO_ZAVRENI'] = 20000;

//pocet sektoru nutnych pro tymovy scenar pro vyhru
$CONST['LIGA_SEKTORY_PRO_ZAVRENI_TEAM'] = 80;

//pocet prestize nutnych pro zavreni teamoveho scenare
$CONST['LIGA_PRESTIZ_PRO_ZAVRENI_TEAM'] = 400;

//pocet dnu kdy se zavre treningova liga
$CONST['LIGA_KONEC_TRENINGU'] = 10;

//maximalni pocet kol, co muze hrac po prepoctu mit 
$CONST['maximalni_pocet_kol'] = 16;

$CONST['NEUTRALKA_INFRASTRUKTURA'] = 12;
$CONST['NEUTRALKA_PECHOTA'] = 10;
$CONST['NEUTRALKA_TANKY'] = 3;
$CONST['NEUTRALKA_SILA_1_DEN'] = 54;

$CONST['START_SSSR_suroviny'] = 1000;
$CONST['START_SSSR_palivo'] = 200;
$CONST['START_SSSR_bv'] = 0;
$CONST['START_SSSR_infra'] = 20;

$CONST['START_US_suroviny'] = 1000;
$CONST['START_US_palivo'] = 200;
$CONST['START_US_bv'] = 0;
$CONST['START_US_infra'] = 20;

$CONST['START_trening_suroviny'] = 30000;
$CONST['START_trening_palivo'] = 10000;
$CONST['START_trening_bv'] = 150;
		
//zeme pro log team
$CONST['zeme_pro_log_us'] = "10,52,104,93,74,145,131,118,138,48";
$CONST['zeme_pro_log_sssr'] = "40,90,111,24,47,128,8,82,4,30";

//o kolik zemi vice bude mit hrac pri logu v treninku
$CONST['pocet_zeme_trenink'] = 2;

//maximum policejnich stanic
$CONST['MAX_POL_STANIC'] = 10;
//maximum pvo stanic
$CONST['MAX_PVO_STANIC'] = 10;

//kolik procent se opravi za 1 klik
$CONST["PROCENTA_OPRAVENE_INFRASTRUKTURY"] = 5;

$CONST["MIN_ZTRATY_PECHOTA_USPESNY_UTOK"] = 10;
$CONST["MAX_ZTRATY_PECHOTA_USPESNY_UTOK"] = 20;
$CONST["MIN_ZTRATY_TANKY_USPESNY_UTOK"] = 5;
$CONST["MAX_ZTRATY_TANKY_USPESNY_UTOK"] = 10;

$CONST["MIN_ZTRATY_PECHOTA_NEUSPESNY_UTOK"] = 50;
$CONST["MAX_ZTRATY_PECHOTA_NEUSPESNY_UTOK"] = 70;
$CONST["MIN_ZTRATY_TANKY_NEUSPESNY_UTOK"] = 30;
$CONST["MAX_ZTRATY_TANKY_NEUSPESNY_UTOK"] = 50;

$CONST["MIN_ZTRATY_PECHOTA_OBRANA"] = 5;
$CONST["MAX_ZTRATY_PECHOTA_OBRANA"] = 10;
$CONST["MIN_ZTRATY_TANKY_OBRANA"] = 1;
$CONST["MAX_ZTRATY_TANKY_OBRANA"] = 5;

$CONST["POLICEJNI_STANICE_BONUS"] = 7;
$CONST["PVO_STANICE_BONUS"] = 7;

//postih k utoku za volny pruchod
$CONST["POSTIH_UTOK_PRES_PRUCHOD"] = 0.5;

// procenta, kolik obrany se dava sousednim zemim
$CONST["PODPORA_SOUSEDNICH_ZEMI_DO_OBRANY"] = 0.1;

// bonus se pocita 1 + pocet_tanku * UCINEK / 100
$CONST["UCINEK_TANKU_OBRANA"] = 1;
$CONST["UCINEK_TANKU_UTOK"] = 0.5;

// postih k obrane a utoku za obkliceni
$CONST["OBKLICENI_POSTIH_OBRANA"] = 0.5;
$CONST["OBKLICENI_POSTIH_UTOK"] = 0.5;

//cim se vydeli maximalni LS aby se ziskalo minimum
// 100 / 1.4 == 70
$CONST["MINIMUM_LS"] = 1.4;

//pokud ma hrac mene odehranych dnu, posle se mu pri prihlaseni uvodni zprava
$CONST['max_dny_zasilani_uvodni_zpravy'] = 15;

//pocet dnu neaktivity hrace, po kterych bude odlogovan
$CONST['dni_pro_odlognuti'] = 3;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//STAVBY
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
$CONST["STAVBY_SSSR"] = "14,16";
$CONST["STAVBY_US"] = "12,22";

//kolik procent zasob se vrati po zbourani 
$CONST['STAVBY_POMER_ZBOURANI'] = 0.25;

//letiste - zapocitavaji se oboje do limitu LS
$CONST["STAVBY_letiste"] = 8;
$CONST["STAVBY_letiste_add_ls"] = 3;

$CONST["STAVBY_polni_letiste"] = 4;

$CONST["STAVBY_hangar"] = 2;

//kolik umozni jedno letiste letecke sily
$CONST["EFEKT_1_LETISTE_NA_LS"] = 15;
$CONST["EFEKT_1_HANGAR_NA_LS"] = 3;

//jaky ma provoz 1 letiste
$CONST["PROVOZ_LETISTE"] = 500;

//zasobovaci sklad
$CONST["STAVBY_zasobovaci_sklad"] = 500;

//municni sklad
$CONST["STAVBY_municni_sklad"] = 1000;

//zeleznicni prekladiste
$CONST["STAVBY_zeleznicni_prekladiste_suroviny"] = 1500;
$CONST["STAVBY_zeleznicni_prekladiste_palivo"] = 100;

//centralni skladiste
$CONST["STAVBY_centralni_skladiste"] = 1.05;

//tankovaci stanice
$CONST["STAVBY_tankovaci_stanice"] = 50;

//sklad pohonych hmot
$CONST["STAVBY_sklad_pohonych_hmot"] = 150;

//TYLOVE OPRAVNY
$CONST["STAVBY_TYLOVE_OPRAVNY_SLEVA_TANKY"] = 300;

//POLNI NEMOCNICE
$CONST["STAVBY_POLNI_NEMOCNICE_SLEVA_PECHOTA"] = 10;

//MASH
$CONST["STAVBY_MASH_SLEVA_PECHOTA"] = 5;

//minove pole
$CONST["STAVBY_MINOVE_POLE_ZTRATY_PECHOTA"] = 1.2;
$CONST["STAVBY_MINOVE_POLE_ZTRATY_TANKY"] = 1.1;
$CONST["STAVBY_MINOVE_POLE_POMOC_DO_OBRANY"] = 75;

//BUNKR
$CONST["STAVBY_BUNKR_ZTRATY_PECHOTA"] = 1.2;
$CONST["STAVBY_BUNKR_ZTRATY_TANKY"] = 1.2;
$CONST["STAVBY_BUNKR_POMOC_DO_OBRANY"] = 300;

//VOJENSKA POLICIE
$CONST["STAVBY_VOJENSKA_POLICIE_BONUS"] = 29;

//delostrelecka baterie
$CONST["STAVBY_DELOSTRELECKA_BATERIE_BONUS"] = 0.2;

//FLAK - protiletadlova baterie
// pricita se
$CONST["STAVBY_FLAK_BONUS"] = 30;

//katusa
$CONST["STAVBY_KATUSA_BONUS"] = 0.2;

//zemljanka - cim se pronasobi velikost ztrat
$CONST["STAVBY_ZEMLJANKA_OBRANA_BONUS"] = 0.75;
$CONST["STAVBY_ZEMLJANKA_PVO_BONUS"] = 0.75;

//kryt
$CONST["STAVBY_KRYT_PVO_BONUS"] = 0.5;

//zenijni brigada
$CONST['STAVBY_ZENIJNI_BRIGADA_POD_25'] = 1;
$CONST['STAVBY_ZENIJNI_BRIGADA_POD_50'] = 0;
$CONST['STAVBY_ZENIJNI_BRIGADA_POD_75'] = 0;
$CONST['STAVBY_ZENIJNI_BRIGADA_POD_100'] = 0;

//stab rozvedky
$CONST["STAVBY_STAB_BV"] = 25;

//protiletadlova brigada - cim vynasobi obrana flaku
$CONST["STAVBY_BRIGADA"] = 2;

for ($i = 0; $i < 30; $i++ ) {
	//defaultne nema zadna stavba omezni na infra
	$CONST['stavby_omezeni_infra'][$i] = 0;
}
// prekladiste
$CONST['stavby_omezeni_infra'][8] = 40;
// municni sklad
$CONST['stavby_omezeni_infra'][5] = 25;
// sklad pohonnych hmot
$CONST['stavby_omezeni_infra'][7] = 20;
// bunkr
$CONST['stavby_omezeni_infra'][11] = 25;
// tylove opravny
$CONST['stavby_omezeni_infra'][19] = 25;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//letecke akce
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
$pole_skodlive_letectvo = array(2,3,4,5,7,8);
//takticke bombardovani
//jaka je sance ze se zboura stavba - 0 znamena 100%
$CONST["LETECKA_AKCE_takticke_bombardovani_sance"] = 0;


//cilene bombardovani
//jaka je sance ze se zboura stavba - 0 znamena 100%
$CONST["LETECKA_AKCE_cilene_bombardovani_sance"] = 0;

//nalet na letiste
//jaka je sance ze se zboura stavba - 0 znamena 100%
$CONST["LETECKA_AKCE_nalet_na_letiste_sance"] = 0;

//nalet na komunikace
// body infa, ktere znici
$CONST["LETECKA_AKCE_nalet_na_komunikace_sance"] = 5;

//strategicke bombardovani
//jaka je sance ze se zboura stavba
$CONST["LETECKA_AKCE_strategicke_bombardovani_sance"] = 50;

//blizka letecka podpora
//procenta ztraty tanku
$CONST["LETECKA_AKCE_BLIZKA_LETECKA_PODPORA_TANKY_UCINEK"] = 0.2;

//stremhlavy nalet
//procenta ztrat
$CONST["LETECKA_AKCE_stremhlavy_nalet_PECHOTA_UCINEK"] = 0.2;
//procenta ztrat
$CONST["LETECKA_AKCE_stremhlavy_nalet_TANKY_UCINEK"] = 0.2;


////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
// podpora z domova
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
$pole_skodliva_podpora = array(2,3,6,9,10,11);
//vysadek
$CONST["PODPORA_VYSADEK_pechota"] = 0.2;
$CONST["PODPORA_VYSADEK_tanky"] = 0.2;

//ocernujici kampan
// vliv na prijmy BV
$CONST["PODPORA_ocernujici_kampan"] = -1;

//PROPAGANDA
// vliv na prijmy BV
$CONST["PODPORA_PROPAGANDA"] = 1;

//zasobovani ze vzduchu
//bonus kterym se nasobi zisky
$CONST["PODPORA_zasobovani_ze_vzduchu"] = 1.3;
$CONST["PODPORA_zasobovani_ze_vzduchu_palivo"] = 1.2;

// pruzkum  - negativni nasobivej bonus do obrany 
$CONST["PODPORA_PRUZKUM_BONUS"] = -0.15;

// maskovani 
//o kolik procent se snizi sance na zbourani letiste
$CONST["PODPORA_maskovani_letiste"] = 10;
// o kolik se snizi ztraty- rozsah 0-1
$CONST["PODPORA_maskovani_tanky"] = 0.2;

//sniperi
$CONST["PODPORA_SNIPERI_BONUS"] = 0.3;

//sabotaz - kolik procent je ztrata
$CONST["PODPORA_SABOTAZ"] = 0.3;

// VITEZNE BODY
$CONST["VB_dm_vyhra"] = 10;
$CONST["VB_dm_druhy"] = 7;
$CONST["VB_dm_treti"] = 4;
$CONST["VB_team_vyhra"] = 8;
$CONST["VB_team_clenstvi"] = 5;

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
// pocasi
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

$ne_letectvo_pocasi = array(8,13,14);

//o kolik se muze maximalne pocasi zmenit
$CONST['pocasi_zmena_max'] = 30;
//procenta ze se zmeni smer pocasi
$CONST['pocasi_zmena_smeru'] = 30;

//posileni leteckych akci pri jasnem pocasi
$CONST['pocasi_jasno_letecky_bonus'] = 1.2; 

//omezeni poctu leteckych akci
$omezeni_let_akci = array();
$omezeni_let_akci[1] = 1;$omezeni_let_akci[2] = 1;$omezeni_let_akci[3] = 1.5;
$omezeni_let_akci[4] = 1;$omezeni_let_akci[5] = 0.5;$omezeni_let_akci[6] = 1;
$omezeni_let_akci[7] = 0.5;$omezeni_let_akci[8] = 0;$omezeni_let_akci[9] = 1;
$omezeni_let_akci[10] = 0.5;$omezeni_let_akci[11] = 0.5;$omezeni_let_akci[12] = 0.5;
$omezeni_let_akci[13] = 0;$omezeni_let_akci[14] = 0;

//efekt na ekonomiku
$pocasi_zisky = array();
$pocasi_zisky[1] = 1.25;$pocasi_zisky[2] = 1.15;$pocasi_zisky[3] = 1.05;
$pocasi_zisky[4] = 1;$pocasi_zisky[5] = 0.95;$pocasi_zisky[6] = 1;
$pocasi_zisky[7] = 1;$pocasi_zisky[8] = 1;$pocasi_zisky[9] = 0.95;
$pocasi_zisky[10] = 1;$pocasi_zisky[11] = 1;$pocasi_zisky[12] = 0.9;
$pocasi_zisky[13] = 0.85;$pocasi_zisky[14] = 0.75;


$POCASI = array();
// rocni obdobi/ procenta / id pocasi
$POCASI['jaro'][8] = 1;
$POCASI['jaro'][14] = 2;
$POCASI['jaro'][22] = 3;
$POCASI['jaro'][11] = 4;
$POCASI['jaro'][5] = 5;
$POCASI['jaro'][12] = 6;
$POCASI['jaro'][13] = 7;
$POCASI['jaro'][3] = 8;
$POCASI['jaro'][7] = 9;
$POCASI['jaro'][6] = 10;

$POCASI['leto'][25] = 1;
$POCASI['leto'][22] = 2;
$POCASI['leto'][16] = 3;
$POCASI['leto'][15] = 4;
$POCASI['leto'][3] = 5;
$POCASI['leto'][12] = 7;
$POCASI['leto'][7] = 8;

$POCASI['podzim'][3] = 1;
$POCASI['podzim'][5] = 2;
$POCASI['podzim'][6] = 3;
$POCASI['podzim'][16] = 4;
$POCASI['podzim'][2] = 5;
$POCASI['podzim'][18] = 6;
$POCASI['podzim'][19] = 7;
$POCASI['podzim'][7] = 8;
$POCASI['podzim'][10] = 9;
$POCASI['podzim'][17] = 11;

$POCASI['zima'][3] = 2;
$POCASI['zima'][5] = 3;
$POCASI['zima'][7] = 4;
$POCASI['zima'][1] = 5;
$POCASI['zima'][16] = 9;
$POCASI['zima'][5] = 10;
$POCASI['zima'][17] = 11;
$POCASI['zima'][15] = 12;
$POCASI['zima'][11] = 13;
$POCASI['zima'][6] = 14;


// TABULKY

define("T_VITEZNE_BODY", "vitezne_body_" . date('Y'));
define("AKTUALNI_ROK", date('Y'));
