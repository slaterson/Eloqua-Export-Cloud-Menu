<?php

  $body = json_decode(file_get_contents('php://input'));
  $assetUri = $body->assetUri;
  $siteId = $body->siteId;

#  print "Segment id: " . $segment . "<br>";
  $phpExport = '/usr/bin/php /var/www/localhost/htdocs/eloqua/menu/download-cli.php ' . $assetUri . ' ' . $siteId;
  print "Command: " . $phpExport . "<br>";
#  $exec1 = exec("touch /var/www/localhost/htdocs/eloqua/menu/exec1.txt");
  $export = exec($phpExport . ' > /dev/null &');
#  $export = exec($phpExport . ' 2>&1 > /var/www/localhost/htdocs/eloqua/menu/out.log &');
#  $exec2 = exec("touch /var/www/localhost/htdocs/eloqua/menu/exec2.txt");
?>
