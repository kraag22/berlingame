<?php
	$obsah = "";

	
	$obsah .= '<table border="0" align="center" class="tutorial">';
	
	$obsah .= '<tr><td align="center"><a href="index.php?section=tutorial&amp;page=tut3">Zpět</a></td>
	<td align="center">4 z '.$max_tut.' : ' . $nazev_tut . ' - denní hlášení</td>
	<td align="center"><a href="index.php?section=tutorial&amp;page=tut5">Dále</a></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	<a href="./images/screen2.jpg" rel="lightbox[roadtrip]" title="Přehledné hlášení ve kterém uvidíte výsledek vašich akcí.">
	<img src="images/tut3.jpg" alt="hlášení" border="0" />
	</a>
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- hlášení má 3 části: globální, hodnocení akcí a zprávy z boje
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- v levé části vidíte zkrácený seznam 
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- kliknutím na položku seznam zobrazíte detailní informace
	</td><td></td></tr>';
	
	$obsah .= '</table>';

	$form = new textElement( $obsah );
                        
	$page->add_element($form, 'obsah');
?>