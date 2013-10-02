<?php
$text = '<p>
			<span style="color: #ee2f37; font-size:18pt;">Vítejte,</span><br />
			na stránkách první české webové strategie z prostředí druhé světové války.
			</p>
			<p>
			<span style="color: #ee2f37;">Jak začít hrát?</span> 
			Prohlédněte si <a href="index.php?section=tutorial">tutoriál</a><small> (několik málo minut)</small>.
			</p>
            <p>
           	Vžijte se do role spojeneckého či sovětského velitele a 
rozhodněte o osudu Německa. Využijte všech dostupných 
prostředků tehdejší doby jako tanků, pěchoty, letectva, 
rozvědky a mnoha dalších. Zažijte drtivé ofenzívy, ale
i taktické ústupy či zoufalé obrany nepřátel pokoušejících
se vzdorovat vašemu strategickému umu. 
            </p>
            <div class="screenshoty">
            <span style="color: #ee2f37; font-size:16pt;">Ukázky ze hry:</span><br />
            <a href="./images/screen1.jpg" rel="lightbox[roadtrip]" title="Záložka povolávání nových jednotek.">
            	<img src="./images/screen1_m.jpg" border="0" width="100" height="60" alt="mapa"/>
            	</a>
            <a href="./images/screen2.jpg" rel="lightbox[roadtrip]" title="Záložka letectva.">
            	<img src="./images/screen2_m.jpg" border="0" width="100" height="60" alt="mapa"/>
            	</a>
            <a href="./images/screen4.jpg" rel="lightbox[roadtrip]" title="Koláž různých částí hry.">
            	<img src="./images/screen4_m.jpg" border="0" width="100" height="60" alt="mapa"/>
            	</a>
            <a href="./images/screen5.jpg" rel="lightbox[roadtrip]" title="Přehledné hlášení ve kterém uvidíte výsledek vašich akcí.">
            	<img src="./images/screen5_m.jpg" border="0" width="100" height="60" alt="mapa"/>
            	</a>
            <a href="./images/screen3.jpg" rel="lightbox[roadtrip]" title="Ve hře můžete komunitkovat s ostatními hráči poštou.">
            	<img src="./images/screen3_m.jpg" border="0" width="100" height="60" alt="mapa"/>
            	</a>
            </div>
            <p>
           	<strong>Zdrojové kódy hry jsou volně přístupné na <a href="https://github.com/kraag22/berlingame" target="_blank">githubu</a></strong>
           	<br />
           	Pokud se chcete podívat nebo něco opravit, jen do toho.
           	</p>';

function VratSeznam( $query ){
	global $db;
	
	$res = $db->Query( $query );
	$text = "<ol class=\"hraci\">";
	$i = 1;
	while( $row = $db->GetFetchAssoc( $res ) ){
		if( $i == 1){
			$text .= "<li style=\"color:#EE2F37;\" >".$i;
			$text .= ".<a href=\"profil.php?id_hrac=${row['id']}\" target=\"_blank\"> <span class=\"hrac_prvni\">${row['login']}</span> </a> </li>";
		}
		else{
			$text .= "<li >$i. <a href=\"profil.php?id_hrac=${row['id']}\" target=\"_blank\"> ${row['login']} </a> </li>";
		}
		$i++;
	}
	return $text . "</ol>";
}

$statistiky = "";
$url = "./skins/default/index/";
$statistiky .= '<img src="'.$url.'cara.png" width="539" height="4" style="margin-left:25px;margin-bottom:20px;" />';
$statistiky .= '<div class="statistiky">';




$statistiky .= '<div class="poradnik">';
$statistiky .= '<img src="'.$url.'uspesni.png" width="120" height="17" style="margin-left:14px;" />';
//NEJLEPSI
$query = "select uh.id_user as id, u.login, uh.".T_VITEZNE_BODY." 
	FROM users_sys as u inner join users_hrac as uh ON uh.id_user=u.id
	where uh.".T_VITEZNE_BODY." > 0
	order by uh.".T_VITEZNE_BODY." DESC, uh.odehranych_dnu LIMIT 6";
$statistiky .= VratSeznam( $query );

$statistiky .= '</div>';
$statistiky .= '<div class="poradnik">';
$statistiky .= '<img src="'.$url.'stari.png" width="97" height="17" style="margin-left:25px;" />';
//NEJDELE HRAJICI
$query = "select id_user as id, login from users_sys as us join users_hrac as uh on us.id=uh.id_user
 order by uh.odehranych_dnu DESC LIMIT 6";
$statistiky .= VratSeznam( $query );

$statistiky .= '</div>';
$statistiky .= '<div class="poradnik">';
$statistiky .= '<img src="'.$url.'aktivni.png" width="120" height="17" style="margin-left:13px;" />';
//NEJAKTIVNEJSI
$query = "select id, login from users_sys order by login_count DESC LIMIT 6";
$statistiky .= VratSeznam( $query );

$statistiky .= '</div>';
$statistiky .= '</div>';
$statistiky .= '<div style="margin-left:210px;"><a href="zebricky.php" target="_blank">Žebříček všech hráčů</a></div>';
$form = new textElement( $text . $statistiky );
$page->add_element($form, 'obsah');

?>