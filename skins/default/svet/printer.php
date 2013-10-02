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
                global $error;
                $this->set_page_properties($page);

                echo    '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">'."\n".
                                '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="cs" lang="cs"> ';
                echo    '<head>'."\n".
                                '       <meta http-equiv="Content-language" content="cs" />'."\n".
                                '       <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />'."\n".
                                '       <title>'.$page->title.'</title>'."\n";

                foreach ($page->css as $index => $value) {
                 echo   '       <link rel="stylesheet" type="text/css" href="'.$value.'" media="screen,projection" />'."\n";
                }
                global $DIRECTORY;
  				echo	'	<link rel="shortcut icon" href="'.$DIRECTORY.'favicon.ico" />'."\n";
  		
                echo    '       <meta name="description" content="'.$page->headline.'" />'."\n".
                                '       <meta name="copyright" content="'.$page->copyright.'" />'."\n".
                                '       <meta name="author" content="'.$page->document_author.'" />'."\n".
                                '       <meta name="robots" content="'.$page->robots.'" />'."\n";
        //      foreach ($page->javascripts as $index => $value) {
        // echo '<script language="JavaScript" src="'.$value.'" type="text/javascript"></script>'."\n";
        //      }
        //      foreach ($page->pub_javascripts as $index => $value) {
   //    echo   '<script language="JavaScript" src="'.$value.'" type="text/javascript"></script>'."\n";
        //      }
                echo    '</head>'."\n";



echo    '<frameset rows="42,100%" border=0>'."\n".

  '<frame name="menu" frameborder="0" noresize="noresize" scrolling="no" src="'. $page->elements['menu']->text .'" />'."\n".
  '<frameset cols="296,100%" border=0>'."\n".
    '<frame  name="am" frameborder="0" noresize="noresize" src="'. $page->elements['popis']->text .'" />'."\n".
    '<frame name="mapa" frameborder="0" noresize="noresize" src="'. $page->elements['mapa']->text .'" />'."\n".
'<noframes><body> Váš prohlížeč nepodporuje rámce</body></noframes>'."\n".
  '</frameset>'."\n".
'<noframes><body> Váš prohlížeč nepodporuje rámce</body></noframes>'."\n".
'</frameset>'."\n";

echo  '</html>' ."\n" ;


        }
}

?>
