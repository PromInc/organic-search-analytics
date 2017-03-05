<?php $titleTag = "Settings | Organic Search Analytics"; ?>
<?php $dbTable_settings = 'settings'; ?>

<?php include_once('inc/html/_head.php'); ?>

<?php
$sitesList = array();
if( $isConfigured ) {
	/* Get sites settings */
	$siteSettings = array();
	$siteSettings['Google'] = $mysql->getSettings("sites_google");
	$siteSettings['Bing'] = $mysql->getSettings("sites_bing");
	
	/* Get list of sites */
	$wmtSites = array();
	$wmtSites['Google'] = $dataCapture->getSitesGoogleSearchConsole();
	$wmtSites['Bing'] = $dataCapture->getSitesBingWebmaster();
	
	/* Get combined sites */
	foreach( $wmtSites as $searchEngine => $sitesData ) {
		foreach( $sitesData as $key => $data ) {
			if( !isset( $sitesList[ $data['url'] ] ) ) {
				$sitesList[ $data['url'] ] = array( "availableTo" => array(), "enabled" => array() );
			}
			array_push( $sitesList[ $data['url'] ]['availableTo'], $searchEngine );
		}
	}

	/* Mark sites as enabled */
	foreach( $siteSettings as $searchEngine => $settings ) {
		foreach( $settings as $site => $value ) {
			if( $value == 1 ) {
				if( isset($sitesList[ $site ] ) ) {
					array_push( $sitesList[ $site ]['enabled'], $searchEngine );
				}
			}
		}
	}

	/* Update settings table */
	if( isset( $_POST['save_settings'] ) ) {
		foreach( $sitesList as $site => $data ) {
			// Set to checked
			$siteToAdd = addslashes( $site );
			
			foreach( $data['availableTo'] as $searchEngine ) {
				if( in_array( $searchEngine, $data['enabled'] ) && isset( $_POST['site_settings'][$site][$searchEngine] ) ) {
					//do nothing
				} elseif( in_array( $searchEngine, $data['enabled'] ) && !isset( $_POST['site_settings'][$site][$searchEngine] ) ) {
					//delete from database
					$response = $mysql->qryDBdelete( $mysql::DB_TABLE_SETTINGS, "type='sites_".strtolower($searchEngine)."' AND value='$siteToAdd'" );
					if( $response ) {
						if(($key = array_search($searchEngine, $sitesList[$site]['enabled'])) !== false) {
							unset($sitesList[$site]['enabled'][$key]);
						}
					}
				} elseif( !in_array( $searchEngine, $data['enabled'] ) && isset( $_POST['site_settings'][$site][$searchEngine] ) ) {
					//add to database
					$response = $mysql->qryDBinsert( $mysql::DB_TABLE_SETTINGS, "NULL, 'sites_".strtolower($searchEngine)."', '$siteToAdd', 1" );
					if( $response ) {
						array_push( $sitesList[$site]['enabled'], $searchEngine );
					}
				}
			}
		}
		$alert = array("type"=>"success", "message"=>"Settings Succesfully Saved");
	}
}
?>

	<?php include_once('inc/html/_alert.php'); ?>
	<h1>Organic Search Analytics | Settings</h1>

	<h2>Database Configuration</h2>
	<p>Settings for connecting to MySQL, Google OAuth 2.0, and Bing Webmaster Tools API.</p>
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
	<p>Select the sites and Search Engine to capture data from.</p>
	<h3>Not seeing the sites you expect?</h3>
	<h4>Google Search Console</h4>
	<p>Ensure that you have enabled the <b>Google Search Console API</b> and added your Google API Service Account email address as a user to each of your sites in Google Search Console.<br><a href="http://promincproductions.com/blog/google-api-access-google-search-analytics-from-google-search-console/" target="blank">Instruction on how to configure Google Search Analytics for API Access</a></p>
	<h4>Bing Webmaster Tools</h4>
	<p>Ensure that you have configured your site with <b>Bing Wembaster Tools</b> and that the API key set in the configuration matches that of the <br><a href="https://www.bing.com/webmaster/home/api" target="blank">API key found in Bing Webmaster Tools</a>.</p>

	<form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="post">
		<?php
		if( count( $sitesList ) == 0 ) {
			echo '<p><b><i>No sites found.  Add sites to either Google Search Console or Bing Wembaster Tools and configure the API settings.</i></b></p>';
		}
		?>

		<div id="site_settings">
			<div class="site_settings_heading">
				<div class="site_settings_label"><span>URL</span></div>
				<div class="site_settings_google"><span>Google Search Analytics</span></div>
				<div class="site_settings_bing"><span>Bing Webmaster Tools</span></div>
			</div>
			<?php if( $sitesList ) { ?>
				<?php foreach( $sitesList as $site => $sitesList ) { ?>
					<div class="site_settings_row">
						<div class="site_settings_label"><?php echo $site ?></div>
						<div class="site_settings_google">
							<?php $identifier = "Google"; ?>
							<?php if( in_array( $identifier, $sitesList['availableTo'] ) ) { ?>
							<input type="checkbox" name="site_settings[<?php echo $site ?>][Google]" value="<?php echo $site ?>" <?php echo ( in_array( $identifier, $sitesList['enabled'] ) ? " checked" : "" ) ?> />
							<?php } ?>
						</div>
						<div class="site_settings_bing">
							<?php $identifier = "Bing"; ?>
							<?php if( in_array( $identifier, $sitesList['availableTo'] ) ) { ?>
							<input type="checkbox" name="site_settings[<?php echo $site ?>][Bing]" value="<?php echo $site ?>" <?php echo ( in_array( $identifier, $sitesList['enabled'] ) ? " checked" : "" ) ?> />
							<?php } ?>
						</div>
					</div>
				<?php } ?>
			<?php } ?>
		</div>
		<input type="submit" name="save_settings" value="Save Settings" class="inputButton inputButtonSave">

	</form>

	<hr>

<?php include_once('inc/html/_foot.php'); ?>