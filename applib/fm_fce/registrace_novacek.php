<?php
function novy_hrac($myPost){
  global $db,$users_class, $DIR_CONFIG;
  $nick = sqlsafe($myPost['rn_nick']);
  $heslo = sqlsafe($myPost['rn_pswd']);

  $group_id = "hrac";

  $attribs = array (
    "account_created" => date("Y-m-d H:i:s"),
    "last_activity" => date("Y-m-d H:i:s", 0),
    "group_id" => $group_id,
    "account_active" => 1,
    "ip_adresa_registrace" => sqlsafe($_SERVER['REMOTE_ADDR']),
  );

  $id = $users_class->new_user($nick,$heslo,$attribs);

  if (isset($id)){
    $_SESSION['rn_nick'] = $nick;
    $_SESSION['rn_heslo'] = $heslo;
  }
  //vytvoreni zaznamu v users_hrac
  $query = "INSERT INTO `users_hrac` (id_user) VALUES ('$id');";
  $db->DbQuery( $query );

  //vytvoreni zaznamu v users_stat
  $query = "INSERT INTO `users_stat` (id_user) VALUES ('$id');";
  $db->DbQuery( $query );

  //test+pripsani bonusu za privedeni hrace pres referenci
  $users_class->reference_test( $id );
}


function heslo(){
  if ($_POST['rn_pswd'] != $_POST['rn_pswd_again'])
  {
    return "Zadaná hesla jsou různá. Zadejte je prosím znovu";
  }

  if(strlen($_POST['rn_pswd'])==0){
    return "Zadejte heslo";
  }

  return false; // uspech
}

function unikatni_login(){
  global $db;

  $result = checkRecaptcha();

  if ($result) {
    return $result;
  }

  //zabraneni vstupu html kodu
  if((strpos($_POST['rn_nick'],'<')!= 0)||
    (strpos($_POST['rn_nick'],'>')!= 0)||
    (strpos($_POST['rn_nick'],'\'')!= 0)||
    (strpos($_POST['rn_nick'],'\"')!= 0)){
      return "Login nesmí obsahovat znaky <,>,',\"";
    }

  //omezeni delky
  if(strlen($_POST['rn_nick'])>15){
    return "Zadejte heslo";
  }

  //kontrola unikatnosti
  $db->Query("SELECT `id` FROM users_sys
              WHERE `login`='".sqlsafe($_POST['rn_nick'])."'");
  if ($db->GetResult()) {
    return "Uživatel s tímto loginem už existuje. Zvolte si jiný.";
  }
  return false; // uspech
}

function checkRecaptcha() {
  global $RECAPTCHA_SECRET;

  $url = 'https://www.google.com/recaptcha/api/siteverify';
  $data = array(
    'secret' => $RECAPTCHA_SECRET,
    'response' => $_REQUEST['g-recaptcha-response'],
    'remoteip' => $_SERVER['REMOTE_ADDR']
    );

  $options = array(
      'http' => array(
          'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
          'method'  => 'POST',
          'content' => http_build_query($data)
      )
  );
  $context  = stream_context_create($options);
  $result = file_get_contents($url, false, $context);

  if ($result === false) {
    return 'Chyba pripojeni. Zkuste se registrovat pozdeji.';
  }
  else {
    $result = json_decode($result);
    if (!$result->success) {
      return 'Musíte kliknout na reCaptcha pod tlačítkem "registrovat".';
    }
    else if ($result->success === false) {
      return $result->error-codes;
    }
    else if ($result->success === true) {
      return false;
    }
    else {
      return 'unknown error';
    }
  }
}

?>
