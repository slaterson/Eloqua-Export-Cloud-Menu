<?php

  include ("/var/www/localhost/files/default.inc");

#  $install = $_GET["install"];
#  $instance = $_GET["instance"];
  $asset = $_GET["asset"];
  $eventType = $_GET["eventType"];
  $site = $_GET["site"];
#  $oauth_signature_method = $_GET["oauth_signature_method"];
#  $oauth_timestamp = $_GET["oauth_timestamp"];
#  $oauth_version = $_GET["oauth_version"];
#  $oauth_signature = $_GET["oauth_signature"];
#  $install = $_GET["install"];
  $timestamp = time();

  $mysqli = new mysqli($db, $db_user, $db_pass, "firehose");

#  $link = mysql_connect($db, $db_user, $db_pass) or die("Could not connect : " . mysql_error());
#  mysql_select_db("feeder") or die("Could not select database");

  $enableQuery = "insert into firehose value ('', '" . $site . "', '', '', '" . $eventType . "', '" . $timestamp . "', '" . $asset . "')" or die ("Error in insert: " . mysqli_error($mysqli));

  $result = $mysqli->query($enableQuery);
#  http_response_code(204);

?>
