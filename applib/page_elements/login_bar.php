<?php

require_once ("${DIR_LIB}page_elements/page_element.php");

class login_barPrinter extends pageElementPrinter {

        function print_element($element){
                //parent::print_element();
                if (!$element->filled){
                        echo "<h2>$element->err_text<h2>\n";
                        return;
                }

                echo $element->text. "\n";
        }
}


class login_barElement extends pageElement {
        var $text;
        var $err_text;
        var $filled;

        function login_barElement($db,&$page, $form_skin_dir, $printer=null){
                global $users_class;
                global $fm_class,$page;
                global $DIRECTORY;

                $this->db = $db;
                $this->printer = $printer;
                $this->register($page,$form_skin_dir);
                $this->err_text = "Nastala chyba. Popis ještě nebyl zadán\n";

                if ($printer == null){
                        $this->printer = new login_barPrinter();
                }
                if ($users_class->is_logged() ){

			$this->text = "
				Přihlášen: {$users_class->user_login()}
				<br /><a href=\"index.php?section=logout\" id=\"logout\">Odhlásit se</a><br />";
                }
                else {
			$t = time();
			$this->text = "<form id=\"login_form\" method=\"post\">\n
			<input type=\"hidden\" name=\"fm_login_form_no_reload\" value=\"$t\" />\n
      <input type=\"hidden\" name=\"fm_login_form\" value=\"1\" />\n
      <input type=\"hidden\" name=\"fm_login_form_id\" value=\"\" />\n
            <input type=\"text\" name=\"lf_login\" id=\"jmeno\" title=\"Jméno\" /><br />
            <input type=\"password\" name=\"lf_pwd\" id=\"heslo\" title=\"Heslo\" /><br />
            <input type=\"image\" src=\"$form_skin_dir/login_btn.jpg\" id=\"login_btn\" name=\"lf_submit\" value=\"Přihásit\" />
          </form>";
      
      /*registrace...*/
			$this->text .= '
			<div id="registrace">
          <a href="registrace.php?section=novacek"><span>Registrace</span></a>
      </div>
      ';
      


                }
                $this->err_text = "OK";
                $this->filled = true;
        }

        function get_data_from_db(){
        }

        function register(&$page,$form_skin_dir){
                parent::register($page, null);
                $this->page = &$page;
                //$this->page->set_headline($this->page->headline. " se zaregistrovanou kartou firem");
                //$this->page->add_css("$form_skin_dir/form_style.css");
        }

        function draw() {
                parent::draw();
        }
}

?>
