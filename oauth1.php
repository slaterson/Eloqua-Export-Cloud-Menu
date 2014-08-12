<?php

  $instance = $_GET["instance"];
  $responseType = urlencode("code");
  $clientId = urlencode("7400c9f8-8ce5-4654-b071-5c87c8bc98e1");
  $redirectUri = urlencode("https://mungkey.org/eloqua/sfdcAction/oauth2.php");
  $scope = urlencode("full");
  $state = urlencode("");

  $queryGet = "response_type=" . $responseType . "&client_id=" . $clientId . "&redirect_uri=" . $redirectUri . "&scope=" . $scope . "&state=" . $state;
  $url = "https://login.eloqua.com/auth/oauth2/authorize?" . $queryGet;
  header("Location: " . $url);
  exit;

?>
