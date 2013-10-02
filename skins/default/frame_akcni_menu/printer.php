<?php
class Printer {

        function Printer(){

        }
        /** set pageproperties specific to this skin */
        function set_page_properties(&$page){
                $page->add_css($page->skin_dir.'/../allpages/style.css');
                $page->add_css($page->skin_dir.'/style.css');
                $page->add_css($page->skin_dir.'/vystavba/style.css');
                $page->add_css($page->skin_dir.'/berlin/style.css');
                $page->add_css($page->skin_dir.'/veleni/style.css');
                $page->add_css($page->skin_dir.'/obrana/obrana.css');
                $page->add_css($page->skin_dir.'/zem/zem.css');
                $page->add_css($page->skin_dir.'/letectvo/letectvo.css');
                $page->add_css($page->skin_dir.'/info_hrac/hrac.css');
                $page->add_css($page->skin_dir.'/statistika/stat.css');
        }

        function print_page($page) {
                global $error,$DIR_SCRIPTS;
                $this->set_page_properties($page);

                echo    '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n".
                                '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="cs" lang="cs"> ';
                echo    '<head>'."\n".
                                '       <meta http-equiv="Content-language" content="cs" />'."\n".
                                '       <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />'."\n".
                                '       <title>'.$page->title.'</title>'."\n";

                foreach ($page->css as $index => $value) {
                 echo   '       <link rel="stylesheet" type="text/css" href="'.$value.'" media="screen,projection" />'."\n";
                }
                echo    '       <meta name="description" content="'.$page->headline.'" />'."\n".
                                '       <meta name="copyright" content="'.$page->copyright.'" />'."\n".
                                '       <meta name="author" content="'.$page->document_author.'" />'."\n".
                                '       <meta name="robots" content="'.$page->robots.'" />'."\n";
                /* 
                  tooltips js  
                */
                echo '<script language="JavaScript1.2" src="'.$DIR_SCRIPTS.'/main.js" type="text/javascript"></script>' . "\n";
                
		        foreach ($page->scripts as $index => $value) {
		    	 echo	'	<script language="'.$value["language"].'" src="'.$value["fileName"].'" type="'.$value["type"].'"></script>'."\n";	
				}		
				foreach ($page->pub_javascripts as $index => $value) {
		    	 echo	'	<script language="JavaScript" src="'.$value.'" type="text/javascript"></script>'."\n";	
				}
        

                
                echo    '</head>'."\n";
                echo    '<body';
                
 
if (isset($page->elements['refresh']->text))
  echo " ".$page->elements['refresh']->text." ";
                echo    '>'."\n";

                /*
                  this text (and javascript) can be generated according to the needs of the action menu
                */
                echo    '<div id="TipLayer" style="visibility: hidden; position: absolute; z-index: 1000; top: -100px;"></div>' . "\n" .
                        '<script language="JavaScript1.2" type="text/javascript">                       
                        var FiltersEnabled = 1 // if your not going to use transitions or filters in any of the tips set this to 0';
                
if (isset($page->elements['tooltip']->text))
  echo $page->elements['tooltip']->text;       
                        
                echo    'applyCssFilter()
     
                        </script>' . "\n";
if (isset($page->elements['menu']->text))
  echo $page->elements['menu']->text;

if (isset($page->elements['obsah']->text))
  echo $page->elements['obsah']->text;

    global $GOOGLE_ANALYTICS, $ANALYZE;
	if ($ANALYZE){
		echo $GOOGLE_ANALYTICS;
	}

echo    '</body>'."\n".
          '</html>' ."\n" ;


        }
}

?>
