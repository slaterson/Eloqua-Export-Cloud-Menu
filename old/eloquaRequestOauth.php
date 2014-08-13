<?php

/**
 * REST client for Eloqua's API.
 */
class EloquaRequestOauth
{
    private $ch;
    public $baseUrl;
    public $responseInfo;

	public function __construct($clientId, $clientSecret, $baseUrl, $accessToken)
	{
		// basic authentication credentials
		$credentials =$clientId . ':' . $clientSecret;

		// set the base URL for the API endpoint
		$this->baseUrl = $baseUrl;		

		// initialize the cURL resource
		$this->ch = curl_init();

		// set cURL and credential options
		curl_setopt($this->ch, CURLOPT_URL, $this->baseUrl);
		curl_setopt($this->ch, CURLOPT_USERPWD, $credentials); 

		// set headers
		$headers = array('Content-type: application/json', 'Authorization: Bearer ' . $accessToken);
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($this->ch, CURLOPT_CAINFO, "/etc/ssl/certs/CAcert_Class_3_Root_CA.pem");
#		curl_setopt($this->ch, CURLOPT_CAINFO, "/usr/share/ca-certificates/cacert.pem");
		curl_setopt($this->ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($this->ch, CURLOPT_VERBOSE, true);
		curl_setopt($this->ch, CURLOPT_HEADER, true);

		// return transfer as string
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
	}

	public function __destruct()
	{
		curl_close($this->ch);
	}

	public function get($url)
	{
		return $this->executeRequest($url, 'GET');
	}

	public function post($url, $data)
	{
		return $this->executeRequest($url, 'POST', $data);
	}

	public function put($url, $data)
	{
		return $this->executeRequest($url, 'PUT', $data);
	}

	public function delete($url)
	{
		return $this->executeRequest($url, 'DELETE');	
	}
	
	public function executeRequest($url, $method, $data=null)
	{
		// set the full URL for the request
		curl_setopt($this->ch, CURLOPT_URL, $this->baseUrl . '/' . $url);

		switch ($method) {
			case 'GET':
				curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'GET');
				break;
			case 'POST':
				curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'POST');
				curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($data));
				break;
			case 'PUT':
				curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PUT');
				curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($data));
				break;
			case 'DELETE':
				curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
				break;
			default:
				break;
		}

        // execute the request
        $response = curl_exec($this->ch);

        // store the response info including the HTTP status
        // 400 and 500 status codes indicate an error
        $this->responseInfo = curl_getinfo($this->ch);

	$headers = curl_getinfo($this->ch, CURLINFO_HEADER_OUT);

	$header_size = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE);
	$header = substr($response, 0, $header_size);
	$body = substr($response, $header_size);

        $httpCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);

        if ($httpCode > 400) 
        {  
          print $httpCode . "<br>";
          print_r($this->responseInfo);
	  print "<br>";

        }
        
        // todo : add support in constructor for contentType {xml, json}	
        return json_decode($body);
	}
}
?>
