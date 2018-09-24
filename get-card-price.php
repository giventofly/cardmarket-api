<?php

//user info
$appToken           = "CHANGE THIS";
$appSecret          = "CHANGE THIS";
$accessToken        = "CHANGE THIS";
$accessSecret       = "CHANGE THIS";

//get card id Or get card price
/*
use as getMCMinfo('tarmogoyf','Future Sight') to get product id, return array('id' => id, 'http' => 'http response status')
or getMCMinfo(null,null,1452) to get product info array('id' => id, 'status' => 'http response status', 'url' => 'url', 'price' => 'price')
*/
function getMCMinfo($cardname=null,$edition=null,$productID=null){
  //vars
  global $myappToken;
  global $myappSecret;
  global $myaccessToken;
  global $myaccessSecret;
  $searchparams = '';
  //check wich one is to do
  //############### search for cards with name and edition
  if(isset($cardname)) {
    $card = rawurlencode($cardname);
    //exact true, idgame => magic, idlangue = EN
    $searchparams = "?search=$card&exact=true&idGame=1&idLanguage=1";
    $url = 'https://api.cardmarket.com/ws/v2.0/output.json/products/find';
  }
  //################# get info with card id
  else {
    $url = "https://api.cardmarket.com/ws/v2.0/output.json/products/$productID";
  }
  //params
  $method = "GET";
  $appToken  = $myappToken;
  $appSecret  = $myappSecret;
  $accessToken  = $myaccessToken;
  $accessSecret = $myaccessSecret;
  $nonce  = uniqid();
  $timestamp  = time();
  $signatureMethod  = "HMAC-SHA1";
  $version  = "1.0";

  $params = array(
    'realm'                     => $url,
    'oauth_consumer_key'        => $appToken,
    'oauth_token'               => $accessToken,
    'oauth_nonce'               => $nonce,
    'oauth_timestamp'           => $timestamp,
    'oauth_signature_method'    => $signatureMethod,
    'oauth_version'             => $version,
  );

  //attach params only if they are needed
  if(isset($cardname)) {
    $params['search'] =  $cardname;
    $params['exact'] = 'true';
    $params['idGame'] = '1';
    $params['idLanguage'] = '1';
  }

  $baseString   = strtoupper($method) . "&" . rawurlencode($url) . "&";
  //encode params alpha
  $encodedParams      = array();
  foreach ($params as $key => $value) { if ("realm" != $key) { $encodedParams[rawurlencode($key)] = rawurlencode($value); } }
  ksort($encodedParams);
  //add params to URL
  $values  = array();
  foreach ($encodedParams as $key => $value) { $values[] = $key . "=" . $value; }
  $paramsString = rawurlencode(implode("&", $values));
  $baseString .= $paramsString;
  //encode to sigkey
  $signatureKey = rawurlencode($appSecret) . "&" . rawurlencode($accessSecret);
  //widget app doesn't attach the access Secret, uncomment to use it
  //$signatureKey = rawurlencode($appSecret) . "&";
  $rawSignature  = hash_hmac("sha1", $baseString, $signatureKey, true);
  $oAuthSignature  = base64_encode($rawSignature);
  $params['oauth_signature'] = $oAuthSignature;
  //header
  $header = "Authorization: OAuth ";
  $headerParams = array();
  foreach ($params as $key => $value) {
  $headerParams[] = $key . "=\"" . $value . "\"";
  }
  $header .= implode(", ", $headerParams);

  //don't forget to attach params to GET url
  $ch  = curl_init();
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, $url.$searchparams);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

  // Execute
  $content =  curl_exec($ch);
  //$content            = curl_exec($curlHandle);
  $info               = curl_getinfo($ch);
  curl_close($ch);
  //Error check if you need to debug or use for any purpose
  //if($info['http_code'] != '200') { echo "error: " . $info['http_code'] . "<br>"; }
  $decoded  = json_decode($content, true);
  //array for results
  $result = array();
  //################## search for card according to edition
  if(isset($cardname)) {
    $achei = false;
    //missed the card for some reason (wrong name or edition? do your checks)
    if(!isset($decoded['product'])) { echo "Missing card search for $cardname<br>\n"; die(); }
    foreach ($decoded['product'] as $value) {
      $thisexp = $value['expansionName'];
      //this kind of depends on how you pass the edition i remove the ' (example urza's sage)
      if(!$achei && strtolower(str_replace("'","",$edition)) == strtolower(str_replace("'","",$thisexp))){
        $result['id'] = $value['idProduct'];
        $achei = true;
      }
    }
    if(!$achei) {
      echo "Missing search for $cardname with edition $edition<br>";
      // Want to check the editions returned? a print_r could be a better option
      // foreach ($decoded['product'] as $value) {
      //   echo "Editions returned: " . $value['expansionName'] . "<br>\n";
      // }

    }
  }
  //####################### get info by productid
  else {
    if(!isset($decoded['product'])) {
      echo "missed search for " . $product['idProduct'] . " <br>";
    }
    $result = $decoded['product'];
    $result['priceinfo'] = $product['priceGuide'];
    //check for whatever info you want :)

  }

  //returns card id or array with price info
  return $result;
}


