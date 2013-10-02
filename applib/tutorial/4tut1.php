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
	- podpora z domova zahrnuje akce rozmanitého typu, jako jsou
  například útoky speciálních jednotek, propaganda či žádosti o 
  zásobování.
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- I. pro její využívání je třeba postavit zařízení VRCHNÍ VELITELSTVÍ
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- za použítí těchto akcí se ,,platí" body vlivu
	</td><td></td></tr>';
	
	$obsah .= '</table>';

	$form = new textElement( $obsah );
                        
	$page->add_element($form, 'obsah');
?>