<?php

  include_once ("/var/www/localhost/files/eloqua.inc");
  include_once ("eloquaRequest.php");

  $segment = $argv[1];
#  print "Segment id: " . $segment . "<br><br>";
  $login = new EloquaRequest($eloqua_site, $eloqua_userA, $eloqua_pass, "https://login.eloqua.com/id");
  $endPointBase = $login->get("");
  $endPointURL = $endPointBase->urls->base . "/API/bulk/2.0";
#  print "$endPointURL<br>";

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

#  print "Export:<br>";
#  print_r ($export);
#  print "<br>";
  $client = new EloquaRequest($eloqua_site, $eloqua_userA, $eloqua_pass, $endPointURL);
  $response = $client->post('/contacts/exports', $export);
  $exportUri = $response->uri;
#  print "Export result:<br>";
#  print_r ($response);
#  print "<br><br>";

  $callbackUri = "https://mungkey.org/eloqua/menu/download.php?eu=" . urlencode($response->uri);

  $sync = (object) array(#'status' => 'SyncStatusType.pending',
			 'callbackUrl' => $callbackUri,
                         'syncedInstanceUri' => $response->uri);

#  print "Sync:<br>";
#  print_r ($sync);
#  print "<br><br>";
  $syncSend = $client->POST('/syncs', $sync);
  $syncUri = $syncSend->uri;
#  print "Sync POST results:<br>";
#  print_r ($syncSend);
#  print "<br><br>";

  for ($i = 0; $i < 10; $i++) {
    $syncStatus = $client->GET($syncUri);
    print $syncStatus->status . "\n";
    if ($syncStatus->status == "pending" or $syncStatus->status == "active" ) {
      sleep(1);
    } else {
      break;
    }
  }
#  print "Sync status:<br>";
#  print_r ($syncStatus);
#  print "<br><br>";

  $get_contacts = $client->get($exportUri . "/data");
  $export = exec('echo "' . json_encode($get_contacts->items) . '" > /var/www/localhost/htdocs/eloqua/menu/contacts.json');
#  print $response->uri . "<br>";

?>
