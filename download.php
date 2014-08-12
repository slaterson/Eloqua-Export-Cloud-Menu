<?php

  include_once ("/var/www/localhost/files/eloqua.inc");
  include_once ("eloquaRequest.php");
  $limit = 1000;
  $offset = 0;

  $exportUri = $_GET["eu"];
#  $body = file_get_contents('php://input');
  $body = json_decode(file_get_contents('php://input'));
  $assetUri = $body->assetUri;
  $siteId = $body->siteId;

  $login = new EloquaRequest($eloqua_site, $eloqua_userA, $eloqua_pass, "https://login.eloqua.com/id");
  $endPointBase = $login->get("");
  $endPointURL = $endPointBase->urls->base . "/API/bulk/2.0";
  $client = new EloquaRequest($eloqua_site, $eloqua_userA, $eloqua_pass, $endPointURL);
  $getData = $client->get($assetUri . "/data?limit=" . $limit . "&offset=" . $offset);

  $handle = fopen("/var/www/localhost/htdocs/eloqua/menu/files/$siteId-download.csv", "w");

  $i = 0;
  foreach ($getData->items as $item) {
    if ($i == 0) {
      $j = 0;
      foreach ($item as $key => $value) {
        if ($j == 0) {
          fwrite($handle, $key);
        } else {
          fwrite($handle, "," . $key);
        }
        $j++;
      }
      fwrite($handle, "\n");
    }
    $i++;
  }

  $hasMore = true;
  while ($hasMore == true) {
    $hasMore = $getData->hasMore;
    foreach ($getData->items as $item) {
      $j = 0;
      foreach ($item as $key => $value) {
        if ($j == 0) {
          fwrite($handle, $value);
        } else {
          fwrite($handle, "," . $value);
        }
        $j++;
      }
      fwrite($handle, "\n");
    }
    $offset = $offset + $limit;
    $getData = $client->get($assetUri . "/data?limit=" . $limit . "&offset=" . $offset);
  }
#  fwrite($handle, $assetUri . "\n");
#  fwrite($handle, $siteId . "\n");

  fclose($handle);

  $delete = $client->delete($exportUri);
?>
