<?php
/**
 * @file
 * @brief Knihovna pro funkce potřebné pro framework
 * @author Michal Podzimek
 *
 * @addtogroup g_framework Framework
 * @{
 * @addtogroup g_library Knihovna funkcí
 * @{
 *
 * @brief Knihovna pro funkce potřebné pro framework. Ošetření vstupu, vypnutí
 * register globals, převody data, ...
 *
 *
 */

/**
 * @brief Funkce, která ruší efekt zapnuté direktivy register_globals
 *
 * Funkce zavolá unset() na všechny proměnné, které vznikly zaregistrováním
 * nějaké proměnné z globálních polí
 *
 */
function unregister_globals() {
        if (!ini_get('register_globals')) {
                return;
        }

        /* Detekce utoku na globals */
        if (isset($_REQUEST['GLOBALS']) || isset($_FILES['GLOBALS'])) {
                global $error, $LANGUAGE;

                $error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                        'CORE_GLOBALS_OVERWRITE',
                        'GLOBALS overwrite attempt detected',
                        $LANGUAGE['CORE_GLOBALS_OVERWRITE']);
        }

        // Variables that shouldn't be unset
        $noUnset = array('GLOBALS',  '_GET',
                                        '_POST',    '_COOKIE',
                                        '_REQUEST', '_SERVER',
                                        '_ENV',     '_FILES');

        $input = array_merge($_GET,     $_POST,
                        $_COOKIE,  $_SERVER,
                        $_ENV,     $_FILES,
                        isset($_SESSION) && is_array($_SESSION) ? $_SESSION : array());

        foreach ($input as $k => $v) {
                if (!in_array($k, $noUnset) && isset($GLOBALS[$k])) {
                        unset($GLOBALS[$k]);
                }
        }
}


/**
 * @brief Převede datum z českého formátu na formát ISO
 *
 * Z českého formátu 12.5.2007 udělá ISO formát 2007-05-12. Pokud vstupní
 * hodnotu nelze převést, tak jí vrátí nezměněnou. Dá se to využít tak, že
 * pokud se funkci předá datum v ISO formátu, tak se vrátí nezměné, tak jak
 * se od návratové hodnoty této funkce předpokládá.
 *
 * @param $datum Datum v českém formátu
 * @return Datum v ISO formátu
 */
function datum_cz_iso($datum) {
        if (strstr($datum, ".")) {
                $datum = explode(".", $datum);
                if(strlen($datum[1]) < 2)
                $datum[1] = '0' . $datum[1];
                if(strlen($datum[0]) < 2)
                $datum[0] = '0' . $datum[0];

                return $datum[2] . "-" . $datum[1] . "-" . $datum[0];
        }
        else
        return $datum;
}


/**
 * @brief Převede datum z ISO formátu do českého formátu
 *
 * Z ISO formátu 2007-05-12 udělá český formát 12.5.2007. Pokud vstupní
 * hodnotu nelze převést, tak jí vrátí nezměněnou. Dá se to využít tak, že
 * pokud se funkci předá datum v českém formátu, tak se vrátí nezměné, tak jak
 * se od návratové hodnoty této funkce předpokládá.
 *
 * @param $datum Datum v ISO formátu
 * @return Datum v českém formátu
 */
function datum_iso_cz($datum) {
        if (strpos($datum,"-")) {
                $datum = explode("-", $datum);
                return $datum[2] . "." . $datum[1] . "." . $datum[0];
        }
        else
        return $datum;
}

/**
 * @brief
 * Z timestampu 2007-04-29 17:36:33 prevede na 29.4.2007 17:36:33
 *
 * @see datum_cz_iso()
 */
function timestamp_iso_cz($datum) {
        if (strpos($datum," ")) {
                $datum = explode(" ", $datum);
                return datum_iso_cz($datum[0]) . " " . $datum[1];
        }
        else
        return $datum;
}

/**
 * @brief Stejné jako datum_iso_cz, akorát se předává referencí
 *
 * @see datum_cz_iso()
 */
function datum_cz_iso_ref(&$datum) {
        $datum = datum_cz_iso($datum);
}




/**
 * @brief Stejné jako datum_iso_cz_ref, akorát se předává referencí
 *
 * @see datum_iso_cz()
 */
function datum_iso_cz_ref(&$datum) {
        $datum = datum_iso_cz($datum);
}

/**
 * @brief Zjistí jestli je v poli proměnná, pokud je nacte ji, jinak vrati null.
 * tato funkce je zde aby odstranila warningy v klasickem php pouziti (if($_GET[xy]...).
 *
 * @param $index Index, ktery  ma by bezpecne nacten
 * @param $array Pole, ke kteremu se index vztahuje
 *
 * @return hodnotu položky v poli
 * @return null pokud položka $index neexistuje
 * @return null pokud $array není pole
 */
function array_get_index_safe($index, $array) {
        if (is_array($array) && array_key_exists($index, $array)) {
                return $array[$index];
        }
        return null;
}
/** Vrací řetězec bezpečný pro vložení do sql dotazu.
  * Escapuje všechny znaky s významem v SQL,
  * předtím případně zruší efekt magic quotes.
  * @param $str řetězec, který má být upraven
  * @param $strip pokud je true, funkce odstraní případné magic quotes;
  *             pokud je funkce volána spolu s htmlsafe(), nastavte na false
  * @return řetězec bezpečný pro vložení do SQL dotazu
  */
function sqlsafe($str, $strip = true) {
        if (get_magic_quotes_gpc() && $strip) {
                $str = stripslashes($str);
        }

        return mysql_real_escape_string($str);
}

/**
 * Vrací řetězec bezpečný pro vložení do kódu HTML stránky.
 * Escapuje všechny HTML znaky, entity apod. Výsledný text se v HTML
 * zobrazí přesně tak, jak vypadá vstupní string v textové podobě.
 *
 * @param $str řetězec, který má být upraven
 * @param $strip pokud je true, funkce odstraní případné magic quotes;
 *                      pokud je funkce volána spolu s sqlsafe(), nastavte na false
 * @return řetězec bezpečný pro vložení do kódu HTML stránky
 */
function htmlsafe($str, $strip = true) {
        if (get_magic_quotes_gpc() && $strip) {
                $str = stripslashes($str);
        }

        return htmlspecialchars($str, ENT_QUOTES);
}

/**
 * Pokud jsou magic quotes zapnuté, odstraní jejich efekt.
 *
 * @param $str řetězec pro zpracování
 * @return řetězec zbavený efektu magoc quotes, pokud byly zapnuté
 */
function my_stripslashes($str) {
        if (get_magic_quotes_gpc()) {
                return stripslashes($str);
        }
        return $str;
}


/**
 * @}
 * @}
 */
?>