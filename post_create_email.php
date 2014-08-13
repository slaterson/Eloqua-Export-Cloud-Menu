<?php

  include_once ("/var/www/localhost/files/eloqua.inc");
  include_once ("eloquaRequest.php");

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
  $htmlContent->html = "<html><head></head><body>Email created by API</body></html>";

  $email = new email();
  $email->name = "Email from API";
  $email->emailFooterId = 1;
  $email->emailHeaderId = 1;
  $email->encodingId = 1;
  $email->emailGroupId = 1;
  $email->subject = "Subject from API";
  $email->htmlContent = $htmlContent;

  print "Email object:<br>";
  print_r ($email);
  print "<br><br>";

  $client = new EloquaRequest($eloqua_site, $eloqua_userA, $eloqua_pass, 'https://secure.eloqua.com/API/rest/2.0');
  $emailCreate = $client->post("/assets/email", $email);
  print "Email Create response:<br>";
  print_r ($emailCreate);
  $emailId = $emailCreate->id;
  print "<br><br>";
  print "Email id: " . $emailId . "<br>";
  
?>
