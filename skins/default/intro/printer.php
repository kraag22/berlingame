<?php
class Printer {

        function Printer(){

        }
        /** set pageproperties specific to this skin */
        function set_page_properties(&$page){
        	global $DIR_SCRIPTS;
                $page->add_css($page->skin_dir.'/../allpages/style.css');
                $page->add_css($page->skin_dir.'/style.css');
                $pole = array();
                //TODO includovat jquery globalne
                $pole['fileName'] = $DIR_SCRIPTS . 'jquery-1.3.2.min.js';
                $page->add_script($pole);
        }

        function print_page($page) {
			global $error,$DIR_SKINS;
            $this->set_page_properties($page);

			require_once( $DIR_SKINS . "default/allpages/default_file_header.php");
        	



echo '<div class="ohraniceni">'."\n";

if (isset($page->elements['obsah']->text))
  echo $page->elements['obsah']->text;

echo '</div>'."\n";



if (isset($page->private_texts['footer']))
        echo    $page->private_texts['footer'];
    
    global $GOOGLE_ANALYTICS, $ANALYZE;
	if ($ANALYZE){
		echo $GOOGLE_ANALYTICS;
	}
echo      '      </body>        ' ."\n" .
          '</html>' ."\n" ;


        }
}

?>
