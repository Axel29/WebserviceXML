<?php
$urlSearch = 'http://apixml.josealbea.com/user';
		

$public_key = hash_hmac("sha256", '1' . 'admin-api@yopmail.fr' . time() . '557afa6e50b9a', '57b59e43b10aa25');

$headers    = [
	'PUB: ' . $public_key,
	'USEREMAIL: admin-api@yopmail.fr',
	'APIKEY: 557afa6e50b9a',
];

$userAgent  = "Mozilla/5.0";
$referer    = 'http://apixml.josealbea.com/username';

// $postFields = [
// 	'email'    => 'user-api@yopmail.fr',
// 	'username' => 'user',
// 	'password' => 'azerty',
// 	'role'     => '2',
// ];
// $httpQuery  = http_build_query($postFields);

$curlSearch = curl_init($urlSearch);
// curl_setopt($curlSearch, CURLOPT_POST, true);
// curl_setopt($curlSearch, CURLOPT_POSTFIELDS, $httpQuery);
curl_setopt($curlSearch, CURLOPT_HTTPGET, true);
curl_setopt($curlSearch, CURLOPT_FAILONERROR, 1);
curl_setopt($curlSearch, CURLOPT_USERAGENT, $userAgent);
curl_setopt($curlSearch, CURLOPT_REFERER, $referer);
curl_setopt($curlSearch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curlSearch, CURLOPT_RETURNTRANSFER, 1);
$postXML = curl_exec($curlSearch);
header("Content-type: text/xml; charset=utf-8");
echo($postXML);
curl_close($curlSearch);