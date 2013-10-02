<?php
	$obsah = "";

	
	$obsah .= '<table border="0" align="center" class="tutorial">';
	
	$obsah .= '<tr><td align="center"><a href="index.php?section=tutorial&amp;page=tut1">Zpět</a></td>
	<td align="center">2 z '.$max_tut.' : ' . $nazev_tut . ' - menu sektoru</td>
	<td align="center"><a href="index.php?section=tutorial&amp;page=tut3">Dále</a></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	<img src="images/tut2.jpg" alt="informace o sektoru" />
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- v horním menu vidíte stav základních surovin - zásob, paliva a bodů vlivu
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- menu sektoru umožnuje snadno přecházet mezi statistikou, výstavbou, najímáním
	jednotek a posíláním útoků
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- statistika sektoru ukazuje stav infrastruktury a zisky surovin
	</td><td></td></tr>';
	
	$obsah .= '</table>';

	$form = new textElement( $obsah );
                        
	$page->add_element($form, 'obsah');
?>