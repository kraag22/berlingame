<?php
	$obsah = "";

	
	$obsah .= '<table border="0" align="center" class="tutorial">';
	
	$obsah .= '<tr><td align="center"><a href="index.php?section=tutorial&amp;page=tut4">Zpět</a></td>
	<td align="center">5 z '.$max_tut.' : ' . $nazev_tut . ' - registrace</td>
	<td align="center">&nbsp;&nbsp;&nbsp;</td></tr>';
		
	$obsah .= '<tr><td>&nbsp;</td><td>
	</td><td>&nbsp;</td></tr>';
	$obsah .= '<tr><td></td><td>
	</td><td> </td></tr>';
	$obsah .= '<tr><td></td><td>
	</td><td> </td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- <a href="registrace.php?section=novacek">registrujte </a> se do hry<small>(max 20 sekund)</small>.
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- podrobnější informace o hře najdete v menu <a href="index.php?section=napoveda ">nápověda</a>
	</td><td></td></tr>';
	
	
	$obsah .= '</table>';

	$form = new textElement( $obsah );
                        
	$page->add_element($form, 'obsah');
?>