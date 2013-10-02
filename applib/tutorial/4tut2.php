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
	- pokud máš postavenou stavbu Vrchní velitelství a máš-li na kontě 
  nějáké Body vlivu za které bys mohl akce ,,kupovat" už ti nic 
  nebrání je využívat.
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- I. akce se planují v menu VRCHNÍ VELITELSTVÍ
	</td><td></td></tr>';
		
	$obsah .= '</table>';

	$form = new textElement( $obsah );
                        
	$page->add_element($form, 'obsah');
?>