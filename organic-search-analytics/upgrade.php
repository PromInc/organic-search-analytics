<?php $titleTag = "Upgrade"; ?>

<?php include_once('inc/html/_head.php'); ?>

<?php
if( isset( $_GET ) && isset( $_GET['upgrade'] ) ) {
	switch( $_GET['upgrade'] ) {
		case "1_x_to_2_0_0":
			$configFile = 'config/config.php';
			/* Get contents of Config file */
			$configFileContents = file_get_contents( $configFile );
			$configFileContentsFirst = substr( $configFileContents, 0, strpos( $configFileContents, "}" ) );
			$configFileContentsLast = substr( $configFileContents, strlen( $configFileContentsFirst ) );
			/* Set updated contents */
			$configFileContentsUpdated = $configFileContentsFirst . "\t\tconst CREDENTIALS_BING_API_KEY = '';\n\t" . $configFileContentsLast;
			/* Write updated contents */
			$confFileHandler = fopen($configFile, "w") or die("Unable to open file!");
			fwrite($confFileHandler, $configFileContentsUpdated);
			fclose($confFileHandler);

			/* Include resources */
			include_once( 'inc/code/core.php' ); //Core functions
			include_once( 'inc/code/mysql.php' ); //Database Connection
			$core = new Core(); //Load core
			$mysql = new MySQL(); //Load MySQL
			
			require_once( $GLOBALS['basedir'].'config/config.php' );  //Credentials & Configuration
			$GLOBALS['db'] = $core->mysql_connect_db(); // Connect to DB

			$query = "ALTER TABLE `search_analytics` CHANGE `ctr` `ctr` FLOAT NOT NULL";
			$result = $mysql->query( $query );

			$alert = array("type"=>"success", "message"=>"Upgrade performed succesfully.");
			break;
		case "2_1_0_to_2_2_0":
			/* Include resources */
			include_once( 'inc/code/core.php' ); //Core functions
			include_once( 'inc/code/mysql.php' ); //Database Connection
			$core = new Core(); //Load core
			$mysql = new MySQL(); //Load MySQL
			
			require_once( $GLOBALS['basedir'].'config/config.php' );  //Credentials & Configuration
			$GLOBALS['db'] = $core->mysql_connect_db(); // Connect to DB

			/* Add quick links categories table */
			$query = "CREATE TABLE IF NOT EXISTS `report_saved_categories` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(256) NOT NULL, `description` varchar(1000) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0";
			$result = $mysql->query( $query );

			/* Add quick links table */
			$query = "CREATE TABLE IF NOT EXISTS `report_saved` ( `id` int(11) NOT NULL AUTO_INCREMENT, `domain` varchar(256) NOT NULL, `name` varchar(256) NOT NULL, `category` int(11) NOT NULL, `paramaters` varchar(1000) NOT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0";
			$result = $mysql->query( $query );

			$alert = array("type"=>"success", "message"=>"Upgrade performed succesfully.");
			break;
		case "2_x_x_to_2_4_0":
			/* Include resources */
			include_once( 'inc/code/core.php' ); //Core functions
			include_once( 'inc/code/mysql.php' ); //Database Connection
			$core = new Core(); //Load core
			$mysql = new MySQL(); //Load MySQL
			$GLOBALS['db'] = $core->mysql_connect_db(); // Connect to DB

			/* Add Country to the search_analytics table */
			$query = "ALTER TABLE `search_analytics` ADD `country` VARCHAR(10) NULL AFTER `device_type`";
			$result = $mysql->query( $query );

			$alert = array("type"=>"success", "message"=>"Upgrade performed succesfully.");
			break;
		case "2_x_x_to_2_5_0":
			/* Include resources */
			include_once( 'inc/code/core.php' ); //Core functions
			include_once( 'inc/code/mysql.php' ); //Database Connection
			$core = new Core(); //Load core
			$mysql = new MySQL(); //Load MySQL
			$GLOBALS['db'] = $core->mysql_connect_db(); // Connect to DB

 			$errors = array();

			/* Table: search_analytics */
			$query = "ALTER TABLE `search_analytics`".
					 " ADD `page` VARCHAR(500) NULL DEFAULT NULL AFTER `query`,".
					 " CHANGE COLUMN `device_type` `device_type` VARCHAR(24) NULL DEFAULT NULL,".
					 " CHANGE COLUMN `country` `country` VARCHAR(10) NULL DEFAULT NULL,".
					 " CHANGE COLUMN `query` `query` VARCHAR(500) NULL DEFAULT NULL,".
					 " CHANGE COLUMN `avg_position` `avg_position` FLOAT NULL DEFAULT NULL,".
					 " CHANGE COLUMN `avg_position_click` `avg_position_click` FLOAT NULL DEFAULT NULL,".
					 " CONVERT TO CHARACTER SET `utf8` COLLATE `utf8_unicode_ci`"
					 ;
			$result = $mysql->query( $query );
			if( !$result ) {
				$errors[] = $mysql->error;
			}
			
			/* Table: search_analytics */
			/* Action: Prepopulate settings */
			$query = "INSERT INTO `settings`".
					 "	(type, value, data)".
					 "VALUES".
					 "	('settings', 'google_search_console_dimensions_query', 'On'),".
					 "	('settings', 'google_search_console_dimensions_page', 'Off'),".
					 "	('settings', 'google_search_console_dimensions_device', 'On'),".
					 "	('settings', 'google_search_console_dimensions_country', 'On')"
					 ;
			$result = $mysql->query( $query );
			if( !$result ) {
				$errors[] = $mysql->error;
			}
			
			/* Table: report_saved */
			$query = "ALTER TABLE `report_saved` CONVERT TO CHARACTER SET `utf8` COLLATE `utf8_unicode_ci`";
			$result = $mysql->query( $query );
			if( !$result ) {
				$errors[] = $mysql->error;
			}

			/* Table: report_saved_categories */
			$query = "ALTER TABLE `report_saved_categories` CONVERT TO CHARACTER SET `utf8` COLLATE `utf8_unicode_ci`";
			$result = $mysql->query( $query );
			if( !$result ) {
				$errors[] = $mysql->error;
			}

			/* Table: settings */
			$query = "ALTER TABLE `settings` ENGINE=`INNODB`, CONVERT TO CHARACTER SET `utf8` COLLATE `utf8_unicode_ci`";
			$result = $mysql->query( $query );
			if( !$result ) {
				$errors[] = $mysql->error;
			}

			if( !count( $errors ) ) {
				$alert = array("type"=>"success", "message"=>"Upgrade performed succesfully.");
			} else {
				$errorString = "There were errors in the upgrade process.<br><br>";
				$errorString .= implode("<br>", $errors );
				$alert = array("type"=>"error", "message"=>$errorString);
			}

			$alert = array("type"=>"success", "message"=>"Upgrade performed succesfully.<br>Go to the <a href=\"settings-configure.php\"><b>Settings Configuration</b></a> page and click the <b>Save</b> button.");
			break;			
		case "2_5_0_to_2_5_1":
			/* Include resources */
			include_once( 'inc/code/core.php' ); //Core functions
			include_once( 'inc/code/mysql.php' ); //Database Connection
			$core = new Core(); //Load core
			$mysql = new MySQL(); //Load MySQL
			$GLOBALS['db'] = $core->mysql_connect_db(); // Connect to DB

 			$errors = array();

			/* Table: search_analytics */
			$query = "ALTER TABLE `search_analytics` CHANGE COLUMN `search_type` `search_type` VARCHAR(24) NULL DEFAULT NULL";
			$result = $mysql->query( $query );
			if( !$result ) {
				$errors[] = $mysql->error;
			}
			
			if( !count( $errors ) ) {
				$alert = array("type"=>"success", "message"=>"Upgrade performed succesfully.");
			} else {
				$errorString = "There were errors in the upgrade process.<br><br>";
				$errorString .= implode("<br>", $errors );
				$alert = array("type"=>"error", "message"=>$errorString);
			}

			$alert = array("type"=>"success", "message"=>"Upgrade performed succesfully.");
			break;
	}
}
?>


	<?php include_once('inc/html/_alert.php'); ?>
	<h1>Organic Search Analytics | Upgrade</h1>
	<p>Certain versions require special consideration when upgrading.  This page will take care of technical changes that need to be made.</p>
	<p><b>NOTE</b>: If upgrading through multiple versions, it's advised to run all of the updates in order.</p>
	<ul>
		<li>
			<h2>Version 2.5.0 to 2.5.1</h2>
			<ul>
				<li>Updates column <b>search_type</b> to allow NULL and default to NULL.</li>
				<li><a href="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>?upgrade=2_5_0_to_2_5_1" class="button">Run Update for Version 2.5.0 to 2.5.1 <i class="fa fa-play" aria-hidden="true"></i></a></li>
			</ul>
		</li>
		<li>
			<h2>Version 2.x.x to 2.5.0</h2>
			<ul>
				<li>Several database changes:</li>
				<ul>
					<li>Adds column <b>page</b> to the search_analytics table.</li>
					<li>Updates column <b>country</b> to default to NULL.</li>
					<li>Updates column <b>query</b> to allow NULL and default to NULL.</li>
					<li>Updates column <b>device_type</b> to allow NULL and default to NULL.</li>
					<li>Updates column <b>avarage_position</b> setting default to NULL and change type from int(11) to float.</li>
					<li>Updates column <b>avarage_position_click</b> changing type from int(11) to float.</li>
					<li>Change the default character set to utf8 for all tables</li>
					<li>Change the engine from MyISAM to InnoDB on the <b>settings</b> table.</li>
				</ul>
				<li>Added debug logging functionality (only added to <b>Data Capture</b> processes at this time)</li>
				<ul>
					<li class="error">NOTE: Manual Upgrade Step is Required!</li>
					<li>Go to the <a href="settings-configure.php"><b>Settings Configuration</b></a> page and click the <b>Save</b> button.  This updates your config file to add the Debug Logger configuration.  You do NOT need to enable it (and it is wise not to unless you need to do debugging).</li>
				</ul>
				<li><a href="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>?upgrade=2_x_x_to_2_5_0" class="button">Run Update for Version 2.x.x to 2.5.0 <i class="fa fa-play" aria-hidden="true"></i></a></li>
			</ul>
		</li>
		<li>
			<h2>Version 2.x.x to 2.4.0</h2>
			<ul>
				<li>Adds column <b>country</b> to the search_analytics table.</li>
				<li><a href="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>?upgrade=2_x_x_to_2_4_0" class="button">Run Update for Version 2.x.x to 2.4.0 <i class="fa fa-play" aria-hidden="true"></i></a></li>
			</ul>
		</li>
		<li>
			<h2>Version 2.1.0 and below to 2.2.0</h2>
			<ul>
				<li>Adds tables to the database for the Quick Links reporting feature.</li>
				<li><a href="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>?upgrade=2_1_0_to_2_2_0" class="button">Run Update for Version 2.1.0 and below to 2.2.0 <i class="fa fa-play" aria-hidden="true"></i></a></li>
			</ul>
		</li>
		<li>
			<h2>Version 1.x to 2.0.0</h2>
			<ul>
				<li>Adds the CREDENTIALS_BING_API_KEY constant to the config/config.php file to allow for setting the Bing Webmaster Tools API connection.</li>
				<li>Updates the Click Through Rate data type in the search_analytics table to correct data inaccuracies.</li>
				<li><a href="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>?upgrade=1_x_to_2_0_0" class="button">Run Update for Version 1.x to 2.0.0 <i class="fa fa-play" aria-hidden="true"></i></a></li>
			</ul>
		</li>
	</ul>

<?php include_once('inc/html/_foot.php'); ?>