<?php
/**
 * Basic pageElement types
 */

/** Page element */
class pageElement {
        var $elements;
        //podobne jako u stranky
        var $printer;
        // globalni databaze
        var $db;

        //povinne funkce
        function register(&$page, $form_skin_dir) {}
        function draw(){
                $this->printer->print_element($this);
        }

        function set_db($db){$this->db = $db;}
        function set_printer($printer){$this->printer = $printer;}
}



class pageElementPrinter {
        function print_element($element) {
        }
}


//example  - text element


class textPrinter extends pageElementPrinter {
        function print_element($element){
                parent::print_element($element);
                echo $element->text;
        }
}


class textElement extends pageElement {
        var $text;


/*        function textElement($text) {
                $this->text = $text;
                $this->printer = new textPrinter();
;
        }*/

        function textElement($text,$printer=NULL) {
                $this->text = $text;
                $this->printer = $printer;
		if ($printer == NULL) {
                	$this->printer = new textPrinter();
		} 
        }

        function draw(){
                parent::draw();
        }
}

//example  - table element (composite)

/** Page element */
class tableElement extends pageElement {
       	var $head = "";
       	var $body = array();
       	var $foot = "";
	
}
class tablePrinter extends pageElementPrinter {
        function print_element($element){
                parent::print_element($element);
                echo $element->head . "\n";
 		foreach ($element->body as $i => $value) {
			echo "<!-- $i -->$value\n";
		}

                echo $element->foot . "\n";
        }
}


?>