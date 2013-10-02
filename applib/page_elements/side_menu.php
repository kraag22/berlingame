<?php

require_once("${DIR_LIB}/page_elements/page_element.php");

class side_menuElement extends tableElement {

	var $form_skin_dir;

        function side_menuElement($form_skin_dir){

                $this->printer = new tablePrinter();
		$this->form_skin_dir = $form_skin_dir;

		$this->head = "<table id=\"sidemenu\">";
		$this->body = array();
		$this->foot = "</table>";
		// TODO: add ads to foot

	}

	function add_menuitem($text, $link, $img) {
		$path = $this->form_skin_dir;

		list($width, $height, $type, $attr) = getimagesize("{$this->form_skin_dir}/$img");
		$this->body[] = 
			"<tr><td><a href=\"$link\">" .
			"<img class=\"mainmenu_button\" src=\"$path/$img\" alt=\"$text\" $attr />".
			"</a></td></tr>\n";
	}


        function draw() {
                parent::draw();
        }

}
