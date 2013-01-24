<?php
//MyFlood by Polarbear541
//Released under the LGPL Licence (http://www.gnu.org/licenses/lgpl.html)

//Plugin
$l['myflood'] = 'MyFlood';
$l['myflood_desc'] = 'A plugin which allows you to prevent flooding through new posts and new threads per usergroup.';

//Settings
$l['myflood_settings'] = 'MyFlood Settings';
$l['myflood_settings_desc'] = 'Settings for MyFlood.';
$l['myflood_posts'] = 'Post MyFlood On/Off';
$l['myflood_posts_desc'] = 'Turns the MyFlood post flood protection on and off for all usergroups.';
$l['myflood_threads'] = 'Thread MyFlood On/Off';
$l['myflood_threads_desc'] = 'Turns the MyFlood thread flood protection on and off for all usergroups.';
$l['myflood_ptflood'] = 'Post MyFlood includes new threads?';
$l['myflood_ptflood_desc'] = 'If this is set to yes then the post flood protection (first setting) will count new threads as posts. For this to work the first setting above must be enabled.';
$l['myflood_exforums'] = 'MyFlood Excluded Forums';
$l['myflood_exforums_desc'] = 'Choose which forums are excluded from MyFlood protection. Use comma-separated forum IDs.';

//Usergroup Permissions
$l['post_flood_perm'] = 'Post Flood Control';
$l['post_flood_perm_desc'] = 'Time between new posts in seconds (0 to disable)';
$l['thread_flood_perm'] = 'Thread Flood Control';
$l['thread_flood_perm_desc'] = 'Time between new threads in seconds (0 to disable)';

//Errors
$l['post_flood_error'] = 'You are trying to make a new post too quickly after your previous message. Please wait {1} more second.';
$l['post_flood_error_plural'] = 'You are trying to make a new post too quickly after your previous message. Please wait {1} more seconds.';
$l['thread_flood_error'] = 'You are trying to post a thread too quickly after posting a previous thread. Please wait {1} more second.';
$l['thread_flood_error_plural'] = 'You are trying to post a thread too quickly after posting a previous thread. Please wait {1} more seconds.';
?>