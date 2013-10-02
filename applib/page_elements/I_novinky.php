<?php


	$text = '';
	
	$text .= '<div id="important_news">
    <div id="i_news_top"></div>
    <div id="i_news_content">';
	
	$query = "SELECT datum, novinka FROM novinky ORDER BY datum DESC LIMIT 3";
	$res = $db->Query( $query );
	while ( $row = $db->GetFetchAssoc( $res )){
		$text .= timestamp_iso_cz($row['datum']) . '<br />' . $row['novinka'] . '<br /><br />';
	}
	
	
	$text .= '</div>
    <div id="i_news_bottom"></div>
    </div>
    ';

	$page->add_element( new textElement( $text ), 'novinky');
?>