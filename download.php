<?php

  $body = json_decode(file_get_contents('php://input'));
  $assetUri = $body->assetUri;
  $siteId = $body->siteId;

  $phpExport = '/usr/bin/php /var/www/localhost/htdocs/eloqua/menu/download-cli.php ' . $assetUri . ' ' . $siteId;
  print "Command: " . $phpExport . "<br>";
  $export = exec($phpExport . ' > /dev/null &');
?>
