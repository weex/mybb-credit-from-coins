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
//Test myBB
if(!defined("IN_MYBB")) {
    die("This file cannot be accessed directly.");
}
//Our hook
$plugins->add_hook("build_friendly_wol_location_end", "credit_from_coins_online");
$plugins->add_hook('admin_tools_menu','credit_from_coins_admin_menu');
$plugins->add_hook('admin_tools_action_handler','credit_from_coins_admin_action');
//Plugin information
function credit_from_coins_info()
{
    return array(
        "name"				=> "Credit From Coins",
        "description"		=> "This plugin displays a page where users can buy credit or forum points with bitcoins.",
        "website"			=> "http://davidsterry.com/credit_from_coins",
        "author"			=> "David Sterry",
        "authorsite"		=> "http://davidsterry.com",
        "version"			=> "1.1",
        "guid"              => "feaa1a06c281034fdcd621c688048b68",
		'compatibility'     => '14*,16*,18*',
                );
}
//Activate plugin
function credit_from_coins_activate() 
{
    global $db;	
	require MYBB_ROOT."/inc/adminfunctions_templates.php";
	$grup_de_setari = array(
	"gid"			=> "NULL",
	"name"			=> "credit_from_coins_group",
	"title" 		=> "Credit From Coins",
	"description"	=> "Settings for \"Credit From Coins\" plugin.",
	"disporder"		=> "",
	"isdefault"		=> "0",
	);
	$db->insert_query("settinggroups", $grup_de_setari);
	$gid = $db->insert_id();

	$points_setting_1 = array(
	"sid"			=> "NULL",
	#"name"			=> "credit_from_coins_amounts_paypal",
	"name"			=> "credit_from_coins_amounts",
	"title"			=> "Available point quantities",
	"description"	=> "Set amount of points that can be purchased using <b>Bitcoin</b>. Default : 10|20|30",
	"optionscode"	=> "text",
	"value"			=> "10|20|30",
	"disporder"		=> "1",
	"gid"			=> intval($gid),
	);
	$points_setting_2 = array(
	"sid"			=> "NULL",
	#"name"			=> "credit_from_coins_cost_paypal",
	"name"			=> "credit_from_coins_cost",
	"title"			=> "How much should a point cost?",
	"description"	=> "How much is a point in bitcoins. This will be used to calculate the amount of points added for an incoming payment!",
	"optionscode"	=> "text",
	"value"			=> "0.25",
	"disporder"		=> "2",
	"gid"			=> intval($gid),
	);
	$points_setting_3 = array(
	"sid"			=> "NULL",
	"name"			=> "credit_from_coins_secret",
	"title"			=> "Secret to share with Blockchain.info",
	"description"	=> "This secret will be used to validate incoming payment notifications. It should be long and random enough that it cannot be guessed.",
	"optionscode"	=> "text",
	"value"			=> "",
	"disporder"		=> "3",
	"gid"			=> intval($gid),
	);
	$points_setting_4 = array(
	"sid"			=> "NULL",
	"name"			=> "credit_from_coins_address",
	"title"			=> "Enter Bitcoin Address",
	"description"	=> "Enter the bitcoin address where you would like to receive payments. Attention : Double check that this address is complete and update it often for privacy.",
	"optionscode"	=> "text",
	"value"			=> "",
	"disporder"		=> "4",
	"gid"			=> intval($gid),
	);
	$points_setting_5 = array(
	"sid"			=> "NULL",
	"name"			=> "credit_from_coins_field",
	"title"			=> "The name of database field",
	"description"	=> "Which database field stores user points? Attention : The field must exist in the \"users\" table, must accept float values, and should have a default of 0, with no nulls allowed. (Compatible with \"NewPoints\" , \"MyPS\" or \"Image Points\" if you use those systems in your forum.)",
	"optionscode"	=> "text",
	"value"			=> "",
	"disporder"		=> "5",
	"gid"			=> intval($gid),
	);
	$points_setting_6 = array(
	"sid"			=> "NULL",
	"name"			=> "credit_from_coins_name",
	"title"			=> "Points name",
	"description"	=> "What do you want to call your points?",
	"optionscode"	=> "text",
	"value"			=> "Points",
	"disporder"		=> "6",
	"gid"			=> intval($gid),
	);

	$db->insert_query("settings", $points_setting_1);
	$db->insert_query("settings", $points_setting_2);
	$db->insert_query("settings", $points_setting_3);
	$db->insert_query("settings", $points_setting_4);
	$db->insert_query("settings", $points_setting_5);
	$db->insert_query("settings", $points_setting_6);
    
	$db->query("CREATE TABLE `".TABLE_PREFIX."creditfromcoins_logs` (
        `lid` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
        `txn_id` TEXT NOT NULL ,
        `time` TEXT NOT NULL ,
        `amount` DECIMAL(10,8) NOT NULL,
        `uid` INT NOT NULL,
        `obs` TEXT NOT NULL
        ) ");
	rebuild_settings();
}
//Deactivate plugin
function credit_from_coins_deactivate() 
{
	global $db;
	require MYBB_ROOT."/inc/adminfunctions_templates.php";
	$db->delete_query("settings","name IN ('credit_from_coins_amounts','credit_from_coins_cost','credit_from_coins_secret','credit_from_coins_field','credit_from_coins_name','credit_from_coins_address')",'');
	$db->delete_query("settinggroups","name='credit_from_coins_group'",'');
	$db->query("DROP TABLE ".TABLE_PREFIX."creditfromcoins_logs");
	rebuild_settings();
}
//Location
function credit_from_coins_online(&$plugin_array)
{
	if (preg_match('/buy\.php/', $plugin_array['user_activity']['location'])) {
        $plugin_array['location_name'] = "Viewing <a href=\"buy.php\">Credit From Coins Page</a>";
	}
	return $plugin_array;
}
//Other important function
function credit_from_coins_admin_menu(&$sub_menu)
{
	global $lang;
	end($sub_menu);
	$key = (key($sub_menu)) + 10;
	$sub_menu[$key] = array
	(
		'id'      => 'credit_from_coins',
		'title'   => 'Credit From Coins',
		'link'    => 'index.php?module=tools/credit_from_coins'
	);
}
//Another admin function
function credit_from_coins_admin_action(&$action)
{
	$action['credit_from_coins'] = array('active' => 'credit_from_coins' , 'file' => 'credit_from_coins.php');
}
//Send PM function
function credit_from_coins_send_pm($pm, $fromid = 0)
{
	global $lang, $mybb, $db;
	if($mybb->settings['enablepms'] == 0)
		return false;
	if (!is_array($pm))
		return false;
	if (!$pm['subject'] ||!$pm['message'] || !$pm['touid'] || !$pm['receivepms'])
		return false;
	$lang->load('messages');	
	require_once MYBB_ROOT."inc/datahandlers/pm.php";	
	$pmhandler = new PMDataHandler();
    // prepare data
	$subject = $pm['subject'];
	$message = $pm['message'];
	$toid = $pm['touid'];
	if (is_array($toid))
		$recipients_to = $toid;
	else
		$recipients_to = array($toid);
	$recipients_bcc = array();
	if (intval($fromid) == 0)
		$fromid = intval($mybb->user['uid']);
	elseif (intval($fromid) < 0)
		$fromid = 0;
	// final data
	$pm = array(
		"subject" => $subject,
		"message" => $message,
		"icon" => -1,
		"fromid" => $fromid,
		"toid" => $recipients_to,
		"bccid" => $recipients_bcc,
		"do" => '',
		"pmid" => ''
	);
	// options
	$pm['options'] = array(
		"signature" => 0,
		"disablesmilies" => 0,
		"savecopy" => 0,
		"readreceipt" => 0
	);
	// handler data
	$pm['saveasdraft'] = 0;
	$pmhandler->admin_override = 1;
	$pmhandler->set_data($pm);
	if($pmhandler->validate_pm()) {
		$pmhandler->insert_pm();
	}
	else {
		return false;
	}
	return true;
}
?>
