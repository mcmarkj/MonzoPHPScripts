<?php
require __DIR__ . '/Twilio/Twilio/autoload.php';
require __DIR__ . '/config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

use Twilio\Rest\Client;
$dbhoster = $dbhost .':3036';


wh_log('==================[ Incoming Request ]==================');

wh_log("Full _REQUEST dump:\n".print_r($_REQUEST,true));

if ( !isset($_GET['key']) ){
    wh_log('No security key specified, ignoring request');
} elseif ($_GET['key'] != $hookkey) {
    wh_log('Security key specified, but not correct:');
    wh_log("\t".'Wanted: "'.$hookkey.'", but received "'.$_GET['key'].'"');
} else {
    //process the request
    wh_log('Processing a "'.$_POST['type'].'" request...');
    switch($_POST['type']){
        case 'transaction.created'  : transaction($_REQUEST);   break;
        case 'expenses'   : expenses($_REQUEST);    break;
        default:
            wh_log('Request type "'.$_POST['type'].'" unknown, ignoring.');
    }
}
wh_log('Finished processing request.');

/***********************************************
    Helper Functions
***********************************************/
function wh_log($msg){
    $logfile = 'webhook.txt';
    file_put_contents($logfile,date("Y-m-d H:i:s")." | ".$msg."\n",FILE_APPEND);
}

function expenses($data){
  require __DIR__ . '/config.php';
$amount = $_POST['amount'];
$reportID = $_POST['report'];

$amount = $amount * 100;

wh_log('Amount in pence: ' . $amount);

$datePaid = $_POST['datePaid'];

$dateTransfer = date('Y-m-d H:i:s', strtotime($datePaid . ' +5 days'));

wh_log('I will transfer '. $amount .'pence  on ' . $dateTransfer);
            $dbhoster = $dbhost .':3036';
            $conn = mysql_connect($dbhoster, $dbuser, $dbpass);

            if(! $conn ) {
              wh_log(mysql_error());
               die('Could not connect: ' . mysql_error());
            }


            $sql = "INSERT INTO `schedule`(`AMOUNT`, `POTID`, `DATE`) VALUES ('$amount', '$expensepot', '$dateTransfer')";
            wh_log('SQL: ' . $sql);
//echo $sql;
            mysql_select_db($database);
            $retval = mysql_query( $sql, $conn );

            if(! $retval ) {
               wh_log(mysql_error());
               $status = "0";
            } else {
            wh_log('Update successful!');
            // Your Account SID and Auth Token from twilio.com/console

            $client = new Client($sid, $token);

            $Message = "💸 EXPENSES £".$amount/100 . " SCHEDULED FOR " . $dateTransfer;

            // Use the client to do fun stuff like send text messages!
            $client->messages->create(
                // the number you'd like to send the message to
                $MSISDN,
                array(
                    // A Twilio phone number you purchased at twilio.com/console
                    'from' => '',
                    // the body of the text message you'd like to send
                    'body' => $Message
                )
            );
$status = "1"; }
            mysql_close($conn);


}


function transaction($data){
  require __DIR__ . '/config.php';

  $amount = $data['amount'];
  $description = $data['description'];
  $merchant[] = $data['merchant'];
  $merchantname = $merchant['name'];
  $category = $merchant['category'];



                                      $dbhoster = $dbhost .":3036";
                                 $conn = mysql_connect($dbhoster, $dbuser, $dbpass);

                                 if(! $conn ) {
                                    die('Could not connect: ' . mysql_error());
                                 }


                                 $sql = "INSERT INTO `purchases`(`amount`, `merchantName`, `merchantCat`, `action`) VALUES ('$amount', '$merchantname', '$category', '1')";

                     //echo $sql;
                                 mysql_select_db($database);
                                 $retval = mysql_query( $sql, $conn );

                                 if(! $retval ) {
                                    wh_log(mysql_error());
                                    $status = "0";
                                 } else {
                                 wh_log('Update successful!');
                   $status = "1"; }
                                 mysql_close($conn);

                   //db_log("Added to DB by Webhooker",$EMAIL);



                       wh_log($data['description'] . ' just logged!');






}
?>
