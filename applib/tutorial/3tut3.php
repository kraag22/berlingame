<?php
	$obsah = "";

	
	$obsah .= '<table border="0" align="center" class="tutorial">';
	
	$obsah .= '<tr><td align="center"><a href="index.php?section=tutorial'.$tut_num.'&amp;page=tut2">Zpět</a></td>
	<td align="center">3 z '.$max_tut.' : ' . $nazev_tut . ' - jak naplánovat akci</td>
	<td align="center"><a href="index.php?section=tutorial'.$tut_num.'&amp;page=tut5">Dále</a></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	<img src="images/'.$tut_num.'tut3.jpg" alt="povolávání jednotek" />
	</td><td></td></tr>';
		
	$obsah .= '<tr><td></td><td>
	- úspěch letecké akce záleží na tom, zda je tvé letectvo dostatecně 
  silné, aby překonalo protivzdušnou obranu (PVO) sektoru na který útočíš.
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- I.čím vyšší leteckou sílu máš, tím těžší je pro nepřátelskou protivzdušnou 
  obranu (II.) tvou akci překazit
	</td><td></td></tr>';
	
	$obsah .= '</table>';

	$form = new textElement( $obsah );
                        
	$page->add_element($form, 'obsah');
?>