<?php
	$obsah = "";

	
	$obsah .= '<table border="0" align="center" class="tutorial">';
	
	$obsah .= '<tr><td align="center">&nbsp;&nbsp;&nbsp;&nbsp;</td>
	<td align="center">1 z '.$max_tut.' : ' . $nazev_tut . ' - typy jednotek</td>
	<td align="center"><a href="index.php?section=tutorial'.$tut_num.'&amp;page=tut2">Dále</a></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	<img src="images/'.$tut_num.'tut1.jpg" alt="mapa" />
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- hra má dva základní typy jednotek: pěchotu a tanky
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- I. jejich bojové parametry (najetí kurzorem) jsou útok a obrana
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- celkový útok či obrana zvoleného sektoru se vypočítá sečtením 
  těchto parametrů jednotek, přítomných v sektoru.
	</td><td></td></tr>';
	
	$obsah .= '</table>';

	$form = new textElement( $obsah );
                        
	$page->add_element($form, 'obsah');
?>