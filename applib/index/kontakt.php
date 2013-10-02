<?php
$form = new textElement('
	Tým berlingame.cz:<br />
	<ul>
	<li>Dříve <s>Grimon - grafika, návrh hry, texty</s></li>
	<li>Necromorgon - programování, herní systém, webmaster</li>
	</ul>
    <br />
	<p>Kontakt:
 	<a href="mailto:bg-admin@seznam.cz">bg-admin@seznam.cz</a>
    </p>
    <br />
	<p>Pokud se chcete dozvědět aktuální informace o připravovaných novinkách ve hře, mrkněte 
	na skupinu <a href="http://www.facebook.com/group.php?gid=41419182435" target="_blank">berlingame.cz na stránkách facebook.com</a>
    </p>
    ');
                        
$page->add_element($form, 'obsah');
?>