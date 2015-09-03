<?php
/* Write saved settings to configuration file */
if( isset( $_POST['save'] ) ) {
	$basedir = preg_replace( '/\\\/', '/', realpath(dirname(__FILE__).'/').'/' );

	$nl = "\n";
	$t = "\t";

	/* File contents */
	$writeToConfigFile = "<?php".$nl;
	$writeToConfigFile .= $t."class Config".$nl;
	$writeToConfigFile .= $t."{".$nl;
	$writeToConfigFile .= $t.$t."const DB_CONNECTION_DOMAIN = '".$_POST['DB_CONNECTION_DOMAIN']."';".$nl;
	$writeToConfigFile .= $t.$t."const DB_CONNECTION_USER = '".$_POST['DB_CONNECTION_USER']."';".$nl;
	$writeToConfigFile .= $t.$t."const DB_CONNECTION_PASSWORD = '".$_POST['DB_CONNECTION_PASSWORD']."';".$nl;
	$writeToConfigFile .= $t.$t."const DB_CONNECTION_DATABASE = '".$_POST['DB_CONNECTION_DATABASE']."';".$nl;
	$writeToConfigFile .= "".$nl;
	$writeToConfigFile .= $t.$t."const OAUTH_CREDENTIALS_EMAIL = '".$_POST['OAUTH_CREDENTIALS_EMAIL']."';".$nl;
	$writeToConfigFile .= $t.$t."const OAUTH_CREDENTIALS_PRIVATE_KEY_FILE_NAME = '".$_POST['OAUTH_CREDENTIALS_PRIVATE_KEY_FILE_NAME']."';".$nl;
	$writeToConfigFile .= $t."}".$nl;
	$writeToConfigFile .= "?>";

	/* Write to file */
	$myfile = fopen($basedir."config/config.php", "w") or die("Unable to open file!");
	fwrite($myfile, $writeToConfigFile);
	fclose($myfile);

	$alert = array("type"=>"success", "message"=>"Configuration Succesfully Saved");
}
?>

<?php $titleTag = "Settings Configuration | Organic Search Analytics"; ?>
<?php $dbTable_settings = 'settings'; ?>

<?php include_once('inc/html/_head.php'); ?>

	<?php include_once('inc/html/_alert.php'); ?>
	<h1>Organic Search Analytics | Settings Configuration</h1>

	<h2>Configuration</h2>
	<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="post">

	<?php
		if( $isConfigured ) {
			$db_connection_host = config::DB_CONNECTION_DOMAIN;
			$db_connection_user = config::DB_CONNECTION_USER;
			$db_connection_password = config::DB_CONNECTION_PASSWORD;
			$db_connection_database = config::DB_CONNECTION_DATABASE;

			$oauth_credentials_email = config::OAUTH_CREDENTIALS_EMAIL;
			$oauth_credentials_private_key_file_name = config::OAUTH_CREDENTIALS_PRIVATE_KEY_FILE_NAME;

		} else {
			$db_connection_host = $db_connection_user = $db_connection_password = $db_connection_database = $oauth_credentials_private_key_file_name = $oauth_credentials_email = "";
		}
	?>

	<hr>
	<h3>Database</h3>

	<p>
		<label for="DB_CONNECTION_DOMAIN">Database Host</label>
		<input type="text" id="DB_CONNECTION_DOMAIN" name="DB_CONNECTION_DOMAIN" value="<?php echo $db_connection_host ?>">
	</p>

	<p>
		<label for="DB_CONNECTION_USER">Database Username</label>
		<input type="text" id="DB_CONNECTION_USER" name="DB_CONNECTION_USER" value="<?php echo $db_connection_user ?>">
	</p>

	<p>
		<label for="DB_CONNECTION_PASSWORD">Database Password</label>
		<input type="password" id="DB_CONNECTION_PASSWORD" name="DB_CONNECTION_PASSWORD" value="<?php echo $db_connection_password ?>">
	</p>

	<p>
		<label for="DB_CONNECTION_DATABASE ">Database Name</label>
		<input type="text" id="DB_CONNECTION_DATABASE " name="DB_CONNECTION_DATABASE" value="<?php echo $db_connection_database ?>">
	</p>

	<hr>
	<h3>Google OAuth2.0</h3>

	<p>
		<label for="OAUTH_CREDENTIALS_EMAIL">OAuth 2.0 Email Address</label>
		<input type="text" id="OAUTH_CREDENTIALS_EMAIL" name="OAUTH_CREDENTIALS_EMAIL" value="<?php echo $oauth_credentials_email ?>">
	</p>

	<p>
		<label for="OAUTH_CREDENTIALS_PRIVATE_KEY_FILE_NAME">OAuth 2.0 P12 File Name</label>
		<input type="text" id="OAUTH_CREDENTIALS_PRIVATE_KEY_FILE_NAME" name="OAUTH_CREDENTIALS_PRIVATE_KEY_FILE_NAME" value="<?php echo $oauth_credentials_private_key_file_name ?>">
	</p>

	<hr>

	<input type="submit" name="save" id="save" value="Save Configuration">

	</form>

<?php include_once('inc/html/_foot.php'); ?>