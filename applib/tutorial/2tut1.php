<?php
	$obsah = "";

	
	$obsah .= '<table border="0" align="center" class="tutorial">';
	
	$obsah .= '<tr><td align="center">&nbsp;&nbsp;&nbsp;&nbsp;</td>
	<td align="center">1 z '.$max_tut.' : ' . $nazev_tut . ' - základ</td>
	<td align="center"><a href="index.php?section=tutorial'.$tut_num.'&amp;page=tut2">Dále</a></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	<img src="images/'.$tut_num.'tut1.jpg" alt="mapa" />
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- v každém sektoru můžeš stavět zařízení či povolat podpůrné jednotky
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- většinu lze zřídit/povolat v každé zemi
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- některé však můžeš mít jen v jednom sektoru, jiné jsou zas unikátní
  pro jednu herní stranu (Spojenci či Rudá armáda)
	</td><td></td></tr>';
	
	$obsah .= '</table>';

	$form = new textElement( $obsah );
                        
	$page->add_element($form, 'obsah');
?>