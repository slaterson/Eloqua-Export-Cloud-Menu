<?php

  include_once ("/var/www/localhost/files/eloqua.inc");
  include_once ("eloquaRequest.php");
  $limit = 1000;
  $offset = 0;

#  $exportUri = $_GET["eu"];
#  $body = file_get_contents('php://input');
#  $body = json_decode(file_get_contents('php://input'));
  $assetUri = $argv[1];
  $siteId = $argv[2];

#  $assetUri = $body->assetUri;
#  $siteId = $body->siteId;

  $login = new EloquaRequest($eloqua_site, $eloqua_userA, $eloqua_pass, "https://login.eloqua.com/id");
  $endPointBase = $login->get("");
  $endPointURL = $endPointBase->urls->base . "/API/bulk/2.0";
  $client = new EloquaRequest($eloqua_site, $eloqua_userA, $eloqua_pass, $endPointURL);
  $getData = $client->get($assetUri . "/data?limit=" . $limit . "&offset=" . $offset);

  $filename = $siteId . "-" . sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)) . ".csv";

  $handle = fopen("/var/www/localhost/htdocs/eloqua/menu/files/$filename", "w");

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

#  $delete = $client->delete($exportUri);

  class htmlContent {
    public $type;
    public $html;
  }

  class email {
    public $name;
    public $emailFooterId;
    public $emailHeaderId;
    public $encodingId;
    public $emailGroupId;
    public $subject;
    public $htmlContent;
  }

  $htmlContent = new htmlContent();
  $htmlContent->type = "RawHtmlContent";
  $htmlContent->html = "<html><head></head><body><a href='http://mungkey.org/eloqua/menu/files/$filename'>$filename</a></body></html>";

  $email = new email();
  $email->name = "Menu Email from API";
  $email->emailFooterId = 1;
  $email->emailHeaderId = 1;
  $email->encodingId = 1;
  $email->emailGroupId = 1;
  $email->subject = "Eloqua - Your File is Ready to Download";
  $email->htmlContent = $htmlContent;

  $client = new EloquaRequest($eloqua_site, $eloqua_userA, $eloqua_pass, 'https://secure.eloqua.com/API/rest/2.0');
  $emailCreate = $client->post("/assets/email", $email);
  $emailId = $emailCreate->id;

  class emailSend {
    public $name;
    public $id;
  }

  class Deployment {
    public $contactId;
    public $email;
    public $type;
  }

  $emailSend = new emailSend;
  $emailSend->name = "Export Download Email Send";
  $emailSend->id = $emailId;

  $deployment = new Deployment();
  $deployment->contactId = 300;
  $deployment->email = $emailSend;
  $deployment->name = "REST API Deploy";
  $deployment->type = "EmailTestDeployment";
  $response = $client->post('/assets/email/deployment', $deployment);

?>
