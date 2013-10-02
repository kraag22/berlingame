<?php
/**
 * @file
 *
 * @brief Knihovna pro práci se soubory. Obsahuje funkce pro práci s
 * filesystémem jako je vytváření nebo mazání adresářů, nebo přijetí
 * uploadovaného souboru
 *
 * @author Tomáš Pop, Jan Cipra, Jiří Toušek
 *
 *
 * @addtogroup g_framework
 * @{
 *
 * @addtogroup g_files Práce se soubory
 * @{
 *
 * @brief Funkce pro práci se soubory, filesystémem, obrázky a zip-archivy
 *
 */


/**
 * @brief Nahrazuje v národních řetězcích znaky tak, aby výsledný string
 * obsahoval jen písmena anglické abecedy.
 *
 * Co a jak bude nahrazeno udává pole $LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]
 * @see $LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]
 * @param $str String k přeložení
 * @return String, řetězec s nahrazenými znaky
 */
function common_translate_national_chars($str){
        global $LANGUAGE;
        return strtr($str, $LANGUAGE["LANGUAGE_NATIONALS_NATIONAL"]);
}




/**
 * @brief Odstraní rekursivně adresář i s obsahem
 *
 * @throws REMOVE_DIR_NOT_DIR
 * @param $dirname Název adresáře ke smazání
 * @return Bool, true pokud je adresář odstraněn, false jinak
 */
function fs_rm_directory($dirname) {
        global $error, $LANGUAGE;
        if (!is_dir($dirname)) {
                $error->add_error($error->ERROR_LEVEL_ERROR, __FILE__, __LINE__,
                        "REMOVE_DIR_NOT_DIR",
                        $LANGUAGE['FILES_REMOVE_DIR_NOT_DIR'],
                        $LANGUAGE['FILES_REMOVE_DIR_NOT_DIR_USR']);
          return false;
        }

        $handle = opendir($dirname);
        while (($file = readdir($handle))!== false)
        {
                if (($file != "..") && ($file != ".") && (is_file("$dirname/$file"))) {
                        if (!unlink("$dirname/$file")) {
                                $error->add_error($error->ERROR_LEVEL_ERROR, __FILE__, __LINE__,
                                        "REMOVE_DIR_NOT_DIR",
                                        $LANGUAGE['FILES_CANNOT_REMOVE_FILE'] . $dirname . '/' . $file,
                                        $LANGUAGE['FILES_CANNOT_REMOVE_FILE_USR']);
                                return false;
                        }
                }

                elseif (($file != "..") && ($file != ".") && (is_dir("$dirname/$file"))) {
                        if (!fs_rm_directory("$dirname/$file")) {
                                $error->add_error($error->ERROR_LEVEL_ERROR, __FILE__, __LINE__,
                                        "REMOVE_DIR_NOT_DIR",
                                        $LANGUAGE['FILES_CANNOT_REMOVE_DIR']  . $dirname . '/' . $file,
                                        $LANGUAGE['FILES_CANNOT_REMOVE_DIR_USR']);
                                return false;
                        }
                }
        }


        closedir($handle);
        if (!rmdir($dirname) ) {
                $error->add_error($error->ERROR_LEVEL_ERROR, __FILE__, __LINE__,
                                "REMOVE_DIR_NOT_DIR",
                                $LANGUAGE['FILES_CANNOT_REMOVE_DIR']  . $dirname,
                                $LANGUAGE['FILES_CANNOT_REMOVE_DIR_USR']);
                return false;
        }

        // pokud bylo vse OK
        return true;
}

/**
 * @brief Vytvoří adresář a nastaví jeho práva
 *
 * @param $dirname Název adresáře k vytvoření
 * @param $attributes Atributy vytvořeného adresáře, oktalově, např 0777
 * @return Bool, true pokud je adresář vytvořen a práva nastavena, false jinak
 */
function fs_mk_dir($dirname, $attributes = 0777) {
        if (mkdir($dirname, $attributes) && chmod($dirname, $attributes)) {
                return true;
        } else {
                return false;
        }
}

/**
 * @brief Přijme uploadovaný soubor.
 *
 * @param $post_file_name Jméno formulářové položky typu file,
 *                      která se zpracovává
 * @param $new_name Nové jméno souboru po přijetí
 * @param $maxsize Maximání velikost, pokud 0 nekontroluje se
 * @param $types_allowed Pole povolených mime-types
 *                      Pozor, některé vyplňuje jinak Mozzila a Explorer
 *                      Pokud je null, nekontroluje se
 * @param $atributes Atributy přijatého souboru, oktalově např 0777
 */
function fs_receive_file($post_file_name, $new_name, $maxsize = 0,
                                                                        $types_allowed = null, $atributes = null) {
        global $LANGUAGE, $error;
        $OK = false;

        $type = $_FILES[$post_file_name]["type"];
        $size = $_FILES[$post_file_name]["size"];
        if ($atributes === null) {
                $atributes = 0777;
        }

        if ($_FILES[$post_file_name] && $_FILES[$post_file_name]["size"] != 0 ) {
                $TYP_OK = false;
                if ($types_allowed) {
                        $TYP_OK = in_array($_FILES[$post_file_name]["type"], $types_allowed);
                        /*foreach ($types_allowed as $i => $value) {
                                if ($_FILES[$post_file_name]["type"] == $value){
                                        $TYP_OK = true;
                                        break;
                                }
                        }*/
                } else {
                        $TYP_OK = true;
                }

                $SIZE_OK = false;
                if ($maxsize) {
                        if ($_FILES[$post_file_name]["size"] < $maxsize ) {
                                $SIZE_OK = true;
                        }
                } else {
                        $SIZE_OK = true;
                }

                if ($TYP_OK && $SIZE_OK) {
                        $OK = true;

                        if (!move_uploaded_file($_FILES[$post_file_name]["tmp_name"],
                                         $new_name)) {
                                $error->add_error($error->ERROR_LEVEL_ERROR, __FILE__, __LINE__,
                                        "FILES_UPLOAD_FILE",
                                        $LANGUAGE['FILES_MOVE_UPLOADED_ERROR'] .
                                        $_FILES[$post_file_name]["tmp_name"],
                                        $LANGUAGE['FILES_MOVE_UPLOADED_ERROR_USR']);
                                $OK = false;
                        } else {
                                if (!chmod($new_name, $atributes)) {
                                        $error->add_error($error->ERROR_LEVEL_WARNING, __FILE__, __LINE__,
                                                "FILES_CHMOD",
                                                $LANGUAGE['FILES_CANNOT_CHMOD']  . $new_name,
                                                $LANGUAGE['FILES_CANNOT_CHMOD']);
                                        $OK = false;
                                }
                        }
                }
                if (!$SIZE_OK) {
                        $error->add_error($error->ERROR_LEVEL_ERROR, __FILE__, __LINE__,
                                "FILES_SIZE",
                                $LANGUAGE['FILES_FILE_TO_LARGE']  . $new_name,
                                $LANGUAGE['FILES_FILE_TO_LARGE_USR']);
                        return false;
                }

                if (!$TYP_OK){
                        $files_allowed = "";
                        foreach ($types_allowed as $i => $value) {
                                $files_allowed .= $value . ", ";
                        }
                        $error->add_error($error->ERROR_LEVEL_ERROR, __FILE__, __LINE__,
                                "FILES_TYPE",
                                $LANGUAGE['FILES_BAD_FILE_TYPE']  . $files_allowed,
                                $LANGUAGE['FILES_BAD_FILE_TYPE_USR'] . $files_allowed);
                        return false;
                }
        } else {
         $error->add_error($error->ERROR_LEVEL_ERROR, __FILE__, __LINE__,
                        "FILES_TYPE",
                        $LANGUAGE['FILES_BAD_FILE']  . $new_name,
                        $LANGUAGE['FILES_BAD_FILE_USR']);
                return false;
        }
        if ($OK) {
                return true;
        }
        //sem by se nikdy nemělo dojít
        return false;
}




/**
 * @brief Přejmenuje soubor.
 *
 * @param $oldname Staré jméno
 * @param $newname Nové jméno
 */
function fs_rename($oldname, $newname){
        return rename($oldname, $newname);
}




/**
 * @brief Okopíruje soubor a nastaví mu práva.
 *
 * @param $from Pozice souboru (Source)
 * @param $to Pozice souboru (Destination)
 * @param $attributes Atributy okopírovaného souboru, oktalově, např 0777
 */
function fs_my_copy($from, $to, $attributes = 0777){
        return (copy($from, $to) && chmod($to, $attributes));
}


/**
 * @brief Kopíruje, soubor nebo rekursivně adresář.
 *
 * @param $source Co kopírovat
 * @param $dest Na co / Kam kopírovat
 * @param $attributes Atributy okopírované entity, oktalově, např 0777.
 */
function fs_copyr($source, $dest, $attributes = 0777) {
        if (is_file($source)) {
                $c = copy($source, $dest);
                chmod($dest, $attribute);
                return $c;
        }

        if (!is_dir($dest)) {
                $oldumask = umask(0);
                if (!mkdir($dest, $attribute)) {
                        return false;
                }
                umask($oldumask);
        }

        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
                if ($entry == '.' || $entry == '..') {
                        continue;
                }
                if ($dest !== "$source/$entry") {
                        fs_copyr("$source/$entry", "$dest/$entry");
                }
        }
        $dir->close();
        return true;
}



/** Převzorkování obrázku GIF, PNG nebo JPG
 *
 * @brief Zmenší obrázek podle zadaných paramtrů
 *
 * @param $file_in Název zmenšovaného souboru
 * @param $file_out Název výsledného souboru
 * @param $max_x Maximální šířka výsledného obrázku, 0 pokud
 * na ní nezáleží
 * @param $max_y Maximální výška výsledného obrázku, 0 pokud
 * na ní nezáleží
 * @return Bool true, false v případě chyby
 */
function image_shrink($file_in, $file_out, $max_x, $max_y = 0) {
        global $error, $LANGUAGE;

        $imagesize = getimagesize($file_in);
        if ((!$max_x && !$max_y) || !$imagesize[0] || !$imagesize[1]) {
                return false;
        }
        switch ($imagesize[2]) {
                case 1:
                        $img = imagecreatefromgif($file_in);
                        break;

                case 2:
                        $img = imagecreatefromjpeg($file_in);
                        break;

                case 3:
                        $img = imagecreatefrompng($file_in);
                        break;

                default:
                        $error->add_error($error->ERROR_LEVEL_ERROR, __FILE__, __LINE__,
                                "FILES_IMAGE_TYPE_NOT_SUPP",
                                $LANGUAGE['FILES_IMAGE_TYPE_NOT_SUPP'],
                                $LANGUAGE['FILES_IMAGE_TYPE_NOT_SUPP_USR']);
                        return false;
        }

        if (!$img) {
                return false;
        }

        if ($max_x) {
                $width = $max_x;
                $height = round($imagesize[1] * $width / $imagesize[0]);
        }

        if ($max_y && (!$max_x || $height > $max_y)) {
                $height = $max_y;
                $width = round($imagesize[0] * $height / $imagesize[1]);
        }

        $img2 = imagecreatetruecolor($width, $height);
        imagecopyresampled($img2, $img, 0, 0, 0, 0, $width, $height,
                $imagesize[0], $imagesize[1]);

        if ($imagesize[2] == 2) {
                return imagejpeg($img2, $file_out);

        } elseif (($imagesize[2] == 1) && function_exists("imagegif")) {
                imagetruecolortopalette($img2, false, 256);
                return imagegif($img2, $file_out);

        } else {
                return imagepng($img2, $file_out);
        }
}

/**
 * @brief Funkce na rozbalení celého zipu do určeného adresáře
 *
 * @param $src_file Cesta k zip souboru.
 * @param $dest_dir Cesta k adrasáři, kam má být zip soubor rozbalen
 * @param $create_zip_name_dir Určuje zda má být vytvořen adresář se jménem
 * zipu
 * @param $overwrite true - rozbalování může přepisovat soubory
 *
 * @return true operace proběhla bez chyby
 * @return false operace se nezdařila
 */
function unzip($src_file, $dest_dir = false,
                                                        $create_zip_name_dir = true, $overwrite = true) {

        if (is_resource($zip = zip_open(getcwd() . "/" . $src_file))) {
                $splitter = ($create_zip_name_dir === true) ? "." : "/";
                if ($dest_dir === false) {
                        $dest_dir = substr($src_file, 0, strrpos($src_file, $splitter)) . "/";
                }

                // Create the directories to the destination dir if they don't already
                // exist
                create_dirs($dest_dir);
                // For every file in the zip-packet
                while ($zip_entry = zip_read($zip)) {

                        // Now we're going to create the directories in the destination
                        // directories
                        // If the file is not in the root dir
                        $pos_last_slash = strrpos(zip_entry_name($zip_entry), "/");
                        if ($pos_last_slash !== false) {
                                // Create the directory where the zip-entry
                                //should be saved (with a "/" at the end)
                                create_dirs($dest_dir . substr(zip_entry_name($zip_entry), 0,
                                $pos_last_slash + 1));
                        }

                        // Open the entry
                        if (zip_entry_open($zip,$zip_entry, "r")) {

                                // The name of the file to save on the disk
                                $file_name = $dest_dir . zip_entry_name($zip_entry);

                                // Check if the files should be overwritten or not
                                if (($overwrite === true) ||
                                        (($overwrite === false) && !is_file($file_name))) {

                                        // Get the content of the zip entry
                                        $fstream = zip_entry_read($zip_entry,
                                                zip_entry_filesize($zip_entry));

                                        //file_put_contents($file_name, $fstream );
                                        if (!is_dir($file_name) && $file_name) {
                                                $f = fopen($file_name, "w");
                                                fwrite($f, $fstream);
                                                fclose($f);
                                        }
                                        // Set the rights
                                        if(file_exists($file_name)) {
                                                chmod($file_name, 0777);
                                        } else {
                                                //TODO else what?
                                        }
                                }

                                // Close the entry
                                zip_entry_close($zip_entry);
                        }
                }
                // Close the zip-file
                zip_close($zip);

                return true;
        } else {
                return false;
        }
}

/**
 * @brief Rekurzivně vytvoří adresáře, interní funkce pro unzip()
 *
 * @param $path Cesta, která se má vytvořit
 *
 */
function create_dirs($path) {
        if (!is_dir($path)) {
                $directory_path = "";
                $directories = explode("/", $path);
                array_pop($directories);

                foreach($directories as $directory) {
                        $directory_path .= $directory . "/";
                        if (!is_dir($directory_path)) {
                                mkdir($directory_path);
                                chmod($directory_path, 0777);
                        }
                }
        }
}

/**
 * @brief Projde adresář a hledá první soubor se zadanou příponou
 *
 * @param $dir Adresář, který se má prohledat
 * @param $pripona Přípona, která se má najít
 *
 * @return false soubor nebyl nalezen
 * @return true soubor byl nalezen
 *
 * @return String/Bool Cesta k nalezenu souboru, nebo false, pokud nebyl nalezen
 */
function najdi_priponu($dir, $pripona) {
        $swf_file = false;
        $tmp = dir($dir);
        while ($soubor = $tmp->read()) {
                $koncovka = substr($soubor, -4, 4);

                if (($koncovka == $pripona) && (is_file($dir . "/" . $soubor))) {
                        return $dir . "/" . $soubor;
                }

                if (is_dir($dir . "/" . $soubor) &&
                        $soubor != '.' and $soubor != '..') {

                        if ($swf_file = najdi_priponu($dir . "/" . $soubor, $pripona)) {
                                return $swf_file;
                        }
                }
        }
        $tmp->close();

        return false;
}


/**
 * @brief Vrátí setříděný seznam souborů v adresáři.
 *
 * Vrací pouze soubory, ne adresáře.
 *
 * @param $dir adresář k vypsání
 * @return pole jmen souborů (bez adresáře)
 * @return prázdné pole, pokud adresář žádné soubory neobsahuje
 */
function list_dir($dir) {

        $files = array();

        $dh = opendir($dir);
        while (false !== ($filename = readdir($dh))) {
                if (is_file($dir . $filename)) {
                        $files[] = $filename;
                }
        }
        closedir($dh);

        if (count($files) > 0) {
                sort($files);
        }

        return $files;
}

/**
 * @}
 * @}
 */
?>