<?php
/**
 * @file dblib.php
 * @brief Knihovna pro třídu, která zajišťuje práci s databází.
 * @author Michal Podzimek
 *
 * @addtogroup g_framework
 * @{
 * @addtogroup g_dblib DB Lib
 * @{
 *
 * Tento modul má na starosti podporu práce s databází.
 *
 *
 */

/**
 * @brief Obecná třída pro zajištění práce s databází.
 *
 *
 */
class DbLib {

        /** Jméno serveru na kterém běží databáze */
        var     $mHost          = 'localhost';
        /** Port na kterém je databázový server */
        var     $mPort          =  3306;
        /** Uživatelské jméno pro přihlášení k databázi */
        var     $mUser          = 'root';
        /** Heslo pro přihlášení k databázi */
        var     $mPassword      = '';
        /** Jméno databáze */
        var     $mDatabase      = 'db';
        /** Proměnná pro uložení čísla socketu vytvořeného pro připojení k db */
        var     $mSocket;
        /** Proměnná pro uložení výsledku dotazu do databáze */
        var     $mResult;
        /** Proměnná pro uložení jednoho řádku při zpracovávání dotazu do db */
        var     $mRow;
        /** Pocet zpracovanych dotazu do DB*/
        var		$query_count = 0;

        /**
         * @brief Funkce, která se volá při otevírání databáze
         *
         * Nastaví proměnnou mSocket na hodnotu 1.
         *
         */
        function OpenConnection()       {$this->mSocket  =  1;}
        /**
         * @name Obecné funkce
         *
         * Funkce obecné třídy pro připojení k db, zjišťují jen jestli je připojení
         * aktivní.
         * @{
         */
        function SelectDatabase()       {$this->IsConnected();}
        function DbQuery()                      {$this->IsConnected();}
        function Query()                        {$this->IsConnected();}
        function GetResult()            {$this->IsConnected();}
        function GetNumRows()           {$this->IsConnected();}
        function GetNextRecord()        {$this->IsConnected();}
        function GetField()                     {$this->IsConnected();}
        function GetLastId()            {$this->IsConnected();}
        function FreeResult()           {$this->IsConnected();}
        function CloseConnection()      {$this->IsConnected();}
        function SqlCorrection($string)        {$this->IsConnected();}
        function GetTableDef($table, $crlf)          {$this->IsConnected();}
        function GetTableContent($table, $crlf, $file_handle = "")      {$this->IsConnected();}
        function GetError()                     {}
        function __wakeup()                     {$this->OpenConnection();}
        function __sleep()                      {$this->CloseConnection();}
        function IsConnected()          {
                return $this->mSocket;
        }
        /** @} */
}


/**
 * @brief Rozšířená třída pro zajištění práce s MySQL databází.
 *
 *
 */
class DbLibMySQL extends DbLib {

        /**
         * @brief Inicializuje data pro připojení k databázi.
         *
         * @param $host Jméno databázového serveru
         * @param $user Uživatelské jméno pro připojení k db serveru
         * @param $password Heslo pro připojení k db serveru
         * @param $database Jméno databáze
         *
         * Nastaví hodnoty parametrů pro připojení k databázi.
         *
         */
        function init($host, $port, $user, $password, $database) {
                if ($host) {
                        $this->mHost = $host;
                }
                if ($port) {
                        $this->mPort = $port;
                }
                if ($user) {
                        $this->mUser = $user;
                }
                if ($password) {
                        $this->mPassword = $password;
                }
                if ($database) {
                        $this->mDatabase = $database;
                }
        }

        /**
         * @brief Funkce, která otevře spojení k databázi.
         * @return Číslo socketu
         *
         * Funkce se pokusí připojit k databázovému serveru, podle parametrů
         * zadaných v init(). V případě neúspěchu je volána funkce HandleError a
         * celý skript je ukončen, protože bez databáze nemá celá aplikace smysl.
         * Po připojení k serveru se pokusí otevřít databázi, v případě chyby
         * je opět skript ukončen. Poté skript nastaví používanou znakovou sadu na
         * UTF-8. Funkce vrátí číslo soketu otevřeného pro připojení do databáze.
         *
         */
        function OpenConnection() {
                parent::OpenConnection();
                // připojení k serveru
                $this->mSocket = @mysql_connect($this->mHost . ':' . $this->mPort,
                                                                                $this->mUser, $this->mPassword)
                        or ($this->HandleError($this->GetError()));
                // otevření databáze
                @mysql_select_db($this->mDatabase, $this->mSocket)
                        or $this->HandleError($this->GetError());

                // nastavení znakové sady na UTF-8
                $this->Query('SET NAMES utf8');

                return $this->mSocket;
        }

        /**
         * @brief Nastaví databází a připojí se k ní.
         *
         * @param $database Jméno databáze
         *
         * Funkce se pokusí připojit otevřít databázi, podle zadaného jména.
         * V případě neúspěchu je volána funkce HandleError a celý skript je
         * ukončen, protože bez databáze nemá celá aplikace smysl.
         *
         */
        function SelectDatabase($database = '')
        {
                parent::SelectDatabase();
                @mysql_select_db($database, $this->mSocket) or
                        $this->HandleError($this->GetError());
                $this->mDatabase = $database;
        }


        /**
         * @brief Vykoná nebuferovaný dotaz do databáze.
         *
         * @param $query Dotaz
         *
         * Funkce se pokusí vykonat zadaný dotaz. V případě neúspěchu zavolá
         * HandleError(), která ukončí skript.
         *
         */
        function DbQuery($query = '')
        {
        		$this->query_count++;
        		
                parent::DbQuery();
                ($this->mResult = @mysql_unbuffered_query($query, $this->mSocket)) or
                        $this->HandleError($this->GetError(), $query);
                return $this->mResult;

        }


        /**
         * @brief Vykoná dotaz do databáze.
         *
         * @param $query Dotaz
         * @return Vrací číslo MySQL resultu
         *
         * Funkce se pokusí vykonat zadaný dotaz. V případě neúspěchu zavolá
         * HandleError(), která ukončí skript.
         *
         */
        function Query($query = '')
        {
        		$this->query_count++;
        		
                parent::Query();
                ($this->mResult = mysql_query($query, $this->mSocket )) or
                        $this->HandleError($this->GetError(), $query);
                return $this->mResult;
        }


        /**
         * @brief Vrátí jednu hodnotu dat z databáze.
         *
         * @param $row Řádek ve vrácených datech
         * @param $coll Sloupec ve vrácených datech
         * @param $result Číslo MySQL resultu
         * @return Hodnota na zadaném místě v resultu
         *
         * Funkce se pokusí vrátit hodnotu na požadovaných souřadnící dat
         * vrácených z databáze. Pokud není zadán result použije se naposledy
         * vygenerovaný. V případě, že je result neplatný, je skript ukončen.
         *
         */
        function GetResult($row = 0, $coll = 0, $result = 0)
        {
                parent::GetResult();

                // pokud není zadán result, použije se naposledy vygenerovaný
                ($result) ? $temp_result = $result : $temp_result = $this->mResult;

                // pokud není result platný, je skript ukončen
                if (!is_resource($temp_result))
                        $this->HandleError('Not MySQL resource while getting results');

                return @mysql_result($temp_result,$row,$coll);
        }


        /**
         * @brief Vrátí počet řádku v MySQL resultu.
         *
         * @param $result Číslo MySQL resultu
         * @return Počet řádků v resultu
         *
         * Funkce se pokusí vrátit počet řádků resultu. Pokud není zadán result
         * použije se naposledy
         * vygenerovaný. V případě, že je result neplatný, je skript ukončen.
         *
         */
        function GetNumRows($result = 0)
        {
                parent::GetNumRows();

                // pokud není zadán result, použije se naposledy vygenerovaný
                ($result) ? $temp_result = $result : $temp_result = $this->mResult;

                // pokud není result platný, je skript ukončen
                if (!is_resource($temp_result))
                        $this->HandleError('Not MySQL resource while getting results');

                return @mysql_num_rows($temp_result);
        }


        function GetLastId()
        {
                parent::GetLastId();
                return mysql_insert_id();
        }
        /**
         * Vraci jednu hodnotu u dortazu typu sum, count...
         *
         * @param string $query dotaz
         */
        function GetSingleValue($query)
        {
                        //echo $query;
                        $res = $this->Query($query);
                        if (!$res)
                                return NULL;
                        $resarr = $this->GetFetchRow($res);
                        if (!$res)
                                return "";
                        $ret = $resarr[0];
                        $this->FreeResult($res);
                        return $ret;
        }


        function GetNextRecord($result = 0)
        {
                parent::GetNextRecord();
                ($result) ? $temp_result = $result : $temp_result = $this->mResult;

                if (!is_resource($temp_result))
                  $this->HandleError('Not MySQL resource while getting results');
                $this->mRow = @mysql_fetch_assoc($temp_result);
                return $this->mRow != FALSE;
        }


        function GetField($field = '')
        {
                parent::GetField();
                return $this->mRow[$field];
        }


        function FreeResult($result = 0)
        {
                parent::FreeResult();
                if ($result) $temp_result = $result;
                else                      $temp_result = $this->mResult;

                if (!is_resource($temp_result))
                  $this->HandleError('Not MySQL resource while getting results');
                @mysql_free_result($temp_result);
        }


        function SqlCorrection($string)
        {
                parent::SqlCorrection($string);
                return @mysql_real_escape_string($string);
        }


        function GetError()
        {
                $error = @mysql_error();
                parent::GetError();
                return $error;
        }

        function HandleError($error_text, $query = "")
        {
                if (parent::IsConnected())
                        $this->DbQuery('UNLOCK TABLES');

                global $error, $LANGUAGE;

				if ($query) {
					$query = "<br />\nChyba nastala v dotazu: " . $query;
				}
				
				$uri = $_SERVER['REQUEST_URI'];
				
				throw new Exception('Chyba databáze, popis chyby: ' . $error_text 
					. $query . "<br \>\nK chybě došlo na URI: " . $uri);
        }


        function CloseConnection()
        {
                $this->DbQuery('UNLOCK TABLES');
                parent::CloseConnection();
                @mysql_close($this->mSocket) or $this->HandleError('There is no valid MySQL-Link resource');
        }

        function GetFetchRow($result = 0)
        {
                parent::GetResult();
                ($result) ? $temp_result = $result : $temp_result = $this->mResult;

                if (!is_resource($temp_result))
                  $this->HandleError('Not MySQL resource while getting results');
                return @mysql_fetch_row($temp_result);
        }

        function GetAffectedRows($socket = 0)
        {
                parent::GetResult();
                ($socket) ? $temp_socket = $socket : $temp_socket = $this->mSocket;

                if (!is_resource($temp_socket))
                  $this->HandleError('Not MySQL link while getting results');
                return @mysql_affected_rows($temp_socket);
        }

        function GetFetchAssoc($result = 0)
        {
                parent::GetResult();
                ($result) ? $temp_result = $result : $temp_result = $this->mResult;

                if (!is_resource($temp_result))
                  $this->HandleError('Not MySQL resource while getting results');
                return @mysql_fetch_assoc($temp_result);
        }

        /**
        * @brief Získá informaci o struktuře tabulky.
        *
        * @param $table Jméno tabuky, o které se má získat informace o struktuře.
        * @param $crlf Konec řádky.
        * @return dotaz, který smaže tabulku pokud existuje a vytvoří danou tabulku,
        *               nebo false, pokud taková tabulka neexistuje
        */
        function GetTableDef($table, $crlf) {
                /* MySQL >= 3.23.20 */
                parent::GetTableDef($table, $crlf);
                $schema_create = "";
                $schema_create .= "DROP TABLE IF EXISTS $table;$crlf";
                $result = mysql_query("SHOW CREATE TABLE $table");
                if (!$result) {
                        return false;
                }
                $data = mysql_fetch_array($result);
                $schema_create .= $data[1] . ";$crlf$crlf";
                mysql_free_result($result);
                return $schema_create;
        }
        
		function GetQueryCount() {
			return $this->query_count;
		}
	
        /**
         * @brief Získá obsah tabulky ve formě insertů.
         *
         * omezení: pokud se při dumpu vrací výsledek jako proměnná všechny dotazy
         *              dohromady nesmí přesáhnout maximální velikost proměnné v php.
         * omezení: pokud je použito dumpováni do souboru, velikost jednoho dotazu
         *              ve formě insertu nesmí přesáhnout velikost maximální proměnné v php.
         *
         * @param $table Tabulka.
         * @param $crlf Lineseparator.
         * @param $file_handle Použitý, pokud se dumpuje přímo do souboru.
         * @return V případě, že se dumpuje do proměnné a ta se vrací,
         *              tak obsah tabulky ve formě insertu, nebo prázdný řetězec, pokud
         *              taková tabulka neexistuje pokud se dumpuje do souboru, tak vrací
         *              true, nebo false, podle toho, zda se operace zdařila.
         */
        function GetTableContent($table, $crlf, $file_handle = "")
        {
                parent::GetTableContent($table, $crlf, $file_handle = "");
                global $use_backquotes;
                global $rows_cnt;
                global $current_row;

                $schema_insert = "";
                $local_query = "SELECT * FROM $table";
                $result = mysql_query($local_query);
                if ($result != FALSE) {
                        $fields_cnt = mysql_num_fields($result);
                        $rows_cnt   = mysql_num_rows($result);
                        /* Checks whether the field is an integer or not */
                        for ($j = 0; $j < $fields_cnt; $j++) {
                                $field_set[$j] = mysql_field_name($result, $j);
                                $type = mysql_field_type($result, $j);
                                if ($type == 'tinyint' || $type == 'smallint' ||
                                        $type == 'mediumint' || $type == 'int' ||
                                        $type == 'bigint' /* || $type == 'timestamp' */) {
                                        $field_num[$j] = TRUE;
                                } else {
                                $field_num[$j] = FALSE;
                                }
                        }

                        /* Sets the scheme */
                        /* $schema_insert .= "INSERT INTO $table VALUES (";
                                \x08\\x09, not required */
                        $search = array("\x00", "\x0a", "\x0d", "\x1a");
                        $replace = array('\0', '\n', '\r', '\Z');
                        $current_row = 0;

                        while ($row = mysql_fetch_row($result)) {
                                $schema_insert .= "INSERT INTO $table VALUES (";
                                $current_row++;
                                for ($j = 0; $j < $fields_cnt; $j++) {
                                        if (!isset($row[$j])) {
                                                $values[] = 'NULL';
                                        } else if ($row[$j] == '0' || $row[$j] != '') {
                                                /* a number */
                                                if ($field_num[$j]) {
                                                        $values[] = $row[$j];
                                                }
                                                /* a string */
                                                else {
                                                        $values[] = "'" . str_replace($search, $replace,
                                                        $row[$j]) . "'";
                                                }

                                        } else {
                                                        $values[] = "''";
                                        }
                                }
                                $max = SizeOf($values);
                                for ($i = 0; $i < $max; $i++) {
                                        if ($i!=0) {
                                                $schema_insert .= ", ";
                                        }
                                        $schema_insert .= $values[$i] ;
                                }
                                $schema_insert .= ");$crlf";
                                unset($values);

                                if ($file_handle) {
                                        if (!fwrite($file_handle,$schema_insert)) {
                                                return false;
                                        }
                                        $schema_insert = "";
                                }
                        }
                }

                mysql_free_result($result);

                if ($file_handle) {
                        if (!$schema_insert) {
                                return false;
                        }
                        else {
                                return true;
                        }
                }

                return $schema_insert;
        }


        /**
         * @brief Provede dump databáze.
         *
         * omezení: pokud se při dumpu vrací výsledek jako proměnná všechny dotazy
         *              dohromady nesmí přesáhnout maximální velikost proměnné v php
         * omezení: pokud je použito dumpování do souboru, velikost jednoho dotazu
         *              ve formě insertu nesmi přesáhnout velikost maximální proměnné v php.
         * omezení: pokud používáte volbu download_imediate, nesmý být v době volání
         *              odeslána hlavička.
         *
         * @param $file_name Jméno souboru se zálohou, které bude vidět uživatel
         *              (když si stahuje zálohu s volbou immediate download).
         *
         * @param $save_file_name Jméno souboru pro dump. pokud je použita kombinace
         * voleb dump_to_file a immediate_download, tak se vytvoří záloha do toho
         * souboru, ten se pak odešle uživateli, a soubor se smaže. Pokud je použita
         * volba dump_to_file, a jméno není zadáno, vytvoří se nějaké dost dlouhé
         * náhodné.
         *
         * @param $dbname Pokud je různý od null, tak se toto jméno použije ve
         *              výpisu, místo $this->$mDatabase.
         * @param $with_structure dumpovat tabulky jen data, nebo i strukturu
         *              tabulek?
         * @param $lineseparator Oddělovač řádek.
         * @param $dump_to_file Pokud je zadána tato volba, je proveden dump do
         *              souboru. Viz $save_file_name.
         * @param $download_immediate Pokud je zadána tato volba, je soubor ihned
         *              odeslán v hlavičce ke klientovi. Viz omezení v popisu funkce.
         * @return $vysledek V připadě, že se dumpuje do proměnné a ta se vraci
         *              tak obsah tabulky ve formě insertu, nebo prázdný řetězec, pokud
         *              taková tabulka neexistuje, pokud se dumpuje do souboru, tak vrací
         *              true, nebo false, podle toho, zda se operace zdařila.
         */
        function DumpDB($file_name, $save_file_name = null, $dbname = null,
                $with_structure = false, $lineseparator = "\n", $dump_to_file = true,
                $working_dir=".",
                $download_immediate = true, $no_views = true)
        {
                $this->IsConnected();
                /* $file_name="backup.sql"; /* jmeno souboru pro DUMP */
                $crlf = $lineseparator; /* nastavení konce řádku */

                if ($dump_to_file && !$save_file_name){
                        $length = 32;
                        $pattern = "1234567890abcdefghijklmnopqrstuvwxyz";
                        for($i = 0; $i < $length; $i++)
                        {
                                $save_file_name .= $pattern{rand(0,35)};
                        }
                        $save_file_name .= ".bcp";
                }

                $save_file_name = $working_dir . "/" . $save_file_name;

                /* Pokud se má dumpovat do souboru, tak jej otevřeme */
                if ($dump_to_file){
                        $file_handle = fopen($save_file_name, "w");
                        if (!$file_handle){
                                return false;
                        }
                }
                else {
                        $file_handle = null;
                }
                /* echo "file opened\n"; */
                if (!$dbname){
                        $dbname = $this->mDatabase;
                }



                $tables_r = $this->Query('SHOW FULL TABLES');

                if(!$tables_r){
                        return false;
                }
                $num_tables = 0;
                /* zjistit si tabulky a případně vyházet pohledy */
                while ($table = $this->GetFetchRow($tables_r)){
                        if( $no_views && $table[1] == 'VIEW') {
                                // nic
                        }else {
                                $tables[] = $table;
                                $num_tables +=1;
                        }
                }



                $dump_buffer = "#MySQL DUMP of databaze: $dbname  (from " .
                        date("Y-m-d H:i:s") . ")$crlf";
                $dump_buffer .= "Charset: ". mysql_client_encoding($this->mSocket);


                if ($dump_to_file) {
                        if (!fwrite($file_handle,$dump_buffer)) {
                                return false;
                        }
                        $dump_buffer = "";
                }


                $table = NULL;
                for ($i = 0; $i < $num_tables; $i++) {
                        /* Zjistíme jméno i-té tabulky */
                        $table = $tables[$i][0];
                        /* Zapíšeme informaci o jménu tabulky */
                        $dump_buffer .= "$crlf#Table name: $table$crlf$crlf";

                        if ($with_structure) {
                                /* Zapíšeme strukturu tabulky */
                                $dump_buffer .= GetTableDef($table, "$crlf");
                        }

                        /* Zapíšeme informaci o tom, že budou následovat INSERTy */
                        $dump_buffer .= "$crlf#DATA$crlf";

                        if ($dump_to_file) {
                                if (!fwrite($file_handle, $dump_buffer)) {
                                        return false;
                                }
                                $dump_buffer = "";
                        }


                        $dump_buffer .= $this->GetTableContent($table, "$crlf",
                                $file_handle);

                        $dump_buffer .= "$crlf$crlf";

                        if ($dump_to_file) {
                                if (!fwrite($file_handle, $dump_buffer)) {
                                        return false;
                                }
                                $dump_buffer = "";
                        }
                }

                mysql_close();
                if ($dump_to_file){
                        if (!fclose($file_handle)) {
                                return false;
                        }
                }

                /* chmod($save_file_name,0777); */
                if ($download_immediate) {

                                $mime_type = "";

                                if (ereg('Opera(/| )([0-9].[0-9]{1,2})',
                                                $_SERVER['HTTP_USER_AGENT'])) {
                                        $UserBrowser = "Opera";
                                }
                                elseif (ereg('MSIE ([0-9].[0-9]{1,2})',
                                                $_SERVER['HTTP_USER_AGENT'])) {
                                        $UserBrowser = "IE";
                                }
                                else {
                                        $UserBrowser = '';
                                }
                                $mime_type = ($UserBrowser == 'IE' || $UserBrowser == 'Opera')
                                        ? 'application/octetstream' : 'application/octet-stream';
                                header('Content-Type: ' . $mime_type);
                                header('Content-Disposition: attachment; filename="' .
                                        $file_name.'"');
                                header('Accept-Ranges: bytes');
                                header("Cache-control: private");
                                header('Pragma: private');



                                if (!$dump_to_file) {
                                        header("Content-Length: " . (strlen($dump_buffer)));
                                        echo $dump_buffer;
                                }

                                else {
                                        $fp = fopen($save_file_name, "r");
                                        if (!$fp) {
                                                return false;
                                        }
                                        header("Content-Length: " . filesize($save_file_name));

                                        while ($buffer = fread($fp, 1024)) {

                                                echo $buffer;
                                        }
                                        fclose($fp);
                                }

                                /* header("Connection: close"); */
                        if ($dump_to_file) {
                                unlink($save_file_name);
                        }
                }



        }


}

/**
 * @}
 * @}
 */

?>