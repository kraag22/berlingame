<?php
/**
 * @file
 * @brief Language - core
 * @author Team
 *
 * @addtogroup g_lang Jazykové konstanty
 * @{
 *
 * @name Jazyk - jádro
 * Jazykové konstanty specifické pro jádro
 *
 *
 * Texty v aplikaci, které se zobrazí uživateli, by měly být
 * všechny v jazykových konstantách.
 * @{
 *
 */



// generic error texts
$LANGUAGE['ERROR_CAPTION'] = "Chyba aplikace";
$LANGUAGE['ERROR_HEADER'] = "Při běhu aplikace nastala chyba:";
$LANGUAGE['DEFAULT_ERROR_TEXT'] = "Při běhu aplikace nastala chyba.";
$LANGUAGE['ERROR_INFO'] = "Je možné, že se aplikace nebude chovat normálně. Omlouváme se. Administrátoři byli o chybě automaticky informováni.";
$LANGUAGE['GENERIC_FATAL_ERROR_USR'] = "V aplikaci nastala zásadní chyba. Prosím, kontaktujte správnce ";

$LANGUAGE['GENERIC_ERROR_ERROR_USR'] = "V aplikaci nastala chyba která může zapříčinit abnormální chování aplikace. ";

// error user texts
$LANGUAGE['ERROR_DB_COMMUNICATION'] = "Nastala chyba při komunikaci s databází.";
$LANGUAGE['ERROR_INVALID_SKIN'] = "Nastaven neplatný skin, bude použit defaultní.";
$LANGUAGE['ERROR_FILE_TRANSFER_FAILED'] = ".";

// page and layout errors
$LANGUAGE['PAGE_BADREDIR_PRE'] = "Špatné přesměrování - pokračujte prosím na adrese ";
$LANGUAGE['PAGE_BADREDIR_POST'] = ".";
$LANGUAGE['PAGE_CSS_FILE_DOESNT_EXIST'] = "Nepodařilo se nahrát soubor se stylem stránky";
$LANGUAGE['PAGE_SCRIPT_INCLUDE_PARAM'] = "Pokus o vložení skriptu špatným parametrem";
$LANGUAGE['PAGE_SCRIPT_INCLUDE_PARAM_USR'] = "Došlo k pokusu o vložení skriptu nevhodným způsobem.";
$LANGUAGE['PAGE_SCRIPT_INCLUDE_SRCFILE'] = "Pokus o vložení souboru se skriptem který neexistuje";
$LANGUAGE['PAGE_DIS_INCLUDE_SRCFILE'] = "Pokus o vložení souboru se skriptem typu DirectInputScript který neexistuje";
$LANGUAGE['PAGE_DIS_INCLUDE_SRCFILE_USR'] = "Nepodařilo se vložit některé skripty. Aplikace se možná bude chovat anomálně";
$LANGUAGE['PAGE_BAD_REDIRECT'] = "Redirect po začátku výstupu na obrazovku, URI: ";
$LANGUAGE['PAGE_BAD_SET_SKIN'] = 'Funkce set_skin() byla volána před funkcí set_skin_subdir().';
$LANGUAGE['PAGE_MENU_ITEM_NOT_ARRAY'] = "Nesprávný parametr funkce na přidání položky menu (má být pole)";
$LANGUAGE['PAGE_PRINTER_FILE_NOT_RESENT'] = "Soubor s printerem neexistuje nebo nejde číst";
$LANGUAGE['PAGE_PRINTER_FILE_NOT_RESENT_USR'] = $LANGUAGE['GENERIC_FATAL_ERROR_USR'];
$LANGUAGE['PAGE_PRINTER_NOT_RESENT_USR'] = $LANGUAGE['GENERIC_FATAL_ERROR_USR'];
$LANGUAGE['PAGE_PRINTER_NOT_RESENT'] = "PHP nezná definici třídy printer po vložení souboru printer.php";
$LANGUAGE['PAGE_FCALL_DEFINITION_PROBLEM'] = "Nepodařilo se připojit volání funkce. Funkce pravděpodobně není deklarovaná";
$LANGUAGE['PAGE_FCALL_DEFINITION_PROBLEM_USR'] = "Nepodařilo se připojit volání funkce.";
$LANGUAGE['PAGE_ELEMENT_CLASS_NOT_EXISTS'] = "Pokus o pripojení objektu, který není definován";
$LANGUAGE['PAGE_ELEMENT_CLASS_NOT_EXISTS_USR'] = "Chyba při připojování části stránky. Stránka možná nebude zobrazena celá, nebo zcela korektně";
$LANGUAGE['PAGE_BAD_FCALL'] = "Definice funkce pro volani page->call nebyla registrována : ";
$LANGUAGE['PAGE_BAD_FCALL_USR'] = $LANGUAGE['PAGE_ELEMENT_CLASS_NOT_EXISTS_USR'];
$LANGUAGE['PAGE_SKIN_NOT_FOUND'] = "Nepodařilo se najít skin, nebo skin není v DB. Volím default. Požadovaný skin: ";


// file files.php. Chyby jsou většinou interní, uživatel o nich nemusí vědět.
$LANGUAGE['FILES_REMOVE_DIR_NOT_DIR'] = "Odstraňovaná položka není adresářem";
$LANGUAGE['FILES_REMOVE_DIR_NOT_DIR_USR'] = "";

$LANGUAGE['FILES_CANNOT_REMOVE_DIR'] = "Nepodařilo se odstranit adresář. (Práva k souboru? Práva k nadřazenému adresáři?). Jméno: ";
$LANGUAGE['FILES_CANNOT_REMOVE_DIR_USR'] = "";
$LANGUAGE['FILES_CANNOT_REMOVE_FILE'] = "Nepodařilo se odstranit soubor. (Práva k souboru? Práva k nadřazenému adresáři?) Jméno: ";
$LANGUAGE['FILES_CANNOT_REMOVE_FILE_USR'] = "";

$LANGUAGE['FILES_MOVE_UPLOADED_ERROR'] = "Chyba vykonávání move_uploaded_files";
$LANGUAGE['FILES_MOVE_UPLOADED_ERROR_USR'] = $LANGUAGE['GENERIC_ERROR_ERROR_USR'] . "Nepodařilo se nahrát soubor";

$LANGUAGE['FILES_CANNOT_CHMOD'] = "Chyba vykonávání chmod Soubor: ";
$LANGUAGE['FILES_CANNOT_CHMOD_USR'] = $LANGUAGE['GENERIC_ERROR_ERROR_USR'] . "Nepodařilo se nastvait práva souboru";

$LANGUAGE['FILES_FILE_TO_LARGE'] = "Uploadovaný soubor je příliš velký";
$LANGUAGE['FILES_FILE_TO_LARGE_USR'] = $LANGUAGE['FILES_FILE_TO_LARGE'];

$LANGUAGE['FILES_BAD_FILE_TYPE'] = "Soubor je typu, který není mezi přijímanými. Přijímány jsou: ";
$LANGUAGE['FILES_BAD_FILE_TYPE_USR'] = "Soubor nebyl přijat, protože je špatného typu. Přijmuty mohou být: ";

$LANGUAGE['FILES_BAD_FILE'] = "Soubor nebyl přijat. Buď nebyl odeslán, nebo nebyl zpracován (např. kvůli nastevení MAX_UPLOADED_SIZE nebo MAX_POST_SIZE v php.ini)";
$LANGUAGE['FILES_BAD_FILE_USR'] = "Soubor nebyl zpracován";

$LANGUAGE['FILES_IMAGE_TYPE_NOT_SUPP'] = "Pokus o zpracování obrázku nepodporovaného typu";
$LANGUAGE['FILES_IMAGE_TYPE_NOT_SUPP_USR'] = $LANGUAGE['FILES_IMAGE_TYPE_NOT_SUPP'];

$LANGUAGE['CORE_GLOBALS_OVERWRITE'] = "Pokus o přepsání GLOBALS.";

/* Konstanty pro form_manager */
$LANGUAGE['FM_RELOAD_FAIL'] = "Nepovolený reload odeslání formuláře";
$LANGUAGE['FM_NENI_VYPLNENO_POLE'] = "není vyplněno pole ";
$LANGUAGE['FM_PREKROCENA_DELKA1'] = "hodnota v poli ";
$LANGUAGE['FM_PREKROCENA_DELKA2'] = " překračuje povolenou délku";
$LANGUAGE['FM_REGEXP_FAIL1'] = "Pole ";
$LANGUAGE['FM_REGEXP_FAIL2'] = " není vyplněno správně";


// kvuli funkci common translate national chars
$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]["ě"] = "e";
$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]["š"] = "s";
$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]["č"] = "c";
$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]["ř"] = "r";
$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]["ž"] = "z";
$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]["ý"] = "y";
$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]["á"] = "a";
$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]["í"] = "i";
$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]["é"] = "e";
$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]["ů"] = "u";
$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]["ú"] = "u";
$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]["ď"] = "d";
$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]["ť"] = "t";
$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]["ň"] = "n";
$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"][" "] = "_";

$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]["Ě"] = "E";
$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]["Š"] = "S";
$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]["Č"] = "C";
$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]["Ř"] = "R";
$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]["Ž"] = "Z";
$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]["Ý"] = "Y";
$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]["Á"] = "A";
$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]["Í"] = "I";
$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]["É"] = "E";
$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]["Ů"] = "U";
$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]["Ú"] = "U";
$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]["Ď"] = "D";
$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]["Ť"] = "T";
$LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]["Ň"] = "N";

/**
 * @}
 * @}
 */
?>