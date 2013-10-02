<?php
/**
 * @file fm_fce.php
 * @brief Základní funkce pro ošetření vstupů formuláře
 * @author
 *
 * @addtogroup g_app
 * @{
 * @addtogroup g_fm_fce Funkce pro ošetření vstupů formulářů
 * @{
 *
 * @brief Funkce volané před zpracováním formuláře.
 *
 * Tyto funkce jsou volány před zpracováním formuláře pro jednotlivá pole
 * nebo celý formulář. Kontrolují vstupy polí formuláře, případně je mohou
 * i měnit. Pokud zpracování formuláře není prováděno přímo form managerem,
 * bývá zpracování formuláře provedeno na konci funkce
 * kontrolující celý formulář.
 *
 * Funkce dostávají od form manageru odkaz na vstup pole (respektive celého
 * formuláře, pokud jde o funkci kontrolující celý formulář) a jejich výstupem
 * je buď chybová hláška, která bude form managerem zobrazena, nebo hodnota
 * ekvivalentí false (ta znamená úspěch).
 *
 * Form manager formulář zpracuje jen, pokud každá z funkcí ošetřujících vstupy
 * vrátila úspěch.
 */


/**
 * @brief Kontrola data.
 *
 * Funkce zkontroluje správnost data.
 *
 * Pokud je datum správně, zároveň jej převede na formát CZ.
 *
 * Prázdné datum je považované za správné.
 *
 * @param $datum odkaz na hodnotu formulářového pole
 * @param $element celé pole popisující formulářové pole, které se kontroluje
 *
 * @return false pokud je datum v pořádku
 * @return chybovou hlášku, pokud datum v pořádku není
 */
function date_cz_iso_with_check(&$datum, $element) {
    global $LANGUAGE;

    if ($datum == '') {
        /* prazdne datum tahle funkce nezakazuje */
        return false;
    }

    if (!ereg('^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4}$', $datum)) {
        return $element['title'] . ": " . $LANGUAGE['fm_fce_date_wrong_cz_format'];
    }

    list ($d, $m, $r) = explode('.', $datum);

    if(checkdate($m, $d, $r)) {
          /* vse ok, prevod */
        $datum = datum_cz_iso($datum);
          return false;

      } else {
          return $element['title'] . ': ' . $LANGUAGE['fm_fce_date_incorrect_date'];

      }
}



/**
 * @brief Kontrola e-mailové adresy.
 *
 * @param string $email e-mailová adresa
 * @return bool syntaktická správnost adresy
 *
 * copyright Jakub Vrána, http://php.vrana.cz
 */
function check_email_vrana($email) {
    $atom = '[-a-z0-9!#$%&\'*+/=?^_`{|}~]'; // znaky tvořící uživatelské jméno
    $domain = '[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])'; // jedna komponenta domény
    return eregi("^$atom+(\\.$atom+)*@($domain?\\.)+$domain\$", $email);
}


/**
 * @brief Zkontroluje e-mailovou adresu.
 *
 * Pokud je e-mailová adresa ve tvaru "Jméno <e-mail@domena.top>", změní obsah
 * pole tak, aby obsahovalo jen část "e-mail@domena.top".
 *
 * Prázdný e-mail je považován za správný.
 *
 * @param $value odkaz na hodnotu formulářového pole
 * @param $element celé pole popisující formulářové pole, které se kontroluje
 *
 * @return false pokud je pole v pořádku
 * @return chybovou hlášku, pokud pole v pořádku není
 */
function check_email(&$value, $element) {
    global $LANGUAGE;

    if ($value == '') {
        /* prazdny e-mail tahle funkce nezakazuje */
        return false;
    }

    /* nejdriv urvani mezer a user friendly jmena */
    $value = preg_replace('/([^<>]*<)?([^<>]+)(>[^<>]*)?/', '\\2', $value);
    $value = trim($value);

    if ($value == '') {
        /* opravdu hodne spatny format e-mailu - spis textu kolem nej */
        return $element['title'] . ": " . $LANGUAGE['fm_fce_email_wrong_string'];
    }

    if (!check_email_vrana($value)) {
        /* spatny tvar e-mailu */
        return $element['title'] . ": " . $LANGUAGE['fm_fce_email_wrong_email'];
    }

    /* vse ok */
    return false;
}


/**
 * @brief Zkontroluje, zda vložené heslo je správně pro aktuálně přihlášeného uživatele
 *
 * @param $value string Ověřovaná hodnota
 */
function check_pwd($value){
        global $users_class, $LANGUAGE;
        if (!$users_class ->check_pwd($value)){
                return  $LANGUAGE['BAD_PWD'] . " ";
        }
}


/**
 * @}
 * @}
 */
?>