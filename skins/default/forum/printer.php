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
   
	echo '<div class="forum_ohraniceni">
		 <div class="forum_vrsek">';
	echo '</div>
		 
		 <div class="forum_banner">';
	if( isset($page->elements['nadpis']->text))
		echo $page->elements['nadpis']->text;
		
	echo '</div>';
	//OBSAH
	echo '<div class="forum_obsah">';
	//MENU
		echo $page->elements['main_menu']->draw();	
	echo '<div id="reklamni_banner"></div>';

//	echo '<textarea id="text_odeslat" rows="4" cols="83" ></textarea>';
//	echo '<input id="odeslat" type="submit" value=" " name="navyseni"/>';
	echo $page->elements['formular']->draw();
	
	echo '<div id="forum_zpravy">';
	echo $page->elements['obsah']->draw();
	echo '</div>';
	// navigace
	if( isset($page->elements['navigace']->text))
			echo $page->elements['navigace']->text;
	
	echo '</div>';
	
    global $GOOGLE_ANALYTICS, $ANALYZE;
	if ($ANALYZE){
		echo $GOOGLE_ANALYTICS;
	}
	
 echo     "      </body>\n</html>\n" ;


        }
}

?>