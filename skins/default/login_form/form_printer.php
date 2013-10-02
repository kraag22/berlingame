<?php
class Form_printer {

/**
   * @brief Funkce pro vypsání hlavičky formuláře
   * @param $name Jméno formuláře
   * @param $error Řetězec chybových hlášek
   * @param $title Popisek formuláře
   * @param $action Hodnota vlastnosti action tagu form
   * @param $method Hodnota vlastnosti method tagu form
   * @param $id Id hodnot ve formuláři
   * @param $file Určuje jestli se má vypsat vlastnost enctype tagu form
   * @return Zdrojový kód hlavičky formuláře
   *
   *
   */
function l_start_form($name, $error, $title, $action, $method,
                        $id, $file, $no_reload, $succ_text) {

    $ret = "$title\n";
    $ret .= "<form class='flat' action='$action' method='$method'";
    if ($file)
      $ret .= " enctype='multipart/form-data'";

    $ret .= ">\n";
    if ($error){
        $error = str_replace('<br />', '', $error);
        $ret .= "<span id=\"error\">".$error."</span><br />\n";
    }
        if ($succ_text) {
                $ret .= "<span id=\"success\">" . $succ_text . "</span>\n";
        }
        if ($no_reload) {
                $ret .= "<input type='hidden' name='fm_" . $name . "_no_reload' value='$no_reload' />\n";
        }

    $ret .=
    "<input type='hidden' name='fm_$name' value='1' />".
    "<input type='hidden' name='fm_".$name."_id' value='$id' />\n\n";
    return $ret;
  }

  /**
   * @brief Defaultní funkce pro vypsání patičky formuláře
   * @return Zdrojový kód patičky formuláře
   *
   * ...
   *
   */
  function l_end_form() {
    $ret = "\n</form>\n";
    return $ret;
  }

  /**
   * @brief Defaultní funkce pro vypsání elementu formuláře
   * @return Zdrojový kód elementu formuláře
   *
   * ...
   *
   */
  function l_insert_element($elem, $value = '') {
    $ar_inputs = array("text", "file", "submit", "password");
    if ($value == '' || $elem['type'] == "password")
      $value = $elem['default'];
    $ret="";
    $add_text = "";
    if ($elem['html_class']){
                $add_text .= 'class="'.$elem['html_class'].'" ';
    }
    if ($elem['html_id']){
                $add_text .= 'id="'.$elem['html_id'].'" ';
    }

    if (in_array($elem['type'],$ar_inputs)) {

      if ($elem['type']=="text"||$elem['type']=="password"){
        $ret .= "<span class=\"loginbar\">".$elem['title']."</span>";
      }
      else{
        $ret .= $elem['title'];
      }
      $ret .= "    <input $add_text name='".$elem['element']."' type='".$elem['type']."'
            value='$value' ";
      if ($elem['size'])
        $ret .= "maxlength='".$elem['size']."'";
      $ret .= " />\n";
    }

    elseif ($elem['type'] == "textarea") {
      $ret .= "".$elem['title']."
            <textarea  $add_text name='".$elem['element']."' rows='5' cols='50'>".
            $value."</textarea>\n";
    }

    elseif ($elem['type'] == "select") {
      $ret .= "".$elem['title']."
           <select  $add_text name='".$elem['element']."'>";

      if (strpos($elem['stuff'], "fce:") === 0) {
        $fce = substr($elem['stuff'], 4);
        $options = $fce();
      }
      elseif ($elem['stuff']) {
        $stuff = explode("\n", $elem['stuff']);
        foreach ($stuff as $st) {
          $options[] = explode(";", $st);
        }
      }

      if (is_array($options)){
        foreach ($options as $opt) {
          if ($value && ($value == $opt[0])) {
            $sel = "selected='selected'";
          }
          elseif (!$value && trim($opt[2])) {
            $sel = "selected='selected'";
          }
          else {
            $sel = "";
          }
          $ret .= "<option value='".$opt[0]."' $sel>".$opt[1]."</option>\n";
        }
      }

      $ret .= "</select>\n";
    }
    return $ret;
  }

}



?>
