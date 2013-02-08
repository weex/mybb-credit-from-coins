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

define('IN_MYBB', 1);
require_once './global.php';
//if you are a guest you cannot see this page
if(!$mybb->user['uid']) 
        error_no_permission();
$lang->load("creditfromcoins");
$act = $_GET['act'];
$name = $mybb->settings['credit_from_coins_name'];

if ($_POST['amount'] == '')
{
	add_breadcrumb($lang->sprintf($lang->creditfromcoins_long_title, $name), "buy.php");
	//build select menu
	$amounts = explode("|", $mybb->settings['credit_from_coins_amounts']);
	$cost = $mybb->settings['credit_from_coins_cost'];
	foreach ($amounts AS $amount)
	{
		unset($price);
		$price = $cost * $amount;
		$options.="<option value='$price'>$amount $name ($price BTC)</option>";
	}

	if ($mybb->settings['credit_from_coins_field'] != "")
	{
	        $interogare = $db->fetch_array($db->simple_select("users", $mybb->settings['credit_from_coins_field'], "uid = ".$mybb->user['uid'], array("order_by" => 'uid', "order_dir" => 'DESC', "limit" => 1)));
        	$points = $interogare[$mybb->settings['credit_from_coins_field']];
	} 
	else 
	{
	        $points = "0";
	}
    	//display page
	$page="
<html>
<head>
<title>{$mybb->settings[bbname]} - ".$lang->sprintf($lang->creditfromcoins_title, $name)."</title>
{$headerinclude}
</head>
<body>
	{$header}
	<form action=\"".$mybb->settings['bburl']."/dobuy.php\" method=\"post\">
	<table border=\"0\" cellspacing=\"{$theme[borderwidth]}\" cellpadding=\"{$theme[tablespace]}\" class=\"tborder\" style=\"clear: both;\">
<input type=\"hidden\" name=\"uid\" value=\"{$mybb->user['uid']}\" />
<tr><td class='thead' colspan='2'><strong>".$lang->sprintf($lang->creditfromcoins_long_title, $name)."</strong></td></tr>
<tr><td class='trow1' colspan='2'>".$lang->sprintf($lang->creditfromcoins_points, $points, $name)."</td></tr>
<tr><td class='trow1'>".$lang->sprintf($lang->creditfromcoins_buypoints, $name)."<td class='trow1'><select name='amount'>$options</select></td></tr>
<tr><td colspan='2' class='trow2'>
<center><input type=\"submit\" value=\"Continue and Get Deposit Address\"></center>
";
$page .= "
</td>
</tr></table>
</form>
<br>
{$footer}
</body>
</html> ";
	output_page($page);
}
?>

