<?php
$form = new textElement('
	<p>Hra Berlingame.cz je ve stavu betaverze. To znamená, že během hraní můžete
	narazit na malé množství chyb a nepřesností. Také pravidla nejsou ve finální
	podobě.
    </p>
    <p>Během testování chceme získat zpětnou vazbu od Vás, hráčů. Pokud Vás napadne
    jakékoliv vylepšení, úprava nebo si všimnete chyby, napište nám mail.
    </p>
    <p>Hra zatím není plně optimalizovaná. To znamená, že nejsou podporovány 
    všechny internetové prohlížeče, a pokud máte pomalejší internetové
     připojení, může se Vám hra načítat pomalu.</p> 
     <p>
     Berlingame.cz bez problémů běží na prohlížečích Internet explorer 7 a
    Firefox 2 i 3. V prohlížeči Opera hra nebyla důkladně otestována, ale nejnovější
    verze 9.5 by měla vyhovovat bez větších nedostatků.
    <span style="color:gold;"> Internet
    explorer 6 a starší podporován není. Grafika hry se zobrazí špatně a herní
    mapa je nepoužitelná. Chyby týkající se tohoto prohlížeče nám NEPIŠTE. 
    Víme o nich.</span>
    </p>
    Tým Berlingame.cz
    ');
                        
$page->add_element($form, 'obsah');

?>