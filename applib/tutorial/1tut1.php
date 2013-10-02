<?php
	$obsah = "";

	
	$obsah .= '<table border="0" align="center" class="tutorial">';
	
	$obsah .= '<tr><td align="center">&nbsp;&nbsp;&nbsp;&nbsp;</td>
	<td align="center">1 z '.$max_tut.' : ' . $nazev_tut . ' - suroviny</td>
	<td align="center"><a href="index.php?section=tutorial1&amp;page=tut2">Dále</a></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	<img src="images/'.$tut_num.'tut1.jpg" alt="statistika výdělků" />
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- každý sektor produkuje suroviny: zásoby, palivo a body vlivu
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- celkové příjmy za tah vzniknou součtem příjmů všech sektorů
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- každý tah spotřebují vojenské jednotky určité množství paliva a zásob
	</td><td></td></tr>';
	
	$obsah .= '</table>';

	$form = new textElement( $obsah );
                        
	$page->add_element($form, 'obsah');
?>