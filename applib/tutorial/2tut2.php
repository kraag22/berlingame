<?php
	$obsah = "";

	
	$obsah .= '<table border="0" align="center" class="tutorial">';
	
	$obsah .= '<tr><td align="center"><a href="index.php?section=tutorial'.$tut_num.'&amp;page=tut1">Zpět</a></td>
	<td align="center">2 z '.$max_tut.' : ' . $nazev_tut . ' - význam</td>
	<td align="center"><a href="index.php?section=tutorial'.$tut_num.'&amp;page=tut3">Dále</a></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	<img src="images/'.$tut_num.'tut2.jpg" alt="informace o sektoru" />
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- dělí se na zařízení (žluté) a podpůrné jednotky (červené)
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- mohou: zvyšovat zisky surovin (zásoby, palivo, body vlivu)
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- obrannou i útočnou sílu jednotek v sektoru
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- a mnoho dalšího
	</td><td></td></tr>';
	
	$obsah .= '</table>';

	$form = new textElement( $obsah );
                        
	$page->add_element($form, 'obsah');
?>