<?php

global $LANGUAGE, $SKIN_SUBDIR, $users_class, $DIR_SKINS,$page, $DIR_LIB;

require_once ($DIR_LIB . "/page_elements/fr_mapa_zobraz_zeme.php");
$skin_dir = $DIR_SKINS. $page->skin . "/frame_mapa/";

$text= '

<div style="overflow:auto;width:600px;">
<!-- loading bar -->
<div style="z-index:50; background-color:grey; width:100px; height:40px;
position:absolute; left:0px; top:0px;visibility:visible;"
id="loading"></div>
<!-- end of loading bar -->

<div class="gr_mapa">';

$id_ligy = isset($_SESSION["id_ligy"])?$_SESSION["id_ligy"]:-1;
$res = $db->Query( "SELECT odehranych_dnu FROM ligy WHERE id='$id_ligy'" );
$row = $db->GetFetchAssoc( $res );
if($row["odehranych_dnu"] == 1){
	$text .= ZobrazZeme( $skin_dir );
}
else{
	$text .= ZobrazMapu( $skin_dir, $id_ligy );
}

$text .= '</div>



</div>



<script type="text/javascript">

//nacitani vsech obrazku
LoadImages(\''.$skin_dir.'zeme/\');
// po nacteni stranky zastavi casovac a zrusi viditelnost loading baru
clearInterval(casovac);
document.getElementById( "loading" ).style.visibility = "hidden";

</script>
';

        $form = new textElement($text);
        $page->add_element($form, 'obsah');

?>