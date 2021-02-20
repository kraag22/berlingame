<?
date_default_timezone_set('Europe/Prague');

$safe = new DateTime("today");
$safe->add(new DateInterval('PT30M'));
$now = new DateTime("now");
if ($now < $safe) {
    echo '<p style="text-align: center;">PREPOCET_STATUS:FAILED</p>';
}

if (is_file($DIR_LOG . date("Y-n-j")) OR ($now < $safe)) {
  $prepocet_info = "OK";
} else {
  $prepocet_info = "FAILED";
}

echo "<html><body>";
echo $prepocet_info;
echo "</body></html>";
?>
