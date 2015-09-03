<?php $titleTag = "Settings | Organic Search Analytics"; ?>
<?php $dbTable_settings = 'settings'; ?>

<?php include_once('inc/html/_head.php'); ?>

<?php
if( $isConfigured ) {
	/* Get list of sites */
	$siteSettings = $mysql->getSettings("sites_google");
	$sitesList = $dataCapture->getSitesGoogleSearchConsole();

	/* Update settings table */
	if( isset( $_POST['sites_google'] ) ) {
		foreach( $sitesList as $key => $value ) {
			if( in_array( $value['url'], $_POST['sites_google'] ) ) {
				/* Set to checked */
				$siteToAdd = addslashes( $value['url'] );

				if( array_key_exists( $siteToAdd , $siteSettings ) ) {
					$response = $mysql->qryDBupdate( $dbTable_settings, array('type'=>'sites_google', 'value'=>$siteToAdd), array('data'=>1) );
				} else {
					$response = $mysql->qryDBinsert( $dbTable_settings, "NULL, 'sites_google', '".$siteToAdd."', 1" );
				}
				if( $response ) {
					$siteSettings[$value['url']] = 1;
				}
			} else {
				/* Set to unchecked */
				$response = $mysql->qryDBupdate( $dbTable_settings, array('type'=>'sites_google', 'value'=>$value['url']), array('data'=>0) );
				if( $response ) {
					$siteSettings[$value['url']] = NULL;
				}
			}
			$alert = array("type"=>"success", "message"=>"Configuration Succesfully Updated");
		}
	}
}
?>

	<?php include_once('inc/html/_alert.php'); ?>
	<h1>Organic Search Analytics | Settings</h1>

	<h2>Configuration</h2>
	<p>Settings for connecting to MySQL and Google OAuth 2.0.</p>
	<?php

		if( !$isConfigured ) {
			echo '<p><b>Status</b>: Configuration file is missing</p>';
			echo '<p><a href="settings-configure.php">Create Configuration File</a></p>';
		} else {
			echo '<p><b>Status</b>: The configuration file is set.</p>';
			echo '<p><a href="settings-configure.php">Change Configuration</a></p>';
		}
	?>
	<hr>
	<h2>Site Setup</h2>
	<h3>Google Search Analytics</h3>
	<p>Choose which sites you wish to capture data from Google Search Console.</p>
	<p><i>Not seeing the sites you expect?</i><br>Ensure that you have enabled the <b>Google Search Console API</b> and added your Google API Service Account email address as a user to each of your sites in Google Search Console.<br><a href="http://promincproductions.com/blog/google-api-access-google-search-analytics-from-google-search-console/" target="blank">Instruction on how to configure Google Search Analytics for API Access</a></p>

	<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="post">
		<?php
		if( count( $sitesList ) == 0 && !isset( $_POST['sites_google'] ) ) {
			echo '<p><b><i>No sites are configured at this time.  Add a site by typing it in the field below and choosing Save Settings.</i></b></p>';
		}
		?>

	<ul id="sites_google">
		<?php if( $sitesList ) { ?>
			<?php foreach( $sitesList as $value ) { ?>
				<li><input type="checkbox" name="sites_google[]" value="<?php echo $value['url'] ?>" <?php if($siteSettings[$value['url']]==1){echo " checked";}?> /><?php echo $value['url'] ?></li>	
			<?php } ?>
		<?php } ?>
	</ul>
	<input type="submit" value="Save Settings">

	</form>

	<hr>

<?php include_once('inc/html/_foot.php'); ?>