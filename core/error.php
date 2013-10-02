<?php
/**
 * @file
 * @brief Knihovna pro třídu, která zajišťuje zpracování chyb aplikace.
 *
 * @addtogroup g_framework
 * @{
 * @addtogroup g_error Zpracování chyb
 * @{
 *
 * @brief Knihovna pro třídu, která zajišťuje zpracování chyb aplikace.
 *
 *
 */





/**
 * Typy chyb.
 */
$ERROR_TYPES = array(   "Notice" => "Notice",
                                                "Warning" => "Warning",
                                                "Error" => "Error",
                                                "Fatal error" => "Fatal error"
                                                );

/**
 * Typ chyby - Notice
 */
$ERROR_LEVEL_NOTICE = $ERROR_TYPES["Notice"];

/**
 * Typ chyby - Warning
 */
$ERROR_LEVEL_WARNING = $ERROR_TYPES["Warning"];

/**
 * Typ chyby - %Error
 */
$ERROR_LEVEL_ERROR = $ERROR_TYPES["Error"];

/**
 * Typ chyby - Fatal
 */
$ERROR_LEVEL_FATAL = $ERROR_TYPES["Fatal error"];


/**
 * Třída pro zpracování chybových hlášení aplikace.
 *
 * Přijímá chybová hlášení modulů aplikace a v závislosti na
 * nastavení konstant $DEBUG, $ERRORS_LOG a $ERRORS_PRINT chyby
 * loguje a vypisuje na obrazovku.
 */
class Error {
        /** Pole pro uchovávání chyb */
        var $errors;

        /** jméno souboru pro logování chyb */
        var $log_file;

        /** Defaultní úroveň závažnosti chyby */
        var $default_error_level;

        /** Defaultní kód chyby */
        var $default_error_code;

        /** Defaultní text vypisovaný uživateli */
        var $default_error_user_text;

        /** Defaultní popis chyby */
        var $default_error_description_text;

        /** Defaultní jméno souboru */
        var $default_error_file;

        /** Defaultní číslo řádky */
        var $default_error_line;

        /** Typ chyby - NOTICE */
        var $ERROR_LEVEL_NOTICE;

        /** Typ chyby - WARNING */
        var $ERROR_LEVEL_WARNING;

        /** Typ chyby - ERROR */
        var $ERROR_LEVEL_ERROR;

        /** Typ chyby - FATAL */
        var $ERROR_LEVEL_FATAL;




        /**
         * @brief Konstruktor.
         *
         * Nastavuje hodnoty na defaultní.
         *
         * @param $log_file soubor (v adresáři $DIR_LOG) pro logování chyb
         */
        function Error($log_file = "error.log") {
                global $LANGUAGE, $ERROR_LEVEL_NOTICE, $ERROR_LEVEL_WARNING,
                        $ERROR_LEVEL_ERROR,     $ERROR_LEVEL_FATAL;

                $this->errors = array();
                $this->log_file = $log_file;

                // defaultni hodnoty popisu chyb
                $this->default_error_level = $ERROR_LEVEL_FATAL;
                $this->default_error_code = "UNKNOWN";
                $this->default_error_user_text = $LANGUAGE['DEFAULT_ERROR_TEXT'];
                $this->default_error_file = "N/A";
                $this->default_error_line = 0;
                $this->default_error_description_text = "Popis chyby nebyl vyplněn";

                // lokalni promenne pro typy chyb
                $this->ERROR_LEVEL_NOTICE = $ERROR_LEVEL_NOTICE;
                $this->ERROR_LEVEL_WARNING = $ERROR_LEVEL_WARNING;
                $this->ERROR_LEVEL_ERROR = $ERROR_LEVEL_ERROR;
                $this->ERROR_LEVEL_FATAL = $ERROR_LEVEL_FATAL;
        }



        /**
         * @brief Zavolá user-defined error handler
         *
         * Fail safe - nikdy nevyhodí další chybu pomocí Error.
         *
         * @param $error_array pole reprezentující chybu
         *              [error_level, file, line, error_code, error_text, error_user_text]
         */
        function call_error_handler($error_array){
                // nesmi vyhodit zadnou chybu pomoci Error, ani handle_fatal_error()
                global $DIR_CONFIG;

                if (!is_readable($DIR_CONFIG . 'error_handler.php')) {
                        // soubor neexistuje
                        //pridat chybu nejde, zacyklili bychom se
                        return;
                }

                include_once($DIR_CONFIG . 'error_handler.php');

                if (!function_exists('error_handler')) {
                        // funkce neexistuje
                        //pridat chybu nejde, zacyklili bychom se
                        return;
                }

                error_handler($this, $error_array);
        }


        /**
         * Zpracuje fatální chybu.
         *
         * Fail safe - nikdy nevyhodí další chybu pomocí Error.
         *
         * Na konci této funkce se volá finalize(), tato funkce se tedy nikdy nevrátí.
         *
         * @param $error_array pole reprezentující chybu
         *              [error_level, file, line, error_code, error_text, error_user_text]
         */
        function handle_fatal_error($error_array) {
                echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\" /></head><body>\n";

                $this->call_error_handler($error_array);

                echo "<h2>FATAL ERROR</h1>\n<p><strong>" . $this->error_text($error_array) . "</strong></p>\n";
                //echo "<h3>SCRIPT TERMINATING</h3>\n";
                echo "</body></html>\n";

                finalize(1);
        }



        /**
         * Zapíše chybu do logu.
         *
         *
         * @param $error_array pole reprezentující chybu
         *              [error_level, file, line, error_code, error_text, error_user_text]
         */
        function log_error($error_array) {
                global $DIR_LOG;
                $file = fopen($DIR_LOG . $this->log_file, 'ab');

                if ($file === false) {
                        $this->handle_fatal_error($this->make_error_array(
                                $this->ERROR_LEVEL_FATAL, __FILE__, __LINE__, 'ERROR_LOG_OPEN',
                                'Nepodařilo se otevřít log: ' . $DIR_LOG . $this->log_file,
                                null));
                }

                $log_message = date('Y-m-d H:i:s') . "  [" . $error_array['error_level'] . "]";
                $log_message .= str_repeat(' ', max(2, 36 - strlen($log_message))); // indent
                $log_message .= '(' . $error_array['file'] . ', line ' . $error_array['line'] . ')';
                $log_message .= '  '; //$log_message .= str_repeat(' ', min(2, 60 - sizeof($log_message))); // indent (typical 43 without  filename)
                $log_message .= $error_array['error_code'] . ' - ' . $error_array['error_text'] . "\n";

                if (fwrite($file, $log_message) === false) {
                        $this->handle_fatal_error($this->make_error_array(
                                $this->ERROR_LEVEL_FATAL, __FILE__, __LINE__, 'ERROR_LOG_WRITE',
                                'Nepodařilo zapsat do logu: ' . $DIR_LOG . $this->log_file,
                                null));
                }


                if (fclose($file) === false) {
                        $this->handle_fatal_error($this->make_error_array(
                                $this->ERROR_LEVEL_FATAL, __FILE__, __LINE__, 'ERROR_LOG_CLOSE',
                                'Nepodařilo zavřít log: ' . $DIR_LOG . $this->log_file,
                                null));
                }

                // we can't even log the error here!
        }



        /**
         * Vyrobí pole reprezentující chybu.
         *
         * null parametry nahradí defaultními hodnotami.
         *
         * @param $error_level úroveň závažnosti chyby
         * @param $error_file jméno souboru, kde chyba nastala
         * @param $error_line číslo řádky, kde chyba nastala
         * @param $error_code kód chyby
         * @param $description popis chyby
         * @param $user_description uživatelský popis chyby
         *
         * @return pole reprezentující chybu
         *              [error_level, file, line, error_code, error_text, error_user_text]
         */
        function make_error_array($error_level, $error_file, $error_line,
                                                        $error_code, $description, $user_description) {

                $new_error = array (
                                                'error_level' => $this->default_error_level,
                                                'file' => $this->default_error_file,
                                                'line' => $this->default_error_line,
                                                'error_code' => $this->default_error_code,
                                                'error_text' => $this->default_error_description_text,
                                                'error_user_text' => $this->default_error_user_text
                          );


                if ($error_level !== null) {
                        $new_error['error_level'] = $error_level;
                }

                if ($error_file !== null) {
                        $new_error['file'] = $error_file;
                }

                if ($error_line !== null) {
                        $new_error['line'] = $error_line;
                }

                if ($error_code !== null) {
                        $new_error['error_code'] = $error_code;
                }

                if ($description !== null) {
                        $new_error['error_text'] = $description;
                }

                if ($user_description !== null) {
                        $new_error['error_user_text'] = $user_description;
                }

                return $new_error;
        }



        /**
         * Vrátí text chyby v závislosti na nastavení $DEBUG.
         *
         * Pokud je $DEBUG true, vrátí popis chyby.
         * Pokud je $DEBUG false, vrátí uživatelský popis chyby.
         *
         * @param $error_array pole reprezentující chybu
         *              [error_level, file, line, error_code, error_text, error_user_text]
         * @return popis chyby
         */
        function error_text($error_array) {
                global $DEBUG;

                if ($DEBUG) {
                        return "[{$error_array['error_level']}] ({$error_array['file']}, {$error_array['line']}) "
                                . $error_array['error_code'] . " - " . $error_array['error_text'];
                } else {
                        return $error_array['error_code'] . " - " . $error_array['error_user_text'];
                }
        }




        /**
         * Vrací údaj o tom, zda má být chyba zapsána do logu.
         *
         * @param $error_level typ chyby
         *
         * @return true, pokud tento typ chyby má být logován
         */
        function error_is_logged($error_level) {
                global $ERRORS_LOG;

                switch ($error_level) {
                        case $this->ERROR_LEVEL_NOTICE:
                                return ($ERRORS_LOG & 1) > 0;
                        case $this->ERROR_LEVEL_WARNING:
                                return ($ERRORS_LOG & 2) > 0;
                        case $this->ERROR_LEVEL_ERROR:
                                return ($ERRORS_LOG & 4) > 0;
                        case $this->ERROR_LEVEL_FATAL:
                                return ($ERRORS_LOG & 8) > 0;
                        default:
                                $err = $this->make_error_array($this->ERROR_LEVEL_FATAL,
                                                __FILE__, __LINE__, 'ERROR_UNKNOWN_ERROR_TYPE',
                                                "Neznámý typ chyby: $error_level", null);

                                // this error gets logged regardless of settings
                                // - it's not safe to even ask about logging settings now!
                                $this->log_error($err);
                                $this->handle_fatal_error($err);
                }
                // unreachable
        }



        /**
         * Vrací údaj o tom, zda má být chyba vypsána na stránku.
         *
         * @param $error_level typ chyby
         *
         * @return true, pokud tento typ chyby má být vypsán na stránku
         */
        function error_is_printed($error_level) {
                global $ERRORS_PRINT;

                switch ($error_level) {
                        case $this->ERROR_LEVEL_NOTICE:
                                return ($ERRORS_PRINT & 1) > 0;
                        case $this->ERROR_LEVEL_WARNING:
                                return ($ERRORS_PRINT & 2) > 0;
                        case $this->ERROR_LEVEL_ERROR:
                                return ($ERRORS_PRINT & 4) > 0;
                        case $this->ERROR_LEVEL_FATAL:
                                return ($ERRORS_PRINT & 8) > 0;
                        default:
                                $err = $this->make_error_array($this->ERROR_LEVEL_FATAL,
                                                __FILE__, __LINE__, 'ERROR_UNKNOWN_ERROR_TYPE',
                                                "Neznámý typ chyby: $error_level", null);

                                // this error gets logged regardless of settings
                                // - it's not safe to even ask about logging settings now!
                                $this->log_error($err);
                                $this->handle_fatal_error($err);
                }
                // unreachable
        }


        /**
         * Přidání chybové hlášky.
         *
         * null položky budou nahrazeny defaultními hodnotami.
         *
         * @param $error_level úroveň závažnosti chyby
         * @param $error_file jméno souboru, kde chyba nastala (__FILE__)
         * @param $error_line číslo řádky, kde chyba nastala (__LINE__)
         * @param $error_code kód chyby - krátký (max. 30 znaků) textový řetězec
         *              identifikující chybu (logicky stejná chyba na více místech v kódu
         *              může mít stejný kód)
         * @param $description popis chyby
         * @param $user_description uživatelský popis chyby
         */
        function add_error($error_level, $error_file, $error_line, $error_code, $description, $user_description) {

                // make an array from the input, filling in default value where null is in the input
                $new_error = $this->make_error_array($error_level, $error_file,
                        $error_line, $error_code, $description, $user_description);

                // log error
                // later it might be too late to log - fatal error halts the script
                if ($this->error_is_logged($new_error['error_level'])) {
                        $this->log_error($new_error);
                }

                // halt on fatal error
                if ($new_error['error_level'] == $this->ERROR_LEVEL_FATAL) {
                        $this->handle_fatal_error($new_error);
                }
                // above function dies on fatal

                // add error to the list
                $this->errors[] = $new_error;

                // call custom error handler
                $this->call_error_handler($new_error);
        }




        /**
         * Vrací počet všech chyb.
         *
         * @return počet všech chyb
         */
        function count() {
                return count($this->errors);
        }


        /**
         * Vrací počet chyb k vytištění.
         *
         * @return počet chyb k vytištění
         */
        function count_printable() {
                $num_errors = 0;

                foreach ($this->errors as $i => $v) {
                        if ($this->error_is_printed($v['error_level'])) {
                                $num_errors++;
                        }
                }

                return $num_errors;
        }


        /**
         * Vrátí texty chyb, které mají být zobrazeny.
         *
         * Vrací texty chyb vybraných bitovým polem $ERRORS_PRINT.
         * Volba varianty textů závisí na nastavení $DEBUG.
         *
         * Pokud nenastala žádná chyba, vrací prázdné pole (ne null).
         *
         * @note Tato funkce se nikdy nedostane ke zpracování chyby typu FATAL -
         *              FATAL je zpracována handle_fatal_error(), která na konci volá
         *              finalize().
         *
         * @return pole textových popisů chyb
         * @return prázdné pole, pokud žádná chyba nenastala
         */
        function get_printable_errors() {
                $error_texts = array();

                foreach ($this->errors as $i => $v) {
                        if ($this->error_is_printed($v['error_level'])) {
                                $error_texts[] = $this->error_text($v);
                        }
                }

                // vratit texty
                return $error_texts;
        }
}



/**
 * @}
 * @}
 */
?>