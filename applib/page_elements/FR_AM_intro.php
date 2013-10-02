<?php

	$text = "<div class=\"intro\">";
	$text .= "Nejste hráč tohoto scénáře. Pokud chcete hrát, musíte se ";
	$text .= "<a href=\"registrace.php\" target=\"_blank\" >registrovat</a>
	a následně přihlásit.";
	$text .= "</div>";

    $form = new textElement($text);
    $page->add_element($form, 'obsah');
?>