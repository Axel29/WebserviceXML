<?php
$urlSearch = 'http://ws-xml.localhost.com/role';
		
$postFields = [
	'role' => 'Test curl',
];

$public_key = hash_hmac("sha256", '1' . 'axel.bouaziz@hotmail.fr' . time() . 'jfdljkhqepiyezh3893IYHnds', 'fdjfsdhfsdjfkn');

$headers    = [
	'PUB: ' . $public_key,
	'USEREMAIL: axel.bouaziz@hotmail.fr',
	'APIKEY: fdjfsdhfsdjfkn',
];

$dataSearch = http_build_query($postFields);

$userAgent  = "Mozilla/5.0";
$referer    = 'http://ws-xml.localhost.com/role';
 
$curlSearch = curl_init($urlSearch);
curl_setopt($curlSearch, CURLOPT_HTTPGET, true);
curl_setopt($curlSearch, CURLOPT_FAILONERROR, 1);
curl_setopt($curlSearch, CURLOPT_USERAGENT, $userAgent);
curl_setopt($curlSearch, CURLOPT_REFERER, $referer);
curl_setopt($curlSearch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curlSearch, CURLOPT_RETURNTRANSFER, 1);

$postXML = curl_exec($curlSearch);
echo($postXML);
curl_close($curlSearch);