<?php if (!defined('BoB')) exit();

/**
 * @file tools.php
 * @brief Různé pomocné funkce.
 *
 * 
 */

function request($name, $default = "") {
	
	if (array_key_exists($name, $_REQUEST)) {
		return $_REQUEST[$name];
	} else {
		return $default;
	}
	
}

?>