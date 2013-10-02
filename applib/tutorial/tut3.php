<?php
	$obsah = "";

	
	$obsah .= '<table border="0" align="center" class="tutorial">';
	
	$obsah .= '<tr><td align="center"><a href="index.php?section=tutorial&amp;page=tut2">Zpět</a></td>
	<td align="center">3 z '.$max_tut.' : ' . $nazev_tut . ' - povolávání jednotek</td>
	<td align="center"><a href="index.php?section=tutorial&amp;page=tut4">Dále</a></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	<img src="images/tut4.jpg" alt="povolávání jednotek" />
	</td><td></td></tr>';
		
	$obsah .= '<tr><td></td><td>
	- v každém sektoru můžete najímat jednotky: pěchotu a tanky
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- počet jednotek, které můžete jeden den povolat, je omezen
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- přejeďte myší nad obrázek jednotky a zobrazí se Vám nápověda
	</td><td></td></tr>';

	
	$obsah .= '</table>';

	$form = new textElement( $obsah );
                        
	$page->add_element($form, 'obsah');
?>