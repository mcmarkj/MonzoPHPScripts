<!DOCTYPE html>
<html>
<head>
    <title>Monzo Tool's</title>
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

<h2>Monzo Expenses Script</h2>



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
wh_log("--RUNNING SCHEDULED TRANSFERS--");
wh_log("Token file stored at: ". $arrayfile);
wh_log("Accessed by: ". $reqsrc);
wh_log("Token will expire: " . date("d-m-Y @ H:i", $tsamp));
?>

<h3>Expires: <?php echo date("d-m-Y @ H:i", $tsamp); ?></h3>

<table style="width:50%">

  <tr>
    <th>Amount</th>
    <th>Date</th>
    <th>Output</th>
  </tr>

  <?php

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

//echo $pence;


$con=new mysqli($dbhost,$dbuser,$dbpass,$database);
if($con->connect_error){
    echo 'Connection Faild: '.$con->connect_error;
    }else{

      //SELECT * FROM `SMSQueue` WHERE `STATUS` = 0 && `SENDAFTER` < CURRENT_TIMESTAMP ORDER BY `SMSID` ASC
        $sql="SELECT * FROM `schedule` WHERE `STATUS` <> '2' && `DATE` < CURRENT_TIMESTAMP";

        $res=$con->query($sql);








        while($row=$res->fetch_assoc()){



          $ID = $row['ID'];
                    $amount = $row['AMOUNT'];
                              $date = $row['DATE'];
                                        $POT = $row['POTID'];
                                        echo "<tr>";
                                        echo "<td>".$amount."</td>";
                                        echo "<td>".$date."</td>";
                                        echo "<td>";





//if balance doesn't end .00 - transfer money//
wh_log("Transferring: £" . $amount/100);
// perform transfer //
$dedupe = date("ignz");
//echo $dedupe;

$url = 'https://api.monzo.com/pots/'.$POT.'/deposit';
$data = array(
  "source_account_id" => $accountid,
  "amount" => $amount,
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
echo $result;

//wh_log($result);
curl_close($ch);


        $dbhoster = $dbhost .":3036";
            $conn = mysql_connect($dbhoster, $dbuser, $dbpass);

            if(! $conn ) {
               die('Could not connect: ' . mysql_error());
            }


            $sql = "UPDATE `schedule` SET `STATUS` = '2' WHERE `ID` = $ID";

//echo $sql;
            mysql_select_db('admin_monzo');
            $retval = mysql_query( $sql, $conn );

            if(! $retval ) {
               echo " - " . mysql_error();
               $status = "0";
            } else {
            echo " - Update successful!";
$status = "1"; }
            mysql_close($conn);
            echo "</td>";
            echo "</tr>";


//end transfer//

// end if

}
}}
function wh_log($msg){
    $logfile = __DIR__ . '/balancelogs.txt';
    file_put_contents($logfile,date("Y-m-d H:i:s")." | ".$msg."\n",FILE_APPEND);
}

?>

</table>

</body>
</html>
