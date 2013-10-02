<?php

/**
 * @file layout.php
 * @brief Třída zajišťující vykreslení stránky.
 * @author Tomas Pop
 *
 * @addtogroup g_framework
 * @{
 * @addtogroup g_layout Layout
 * @{
 *
 * @brief Třída k uchování informací o datech na stránce.
 */



/**
 * @brief Třída k uchování informací o datech na stránce.
 *
 * Je navržena kvůli požadavku
 * na skinovatelnost, abychom odsáhli pokud možno co největšího oddělění dat od
 * vzhledu.
 * Umožňuje nastavovat jednotlivé parametry stránky jako je třeba nadpis a podobně,
 * navíc umožňuje volat v průběhu vykreslení stránky funkce, nebo jako části
 * stránky použít elementy.
 *
 * Elementy musí mít povinné funkce register() a draw().
 * První se volá, při připojení elementu ke stránce, druhý se volá ve chvíli,
 * kdy se má element vykreslit. Mělo by to usnadnit rekursivní přístup k
 * rekursivnímu objektu jako je stránka
 *
 *
 */
class Page {

        /** typ dokumentu, např. xhtm 1.0 transitional nebo html 4.0 strict. */
        var $document_type;

        /** Autor dokumetu. Vyplňuje pole <b>meta author</b> */
        var $document_author;

    /** Obsah pole <b>meta robots</b>, default je nastaven na index,follow. */
    var $robots;

    /** Natavuje <b>meta http-equiv="Content-language"</b> element. */
    var $document_language;

    /** Znaková sada dokumentu, obsah <b>meta http-equiv="Content-Type"
                </b> Defaultní je  utf-8.
        */
    var $document_charset;

    /** Obsah pole <b>meta copyright</b> */
    var $copyright;

    /** Nastavuje <b>meta description</b> */
    var $document_description;

    /** Pole cest k souborům s kaskádovými styly */
    var $css;

    /** Pole cest k souborům se skripty (JavaScript a VBscript) */
    var $scripsts;

    /** Pole cest k souborům, které budou přímo vloženy
         *      do hlavičky stránky
         */
    var $directInputScripts;

    /**  Pole subelementů stránky, viz popis souboru a popis mechanismu */
    var $elements;

    /** Volání funkce. V některých situacích může být šikovné
     *  mít možnost zavolat v nějakém okamžiku vykreslování
         *      stránky nějakou funkci. Příklad: chcete na stránky vložit fórum,
         *      které je realizováno funkcí phorum($thread_id). To provedete tak, že
         *      zaregistrujete phorum(10) a v printeru stránky tuto funci pomocí id
         *      zavoláte.
         */
    var $calls;

    /** Obsah tagu <b>title</b> */
    var $title;

    /** Nadpis, předpokládané užití je obsah tagu <b>h1</b> */
    var $headline;

    /** Pole položek menu. Každý prvek má indexy text, link */
    var $menuitems;

    /** pole textů, které jsou součástí stránky, zjednodušení v případě, kdy
         *      je mechanismus elementů zbytečně komplikovaný. Použití pro rozsáhlejší
         *      texty nedoporučujeme, bývají často zdrojem chyb ve validitě.
         */
    var $texts;

    /** Pole krátkých textů s id. Viz add_private_text() */
    var $private_texts;

    /** Printer, třída která má na starosti vykreslení (tj. grafické  spracování)
         *      obsahu stránky. Je naprosto nepřijatelné, aby obsahoval kód, který se
         *      nějakým způsobem  podílí na logice aplikace.
         */
    var $printer;

    /** Podadresář v adresari skinu, kde jsou uloženy potřebné styly a printery
         *      pro právě spracovávanou stránku. Např. <b>titulka</b>
         */
    var $page_skin_subdir;

    /** Název adresáře, kde jsou uložené podaresáře s printery, grafikou a css
         *      styly jednotlivých stránek. Např. <b>default</b>
         */
    var $skin;

    /** Cesta k adresáři s printery, css styly a ostatní grafikou relativní
         *  k pozici právě vykonávaného skriptu.
         */
    var $skin_dir;

    /** id defaultního skinu */
    var $skin_default_id;

    /** Název Databázové tabulky, ve které je uložená informace o skinech
         * uživatelů
         */
    var $skin_table;

        /** Indikátor, zda již layout začal vykreslovat stránku (pro redirect()
         *      metodu).
         */
        var $output_started = 0;





        /**
         * @brief Konstruktor
         *
         * Pouze nastaví defaultní hodnoty.
         */
        function Page() {
                $this->title = "No title given";
                $this->robots = "index,follow";
                $this->css = array();
                $this->calls = array();
                $this->scripts = array();
                $this->directInputScripts = array();
                $this->elements = array();
                $this->private_texts = array();
                $this->pub_javascripts = array();
                $this->menuitems[1] = array();
                $this->texts = array();
                $this->headline = "";
                $this->document_language ="cs";
                $this->document_charset = "utf-8";
                $this->skin_table = "skins";
                $this->skin_default_id = 1;
                $this->skin = null;
                $this->page_skin_subdir = null;
        }


    /**
     * @brief Přidá cestu k css souboru.
     *
     * Tato cesta pak bude použita v tagu <b>link relateted jako externi css soubor</b>
     *
     * V případě, že soubro neexistuje, vyhodí chybu.
     *
     * @throws
     *
     * @param $cssfile cesta k css souboru
     * @return true/false podle toho, zda bylo přidání souboru úspěšné.
     */
    function add_css($cssfile) {
        global $error, $LANGUAGE;
        if (is_file($cssfile) && is_readable($cssfile)) {
                $this->css[$cssfile] = $cssfile;
                return true;
        } else {
                global $error, $LANGUAGE;
                $error->add_error($error->ERROR_LEVEL_ERROR, __FILE__, __LINE__,
                        'PAGE_CSS_FILE_DOESNT_EXIST',
                        $LANGUAGE['PAGE_CSS_FILE_DOESNT_EXIST'] . $cssfile,
                        $LANGUAGE['PAGE_CSS_FILE_DOESNT_EXIST']
                        );
                return false;
        }
    }


    /**
     * @brief Přidá link na css soubor, pokud existuje.
     *
     * V případě, že soubro neexistuje, soubor nepřidá, ale chybu nevyhodí.
     *
     * @param $cssfile css soubor
     */
    function add_css_try($cssfile) {
        if (is_file($cssfile)) {
                $this->css[$cssfile] = $cssfile;
        }
    }


        /**
         * @brief vloží externě přilinkovaný skript.
         *
         * @param array scriptDescription pole s popisem vloženého skriptu
         * musi obsahovat index fileName, muže obsahovat index language, které
         * představuje jazyk skriptu. Pokud pole language není vyplněno,
         * doplní JavaScript.
         */
        function add_script($scriptDescription) {
                global $error, $LANGUAGE;

                if (!$scriptDescription || !is_array($scriptDescription) ||
                !array_key_exists("fileName", $scriptDescription) ) {
                        $error->add_error($error->ERROR_LEVEL_ERROR, __FILE__, __LINE__,
                                "PAGE_NOT_SCRIPT_DESCRIPTION",
                                $LANGUAGE['PAGE_SCRIPT_INCLUDE_PARAM'],
                                $LANGUAGE['PAGE_SCRIPT_INCLUDE_PARAM_USR']);
                        return false;
                }

                if (!is_file($scriptDescription["fileName"]) ||
                        !is_readable($scriptDescription["fileName"])) {
                        $error->add_error($error->ERROR_LEVEL_ERROR, __FILE__, __LINE__,
                                "PAGE_LINKED_FILE_NOT_EXISTS",
                                $LANGUAGE['PAGE_SCRIPT_INCLUDE_SRCFILE'],
                                $LANGUAGE['PAGE_SCRIPT_INCLUDE_SRCFILE_USR']);
                        return false;

                }

                if (!array_key_exists("language", $scriptDescription)) {
                        $scriptDescription["language"]= "Javascript";
                }

                if (!array_key_exists("type", $scriptDescription)) {
                        $scriptDescription["type"]= "text/javascript";
                }

                $this->scripts[] = $scriptDescription;
        }


        /**
         * @brief Přidá text do pole textů stránky.
         *
         * @param String $text Text, který má být přidán.
         */
        function add_text($text) {
                $this->texts[] = $text;
        }
        /**
         * @brief Přidá krátký text do pole textů stránky.
         *
         * @param String $text Text, který má být přidán.
         * @param String/Integer $id Identifikátor textu.
         */
        function add_private_text($text, $id = "") {
                if ($id != "") {
                        $this->private_texts[$id] = $text;
                }
                else{
                        $this->private_texts[] = $text;
                }

        }

        /**
         * @brief Přidá položku menu do některého z menu na stránce.
         *
         * @param String $menu Identifikátor menu, do kterého se má položka přidat
         * @param Array $menuitem Pole obsahující samotný obsah položky menu.
         * Má povině indexy <b>link</b> a <b>text</b>. Link je link, kam
         * vede položka menu, text je text uvnitř tagu <b>a</b>
         */
        function add_menu_item($menu, $menuitem) {
                global $error, $LANGUAGE;
                if (!is_array($menuitem)) {
                        $error->add_error($error->ERROR_LEVEL_ERROR, __FILE__, __LINE__,
                                "PAGE_MENU_ITEM_NOT_ARRAY",
                                $LANGUAGE['PAGE_MENU_ITEM_NOT_ARRAY'],
                                null);
                }
                $this->menuitems[$menu][] = $menuitem;
        }




        /**
         * @brief Přidá na stránku celé menu.
         *
         * @param $menuname jméno menu
         * @param $menu pole reprezentující menu - má povině indexy <b>link</b>
         *                      a <b>text</b>, Link je link, kam vede položka menu,
         *                      text je text uvnitř tagu <b>a</b>
         *
         * @note Pokud menu s daným jménem již existuje, bude přepsáno.
         */
        function add_menu($menuname, $menu) {
                global $error, $LANGUAGE;
                if (!is_array($menu)) {
                        $error->add_error($error->ERROR_LEVEL_ERROR, __FILE__, __LINE__,
                                "PAGE_MENU_ITEM_NOT_ARRAY",
                                $LANGUAGE['PAGE_MENU_ITEM_NOT_ARRAY'],
                                null);
                }
                $this->menuitems[$menuname] = $menu;
        }


        /**
         * @brief Nastaví typ dokumentu
         *
         * @param String $doctype Typ dokumentu
         * @see $this->document_type
         */

        function set_document_type($doctype) {
                $this->document_type = $doctype;
        }

        /**
         * @brief Nastaví znakovou sadu dokumentu
         *
         * Defaultní hodnota, která se užije, pokud tato funkce není
         * volána je <b>utf-8</b>
         *
         * @param String $charset
         * @see $this->document_charset
         */
        function set_document_charset ($charset) {
                $this->document_charset = $charset;
        }

        /**
         * @brief Nastaví obsah tagu <b>title</b>
         *
         * @param String $title
         * @see $this->title
         */
        function set_title ($title) {
                $this->title = $title;
        }

        /**
         * @brief Nastaví jazyk dokumentu
         *
         * Defaultní hodnota, která se užije, pokud tato funkce není
         * volána je <b>cs</b>
         *
         * @param String $language
         * @see document_language
         */
        function set_language ($language) {
                $this->document_language = $language;
        }
        /**
         * @brief Nastaví nadpis dokumentu
         *
         * @param String $headline Nadpis.
         * @see $this->document_headline
         */
        function set_headline ($headline) {
                $this->headline = $headline;
        }

    /**
     * @brief Nastavuje který layout bude použit k vyktereslení stránky.
     * Např.: jestli titulka, absolvent, tj určuje ve kterém podadresáři skinu
     * je Printer stránky
     * Dále tato funce zavolá o nastavení skinu podle uživatele
     * @param String $string_subdir_name jemno podadresáře, ve kterém jsou
     * příslušné elementy grafiky a soubor s definicí Printeru
     * POZOR: tato funce nemůže ošetřovat chyby, protože je volána ve chvíli,
     * kdy si nemůže zkonrolovat, zda adresář exituje, nebo ne, protože
     * k němu nezná cestu.
     */
    function set_skin_subdir($skin_subdir_name) {
        global $users_class;
        $this->page_skin_subdir = $skin_subdir_name;
        if ($users_class->is_logged() ) {
                $id_skinu = $users_class->user_attrib('skin_id');
        }
        else {
                $id_skinu = $this->skin_default_id;
        }
        $this->set_skin($id_skinu);
    }

        /**
     * Nastavuje skin, ktery je zadany jen nazvem adresáře v adresáři $DIR_SKINS
     * @param int $skin_id id v tabulce skinu.
     * @see $this->skin_table
     */
        function set_skin($skin_id) {
                global $DIR_SKINS;
                global $DIRECTORY;
                global $error;
                global $user;
                global $db;
                global $LANGUAGE;

                if (!$this->page_skin_subdir) {
                        $error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                                'PAGE_SKIN_NO_SUBDIR',
                                $LANGUAGE['PAGE_BAD_SET_SKIN'],
                                null
                        );
                }

                if (is_numeric($skin_id)) {
                        $query = "select dir from $this->skin_table where id = $skin_id";
                } else {
                        $query = "select dir from $this->skin_table where dir = 'default'";
                }

                $db->Query($query);
                if (!($skin = $db->GetResult())  ||
                        !is_dir($DIR_SKINS . "$skin/$this->page_skin_subdir") ||
                        !is_file($DIR_SKINS . "$skin/$this->page_skin_subdir/printer.php") ) {
                        $skin_dir = $DIR_SKINS . "default/$this->page_skin_subdir";
                        $skin = "default";
                        $error->add_error($error->ERROR_LEVEL_WARNING, __FILE__, __LINE__,
                                'PAGE_SKIN_NOT_FOUND',
                                $LANGUAGE['PAGE_SKIN_NOT_FOUND'] . $skin,
                                $LANGUAGE['ERROR_INVALID_SKIN']
                        );
                } else {
                        $skin_dir = $DIR_SKINS . "$skin/$this->page_skin_subdir";
                }

                if (!is_file("$skin_dir/printer.php")) {
                        $error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                                "PAGE_PRINTER_FILE_NOT_PRESENT",
                                $LANGUAGE['PAGE_PRINTER_FILE_NOT_RESENT'],
                                $LANGUAGE['PAGE_PRINTER_FILE_NOT_RESENT_USR']);
                }

                require_once "$skin_dir/printer.php";

                if (!class_exists("Printer")) {
                        $error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                                "PAGE_PRINTER_NOT_PRESENT",
                                $LANGUAGE['PAGE_PRINTER_NOT_RESENT'],
                                $LANGUAGE['PAGE_PRINTER_NOT_RESENT_USR']);
                }
                $this->skin_dir =  str_replace(dirname(__FILE__), "", $skin_dir);
                $this->skin =  $skin;
                $this->printer = new Printer();
        }

        /**
         * @brief Přidá volání funkce s její id.
         *
         * Funkce v té době musí být deklarována.
         *
         * @param String $text Volání funkce včetně parametrů, která se má provést
         * @param String/Integer $id Identifikátor funkce (použito v printeru.)
         * @see $this->call()
         */
        function add_call($function_definition, $id) {
                global $error, $LANGUAGE;
                if (!function_exists(trim(substr($function_definition, 0,
                        strpos($function_definition, "("))))) {
                        $error->add_error($error->ERROR_LEVEL_ERROR, __FILE__, __LINE__,
                                "PAGE_FCALL_DEFINITION_PROBLEM",
                                $LANGUAGE['PAGE_FCALL_DEFINITION_PROBLEM'],
                                $LANGUAGE['PAGE_FCALL_DEFINITION_PROBLEM_USR']);
                        return false;
                }
                $this->calls[$id]  = $function_definition;
        }
        /**
         * @brief Přidá vložení DirectInputScriptu do stránky.
         *
         * DirectInputScripty jsou ty, které jsou definovány uvnitř tagu
         * <b>script</b> a nejsou pripojeny v externím souboru. V některých
         * situacích je obtížné se jim vyhnout.
         *
         * @param String $text Cesta k souboru se skriptem
         * @param String/Integer $id Identifikátor skriptu (použito v printeru.)
         * @see $this->call()
         */
        function add_dis($directInputScriptFile, $id) {
                global $error, $LANGUAGE;
                if (!is_file($directInputScriptFile)) {
                        $error->add_error( $error->ERROR_LEVEL_ERROR, __FILE__, __LINE__,
                        "PAGE_DIRECT_INPUT_FILE_NOT_FOUND",
                        $LANGUAGE['PAGE_DIS_INCLUDE_SRCFILE'],
                        $LANGUAGE['PAGE_DIS_INCLUDE_SRCFILE_USR']);
                } else {
                        $this->directInputScripts[$id]  = $directInputScriptFile;
                }
        }

        /**
         * @brief Provede volání funkce.
         *
         * Funkce používá funci eval eval(), středník na konec si doplní.
         *
         * @param String/Integer $function_id Identifikátor funkce,
         * která má být zavolána.
         * @see $this->add_call()
         */
        function call($function_id) {
                global $error, $LANGUAGE;
                if (!array_key_exists($function_id, $this->calls)) {
                        /* uroven chyby musi byt FATAL, protoze errors uz muzou byt v dobe volani
                        vypsane
                        */
                        $error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                                "PAGE_LAYOUT_FUNCTION_DEFINITION_NOT_FOUND",
                                $LANGUAGE['PAGE_BAD_FCALL'] . $function_id,
                                $LANGUAGE['PAGE_BAD_FCALL_USR']);
                        return;
                }

                eval($this->calls[$function_id] . ";");
        }

    /**
         * @brief Přidá element do pole elementů stránky pod uvedeným id.
         *
         * @param Class $element Element, který má být připojen
         * @param String/Integer $id Identifikátor elementu (použito v printeru.)
         */
    function add_element($element, $id) {
        global $LANGUAGE, $error;
        if (!class_exists(get_class($element))) {
                $error->add_error($error->ERROR_LEVEL_FATAL , __FILE__, __LINE__,
                                "PAGE_ELEMENT_CLASS_NOT_EXISTS",
                                $LANGUAGE['PAGE_ELEMENT_CLASS_NOT_EXISTS'],
                                $LANGUAGE['PAGE_ELEMENT_CLASS_NOT_EXISTS_USR']);
        }

        $this->elements[$id] = $element;

    }


    /**
         * @brief Vykreslí stránku do output streamu.
         *
         */
    function print_page() {
        $this->output_started = true;
        $this->printer->print_page($this);
    }


        /**
         * Přesměruje klienta na jinou adresu.
         *
         * Metoda je součástí layoutu, aby mohla poznat,
         * zda už začal výstup na obrazovku.
         *
         * @param redirURL adresa, na kterou přesměrovat
         *
         * @return funkce se nikdy nevrátí, po přesměrování je skript ukončen
         * @throws fatal error, pokud již začal výstup na obrazovku pomocí funkcí
         *    layoutu (v popisu chyby pro uživatele je odkaz na URL, kam měl být
         *    přesměrován)
         */
        function redir($redirURL) {
                if ($this->output_started) {
                        global $error, $LANGUAGE;
                        $error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__,
                                'PAGE_BAD_REDIRECT',
                                $LANGUAGE['PAGE_BAD_REDIRECT']
                                . $_SERVER['REQUEST_URI'] . ", redirect: " . $redirURL,
                                $LANGUAGE['PAGE_BADREDIR_PRE']
                                . '<a href="' . $redirURL . '">' . $redirURL . '</a>'
                                . $LANGUAGE['PAGE_BADREDIR_POST']);
                }

                header("Location: $redirURL");
                finalize(0);
        }

}

/**
 * @}
 * @}
 */

?>