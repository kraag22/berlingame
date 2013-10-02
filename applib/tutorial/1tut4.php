<?php
	$obsah = "";

	
	$obsah .= '<table border="0" align="center" class="tutorial">';
	
	$obsah .= '<tr><td align="center"><a href="index.php?section=tutorial1&amp;page=tut3">Zpět</a></td>
	<td align="center">4 z '.$max_tut.' : ' . $nazev_tut . ' - závěr</td>
	<td align="center"></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	<img src="images/tut_congratulation.jpg" alt="hlášení" border="0" />
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- nyní víte, jak vybudovat prosperující ekonomiku
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- můžete pokračovat <a href="index.php?section=tutorial2&amp;page=tut1">následujícím tutoriálem</a>
	</td><td></td></tr>';
	
	$obsah .= '</table>';

	$form = new textElement( $obsah );
                        
	$page->add_element($form, 'obsah');
?>