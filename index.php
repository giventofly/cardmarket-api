  $card = $_GET['card'];
  $var = urlencode($card);
    //min 3 chars
   $method             = "GET";
   $url                = "https://api.cardmarket.com/ws/v2.0/output.json/stock/articles/$var/1"; //example for search stock
   $appToken           = "CHANGE THIS";
   $appSecret          = "CHANGE THIS";
   $accessToken        = "CHANGE THIS";
   $accessSecret       = "CHANGE THIS";
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
       'oauth_version'             => $version
   );

  $baseString   = strtoupper($method) . "&";
  $baseString  .= rawurlencode($url) . "&";

   $encodedParams      = array();
   foreach ($params as $key => $value) { if ("realm" != $key) { $encodedParams[rawurlencode($key)] = rawurlencode($value); } }
   ksort($encodedParams);
   $values  = array();
   foreach ($encodedParams as $key => $value) { $values[] = $key . "=" . $value; }
   $paramsString = rawurlencode(implode("&", $values));
  $baseString .= $paramsString;

   $signatureKey       = rawurlencode($appSecret) . "&" . rawurlencode($accessSecret);
   $rawSignature  = hash_hmac("sha1", $baseString, $signatureKey, true);
   $oAuthSignature  = base64_encode($rawSignature);
   $params['oauth_signature'] = $oAuthSignature;

   $header = "Authorization: OAuth ";
   $headerParams = array();
   foreach ($params as $key => $value) { $headerParams[] = $key . "=\"" . $value . "\""; }
   $header .= implode(", ", $headerParams);


   $ch  = curl_init();
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

 $content =  curl_exec($ch);


curl_close($ch);

$decoded  = json_decode($content, true);

//var_dump($decoded);
  echo "Stock matching results for '$card':<br>";
    foreach ($decoded as $key) {
      for($i = 0; $i < sizeof($key); $i++) {
      $name = $key[$i]['product']['enName'];
      $rarity = $key[$i]['product']['rarity'];
      $condition = $key[$i]['condition'];
      if(empty($key[$i]['isFoil'])) { $foil = ""; } else { $foil = ", FOIL"; } ;
      if(empty($key[$i]["isSigned"])) { $signed = ""; } else { $signed = ", SIGNED"; } ;
      if(empty($key[$i]["isPlayset"])) { $playset = ""; } else { $playset = ", PLAYSET"; } ;
      if(empty($key[$i]["isAltered"])) { $altered = ""; } else { $altered = ", ALTERED"; } ;
      $price = $key[$i]['price'];
      $quantity = $key[$i]['count'];
      //$cardimage = $key[$i]['image'];
      $expantion = $key[$i]["expansion"];
      $language = $key[$i]["language"]['languageName'];
      echo $quantity."x<b>$name</b> - $rarity [$language] - $condition @ ".$price."€$foil $signed $playset $altered<br>";
      }

    }
