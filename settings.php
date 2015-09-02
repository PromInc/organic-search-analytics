<?php $titleTag = "Settings | Organic Search Analytics"; ?>
<?php $dbTable_settings = 'settings'; ?>

<?php include_once('inc/html/_head.php'); ?>

	<h1>Organic Search Analytics | Settings</h1>

	<h2>Configuration</h2>
	<?php

		if( !$isConfigured ) {
			echo '<p>Configuration file is missing</p>';
		} else {
			echo '<p>The configuration file is set.</p>';
		}
	?>
	<hr>
	<h2>Site Setup</h2>
	<h3>Google Search Analytics</h3>
	<p>Choose which sites you wish to capture data from Google Search Console.</p>
	<p><i>Not seeing the sites you expect?</i><br>Ensure that you have enabled the <b>Google Search Console API</b> and added your Google API Service Account email address as a user to each of your sites in Google Search Console.<br><a href="http://promincproductions.com/blog/google-api-access-google-search-analytics-from-google-search-console/" target="blank">Instruction on how to configure Google Search Analytics for API Access</a></p>

	<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="post">
		<?php
		/* Get list of sites */
		$siteSettings = $mysql->getSettings("sites_google");
		$sitesList = $dataCapture->getSitesGoogleSearchConsole();

		if( count( $sitesList ) == 0 && !isset( $_POST['sites_google'] ) ) {
			echo '<p><b><i>No sites are configured at this time.  Add a site by typing it in the field below and choosing Save Settings.</i></b></p>';
		}

		/* Update settings table */
		if( isset( $_POST['save'] ) && $_POST['save'] == "true" ) {
			foreach( $sitesList as $key => $value ) {
				$siteToAdd = addslashes( $value['url'] );
				if( isset( $_POST['sites_google'] ) && in_array( $value['url'], $_POST['sites_google'] ) ) {
					if( in_array( $value['url'], $_POST['sites_google'] ) ) {
						/* Set to checked */
						if( array_key_exists( $siteToAdd , $siteSettings ) ) {
							$response = $mysql->qryDBupdate( $dbTable_settings, array('type'=>'sites_google', 'value'=>$siteToAdd), array('data'=>1) );
						} else {
							$response = $mysql->qryDBinsert( $dbTable_settings, "NULL, 'sites_google', '".$siteToAdd."', 1" );
						}
						if( $response ) {
							$siteSettings[$value['url']] = 1;
						}
					}
				} else {
					/* Set to unchecked */
					$response = $mysql->qryDBupdate( $dbTable_settings, array('type'=>'sites_google', 'value'=>$siteToAdd), array('data'=>0) );
					if( $response ) {
						$siteSettings[$value['url']] = NULL;
					}
				}
			}
		}
		?>

	<ul id="sites_google">
		<?php if( $sitesList ) { ?>
			<?php foreach( $sitesList as $value ) { ?>
				<li><input type="checkbox" name="sites_google[]" value="<?php echo $value['url'] ?>" <?php echo ( isset( $siteSettings[$value['url']] ) && $siteSettings[$value['url']] == 1 ? " checked" : "" )?> /><?php echo $value['url'] ?></li>
			<?php } ?>
		<?php } ?>
	</ul>
	<input type="hidden" id="save" name="save" value="true" />
	<input type="submit" value="Save Settings">

	</form>

	<hr>

<?php include_once('inc/html/_foot.php'); ?>