<?php
	$obsah = "";

	
	$obsah .= '<table border="0" align="center" class="tutorial">';
	
	$obsah .= '<tr><td align="center"><a href="index.php?section=tutorial'.$tut_num.'&amp;page=tut3">Zpět</a></td>
	<td align="center">4 z '.$max_tut.' : ' . $nazev_tut . ' - útočení</td>
	<td align="center"><a href="index.php?section=tutorial'.$tut_num.'&amp;page=tut5">Dále</a></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	<img src="images/'.$tut_num.'tut4.jpg" alt="povolávání jednotek" />
	</td><td></td></tr>';
		
	$obsah .= '<tr><td></td><td>
	- I. kliknutím na černou šipku na mapě v požadovaném směru útoku dojde
  k otevření akčního menu ROZKAZ K ÚTOKU
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- II.automaticky se předpokládá, že na zvolený cíl zaútočí všechny tvé jednotky
  (pokud chceš aby nějáké zůstali a bránili, stačí čísla upravit dle svého)
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- III. Zadáním tlačítka VYDAT ROZKAZ K ÚTOKU dochází ke konečnému útoku
  na zvolený cíl.
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- po odeslání útoku uvidíš popis síly útoku se všemi bonusy. (nejsi-li
  spokojen, můžeš útok ještě do půlnoci odvolat)
	</td><td></td></tr>';
	
	$obsah .= '</table>';

	$form = new textElement( $obsah );
                        
	$page->add_element($form, 'obsah');
?>