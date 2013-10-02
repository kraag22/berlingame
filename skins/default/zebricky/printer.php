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
                global $LANGUAGE, $users_class, $DIR_SKINS, $page;
                $skin_dir = $DIR_SKINS. $page->skin;
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
                                '       <meta name="robots" content="'.$page->robots.'" />'."\n".
                                '       <meta http-equiv="Cache-control" content="no-cache" />'."\n".
                        '       <meta http-equiv="Pragma" content="no-cache" />'."\n";

        // echo '<script src="'.$skin_dir.'skript.js" type="text/javascript"></script>'."\n";
		//		echo '<script type="text/javascript" src="mootools-1.11.js"></script>';
                echo    '</head>'."\n";
		echo '<body>'; ?>

<div id="zebricky">
	&nbsp;
	<div id="wrapper">
		<div id="rozcestnik">
		<?php
				if (isset($page->elements['rozcestnik']->text))
  					echo $page->elements['rozcestnik']->text;
		?>
		</div>
		<div id="up">
		</div>
	
		<div id="middle">
		<table>
			<tr class="first">
				<td class="prvni">Pořadí</td>
				<td class="druhy">Jméno hráče</td>
				<td class="treti">Počet bodů</td>
			</tr>
			 <?php
				if (isset($page->elements['obsah']->text))
  					echo $page->elements['obsah']->text;
			 ?>
		</table>
		</div>
	
		<div id="down">
		</div>
	</div>
	
</div>

<?php		  
    global $GOOGLE_ANALYTICS, $ANALYZE;
	if ($ANALYZE){
		echo $GOOGLE_ANALYTICS;
	}

  echo  '</body>'."\n".
          '</html>' ."\n" ;


        }
}

?>