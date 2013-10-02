<?php
	$obsah = "";

	
	$obsah .= '<table border="0" align="center" class="tutorial">';
	
	$obsah .= '<tr><td align="center"><a href="index.php?section=tutorial1&amp;page=tut2">Zpět</a></td>
	<td align="center">3 z '.$max_tut.' : ' . $nazev_tut . ' - infrastruktura</td>
	<td align="center"><a href="index.php?section=tutorial1&amp;page=tut4">Dále</a></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	<img src="images/'.$tut_num.'tut3.jpg" alt="povolávání jednotek" />
	</td><td></td></tr>';
		
	$obsah .= '<tr><td></td><td>
	- infrastruktura sektoru ovlivňuje zisky zásob a paliva
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- čím více procent infrastruktury - tím větší zisky sektoru
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- infrastruktura může změnit zisky maximálně o 50% (snížit i zvýšit)
	</td><td></td></tr>';

	
	$obsah .= '</table>';

	$form = new textElement( $obsah );
                        
	$page->add_element($form, 'obsah');
?>