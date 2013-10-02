<?php if (!defined('BoB')) exit();
/**
 * @file
 * @brief Nastaveni frameworku
 *
 * Soubor obsahuje konstanty - parametry frameworku.
 *
 *
 * @addtogroup g_framework
 * @{
 * @addtogroup g_config Konfigurace
 * @{
 */

// Application settings
$DEBUG = true;
$ENABLE_PROFILER = true;
$ANALYZE = false;
$ERRORS_LOG;
$ERRORS_PRINT;

if ($DEBUG) {
        $ERRORS_PRINT = 1|2|4|8;
        $ERRORS_LOG = 1|2|4|8; // pro odladeni logovani samotneho
} else {
        $ERRORS_PRINT = 4|8;
        $ERRORS_LOG = 2|4|8;
}
// DB settings

/** adresa DB serveru, například "localhost" */
$DB_SERVER = 'localhost';
/** port DB serveru, obvykle 3306  */
$DB_PORT = 3306;
/** jmeno databaze */
$DB_DATABASE = 'berlin';
/** uživatel DB */
$DB_USER = 'root';
/** heslo do DB */
$DB_PASS = '';

//------------------- Directories --------------------------

/* absolutní cesta ke kořenovému adresáři aplikace, včetně posledního lomítka */
$DIRECTORY = './';

/** BaseURL aplikace */
$APP_ROOT = $DIRECTORY;

/** Relativní cesta k ikonkám */
$ICON_PATH = "upload/photo";

/** absolutní cesta k adresáři frameworku, včetně posledního lomítka */
$DIR_CONFIG = $DIRECTORY . "config/";
$DIR_CORE = $DIRECTORY . "core/";
$DIR_LIB = $DIRECTORY . "applib/";
$DIR_SKINS = $DIRECTORY . "skins/";
$DIR_LOG = $DIRECTORY . "log/";
$DIR_LANG = $DIRECTORY . "language/";
$DIR_FILES = $DIRECTORY . "soubory/";
$DIR_SCRIPTS = $DIRECTORY . "scripts/";
$DIR_INC = $DIRECTORY . "include/";
$DIR_LIBRARY = $DIRECTORY . "lib/";

$MAX_TEXT_LENGTH = 500;
$MAX_FILE_SIZE = 2000000;
$not_allowed = array('htm','html','exe','php','asp','js','pl');

//-------------------PREPOCET--------------------

$lockfile = "${DIR_LOG}prepocet.lock";
$secondlock = "${DIR_LOG}prepocet.second.lock";
$PREPOCET_HESLO = 'heslo';
if (file_exists($lockfile)) {
    echo '<html><head>
    <meta http-equiv="Content-language" content="cs" />
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <title>Probíhá přepočet.</title>
    </head><body style="background-color: black;">
    <div style="width:1024; margin-left: auto; margin-right: auto;">
    <img src="'.$DIRECTORY.'images/prepocet.jpg" />
    </div>
    </body></html>';
    die;
}

?>