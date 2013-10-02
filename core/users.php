<?php
/**
 * @file users.php
 * @brief Knihovna pro třídu, která zajišťuje vše okolo přihlašování uživatelů
 * @author Michal Podzimek
 *
 * @addtogroup g_framework
 * @{
 * @addtogroup g_users Uživatelé
 * @{
 *
 * Knihovna se stará o přihlášení uživatele, udržení informací o přihlášeném
 * uživateli, o předávání informací o uživateli, odhlášení uživatele, vytvoření
 * nového uživatele, změnu informací o uživateli, ...
 * Informace o přihlášení uživatele se předávají v $_SESSION
 *
 */

require_once("${DIR_CORE}/user_utils.php");

/**
 * @brief Třída pro práci s objektem uživatele
 *
 * Třída má na starosti ošetření práce s účtem uživatele. V konstruktoru zjistí
 * jestli je nějaký uživatel přihlášen, pokud ne tak nastaví účet nepřihlášeného
 * uživatele (guest). Život přihlášeného uživatele na stránce funguje tak, že
 * funkce log_user() provede přihlášení uživatele. Pokud je úspěšné, tak se info
 * o uživateli uloží do SESSION a pak při otevření další stránky se z
 * konstruktoru volá funkce is_logged(), která zjistí jestli je uživatel
 * přihlášen a jestli jeho přihlašovací údaje souhlasí. U dalších funkcí, kde
 * se zjišťuje jestli je uživatel přihlášen se už nesahá do databáze a vycházi
 * se z dat zjišťěných při prvním volání funkce is_logged. To eleminuje situaci,
 * kdy by mohlo v průběhu vykonávání skriptu dojít ke smazání uživatele a skript
 * by z půlky fungoval a z půlky ne. Odhlášení uživatele probíhá přes funkci
 * logout_user(), která smaže údaje se SESSION a nastaví účet guesta.
 *
 */
class Users {
        /** název pohledu, kde jsou uloženy informace o aktivních uživatelích */
        var $db_viewname = 'users_active_view';
        /** název tabulky, ve které jsou uloženy informace o všech uživatelích */
        var $db_tablename = 'users_sys';
        /** název sloupce s významem id */
        var $db_col_id = 'id';
        /** název sloupce s významem loginu */
        var $db_col_login = 'login';
        /** název sloupce s významem hesla */
        var $db_col_pwd = 'pass';
        /** ukazatel na třídu pro přístup do databáze */
        var $db;
        /** pole atributů uživatele */
        var $attributes;
        /** id uživatele */
        var $user_id;
        /** true, pokud je uživatel správně přihlášen*/
        var $prihlasen = null;
        /** nazev promene, pres kterou se predavaji reference*/
        var $ref_nazev = 'rid';
        /** nazev cookie, ktera zabranuje pripocitani reference */
        var $ref_test_nazev = 'rpriv';

        /**
         * @brief Inicializuje třídu pro práci s uživatelem
         * @param $db Ukazatel na třídu pro přístup do databáze
         * @param $db_tablename Název tabulky, ve které jsou uloženy informace
         *                                              o uživatelích
         * @param $db_col_id Název sloupce s významem id
         * @param $db_col_login Název sloupce s významem loginu
         * @param $db_col_pwd Název sloupce s významem hesla
         *
         * Nastaví předané parametr do vnitřních proměnných a zavolá funkci
         * is_logged(), která zjistí ze $_SESSION, jestli je nějaký uživatel
         * přihlášen. Pokud ano načte údaje o něm.
         *
         */
        function Users($db, $db_tablename = "", $db_col_id = "",
                                   $db_col_login = "", $db_col_pwd = "") {
                $this->db = $db;
                if ($db_tablename) {
                        $this->db_tablename = $db_tablename;
                }
                if ($db_col_id) {
                        $this->db_col_id = $db_col_id;
                }
                if ($db_col_login) {
                        $this->db_col_login = $db_col_login;
                }
                if ($db_col_pwd) {
                        $this->db_col_pwd = $db_col_pwd;
                }
                if (!$this->is_logged()) {
                        $this->set_guest();
                }
        }

        /**
         * @brief Pokusí se přihlásit uživatele
         * @param $login Uživatelský login
         * @param $pwd Uživatelské heslo
         * @return true pokud se uživatel úspěšně přihlásí
         * @return false pokud se nepodařilo uživatele přihlásit
         *
         * Funkce zjistí z databáze heslo podle zadaného loginu, porovná ho
         * se zadaným heslem. Pokud z databáze není žádné heslo vráceno znamená
         * to, že takový uživatel neexistuje, pokud se hesla neshodují, bylo
         * zadané špatné heslo. Pokud je heslo správné uloží se informace o
         * uživateli do $_SESSION a zjistí se z db údaje o uživateli.
         *
         */
        function log_user($login, $pwd) {
                global $_SESSION, $HASH_PASSWORDS;

                /* pokud je zadáno uživatelské jméno guest, tak vrať chybu neexistující
                 * login
                 */
                if ($login == 'guest') {
                        global $error, $LANGUAGE;
                        $error->add_error($error->ERROR_LEVEL_NOTICE, __FILE__, __LINE__,
                                'USERS_LOGIN_NO_EXISTS', 'Uživatelský účet "guest" neexistuje.',
                                null);
                        return false;
                }


                $pwd_hashed = $this->hash_pwd($pwd);
                /* zjištění id a hesla uživatele podle zadaného loginu */
                $this->db->Query("SELECT `" . $this->db_col_pwd . "`, `" .
                                                        $this->db_col_id . "`
                                                  FROM `" . $this->db_viewname . "`
                                                  WHERE `" . $this->db_col_login . "`='$login'");

                /* zadaný login v db neexistuje */
                if (!($row = $this->db->GetFetchRow())) {
                        global $error, $LANGUAGE;
                        $error->add_error($error->ERROR_LEVEL_NOTICE, __FILE__, __LINE__,
                                'USERS_LOGIN_NO_EXISTS', "Uživatelský účet $login neexistuje.",
                                null);
                        return false;
                }

                /* uložení hodnot z databáze do lokálních proměnných
                 */
                $dbpwd = $row[0];
                $dbuserid = $row[1];

                /* heslo nesouhlasí */
                if ($dbpwd != $pwd_hashed) {
                        global $error, $LANGUAGE;
                        $error->add_error($error->ERROR_LEVEL_NOTICE, __FILE__, __LINE__,
                                'USERS_PWD_FAIL', "Uživatel $login zadal špatné heslo.",
                                null);
                        return false;
                }

                /* heslo je správné, data se uloží do $_SESSION */
                $_SESSION['users']['login'] = $login;
                $_SESSION['users']['pwd'] = $pwd;
                $_SESSION['users']['id'] = $dbuserid;
                $_SESSION['users']['ip'] = getenv("REMOTE_ADDR");
                $this->user_id = $dbuserid;
                $this->prihlasen = true;

                /* přesun posledního času přihlášení do session */
                $this->db->Query("SELECT `last_login_time`
                                                  FROM`" . $this->db_tablename . "`
                                                  WHERE `" . $this->db_col_id . "`='" .
                                                        $this->user_id . "'");
                $_SESSION['last_login_time'] = $this->db->GetResult();

                /* uložení času přihlášení */
                $this->db->Query("UPDATE `" . $this->db_tablename . "`
                                                  SET `last_login_time`=NOW(),
                                                  `login_count` = `login_count` + 1,
                                                  `ip_adresa_posledni` = '".sqlsafe($_SERVER['REMOTE_ADDR'])."'
                                                  WHERE `" . $this->db_col_id . "`='" .
                                                        $this->user_id . "'");

                /* zjištění atributů uživatele a uložení do proměnných třídy */
                $this->load_attributes();
                return true;
        }

        /**
         * @brief Zjistí, jestli je uživatel přihlášený
         * @return true uživatel je správně přihlášený
         * @return false uživatel není přihlášený, nebo mu byl přístup zrušen
         *
         * V případě, že proměnná $prihlasen je null, tak ještě neproběhl žádný
         * pokus o přihlášení uživatele a jsou tedy zjištěna a ověřena data ze
         * $_SESSION. Poté je nastaveno $prihlasen buď na false nebo true. Při
         * příštím volání funkce je rovnou vrácena hodnota proměnné $prihlasen
         *
         */
        function is_logged() {
                global $_SESSION;

                if ($this->prihlasen === null) {
                        $session_login = "";
                        if (array_key_exists('users', $_SESSION) &&
                                array_key_exists('login', $_SESSION['users'])) {
                                $session_login = $_SESSION['users']['login'];
                        }
                        else {
                                $session_login = "";
                                return false;
                        }

                        if (array_key_exists('users', $_SESSION) &&
                                array_key_exists('pwd', $_SESSION['users'])) {
                                $session_pwd = $_SESSION['users']['pwd'];
                        }
                        else {
                                $session_pwd = "";
                        }
                        if (array_key_exists('users', $_SESSION) &&
                                array_key_exists('id', $_SESSION['users'])) {
                                $session_user_id = $_SESSION['users']['id'];
                        }
                        else {
                                $session_user_id = "";
                        }
                        if (array_key_exists('users', $_SESSION) &&
                                array_key_exists('ip', $_SESSION['users'])) {
                                $session_user_ip = $_SESSION['users']['ip'];
                        }
                        else {
                                $session_user_ip = "";
                        }

                        $this->db->Query("SELECT `" . $this->db_col_pwd . "`, `"
                                                                . $this->db_col_id . "`
                                                          FROM `" . $this->db_viewname . "`
                                                          WHERE `" . $this->db_col_login . "`='"
                                                                . $session_login . "'");
                        /* ip adresa uživatel se změnila, může se jednat o pokus zneužít
                         * session id, tak uživatele odhlásíme a nahlásíme chybu
                         */
                        /*if ($session_user_ip != getenv("REMOTE_ADDR")) {
                                global $error, $LANGUAGE;
                                $error->add_error($error->ERROR_LEVEL_ERROR, __FILE__,
                                        __LINE__, 'USERS_IP_CHANGED',
                                        "Uživateli se změnila IP adresa, byl odhlášen.",
                                        $LANGUAGE['USERS_IP_CHANGED']);
                                $this->prihlasen = false;
                                unset($_SESSION['users']);
                                return false;
                        }*/
                        /* login, který je v session neexistuje, vyvoláme chybu a uživatele
                         * odhlásíme
                         */
                        if (!($row = $this->db->GetFetchRow())) {
                                global $error, $LANGUAGE;
                                $error->add_error($error->ERROR_LEVEL_NOTICE, __FILE__,
                                        __LINE__, 'USERS_LOGIN_NO_EXISTS',
                                        "Uživatelský účet $session_login neexistuje.", null);
                                $this->prihlasen = false;
                                unset($_SESSION['users']);
                                return false;
                        }
                        $dbpwd = $row[0];
                        $dbuserid = $row[1];


                        $pwd_hashed = $this->hash_pwd($session_pwd);

                        /* heslo, které je uloženo v session je neplatné, vyvoláme chybu a
                         * uživatele odhlásíme
                         */
                        if ($dbpwd != $pwd_hashed) {
                                global $error, $LANGUAGE;
                                $error->add_error($error->ERROR_LEVEL_NOTICE, __FILE__,
                                        __LINE__, 'USERS_PWD_FAIL',
                                        "Uživatel $session_login zadal špatné heslo.", null);
                                $this->prihlasen = false;
                                unset($_SESSION['users']);
                                return false;
                        }
                        /* id uživatele, které je uloženo v session je neplatné, vyvoláme
                         * chybu a uživatele odhlásíme
                         */
                        if ($dbuserid != $session_user_id) {
                                global $error, $LANGUAGE;
                                $error->add_error($error->ERROR_LEVEL_NOTICE, __FILE__,
                                        __LINE__, 'USERS_ID_NO_EXISTS',
                                        "ID uživatele $session_user_id neexistuje.", null);
                                return false;
                        }
                        $this->user_id = $dbuserid;
                        $this->prihlasen = true;
                        $this->load_attributes();

                        /* nastavit čas poslední aktivity do db */
                        $this->db->DbQuery("UPDATE `" . $this->db_tablename . "`
                                                                SET `last_activity`=NOW()
                                                                WHERE `" . $this->db_col_id . "`='"
                                                                . $dbuserid . "'");

                        return true;
                }
                else {
                        return $this->prihlasen;
                }
        }

        /**
         * @brief Vytvoří nového uživatele
         * @param $login Login uživatele
         * @param $pwd Heslo uživatele
         * @param $attribs Ostatní atributy v poli (sloupec => hodnota)
         * @return id nového uživatele, pokud je login a heslo v pořádku,
         *                      false v případě, že dojde k chybě.
         *
         * Najde, jestli login již v databázi existuje, pokud ne vytvoří nového
         * uživatele a nastaví jeho atributy
         *
         */
        function new_user($login, $pwd, $attribs) {
                global $auth;

                /* Ověření, jestli má užival právo přidávat nový účet */
                if (!($auth->authorised_to('create_account'))) {
                        global $error, $users_class, $LANGUAGE;

                        $error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                                        'ACCOUNT_CREATE_RESTRICTION',
                                        "Uživatel \"" . $users_class->user_login() . "\" se pokusil
                                         vytvořit nového uživatele, k čemuž nemá právo.",
                                        $LANGUAGE['ADMIN_NO_RIGHTS']);
                        break;
                }

                $this->db->DbQuery("LOCK TABLES `" . $this->db_tablename . "` WRITE");
                $this->db->Query("SELECT `id`
                                                  FROM `" . $this->db_tablename . "`
                                                  WHERE `" . $this->db_col_login . "`='$login'");
                if ($this->db->GetResult()) {
                        global $error, $LANGUAGE;
                        $error->add_error($error->ERROR_LEVEL_NOTICE, __FILE__,
                                __LINE__, 'USERS_LOGIN_EXISTS',
                                "Login uživatele $login již existuje.", null);
                        $this->db->DbQuery("UNLOCK TABLES");
                        return false;
                }

                $pwd = $this->hash_pwd($pwd);

                /* login neexistuje, můžem přidat nového uživatele */
                /* připravíme atributy */
                $ins_name_cols = "";
                $ins_name_values = "";
                foreach ($attribs as $ati => $atv) {
                        $ins_name_cols .= ", `" . $ati . "`";
                        $ins_name_values .= ", '" . $atv . "'";
                }
                $this->db->DbQuery("INSERT INTO `" . $this->db_tablename . "`
                                                        (`" . $this->db_col_login . "`, `" .
                                                                $this->db_col_pwd . "`" . $ins_name_cols . ")
                                                        VALUES ('$login','$pwd'$ins_name_values) ");

                $this->db->Query("SELECT LAST_INSERT_ID()");
                $new_id = $this->db->GetResult();
                $this->db->DbQuery("UNLOCK TABLES");
                return $new_id;
        }

        /**
         * @brief Změní heslo uživatele
         * @param $id Id uživatele
         * @param $pwd_new Nové heslo
         * @param $pwd_old Staré heslo, vyžadováno pokud $old_pwd_req <> 0
         * @param $old_pwd_req Určuje jestli je třeba zadat i staré heslo
         * @return true úspěch, false neúspěch
         *
         * Pokud id uživatele je v databázi, uživatel má oprávnění měnit heslo
         * uživatele se zadaným id a je správně zadáno starého heslo, pokud je
         * vyžadováno, tak dojde ke změně hesla u uživatele.
         *
         */
        function change_pwd($id, $pwd_new, $pwd_old = "",
                                                $old_pwd_req = 0) {
                global $auth;

                /* Ověření, jestli má užival právo upravovat účet */
                if (!($auth->authorised_to('edit_account', $id))) {
                        global $error;
                        $error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                                'ACCOUNT_EDIT_RESTRICTION',
                                "Uživatel " . $this->user_login()
                                . " nemá právo měnit údaje uživatele s ID " . $id,
                                null);

                        /* pro jistotu */
                        finalize(1);
                }

                $this->db->DbQuery("LOCK TABLES `" . $this->db_tablename . "` WRITE");
                $this->db->Query("SELECT `" . $this->db_col_login . "`
                                                  FROM `" . $this->db_tablename . "`
                                                  WHERE `" . $this->db_col_id . "`='$id'");
                if (!($login = $this->db->GetResult())) {
                        global $error, $LANGUAGE;
                        $error->add_error($error->ERROR_LEVEL_NOTICE, __FILE__,
                                __LINE__, 'USERS_ID_NO_EXISTS',
                                "ID uživatele $id neexistuje.", null);
                        $this->db->DbQuery("UNLOCK TABLES");
                        return false;
                }

                /* pokud je zadáno uživatelské jméno guest, tak vrať že nesmí být měněn
                 */
                if ($login == 'guest') {
                        global $error, $LANGUAGE;
                        $error->add_error($error->ERROR_LEVEL_NOTICE, __FILE__,
                                __LINE__, 'USERS_GUEST_MODIF',
                                "Došlo ke snaze upravit účet guesta.", null);
                        $this->db->DbQuery("UNLOCK TABLES");
                        return false;
                }

                $pwd_new_hashed = $this->hash_pwd($pwd_new, $id);


                /* pokud je vyžadováno zadat staré heslo, tak ho ověř */
                if ($old_pwd_req) {
                        if (!$this->check_pwd($pwd_old)) {
                                global $error, $LANGUAGE;
                                $error->add_error($error->ERROR_LEVEL_NOTICE, __FILE__,
                                        __LINE__, 'USERS_PWD_FAIL',
                                        "Uživatel $id zadal špatné heslo.", null);
                                $this->db->DbQuery("UNLOCK TABLES");
                                return false;
                        }
                }
                $this->db->DbQuery("UPDATE `" . $this->db_tablename . "`
                                                        SET `" . $this->db_col_pwd . "`='$pwd_new_hashed'
                                                        WHERE `" . $this->db_col_id . "`='$id'");
                $this->db->DbQuery("UNLOCK TABLES");
                $this->load_attributes();

                /* změn data v session, pokud uživatel upravoval vlastní heslo */
                if ($id == $this->user_id) {
                        $_SESSION['users']['pwd'] = $pwd_new;
                }

                return true;
        }

        /**
         * @brief Upraví atributy uživatele.
         * @param $id Id uživatele
         * @param $attribs Atributy, které se maj změnit pole (sloupec => hodnota)
         * @return true úspěch, false neúspěch
         *
         * Pokud id uživatele je v databázi, uživatel má oprávnění měnit atributy
         * uživatele se zadaným id, tak dojde k úpravě požadovanách atributů u
         * uživatele.
         *
         */
        function change_attribs($id, $attribs) {
                global $auth;

                /* Ověření, jestli má užival právo upravovat účet */
                if (!($auth->authorised_to('edit_account', $id))) {
                        global $error;
                        $error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                                'ACCOUNT_EDIT_RESTRICTION',
                                "Uživatel " . $this->user_login()
                                . " nemá právo měnit údaje uživatele s ID " . $id,
                                null);

                        /* pro jistotu */
                        finalize(1);
                }

                /* pole atributů je prázdné, tak není třeba nic měnit */
                if (!$attribs) {
                        return true;
                }

                /* proměnná atributů není prázdná, ale není to pole a to je chyba */
                if (!is_array($attribs)) {
                        return false;
                }

                $this->db->DbQuery("LOCK TABLES `" . $this->db_tablename . "` WRITE");
                $this->db->Query("SELECT `id`, `" . $this->db_col_login . "`
                                                  FROM `" . $this->db_tablename . "`
                                                  WHERE `" . $this->db_col_id . "`='$id'");
                if (!$row = $this->db->GetFetchRow()) {
                        global $error, $LANGUAGE;
                        $error->add_error($error->ERROR_LEVEL_NOTICE, __FILE__,
                                __LINE__, 'USERS_ID_NO_EXISTS',
                                "ID uživatele $id neexistuje.", null);
                        $this->db->DbQuery("UNLOCK TABLES");
                        return false;
                }

                /* pokud je zadáno uživatelské jméno guest, tak vrať že nesmí být měněn
                 */
                if ($row[1] == 'guest') {
                        global $error, $LANGUAGE;
                        $error->add_error($error->ERROR_LEVEL_NOTICE, __FILE__,
                                __LINE__, 'USERS_GUEST_MODIF',
                                "Došlo ke snaze upravit účet guesta.", null);
                        $this->db->DbQuery("UNLOCK TABLES");
                        return false;
                }

                /* připrav atributy */
                foreach ($attribs as $ati => $atv) {
                        if ($atv != '') {
                                $atv = "'" . $atv . "'";
                        }
                        else {
                                $atv = "NULL";
                        }
                        $upd_cols_val[] = " `" . $ati . "`=" . $atv . "";
                }
                if ($upd_cols_val) {
                        $upd_cols_val = implode(",", $upd_cols_val);
                }

                $this->db->DbQuery("UPDATE `" . $this->db_tablename . "`
                                                        SET $upd_cols_val
                                                        WHERE `" . $this->db_col_id . "`='$id'");
                $this->db->DbQuery("UNLOCK TABLES");
                $this->load_attributes();
                return true;
        }

        /**
         * @brief Vrátí id aktuálně přihlášeného uživatele.
         *
         * @return Id uživatele
         *
         */
        function user_id() {
                return $this->user_id;
        }

        /**
         * @brief Vrátí login uživatele
         * @param $id Id uživatele, pokud není vyplněno bere se přihlášený
         * @return Login uživatele
         *
         * Pokud není id zadáno, tak se bere aktuálně přihlášený uživatel. Funkce
         * použije funkci na vrácení atributu uživatele a vrátí jeho login.
         *
         */
        function user_login($id = "") {
                return $this->user_attrib($this->db_col_login, $id);
        }

        /**
         * @brief Vrátí vybraný atribut uživatele
         *
         * @param $attrib Název požadovaného atributu
         * @param $id Id uživatele, pokud není vyplněno bere se přihlášený
         * @return atribut uživatele definovaný v $attrib nebo false pokud id
         * uživatele neexistuje.
         *
         */
        function user_attrib($attrib, $id = "") {
                global $_SESSION;

                if (!$id) {
                        $id = $this->user_id;
                }

                if ($id == $this->user_id) {
                        if ($this->attributes) {
                                return $this->attributes[$attrib];
                        }
                }
                else
                {
                        $this->db->Query("SELECT `$attrib`
                                                          FROM `" . $this->db_tablename . "`
                                                          WHERE `" . $this->db_col_id . "`='" . $id . "'");
                        if (!($value = $this->db->GetResult())) {
                                global $error, $LANGUAGE;
                                $error->add_error($error->ERROR_LEVEL_NOTICE, __FILE__,
                                        __LINE__, 'USERS_ID_NO_EXISTS',
                                        "ID uživatele $id neexistuje.", null);
                                return false;
                        }
                        return $value;
                }
        }

        /**
         * @brief Odhlásí uživatele
         * @return vždy vrací true
         *
         * Funkce smaže uživatele ze session a nastaví účet hosta (nepřihlášeného).
         */
        function logout_user() {
                global $_SESSION;

                $this->user_id = "";
                $this->attributes = "";
                unset($_SESSION['users']);
                $this->set_guest();
                $this->prihlasen = false;
                return true;
        }

        /**
         * @brief Nahraje atributy z tabulky uživatele do pole attributes
         * @param $id Id uživatele - nepovinný
         * @return vrací true, pokud uživatel existuje, jinak false
         *
         * Pokud není předán funkci parametr, zjistí jej voláním funkce user_id(),
         * takže se použije aktuálně přihlášený uživatel.
         * Tato funkce je pouze privátní pro modul users, její používání mimo
         * tento modul není doporučeno, protože funkce předpokládá, že id uživatele,
         * které je předáno parametrem je oveřené a uživatel je přihlášen.
         *
         */
        function load_attributes($id = null) {
                global $_SESSION;

                if (!$id) {
                        $id = $this->user_id;
                }
                $this->db->Query("SELECT *
                                                  FROM `" . $this->db_tablename . "`
                                                  WHERE `" . $this->db_col_id . "`='" . $id . "'");
                if (!($pole = $this->db->GetFetchAssoc())) {
                        global $error, $LANGUAGE;
                        $error->add_error($error->ERROR_LEVEL_NOTICE, __FILE__,
                                __LINE__, 'USERS_ID_NO_EXISTS',
                                "ID uživatele $id neexistuje.", null);
                        return false;
                }
                else {
                        $this->attributes = $pole;
                }
                return true;
        }

        /**
         * @brief Nastaví účet nepřihlášeného uživatele
         *
         * Funkce zjistí z databáze id účtu hosta, jehož login musí být pokaždé
         * 'guest'. Pokud uživatele najde, nastaví jeho id jako id "přihlášeního"
         * uživatele. V opačném případě vyvolá chybu.
         *
         */
        function set_guest() {

                $this->db->Query("SELECT `" . $this->db_col_id . "`
                                                  FROM `" . $this->db_tablename . "`
                                                  WHERE `" . $this->db_col_login . "`='guest'");
                if ($guest_id = $this->db->GetResult()) {
                        $this->user_id = $guest_id;
                        $this->load_attributes();
                }
                /* účet guesta neexistuje, vyvolej chybu */
                else
                {
                        global $error;
                        $error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                                                          'USERS_GUEST_NOT_EXISTS',
                                                          'V databázi chybí uživatelský účet se jménem ' .
                                                          '"guest", bez něj nemůže aplikace fungovat.',
                                                          null);

                }

        }


        /**
         * @brief Nastaví účet uživatele jako zablokovaný
         *
         * @param $id Id uživatele
         * @return Vždy true
         *
         * Nastaví v databázi účet uživatele jako neaktivní=zablokovaný a nastaví
         * datum zablokování.
         *
         */
        function disable_user($id) {

                $attribs = array('account_active' => '0',
                                                 'disabled_time' => date("Y-m-d H:i:s",time()));
                return $this->change_attribs($id, $attribs);
        }


        /**
         * @brief Odblokuje účet uživatele
         *
         * @param $id Id uživatele
         * @return Vždy true
         *
         * Nastaví v databázi účet uživatele jako aktivní=odblokovaný a nastavý
         * datum zablokování na NULL.
         *
         */
        function enable_user($id) {
                $attribs = array('account_active' => '1',
                                                 'disabled_time' => '');
                return $this->change_attribs($id, $attribs);
        }


        /**
         * @brief Zjistí jestli je uživatelský účet aktivní (nezablokovaný)
         *
         * @param $id Id uživatele
         * @return 0 pokud dojde k chybě, jinak hodnota account_active z db
         *
         * Funkce zjistí hodnotu pole account_active příslušící danému id uživate
         * v databázi. Pokud uživatel neexistuje je vyvolána chyba a vrácena hodnota
         * 0. Jinak je vrácena hodnota pole account_active, což je 1 v případě,
         * že je účet aktivní, jinak 0.
         *
         */
        function is_active($id) {

                $this->db->Query("SELECT `account_active`
                                                  FROM `" . $this->db_tablename . "`
                                                  WHERE `" . $this->db_col_id . "`='" . $id . "'");
                if (!($pole = $this->db->GetFetchAssoc())) {
                        global $error;
                        $error->add_error($error->ERROR_LEVEL_WARNING, __FILE__, __LINE__,
                                                          'USERS_ID_NOT_EXISTS',
                                                          'V databázi chybí uživatelský účet se zadaným id.'
                                                          ,null);
                        return 0;
                }
                else {
                        return $pole['account_active'];
                }
        }


        /**
         * @brief Ošetří hashování hesla uživatele
         *
         * @param $pwd Heslo
         * @return Zahashované heslo, pokud je hashování zapnuto
         *
         * Zjistí z globální funkce $HASH_PASSWORDS jestli je hashování zapnuto a
         * podle toho buď zavolá na heslo funkci md5 nebo vrátí heslo beze změny.
         *
         */
        function hash_pwd($pwd) {
                global $HASH_PASSWORDS;

                if ($HASH_PASSWORDS) {
                        return md5($pwd);
                }
                else {
                        return $pwd;
                }
        }

        /**
         * @brief Zjistí, jestli heslo souhlasí se zadaným id uživatele.
         *
         * @param $pwd Heslo
         * @param $id Id uživatele
         * @return true - heslo souhlasí, false - heslo nesouhlasí
         *
         * Zjistí, jestli zadané heslo souhlasí se zadaným id uživatele. Pokud
         * id uživatele není zadáno, bere se přihlášený uživatel.
         *
         */
        function check_pwd($pwd, $id = null) {

                if (!$id) {
                        $id = $this->user_id;
                }

                $pwd_hashed = $this->hash_pwd($pwd);

                /* zjištění hesla uživatele podle zadaného id */
                $this->db->Query("SELECT `" . $this->db_col_pwd . "`
                                                  FROM `" . $this->db_tablename . "`
                                                  WHERE `" . $this->db_col_id . "`='$id'");

                /* zadaný user id v db neexistuje */
                if (!($pwd_db = $this->db->GetResult())) {
                        global $error, $LANGUAGE;
                        $error->add_error($error->ERROR_LEVEL_NOTICE, __FILE__,
                                __LINE__, 'USERS_ID_NO_EXISTS',
                                "ID uživatele $id neexistuje.", null);
                        return false;
                }
                if ($pwd_hashed == $pwd_db) {
                        return true;
                }
                else {
                        return false;
                }

        }

        /**
         * funkce vrati tvar odkazu, kterym prihlaseny uzivatel muze ziskavat
         * vyhody za nove privedene uzivatele
         *
         * @return cast odkazu nebo nic - pokud neni nikdo prihlasen
         */
        function reference_vygeneruj(){
        	//neprihlaseny uzivatel nemuze zvat nove lidi
        	if( !$this->is_logged() ){
        		return false;
        	}
        	
        	$string = $this->ref_nazev . "=" . $this->user_id();        	
        	return $string;
        }
        
        /**
         * Otestuje zda nebyl uzivatel nekym pozvan a pokud ano, zapise to do DB
         *
         * @param $id - registrovany uzivatel, u ktereho testujeme, zda byl pozvan
         *  
         */
        function reference_test( $id ){
        	
        	if(isset($_SESSION[$this->ref_nazev])){
        		$id_ref = $_SESSION[$this->ref_nazev];
        		$query = "SELECT * FROM users_sys WHERE id=$id_ref";
        		$res = $this->db->Query( $query );
        		
        		//je id platne?
        		if($row = $this->db->GetFetchAssoc( $res )){
        			$query = "SELECT * FROM users_sys WHERE id=$id";
        			$res2 = $this->db->Query( $query );
        			$novy_uzivatel = $this->db->GetFetchAssoc( $res2 );
        			//pokud uz uzivatele nekdo pozval, skonci
        			if(isset($novy_uzivatel['reference_od_id'])){
        				return;
        			}
        			else{
        				$query = "UPDATE users_sys SET reference_od_id=$id_ref
        						WHERE id=$id";
        				$this->db->DbQuery( $query );
        				//SEM PRIDAT KAZDE DALSI BONUSY PRO UZIVATELE!!!
        			}
        			
        		}
        	}
        	unset($_SESSION[$this->ref_nazev]);
        }
        
        /**
         * Ulozi do cookie ID uzivatele, ktery pristup zprostredkoval.
         * Pokud je nastaveno
         *
         */
        function reference_set_cookie(){
        	//pokud se pres tento prohlizec uz registroval, skonci
			if(isset($_COOKIE[$this->ref_test_nazev])){
				if($_COOKIE[$this->ref_test_nazev]==1){
					return;
				}
			}        	
        	if(isset($_REQUEST[$this->ref_nazev]) && is_numeric($_REQUEST[$this->ref_nazev])){
        		$_SESSION[$this->ref_nazev] = $_REQUEST[$this->ref_nazev];
        		//nastaveni cookie
        		setcookie($this->ref_test_nazev, 1, time()+3600*24*10);
         	}
        }
}
/**
 * @}
 * @}
 */

?>