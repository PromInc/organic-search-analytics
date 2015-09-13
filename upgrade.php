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
	}
}
?>


	<?php include_once('inc/html/_alert.php'); ?>
	<h1>Organic Search Analytics | Upgrade</h1>
	<div>Certain versions require special consideration when upgrading.  This page will take care of technical changes that need to be made.</div>
	<ul>
		<li>
			<h2>Version 1.x to 2.0.0</h2>
			<ul>
				<li>Adds the CREDENTIALS_BING_API_KEY constant to the config/config.php file to allow for setting the Bing Webmaster Tools API connection.</li>
				<li>Updates the Click Through Rate data type in the search_analytics table to correct data inaccuracies.</li>
			</ul>
			<div><a href="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>?upgrade=1_x_to_2_0_0">Run Update for Version 1.x to 2.0.0</a></div>
		</li>
	</ul>

<?php include_once('inc/html/_foot.php'); ?>