<?php
$a=file_get_contents("https://www.coingecko.com/coins/1/sparkline");
$myfile = fopen("/tmp/newfile.txt", "w") or die("Unable to open file!");
fwrite($myfile, $a);
fclose($myfile);
?>
