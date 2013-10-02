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
        	



echo '
<div id="wrap_margin">
<div id="wrap">
<div id="wrap_top">
</div> <!-- wrap_top -->
<div id="wrap_body">
<div id="cont1">
<div id="cont2">
<div id="cont2a">
<div id="cont2a_top">
</div> <!-- cont2a_top -->
<div id="cont2a_body">
<div class="podminky titulek">
PODMÍNKY PRO VSTUP
</div> 
<div class="podminky_text">';
echo isset($page->elements['podminky_vstup']->text) ? $page->elements['podminky_vstup']->text : '';
echo '</div> 
</div> <!-- cont2a_body -->
<div id="cont2a_bottom">
</div> <!-- cont2a_bottom -->
<div id="cont2b_top">
</div> <!-- cont2b_top -->
<div id="cont2b_body">
<div class="podminky titulek">PODMÍNKY PRO VÝHRU</div> 
<div class="podminky_text">';
echo isset($page->elements['podminky_vyhra']->text) ? $page->elements['podminky_vyhra']->text : '';
echo '
</div>
</div> <!-- cont2b_body -->
<div id="cont2b_bottom">
</div> <!-- cont2b_bottom -->
<div id="cont2c_top">
</div> <!-- cont2c_top -->
<div id="cont2c_body">
<a title="vstoupit" href="';
echo isset($page->elements['vstoupit']->text) ? $page->elements['vstoupit']->text : '';
echo '" >
<img width="270" height="148" alt="vstoupit" src="./skins/default/info/button_vstoupit.jpg" border="0"/>
</a>
</div> <!-- cont2c_body -->
<div id="cont2c_bottom">
</div> <!-- cont2c_bottom -->
<div id="cont2button">
<a title="zpet" href="./intro.php">
<img width="180" height="33" alt="vstoupit" src="./skins/default/info/button_zpet.png" border="0"/>
</a>
</div> <!-- cont2button -->
<div id="cont2button">
<a title="titulni stranka" href="./index.php">
<img width="180" height="34" alt="titulni stranka" src="./skins/default/info/button_titulnistranka.png" border="0"/>
</a>
</div> <!-- cont2button -->
</div> <!-- cont2a -->
</div> <!-- cont2 -->
<div id="cont3">
<div id="cont3a">
<div id="cont3a_top">
</div> <!-- cont3a_top -->
<div id="cont3a_body">
<div id="cont3a_image">
<img width="170" height="135" alt="scenar" src="./skins/default/intro/'.$page->elements['typ_z_db']->text.'.png"/>
</div>
<div id="cont3a_text">
<div class="titulek_scenar_big">
<div class="titulek_scenar_big_left">NÁZEV SCÉNÁŘE:</div>
<div class="titulek_scenar_big_right">'; 
echo isset($page->elements['nazev']->text) ? $page->elements['nazev']->text : '';
echo'</div>
</div> <!-- titulek_scenar_big -->
<div class="titulek_scenar_small">
<div class="titulek_scenar_small_left">TYP SCÉNÁŘE:</div>
<div class="titulek_scenar_small_right">'; 
echo isset($page->elements['typ']->text) ? $page->elements['typ']->text : '';
echo'</div>
</div> <!-- titulek_scenar_small -->
<div class="titulek_scenar_small">
<div class="titulek_scenar_small_left">ROČNÍ OBDOBÍ:</div>
<div class="titulek_scenar_small_right">'; 
echo isset($page->elements['obdobi']->text) ? $page->elements['obdobi']->text : '';
echo'</div>
</div> <!-- titulek_scenar_small -->
<div class="titulek_scenar_small">
<div class="titulek_scenar_small_left">HERNÍ DEN:</div>
<div class="titulek_scenar_small_right_herniden">'; 
echo isset($page->elements['herni_den']->text) ? $page->elements['herni_den']->text : '';
echo'</div>
</div> <!-- titulek_scenar_small -->
<div class="titulek_scenar_small">
<div class="titulek_scenar_small_left">SOUČASNÝ POČET HRÁČŮ:</div>
<div class="titulek_scenar_small_right">'; 
echo isset($page->elements['akt_poc_hracu']->text) ? $page->elements['akt_poc_hracu']->text : '';
echo'</div>
</div> <!-- titulek_scenar_small -->
</div> <!-- cont3a_text --> 
</div> <!-- cont3a_body -->
<div id="cont3a_bottom">
</div> <!-- cont3a_bottom -->
<div id="cont3b_top">
</div> <!-- cont3b_top -->
<div id="cont3b_body">
<div class="podminky titulek">ÚVOD DO SCÉNÁŘE</div> 
<div class="uvod_scenare_text">';
echo isset($page->elements['uvod']->text) ? $page->elements['uvod']->text : '';
echo '</div> 

</div> <!-- cont3b_body -->
<div id="cont3b_bottom">
</div> <!-- cont3b_bottom -->
</div> <!-- cont3a -->
</div> <!-- cont3 -->
</div> <!-- cont1 -->
</div> <!-- wrap_body -->
<div id="wrap_bottom">
</div> <!-- wrap_bottom -->
</div> <!-- wrap -->
</div> <!-- wrap_margin  -->
';
    
    global $GOOGLE_ANALYTICS, $ANALYZE;
	if ($ANALYZE){
		echo $GOOGLE_ANALYTICS;
	}
echo      '      </body>        ' ."\n" .
          '</html>' ."\n" ;


        }
}

?>
