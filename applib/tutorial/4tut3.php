<?php
	$obsah = "";

	
	$obsah .= '<table border="0" align="center" class="tutorial">';
	
	$obsah .= '<tr><td align="center"><a href="index.php?section=tutorial'.$tut_num.'&amp;page=tut2">Zpět</a></td>
	<td align="center">3 z '.$max_tut.' : ' . $nazev_tut . ' - jak naplánovat akci</td>
	<td align="center"><a href="index.php?section=tutorial'.$tut_num.'&amp;page=tut4">Dále</a></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	<img src="images/'.$tut_num.'tut3.jpg" alt="povolávání jednotek" />
	</td><td></td></tr>';
		
	$obsah .= '<tr><td></td><td>
	- nejdříve zvolíš typ akce. (např. Očerňující kampaň) 
	</td><td></td></tr>';
	
	$obsah .= '<tr><td></td><td>
	- poté vyber cíl (buď celého hráče nebo jen jeden sektor)
	</td><td></td></tr>';
		
	$obsah .= '</table>';

	$form = new textElement( $obsah );
                        
	$page->add_element($form, 'obsah');
?>