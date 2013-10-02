<?php
        $printer = new textPrinter();
    $form = new textElement($fm_class->create_form('registrace_veteran', null, null, $page) , $printer);
    $page->add_element($form, 'obsah');
?>