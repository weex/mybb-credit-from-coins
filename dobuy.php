<?php
/*
    @author     : David Sterry;
    @date       : 01.29.2013 ;
    @version    : 1.0 ;
    @mybb       : compatibility with MyBB 1.4.x and MyBB 1.6.x ;
    @description: This plugin helps you to sell points for bitcoins.
    @homepage   : http://davidsterry.com/credit_from_coins 
    @copyright  : MyBB License. All rights reserved. 
*/

define("IN_MYBB", 1);
require_once "./global.php";
//error_reporting(E_ALL);

if(!$mybb->user['uid']) 
        error_no_permission();
$lang->load("creditfromcoins");
$act = $_GET['act'];
$name = $mybb->settings['credit_from_coins_name'];

if ($_POST['amount'] != '')
{

	$secret = $mybb->settings['credit_from_coins_secret'];

	$my_address = $mybb->settings['credit_from_coins_address'];

	$my_callback_url = $mybb->settings['bburl'].'/dobuy2.php?uid='.$_POST['uid'].'&amount='.$_POST['amount'].'&secret='.$secret;

	$root_url = 'https://blockchain.info/api/receive';

	$parameters = 'method=create&address=' . $my_address .'&anonymous=false&callback='. urlencode($my_callback_url);

	$response = file_get_contents($root_url . '?' . $parameters);

	if( !$response ) {

		echo "Error connecting to Blockchain.info.";

	} else {

		$object = json_decode($response);

		$page="
<html>
<head>
<title>{$mybb->settings[bbname]} - ".$lang->sprintf($lang->creditfromcoins_title, $name)."</title>
{$headerinclude}
</head>
<body>
	{$header}
	<table border=\"0\" cellspacing=\"{$theme[borderwidth]}\" cellpadding=\"{$theme[tablespace]}\" class=\"tborder\" style=\"clear: both;\">

<tr><td class='thead' colspan='2'><strong>".$lang->sprintf($lang->creditfromcoins_long_title, $name)."</strong></td></tr>
<tr><td class='trow1' colspan='2'><center><br />To complete your purchase send <b>".$_POST['amount']." BTC</b> to : <br /><b>".$object->input_address."</b><br />or use the QR code below with your Bitcoin wallet app.<br /><br /><img src=\"https://chart.googleapis.com/chart?chs=128x128&cht=qr&chl=bitcoin:".$object->input_address."?amount=".$_POST['amount']."&label=".$mybb->settings[bbname].".&choe=UTF-8&chld=H|0\"><br /><br /></center></td></tr>
<tr><td class='trow1'><center>Please allow 1-2 hours after sending for your purchase to appear in your account.</center></td></tr>
</table>
<br>
{$footer}
</body>
</html> ";

		output_page($page);
	}
}
