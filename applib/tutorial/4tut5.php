<?php
	$obsah = "";

	
	$obsah .= '<table border="0" align="center" class="tutorial">';
	
	$obsah .= '<tr><td align="center"><a href="index.php?section=tutorial'.$tut_num.'&amp;page=tut4">Zpět</a></td>
	<td align="center">5 z '.$max_tut.' : ' . $nazev_tut . ' - závěr</td>
	<td align="center"></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	<img src="images/tut_congratulation.jpg" alt="hlášení" border="0" />
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- výsledek akcí naleznete v denním hlášení
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- nyní víte, jak využívat podpory z domova
	</td><td></td></tr>';
		
	$obsah .= '</table>';

	$form = new textElement( $obsah );
                        
	$page->add_element($form, 'obsah');
?>