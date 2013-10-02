<?php
	$obsah = "";

	
	$obsah .= '<table border="0" align="center" class="tutorial">';
	
	$obsah .= '<tr><td align="center"><a href="index.php?section=tutorial'.$tut_num.'&amp;page=tut1">Zpět</a></td>
	<td align="center">2 z '.$max_tut.' : ' . $nazev_tut . ' - kde naplánovat akci</td>
	<td align="center"><a href="index.php?section=tutorial'.$tut_num.'&amp;page=tut3">Dále</a></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	<img src="images/'.$tut_num.'tut2.jpg" alt="informace o sektoru" />
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- pokud máš letiště a leteckou sílu, můžeš plánovat letecké akce,
  jejich vyhodnocení stejně jako u útoků a obran probíhá o půlnoci
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- I. je třeba zvolit typ letecké akce. (např. bombardování)
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- poté vybrat cíl (buďto celého hráče nebo následně jen jeden sektor)
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- a nakonec vše potvrdit kliknutím na tlačítko NAPLÁNOVAT
	</td><td></td></tr>';
		
	$obsah .= '</table>';

	$form = new textElement( $obsah );
                        
	$page->add_element($form, 'obsah');
?>