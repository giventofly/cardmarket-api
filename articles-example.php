
<?php


function mcm(){

   $method             = "GET";
   //don't include here the query params
   $url = 'https://api.cardmarket.com/ws/v2.0/output.json/users/karmacrow/articles';
   $appToken           = "CHANGE HERE";
   $appSecret          = "CHANGE HERE";
   $accessToken        = "CHANGE HERE";
   $accessSecret       = "CHANGE HERE";
   $nonce              = uniqid();
   $timestamp          = time();
   $signatureMethod    = "HMAC-SHA1";
   $version            = "1.0";

   $params             = array(
       'realm'                     => $url,
       'oauth_consumer_key'        => $appToken,
       'oauth_token'               => $accessToken,
       'oauth_nonce'               => $nonce,
       'oauth_timestamp'           => $timestamp,
       'oauth_signature_method'    => $signatureMethod,
       'oauth_version'             => $version,
       'maxResults' => '2',
       'start' => '0'
   );

    //encode without params!
    $baseString   = strtoupper($method) . "&" . rawurlencode($url) . "&";
   $encodedParams      = array();
   foreach ($params as $key => $value) { if ("realm" != $key) { $encodedParams[rawurlencode($key)] = rawurlencode($value); } }
   ksort($encodedParams);
   $values  = array();
   foreach ($encodedParams as $key => $value) { $values[] = $key . "=" . $value; }
   $paramsString = rawurlencode(implode("&", $values));
   $baseString .= $paramsString;
   $signatureKey = rawurlencode($appSecret) . "&" . rawurlencode($accessSecret);
   $rawSignature  = hash_hmac("sha1", $baseString, $signatureKey, true);
   $oAuthSignature  = base64_encode($rawSignature);
   $params['oauth_signature'] = $oAuthSignature;

   $header = "Authorization: OAuth ";
   $headerParams = array();
   foreach ($params as $key => $value) {
     $headerParams[] = $key . "=\"" . $value . "\"";
    }
   $header .= implode(", ", $headerParams);
   //curl call
   $ch  = curl_init();
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   //attach the params for the GET request
   curl_setopt($ch, CURLOPT_URL, $url.'?start=0&maxResults=2');
   curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 
  // Execute
  $content =  curl_exec($ch);
  //$content            = curl_exec($curlHandle);
   $info               = curl_getinfo($ch);
  curl_close($ch);

$decoded  = json_decode($content, true);
//see the Json results
echo "<pre>".print_r($decoded,true)."</pre>";

}

mcm();
