<?php

define("IN_MYBB", 1);
require_once "./global.php";
global $mybb, $db;

$real_secret = $mybb->settings['credit_from_coins_secret'];
$uid = $_GET['uid']; //invoice_id is past back to the callback URL
$txn_id = $transaction_hash = $_GET['transaction_hash'];
$value_in_btc = bcdiv($_GET['value'] , 100000000, 8);
$my_bitcoin_address = $mybb->settings['credit_from_coins_address'];
$secret = $_GET['secret'];

//Check the secret passed to the create method is equal
if ($real_secret != $secret) {
	return;
}

//Commented out to test, uncomment when live
if ($_GET['test'] == true) {
	return;
}

//Check the address is our address
if ($_GET['destination_address'] != $my_bitcoin_address)
	return;

//Check the Request ip matches that from blockchain.info
if ( $_SERVER['REMOTE_ADDR'] == '91.203.74.202') {
        foreach ($_POST as $key => $value) {
            $emailtext .= $key . " = " .$value ."\n\n";
        }

        //get vars
        $amount = $value_in_btc; 

        //calculate number of points
        $points = ceil($amount / $mybb->settings['credit_from_coins_cost']);

        //another check
        $num = $db->num_rows($db->simple_select("creditfromcoins_logs", "*", "txn_id='$txn_id'"));
	if( $num > 0 ) {
		echo '*ok*';
		exit();
	}

	if( $_GET['confirmations'] >= 3 ) {
	        $insert['txn_id'] = $txn_id;
	        $insert['time'] = time();
	        $insert['uid'] = $uid;
	        $insert['amount'] = $points;
	        $insert['obs'] = "bitcoin";
	        $db->insert_query("creditfromcoins_logs", $insert);
	        $db->query("UPDATE ".TABLE_PREFIX."users SET {$mybb->settings['credit_from_coins_field']}={$mybb->settings['credit_from_coins_field']}+$points WHERE uid='$uid'");
	        echo "*ok*";
	} 

	return;
}
