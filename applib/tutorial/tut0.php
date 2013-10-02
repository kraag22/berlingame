<?php
	$obsah = "";

	
	$obsah .= '<table border="0" align="center" class="tutorial">';
	
	$obsah .= '<tr><td align="center">&nbsp;&nbsp;&nbsp;&nbsp;</td>
	<td align="center">0 z '.$max_tut.' : ' . $nazev_tut . ' - mapa</td>
	<td align="center"><a href="index.php?section=tutorial&amp;page=tut1">Dále</a></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	<a href="./skins/default/frame_mapa/map.jpg" rel="lightbox[roadtrip]" title="Grafická mapa Německa.">
	<img src="images/tut0.jpg" alt="mapa" border="0" />
	</a>
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- hraje se na grafické mapě s omezeným počtem protivníků (30-50).
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- kdykoliv během dne můžete odehrát svá kola - je jedno kdy.
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- o půlnoci se odehrávka vyhodnotí.
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- jedna hra trvá 2-3 týdny, takže někdy nemáte čas, snadno začnete znovu.
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- můžete hrát buďto za spojence nebo za sověty.
	</td><td></td></tr>';
		
	$obsah .= '</table>';

	$form = new textElement( $obsah );
                        
	$page->add_element($form, 'obsah');
?>