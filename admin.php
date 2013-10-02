<?php
$DIRECTORY = "./";
$SKIN_SUBDIR = "admin";

define("BoB", 1);

// vlozi soubor s konfiguracnim nastavenim unikatnim pro tento server
require_once("config/config.php");

// include basic functions and classes
require_once($DIR_CORE . "main.php");

if (!$auth->authorised_to('module_admin')) {
	global $error;
	$error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__, 'NO_RIGHTS',
			$users_class->user_login() . ":" . "Nemate prave pro admin modul",
			"K temto strankam nemate pristup");
	//$error should cause the script to die here, but to be sure:
	exit(1);
}


/* important, when no skin is given, then page can not be drawn */
$page->set_skin_subdir("$SKIN_SUBDIR");

$page->set_headline("Administrační sekce");

$page->set_title("Administrační sekce");

$section = array_get_index_safe('section', $_GET);
$sectionpage = array_get_index_safe('page', $_GET);

if (!isset($_GET['section'])) {
	$_GET['section'] = '';
}

if (!isset($_GET['page'])) {
	$_GET['page'] = '';
}

if (!isset($_GET['action'])) {
	$_GET['action'] = '';
}


// SUBMENU - novinky
$admin_novinky_menu = array (
	'admin_novinky_add' => array (
		'link' => 'admin.php?section=novinky&amp;page=add_nov',
		'text' => "Přidat novinku"
	),
		"admin_novinky_list" =>  array (
		'link' => 'admin.php?section=novinky&amp;page=list_nov',
		'text' => "Seznam novinek"
	)
);
// SUBMENU - ligy
$admin_ligy_menu = array (
	'admin_ligy_add' => array (
		'link' => 'admin.php?section=ligy&amp;page=add',
		'text' => "Vytvořit scénář"
	),
		"admin_ligy_list" =>  array (
		'link' => 'admin.php?section=ligy&amp;page=list',
		'text' => "Seznam scénářů"
	)
);


/* HLAVNI MENU */

$admin_menu = array (
	'novinky' => array (
		'link' => 'admin.php?section=novinky',
		'submenu' => $admin_novinky_menu,
		'text' => "Administrace novinek",
		'auth_token' => 'admin_novinky'
	),
	'uzivatele' => array (
		'link' => 'admin.php?section=uzivatele',
		'submenu' => NULL, 
		'text' => "Administrace uživatelů",
		'auth_token' => 'admin_uzivatele'
	),
	'ligy' => array (
		'link' => 'admin.php?section=ligy',
		'submenu' => $admin_ligy_menu, 
		'text' => "Administrace scénářů",
		'auth_token' => 'admin_ligy'
	)
);

/* autorizace */
if ($section) {
	if (!$auth->authorised_to($admin_menu[$section]['auth_token'], 'main')) {
		global $error,$users_class,$LANGUAGE;

		$error->add_error($error->ERROR_LEVEL_FATAL, __FILE__, __LINE__, 
				'NO_RIGHTS_FOR_ADMIN',
				"Uživatel \"" . $users_class->user_login() . "\" se dostal do
				 modulu pro administrátory, ke kterému nemá práva ($section)",
				"K temto strankam nemate pristup");
	} else {
		/* zaktivneni polozky menu */
		if (isset($admin_menu[$section])) {
			$admin_menu[$section]['active'] = 1;
		}

		if ($sectionpage 
			&& isset($admin_menu[$section]['submenu'][$sectionpage])) {
			/* zaktivneni polozky submenu */
			$admin_menu[$section]['submenu'][$sectionpage]['active'] = 1;
		}
	}

	/*
	 * podmenu autorizujeme pomoci tokenu $item['auth_token']
	 * a indexu odpovidajicimu klici podmenu
	 */
	if (is_array($admin_menu[$section]['submenu'])) {
		foreach ($admin_menu[$section]['submenu'] as $i => $v) {
			if (!($auth->authorised_to($i))) {
				unset($admin_menu[$section]['submenu'][$i]);
			}
		}
	}
}

/* hlavni menu autorizujeme pomoci tokenu $item['auth_token'] a indexu 'main' */
foreach ($admin_menu as $item) {
	if($auth->authorised_to($item['auth_token'], 'main')) {
		$page->add_menu_item('admin_menu', $item);
	}
}

switch(array_get_index_safe('section', $_GET)) {
	case 'novinky' :
  		require($DIR_LIB . "administrace/admin_novinky.php");
  	break;
	case 'ligy' :
		require($DIR_LIB . "administrace/admin_ligy.php");
		break;
	case 'uzivatele' :
		require($DIR_LIB . "administrace/admin_uzivatele.php");
		break;
	default:
		
		break;
	}

$page->set_title("admin");
$page->print_page();

/* ukončeni skriptu */
finalize();

?>