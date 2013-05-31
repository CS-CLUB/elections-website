INSTALLING/SETTING UP ELECTIONS WEBSITE
=======================================


Install System_Daemon PEAR Package
----------------------------------------

System daemon examples (from the creator) and package info:

  * http://kevin.vanzonneveld.net/techblog/article/create_daemons_in_php/
  * http://pear.php.net/package/System_Daemon


1.	Install PEAR if you have not already done so:

	```
    sudo apt-get install php-pear
    ```


2.	Install System_Daemon

    ```
	sudo pear install System_Daemon
    ```

3.	Restart apache

    ```
	sudo service apache2 restart
    ```

Database Setup
----------------------------------------

Create a database for the elections and give the account limited privileges

1.  First create the elections database which will be used to store all the
    election information

    ```
	  mysql -u root -p
	  CREATE DATABASE elections;
    ```

1.  Next limit the permissions of the account which later on will be used as it will
    only have limited database access, (this is a security precaution so that if the account
    elections page is exploited it will not easily be possible to drop any tables/databases
    as the rms account will be a limited account.


    a.  Limit the permissions that rms has so in a worst-case-scenario that the
		    website has an exploit the hackers can't drop/delete any information :)

    ```
		GRANT SELECT,INSERT,UPDATE,CREATE ON elections.* TO 'rms'@'localhost';
    ```

	  b.  Verify you can login as rms and can only view the elections database and
	      cannot drop/delete anything.

    ```
		mysql -u rms -p
		SHOW DATABASES;					# You should only see information_schema and
		DROP DATABASE elections;		# Make sure this fails
		SHOW DATABASES;					# Make sure elections still exists!
    ```

	  c.	Refer to the "DATABASE SETUP NOTES" for creating backups and recovering
		    backups

2.	Since this is the first time there has been an election the database has
	  to have a table populated with the people who are currently executives.
	  These are the "incumbents" in the election, you have to get their access
	  account #'s and first/last name from ucsc_accounts DB ucsc_members table.

	  a. 	First get the access accounts for the current executive. Connect to the
		    database on the UCSC server and do following:

    ```
		USE ucsc_accounts;
		SELECT access_account, first_name, last_name FROM ucsc_members;
    ```

	  b.  Next, create a table for the current executives to be placed in.

    ```
    USE elections;
    CREATE TABLE winners_elect_2011
    (
    	reference SMALLINT UNSIGNED NOT NULL REFERENCES members(access_account),
    	position VARCHAR(32)
    );
    ```

	  c. 	Now, populate the table with the current executives, get their access_account
	   	  numbers from the query in step a. The following is an example for the
		    current club coordinator

    ```
    INSERT INTO winners_elect_2011 VALUES (4, 'Coordinator');
    ```

	  d. 	Now, the this step should not have to be executed, the members table should
		    be populated with the current executives when they login on the elections
		    site. But in the event that some executives have not bothered to login
		    to the elections site you can manually add them to members table.

    ```
    USE elections;
    INSERT INTO members_2012 VALUES (4, 'Joseph', 'Heron');
    ```



Deploying the PHP website
--------------------------

1.	Start by cloning the latest stable tag release or download from github, the
	  project can be found on Github under the [CS-CLUB account](https://github.com/CS-CLUB/elections-website)

2.	Copy the elections website over to the server and place it under the Apache
	  root directory in the folder elections. At present this would be /var/www/election

    ```
	  scp -r -P 8888 elections-website/* root@<club server>:/var/www/election
    ```

3.	Next, configure the following files which contain confidential information
	  or global data such as passwords and election start/end times

	  a. 	Instead of configuring the election authentication details all over again.
		    There should be a backup file called election_auth.bak in /var/www/election/inc/
		    cp this file a php file and then open it in vim and double check it's right.

    ```
		cp -f election_auth.bak election_auth.php
    ```

	  b.	You can also just edit the authorization file in inc/election_auth.php and set the
		    variables, you may need to refer to some of the administration files
		    or variables defined in the election registration page for the db password
		    and AES_KEY. At present the following are the configuration options:

    ```php
		 /* Database access */
		 $db_user = 'rms';
		 $db_pass = '...';
		 $db_acc_name = 'ucsc_accounts';
		 $db_elec_name = 'elections';

		 /* AES ENCRYPT/DECRYPT KEY */
		 $AES_KEY = '...';

		 /* SESSION ENCRYPT/DECRYPT KEY */
		 $SESSION_KEY = '...';
    ```

	  c. 	Next, open the election date file called election_date.php in election/inc
		    and set all the parameters in that file accordingly.


3.	Next, set the following permissions to various files that should never be made
    accessible (even READ ONLY) to the public as they contain confidential data
	  such as passwords

	  a.	For the inc/election_auth file

    ```bash
    chown root.www-data election_auth.php
    chmod 640 election_auth.php
    ```

	  b.	For the entire election/etc folder which contains the daemon file set
		    the following are required (this file is run by cron/initscripts not
		    apache AT ALL, its literally run as a shell script not PHP in traditional
		    sense)

    ```bash
    chown -R root.root etc/
    chmod -R 750 etc/
    ```

4.	The last step is to start the PHP daemon for the election, the daemon basically
	  manages the election and tallies the votes, etc.. It should also be set as
	  executable and to run on startup if you haven't done so already

	  a.  Kill any pre-existing instances

    ```bash
    killall daemon.php
    ```

    b.	Start the daemon, configure it to also run on startup (incase system reboots)

    ```bash
    cd /var/www/election/etc
    chmod u+x daemon.php
    ./daemon.php --write-initd
    ```
