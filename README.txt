Credit From Coins

Version : 1.1
License : Creative Commons Attribution-NonCommercial 3.0 
Price : free
Project released by : David Sterry

Based on Buy Credit by developer Mihu from MyBBRomania Team http://mybb.ro

Last change : 03.13.2015

ENGLISH LANGUAGE
I. Introduction
This mod provides a secure interface for your users to buy forum points with bitcoins. These forum points (like NewPoints, Image Points, points, credits, gold, etc.) are often used to buy access to protected areas, downloads, badges, or just to support your forum.

There are 6 configuration settings to this plugin: 

* Available point quantities - Set the amounts of points that are offered to the user in a dropdown box. This is really just a guide because any amount of bitcoins that are sent will be converted into points according to the next option.

* How much should a point cost - The incoming amount of bitcoins is divided by this to figure out the number of points to add to the user's account.

* Secret to share with Blockchain.info - Blockchain.info's API is used to do the communication and forwarding of transactions on the Bitcoin network. Once a payment is received by Blockchain.info, their service will ping a script in this plugin and this secret is sent with each request to verify that someone else isn't attempting to fake a payment.

* Enter Bitcoin Address - Blockchain.info supplies a new Bitcoin address for each payment but forwards every payment onto the address you enter here. It's a good idea to update this periodically for privacy purposes.

* The name of database field - This is the field in the `users` table where points amounts are stored. You may need to create this field if you don't already have a points system like NewPoints installed and should create it as a decimal(16,8) MySQL field. Amounts stored here will be incremented when a payment is received. The following command will create the extra field from the mysql cli. 

    ALTER TABLE mybb_users ADD `points` decimal(16,8) DEFAULT 0;

* Points name - Set your points name so they are referred to correctly by the pages supplied by this plugin.

The plugin creates a log showing each payment that has been received, the associated user, and Bitcoin transaction information.

II. Install

1) Unzip the downloaded file and copy each file to the same folder under the root directory of your MyBB forum.

buy.php  
dobuy2.php 
dobuy.php  
inc/languages/english/creditfromcoins.lang.php  
inc/plugins/credit_from_coins.php  
admin/modules/tools/credit_from_coins.php  

2) Go to Admin CP -> Plugin Manager -> Credit From Coins and activate the plugin!

3) View and change the settings the plugin. [Admin CP -> Configuration -> Settings -> Credit From Coins (6 Settings)]


