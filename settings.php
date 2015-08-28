<?php $titleTag = "Settings | Organic Search Analytics"; ?>
<?php $dbTable_settings = 'settings'; ?>

<?php include_once('inc/html/_head.php'); ?>

	<h1>Organic Search Analytics | Settings</h1>

	<h2>Configuration</h2>
	<?php

		if( !$isConfigured ) {
			echo '<p>Configuration file is missing</p>';
		} else {
			echo '<p>Configuration is set.  No further action is needed at this time.</p>';
		}
	?>
	<hr>
	<h3>Google</h3>
	<h4>Sites</h4>
	<p>The sites listed here directly correlate to the URL for the site you have configured in <b>Google Search Console</b>.  Ensure they match what is entered in <b>Google Search Console</b>.</p>
	<p>Use the checkbox to enable/disable this site from being available to the <b>Data Capture</b> tool.</p>

	<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="post">
		<?php
		/* Get list of sites */
		$sitesList = $mysql->getSettings("sites_google");

		if( count( $sitesList ) == 0 && !isset( $_POST['sites_google'] ) ) {
			echo '<p><b><i>No sites are configured at this time.  Add a site by typing it in the field below and choosing Save Settings.</i></b></p>';
		}

		if( isset( $_POST['sites_google'] ) ) {
			foreach( $sitesList as $site => $value ) {
				if( in_array( $site, $_POST['sites_google'] ) ) {
					/* Set to checked */
					$response = $mysql->qryDBupdate( $dbTable_settings, array('type'=>'sites_google', 'value'=>$site), array('data'=>1) );
					if( $response ) {
						$sitesList[$site] = 1;
					}
				} else {
					/* Set to unchecked */
					$response = $mysql->qryDBupdate( $dbTable_settings, array('type'=>'sites_google', 'value'=>$site), array('data'=>0) );
					if( $response ) {
						$sitesList[$site] = NULL;
					}
				}
			}

			/* Add new sites */
			if( isset( $_POST['sites_google'] ) ) { 
				foreach( $_POST['sites_google'] as $site ) {
					if( !$sitesList || !array_key_exists( $site, $sitesList ) ) {
						$mysql->qryDBinsert($dbTable_settings,"NULL,'sites_google','".$site."',1");
						$sitesList[$site] = 1;
					}
				}
			}
		}
		?>

	<ul id="sites_google">
		<?php if( $sitesList ) { ?>
			<?php foreach( $sitesList as $site => $value ) { ?>
				<li><input type="checkbox" name="sites_google[]" value="<?php echo $site ?>" <?php if($value==1){echo " checked";}?> /><?php echo $site ?></li>	
			<?php } ?>
		<?php } ?>
		<li><input id="sites_google_new_check" type="checkbox" name="sites_google[]" value="" /><input id="sites_google_new_text" type="text"><i>Add new site here</i></li>
	</ul>
	
	<h4>Data/Reports to Capture from Google</h4>
	<p>Show a list of options the user can enable/disable here.  NOTE: At this point the only option would be <b>Search Analytics</b> as I haven't built out any other features yet.</p>

	<input type="submit" value="Save Settings">

	</form>

	<hr>

<?php include_once('inc/html/_foot.php'); ?>