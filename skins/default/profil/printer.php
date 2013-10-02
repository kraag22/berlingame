<?php
class Printer {

        function Printer(){

        }
        /** set pageproperties specific to this skin */
        function set_page_properties(&$page){
                $page->add_css($page->skin_dir.'/../allpages/style.css');
                $page->add_css($page->skin_dir.'/style.css');
        }
		function zobraz( $page, $id ){
			if (isset($page->elements[$id]->text))
  				echo $page->elements[$id]->text;
		}
		
        function print_page($page) {
			global $error,$DIR_SKINS;
            $this->set_page_properties($page);

			require_once( $DIR_SKINS . "default/allpages/default_file_header.php");

                echo    '<div id="TipLayer" style="visibility: hidden; position: absolute; z-index: 1000; top: -100px;"></div>' . "\n" .
                        '<script language="JavaScript1.2" type="text/javascript">                       
                        var FiltersEnabled = 1 // if your not going to use transitions or filters in any of the tips set this to 0';
                
if (isset($page->elements['tooltip']->text))
  echo $page->elements['tooltip']->text;       
                        
                echo    'applyCssFilter()
     
                        </script>' . "\n";
?>
<div id="wrap_margin">
<div id="wrap">
<div id="wrap_top">
</div> <!-- wrap_top -->
<div id="wrap_body">
<div id="cont1">
<div id="cont2">
<div id="cont2a">
<div id="cont2a_vyznamenani">
<div class="vyznamenani"></div> <!-- zde zapisuj vyznamenani. -->
</div> <!-- cont2a_vyznamenani -->
<a href="#" class="cont2a_seznam_text"></a>
<form action="profil.php" method="post">
<div id="cont2c_body">
<input type="text" id="cont2c_input" title="Vyhledat hrace" name="search_player" maxlength="30" value="<?php $this->zobraz($page, 'input_value');?>"/>
</div> <!-- cont2c_body -->
<input type="submit" name="vyhledat" title="" id="cont2buttonsearch" value="" />
</form>
</div> <!-- cont2a -->
</div> <!-- cont2 -->
<div id="cont3">
<div id="cont3a">
<div id="cont3a_image">
<img width="75" height="81" alt="scenar" src="./skins/default/profil/portret.png"/>
</div>
<div id="cont3a_text">
<div class="profil_name"><div><?php $this->zobraz($page, 'nick');?></div></div>
<!-- titulek_scenar_big -->
<div class="profil_created_wrap">
<div>Profil založen dne: <?php $this->zobraz($page, 'zalozen');?><br/>
Počet odehraných dnů: <?php $this->zobraz($page, 'odehranych_dnu');?></div>
</div> <!-- profil_created_wrap -->
</div> <!-- cont3a_text --> 
</div> <!-- cont3a -->

<div id="cont3b_top">
</div> <!-- cont3b_top -->
<div id="cont3b_body">
<div class="statistika_text statistika_common"><?php $this->zobraz($page, 'statistiky');?></div> 

</div> <!-- cont3b_body -->
<div id="cont3b_bottom">
</div> <!-- cont3b_bottom -->

</div> <!-- cont3 -->
</div> <!-- cont1 -->
</div> <!-- wrap_body -->
<div id="wrap_bottom">
</div> <!-- wrap_bottom -->
</div> <!-- wrap -->
</div> <!-- wrap_margin  -->

<?php 
    global $GOOGLE_ANALYTICS, $ANALYZE;
	if ($ANALYZE){
		echo $GOOGLE_ANALYTICS;
	}
	
echo      '      </body>        ' ."\n" .
          '</html>' ."\n" ;


        }
}

?>
