<?php
	$obsah = "";

	
	$obsah .= '<table border="0" align="center" class="tutorial">';
	
	$obsah .= '<tr><td align="center"><a href="index.php?section=tutorial&amp;page=tut0">Zpět</a></td>
	<td align="center">1 z '.$max_tut.' : ' . $nazev_tut . ' - mapa</td>
	<td align="center"><a href="index.php?section=tutorial&amp;page=tut2">Dále</a></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	<img src="images/tut1.jpg" alt="mapa" />
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- sektory pod Vaší kontrolou jsou červeně podbarvené
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- kliknutím na symbol strany otevřete menu a informace o sektoru
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- mapa je aktivní a velice ulehčuje a zrychluje odehrání
	</td><td></td></tr>';
		
	$obsah .= '</table>';

	$form = new textElement( $obsah );
                        
	$page->add_element($form, 'obsah');
?>