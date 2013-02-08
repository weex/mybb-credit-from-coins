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
//Test MyBB
if(!defined("IN_MYBB")) {
	die("This file cannot be accessed directly.");
}
$lang->load("creditfromcoins");
//Add breadcrumb
$page->add_breadcrumb_item("Credit From Coins", "index.php?module=tools/credit_from_coins");
//Action
if($mybb->input['action'] == "purge")
{
		$db->delete_query("creditfromcoins_logs");
		flash_message($lang->admin_logsdeleted_succes, 'success');
		admin_redirect("index.php?module=tools/credit_from_coins");
}
if($mybb->input['action'] == "delete")
{
    if (empty($mybb->input['sid']) || !is_numeric($mybb->input['sid']))
    {
        flash_message($lang->admin_fieldempty_id, 'error');
    }

    admin_redirect("index.php?module=tools/credit_from_coins");
}
if($mybb->input['action'] == "team")
{
    //header
	$page->output_header($lang->admin_team_title);
	//menu list
	$sub_tabs['info'] = array(
		'title'       => $lang->creditfromcoins_menu_info,
		'link'        => "index.php?module=tools/credit_from_coins",
		'description' => $lang->creditfromcoins_menu_infodesc	
    );
	$sub_tabs['logs'] = array(
		'title'       => $lang->creditfromcoins_menu_view,
		'link'        => "index.php?module=tools/credit_from_coins&action=view",
		'description' => $lang->creditfromcoins_menu_viewdesc
	);
	$sub_tabs['team'] = array(
		'title'       => $lang->creditfromcoins_menu_team,
		'link'        => "index.php?module=tools/credit_from_coins&action=team",
		'description' => $lang->creditfromcoins_menu_teamdesc
	);

	$page->output_nav_tabs($sub_tabs, 'Credit From Coins');
    //team work
    $table = new Table;

	$table->construct_header($lang->admin_team_title, array("class" => "align_center", "colspan" => 2, "width" => "60%"));
	$table->construct_header($lang->admin_website_title, array("class" => "align_center", "colspan" => 1, "width" => "40%"));
	$table->construct_row();

	$table->construct_cell("<b><i>DEVELOPERS</i></b>", array("class" => "align_center"));
	$table->construct_cell("<small>weex</small>", array("class" => "align_center"));
	$table->construct_cell("<a href = 'http://davidsterry.com' target = '_blank'>Visit the developer's website</a>", array("class" => "align_center", "rowspan" => 2));
	$table->construct_row();
		
	$table->output("David Sterry");
    
	$page->output_footer();  
}
if($mybb->input['action'] == "view")
{
    $per_page = 20;
    //check page number
	if($mybb->input['page'] && $mybb->input['page'] > 1)
	{
		$mybb->input['page'] = intval($mybb->input['page']);
		$start = ($mybb->input['page'] * $per_page) - $per_page;
	}
	else
	{
		$mybb->input['page'] = 1;
		$start = 0;
	}
    //header
	$page->output_header($lang->admin_logspoints_title);
	//mene list
	$sub_tabs['info'] = array(
		'title'       => $lang->creditfromcoins_menu_info,
		'link'        => "index.php?module=tools/credit_from_coins",
		'description' => $lang->creditfromcoins_menu_infodesc	
    );
	$sub_tabs['logs'] = array(
		'title'       => $lang->creditfromcoins_menu_view,
		'link'        => "index.php?module=tools/credit_from_coins&action=view",
		'description' => $lang->creditfromcoins_menu_viewdesc
	);
	$sub_tabs['team'] = array(
		'title'       => $lang->creditfromcoins_menu_team,
		'link'        => "index.php?module=tools/credit_from_coins&action=team",
		'description' => $lang->creditfromcoins_menu_teamdesc
	);

	$page->output_nav_tabs($sub_tabs, 'Credit From Coins');
	
    //now we have to display all logs
	$table = new Table;
	$table->construct_header($lang->admin_logs_user);
	$table->construct_header($lang->admin_logs_sum, array("width" => "10%"));
	$table->construct_header($lang->admin_logs_system, array("width" => "10%"));
	$table->construct_header($lang->creditfromcoins_logs_txn_id, array("width" => "20%"));
	$table->construct_header($lang->admin_logs_date, array("width" => "20%"));

	$query = $db->query("SELECT * FROM ".TABLE_PREFIX."creditfromcoins_logs ORDER BY lid DESC LIMIT {$start}, {$per_page}");
	while($log = $db->fetch_array($query))
	{
		$user = $db->fetch_array($db->simple_select("users","username","uid='{$log['uid']}'"));
		$log['time'] = date($mybb->settings['dateformat'], $log['time']);
		$table->construct_cell($user['username']);
		$table->construct_cell($log['amount']);
		$table->construct_cell($log['obs']);
		$table->construct_cell('<a href="https://blockchain.info/rawtx/'.$log['txn_id'].'">'.$log['txn_id'].'</a>');
		$table->construct_cell($log['time']);
		$table->construct_row();
	}
		
	if($table->num_rows() == 0)
	{
		$table->construct_cell($lang->admin_logs_nountilnow, array("colspan" => "5"));
		$table->construct_row();
	}
	$table->output($lang->admin_logs_deleteall);

	$page->output_footer();
}
if(!$mybb->input['action'])
{
    //header
	$page->output_header("Information");
	//menu list
	$sub_tabs['info'] = array(
		'title'       => $lang->creditfromcoins_menu_info,
		'link'        => "index.php?module=tools/credit_from_coins",
		'description' => $lang->creditfromcoins_menu_infodesc	
    );
	$sub_tabs['logs'] = array(
		'title'       => $lang->creditfromcoins_menu_view,
		'link'        => "index.php?module=tools/credit_from_coins&action=view",
		'description' => $lang->creditfromcoins_menu_viewdesc
	);
	$sub_tabs['team'] = array(
		'title'       => $lang->creditfromcoins_menu_team,
		'link'        => "index.php?module=tools/credit_from_coins&action=team",
		'description' => $lang->creditfromcoins_menu_teamdesc
	);
    $page->output_nav_tabs($sub_tabs, 'Credit From Coins');

    $table = new Table;
    
	$table->construct_header($lang->admin_main_serviceid, array("width" => "40%"));
 	$table->construct_header($lang->admin_main_message, array("width" => "20%"));
	$table->construct_header($lang->admin_main_price, array("width" => "10%"));
	$table->construct_header($lang->admin_main_points, array("width" => "10%"));
	$table->construct_header($lang->admin_main_shortnumber, array("width" => "10%"));
	$table->construct_header($lang->admin_main_action, array("class" => "align_center", "colspan" => 2, "width" => "10%"));
	$table->construct_row();
    
    // general stats
    $nr_log = $db->num_rows($db->simple_select("creditfromcoins_logs", "*", ""));
    $nr_bitcoins = $db->num_rows($db->simple_select("creditfromcoins_logs", "*", "obs ='bitcoins'"));
    $balance = "";
    $nvpStr = "";
    //table with stats
    $table1 = new Table;

	$table1->construct_header($lang->admin_main_stats, array("class" => "align_center", "colspan" => 1, "width" => "100%"));
	$table1->construct_row();

	$table1->construct_cell($lang->sprintf($lang->admin_main_stats_info1, intval($nr), intval($nr_log), intval($nr_bitcoins)));
	$table1->construct_row();
		
	$table1->output($lang->admin_main_miscinfo);
    
	$page->output_footer();  
}
?>
