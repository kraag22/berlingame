<?php if (!defined('BoB')) exit();

/**
 * @file napsat.php
 * @brief Pošta uživatele - napsat zprávu
 *
 * 
 */

function posta_napsat() {

	global $POSTA_ODESLANI;

	$text = "";
	
	$text .= "<div class=\"posta_napsat\">\n";
	
	$text .= <<<EOT
<form method="post" name="prvni">
 <input type="hidden" name="section" value="$POSTA_ODESLANI" />

 <textarea id="obsah" name="obsah" cols="60" rows="40"></textarea>
EOT;

//PANEL
$text .= "<div class=\"posta_napsat_panel\">\n";	
	
	if(isset($_REQUEST['hrac'])){
		$text .= '<input type="text" name="komu" id="komu" value="'.$_REQUEST['hrac'].'" />';
	}
	else{
		$text .= '<input type="text" name="komu" id="komu" />';
	}
	
	if(isset($_REQUEST['re'])){
		$text .= '<textarea id="predmet" name="predmet" cols="20" rows="3">RE: '.trim(urldecode($_REQUEST['re'])).'</textarea>';
	}
	else{
		$text .= '<textarea id="predmet" name="predmet" cols="20" rows="3"></textarea>';
	}
	
	$text .= "</div>\n";

$text .= '<input type="submit" id="odeslat" value="" />';

$text .= "</form></div>\n";

/*$text .='<a href="javascript:self.document.forms.prvni.submit()" id="odeslat">
		  <img src="obrazek_tlacitka.gif" 
		  border="0"
		    alt="Po kliknutí na obrázek se formulář odešle" />
		</a>';*/

	return new textElement($text);

}