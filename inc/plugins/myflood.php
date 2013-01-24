<?php
//MyFlood by Polarbear541
//Released under the LGPL Licence (http://www.gnu.org/licenses/lgpl.html)
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$plugins->add_hook("newthread_start", "myflood_threads");
$plugins->add_hook("newthread_do_newthread_start", "myflood_threads");
$plugins->add_hook("newreply_start", "myflood_posts");
$plugins->add_hook("newreply_do_newreply_start", "myflood_posts");
$plugins->add_hook("admin_formcontainer_output_row", "myflood_perm");
$plugins->add_hook("admin_user_groups_edit_commit", "myflood_perm_submit");

function myflood_info()
{
	global $lang;
	$lang->load("myflood");
	return array(
		"name"			=> $lang->myflood,
		"description"	=> $lang->myflood_desc,
		"author"		=> "Polarbear541",
		"website"		=> "http://community.mybb.com/thread-123207.html",
		"version"		=> "1.3",
		"compatibility" => "16*",
		"guid" 			=> "2ffa3c273c4b27a258a3a7f1d7ee336c"
	);
}

function myflood_install() //When plugin installed
{
	global $db, $cache, $lang;
	$lang->load("myflood");
	//Add column to DB if doesn't already exist
	if(!$db->field_exists("timebtwnthreads", "usergroups"))
	{
		$db->add_column("usergroups", "timebtwnthreads", "int(10) NOT NULL");
	}
	if(!$db->field_exists("timebtwnposts", "usergroups"))
	{
		$db->add_column("usergroups", "timebtwnposts", "int(10) NOT NULL");
	}
	//Recache usergroups
	$cache->update_usergroups();
	
	//Insert setting group and settings then rebuild
	$setting_group = array(
		'gid'			=> 'NULL',
		'name'			=> 'myflood',
		'title'			=> $lang->myflood_settings,
		'description'	=> $lang->myflood_settings_desc,
		'disporder'		=> "1",
		'isdefault'		=> 'no',
	);
	$db->insert_query('settinggroups', $setting_group);
	$gid = $db->insert_id();
	
	$setting_one = array(
		'name'			=> 'myflood_posts_onoff',
		'title'			=> $lang->myflood_posts,
		'description'	=> $lang->myflood_posts_desc,
		'optionscode'	=> 'yesno',
		'value'			=> '1',
		'disporder'		=> 1,
		'gid'			=> intval($gid)
	);
	$db->insert_query('settings', $setting_one);

	$setting_two = array(
		'name'			=> 'myflood_threads_onoff',
		'title'			=> $lang->myflood_threads,
		'description'	=> $lang->myflood_threads_desc,
		'optionscode'	=> 'yesno',
		'value'			=> '1',
		'disporder'		=> 2,
		'gid'			=> intval($gid)
	);
	$db->insert_query('settings', $setting_two);

	$setting_three = array(
		'name'			=> 'myflood_ptflood',
		'title'			=> $lang->myflood_ptflood,
		'description'	=> $lang->myflood_ptflood_desc,
		'optionscode'	=> 'yesno',
		'value'			=> '1',
		'disporder'		=> 3,
		'gid'			=> intval($gid)
	);
	$db->insert_query('settings', $setting_three);
	
	$setting_four = array(
		'name'			=> 'myflood_exforums',
		'title'			=> $lang->myflood_exforums,
		'description'	=> $lang->myflood_exforums_desc,
		'optionscode'	=> 'text',
		'value'			=> '',
		'disporder'		=> 4,
		'gid'			=> intval($gid)
	);
	$db->insert_query('settings', $setting_four);
	
	rebuild_settings();
}

function myflood_is_installed()
{
	global $db;
	return $db->field_exists("timebtwnthreads", "usergroups");
	return $db->field_exists("timebtwnposts", "usergroups");
}

function myflood_uninstall() //When plugin uninstalled
{
	global $db, $cache;
	//If column exists then drop from DB
	if($db->field_exists("timebtwnthreads", "usergroups"))
	{
		$db->drop_column("usergroups", "timebtwnthreads");
	}
	if($db->field_exists("timebtwnposts", "usergroups"))
	{
		$db->drop_column("usergroups", "timebtwnposts");
	}
	//Recache usergroups
	$cache->update_usergroups();
	
	//Remove and rebuild settings
	$db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN ('myflood_posts_onoff','myflood_ptflood','myflood_threads_onoff','myflood_exforums')");
	$db->query("DELETE FROM ".TABLE_PREFIX."settinggroups WHERE name='myflood'");
	rebuild_settings(); 
}

function myflood_posts() //When post is being created run flood checks
{
	global $mybb, $lang, $db, $thread;
	$lang->load("myflood");
	if(!empty($mybb->settings['myflood_exforums']))
	{
		$exforums = explode(",",$mybb->settings['myflood_exforums']);
	}
	else
	{
		$exforums = array();
	}
	//If time between posts isn't disabled for current usergroup and flood for posts is on. Also checks for excluded forums.
	if($mybb->usergroup['timebtwnposts'] != 0 && $mybb->settings['myflood_posts_onoff'] != 0 && !in_array($thread['fid'],$exforums))
	{
		//Find last post timestamp
		if($mybb->settings['myflood_ptflood'] == 1)
		{
			$qry = $db->simple_select("posts", "dateline", "uid='{$mybb->user['uid']}'", array("order_by" => 'dateline', "order_dir" => 'DESC'));
		}
		else
		{
			$qry = $db->simple_select("posts", "dateline", "uid='{$mybb->user['uid']}' AND replyto!='0'", array("order_by" => 'dateline', "order_dir" => 'DESC'));
		}
		$lastpost = $db->fetch_array($qry);
		//If time since last thread less than time between threads then throw error
		if((time() - $lastpost['dateline']) < $mybb->usergroup['timebtwnposts'])
		{
			//If time remaining is 1 then use singular lang
			if($mybb->usergroup['timebtwnposts'] == 1 || (($lastpost['dateline'] + $mybb->usergroup['timebtwnposts']) - time()) == 1)
			{
				error($lang->sprintf($lang->post_flood_error, (($lastpost['dateline'] + $mybb->usergroup['timebtwnposts']) - time())));
			}
			//Else use plural lang
			else
			{
				error($lang->sprintf($lang->post_flood_error_plural, (($lastpost['dateline'] + $mybb->usergroup['timebtwnposts']) - time())));
			}
		}	
	}
}

function myflood_threads() //When thread is being created run flood checks
{
	global $mybb, $lang, $db, $fid;
	$lang->load("myflood");
	if(!empty($mybb->settings['myflood_exforums']))
	{
		$exforums = explode(",",$mybb->settings['myflood_exforums']);
	}
	else
	{
		$exforums = array();
	}
	//If time between threads isn't disabled for current usergroup and flood for threads is on. Also checks excluded forums.
	if($mybb->usergroup['timebtwnthreads'] != 0 && $mybb->settings['myflood_threads_onoff'] != 0 && !in_array($fid,$exforums))
	{
		//Find last thread timestamp
		$qry = $db->simple_select("threads", "dateline", "uid='{$mybb->user['uid']}'", array("order_by" => 'dateline', "order_dir" => 'DESC'));
		$lastthread = $db->fetch_array($qry);
		//If time since last thread less than time between threads then throw error
		if((time() - $lastthread['dateline']) < $mybb->usergroup['timebtwnthreads'])
		{
			//If time remaining is 1 then use singular lang
			if($mybb->usergroup['timebtwnthreads'] == 1 || (($lastthread['dateline'] + $mybb->usergroup['timebtwnthreads']) - time()) == 1)
			{
				error($lang->sprintf($lang->thread_flood_error, (($lastthread['dateline'] + $mybb->usergroup['timebtwnthreads']) - time())));
			}
			//Else use plural lang
			else
			{
				error($lang->sprintf($lang->thread_flood_error_plural, (($lastthread['dateline'] + $mybb->usergroup['timebtwnthreads']) - time())));
			}
		}
	}
}

function myflood_perm($setting) //Show permission setting in usergroup tab
{
	global $mybb, $lang, $form;
	$lang->load("myflood");
	//If setting group is Posting/Rating Options then add new setting row
	if($setting['title'] == $lang->posting_rating_options && !empty($setting['title']))
	{
		$setting['content'] .= "<div class='group_settings_bit'>{$lang->post_flood_perm}:<br /><small>{$lang->post_flood_perm_desc}</small><br /></div>" . $form->generate_text_box('timebtwnposts', $mybb->input['timebtwnposts'], array('id' => 'timebtwnposts', 'class' => 'field50'));
		$setting['content'] .= "<div class='group_settings_bit'>{$lang->thread_flood_perm}:<br /><small>{$lang->thread_flood_perm_desc}</small><br /></div>" . $form->generate_text_box('timebtwnthreads', $mybb->input['timebtwnthreads'], array('id' => 'timebtwnthreads', 'class' => 'field50'));
	}
	return $setting;
}

function myflood_perm_submit() //When permission updated
{
	global $mybb, $updated_group;
	//When perms submitted add value into update array
	$updated_group['timebtwnposts'] = intval($mybb->input['timebtwnposts']);
	$updated_group['timebtwnthreads'] = intval($mybb->input['timebtwnthreads']);
}
?>