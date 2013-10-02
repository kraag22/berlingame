<?php
/**
 * @file list_element.php
 * @brief Knihovna pro třídu, která umožňuje vypisovat seznamy z databáze,
 * třídit je a filtrovat.
 *
 * @author Jan Cipra
 *
 * @addtogroup g_framework
 * @{
 * @addtogroup g_list_element List Element
 * @{
 *
 * @brief Knihovna pro třídu, která umožňuje vypisovat seznamy z databáze,
 * třídit je a filtrovat.
 *
 */

require_once $DIR_LIB . "page_elements/page_element.php";

/**
 * TODO DOKUMENTACE!!!!!!!!!!
 */

/**
 * @brief Abstraktní třída reprezentující filtr
 *
 */
class filter {
        /** název filtru */
        var $name;
        /** pole v němž je uložena struktura filtru */
        var $values;
        /** zde je uložena aktuální hodnota filtru */
        var $current_value;

        function filter($filter_name, $filter_values, $current_value = 0) {
                $this->name = $filter_name;
                $this->values = $filter_values;
                $this->current_value = $current_value;
        }
        /** funkce pro vykreslení */
        function draw($db = null, $db_table = null, $draw_hidden = 1, $form_name = '') {
        }

        /** funkce pro úpravu dotazu na databázi */
        function update_query(&$query) {
        }
}

/**
 * @brief Třída reprezentující jednoduchý filtr zobrazovaný jako listbox
 *
 * Tato třída slouží pro vytvoření jednnoduchého uživatelského filtru, který se ve výsledném
 * dokumentu zobrazí jeko listbox. Uvnitř listboxu budou možnoti zadaná při vztváření instance třídy.
 * Ukaždé volby je zadána podmínka, která se při její aktivaci přiřadí do WHERE klauzule dotazu
 * pro získání dat z databáze.
 *
 */
class listbox_filter extends filter{
        /**
         * @brief Inicializuje třídu jednoduchého uživatelského filtru
         *
         * @param $filter_name Název filtru
         * @param $filter_values Pole indexované od 1, obsahující sloupce ve kterých se má vyhledávat.
         *                Příklad:  $filter_array = array(1 => 'sloupec1', 2 = > 'sloupec2');
         *                kde 'sloupec1' a 'sloupec2' jsou definované v tabulce zadané při vytváření list_elementu ke
         *                kterému filtr vytváříme.
         * @param $current_value Nepovinný parametr, obsahuje hodnotu na kterou chceme filtr nastavit.
         *
         * Nastaví vstupní parametry do odpovídajících proměnných třídy.
         *
         */
        function listbox_filter($filter_name, $filter_values, $current_value = 0) {

                if (($current_value == 0) && isset($_POST["le_filter_$filter_name"])) {
                        $current_value = $_POST["le_filter_$filter_name"];
                }
                if (isset($_POST["le_filter_".$filter_name."_button"])) {
                        $current_value = $_POST["le_filter_".$filter_name."_select"];
                }
                parent::filter($filter_name, $filter_values, $current_value);
        }

        /**
         * @brief Vykresluje filtr do dokumentu
         *
         * @param $db Ukazatel na třídu pro obsluhu databáze
         * @param $db_table Tabulka ze které se tvoří #list_element
         * @param $draw_hidden Nepovinný parametr, když je nastaven na 0, pak se při vykreslení
         *                nevygenerují do dokumentu hidden input elementy obsahující aktuální hodnoty
         *        filtru. Pokud obsahuje něco jiného pak se vygenerují i tyto elementy.
         *
         */
        function draw($db = null, $db_table = null, $draw_hidden = 1, $form_name = '') {
                if ($draw_hidden) {
                        echo "<input type='hidden' name='le_filter_".$this->name."' value='".$this->current_value."' />\n";
            }

                echo "<select name='le_filter_".$this->name."_select' >";
                echo "  <option value='0'>--Vyberte--</option>";
                foreach ($this->values as $index => $value) {
                        ($index == $this->current_value) ? $sel = 'selected="selected"' : $sel = "";
                        echo "  <option $sel value='".$index."'>".$value['name']."</option>";
                }
                echo "</select>";
                echo "<input type='submit' name='le_filter_".$this->name."_button' value='Filtruj' />";
        }

        /**
         * @brief Upravuje databázový dotaz podle aktuálních hodnot filtru
         *
         * @param $query Databázový dotaz
         *
         */
        function update_query(&$query) {
                if (($this->current_value != 0) && (isset($this->values[$this->current_value]['condition']))) {
                        $cond = $this->values[$this->current_value]['condition'];
                        if ($query['where'] != "") {
                                        $query['where'] .= "AND ".$cond." ";
                                } else {
                                        $query['where'] = " WHERE ".$cond." ";
                                }
                } else {
                        $cond = "";
                }
        }
}

/**
 * @brief Třída reprezentující jednoduchý filtr sloužící pro vyhledávání zadaného řetězce v seznamu
 *
 * Tato třída slouží pro vytvoření jednoduchého uživatelského filtru, který se ve výsledném
 * dokumentu zobrazí jeko input pro zadání řetězce, který se má v seznamu vyhledávat.
 * Při vytváření rovněž dostane informaci v jakých sloupcích má vyhledávat.
 *
 */
class search_filter extends filter{

        /**
         * @brief Inicializuje třídu vyhledávacího filtru
         *
         * @param $filter_name Název filtru
         * @param $filter_values Pole indexované od 1, obsahující sloupce ve kterých se má vyhledávat.
         *                Příklad:  $filter_array = array (1 => array('name' => 'název1', 'condition' => 'sloupec1 = hodnota1'),
                                                                                    array('name' => 'název2', 'condition' => 'sloupec2 > hodnota2'));
         *                kde 'sloupec1' a 'sloupec2' jsou definované v tabulce zadané při vztváření list_elementu ke
         *                kterému filtr vytváříme.
         * @param $current_value Nepovinný parametr, obsahuje hodnotu na kterou chceme filtr nastavit.
         *
         */
        function search_filter($filter_name, $filter_values, $current_value = '--Vyhledávání--') {

                if (($current_value == '--Vyhledávání--') && !isset($_POST["le_filter_".$filter_name."_button"]) && isset($_POST["le_filter_$filter_name"]) && $_POST["le_filter_$filter_name"] != '') {
                        $current_value = $_POST["le_filter_$filter_name"];
                }
                if (isset($_POST["le_filter_".$filter_name."_button"]) && ($_POST["le_filter_".$filter_name."_input"] != '')) {
                        $current_value = $_POST["le_filter_".$filter_name."_input"];
                }
                parent::filter($filter_name, $filter_values, $current_value);
        }

        /**
         * @brief Vykresluje filtr do dokumentu
         *
         * @param $db Ukazatel na třídu pro obsluhu databáze
         * @param $db_table Tabulka ze které se tvoří #list_element
         * @param $draw_hidden Nepovinný parametr, když je nastaven na 0, pak se při vykreslení
         *                nevygenerují do dokumentu hidden input elementy obsahující aktuální hodnoty
         *        filtru. Pokud obsahuje něco jiného pak se vygenerují i tyto elementy.
         *
         */
        function draw($db = null, $db_table = null, $draw_hidden = 1, $form_name = '') {
                if ($draw_hidden) {
                        echo "<input type='hidden' name='le_filter_".$this->name."' value='".$this->current_value."' />\n";
            }

            echo "<input onclick='clear_input(this)' name='le_filter_".$this->name."_input' value='".$this->current_value."' />\n";
            echo "<input type='submit' name='le_filter_".$this->name."_button' value='Filtruj' />";
        }

        /**
         * @brief Upravuje databázový dotaz podle aktuálních hodnot filtru
         *
         * @param $query Databázový dotaz
         *
         */
        function update_query(&$query) {
                if (($this->current_value != '--Vyhledávání--') && (isset($this->values))) {

                        $cond = "";
                        foreach ($this->values as $column) {
                                if ($cond == "")
                                        $cond = "`".$column."` LIKE '%".$this->current_value."%' ";
                                else
                                        $cond .= " OR `".$column."` LIKE '%".$this->current_value."%' ";
                        }
                        if ($query['where'] != "") {
                                        $query['where'] .= "AND (".$cond.") ";
                                } else {
                                        $query['where'] = " WHERE (".$cond.") ";
                                }
                }
        }
}

/**
 * @brief Třída reprezentující filtr obsahující pro zadané sloupce všechny hodnoty,
 * které se v nich vyskytují.
 *
 * Tato třída slouží pro vytvoření jednoduchého filtru, který se ve výsledném
 * dokumentu zobrazí jeko listbox. Uvnitř listboxu budou pro všechny zadané sloupce
 * vygenerovány všechny hodnoty, které jsou v nich obsaženy.
 *
 */
class all_values_filter extends filter{

        /**
         * @brief Inicializuje třídu filtru
         *
         * @param $filter_name Název filtru
         * @param $filter_values Pole indexované od 1, obsahující pole s definicí sloupců ze kterých se filtr vytvoří.
         *                Pod indexem 'col' je zadán název sloupce v databázové tabulce  a 'name' je název který se zobrazí
         *                jako kategorie ve filtru. Filtr bude obsahovat všechny hodnoty, které se vyskytují v zadaném sloupci v tabulce.
         *                Příklad:  $filter_array = array (1 => array('db_col' => 'sloupec1', 'name' => 'Název1'),
                                                                                    array('db_col' => 'sloupec2', 'name' => 'Název2'));
         *                kde 'sloupec1' a 'sloupec2' jsou definované v tabulce zadané při vytváření list_elementu ke
         *                kterému filtr vytváříme.
         * @param $current_value Nepovinný parametr, pole kde pod indexem 'col' resp. 'val' je název sloupce
         *                resp. hodnota na kterou se má filtr nastavit
         *
         * Nastaví vstupní parametry do odpovídajících proměnných třídy.
         *
         */
        function all_values_filter($filter_name, $filter_values, $current_value = array('col' => '', 'val' => '--Vyberte hodnotu--')) {

                /**
                 * Pokud je pole #current_value nastaveno na implicitní hodnoty, pak zkontrolujeme zda máme byly v poli
                 * POST odeslány hodnoty pro tento filtr a pokud ano pak je uložíme do pole #current_value
                 */
                if (($current_value['col'] == '') && isset($_POST["le_filter_".$filter_name."_col"]) && ($_POST["le_filter_".$filter_name."_col"] != '')
                         && isset($_POST["le_filter_".$filter_name."_val"]) && ($_POST["le_filter_".$filter_name."_val"] != '--Vyberte hodnotu--')) {

                        $current_value['col'] = $_POST["le_filter_".$filter_name."_col"];
                        $current_value['val'] = $_POST["le_filter_".$filter_name."_val"];
                }
                if (isset($_POST["le_filter_".$filter_name."_button"])) {
                        $values = explode(";", $_POST["le_filter_".$filter_name."_select"]);
                        $current_value['col'] = $values[0];
                        $current_value['val'] = $values[1];
                }
                parent::filter($filter_name, $filter_values, $current_value);
        }

        /**
         * @brief Generuje hodnoty pro filtr
         *
         * @param $db Ukazatel na třídu pro obsluhu databáze
         * @param $db_table Tabulka ze které se tvoří #list_element
         *
         * @return Vrací pole ve kterém jsou pro každý sloupec
         * všechny hodnoty, které se v něm vyskytují.
         *
         */
        function generate_distinct_values($db, $db_table) {
                $array_ret = array();

                $array_ret[0]['db_col'] = '';
                $array_ret[0]['name'] = '--Vyberte sloupec--';
                $array_ret[0]['values'][0] = '--Vyberte hodnotu--';

                foreach ($this->values as $index => $col ) {
                        $array_ret[$index]['db_col'] = $col['db_col'];
                        $array_ret[$index]['name'] = $col['name'];

                        $query = "SELECT DISTINCT `".$col['db_col']."` FROM ".$db_table." ";
                        if (!($res = $db->Query($query)) ){
                                $ERROR = array ('line'=>__LINE__, 'file'=>__FILE__,
                                        'err_text'=> "Data pro filtr se nepodařilo načíst z databáze\nDotaz: $query",
                                        'err_usr_text'=> 'Chyba spojení s databází',
                                        'err_level'=>$ERROR_LEVEL_FATAL);
                                $error->add_error($ERROR);
                        }
                        while ($row = $db->GetFetchAssoc()) {
                                $array_ret[$index]['values'][] = $row[$col['db_col']];
                        }
                }
                return $array_ret;
        }

        /**
         * @brief Vykresluje filtr do dokumentu
         *
         * @param $db Ukazatel na třídu pro obsluhu databáze
         * @param $db_table Tabulka ze které se tvoří #list_element
         * @param $draw_hidden Nepovinný parametr, když je nastaven na 0, pak se při vykreslení
         *                nevygenerují do dokumentu hidden input elementy obsahující aktuální hodnoty
         *        filtru. Pokud obsahuje něco jiného pak se vygenerují i tyto elementy.
         *
         */
        function draw($db = null, $db_table = null, $draw_hidden = 1, $form_name = '') {
                $array = $this->generate_distinct_values($db, $db_table);
                if ($draw_hidden) {
                        echo "<input type='hidden' name='le_filter_".$this->name."_col' value='".$this->current_value['col']."' />\n";
                        echo "<input type='hidden' name='le_filter_".$this->name."_val' value='".$this->current_value['val']."' />\n";
            }
            echo "<select name='le_filter_" . $this->name . "_select'>\n";
            foreach ($array as $upper) {
                        if (isset($upper['values']) && count($upper['values']) > 0) {
                                echo "  <optgroup label='". htmlsafe($upper['name']) ."'>\n";
                                foreach ($upper['values'] as $lower) {
                                        if ($upper['db_col'] == $this->current_value['col'] && $lower == $this->current_value['val'])
                                                $selected = 'selected="selected"';
                                        else
                                                $selected = "";

                                        echo "          <option $selected value='".htmlsafe($upper['db_col']).";".htmlsafe($lower)."'>" . htmlsafe($lower) . "</option>\n";
                        }
                        echo "  </optgroup>\n";
                }
            }
            echo "</select>\n";
            echo "<input type='submit' name='le_filter_".$this->name."_button' value='Filtruj' />\n";
        }

        /**
         * @brief Upravuje databázový dotaz podle aktuálních hodnot filtru
         *
         * @param $query Databázový dotaz
         *
         */
        function update_query(&$query) {
                if (($this->current_value['col'] != '') && ($this->current_value['val'] != '--Vyberte hodnotu--')) {
                        $cond = "`".$this->current_value['col']."` = '".$this->current_value['val']."'";
                        if ($query['where'] != "") {
                                        $query['where'] .= "AND ".$cond." ";
                                } else {
                                        $query['where'] = " WHERE ".$cond." ";
                                }
                } else {
                        $cond = "";
                }
        }
}

/**
 * @brief Třída reprezentující filtr obsahující pro zadané sloupce všechny hodnoty,
 * které se v nich vyskytují.
 *
 * Tato třída slouží pro vytvoření jednoduchého filtru, který se ve výsledném
 * dokumentu zobrazí jeko listbox. Uvnitř listboxu budou pro všechny zadané sloupce
 * vygenerovány všechny hodnoty, které jsou v nich obsaženy.
 *
 */
class all_values_row_filter extends filter{

        /**
         * @brief Inicializuje třídu filtru
         *
         * @param $filter_name Název filtru
         * @param $filter_values Pole indexované od 1, obsahující pole s definicí sloupců ze kterých se filtr vytvoří.
         *                Pod indexem 'col' je zadán název sloupce v databázové tabulce  a 'name' je název který se zobrazí
         *                jako kategorie ve filtru. Filtr bude obsahovat všechny hodnoty, které se vyskytují v zadaném sloupci v tabulce.
         *                Příklad:  $filter_array = array (1 => array('db_col' => 'sloupec1', 'name' => 'Název1'),
                                                                                    array('db_col' => 'sloupec2', 'name' => 'Název2'));
         *                kde 'sloupec1' a 'sloupec2' jsou definované v tabulce zadané při vytváření list_elementu ke
         *                kterému filtr vytváříme.
         * @param $current_value Nepovinný parametr, pole kde pod indexem 'col' resp. 'val' je název sloupce
         *                resp. hodnota na kterou se má filtr nastavit
         *
         * Nastaví vstupní parametry do odpovídajících proměnných třídy.
         *
         */
        function all_values_row_filter($filter_name, $filter_values, $current_value = array()) {

                /**
                 * Pokud je pole #current_value nastaveno na implicitní hodnoty, pak zkontrolujeme zda máme byly v poli
                 * POST odeslány hodnoty pro tento filtr a pokud ano pak je uložíme do pole #current_value
                 */
                foreach ($filter_values as $index => $col ) {
                        if (!isset($current_value[$index])) {
                                $current_value[$index]['val'] = '--Vyberte hodnotu--';
                        }

                        if (isset($current_value[$index]) && ($current_value[$index]['val'] == '--Vyberte hodnotu--')
                            && isset($_POST["le_filter_".$filter_name."_".$col['db_col']."_val"]) && ($_POST["le_filter_".$filter_name."_".$col['db_col']."_val"] != '--Vyberte hodnotu--')) {

                                $current_value[$index]['val'] = $_POST["le_filter_".$filter_name."_".$col['db_col']."_val"];
                        }
                        if (isset($_POST["le_filter_".$filter_name."_button"])) {
                                $value = $_POST["le_filter_".$filter_name."_".$col['db_col']."_select"];

                                $current_value[$index]['val'] = $value;
                        }
                }
                parent::filter($filter_name, $filter_values, $current_value);
        }

        /**
         * @brief Generuje hodnoty pro filtr
         *
         * @param $db Ukazatel na třídu pro obsluhu databáze
         * @param $db_table Tabulka ze které se tvoří #list_element
         *
         * @return Vrací pole ve kterém jsou pro každý sloupec
         * všechny hodnoty, které se v něm vyskytují.
         *
         */
        function generate_distinct_values($db, $db_table) {
                $array_ret = array();

                foreach ($this->values as $index => $col ) {
                        $array_ret[$index]['db_col'] = $col['db_col'];
                        $array_ret[$index]['name'] = $col['name'];
                        $array_ret[$index]['values'][0] = '--Vyberte hodnotu--';

                        $query = "SELECT DISTINCT `".$col['db_col']."` FROM ".$db_table." ";
                        if (!($res = $db->Query($query)) ){
                                $ERROR = array ('line'=>__LINE__, 'file'=>__FILE__,
                                        'err_text'=> "Data pro filtr se nepodařilo načíst z databáze\nDotaz: $query",
                                        'err_usr_text'=> 'Chyba spojení s databází',
                                        'err_level'=>$ERROR_LEVEL_FATAL);
                                $error->add_error($ERROR);
                        }
                        while ($row = $db->GetFetchAssoc()) {
                                $array_ret[$index]['values'][] = $row[$col['db_col']];
                        }
                }
                return $array_ret;
        }

        /**
         * @brief Vykresluje filtr do dokumentu
         *
         * @param $db Ukazatel na třídu pro obsluhu databáze
         * @param $db_table Tabulka ze které se tvoří #list_element
         * @param $draw_hidden Nepovinný parametr, když je nastaven na 0, pak se při vykreslení
         *                nevygenerují do dokumentu hidden input elementy obsahující aktuální hodnoty
         *        filtru. Pokud obsahuje něco jiného pak se vygenerují i tyto elementy.
         *
         */
        function draw($db = null, $db_table = null, $draw_hidden = 1, $form_name = '') {
                $array = $this->generate_distinct_values($db, $db_table);
                if ($draw_hidden) {
                        foreach ($this->values as $index => $col) {
                                echo "<input type='hidden' name='le_filter_".$this->name."_".$col['db_col']."_val' value='".htmlsafe($this->current_value[$index]['val'])."' />\n";
                        }
            }

            foreach ($array as $index =>$upper) {
                echo "  <label>" . $this->values[$index]['name'] . "</label>\n";
                echo "  <select name='le_filter_".$this->name."_".$upper['db_col']."_select'>\n";
                foreach ($upper['values'] as $lower) {
                                if ($lower == $this->current_value[$index]['val'])
                                        $selected = 'selected="selected"';
                                else
                                        $selected = "";

                                echo "          <option $selected value='".htmlsafe($lower)."'>" . htmlsafe($lower) . "</option>\n";
                }
                echo "  </select>\n";
            }
            echo "<input type='submit' name='le_filter_".$this->name."_button' value='Filtruj' />\n";
        }

        /**
         * @brief Upravuje databázový dotaz podle aktuálních hodnot filtru
         *
         * @param $query Databázový dotaz
         *
         */
        function update_query(&$query) {
                foreach ($this->current_value as $index => $curr) {
                        if (($curr['val'] != '--Vyberte hodnotu--')) {
                                $cond = "`".$this->values[$index]['db_col']."` = '".$curr['val']."'";
                                if ($query['where'] != "") {
                                                $query['where'] .= "AND ".$cond." ";
                                        } else {
                                                $query['where'] = " WHERE ".$cond." ";
                                        }
                        } else {
                                $cond = "";
                        }
                }
        }
}

class column_values_filter extends filter{
        /** pole s operatory filtru */
        var $filters = array();
        var $filters_count = 0;

        /**
         * @brief Inicializuje třídu filtru
         *
         * @param $filter_name Název filtru
         * @param $filter_values Pole indexované od 1, obsahující pole s definicí sloupců ze kterých se filtr vytvoří.
         *                Pod indexem 'col' je zadán název sloupce v databázové tabulce  a 'name' je název který se zobrazí
         *                jako kategorie ve filtru. Filtr bude obsahovat všechny hodnoty, které se vyskytují v zadaném sloupci v tabulce.
         *                Příklad:  $filter_array = array (1 => array('db_col' => 'sloupec1', 'name' => 'Název1'),
                                                                                    array('db_col' => 'sloupec2', 'name' => 'Název2'));
         *                kde 'sloupec1' a 'sloupec2' jsou definované v tabulce zadané při vytváření list_elementu ke
         *                kterému filtr vytváříme.
         * @param $current_value Nepovinný parametr, pole kde pod indexem 'col' resp. 'val' je název sloupce
         *                resp. hodnota na kterou se má filtr nastavit
         *
         * Nastaví vstupní parametry do odpovídajících proměnných třídy.
         *
         */
        function column_values_filter($filter_name, $filter_values, $current_value = array('col' => '', 'comp' => '', 'val' => '')) {

                if (isset($_POST["le_filter_".$filter_name."_filters_count"])) {
                        $this->filters_count = $_POST["le_filter_".$filter_name."_filters_count"];
                } else {
                        $this->filters_cout = 0;
                }

                $index = 0;
                $i = 0;
                $del = false;
                while ($index < $this->filters_count) {
                        if (!isset($_POST["le_filter_".$filter_name."_".$index."_del_button"])) {
                                if (isset($_POST["le_filter_".$filter_name."_".$index."_col"]) && ($_POST["le_filter_".$filter_name."_".$index."_col"] != '')
                                         && isset($_POST["le_filter_".$filter_name."_".$index."_val"]) && ($_POST["le_filter_".$filter_name."_".$index."_val"] != '--Vyberte hodnotu--')) {

                                        $this->filters[$i]['enabled'] = $_POST["le_filter_".$filter_name."_".$index."_enabled"];
                                        $this->filters[$i]['value']['col'] = $_POST["le_filter_".$filter_name."_".$index."_col"];
                                        $this->filters[$i]['value']['col_name'] = $_POST["le_filter_".$filter_name."_".$index."_col_name"];
                                        $this->filters[$i]['value']['val'] = $_POST["le_filter_".$filter_name."_".$index."_val"];
                                }

                                if (isset($_POST["le_filter_".$filter_name."_".$index."_disable_button"])) {
                                        $this->filters[$i]['enabled'] = false;
                                }
                                if (isset($_POST["le_filter_".$filter_name."_".$index."_enable_button"])) {
                                        $this->filters[$i]['enabled'] = true;
                                }
                                $index++;
                                $i++;
                        } else {
                                $del = true;
                                $index++;
                        }
                }

                if ($del) {
                        $this->filters_count--;
                }

                if (isset($_POST["le_filter_".$filter_name."_add_button"])
                        && ($_POST["le_filter_".$filter_name."_selected_column"] != '')
                                && ($_POST["le_filter_".$filter_name."_val_select"] != '') ) {

                        $this->filters[$this->filters_count]['enabled'] = true;

                        $values = explode(";", $_POST["le_filter_".$filter_name."_selected_column"]);
                        $this->filters[$this->filters_count]['value']['col'] = $values[0];
                        $this->filters[$this->filters_count]['value']['col_name'] = $values[1];
                        $this->filters[$this->filters_count]['value']['val'] = $_POST["le_filter_".$filter_name."_val_select"];

                        $this->filters_count++;

                        $current_value['col'] = '';
                } else {

                        if (isset($_POST["le_filter_".$filter_name."_selected_column"]) && $_POST["le_filter_".$filter_name."_selected_column"] != '') {
                                $current_value['col'] = $_POST["le_filter_".$filter_name."_selected_column"];
                        }
                }

                if (isset($_POST["le_filter_".$filter_name."_col_select"])) {

                        $current_value['col'] = $_POST["le_filter_".$filter_name."_col_select"];
                }

                if (isset($_POST["le_filter_".$filter_name."_unset_column"])) {

                        $current_value['col'] = '';
                }

                parent::filter($filter_name, $filter_values, $current_value);

        }

        /**
         * @brief Generuje hodnoty pro filtr
         *
         * @param $db Ukazatel na třídu pro obsluhu databáze
         * @param $db_table Tabulka ze které se tvoří #list_element
         *
         * @return Vrací pole ve kterém jsou pro každý sloupec
         * všechny hodnoty, které se v něm vyskytují.
         *
         */
        function generate_distinct_values($db, $db_table, $col) {
                $array_ret = array();

                $array_ret['db_col'] = $col;

                $query = "SELECT DISTINCT `".$col."` AS val FROM ".$db_table." ";
                if (!($res = $db->Query($query)) ){
                        $ERROR = array ('line'=>__LINE__, 'file'=>__FILE__,
                                'err_text'=> "Data pro filtr se nepodařilo načíst z databáze\nDotaz: $query",
                                'err_usr_text'=> 'Chyba spojení s databází',
                                'err_level'=>$ERROR_LEVEL_FATAL);
                        $error->add_error($ERROR);
                }
                while ($row = $db->GetFetchAssoc()) {
                        $array_ret['values'][] = $row;
                }

                return $array_ret;
        }

        /**
         * @brief Vykresluje filtr do dokumentu
         *
         * @param $db Ukazatel na třídu pro obsluhu databáze
         * @param $db_table Tabulka ze které se tvoří #list_element
         * @param $draw_hidden Nepovinný parametr, když je nastaven na 0, pak se při vykreslení
         *                nevygenerují do dokumentu hidden input elementy obsahující aktuální hodnoty
         *        filtru. Pokud obsahuje něco jiného pak se vygenerují i tyto elementy.
         *
         */
        function draw($db = null, $db_table = null, $draw_hidden = 1, $form_name = '') {
                global $LANGUAGE;

                $values = explode(";", $this->current_value['col']);
                $current_col = array_get_index_safe(0, $values);
                $current_col_name = array_get_index_safe(1, $values);

                if ($current_col != '') {
                        $distinct_values = $this->generate_distinct_values($db, $db_table, $current_col);
                }

                echo "<script language='JavaScript' type=\"text/javascript\">\n";
            echo "<!--\n";
            echo '      function submit_frm(name) {
                                        var frm;
                                        frm = document.forms[name];
                                        frm.submit();
                                }'."\n";
            echo "-->\n";
            echo "</script>";

            echo "<input type='hidden' name='le_filter_".$this->name."_filters_count' value='".$this->filters_count."' />\n";

            echo "<table>\n";
            echo "      <tr>\n";
            echo "      <th>" . $LANGUAGE["le_filter_hledat_dle"] . "</th>\n";
            echo "              <th>" . $LANGUAGE["le_filter_zvolte_kriterium"] . "</th>\n";
            echo "              <th></th>\n";
            echo "      </tr>\n";

            echo "      <tr>\n";
            if ($this->current_value['col'] == $LANGUAGE["le_filter_vyberte_sloupec"] || $this->current_value['col'] == '') {
                echo "  <td>\n";
                echo "<select onchange=\"submit_frm('list_element_" . $form_name . "')\" name='le_filter_".$this->name."_col_select' >\n";
                        echo "  <option value=''>" . $LANGUAGE["le_filter_vyberte_sloupec"] . "</option>\n";
                    foreach ($this->values as $col) {
                                if ($col['db_col'] == $current_col)
                                        $selected = 'selected="selected"';
                                else
                                        $selected = "";

                                echo "  <option $selected value='".$col['db_col'].";".$col['name']."'>".$col['name']."</option>\n";
                    }

                    echo "</select>\n";
                    echo "<noscript>\n";
                    echo "      <input type='submit' name='le_filter_".$this->name."_set_column' value='Vybrat' />\n";
                echo "</noscript>\n";
                        echo "<input type='hidden' name='le_filter_".$this->name."_selected_column' value='' />\n";
                echo "  </td>\n";

                echo "  <td>\n";
                echo "<select disabled='disabled' name='le_filter_".$this->name."_val_select' >\n";
                    echo "      <option value=''>" . $LANGUAGE["le_filter_vyberte_hodnotu"] . "</option>\n";
                    if (isset($distinct_values['values'])) {
                            foreach ($distinct_values['values'] as $index => $oper) {
                                        echo "          <option value='".$oper['val']."'>".$oper['val']."</option>\n";
                            }
                    }

                    echo "</select>\n";
                        echo "  </td>\n";
                        echo "  <td>\n";
                    echo "<input disabled='disabled' type='submit' name='le_filter_".$this->name."_add_button' value='" . $LANGUAGE["le_pridat_filter"] . "' />\n";
                echo "  </td>\n";
            } else {
                echo "  <td>\n";
                echo "<select onchange=\"submit_frm('list_element_" . $form_name . "')\" disabled='disabled' name='le_filter_".$this->name."_col_select' >\n";
                        echo "  <option value=''>" . $LANGUAGE["le_filter_vyberte_sloupec"] . "</option>\n";
                    foreach ($this->values as $col) {
                                if ($col['db_col'] == $current_col)
                                        $selected = 'selected="selected"';
                                else
                                        $selected = "";

                                echo "  <option $selected value='".$col['db_col'].";".$col['name']."'>".$col['name']."</option>\n";
                    }

                    echo "</select>\n";

                    echo "<script language='JavaScript' type=\"text/javascript\">\n";
                    echo "<!--\n";
                    echo "      document.forms['list_element_" . $form_name . "'].le_filter_".$this->name."_col_select.disabled = false;\n";
                    echo "-->\n";
                    echo "</script>";

                    echo "<noscript>\n";
                    echo "      <input type='submit' name='le_filter_".$this->name."_unset_column' value='Zrušit' />\n";
                echo "</noscript>\n";
                echo "<input type='hidden' name='le_filter_".$this->name."_selected_column' value='" . $this->current_value['col'] . "' />\n";
                echo "  </td>\n";

                echo "  <td>\n";
                    echo "<select name='le_filter_".$this->name."_val_select' >\n";
                    echo "      <option value=''>" . $LANGUAGE["le_filter_vyberte_hodnotu"] . "</option>\n";
                    if (isset($distinct_values['values'])) {
                            foreach ($distinct_values['values'] as $index => $oper) {
                                        echo "          <option value='".$oper['val']."'>".$oper['val']."</option>\n";
                            }
                    }
                    echo "</select>\n";
                    echo "      </td>\n";

                echo "  <td>\n";
                    echo "<input type='submit' name='le_filter_".$this->name."_add_button' value='" . $LANGUAGE["le_pridat_filter"] . "' />\n";
                echo "  </td>\n";
            }
            echo "      </tr>\n";
            echo "</table>\n";

            foreach ($this->filters as $index => $filter) {
                        if ($draw_hidden) {
                                echo "<input type='hidden' name='le_filter_".$this->name."_".$index."_enabled' value='".$filter['enabled']."' />\n";
                                echo "<input type='hidden' name='le_filter_".$this->name."_".$index."_col' value='".$filter['value']['col']."' />\n";
                                echo "<input type='hidden' name='le_filter_".$this->name."_".$index."_col_name' value='".$filter['value']['col_name']."' />\n";
                                echo "<input type='hidden' name='le_filter_".$this->name."_".$index."_val' value='".$filter['value']['val']."' />\n";
                    }
            }
            if ($this->filters) {
                    echo "<table class=\"filters\">\n";
                        foreach ($this->filters as $index => $filter) {
                            if ($filter['enabled']) {
                                echo "<tr class=\"enabled\">\n";
                                        echo "  <td><input type='submit' title=\"zakázat filtr\" name='le_filter_".$this->name."_".$index."_disable_button' value='-' /></td>\n";
                            } else {
                                        echo "<tr class=\"disabled\">\n";
                                echo "  <td><input type='submit' title=\"povolit filtr\" name='le_filter_".$this->name."_".$index."_enable_button' value='+' /></td>\n";
                            }
                            echo "  <td>".$filter['value']['col_name']."</td><td> = </td><td>".$filter['value']['val']."</td>\n";
                            echo "      <td><input type='submit' title=\"zrušit výběr\" name='le_filter_".$this->name."_".$index."_del_button' value='odebrat'/></td>\n";
                            echo "</tr>\n";

                        }
                        echo "</table>\n";
            } else {
                echo "<br />\n";
            }

        }

        /**
         * @brief Upravuje databázový dotaz podle aktuálních hodnot filtru
         *
         * @param $query Databázový dotaz
         *
         */
        function update_query(&$query) {
                $cond = '';

                foreach ($this->filters as $filter) {
                        if ($filter['enabled'] && ($filter['value']['col'] != '') && ($filter['value']['val'] != '')) {

                                if ($cond != '') {
                                        $cond .= ' AND ';
                                }

                                $cond .= "`".$filter['value']['col']."` = '".$filter['value']['val']."'";
                        }
                }

                if ($cond != '') {
                        if ($query['where'] != "") {
                                        $query['where'] .= "AND ".$cond." ";
                                } else {
                                        $query['where'] = " WHERE ".$cond." ";
                                }
                }
        }
}

class column_values_compare_filter extends filter{
        /** pole s operatory filtru */
        var $compare_opers;
        var $filters = array();
        var $filters_count = 0;
        /**
         * @brief Inicializuje třídu filtru
         *
         * @param $filter_name Název filtru
         * @param $filter_values Pole indexované od 1, obsahující pole s definicí sloupců ze kterých se filtr vytvoří.
         *                Pod indexem 'col' je zadán název sloupce v databázové tabulce  a 'name' je název který se zobrazí
         *                jako kategorie ve filtru. Filtr bude obsahovat všechny hodnoty, které se vyskytují v zadaném sloupci v tabulce.
         *                Příklad:  $filter_array = array (1 => array('db_col' => 'sloupec1', 'name' => 'Název1'),
                                                                                    array('db_col' => 'sloupec2', 'name' => 'Název2'));
         *                kde 'sloupec1' a 'sloupec2' jsou definované v tabulce zadané při vytváření list_elementu ke
         *                kterému filtr vytváříme.
         * @param $current_value Nepovinný parametr, pole kde pod indexem 'col' resp. 'val' je název sloupce
         *                resp. hodnota na kterou se má filtr nastavit
         *
         * Nastaví vstupní parametry do odpovídajících proměnných třídy.
         *
         */
        function column_values_compare_filter($filter_name, $filter_values, $current_value = array('col' => '', 'comp' => '', 'val' => '')) {

                /** inicializace pole operatoru */
                $this->compare_opers = array ('' => '--Vyberte--',
                                                                                'eq' => '=',
                                                                                'gt' => '&gt;',
                                                                                'lt' => '&lt;',
                                                                                'ge' => '&gt;=',
                                                                                'le' => '&lt;=',
                                                                                'like' => 'obsahuje',
                                                                                'notlike' => 'neobsahuje');

                if (isset($_POST["le_filter_".$filter_name."_filters_count"])) {
                        $this->filters_count = $_POST["le_filter_".$filter_name."_filters_count"];
                } else {
                        $this->filters_cout = 0;
                }

                $index = 0;
                $i = 0;
                $del = false;
                while ($index < $this->filters_count) {
                        if (!isset($_POST["le_filter_".$filter_name."_".$index."_del_button"])) {
                                if (isset($_POST["le_filter_".$filter_name."_".$index."_col"]) && ($_POST["le_filter_".$filter_name."_".$index."_col"] != '')
                                         && isset($_POST["le_filter_".$filter_name."_".$index."_comp"]) && ($_POST["le_filter_".$filter_name."_".$index."_comp"] != '--Vyberte hodnotu--')
                                         && isset($_POST["le_filter_".$filter_name."_".$index."_val"]) && ($_POST["le_filter_".$filter_name."_".$index."_val"] != '--Vyberte hodnotu--')) {

                                        $this->filters[$i]['enabled'] = $_POST["le_filter_".$filter_name."_".$index."_enabled"];
                                        $this->filters[$i]['value']['col'] = $_POST["le_filter_".$filter_name."_".$index."_col"];
                                        $this->filters[$i]['value']['col_name'] = $_POST["le_filter_".$filter_name."_".$index."_col_name"];
                                        $this->filters[$i]['value']['val'] = $_POST["le_filter_".$filter_name."_".$index."_val"];
                                        $this->filters[$i]['value']['comp'] = $_POST["le_filter_".$filter_name."_".$index."_comp"];
                                }

                                if (isset($_POST["le_filter_".$filter_name."_".$index."_disable_button"])) {
                                        $this->filters[$i]['enabled'] = false;
                                }
                                if (isset($_POST["le_filter_".$filter_name."_".$index."_enable_button"])) {
                                        $this->filters[$i]['enabled'] = true;
                                }
                                $index++;
                                $i++;
                        } else {
                                $del = true;
                                $index++;
                        }
                }

                if ($del) {
                        $this->filters_count--;
                }

                if (isset($_POST["le_filter_".$filter_name."_add_button"])
                        && ($_POST["le_filter_".$filter_name."_col_select"] != '')
                                && ($_POST["le_filter_".$filter_name."_comp_select"] != '')
                                && ($_POST["le_filter_".$filter_name."_val_input"] != '') ) {

                        $this->filters[$this->filters_count]['enabled'] = true;

                        $values = explode(";", $_POST["le_filter_".$filter_name."_col_select"]);
                        $this->filters[$this->filters_count]['value']['col'] = $values[0];
                        $this->filters[$this->filters_count]['value']['col_name'] = $values[1];
                        $this->filters[$this->filters_count]['value']['comp'] = $_POST["le_filter_".$filter_name."_comp_select"];
                        $this->filters[$this->filters_count]['value']['val'] = $_POST["le_filter_".$filter_name."_val_input"];

                        $this->filters_count++;
                }

                if ($_POST["le_filter_".$filter_name."_selected_column"] != '') {
                        $current_value['col'] = $_POST["le_filter_".$filter_name."_selected_column"];
                }

                if (isset($_POST["le_filter_".$filter_name."_set_column"])) {

                        $current_value['col'] = $_POST["le_filter_".$filter_name."_col_select"];
                }

                parent::filter($filter_name, $filter_values, $current_value);

        }

        /**
         * @brief Generuje hodnoty pro filtr
         *
         * @param $db Ukazatel na třídu pro obsluhu databáze
         * @param $db_table Tabulka ze které se tvoří #list_element
         *
         * @return Vrací pole ve kterém jsou pro každý sloupec
         * všechny hodnoty, které se v něm vyskytují.
         *
         */
        function generate_distinct_values($db, $db_table) {
                $array_ret = array();

                foreach ($this->values as $index => $col ) {
                        $array_ret[$index]['db_col'] = $col['db_col'];
                        $array_ret[$index]['name'] = $col['name'];
                        $array_ret[$index]['values'][0] = '--Vyberte hodnotu--';

                        $query = "SELECT DISTINCT `".$col['db_col']."` FROM ".$db_table." ";
                        if (!($res = $db->Query($query)) ){
                                $ERROR = array ('line'=>__LINE__, 'file'=>__FILE__,
                                        'err_text'=> "Data pro filtr se nepodařilo načíst z databáze\nDotaz: $query",
                                        'err_usr_text'=> 'Chyba spojení s databází',
                                        'err_level'=>$ERROR_LEVEL_FATAL);
                                $error->add_error($ERROR);
                        }
                        while ($row = $db->GetFetchAssoc()) {
                                $array_ret[$index]['values'][] = $row[$col['db_col']];
                        }
                }
                return $array_ret;
        }

        /**
         * @brief Vykresluje filtr do dokumentu
         *
         * @param $db Ukazatel na třídu pro obsluhu databáze
         * @param $db_table Tabulka ze které se tvoří #list_element
         * @param $draw_hidden Nepovinný parametr, když je nastaven na 0, pak se při vykreslení
         *                nevygenerují do dokumentu hidden input elementy obsahující aktuální hodnoty
         *        filtru. Pokud obsahuje něco jiného pak se vygenerují i tyto elementy.
         *
         */
        function draw($db = null, $db_table = null, $draw_hidden = 1, $form_name = '') {
                $distinct_values = $this->generate_distinct_values($db, $db_table);

                $values = explode(";", $this->current_value['col']);
                $current_col = array_get_index_safe(0, $values);
                $current_col_name = array_get_index_safe(1, $values);

            echo "<input type='hidden' name='le_filter_".$this->name."_filters_count' value='".$this->filters_count."' />\n";

            echo "<select name='le_filter_".$this->name."_col_select' >\n";
                echo "  <option value=''> --Vyberte sloupec-- </option>\n";
            foreach ($this->values as $col) {
                        if ($col['db_col'] == $current_col)
                                $selected = 'selected="selected"';
                        else
                                $selected = "";

                        echo "  <option $selected value='".$col['db_col'].";".$col['name']."'>".$col['name']."</option>\n";
            }

            echo "</select>\n";

            echo "<input type='submit' name='le_filter_".$this->name."_set_column' value='Vybrat' />\n";

            if ($this->current_value['col'] == '--Vyberte sloupec--' || $this->current_value['col'] == '') {
                echo "<input type='hidden' name='le_filter_".$this->name."_selected_column' value='' />\n";

                echo "<select disabled='true' name='le_filter_".$this->name."_comp_select' >\n";

                    foreach ($this->compare_opers as $index => $oper) {
                                echo "          <option value='".$index."'>".$oper."</option>\n";
                    }

                    echo "</select>\n";

                    echo "<input readonly='readonly' onclick='clear_input(this)' type='text' name='le_filter_".$this->name."_val_input' value='".$this->current_value['val']."' />\n";

                    //echo "<input disabled='true' type='submit' name='le_filter_".$this->name."_add_button' value='Přidat filtr' />\n";
            } else {
                echo "<input type='hidden' name='le_filter_".$this->name."_selected_column' value='" . $this->current_value['col'] . "' />\n";

                    echo "<select name='le_filter_".$this->name."_comp_select' >\n";

                    foreach ($this->compare_opers as $index => $oper) {
                                echo "          <option value='".$index."'>".$oper."</option>\n";
                    }

                    echo "</select>\n";

                    echo "<input onclick='clear_input(this)' type='text' name='le_filter_".$this->name."_val_input' value='".$this->current_value['val']."' />\n";

                    echo "<input type='submit' name='le_filter_".$this->name."_add_button' value='Přidat filtr' />\n";
            }

            foreach ($this->filters as $index => $filter) {
                        if ($draw_hidden) {
                                echo "<input type='hidden' name='le_filter_".$this->name."_".$index."_enabled' value='".$filter['enabled']."' />\n";
                                echo "<input type='hidden' name='le_filter_".$this->name."_".$index."_col' value='".$filter['value']['col']."' />\n";
                                echo "<input type='hidden' name='le_filter_".$this->name."_".$index."_col_name' value='".$filter['value']['col_name']."' />\n";
                                echo "<input type='hidden' name='le_filter_".$this->name."_".$index."_comp' value='".$filter['value']['comp']."' />\n";
                                echo "<input type='hidden' name='le_filter_".$this->name."_".$index."_val' value='".$filter['value']['val']."' />\n";
                    }
            }
            if ($this->filters) {
                    echo "<table class=\"filters\">\n";
                        foreach ($this->filters as $index => $filter) {
                            if ($filter['enabled']) {
                                echo "<tr class=\"enabled\">\n";
                                        echo "  <td><input type='submit' title=\"zakázat filtr\" name='le_filter_".$this->name."_".$index."_disable_button' value='-' /></td>\n";
                            } else {
                                        echo "<tr class=\"disabled\">\n";
                                echo "  <td><input type='submit' title=\"povolit filtr\" name='le_filter_".$this->name."_".$index."_enable_button' value='+' /></td>\n";
                            }
                            echo "  <td>".$filter['value']['col_name']."</td><td>".$this->compare_opers[$filter['value']['comp']]."</td><td>".$filter['value']['val']."</td>\n";
                            echo "      <td><input type='submit' title=\"zrušit výběr\" name='le_filter_".$this->name."_".$index."_del_button' value='odebrat'/></td>\n";
                            echo "</tr>\n";

                        }
                        echo "</table>\n";
            } else {
                echo "<br />\n";
            }

        }

        /**
         * @brief Upravuje databázový dotaz podle aktuálních hodnot filtru
         *
         * @param $query Databázový dotaz
         *
         */
        function update_query(&$query) {
                $cond = '';

                foreach ($this->filters as $filter) {
                        if ($filter['enabled'] && ($filter['value']['col'] != '') && ($filter['value']['val'] != '') && ($filter['value']['comp'] != '')) {

                                if ($cond != '') {
                                        $cond .= ' AND ';
                                }

                                switch ($filter['value']['comp']) {
                                        case 'eq':
                                                $cond .= "`".$filter['value']['col']."` = '".$filter['value']['val']."'";
                                        break;
                                        case 'lt':
                                                $cond .= "`".$filter['value']['col']."` < '".$filter['value']['val']."'";
                                        break;
                                        case 'gt':
                                                $cond .= "`".$filter['value']['col']."` > '".$filter['value']['val']."'";
                                        break;
                                        case 'le':
                                                $cond .= "`".$filter['value']['col']."` <= '".$filter['value']['val']."'";
                                        break;
                                        case 'ge':
                                                $cond .= "`".$filter['value']['col']."` >= '".$filter['value']['val']."'";
                                        break;
                                        case 'like':
                                                $cond .= "`".$filter['value']['col']."` LIKE '%".$filter['value']['val']."%'";
                                        break;
                                        case 'notlike':
                                                $cond .= "`".$filter['value']['col']."` NOT LIKE '%".$filter['value']['val']."%'";
                                        break;
                                }
                        }
                }

                if ($cond != '') {
                        if ($query['where'] != "") {
                                        $query['where'] .= "AND ".$cond." ";
                                } else {
                                        $query['where'] = " WHERE ".$cond." ";
                                }
                }
        }
}

class compare_filter extends filter{
        /** pole s operatory filtru */
        var $compare_opers;
        var $filters = array();
        var $filters_count = 0;
        /**
         * @brief Inicializuje třídu filtru
         *
         * @param $filter_name Název filtru
         * @param $filter_values Pole indexované od 1, obsahující pole s definicí sloupců ze kterých se filtr vytvoří.
         *                Pod indexem 'col' je zadán název sloupce v databázové tabulce  a 'name' je název který se zobrazí
         *                jako kategorie ve filtru. Filtr bude obsahovat všechny hodnoty, které se vyskytují v zadaném sloupci v tabulce.
         *                Příklad:  $filter_array = array (1 => array('db_col' => 'sloupec1', 'name' => 'Název1'),
                                                                                    array('db_col' => 'sloupec2', 'name' => 'Název2'));
         *                kde 'sloupec1' a 'sloupec2' jsou definované v tabulce zadané při vytváření list_elementu ke
         *                kterému filtr vytváříme.
         * @param $current_value Nepovinný parametr, pole kde pod indexem 'col' resp. 'val' je název sloupce
         *                resp. hodnota na kterou se má filtr nastavit
         *
         * Nastaví vstupní parametry do odpovídajících proměnných třídy.
         *
         */
        function compare_filter($filter_name, $filter_values, $current_value = array('col' => '', 'comp' => '', 'val' => '')) {

                /** inicializace pole operatoru */
                $this->compare_opers = array ('' => '--Vyberte--',
                                                                                'eq' => '=',
                                                                                'gt' => '&gt;',
                                                                                'lt' => '&lt;',
                                                                                'ge' => '&gt;=',
                                                                                'le' => '&lt;=',
                                                                                'like' => 'obsahuje',
                                                                                'notlike' => 'neobsahuje');

                if (isset($_POST["le_filter_".$filter_name."_filters_count"])) {
                        $this->filters_count = $_POST["le_filter_".$filter_name."_filters_count"];
                } else {
                        $this->filters_cout = 0;
                }

                $index = 0;
                $i = 0;
                $del = false;
                while ($index < $this->filters_count) {
                        if (!isset($_POST["le_filter_".$filter_name."_".$index."_del_button"])) {
                                if (isset($_POST["le_filter_".$filter_name."_".$index."_col"]) && ($_POST["le_filter_".$filter_name."_".$index."_col"] != '')
                                         && isset($_POST["le_filter_".$filter_name."_".$index."_comp"]) && ($_POST["le_filter_".$filter_name."_".$index."_comp"] != '--Vyberte hodnotu--')
                                         && isset($_POST["le_filter_".$filter_name."_".$index."_val"]) && ($_POST["le_filter_".$filter_name."_".$index."_val"] != '--Vyberte hodnotu--')) {

                                        $this->filters[$i]['enabled'] = $_POST["le_filter_".$filter_name."_".$index."_enabled"];
                                        $this->filters[$i]['value']['col'] = $_POST["le_filter_".$filter_name."_".$index."_col"];
                                        $this->filters[$i]['value']['col_name'] = $_POST["le_filter_".$filter_name."_".$index."_col_name"];
                                        $this->filters[$i]['value']['val'] = $_POST["le_filter_".$filter_name."_".$index."_val"];
                                        $this->filters[$i]['value']['comp'] = $_POST["le_filter_".$filter_name."_".$index."_comp"];
                                }

                                if (isset($_POST["le_filter_".$filter_name."_".$index."_disable_button"])) {
                                        $this->filters[$i]['enabled'] = false;
                                }
                                if (isset($_POST["le_filter_".$filter_name."_".$index."_enable_button"])) {
                                        $this->filters[$i]['enabled'] = true;
                                }
                                $index++;
                                $i++;
                        } else {
                                $del = true;
                                $index++;
                        }
                }

                if ($del) {
                        $this->filters_count--;
                }

                if (isset($_POST["le_filter_".$filter_name."_add_button"])
                        && ($_POST["le_filter_".$filter_name."_col_select"] != '')
                                && ($_POST["le_filter_".$filter_name."_comp_select"] != '')
                                && ($_POST["le_filter_".$filter_name."_val_input"] != '') ) {

                        $this->filters[$this->filters_count]['enabled'] = true;

                        $values = explode(";", $_POST["le_filter_".$filter_name."_col_select"]);
                        $this->filters[$this->filters_count]['value']['col'] = $values[0];
                        $this->filters[$this->filters_count]['value']['col_name'] = $values[1];
                        $this->filters[$this->filters_count]['value']['comp'] = $_POST["le_filter_".$filter_name."_comp_select"];
                        $this->filters[$this->filters_count]['value']['val'] = $_POST["le_filter_".$filter_name."_val_input"];

                        $this->filters_count++;
                }
                parent::filter($filter_name, $filter_values, $current_value);
        }

        /**
         * @brief Vykresluje filtr do dokumentu
         *
         * @param $db Ukazatel na třídu pro obsluhu databáze
         * @param $db_table Tabulka ze které se tvoří #list_element
         * @param $draw_hidden Nepovinný parametr, když je nastaven na 0, pak se při vykreslení
         *                nevygenerují do dokumentu hidden input elementy obsahující aktuální hodnoty
         *        filtru. Pokud obsahuje něco jiného pak se vygenerují i tyto elementy.
         *
         */
        function draw($db = null, $db_table = null, $draw_hidden = 1, $form_name = '') {

            echo "<input type='hidden' name='le_filter_".$this->name."_filters_count' value='".$this->filters_count."' />\n";

            echo "<select name='le_filter_".$this->name."_col_select' >\n";
                echo "  <option value=''> --Vyberte sloupec-- </option>\n";
            foreach ($this->values as $col) {
                        if ($col['db_col'] == $this->current_value['col'])
                                $selected = 'selected="selected"';
                        else
                                $selected = "";

                        echo "  <option $selected value='".$col['db_col'].";".$col['name']."'>".$col['name']."</option>\n";
            }

            echo "</select>\n";

            echo "<select name='le_filter_".$this->name."_comp_select' >\n";

            foreach ($this->compare_opers as $index => $oper) {
                        echo "          <option value='".$index."'>".$oper."</option>\n";
            }

            echo "</select>\n";

            echo "<input onclick='clear_input(this)' type='text' name='le_filter_".$this->name."_val_input' value='".$this->current_value['val']."' />\n";

            echo "<input type='submit' name='le_filter_".$this->name."_add_button' value='Přidat filtr' />\n";

            foreach ($this->filters as $index => $filter) {
                        if ($draw_hidden) {
                                echo "<input type='hidden' name='le_filter_".$this->name."_".$index."_enabled' value='".$filter['enabled']."' />\n";
                                echo "<input type='hidden' name='le_filter_".$this->name."_".$index."_col' value='".$filter['value']['col']."' />\n";
                                echo "<input type='hidden' name='le_filter_".$this->name."_".$index."_col_name' value='".$filter['value']['col_name']."' />\n";
                                echo "<input type='hidden' name='le_filter_".$this->name."_".$index."_comp' value='".$filter['value']['comp']."' />\n";
                                echo "<input type='hidden' name='le_filter_".$this->name."_".$index."_val' value='".$filter['value']['val']."' />\n";
                    }
            }
            if ($this->filters) {
                    echo "<table class=\"filters\">\n";
                        foreach ($this->filters as $index => $filter) {
                            if ($filter['enabled']) {
                                echo "<tr class=\"enabled\">\n";
                                        echo "  <td><input type='submit' title=\"zakázat filtr\" name='le_filter_".$this->name."_".$index."_disable_button' value='-' /></td>\n";
                            } else {
                                        echo "<tr class=\"disabled\">\n";
                                echo "  <td><input type='submit' title=\"povolit filtr\" name='le_filter_".$this->name."_".$index."_enable_button' value='+' /></td>\n";
                            }
                            echo "  <td>".$filter['value']['col_name']."</td><td>".$this->compare_opers[$filter['value']['comp']]."</td><td>".$filter['value']['val']."</td>\n";
                            echo "      <td><input type='submit' title=\"odebrat filtr\" name='le_filter_".$this->name."_".$index."_del_button' value='odebrat'/></td>\n";
                            echo "</tr>\n";

                        }
                        echo "</table>\n";
            } else {
                echo "<br />\n";
            }

        }

        /**
         * @brief Upravuje databázový dotaz podle aktuálních hodnot filtru
         *
         * @param $query Databázový dotaz
         *
         */
        function update_query(&$query) {
                $cond = '';

                foreach ($this->filters as $filter) {
                        if ($filter['enabled'] && ($filter['value']['col'] != '') && ($filter['value']['val'] != '') && ($filter['value']['comp'] != '')) {

                                if ($cond != '') {
                                        $cond .= ' AND ';
                                }

                                switch ($filter['value']['comp']) {
                                        case 'eq':
                                                $cond .= "`".$filter['value']['col']."` = '".$filter['value']['val']."'";
                                        break;
                                        case 'lt':
                                                $cond .= "`".$filter['value']['col']."` < '".$filter['value']['val']."'";
                                        break;
                                        case 'gt':
                                                $cond .= "`".$filter['value']['col']."` > '".$filter['value']['val']."'";
                                        break;
                                        case 'le':
                                                $cond .= "`".$filter['value']['col']."` <= '".$filter['value']['val']."'";
                                        break;
                                        case 'ge':
                                                $cond .= "`".$filter['value']['col']."` >= '".$filter['value']['val']."'";
                                        break;
                                        case 'like':
                                                $cond .= "`".$filter['value']['col']."` LIKE '%".$filter['value']['val']."%'";
                                        break;
                                        case 'notlike':
                                                $cond .= "`".$filter['value']['col']."` NOT LIKE '%".$filter['value']['val']."%'";
                                        break;
                                }
                        }
                }

                if ($cond != '') {
                        if ($query['where'] != "") {
                                        $query['where'] .= "AND ".$cond." ";
                                } else {
                                        $query['where'] = " WHERE ".$cond." ";
                                }
                }
        }
}

class date_filter extends filter{
}

/**
 * @brief Abstraktní třída reprezentující akci
 *
 */
class action {
        var $name;
        var $title;
        var $action;

        function action($action_name, $action_title, $action) {
                $this->name = $action_name;
                $this->title = $action_title;
                $this->action = $action;
        }

        function draw() {
        }
}

/**
 * @brief Třída reprezentující jednoduchou akci zobrayovanou jako tlačítko
 *
 * Tato třída slouží pro vytvoření jednoduché akce. V dokumentu je vytvořeno tlačítko,
 * které je poté možno využít na pro práci s řádky seznamu vybranými pomocí checkboxů.
 *
 */
class simple_action extends action {

        /**
         * @brief Inicializuje třídu filtru
         *
         * @param $action_name Název akce
         * @param $action title Popisek akce
         * @param $action hodnota, která se odešle v případě aktivování akce, je odeslána v $_POST['le_action']
         *
         * Nastaví vstupní parametry do odpovídajících proměnných třídy.
         *
         */
        function class_action($action_name, $action_title, $action) {
                parent::action($action_name, $action_title, $action);
        }

        /**
         * @brief Vykresluje akci do dokumentu
         *
         * @param $db Ukazatel na třídu pro obsluhu databáze
         *
         */
        function draw($db = null) {
                echo "<input type='submit' name='le_action_".$this->name."' value='$this->title' />";
        }
}

class class_action extends action {
        var $clases;
        var $user_id;
        var $db;

        function class_action($action_name, $action_title, $action, $db, $user_id) {
                $this->user_id = $user_id;
                $this->db = $db;

                parent::action($action_name, $action_title, $action);

                $this->get_data_from_db();
        }

        function draw() {
                echo "<span> $this->title </span>\n";
                echo "<select name='le_action_".$this->name."_class' >\n";
                echo '  <option selected="selected" value="">--Vyberte třídu--</option>';
                foreach ($this->clases as $class) {
                        echo "  <option value='".$class['nazev_tridy']."'>".$class['nazev_tridy']."</option>";
                }
                echo "</select>";
                echo "<input type='submit' name='le_action_".$this->name."' value='Proveď' />";
        }

        function get_data_from_db() {
                global $LANGUAGE;

                $query = "SELECT *
                                        FROM `users_ext_lector`
                                        WHERE `user_id` = '$this->user_id'";

                $this->db->Query($query);
                $user = $this->db->GetFetchAssoc();

                $query = "SELECT *
                                        FROM `skoly_tridy`
                                        WHERE `skola_id` = '".$user['skola_id']."' ";

                $this->db->Query($query);

                $this->clases[]['nazev_tridy'] = $LANGUAGE['zadna_trida'];
                while ($row = $this->db->GetFetchAssoc()) {
                        $this->clases[] = $row;
                }
        }
}

class basicListElementPrinter extends pageElementPrinter {

        function print_element($element){
                $element->get_data_from_db();
                if (!$element->filled){
                        $ERROR = array ('line'=>__LINE__, 'file'=>__FILE__,
                                                        'err_text'=> "Element nedostal žádná data",
                                                        'err_usr_text'=> '',
                                                        'err_level'=>$ERROR_LEVEL_ERROR);
                        $error->add_error($ERROR);
                        return;
                }
            echo "<form class=\"list_element\" name='list_element_" . $element->name . "' action='".$element->current_url."' method='post'>\n";

            echo "<script language='JavaScript' type=\"text/javascript\">\n";
            echo "<!--\n";
            echo "function clear_input(name) {
                                        elem.value = \"\";
                                }\n";
            echo "      function check_all(elem, name) {
                                        var elemList;
                                        var frm;

                                        frm = document.forms[name];
                                        elemList = frm.getElementsByTagName('input');
                                        for (var i=0; i < elemList.length; i++) {
                                                if (elemList[i].type == 'checkbox') {
                                                        elemList[i].checked = elem.checked;
                                                }
                                        }
                                }\n";

            echo "-->\n";
            echo "</script>";

            if ($element->title != "") {
                echo "<h2>$element->title</h2>\n";
            }

            echo "<div class=\"list\">\n";

                // vykreslení filtrů
                $element->draw_filters();

                // vykreslení stránkování
                //$element->draw_page_selector();

                // vykreslení seznamu
                $element->draw_table();

                // vykreslení stránkování
                $element->draw_page_selector();

                // vykreslení akcí
                $element->draw_actions();

                echo "</div>\n";

            echo "</form>\n\n\n";
        }
}

/**
 * @brief Element layoutu umožnující jednoduché vytváření seznamů hodnot z databáze
 *
 * Tento element slouží k jednoduchému vytváření seznamů dat z databáze. Pro zadanou tabulku
 * a sloupce které se z ní mají použít vygeneruje seznam obsahující hodnoty z této tabulky.
 * Element umožňuje kliknutím na popisek sloupce, setřídit data podle hodnot tohoto sloupce a to
 * jak vzestupně tak sestupně. Dále je integrovámo stránkování a je možné k seznamu přířadit
 * nekolik filtrů, které usnadní vyhledávání. Je integrována podpora pro zobrazení cheboxů k
 * hromadnému výběr řádků tabulky a následné akce s nimi.
 *
 */
class listElement extends pageElement {
        /** data, která se zobrazí na aktuální stránce */
        var $data;
        /** indikuje zda byla data načtena z databáze */
        var $filled;
        /** ukazatel na třídu pro obsluhu databáze */
        var $db;
        /** data načtená z databáze */
        var $db_data;
        /** titulek seznamu */
        var $title;
        /** databázová tabulka ze které se data načítají */
        var $db_table;
        /** definuje sloupce seznamu */
        var $columns;
        /** atribut podle kterého se třídí */
        var $sort_by;
        /** směr třídění */
        var $sort_dir;
        /** aktuální stránka stránkování */
        var $current_page;
        /** délka stránky stránkování */
        var $page_length;
        /** počet stránek stránkování */
        var $page_count;
        /** url dokumentu */
        var $current_url;
        /** pole s akcemi seznamu */
        var $actions;
        /** značí zda se mají vykreslovat popisky sloupcu v tabulce*/
        var $draw_legend = true;
        /** pole s filtry seznamu */
        var $filters;
        /** objekt layoutu stránky */
        var $page;
        /** nazev listu*/
        var $name;
        /** parametry url, ktere se mají dále odesílat */
        var $url_params_to_send;
        /** řetězec, který se zobrazí pokud nebudou nalezeny žádné záznamy v databázi*/
        var $no_data_found;

        /**
         * @brief Inicializuje seznam.
         *
         * @param $db Ukazatel na třídu pro obsluhu databáze
         * @param &$page
         * @param $title Titulek seznamu
         * @param $db_table Tabulka ze které se tvoří seznam. Může být zadána jako název tabulky v databázi.
         *                Např.: 'nazev_tabulky'
         *                Nebo jako tabulka specifikovaná SQL dotazem.
         *                Např.: '(SELECT * FROM `nazev_tabulky` WHERE `nazev_sloupce` = 'hodnota') AS nazev_tabulky'
         * @param $columns Pole indexované od 1, obsahující pole s definicí sloupců ze kterých se seznam vytvoří.
         *                Pole definující sloupce může obsahovat následující indexy:
         *                      'name' - Obsahuje popisek sloupce, který se zobrazí v seznamu.
         *                                              Jako speciální hodnotu můžete použít hodnotu 'check_all'. Ta v popisu sloupce
         *                                              zobrazí checbox, který pak zaškrtává resp. odšktává checkboxy v celém sloupci.
         *                      'value' - Obsahuje řetězec, který se zobrazí ve sloupci. Pokud použijete v řetězci název sloupce
         *                                              z databázové tabulky uzavřený do složených závorek (např.: {sloupec1} ),
         *                                              pak se tento vyraz nahradí aktuání hodnotou danného sloupce z databáze.
         *                                              Jako speciální hodnotu můžete použít hodnotu 'checkbox'. Ta v celém sloupci zobrazí
         *                                              checkboxy, které je pak možno využít k hromadnému výběru položek seznamu.
         *                      'sort' - (nep.) Obsahuje název sloupce v databázové tabulce #db_table podle kterého se má třídit
         *                                              v případě, že uživatel zvolí třídění podle tohoto sloupce seznamu.
         *                      'fce' - (nep.) Funkce, která se aplikuje na hodnotu 'value' poté co jsou vloženy hodnoty z databáze.
         * @param $url Nepovinný parametr.
         * @param $page_length Nepovinný parametr. Počet záznamů na stránce
         * @param $printer Nepovinný parametr.
         *
         * Inicializuje seznam.
         *
         */
        function listElement($db, &$page, $title, $db_table, $columns, $name = '',
                                                        $param_array = array('section', 'page', 'subpage'), $url = '', $printer = null) {
                global $LANGUAGE;

                $this->db = $db;
                $this->printer = $printer;
                $this->title = $title;
                $this->db_table = $db_table;
                $this->columns = $columns;
                $this->page_length = 20;
                $this->name = $name;
                $this->url_params_to_send = $param_array;
                $this->no_data_found = $LANGUAGE['prazdny_seznam'];

                $this->register($page);

                $this->filters = array();
                $this->actions = array();

                $this->current_page = (isset($_POST['le_' . $this->name . '_current_page']) ? $_POST['le_' . $this->name . '_current_page'] : 1);

                if (isset($_POST['le_' . $this->name . '_page_button'])) {
                        $this->current_page = $_POST['le_' . $this->name . '_page_select'];
                } else {
                        isset($_POST['le_' . $this->name . '_page_first']) ? $this->current_page = 1 : '' ;
                        isset($_POST['le_' . $this->name . '_page_previous']) ? $this->current_page -= 1 : '' ;
                        isset($_POST['le_' . $this->name . '_page_next']) ? $this->current_page += 1 : '' ;
                        isset($_POST['le_' . $this->name . '_page_last']) ? $this->current_page = -1 : '' ;
                }

                $this->sort_by = (isset($_POST['le_' . $this->name . '_sort_by']) ? $_POST['le_' . $this->name . '_sort_by'] : '');
                $this->sort_dir = (isset($_POST['le_' . $this->name . '_sort_dir']) ? $_POST['le_' . $this->name . '_sort_dir'] : 'ASC');

                if ($this->columns) {
                foreach ($this->columns as $col) {
                                if (isset($col['sort']) && isset($_POST['le_' . $this->name . '_sort_'.$col['sort']])) {
                                        if ($col['sort'] == $this->sort_by) {
                                                if ($this->sort_dir == 'ASC') {
                                                        $this->sort_dir = 'DESC';
                                                } else {
                                                        $this->sort_dir = 'ASC';
                                                }
                                        } else {
                                                $this->sort_dir = 'ASC';
                                        }

                                        $this->sort_by = $col['sort'];
                                }
                }
                }

                if ($printer == null){
                        $this->printer = new basicListElementPrinter;
                }
                if ($url == '') {
                        $this->current_url = substr(htmlsafe($_SERVER['REQUEST_URI']), 0, strpos(htmlsafe($_SERVER['REQUEST_URI']), '?'));

                        $prvni = true;
                        foreach ($_GET as $index => $value) {
                                if (in_array($index, $this->url_params_to_send) && $value != '') {
                                        if ($prvni) {
                                                $this->current_url .= '?';
                                                $prvni = false;
                                        } else {
                                                $this->current_url .= '&amp;';
                                        }
                                        $this->current_url .= $index . "=" . $value;
                                }
                        }
                } else {
                        $this->current_url = $url;
                }
        }

        function set_draw_legend($dl) {
                $this->draw_legend = $dl;
        }

        function set_no_data_string($str) {
                $this->no_data_found = $str;
        }

        function set_page_length($length) {
                $this->page_length = $length;
        }

        /**
         * @breif Nastaví, podle kterého sloupce se má tabulka třídit.
         *
         * Nastaví třídění jen, pokud uživatel nezadal sám třídění podle
         * jiného sloupce.
         *
         * @param $column sloupec pro třídění
         * @param $direction směr třídění (ASC|DESC)
         */
        function set_sort_by($column, $direction) {
                /* pouze pokud uzivatel nezadal trideni */
                if ($this->sort_by == '') {
                        $this->sort_by = $column;
                        $this->sort_dir = (strtoupper($direction) == 'DESC') ? 'DESC' : 'ASC';
                }
        }

        /**
         * @brief Načte data z databáze.
         *
         */
        function get_data_from_db() {
            $query = array();
                $query['select'] = "SELECT * ";
            $query['from'] = "FROM ".$this->db_table." ";
            $query['where'] = "";
            $query['sort'] = "";

            if (isset($this->sort_by) && $this->sort_by != '') {
                        $query['sort'] .= "ORDER BY `".$this->sort_by."` $this->sort_dir";
            }

            foreach($this->filters as $filter) {
                        $filter->update_query($query);
                        //echo $query['where']."\n";
                }

                $query_string = implode("\n",$query);
                //echo $query_string;

                if (!($res = $this->db->Query($query_string)) ){
                        $ERROR = array ('line'=>__LINE__, 'file'=>__FILE__,
                                                        'err_text'=> "Data pro element se nepodařilo načíst z databáze\nDotaz: $query",
                                                        'err_usr_text'=> 'Chyba spojení s databází',
                                                        'err_level'=>$ERROR_LEVEL_FATAL);
                        $error->add_error($ERROR);
                        $filed = false;
                        return false;
                }
                while ($row = $this->db->GetFetchAssoc())
                        $this->db_data[] = $row;

                $rows_count = count($this->db_data);
                $this->page_count = ceil(count($this->db_data) / $this->page_length);

                if ($this->current_page < 0) {
                        $this->current_page = $this->page_count;
                } else {
                        $this->current_page = max(1, min($this->page_count, $this->current_page));
                }

                for($i = ($this->current_page - 1) * $this->page_length; $i < min($this->current_page * $this->page_length, $rows_count); $i++) {
                        $this->data[] = $this->db_data[$i];
                }

                $this->filled = true;
        }

        /**
         * @brief Přidá nový filtr do seznamu filtrů elementu.
         *
         */
        function add_filter($filter) {
                $this->filters[] = $filter;
        }

        /**
         * @brief Vykreslí filtry elementu.
         *
         * @param $draw_hidden Značí zda se mají vykreslovat hidden elementy filtrů
         *
         */
        function draw_filters($draw_hidden = 1) {
                if (count($this->filters) > 0) {

                        echo "<div class=\"filters\">\n";

                        foreach($this->filters as $filter) {
                                $filter->draw($this->db, $this->db_table, $draw_hidden, $this->name);

                                echo "<br/>";
                        }

                        echo "</div>\n\n";
                }
        }

        /**
         * @brief Vykreslí akce elementu.
         *
         */
        function draw_actions() {
                if (count($this->actions) > 0) {

                        echo "\n<br />\n\n<div class=\"actions\">\n";

                        foreach($this->actions as $action) {
                                $action->draw();
                        }

                        echo "</div>\n\n";
                }
        }

        /**
         * @brief Přidá novou akci do seznamu akcí elementu.
         *
         */
        function add_action($action) {
                $this->actions[] = $action;
        }

        /**
         * @brief Vykreslí tabulku seznamu.
         *
         */
        function draw_table() {
                global $LANGUAGE;

                echo "<input type='hidden' name='le_" . $this->name . "_sort_by' value='".$this->sort_by."' />\n";
            echo "<input type='hidden' name='le_" . $this->name . "_sort_dir' value='".$this->sort_dir."' />\n";
            echo "<input type='hidden' name='le_" . $this->name . "_current_page' value='".$this->current_page."' />\n";
                echo "<input type='hidden' name='le_" . $this->name . "_db_table' value=\"".$this->db_table."\" />\n";
            echo "<table class=\"list_element\" border=\"1px\">\n";

            if ($this->draw_legend) {
                    echo "  <tr class=\"first_row\">\n";
                    if ($this->columns) {
                        foreach ($this->columns as $col) {
                                        $dir = "";
                                        $mark = "";
                                if (isset($col['sort']) && $this->sort_by == $col['sort']) {
                                        if ($this->sort_dir == 'ASC') {
                                                echo "    <td class='order_asc chcolorbgr'><input type='submit' name='le_" . $this->name . "_sort_".$col['sort']."' value='".$col['name']."' />$mark</td>\n";
                                        } else {
                                                echo "    <td class='order_desc chcolorbgr'><input type='submit' name='le_" . $this->name . "_sort_".$col['sort']."' value='".$col['name']."' />$mark</td>\n";
                                        }
                                } else {
                                        if(isset($col['sort']))
                                                echo "    <td><input type='submit' name='le_" . $this->name . "_sort_".$col['sort']."' value='".$col['name']."' />$mark</td>\n";
                                                else {
                                                        if ($col['name'] == 'check_all') {
                                                                echo "    <td><input type='checkbox' name='le_" . $this->name . "_check_all' onclick=\"check_all(this, 'list_element_" . $this->name . "')\"></input></td>\n";
                                                        } else {
                                                                echo "    <td>".$col['name']."</td>\n";
                                                        }
                                                }
                                }
                        }
                    }
                    echo "  </tr>\n";
            }

            if ($this->data && count($this->data) > 0) {
                foreach ($this->data as $gen) {
                        echo "  <tr class=\"row\">\n";
                                if ($this->columns) {
                                        foreach ($this->columns as $col) {
                                                $field_text = $col['value'];
                                                        foreach ($gen as $index => $value){
                                                                if(StrStr($field_text, '{'.$index.'}')) {
                                                                        $field_text = str_replace('{'.$index.'}', htmlsafe($value), $field_text);
                                                                } else {
                                                                        if(StrStr($field_text, '{fce:'.$index.'}')) {
                                                                                eval("\$val = ".$col['fce']."('".$gen[$index]."');");

                                                                                //echo
                                                                                $field_text = str_replace('{fce:'.$index.'}', $val, $field_text);
                                                                                //echo "yyy $field_text yyy";
                                                                        }
                                                                }
                                                        }
                                                        switch ($field_text) {
                                                                case 'checkbox':
                                                                        echo "    <td><input type='checkbox' value='".$gen['id']."' name='le_" . $this->name . "_check[".$gen['id']."]'></input></td>\n";
                                                                        break;
                                                                default:
                                                                        // pokud je  zadána funkce, pak se použije na modifikaci vygenerovaného textu
                                                                        /*
                                                                        if (isset($col['fce'])) {
                                                                                $val = '';
                                                                                eval("\$val = ".$col['fce']."('$field_text');");
                                                                                echo "    <td>". $val."</td>\n";
                                                                        } else {

                                                                                echo "    <td>" ."aaa". $field_text . "</td>\n";
                                                                        }
                                                                        break;
                                                                        */
                                                                        echo "    <td>". $field_text . "</td>\n";
                                                        }
                                        }
                                }
                        echo "  </tr>\n";
                }
            } else {
                echo '<tr  class="row"><td align="center" colspan="' . count($this->columns) . '">' . $this->no_data_found . '</td></tr>' . "\n";
            }
            echo "</table>\n";
        }

        /**
         * @brief Vykreslí stránkovač seznamu.
         *
         */
        function draw_page_selector() {
                if ($this->page_count > 1) {
                        if ($this->current_page != 1) {
                                echo "<input class='page_selector' type='submit' name='le_" . $this->name . "_page_first' value='&lt;&lt;' />\n";
                                echo "<input class='page_selector' type='submit' name='le_" . $this->name . "_page_previous' value='&lt;' />\n";
                }
                    echo "<select name='le_" . $this->name . "_page_select'>\n";
                for($i = 1; $i <= $this->page_count; $i++) {
                        $tag = "";
                                if ($this->sort_by) {
                                        $first = max(0, ($i - 1) * $this->page_length);
                                        $last = min(count($this->db_data) - 1, ($i * $this->page_length) - 1);

                                        $fce = null;
                                        foreach($this->columns as $col) {
                                                if ((isset($col['sort'])) && ($col['sort'] == $this->sort_by) && (isset($col['fce']))) {
                                                        $fce = $col['fce'];
                                                }
                                        }

                                        if (isset($fce)) {
                                                $val = '';
                                                eval("\$val = ".$fce."('".$this->db_data[$first][$this->sort_by]."');");
                                                $first_value = $val;
                                                eval("\$val = ".$fce."('".$this->db_data[$last][$this->sort_by]."');");
                                                $last_value = $val;
                                        } else {
                                                $first_value = $this->db_data[$first][$this->sort_by];
                                                $last_value = $this->db_data[$last][$this->sort_by];
                                        }
                                        $tag = " - [$first_value]";
                        }
                                if ($i == $this->current_page) {
                                echo "  <option selected='selected' class='current_page' value='$i'>".$i.$tag."</option>\n";
                                } else {
                                echo "  <option value='$i'>".$i.$tag."</option>\n";
                                }
                }
                    echo "</select>\n";

                    echo "<input class='page_selector' type='submit' name='le_" . $this->name . "_page_button' value='Přejít' />\n";

                    if ($this->current_page != $this->page_count) {
                        echo "<input class='page_selector' type='submit' name='le_" . $this->name . "_page_next' value='&gt;' />\n";
                        echo "<input class='page_selector' type='submit' name='le_" . $this->name . "_page_last' value='&gt;&gt;' />\n";
                    }
            }
        }

        function register(&$page, $form_skin_dir){
                parent::register($page, $form_skin_dir);
                $this->page = &$page;
                //$this->page->add_css("nocss");
        }

        function draw() {
                //parent::draw();
                $this->printer->print_element($this);
        }
}
/**
 * @}
 * @}
 */
?>