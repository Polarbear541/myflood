Plugin Info:
Name: MyFlood
Description: A plugin which allows you to prevent flooding through new posts and new threads per usergroup.
Author: Polarbear541
Version: 1.3
Compatibility: 1.6.x
Files: 2 (1 plugin and 1 language)
Database changes: 2 (2 columns added into usergroups table)
There are no template changes in this plugin.

Information:
This plugin allows you to set a time limit between new threads/posts per usergroup. This allows you to ensure that certain usergroups cannot 'flood' the forums by creating lots of new threads or posts. You can set a 'cooling down period' between new threads and posts which means users in that usergroup will only be able to create a new thread/post every X seconds. This is similar to the inbuilt function in MyBB but allows more control over usergroups.

Install Instructions:
Upload ./inc/plugins/myflood.php to ./inc/plugins/
Upload ./inc/languages/myflood.lang.php to ./inc/languages/english/
Go to ACP > Plugins > Install & Activate
Then define the time between new threads and new posts in the 'Forums and Posts' tab when editing a usergroup.
You can also edit the settings to enable/disable parts of the system and make the system count new threads as posts. You can also add excluded forums.

Important Note: Make sure you only have either the inbuilt MyBB post flood protection or the MyFlood post flood protection enabled or you may get unexpected results. Ideally you should turn the inbuilt feature off if you are using this plugin.

Update Instructions:
Upload new files overwriting the old ones. 
Reinstall to add the new settings then configure to your desire. 
(Reinstalling will remove your existing values for thread flood protection)

Plugin Licence:
This program is free software: you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public License along with this program. If not, see <http://www.gnu.org/licenses/