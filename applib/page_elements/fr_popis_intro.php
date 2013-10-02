<?php
        $text= 'popis intro<br />';
        $printer = new textPrinter();
        $form = new textElement($text, $printer);
        $page->add_element($form, 'obsah');
?>