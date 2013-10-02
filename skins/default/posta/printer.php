<?php
class Printer {

        function Printer(){

        }
        /** set pageproperties specific to this skin */
        function set_page_properties(&$page){
                $page->add_css($page->skin_dir.'/../allpages/style.css');
                $page->add_css($page->skin_dir.'/style.css');
        }

        function print_page($page) {
			global $error,$DIR_SKINS;
            $this->set_page_properties($page);

			require_once( $DIR_SKINS . "default/allpages/default_file_header.php");
        	

?>
<div class="posta_ohraniceni">
	<?php 
	 echo '<div class="posta_menu">';
	  
	 	if (isset($page->elements['menu']->text))
 		 echo $page->elements['menu']->text;

	echo '</div>';
	
	//HLASENI OBSAH	 
	 
	 if (isset($page->elements['obsah']->text))
  		echo $page->elements['obsah']->text;
  	 ?>
	 </div>
 
</div>
<?php 
    global $GOOGLE_ANALYTICS, $ANALYZE;
	if ($ANALYZE){
		echo $GOOGLE_ANALYTICS;
	}
	
 echo     "      </body>\n</html>\n" ;


        }
}

?>