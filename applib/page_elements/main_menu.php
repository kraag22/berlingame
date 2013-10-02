<?php

require_once("${DIR_LIB}/page_elements/page_element.php");

class main_menuElement extends tableElement {

	var $form_skin_dir;

        function main_menuElement($form_skin_dir){

                $this->printer = new tablePrinter();
		$this->form_skin_dir = $form_skin_dir;

		$this->head = "\n";
		$this->body = array();
		$this->foot = "\n";
		// TODO: add ads to foot
    //WARNING: do not add ads here, or they would be added into menu. Use some other technique instead... (new element, etc...) 
	}

	function add_menuitem($text, $link, $img, $target = '_top') {
		$path = $this->form_skin_dir;
    $this->body[] = 
    '<a href="'.$link.'" id="'.$img.'" style="background: url('.$path.'/'.$img.');" onmouseover="menu_rollover(\''.$img.'\',\'on\');" onmouseout="menu_rollover(\''.$img.'\',\'off\');"
    target="'.$target.'"><span>'.$text.'</span></a>';
	}
	
        function draw() {
                parent::draw();
        }

}

