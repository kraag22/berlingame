<?php
/**
 * @file
 * @brief Knihovna pro třídu, která zajišťuje autorizaci uživatelů.
 * @author Jiří Toušek
 *
 * @addtogroup g_framework
 * @{
 * @addtogroup g_authorisation Autorizace
 * @{
 *
 * @brief Knihovna pro třídu, která zajišťuje autorizaci uživatelů.
 *
 *
 */

/**
 * Třída poskytující informace o oprávněních uživatelů systému.
 *
 * Instance této třídy by měla být vytvořena na začátku běhu skriptu
 * (po autentizaci), parametrem user by měl být předán username přihlášeného
 * uživatele.
 * Metoda authorised_to() tohoto objektu pak poskytuje informaci, zda je
 * uživatel autorizován k danému tokenu (akci, zobrazení, ...).
 */
class Authorisation {

        /** ukazatel na objekt pro přístup do databáze (DbLib) */
        var $db;

        /** uživatel, jehož oprávnění mají být vracena */
        var $user;

        /** jméno autorizační tabulky v DB */
        var $auth_table = 'auth_rules';

        /** jméno DB tabulky přiřazení uživatelů do skupin */
        var $groups_table = 'users_sys';

        /**
         * Inicializuje třídu poskytující informace o oprávněních uživatelů systému.
         *
         * Instance této třídy by měla být vytvořena na začátku běhu skriptu
         * (po autentizaci), parametrem user by měl být předán username přihlášeného
         * uživatele.
         *
         * @param $db ukazatel na objekt pro přístup do databáze (DbLib)
         * @param $user username, jehož oprávnění budou vracena. Typicky username
         *                      uživatele, který skript spustil.
         * @param $authtable jméno tabulky s autorizačními daty (nepovinný parametr)
         * @param $groupstable jméno tabulky s přiřazením uživatelů do skupin
         *                      (nepovinný parametr)
         */
        function Authorisation(&$db, $user, $authtable = '', $groupstable = '') {
                $this->db = $db;
                $this->user = $user;
                if ($authtable)
                        $this->auth_table = $authtable;
                if ($groupstable)
                        $this->groups_table = $groupstable;
        }


        /**
         * Test příslušnosti ke skupině.
         *
         * @param $group_id skupina, která se má testovat
         *
         * @return true pokud test uspěl
         * @return false pokud test neuspěl
         */
        function group_test($group_id) {
                $tempresult = $this->db->Query("SELECT count(*)
                                FROM `" . $this->groups_table . "`
                                WHERE `login` = '" . $this->user . "'
                                AND `group_id` = '" . $group_id . "'");
                $row2 = $this->db->GetFetchRow($tempresult);
                if ($row2[0] == 0) {
                        // uzivatel do skupiny nepatri
                        return false;
                } else {
                        // uzivatel do skupiny patri
                        return true;
                }
        }



        /**
         * Test dotazem do obecné tabulky.
         *
         * @param $table tabulka (vcetne ``, pokud jde o jmeno tabulky)
         * @param $name_col sloupec s loginem
         * @param $index_col sloupec s indexem
         * @param $index hodnota indexu
         *
         * @return true pokud test uspěl
         * @return false pokud test neuspěl
         */
        function query_test($table, $name_col, $index_col, $index) {
                $query = "SELECT count(*) FROM " . $table
                         . " tab WHERE 1"; //WHERE 1 proto, aby se daly pridavat dalsi podminky "AND ..."

                // pridani testu na uzivatele
                if ($name_col !== NULL) {
                        $query .= " AND `" . $name_col . "` = '" . $this->user . "'";
                }

                // pridani testu na index
                if ($index_col !== NULL) {
                        $query .= " AND `" . $index_col . "` = "
                                        .( (is_numeric($index))
                                                ? $index                        // numerickou hodnotu bez uvozovek
                                                : "'" . $index . "'"    // textovou v uvozovkach
                                         );
                        //index_col nemuze obsahovat null value - null znamena vynechat
                }

                // exec query
                $tempresult = $this->db->Query($query);
                $row2 = $this->db->GetFetchRow($tempresult);
                if ($row2[0] == 0) {
                        // test neuspel
                        return false;
                } else {
                        // test uspel
                        return true;
                }
        }



        /**
         * Test voláním funkce.
         *
         * Tato funkce může potřebovat více dat, protože může selhat a potřebuje
         * hlásit co nejlépe okolnosti selhání. Proto má jako parametr celý řádek
         * dotazu.
         *
         * @param $row řádek dotazu
         * @param $token token, který je právě autorizován
         * @param $index index
         *
         * @return true pokud test uspěl
         * @return false pokud test neuspěl
         */
        function func_test($row, $token, $index) {
                // urcuje cestu ke applib adresari, aby bylo mozne pouzivat include
                global $DIR_LIB;

                // pokud existuje soubor s touto funkci, tak jej nacti
                if (file_exists($DIR_LIB . "auth_fce/" . $row['func_name'] . ".php")) {
                        require_once($DIR_LIB . "auth_fce/" . $row['func_name'] . ".php");
                } else {
                        // chyba systemu, vyhod varovani
                        global $error;
                        $error->add_error($error->ERROR_LEVEL_ERROR, __FILE__, __LINE__,
                                                'AUTH_FUNC_NOT_FOUND',
                                                "Nenalezena funkce {$row['func_name']} použitá v pravidlu #{$row['id']}",
                                                null);
                        // paranoidne neautorizuj
                        return false;
                }

                // exec function
                return $row['func_name']($row['func_param1'], $row['func_param2'],
                                                                        $token, $this->user, $index);
        }


        /**
         * Vrací oprávnění uživatele.
         *
         * Funkce dostane textový token (identifikátor reprezentující něco,
         * k čemu je vyžadována autentizace) a vrátí oprávnění uživatele
         * (viz konstruktor) k tomuto tokenu.
         *
         * @param $token textový token (identifikátor reprezentující něco, k čemu je
         *                      vyžadována autentizace)
         * @param $index index (textový nebo numerický), který bude použit, pokud
         *                      podmínka používá test proti tabulce s index_col nebo test funkcí
         *
         * @return true pokud má uživatel systému k tomuto tokenu oprávnění
         * @return false pokud uživatel systému oprávnění nemá
         */
        function authorised_to($token, $index = '') {

                $big_cycle = $this->db->Query("SELECT *
                        FROM `" . $this->auth_table . "`
                        WHERE `auth_token` = '" . sqlsafe($token) . "'");


                // zkontroluj, zda mame aspon jeden radek
                if ($this->db->GetNumRows($big_cycle) == 0) {
                        // vyhod varovani
                        global $error;
                        $error->add_error($error->ERROR_LEVEL_WARNING, __FILE__, __LINE__,
                                                'AUTH_NO_RULE_FOUND',
                                                "Tokenu $token neodpovídá žádné pravidlo",
                                                null);
                }


                // jednotlive radky maji semantiku OR
                $authorised = false;
                while ($row = $this->db->GetFetchAssoc($big_cycle)) {
                        // polozky na radku maji semantiku AND

                        // prislusnost do skupiny
                        if ($row['group_id'] !== NULL) {
                                if (!$this->group_test($row['group_id'])) {
                                        // uzivatel do te skupiny nepatri
                                        continue;
                                }
                                // else fall through to next test
                        }

                        // test proti obecne tabulce
                        if ($row['table'] !== NULL) {
                                if (!$this->query_test($row['table'], $row['name_col'],
                                                                                $row['index_col'], $index)) {
                                        // test neuspel
                                        continue;
                                }
                                // else fall through to next test
                        }

                        // volani funkce
                        if ($row['func_name'] !== NULL) {
                                // exec function
                                if (!$this->func_test($row, $token, $index)) {
                                        // test neuspel
                                        continue;
                                }
                        }

                        // pokud rizeni doslo az sem, je radek splnen a muzeme autorizovat
                        $authorised = true;
                        break;
                }

                return $authorised;
        }

}

/**
 * @}
 * @}
 */
?>