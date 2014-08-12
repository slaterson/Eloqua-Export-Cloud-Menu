<?php

  include ("/var/www/localhost/files/default.inc");

  $oauth_consumerkey = $_GET["oauth_consumerkey"];
  $oauth_nonce = $_GET["oauth_nonce"];
  $oauth_signature_method = $_GET["oauth_signature_method"];
  $oauth_timestamp = $_GET["oauth_timestamp"];
  $oauth_version = $_GET["oauth_version"];
  $oauth_signature = $_GET["oauth_signature"];
  $install = $_GET["install"];

  $mysqli = new mysqli($db, $db_user, $db_pass, "menu");
#  $link = mysql_connect($db, $db_user, $db_pass) or die("Could not connect : " . mysql_error());
#  mysql_select_db("action1") or die("Could not select database");

  $enableQuery = "insert into menu value ('', '" . $oauth_consumerkey . "', '" . $oauth_nonce . "', '" . $oauth_signature_method . "', '" . $oauth_timestamp . "', '" . $oauth_version . "', '" . $oauth_signature . "', '" . $install . "')" or die("Error in insert: " . mysqli_error($mysqli));
#  print $enableQuery;
  $result = $mysqli->query($enableQuery);

?>
