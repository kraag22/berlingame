<?php
/**
 * @file login_form.php
 * $brief Funkce pro zpracovani formulare login_form
 *
 */

function try_log_user ($myPost) {
  global $users_class, $_ERR;

  if (!$users_class->log_user($myPost['lf_login'], $myPost['lf_pwd']))
    return "Nepodařilo se přihlásit";
}

function login_form_trim (&$value) {
  $value = trim($value);
}

?>