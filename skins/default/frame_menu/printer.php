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
		        foreach ($page->scripts as $index => $value) {
		    	 echo	'	<script language="'.$value["language"].'" src="'.$value["fileName"].'" type="'.$value["type"].'"></script>'."\n";	
				}		
				foreach ($page->pub_javascripts as $index => $value) {
		    	 echo	'	<script language="JavaScript" src="'.$value.'" type="text/javascript"></script>'."\n";	
				}
        
                echo '<style type="text/css">'."\n";
/*
                if (isset($page->elements['css_style']->text))
                        echo $page->elements['css_style']->text;
*/ 
                echo '</style>'."\n";
        
                echo    '</head>'."\n";
                echo    '<body class="podklad" ';
                
 
if (isset($page->elements['refresh']->text))
  echo " ".$page->elements['refresh']->text." ";
                echo    '>'."\n";
?>
<div id="wrap">
  <div id="menu">
  	<ul id="menu2">
  			<li id="disp1" class="info">
  			<?php if (isset($page->elements['pocasi']->text)){echo $page->elements['pocasi']->text;} ?>
  			</li>
  			<li id="disp2" class="info"><div class="all_suroviny"><?php if (isset($page->elements['suroviny']->text)){echo $page->elements['suroviny']->text;} ?></div></li>
  			<li id="disp3" class="info"><div class="all_palivo"><?php if (isset($page->elements['palivo']->text)){echo $page->elements['palivo']->text;} ?></div></li>
  			<li id="disp4" class="info"><div class="all_body_vlivu"><?php if (isset($page->elements['body_vlivu']->text)){echo $page->elements['body_vlivu']->text;} ?></div></li>
  			<li id="disp5" class="kola"><?php if (isset($page->elements['pocet_kol']->text)){echo $page->elements['pocet_kol']->text;} ?> </li>
  			<?php echo $page->elements['buttony']->text; ?>
  	</ul>
  </div>
</div>

<?php


if (isset($page->elements['obsah']->text))
  echo $page->elements['obsah']->text;


    global $GOOGLE_ANALYTICS, $ANALYZE;
	if ($ANALYZE){
		echo $GOOGLE_ANALYTICS;
	}

echo    '</body>'."\n";
          '</html>' ."\n" ;


        }
}

?>
