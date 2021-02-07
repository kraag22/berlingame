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
                global $error, $DIRECTORY;
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

  				echo	'	<link rel="shortcut icon" href="favicon.ico" />'."\n";

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
        echo "
        <script language=\"javascript\" type=\"text/javascript\">
function menu_rollover(which, what)
{
  var anchor = document.getElementById(which);
  if(what == 'on')
  {
    anchor.style.backgroundPosition = \"-133px 1px\";
  }
  if(what == 'off')
  {
    anchor.style.backgroundPosition = \"0 0\";
  }
}
</script>";

echo '<script type="text/javascript">';
	if (isset($page->elements['js_not_logged'])) {
		echo $page->elements['js_not_logged']->text;
	}
echo '</script>';
        //lightbox
				echo '<script type="text/javascript" src="lightbox/prototype.js"></script>
<script type="text/javascript" src="lightbox/scriptaculous.js?load=effects,builder"></script>
<script type="text/javascript" src="lightbox/lightbox.js"></script>';
				echo '<link rel="stylesheet" href="lightbox/lightbox.css" type="text/css" media="screen" />';

                echo    '</head>'."\n";

//test na IE6
if (isset($page->private_texts['identifikace'])){
        echo    $page->private_texts['identifikace'];
}
else{
	echo '<body>';
}


$sd = $page->skin_dir;
echo '<div id="wrap">'."\n";
echo '<div id="container1">' . "\n" .
        '<div id="container2">' . "\n" .
          '<div id="top">' . "\n" .
				'
				<a href="index.php" border="0" title="">
				<img src="./skins/default/index/razitko.png" border="0" id="razitko" alt="razitko" />
				</a>

				<a href="index.php" border="0" class="logo" title="www.berlingame.cz">
				</a>
				<div id="registrovani_hraci">Registrovaných hráčů: ';
				//pocet registrovanych hracu
		        if (isset($page->elements['registrovani_hraci'])) {
					echo $page->elements['registrovani_hraci']->text;
				}
				echo'</div>

				' . "\n" .
          '</div>'."\n";
echo '<div id="menu">' . "\n" .'
<div id="menu_wrap">' . "\n" . '
	<div id="container_menu">' . "\n" .'
	  <div id="container_top">' . "\n" .'
	    <div id="container_bottom">' . "\n" .'
	      <div id="menu_content">' . "\n";

	echo $page->elements['main_menu']->draw();

	echo "</div>\n
	    </div>\n
	  </div>\n
	</div>\n";
echo "</div>\n
</div>\n";

echo '<div id="main">' . "\n" .
        '<div id="main_img">' . "\n" .
        '<div id="ie_min_height"></div>' . "\n" .
            '<div id="content">' . "\n";
echo $page->elements['obsah']->draw();
echo "</div>\n
        </div>\n
      </div>\n" .
      '<div id="right">' . "\n";
//login bar
if (isset($page->elements['sidebar'])) {
	echo $page->elements['sidebar']->draw();
}
//novinky
if (isset($page->elements['novinky'])) {
	echo $page->elements['novinky']->text;
}

if (isset($page->elements['sidemenu'])) {
  echo $page->elements['sidemenu']->draw();
}

echo "</div>\n" .
      '<div id="clearance">' . "\n" .
      "</div>\n
    </div><!-- container2 -->\n
    </div><!-- container1 -->\n
    \n
  </div><!-- wrap -->\n";

    global $GOOGLE_ANALYTICS, $ANALYZE;
	if ($ANALYZE){
		echo $GOOGLE_ANALYTICS;
	}

if (isset($page->private_texts['prepocet_info'])){
  echo "<p hidden>\n";
  echo "PREPOCET_STATUS:" . $page->private_texts['prepocet_info'];
  echo "</p>\n";
}

echo '</body>
</html>
' ;
        }
}

?>
