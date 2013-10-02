<?php
	$obsah = "";

	
	$obsah .= '<table border="0" align="center" class="tutorial">';
	
	$obsah .= '<tr><td align="center"><a href="index.php?section=tutorial'.$tut_num.'&amp;page=tut1">Zpět</a></td>
	<td align="center">2 z '.$max_tut.' : ' . $nazev_tut . ' - útoky a obrana</td>
	<td align="center"><a href="index.php?section=tutorial'.$tut_num.'&amp;page=tut3">Dále</a></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	<img src="images/'.$tut_num.'tut2.jpg" alt="informace o sektoru" />
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- sektor je bráněn všemi přítomnými jednotkami
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- pokud však vyšleš jednotky do útoku - přestanou sektor bránit
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- I. Zde je kompletní výčet z čeho se aktuálně skládá obrana zvoleného sektoru
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- veškeré útoky a obrany se hromadně vyhodnotí během půlnočního přepočtu
	</td><td></td></tr>';
		
	$obsah .= '</table>';

	$form = new textElement( $obsah );
                        
	$page->add_element($form, 'obsah');
?>