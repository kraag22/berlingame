<?php
	$text ='';
	$sudy_lichy = 'lichy';
	
	if( isset($_GET['rok']) && is_numeric($_GET['rok']) ){
		$rok = $_GET['rok'];
	}
	else{
		$rok = date('Y');
	}
	$t_vitezne_body = 'vitezne_body_' . $rok;
	
	$query = "select u.id, u.login, uh.".$t_vitezne_body." 
	FROM users_sys as u inner join users_hrac as uh ON uh.id_user=u.id
	WHERE uh.".$t_vitezne_body."<>0
	order by uh.".$t_vitezne_body." DESC, uh.odehranych_dnu";
	
	$res = $db->Query( $query );
	$i = 1;
	while( $row = $db->GetFetchAssoc( $res ) ){
		$text .= "<tr class=\"$sudy_lichy\">";
		$text .= '<td class="prvni">'.$i.'.</td>';
		$text .= '<td class="druhy"><a href="profil.php?id_hrac='.$row['id'].'" target="_blank">'.$row['login'].'</a></td>';
		$text .= '<td class="treti">'.$row[$t_vitezne_body].'</td>';
		$text .= "</tr>";
		$sudy_lichy = $sudy_lichy == 'lichy' ? 'sudy' : 'lichy';
		$i++;
	}

	$form = new textElement($text);
	$page->add_element($form, 'obsah');

	// Rozcestnik mezi roky
	
	$text = '';
	
	if ($rok != 2008){
		$text .= '<a href="zebricky.php?rok='.($rok - 1).'">
		<img src="./skins/default/zebricky/left.png" style="float:left; border:0px;" /></a>';
	}

    if ($rok > 2012) {
        $text .= '<div style="float:left;font-size:13px;color:rgb(45,100,25);margin-top:4px;">'.htmlsafe($rok).'</div>';
    }
    else {
        $text .= '<img src="./skins/default/zebricky/'.$rok.'.png" style="float:left;" />';
    }
	
	if ($rok != date('Y')) {
		$text .= '<a href="zebricky.php?rok='.($rok + 1).'">
		<img src="./skins/default/zebricky/right.png" style="float:left; border:0px;" /></a>';
	}
	
	$form = new textElement($text);
	$page->add_element($form, 'rozcestnik');
?>