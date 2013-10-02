<?php
/**
 * @file form_manager.php
 * @brief Knihovna pro třídu, která umožňuje vytvářet formuláře definované v
 *                databázi.
 * @author Michal Podzimek
 *
 * @addtogroup g_framework
 * @{
 * @addtogroup g_form_manager Form manager
 * @{
 *
 * Tento modul má na starosti generování formulářů definovaných v databázi.
 *
 * Co v této knihovně chybí dodělat: TODO
 * - přidat zobrazování dalších elementů (průběžně, jak budou potřeba)
 * - dopsat pokyny pro přidávání formulářů
 * - předělat klíč tabulky z pevného "id" na zjištění klíče z tabulky
 * - hashovat hodnoty hidden elementů
 */


/* include zakladnich funkci pro osetreni vstupu a jejich jazykovych konstant */
require_once($DIR_LANG . "fm_fce/fm_fce.php");
require_once($DIR_LIB . "fm_fce/fm_fce.php");


/**
 * @brief Třída pro vytváření formulářů
 *
 * Primárně se používají tabulky `fm_form` a `fm_element`, ale je možné v
 * v konstruktoru třídy definovat jiné názvy tabulek.
 * Funkce Form_manager(konstruktor) slouží k inicializaci třídy. Funkce
 * create_form vytvoří a vrátí text formuláře, který byl předán v paramatru.
 * Funkce form_catch slouží k automatickému zpracování odeslaného formuláře.
 * Primárně slouží, ke kontrole dat a jejich úpravě. Může se postarat i o
 * uložení dat do databáze. Funkce form_catch je standartně volána v
 * applib/appmain.php, ale nemusí tomu tak být.
 *
 * Třída se snaží být pokud možno co nejvíce univerzální, ale není řešením pro
 * formuláře se složitou provázaností jednotlivých elementů, ... Pokud
 * nevyhovuje automatické zpracování, tak není problém využít buď přesně
 * odeslané hodnoty v $_POST nebo hodnoty zpracované funkcí form_catch, které
 * jsou vráceny touto funkcí. To umožňuje definovat v databázi funkci, která
 * upraví data. Data jsou upravena automaticky a pak lze data dále zpracovat
 * vlastní funkcí.
 *
 */
class Form_manager {
        /** ukazatel na třídu pro přístup do databáze */
        var $db;
        /** název tabulky, ve které jsou uloženy informace o formulářích */
        var $form_table = 'fm_form';
        /** název tabulky, ve které jsou uloženy informace o elementech formulářů */
        var $element_table = 'fm_element';
        /** pole chyb, které jsou poté zobrazeny uživateli */
        var $errors;
        /** pole informací o úspěšném zpracování formuláře */
        var $catched;
        /** pokud byl použit ke vkládání, vloží se sem poslední vložené ID */
        var $last_inserted_id;
        /** informace, která při vytváření elementů formuláře informuje o tom, že po
         * předchozím elementu následoval nový řádek */
        var $new_line;
        /** informuje, jestli se má vložit soubor s definicí stylů*/
        var $external_css = false;

        /**
         * @brief Inicializuje třídu pro vytváření formulářů
         * @param $db Ukazatel na třídu pro obsluhu databáze
         * @param $form_table Název tabulky, ve které jsou uloženy informace o
         *                formulářích
         * @param $element_table Název tabulky, ve které jsou uloženy informace o
         *                elementech
         *
         * Nastaví vstupní parametry do odpovídajících proměnných třídy.
         *
         */
        function Form_manager($db, $form_table = '', $element_table = '') {
                $this->db = $db;
                if ($form_table) {
                        $this->form_table = $form_table;
                }
                if ($element_table) {
                        $this->element_table = $element_table;
                }
        }

        /**
         * @brief Funkce, která vytvoří požadovaný formulář
         * @param $form Jméno formuláře
         * @param $values Pole výchozích hodnot formuláře
         * @param $form_skin_dir Jméno adresáře se skinama
         * @param $page ukazatel na třídu pro vykreslování stránky
         * @return Zdrojový kód formuláře
         * @return false pokud se vyskytne chyba
         *
         * Funkce nejdřív zjistí jestli formulář vůbec existuje. Pokud existuje, tak
         * zkusí vložit soubor(pokud existuje) s funkcemi pro tento formulář. V
         * případě, že se třída již předtím snažila formulář zpracovat a došlo k
         * chybám, tak se připraví do proměnné $warning výpis chyb.
         * Poté se zavolá funkce layoutu, která vypisuje hlavičku formuláře.
         * Pole hodnot se nastavuje, tak že index je jméno elementu a hodnota, je
         * hodnota, která se má do elementu vložit. Jako první se používá hodnota
         * poslaná v POSTu, pokud POST nebyl odeslán, tak se použije hodnota předaná
         * v poli $values a pokud ani tam není hodnota elementu definována použije
         * se výchozí hodnota z databáze.
         *
         */
        function create_form($form, $values, $form_skin_dir, &$page) {
                /* určuje, cestu ke kořenovému adresáři, aby bylo možné používat
                 * include
                 */
                global $DIRECTORY, $DIR_LIB;
                /* umožňuje přístup k odeslaným hodnotám, pro uchování hodnot
                 * formuláře
                 */
                global $_POST;

                $form_skin_dir = $DIRECTORY . "/" . $form_skin_dir;

                $warning = ""; $ret = "";
                /* pokud k formuláři existuje adresář s dodatečnými informacemi */
                if (is_dir($form_skin_dir)) {
                        /* pokud existuje soubor se stylama */
                        if ($page && is_file("$form_skin_dir/form_style.css")) {
                                /* zaregistrovat kaskadovy styl do stranky */
                                $page->add_css("$form_skin_dir/form_style.css");
                                $this->external_css = true;
                        }
                        else {
                                $this->external_css = false;
                        }
                        $external_printer = false;

                        /* pokud existuje soubor s externím printerem */
                        if (is_file("$form_skin_dir/form_printer.php")) {
                                include_once("$form_skin_dir/form_printer.php");
                                $external_printer = true;
                                $fp = new Form_printer;
                        }
                        else {
                                $external_printer = false;
                        }
                }
                else {
                        $this->external_css = false;
                }

                /* pokud neexistuje externí printer, nastaví printer sám na sebe a
                 * a použijí se defaultní funkce
                 */
                if (!$external_printer) {
                        $fp = $this;
                }

                /* zjistí se údaje o formuláři a jestli vůbec formulář existuje */
                $this->db->Query("SELECT *
                                                  FROM `" . $this->form_table . "`
                                                  WHERE name='$form'");
                if ($row_form = $this->db->GetFetchAssoc()) {
                        /* pokud existuje soubor s funkcema pro tento formulář, tak jej
                         * načti, once je tam, protože se soubor může zároveň includovat z
                         * form_catch
                         */
                        if (file_exists($DIR_LIB . "fm_fce/" . $row_form['name'] .
                                        ".php")) {
                                include_once($DIR_LIB . "fm_fce/" . $row_form['name'] . ".php");
                        }

                        /* pokud form_catch vygeneroval nějakou chybu, tak jí zobrazí nad
                         * formulářem chyba může být buď jedna, nebo to může být pole chyb,
                         * které se zřetězí a oddělí novým řádkem. Layout případně může
                         * vyhledat <br /> a předělat si to podle své potřeby
                         */
                        if (is_array($this->errors))
                        {
                                if (is_array(array_get_index_safe('form_catch', $this->errors)))
                                {
                                        if (is_array(array_get_index_safe($row_form['name'],
                                                        $this->errors['form_catch']))) {
                                                foreach ($this->errors['form_catch'][$row_form['name']]
                                                                 as $error) {
                                                        $warning .= $error . "<br />\n";
                                                }

                                        }
                                        elseif (array_get_index_safe($row_form['name'],
                                                        $this->errors['form_catch'])) {
                                                $warning =
                                                        $this->errors['form_catch'][$row_form['name']];
                                        }
                                }
                        }

                        /* zjištění položky id ve formuláři, pokud je formulář obnoven
                         * použije se hodnota z POSTu, jinak se použije hodnota z $values
                         */
                        if (array_key_exists('fm_' . $row_form['name'] . '_id', $_POST)) {
                                $form_value_id = $_POST['fm_' . $row_form['name'] . '_id'];
                        }
                        elseif (is_array($values) && array_key_exists('id', $values)) {
                                $form_value_id = $values['id'];
                        }
                        else {
                                $form_value_id = "";
                        }

                        /* pokud je zakázáno reloadováni formuláře, nastavím timestamp pro
                         * uložení, předám timestamp formuláři a uložím do $_SESSION
                         */
                        if ($row_form['no_reload']) {
                                $no_reload = time();
                                $_SESSION['fm_manager']['no_reload'] = $no_reload;
                        }
                        else {
                                $no_reload = "";
                        }

                        /* pokud byl formulář úspěšně zpracován zobraz succ_text */
                        if ($this->catched($row_form['name'])) {
                                $succ_text = $row_form['succ_text'];
                        }
                        else {
                                $succ_text = "";
                        }

                        $hid_elem = "";
                        /* zjistím si všechny elementy, které jsou hidden, odděleny od
                         * ostatních jsou kvůli tomu aby nepřekážely v tabulce, ale byly
                         * hned za tagem form
                         */
                        $result = $this->db->Query("SELECT *
                                                                                FROM `" . $this->element_table . "`
                                                                                WHERE `form`='" . $row_form['id'] . "'
                                                                                AND `order`>0
                                                                                AND `type`='hidden'
                                                                                ORDER BY `order` ASC");
                        while ($element = $this->db->GetFetchAssoc($result)) {
                                /* zjisti výchozí hodnotu */
                                $val = $element['default'];
                                if (is_array($values) && array_key_exists($element['element'],
                                        $values)) {
                                        $val = htmlsafe($values[$element['element']], false);
                                }
                                if (array_key_exists($element['element'], $_POST)) {
                                        $val = htmlsafe($_POST[$element['element']]);
                                }
                                $hid_elem[] = array($element, $val);
                        }

                        /* volám funkci, která vykreslí hlavičku formuláře */
                        $ret .= $fp->l_start_form($row_form['name'], $warning,
                                                                          $row_form['title'], $row_form['action'],
                                                                          $row_form['method'],
                                                                          $form_value_id,
                                                                          $row_form['file'],
                                                                          $no_reload,
                                                                          $succ_text,
                                                                          $hid_elem);

                        /* vyberu z databáze všechny elementy, které se vztahují k dannému
                         * formuláře a nejsou typu hidden
                         */
                        $result = $this->db->Query("SELECT *
                                                                                FROM `" . $this->element_table . "`
                                                                                WHERE `form`='" . $row_form['id'] . "'
                                                                                AND `order`>0
                                                                                AND `type` <> 'hidden'
                                                                                ORDER BY `order` ASC");
                        while ($element = $this->db->GetFetchAssoc($result)) {
                                /* nastavím defaultní hodnotu: pořadí POST, $values, db */
                                if (array_key_exists($element['element'], $_POST)) {
                                        $val = htmlsafe($_POST[$element['element']]);
                                }
                                elseif (is_array($values) && array_key_exists(
                                        $element['element'], $values)) {
                                        $val = htmlsafe($values[$element['element']], false);
                                }
                                else {
                                        $val = $element['default'];
                                }
                                /* volám funkci, která kreslí jednotlivé elementy */
                                $ret .= $fp->l_insert_element($element, $val);
                        }
                        /* volám funkci, která vykreslí konec formuláře */
                        $ret .= $fp->l_end_form();
                }
                else {

                        /* pokud formulář neexistuje vyhodím chybové hlášení a vrátím
                         * false
                         */
                        global $error, $LANGUAGE;

                        $error->add_error($error->ERROR_LEVEL_ERROR, __FILE__, __LINE__,
                                'FM_FORM_NOT_FOUND',
                                'Nenalezen formular: ' . $form,
                                $LANGUAGE['FM_FORM_NOT_FOUND']);

                        return false;
                }
                return $ret;
        }

        /**
         * @brief Funkce, která zpracuje odeslaný formulář
         * @param $_POST Proměnné odeslané z formuláře
         * @return Upravený $_POST dle definic v db
         *
         * Funkce najde všechny formuláře v databázi a pro všechny zkusí, jestli
         * byly odeslány (je definováno skryté pole 'fm_jmenoformulare' ). Pokud
         * ano, projde všechny elementy a zjistí, jestli splňují podmínku required,
         * vyhovují zadanému regexpu a jestli uživatelem definovaná funkce vrací
         * false. Všechny chyby jsou uloženy do pole chyb funkce form_catch. Cyklus,
         * který testuje jednotlivé elementy, otestuje vždy všechny elementy a uloží
         * všechny chyby. Druhou možností by bylo hned při první chybě skončit.
         * Pokud všechna pole jsou správně vyplněná, tak se zavolá funkce uživatelem
         * definovaná pro celý formulář, která opět, když vrátí false, tak se
         * pokračuje ve zpracování a jinak se vyvolá chyba. Pokud má alespoň jeden
         * element definovanou tabulku databáze, tak se pro všechny definované
         * tabulky připraví textové řetězce SET pro SQL příkazy INSERT a UPDATE.
         * Podle skrytého pole 'fm_jmenoformulare_id' se vybere buď příkaz UPDATE v
         * případě, že je pole neprázdné, a příkaz INSERT v případě, že pole s id
         * není vyplněné. Toto id se pak použije, pro WHERE podmínku. V tabulce
         * musí vždy existovat sloupec id, jinak je vyvolána chyba databáze. Pokud
         * vše proběhne bez chyb, tak se formulář smaže, jinak se ponechají přesně
         * hodnoty vložené uživatelem. Po vložení do databáze je zavolána funkce
         * definovaná sloupcem fce_after_db.
         *
         * V případě vytváření uživatelských funkcí, záleží na autorovi, jestli chce
         * proměnnou předávat odkazem nebo hodnotou, podle toho pak může
         * zpracovávanou hodnotu měnit a nebo jen kontrolovat její hodnotu.
         * Uživatelem definovaná funkce v případě úspěchu vrací false, jinak vrací
         * popis chyby. Tento popis se potom zobrazí nad formulářem.
         *
         */
        function form_catch($postPassed) {

                /** určuje, cestu ke kořenovému adresáři, aby bylo možné používat
                 * include
                 */
                global $DIRECTORY, $DIR_LIB, $DIR_LANG, $LANGUAGE;
                $save_to_db = false;
                $chyba = false;

                /* hodnoty z $postPassed uložím do nové proměnné, abych je mohl měnit a v
                 * případě neúspěchu ukládání formuláře mohl zobrazit původní hodnoty
                 */
                $post_novy = $postPassed;
                $post_puvodni = $postPassed;

                /* nalezení všech formulářů v databázi */
                $res = $this->db->Query("SELECT *
                                                                 FROM `" . $this->form_table . "`");
                while ($row_form = $this->db->GetFetchAssoc($res)) {
                        /* pokud je formulář odeslán zpracuji jej */
                        if (array_key_exists('fm_' . $row_form['name'], $postPassed)) {

                                /* nastaví, že formulář nebyl úspěšně zpracován,
                                 * pokud se podaří jej úspěšně zpracovat, tak je hodnota na
                                 * konci přepsána na 1
                                 */
                                $this->catched[$row_form['name']] = 0;
                                /* kontrola jestli je zakázaný reload formuláře */
                                if ($row_form['no_reload']) {
                                        if (!array_key_exists('fm_' . $row_form['name'] .
                                                '_no_reload', $postPassed)) {
                                            $postPassed['fm_' . $row_form['name'] . '_no_reload'] = "";
                                        }
                                        if (!array_key_exists('fm_manager', $_SESSION)
                                                || !array_key_exists('no_reload',
                                                $_SESSION['fm_manager'])) {
                                                $_SESSION['fm_manager']['no_reload'] = "";
                                        }
                                        if (!$postPassed['fm_' . $row_form['name'] . '_no_reload']
                                                || ($postPassed['fm_' . $row_form['name'] . '_no_reload'] !=
                                                $_SESSION['fm_manager']['no_reload'])) {
                                                $this->errors['form_catch'][$row_form['name']] =
                                                        $LANGUAGE['FM_RELOAD_FAIL'];
                                                continue;
                                        }
                                        else
                                        {
                                                $_SESSION['fm_manager']['no_reload'] = "";
                                        }

                                }

                                /* pokud existuje soubor s funkcemi, tak jej načti */
                                if (file_exists($DIR_LIB . "fm_fce/" . $row_form['name'] .
                                                ".php")) {
                                        include_once($DIR_LIB . "fm_fce/" . $row_form['name'] .
                                                ".php");
                                }
                                /* pokud existuje soubor jazykovými konstantami, tak jej načti
                                 */
                                if (file_exists($DIR_LANG . "fm_fce/" . $row_form['name'] .
                                                ".php")) {
                                        global $LANGUAGE;
                                        include_once($DIR_LANG . "fm_fce/" . $row_form['name'] .
                                                ".php");
                                }
                                /* vybrání všech elementů, které patří ke zpracovávanému
                                 * formuláři
                                 */
                                $res2 = $this->db->Query("SELECT *
                                                                                  FROM `" . $this->element_table . "`
                                                                                  WHERE form='" . $row_form['id'] . "'
                                                                                  ORDER BY `order` ASC");
                                while ($element_array[] = $this->db->GetFetchAssoc($res2));

                                /* cyklus přes všechny elementy, kde se ověřuje, že mají
                                 * správné hodnoty
                                 */
                                foreach ($element_array as $element) {

                                        /* kontrola, jestli je element vyplněný, pokud musí být,
                                         * pokud je element ruzný od file
                                         */
                                        if ($element['required'] && $element['type'] != 'file' &&
                                            $postPassed[$element['element']] == '') {
                                                $this->errors['form_catch'][$row_form['name']][]
                                                        = $LANGUAGE['FM_NENI_VYPLNENO_POLE'] .
                                                                $element['title'];
                                                continue;
                                        }

                                        /* pokud je element file, v případě, že se jedná o formulář
                                         * na úpravu, tak není zadání file vyžadováno a v tom
                                         * případě nedojde k úpravě
                                         */
                                        if ($element['required'] && $element['type'] == 'file' &&
                                                $_FILES[$element['element']]['name'] == '' &&
                                                !$postPassed['fm_' . $row_form['name'] . '_id']) {
                                                $this->errors['form_catch'][$row_form['name']][]
                                                        = $LANGUAGE['FM_NENI_VYPLNENO_POLE'] .
                                                                $element['title'];
                                                continue;
                                        }

                                        /* u typu file odstraníme ze jména diakritiku */
                                        if (($element['type'] == 'file') &&
                                                isset($_FILES[$element['element']]) &&
                                                isset($_FILES[$element['element']]['name'])) {

                                                $_FILES[$element['element']]['name'] =
                                                        common_translate_national_chars(
                                                                $_FILES[$element['element']]['name']
                                                        );

                                        }


                                        /* zjištění, jestli hodnota má správnou délku, pokud je tato
                                         * omezena parametrem size
                                         */
                                        if ($element['size'] &&
                                                in_array($element['type'],
                                                        array('text', 'password', 'textarea')) &&
                                                strlen($postPassed[$element['element']]) > $element['size']) {
                                                $this->errors['form_catch'][$row_form['name']][]
                                                        = $LANGUAGE['FM_PREKROCENA_DELKA1']
                                                                . $element['title'] .
                                                        $LANGUAGE['FM_PREKROCENA_DELKA2'];
                                                continue;
                                        }

                                        /* otestování regexpu, pokud je regexp zadaný */
                                        if ($element['regexp']) {
                                                $value = ($element['type'] == 'file')
                                                        ? $_FILES[$element['element']]['name']
                                                        : $postPassed[$element['element']];

                                                if (!preg_match('/' . $element['regexp'] . '/', $value))
                                                {

                                                        /* regexp nesouhlasí, zjisti jestli je v db
                                                         * definovaná hláška, která se má zobrazit, pokud
                                                         * ne, tak zobraz defaultní hlášku
                                                         */
                                                        if ($element['regexp_alert']) {
                                                                $this->errors['form_catch'][$row_form['name']][]
                                                                        = $element['regexp_alert'];
                                                                continue;
                                                        } else {
                                                                $this->errors['form_catch'][$row_form['name']][]
                                                                        = $LANGUAGE['FM_REGEXP_FAIL1']
                                                                                . $element['title'] .
                                                                          $LANGUAGE['FM_REGEXP_FAIL2'];
                                                                continue;
                                                        }
                                                }
                                        }

                                        /* otestování návratové hodnoty uživatelské funkce,
                                         * pokud je zadaná
                                         */
                                        $post_novy[$element['element']] = array_get_index_safe(
                                                                                        $element['element'], $post_novy);
                                        if ($element['fce']) {

                                                /* volání funkce, pokud je typ file předáváme hodnotu
                                                 * z FILES */
                                                if ($element['type'] == 'file') {
                                                        $user_fce_err = $element['fce']
                                                                ($_FILES[$element['element']], $element);
                                                }
                                                else {
                                                        $user_fce_err = $element['fce']
                                                                ($post_novy[$element['element']], $element);
                                                }

                                                //echo $value;
                                                if ($user_fce_err) {
                                                        $this->errors['form_catch'][$row_form['name']][]
                                                                = $user_fce_err;
                                                        continue;
                                                }
                                        }

                                        /* v případě, že je vyplněna tabulka, do které se mají
                                         * hodnoty ukládat, tak si zapamatuju, že mám do databáze
                                         * ukládat, ušetřím tak jedno procházení seznamu elementů,
                                         * nebo dotaz do databáze
                                         */
                                        if ($element['db_table'] && !$save_to_db) {
                                                $save_to_db = 1;
                                        }
                                }

                                /* pokud všechny hodnoty elementů prošly filtrama, tak zavolám
                                 * uživatelskou funkci pro celý formulář
                                 */
                                if (!$this->errors['form_catch'][$row_form['name']]
                                        && $row_form['fce']
                                        && ($user_fce_err = $row_form['fce']($post_novy))) {
                                        $this->errors['form_catch'][$row_form['name']]
                                                = $user_fce_err;
                                        continue;
                                }

                                /* pokud některý element má být uložen do databáze, tak projdu
                                 * všechny elementy a připravím si řetězec pro hodnoty SET
                                 * příkazů INSERT A UPDATE
                                 */
                                if (!$this->errors['form_catch'][$row_form['name']]
                                        && $save_to_db) {
                                        foreach ($element_array as $element) {
                                                if ($element['db_table']) {

                                                        /* pokud je element file, jedná se o úpravu a
                                                         * soubor nebyl zadán, tak se nic nemění
                                                         */
                                                        if ($element['type'] == 'file' &&
                                                                $_FILES[$element['element']]['name'] == '' &&
                                                            $postPassed['fm_' . $row_form['name'] . '_id']) {
                                                                continue;
                                                        }
                                                        /* pokud je element prazdný nastav NULL */
                                                        if (array_get_index_safe($element['element'],
                                                                                $post_novy) === '') {
                                                                $hodnota = "NULL";
                                                        }
                                                        else {
                                                                $hodnota = "'" .
                                                                sqlsafe(
                                                                        array_get_index_safe($element['element'],
                                                                                $post_novy)) . "'";
                                                        }

                                                        /* elementy ukládám do polí podle tabulek */
                                                        $set[$element['db_table']][] = "`" .
                                                        $element['db_column'] . "`=" . $hodnota;
                                                }
                                        }

                                        /* cyklus přes všechny tabulky, do kterých se má ukládat */
                                        foreach ($set as $table => $sloupce) {
                                                $sloupce = implode(', ', $sloupce);

                                                /* v případě, že má formulář definované id, tak UPDATE
                                                 * jinak INSERT
                                                 */
                                                if ($postPassed['fm_' . $row_form['name'] . '_id']) {
                                                        $this->db->Query("UPDATE `$table` SET $sloupce
                                                                                          WHERE id='" . $_POST['fm_' .
                                                                                          $row_form['name'] . '_id'] . "'");
                                                        $last_id = $postPassed['fm_' .
                                                                $row_form['name'] . '_id'];
                                                }
                                                else {

                                                        $this->db->Query("INSERT INTO `$table`
                                                                                          SET $sloupce");
                                                        $this->last_inserted_id = $last_id =
                                                                                                                $this->db->GetLastId();
                                                }
                                        }

                                }

                                /* zavolání funkce, která má běžet po uložení do db */
                                if (!$this->errors['form_catch'][$row_form['name']]
                                        && $row_form['fce_after_db']
                                        && ($user_fce_err = $row_form['fce_after_db'](
                                                $post_novy, $last_id))) {
                                        $this->errors['form_catch'][$row_form['name']]
                                                = $user_fce_err;
                                        continue;
                                }

                                /* pokud zpracování proběhlo úspěšně, smaž formulář */
                                if (!$this->errors['form_catch'][$row_form['name']]) {
                                        /* smaž všechny elementy formuláře */
                                        foreach ($element_array as $element) {
                                            $postPassed[$element['element']] = "";
                                        }
                                    $postPassed['fm_' . $row_form['name'] . '_id'] = "";
                                        $this->catched[$row_form['name']] = 1;
                                }
                                else {
                                        $chyba = true;
                                }
                        }
                }
                /* pokud nedošlo k chybě, tak funkce vrací upravené hodnoty, tak aby
                 * uživatel mohl tyto hodnoty využívat.
                 * Jinak vrací původní post.
                 * V POST nadále zůstávají neupravené původní odeslané hodnoty
                 */
                if (!$chyba) {
                        return $post_novy;
                }
                else {
                        return $post_puvodni;
                }

        }

        /**
         * @brief Funkce, která vrátí informaci o úspěšnosti zpracování formu
         * @param $form_name Jméno formuláře
         * @return false formulář nebyl úspěšně zpracován
         * @return true formulář byl úspěšně zpracován
         *
         * Vrací true nebo false podle hodnoty v proměnné catched
         *
         */
        function catched($form_name) {
                if (is_array($this->catched) &&
                        array_key_exists($form_name, $this->catched) &&
                        $this->catched[$form_name]) {
                        return true;
                }
                else {
                        return false;
                }
        }

        /**
         * @brief Defaultní funkce pro vypsání hlavičky formuláře
         * @param $name Jméno formuláře
         * @param $error Řetězec chybových hlášek
         * @param $title Popisek formuláře
         * @param $action Hodnota vlastnosti action tagu form
         * @param $method Hodnota vlastnosti method tagu form
         * @param $id Hodnota id ve formuláři, slouží pro update v db
         * @param $file Určuje jestli se má vypsat vlastnost enctype tagu form
         * @param $no_reload Určuje jestli je dovoleno vícenásobné odeslání formu
         * @param $succ_text Text, který se má vypsat po úspěšném zpracování formu
         * @param $hid_elem Pole skrytých elementů
         * @return Zdrojový kód hlavičky formuláře
         *
         * Tato defaultní funkce vypíše tag form se zadanými parametry, hlášku
         * o úspěchu či neúspěchu, input hidden s timestamp, pokud se nemá formulář
         * reloadovat, input hidden informující o tom, že byl tento formulář
         * odeslán, input hidden s id a začátek tabulky.
         *
         */
        function l_start_form($name, $error, $title, $action, $method,
                                                  $id, $file, $no_reload, $succ_text,
                                                  $hid_elem = null) {
                /* titulek formuláře */
                $ret = "<p class=\"fm_title\">$title</p>";

                /* začátek tagu form */
                $ret .= "<form action='$action' method='$method'";
                if ($file) {
                        $ret .= " enctype='multipart/form-data'";
                }
                $ret .= ">\n";

                /* zpráva o chybě při zpracování formuláře */
                if ($error) {
                        $ret .= "<span";
                        if ($this->external_css) {
                                $ret .= ' class="fm_err"';
                        }
                        else {
                                $ret .= " style='color:red;'";
                        }
                        $ret .= ">" . $error . "</span>\n";
                }

                /* zpráva o úspěchu zpracování formuláře */
                if ($succ_text) {
                        $ret .= "<span ";
                        if ($this->external_css) {
                                $ret .= ' class="fm_succ"';
                        }
                        else {
                                $ret .= " style='color:green;'";
                        }
                        $ret .= ">" . $succ_text . "</span>\n";
                }

                /* zákaz reloadu */
                if ($no_reload) {
                        $ret .= "<input type='hidden' name='fm_" . $name .
                                        "_no_reload' value='$no_reload' />\n";
                }

                /* id a informace o odeslání formuláře */
                $ret .= "<input type='hidden' name='fm_$name' value='1' />\n
                                 <input type='hidden' name='fm_" . $name . "_id' value='$id' />
                                 \n";

                /* výpis skritých elementů */
                if (is_array($hid_elem)) {
                        foreach ($hid_elem as $he) {
                                $ret .= "<input type='hidden' name='" . $he[0]['element'] .
                                                "' value='" . $he[1] . "' />\n";
                        }
                }

                /* začátek tabulky jednotlivých elementů */
                $ret .= "<table border='0' ";
                        if ($this->external_css) {
                                $ret .= 'class="fm_table"';
                        }
                $ret .= ">\n";

                $this->new_line = true;

                return $ret;
        }

        /**
         * @brief Defaultní funkce pro vypsání patičky formuláře
         * @return Zdrojový kód patičky formuláře
         *
         * V této defaultní funkci se vypíše jen obyčejný konec tabulky a formuláře.
         *
         */
        function l_end_form() {
                $ret = "</table>\n</form>\n";
                return $ret;
        }

        /**
         * @brief Defaultní funkce pro vypsání elementu formuláře
         * @param $elem Pole s informacemi o elementu
         * @param $value Defaultní hodnota elementu
         * @return Zdrojový kód elementu formuláře
         *
         * Tato defaultní funkce do obyčejné tabulky přidá element předaný v
         * parametru s hodnotou předanou v parametru value.
         *
         */
        function l_insert_element($elem, $value = '') {
                $ret = "";
                $ar_inputs = array("text", "file", "submit", "password", "hidden");

                /* pokud po předchozím elementu následoval nový řádek tak to ošetři */
                if ($this->new_line) {
                        $ret .= "<tr>\n\n";
                        $this->new_line = false;
                }

                /*
                 * pokud se jedná o speciální element headline, tak jen vypiš buňku
                 * s textem a ukonči funkci
                 */
                if ($elem['type'] == "headline") {
                        $ret .= "  <td colspan='" . $elem['colspan_title'] . "'> <span";

                        if ($elem['html_id']) {
                                $ret .= " id='" . $elem['html_id'] . "'";
                        }
                        if ($elem['html_class']) {
                                $ret .= " class='" . $elem['html_class'] . "'";
                        }

                        $ret .= " >" . $elem['title'] . "</span></td>\n";

                        if ($elem['new_line']) {
                                $ret .= "</tr>\n\n";
                                $this->new_line = true;
                        }

                        return $ret;
                }

                /* pokud je titulek nastaven, tak ho vykresli */
                if ($elem['colspan_title'] > 0) {
                        $ret .= "  <td colspan='" . $elem['colspan_title'] . "'";
                        if ($this->external_css) {
                                $ret .= ' class="fm_td_title"';
                        }
                        $ret .= "><span ";

                        if ($elem['html_id']) {
                                $ret .= " id='" . $elem['html_id'] . "_nazev'";
                        }
                        if ($elem['html_class']) {
                                $ret .= " class='" . $elem['html_class'] . "_nazev'";
                        }
                        $ret .= ">" . $elem['title'] . "</span></td>\n";
                }

                /* vykreslení elementu, začátek buňky */
                $ret .= "  <td colspan='" . $elem['colspan_elem'] . "'";
                if ($this->external_css) {
                        $ret .= ' class="fm_td_elem"';
                }
                $ret .= ">";

                /* pokud je element typu password, tak se nezobrazí jeho hodnota */
                if ($elem['type'] == "password") {
                        $value = htmlsafe($elem['default'], false);
                }

                /* text, file, submit, password, hidden, ... */
                if (in_array($elem['type'], $ar_inputs)) {
                        $ret .= "<input name='" . $elem['element'] . "' type='"
                                        . $elem['type'] . "'".
                                         " value='" . $value . "' ";
                        if ($elem['html_class']) {
                                $ret .= " class='" . $elem['html_class'] . "'";
                        }
                        if ($elem['html_id']) {
                                $ret .= " id='" . $elem['html_id'] . "' ";
                        }
                        if ($elem['size']) {
                                $ret .= " maxlength='" . $elem['size'] . "'";
                        }
                        $ret .= " />";
                }

                /* textarea */
                elseif ($elem['type'] == "textarea") {

                        $rozmery = explode(";", $elem['stuff']);
                        $rozmery[0] = array_get_index_safe(0, $rozmery);
                        $rozmery[1] = array_get_index_safe(1, $rozmery);

                        $ret .= "<textarea name='" . $elem['element'] .
                                        "' cols='" . $rozmery[0] . "' rows='" . $rozmery[1] . "' ";
                        if ($elem['html_class']) {
                                $ret .= " class='" . $elem['html_class'] . "'";
                        }
                        if ($elem['html_id']) {
                                $ret .= " id='" . $elem['html_id'] . "' ";
                        }
                        $ret .= ">" . $value . "</textarea>";
                }

                /*
                 * select, data se berou ze sloupce stuff databáze. Buď se volá funkce,
                 * která je vrátí, pak je ve stuff: "fce:nazev_funkce"
                 * Nebo jsou ve stuff rovnou data ve formátu:
                 * "hodnota;popisek;selected\nhodnota2;popisek2;"
                 */
                elseif ($elem['type'] == "select") {
                        $ret .= "<select name='" . $elem['element'] . "'";
                        if ($elem['html_class']) {
                                $ret .= " class='" . $elem['html_class'] . "'";
                        }
                        if ($elem['html_id']) {
                                $ret .= " id='" . $elem['html_id'] . "' ";
                        }
                        $ret .= ">";

                        /* volá se funkce, která vrátí pole dat */
                        if (strpos($elem['stuff'], "fce:") === 0) {
                                $fce = substr($elem['stuff'], 4);
                                $options = $fce();
                        }
                        /* data jsou přímo ve stuff, tak je třeba je sparsovat */
                        elseif ($elem['stuff']) {
                                $stuff = explode("\n", $elem['stuff']);
                                foreach ($stuff as $st) {
                                        $options[] = explode(";", $st);
                                }
                        }

                        /* výpis optionu */
                        if (is_array($options)) {
                                foreach ($options as $opt) {
                                        if (($value != null) && ($value == $opt[0])) {
                                                $sel = " selected='selected'";
                                        }
                                        elseif (!$value && isset($opt[2]) && trim($opt[2])) {
                                                $sel = " selected='selected'";
                                        }
                                        else {
                                                $sel = "";
                                        }
                                        $ret .= "<option value='" . htmlsafe($opt[0], false) .
                                                        "' $sel>" . htmlsafe($opt[1], false) .
                                                        "</option>\n";
                                }
                        }

                        $ret .= "</select>";
                }

                /* pokud je pole povinné, tak zobraz hvězdičku */
                if ($elem['required']) {
                        $ret .= "<span class='fm_required'>*</span>";
                }

                /* konec buňky, případně nový řádek */
                $ret .= "  </td>\n";
                if ($elem['new_line']) {
                        $ret .= "</tr>\n\n";
                        $this->new_line = true;
                }

                return $ret;
        }

}
/**
 * @}
 * @}
 */
?>