<?php

  $segment = $_POST["segment"];
  $view = $_POST["view"];
  print "Segment id: " . $segment . "<br>";
  print "View id: " . $view . "<br>";
  print "Preparing your data... you might be sent an email when it's ready.  You can close this window.";

  include_once ("/var/www/localhost/files/eloqua.inc");
  include_once ("eloquaRequest.php");

  $login = new EloquaRequest($eloqua_site, $eloqua_userA, $eloqua_pass, "https://login.eloqua.com/id");
  $endPointBase = $login->get("");
  $endPointURL = $endPointBase->urls->base . "/API/bulk/2.0";
  $restEndPointURL = $endPointBase->urls->base . "/API/rest/1.0";

  $restClient = new EloquaRequest($eloqua_site, $eloqua_userA, $eloqua_pass, $restEndPointURL);
  $viewDetails = $restClient->get('/assets/contact/view/' . $view);
  $client = new EloquaRequest($eloqua_site, $eloqua_userA, $eloqua_pass, $endPointURL);
  $contactFields = $client->get('/contacts/fields');

  $i = 0;
  foreach ($viewDetails->fields as $field) {
    foreach ($contactFields->items as $cField) {
      if ($cField->uri == "/contacts/fields/" . $field->id ) {
#        print $cField->name . " -> " . $cField->statement . "<br>";
        $name = str_replace(" ", "", $cField->name);
	$name = str_replace("(", "", $name);
	$name = str_replace(")", "", $name);
	$name = str_replace("{", "", $name);
	$name = str_replace("}", "", $name);
	$name = str_replace("[", "", $name);
	$name = str_replace("]", "", $name);
	$name = str_replace("-", "", $name);
	$dictionary[$name] = $cField->statement;
	$i++;
      }
    }
  }

#  print json_encode($dictionary);

  $export = (object) array('name' => 'All Contacts',
			   'dataRetentionDuration' => 'PT1H',
			   'filter' => "EXISTS('{{ContactSegment[" . $segment . "]}}')",
			   'fields' => $dictionary);

#  print "<br><br>Export:<br>";
#  print json_encode($export);
  $client = new EloquaRequest($eloqua_site, $eloqua_userA, $eloqua_pass, $endPointURL);
  $response = $client->post('/contacts/exports', $export);
  $exportUri = $response->uri;

  $callbackUri = "https://mungkey.org/eloqua/menu/download.php?eu=" . urlencode($response->uri);

  $sync = (object) array('callbackUrl' => $callbackUri,
                         'syncedInstanceUri' => $response->uri);

  $syncSend = $client->POST('/syncs', $sync);
  $syncUri = $syncSend->uri;

?>
