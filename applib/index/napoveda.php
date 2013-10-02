<?php
$form = new textElement('
<div class="napoveda_obsah">
<p>
Pokud se chcete rychle seznámit se hrou, prohlédněte si 
<a href="index.php?section=tutorial">tutoriál</a>
<small>(max 60 sekund)</small>.
</p>
<p>
Přímo ve hře narazíte často na tento obrázek 
<img src="'.$DIR_SKINS .'default/frame_akcni_menu/napoveda.png" alt="nápoveda" style="border:0px;" />
. Pokud na něj kliknete, otevřete nápovědu k té části hry, ve které se nacházíte.
</p>	

<b>Obrázkové tutoriály:<small>(max 60 sekund)</small></b><br />
<ul>
<li><a href="index.php?section=tutorial1">Ekonomika</a>
 - rychlá ukázka toho, jak si vybudovat fungující ekonomiku</li>
<li><a href="index.php?section=tutorial2">Výstavba</a>
 - tutoriál s popisem, jak stavět stavby</li>
 <li><a href="index.php?section=tutorial5">Boj</a>
 - tutoriál, jak povolávat jednotky a jak útočit</li>
<li><a href="index.php?section=tutorial3">Letectvo</a>
 - ukázka, jak budovat letectvo a jak jím útočit na soupeře</li>
<li><a href="index.php?section=tutorial4">Podpora</a>
 - návod, jak využívat podpory ze zázemí</li>
 </ul>
</div>
');
                        
$page->add_element($form, 'obsah');
?>