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
//partnerske weby

//primat
echo '

<div class="partner">
<a href="http://www.military.cz" title="Archiv vojenské techniky" target="_blank">
<img width="88" height="33" border="0" src="./images/military.gif" /></a>
</div>

<div class="partner">
<a href="http://www.primat.cz" title="Primát - studijní materiály" target="_blank">
<img width="88" height="31" border="0" src="http://www.primat.cz/soubory/88_31.png" /></a>
</div>';

echo '<div class="partner">
<a href="http://www.valka.cz" title="Valka.cz" target="_blank">
<img width="88" height="31" border="0" src="./images/valka.gif" /></a>
</div>';

echo '<div class="partner">
<a href="http://www.specwar.info" title="Spec war - svět moderního válčení" target="_blank">
<img width="88" height="31" border="0" src="./images/specwar.gif" /></a>
</div>';

echo '
<div class="partner">
<a href="http://webove-hry.ic.cz/" target="_blank">
<img title="webové hry" height="31" src="./images/Banner_Wh_88x31.gif" width="88" border="0">
</a>
</div>';

echo '
<div class="partner">
<a href="http://www.militaryfoto.sk" target="_blank">
<img src="./images/militarysk.gif" alt="military foto" border="0" width="88" height="35">
</a>
</div>';

echo '
<div class="partner">
<a href="http://www.valecnavidea.cekuj.net/" target="_blank">
<img src="./images/valecnavidea.jpg" alt="valecna videa" border="0" width="88" height="31">
</a>
</div>';

echo '
<div class="partner">
<a href="http://top-armyshop.cz" title="Top Armyshop - Army, Security, Camping, Outdoor" target="_blank">
		<img src="http://www.safetyagency.cz/banner/banner_tiny_link.jpg" style="border: 0px; width: 88px; height: 31px">
		</a>
</div>
';

echo '
<div class="partner">
<a href="http://raketka.cz/" target="_blank"><img src="http://raketka.cz/public/bannery/banner-88x31.gif" alt="online hry raketka.cz"></a>
</div>
';

echo '
<div style=" margin-left: 15px;margin-top: 6px;font-size:14px;">
<a href="http://www.topwebhry.cz" target="_blank">
www.topwebhry.cz
</a>
</div>';

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

echo '</body>     
</html>
' ;
        }
}

?>
