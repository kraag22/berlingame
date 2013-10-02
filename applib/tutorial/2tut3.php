<?php
	$obsah = "";

	
	$obsah .= '<table border="0" align="center" class="tutorial">';
	
	$obsah .= '<tr><td align="center"><a href="index.php?section=tutorial'.$tut_num.'&amp;page=tut2">Zpět</a></td>
	<td align="center">3 z '.$max_tut.' : ' . $nazev_tut . ' - ovládání</td>
	<td align="center"><a href="index.php?section=tutorial'.$tut_num.'&amp;page=tut4">Dále</a></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	<img src="images/'.$tut_num.'tut3.jpg" alt="povolávání jednotek" />
	</td><td></td></tr>';
		
	$obsah .= '<tr><td></td><td>
	- I.detailní informace se zobrazí, když najedeš myší na název
    zařízení/jednotky
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- již postavené/povolané...zařízení/jednotky jsou podbarveny modře
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- zařízení/jednotky, které zatím nemůžeš stavět, jsou šedivé. Důvod
  proč je prozatím nelze postavit se dozvíš najetím myší.
	</td><td></td></tr>';
	
	$obsah .= '</table>';

	$form = new textElement( $obsah );
                        
	$page->add_element($form, 'obsah');
?>