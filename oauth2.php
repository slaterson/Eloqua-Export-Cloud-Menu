<?php

  include_once ("/var/www/localhost/files/eloqua.inc");
  include_once ("/var/www/localhost/files/default.inc");
#  include_once ("eloquaRequest.php");

  class grantTokens {
    public $grant_type;
    public $code;
    public $redirect_uri;
  }

  $code = $_GET['code'];
#  $instance = $_GET['instance'];
#  $siteId = $_GET['siteId'];
#  print "code: " . $code . "<br><br>";
  $grantTokens = new grantTokens;
  $grantTokens->grant_type = "authorization_code";
  $grantTokens->redirect_uri = "https://mungkey.org/eloqua/sfdcAction/oauth2.php";
  $grantTokens->code = $code;
#  print_r ($grantTokens);
#  print "<br><br>";
  $clientId = "7400c9f8-8ce5-4654-b071-5c87c8bc98e1";
  $clientSecret = "10BIFzv-mhEej4JtK8UA5X5t9KzrKgF4opqxlDpz69Qy6XhGiDi8s9Nq02dBBgWR3KRTMR0VyG~-0SxHDeQwjjxlQpa0htBJHQtA";
  $url = "https://login.eloqua.com/auth/oauth2/token";
  $request = curl_init();
  curl_setopt($request, CURLOPT_URL, $url);
  curl_setopt($request, CURLOPT_USERPWD, $clientId . ":" . $clientSecret);
  $headers = array('Content-type: application/json');
  curl_setopt($request, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($request, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($request, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($request, CURLINFO_HEADER_OUT, true);
  curl_setopt($request, CURLOPT_VERBOSE, true);
  curl_setopt($request, CURLOPT_HEADER, true);
  curl_setopt($request, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($request, CURLOPT_CUSTOMREQUEST, 'POST');
  curl_setopt($request, CURLOPT_POSTFIELDS, json_encode($grantTokens));
  $response = curl_exec($request);
  $responseInfo = curl_getinfo($request, CURLINFO_HEADER_OUT);
  $header_size = curl_getinfo($request, CURLINFO_HEADER_SIZE);
  $header = substr($response, 0, $header_size);
  $body = substr($response, $header_size);
  $tokens = json_decode($body);

#  $link = mysql_connect($db, $db_user, $db_pass) or die("Could not connect : " . mysql_error());
#  mysql_select_db("eloquaOauth") or die("Could not select database");

  $timestamp = time();
#  $tokensQuery = "insert into oauth value ('', '', '" . $clientId . "', '" . $tokens->access_token  . "', '" . $tokens->token_type . "', '" . $tokens->refresh_token . "', '" . $timestamp . "', '" . $tokens->expires_in . "')";
#  $result = mysql_query($tokensQuery) or die("Install failed : " . mysql_error());

  $url2 = "https://login.eloqua.com/id";
  $request2 = curl_init();
  curl_setopt($request2, CURLOPT_URL, $url2);
  curl_setopt($request2, CURLOPT_USERPWD, $clientId . ":" . $clientSecret);
  $headers = array('Content-type: application/json', 'Authorization: Bearer ' . $tokens->access_token);
  curl_setopt($request2, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($request2, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($request2, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($request2, CURLINFO_HEADER_OUT, true);
  curl_setopt($request2, CURLOPT_VERBOSE, true);
  curl_setopt($request2, CURLOPT_HEADER, true);
  curl_setopt($request2, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($request2, CURLOPT_CUSTOMREQUEST, 'GET');
#  curl_setopt($request2, CURLOPT_POSTFIELDS, json_encode($grantTokens));
  $response2 = curl_exec($request2);
  $responseInfo2 = curl_getinfo($request2, CURLINFO_HEADER_OUT);
  $header_size2 = curl_getinfo($request2, CURLINFO_HEADER_SIZE);
  $header2 = substr($response2, 0, $header_size2);
  $body2 = substr($response2, $header_size2);
  $site = json_decode($body2);
#  print_r ($site);
#  print "<br><br>";

  $mysqli = new mysqli($db, $db_user, $db_pass, "eloquaOauth");
#  $link = mysql_connect($db, $db_user, $db_pass) or die("Could not connect : " . mysql_error());
#  mysql_select_db("eloquaOauth") or die("Could not select database");

#  $timestamp = time();
  $tokensQuery = "insert into oauth value ('', '" . $site->site->id . "', '" . $site->urls->base . "', '', '" . $clientId . "', '" . $tokens->access_token  . "', '" . $tokens->token_type . "', '" . $tokens->refresh_token . "', '" . $timestamp . "', '" . $tokens->expires_in . "')" or die ("Error in insert: " . mysqli_error($mysqli));
  $result = $mysqli->query($tokensQuery);

?>
