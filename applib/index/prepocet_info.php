<?php

if (is_file($DIR_LOG . date("Y-n-j"))) {
  $prepocet_info = "OK";
} else {
  $prepocet_info = "FAILED";
}
?>
