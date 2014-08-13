<?php

  $segment = $_GET["asset"];
  print "Segment id: " . $segment . "<br>";
  print "Preparing your data... you might be sent an email when it's ready.";

  include_once ("/var/www/localhost/files/eloqua.inc");
  include_once ("eloquaRequest.php");

  $login = new EloquaRequest($eloqua_site, $eloqua_userA, $eloqua_pass, "https://login.eloqua.com/id");
  $endPointBase = $login->get("");
  $endPointURL = $endPointBase->urls->base . "/API/bulk/2.0";
  $restEndPointURL = $endPointBase->urls->base . "/API/rest/1.0";

  $restClient = new EloquaRequest($eloqua_site, $eloqua_userA, $eloqua_pass, $restEndPointURL);
  $response = $restClient->get('/assets/contact/views');

  print "<br><br>";
  print "<html><head></head><body>";
  print "<form name='viewselect' action='./export.php' method='post'>";
  print "<input type='hidden' name='segment' value='$segment'>";
  print "<select name='view'>";
  foreach ($response->elements as $view) {
    print "<option value='" . $view->id . "'>" . $view->name . "</option>";
  }
  print "</select>";
  print "<input type='submit' value='Export'>";
  print "</form>";
  print "</body></html>";

exit;

  $dictionary = (object) array('C_EmailAddress' => '{{Contact.Field(C_EmailAddress)}}',
			       'C_FirstName' => '{{Contact.Field(C_FirstName)}}',
			       'C_LastName' => '{{Contact.Field(C_LastName)}}',
			       'C_Company' => '{{Contact.Field(C_Company)}}',
			       'C_Address1' => '{{Contact.Field(C_Address1)}}',
			       'C_Address2' => '{{Contact.Field(C_Address2)}}',
			       'C_City' => '{{Contact.Field(C_City)}}',
			       'C_State_Prov' => '{{Contact.Field(C_State_Prov)}}',
			       'C_Zip_Postal' => '{{Contact.Field(C_Zip_Postal)}}',
			       'C_Birthdate1' => '{{Contact.Field(C_Birthdate1)}}',
			       'M_CompanyName' => '{{Contact.Account.Field(M_CompanyName)}}');

  $export = (object) array('name' => 'All Contacts',
			   'dataRetentionDuration' => 'PT1H',
			   'filter' => "EXISTS('{{ContactSegment[" . $segment . "]}}')",
			   'fields' => $dictionary);

  $client = new EloquaRequest($eloqua_site, $eloqua_userA, $eloqua_pass, $endPointURL);
  $response = $client->post('/contacts/exports', $export);
  $exportUri = $response->uri;

  $callbackUri = "https://mungkey.org/eloqua/menu/download.php?eu=" . urlencode($response->uri);

  $sync = (object) array('callbackUrl' => $callbackUri,
                         'syncedInstanceUri' => $response->uri);

  $syncSend = $client->POST('/syncs', $sync);
  $syncUri = $syncSend->uri;

?>
