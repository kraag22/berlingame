<?
date_default_timezone_set('Europe/Prague');

$safe = new DateTime("today");
$safe->add(new DateInterval('PT1H'));
$now = new DateTime("now");

if (is_file('./log/' . date("Y-n-j")) OR ($now < $safe)) {
  $prepocet_info = "OK";
} else {
  $prepocet_info = "FAILED";
}

echo "<html><body>";
echo '<p style="text-align: center;">';
echo $prepocet_info;
echo '</p>';
echo "</body></html>";
?>
