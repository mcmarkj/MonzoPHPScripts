<?php
error_reporting(E_ALL);
session_start();
$arrayfile = __DIR__ .'/array.json';
require __DIR__ . '/config.php';
ini_set('display_errors', 1);
require_once __DIR__ . '/vendor/autoload.php';


//GET OATH//
$provider = new Edcs\OAuth2\Client\Provider\Mondo([
  'clientId'     => $clientid,
  'clientSecret' => $clientsecret,
  'redirectUri'  => $redicurl,
]);
$arr2 = json_decode(file_get_contents($arrayfile), true);

$token = new \League\OAuth2\Client\Token\AccessToken($arr2);

$existingAccessToken = $token;

if ($existingAccessToken->hasExpired()) {
    echo "TOKEN EXPIRED.";
    $newAccessToken = $provider->getAccessToken('refresh_token', [
        'refresh_token' => $existingAccessToken->getRefreshToken()
    ]);

    wh_log("-----NEW TOKEN GENERATED------");

    file_put_contents($arrayfile,json_encode($newAccessToken));

    // Purge old access token and store new access token to your data store.
} else {



$resourceOwner = $provider->getResourceOwner($token);


$reqsrc = $_SERVER['HTTP_USER_AGENT'];

if($reqsrc==""){
  $reqsrc = "CRON SCRIPT";
}
$tsamp = $arr2["expires"];
//echo date("d-m-Y @ H:i", $tsamp);
wh_log("--NEW REQUEST--");
wh_log("Token file stored at: ". $arrayfile);
wh_log("Accessed by: ". $reqsrc);
wh_log("Token will expire: " . date("d-m-Y @ H:i", $tsamp));


//$token = "eyJhbGciOiJFUzI1NiIsInR5cCI6IkpXVCJ9.eyJlYiI6IjJOblI3cG9IMGhBM21KUEhoNndSIiwianRpIjoiYWNjdG9rXzAwMDA5WnlhNXF1Vk96YmlSM0VjZE4iLCJ0eXAiOiJhdCIsInYiOiI1In0.q670IR5S9nkYoFicrc-ECaIR9Yhw-wdFpIb0yMhzgmYipvc0_qVb2CHI3SBbb4uFxCI57Nv2Ahi-Qrs9vbs2DQ";


// Read the current balance //
function rudr_mailchimp_curl_connect( $url, $request_type, $token) {

    $mch = curl_init();



    curl_setopt($mch, CURLOPT_URL, $url );
    curl_setopt($mch, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
    curl_setopt($mch, CURLOPT_RETURNTRANSFER, true); // do not echo the result, write it into variable
    curl_setopt($mch, CURLOPT_CUSTOMREQUEST, $request_type); // according to MailChimp API: POST/GET/PATCH/PUT/DELETE
    if( $request_type != 'GET' ) {
        curl_setopt($mch, CURLOPT_PUT, true);

    	curl_setopt($mch, CURLOPT_HTTPHEADER, array(
			'Authorization: Bearer ' . $token, ));

} else {
	curl_setopt($mch, CURLOPT_HTTPHEADER, array(
		'Authorization: Bearer ' . $token, ));
}
	curl_setopt($mch, CURLOPT_TIMEOUT, 10);
	curl_setopt($mch, CURLOPT_SSL_VERIFYPEER, false); // certificate verification for TLS/SSL connection


	return curl_exec($mch);


}


$url = 'https://api.monzo.com/balance?account_id='.$accountid;
$headers = array(
	'Authorization: Bearer ' . $token
);
$result = json_decode( rudr_mailchimp_curl_connect( $url, 'GET', $token) );

//wh_log("Full _REQUEST dump:\n".print_r($result,true));


// End balance read //

$balance = $result->balance;
wh_log("Current Balance: £" . $balance/100);


//echo $balance;
$hundreds = substr($balance, -3);
$pence = substr($balance, -2);


function floor2nearest($number, $decimal) {
    return floor($number / $decimal) * $decimal;
}

$subtract = floor2nearest($hundreds, 500);

$totalsub = $hundreds - $subtract;


//echo $pence;





//if balance doesn't end .00 - transfer money//
if($totalsub!=0){
if($balance>1000){
wh_log("Transferring: £" . $totalsub/100);
// perform transfer //
$dedupe = date("ignz");
//echo $dedupe;

$url = 'https://api.monzo.com/pots/'.$potid.'/deposit';
$data = array(
  "source_account_id" => $accountid,
  "amount" => $totalsub,
  "dedupe_id" => $dedupe
);

$data_string = http_build_query($data, '', '&');

$ch = curl_init($url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch,CURLOPT_CUSTOMREQUEST, 'PUT');
curl_setopt($ch,CURLOPT_HTTPHEADER, array(
  'Content-Type: application/x-www-form-urlencoded',
    'Authorization: Bearer ' . $token));
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

$result = curl_exec($ch);
//echo $result;
//wh_log($result);
curl_close($ch);



//end transfer//

// end if
}
}
}
function wh_log($msg){
    $logfile = __DIR__ . '/balancelogs.txt';
    file_put_contents($logfile,date("Y-m-d H:i:s")." | ".$msg."\n",FILE_APPEND);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Monzo Tool</title>
<style>
table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
}
th, td {
    padding: 5px;
    text-align: left;
}
</style>
</head>
<body>

<h2>Monzo Balance Script</h2>
<h3>Expires: <?php echo date("d-m-Y @ H:i", $tsamp); ?></h3>

<table style="width:50%">

  <tr>
    <th>Account</th>
    <th>Value</th>
  </tr>
  <tr>
    <td>Current Balance</td>
    <td>£<?php echo $balance/100; ?></td>
  </tr>
  <tr>
    <td>Being Subtracted</td>
    <td>£<?php echo $totalsub/100; ?></td>
  </tr>
  <tr>
    <td>New Balance</td>
    <td>£<?php echo ($balance - $totalsub)/100; ?></td>
  </tr>
</table>

</body>
</html>
