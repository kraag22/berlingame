<?php
	$obsah = "";

	
	$obsah .= '<table border="0" align="center" class="tutorial">';
	
	$obsah .= '<tr><td align="center">&nbsp;&nbsp;&nbsp;&nbsp;</td>
	<td align="center">1 z '.$max_tut.' : ' . $nazev_tut . ' - podmínky</td>
	<td align="center"><a href="index.php?section=tutorial'.$tut_num.'&amp;page=tut2">Dále</a></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	<img src="images/'.$tut_num.'tut1.jpg" alt="mapa" />
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- aby bylo možno vybudovat letectvo je třeba postavit alespoň 1 letiště
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- I. Zde je tvá současná letecká síla, kterou jsi vybudouval - reprezentuje
  tvé stroje včetně pilotů a personálu. Bez ní není možné letectvo používat.
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- II. Zvyšování letecké síly se provádí vepsáním počtu a potvrzením POVOLAT
  za její produkci se platí surovinou BODY VLIVU.
	</td><td></td></tr>';
	
	$obsah .= '</table>';

	$form = new textElement( $obsah );
                        
	$page->add_element($form, 'obsah');
?>