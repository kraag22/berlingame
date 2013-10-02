<?php
	$obsah = "";

	
	$obsah .= '<table border="0" align="center" class="tutorial">';
	
	$obsah .= '<tr><td align="center"><a href="index.php?section=tutorial'.$tut_num.'&amp;page=tut2">Zpět</a></td>
	<td align="center">3 z '.$max_tut.' : ' . $nazev_tut . ' - celkově</td>
	<td align="center"><a href="index.php?section=tutorial'.$tut_num.'&amp;page=tut4">Dále</a></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	<img src="images/'.$tut_num.'tut3.jpg" alt="povolávání jednotek" />
	</td><td></td></tr>';
		
	$obsah .= '<tr><td></td><td>
	- bojovou sílu sektoru často krom jednotek ovlivňují i jiné (často velmi
  důležité) faktory jako, různá obranná zařízení či útočné podpůrné jednotky
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- I. povolání jednotek v sektoru se provádí vepsáním požadovaného počtu 
  do příslušné kolonky a následného stisknutí tlačítka povolat.
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- II.každý den můžeš povolat jen omezený počet jednotek. Přesný počet 
  je ovlivněn infrastrukturou sektoru a počtem odehraných tahů.
	</td><td></td></tr>';
	
	$obsah .= '</table>';

	$form = new textElement( $obsah );
                        
	$page->add_element($form, 'obsah');
?>