<?php
	$obsah = "";

	
	$obsah .= '<table border="0" align="center" class="tutorial">';
	
	$obsah .= '<tr><td align="center"><a href="index.php?section=tutorial1&amp;page=tut1">Zpět</a></td>
	<td align="center">2 z '.$max_tut.' : ' . $nazev_tut . ' - ovlivnění zisků</td>
	<td align="center"><a href="index.php?section=tutorial1&amp;page=tut3">Dále</a></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	<img src="images/'.$tut_num.'tut2.jpg" alt="informace o sektoru" />
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- zisky surovin(palivo, zásoby) vytváří pouze postavené zařízení
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- body vlivu se zařízením ovlivnit nedají
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- zisky surovin mohou ovlivnit letecké akce i podpora z domova
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- čím více ovládnete sektorů, tím větší nastávají problémy.
	Dojde ke "kolapsu zásobování" a celkové zisky surovin jsou sníženy.
	</td><td></td></tr>';
	
	$obsah .= '</table>';

	$form = new textElement( $obsah );
                        
	$page->add_element($form, 'obsah');
?>