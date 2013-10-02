<?php
	$obsah = "";

	
	$obsah .= '<table border="0" align="center" class="tutorial">';
	
	$obsah .= '<tr><td align="center"><a href="index.php?section=tutorial'.$tut_num.'&amp;page=tut3">Zpět</a></td>
	<td align="center">4 z '.$max_tut.' : ' . $nazev_tut . ' - úspěšnost akce</td>
	<td align="center"><a href="index.php?section=tutorial'.$tut_num.'&amp;page=tut5">Dále</a></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	<img src="images/'.$tut_num.'tut4.jpg" alt="povolávání jednotek" />
	</td><td></td></tr>';
		
	$obsah .= '<tr><td></td><td>
	- I.úspěch seslané akce záleží na tom, zda vaše aktivita v cílovém sektoru
  nebyla odhalena tamní kontrarozvědkou (Vojenská policie - MP). 
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- vojenská policie je podpůrná jednotka, která se dá povolat v menu 
 "výstavba" a jak již bylo řečeno chrání sektory před nepřátelskými 
  podpůrnými akcemi.
	</td><td></td></tr>';
	
	$obsah .= '</table>';

	$form = new textElement( $obsah );
                        
	$page->add_element($form, 'obsah');
?>