<?php if (!defined('BoB')) exit();
/**
 * @file
 * Tento soubor obsahuje user-defined error handler funkci pro Error class.
 *
 * Pokud tento soubor v aplikaci není nebo neobsahuje error_handler() funkci,
 * Error zpracuje funkci standardním způsobem. Pokud error_handler() funkce
 * existuje, je zavolána po standardním zpracování chyby, na konci volání
 * add_error().
 */


/**
 * User-defined error handler funkce.
 *
 * Tato funkce je volána po zpracování chyby, na konci funkce add_error().
 *
 * @param $error_class reference na objekt Error, který tento handler zavolal
 * @param $error_array pole reprezentující chybu
 *              [error_level, file, line, error_code, error_text, error_user_text]
 */
function error_handler(&$error_class, $error_array) {
        // tato funkce nesmi vyhodit zadnou chybu pomoci Error
        // ani volat handle_fatal_error()

        // custom error handler goes here
}



?>