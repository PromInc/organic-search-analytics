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
	$siteSettings['Settings'] = $mysql->getSettings("settings");

	/* Get list of sites */
	$wmtSites = array();
	$wmtSites['Google'] = $dataCapture->getSitesGoogleSearchConsole();
	$wmtSites['Bing'] = $dataCapture->getSitesBingWebmaster();

	/* Get combined sites */
	foreach( $wmtSites as $searchEngine => $sitesData ) {
		foreach( $sitesData as $key => $data ) {
			if( isset( $data['warn'] ) ) {
				$alert = array("type"=>"warn", "message"=>$data['warn']);
			} else {
				if( !isset( $sitesList[ $data['url'] ] ) ) {
					$sitesList[ $data['url'] ] = array( "availableTo" => array(), "enabled" => array() );
				}
				array_push( $sitesList[ $data['url'] ]['availableTo'], $searchEngine );
			}
		}
	}

	/* Mark sites as enabled */
	foreach( $siteSettings as $searchEngine => $settings ) {
		if( $settings ) {
			foreach( $settings as $site => $value ) {
				if( $value == 1 ) {
					if( isset($sitesList[ $site ] ) ) {
						array_push( $sitesList[ $site ]['enabled'], $searchEngine );
					}
				}
			}
		}
	}

	/* Define settings for the page */
	$settingsSetup = array(
		"google" => array(
			"description" => "Customize what infomration is captured from Google",
			"label" => "Google",
			"products" => array(
				"search_console" => array(
					"description" => "Select which dimensions you'd like to capture.<br><i style=\"font-size:.9em; color: #565656; display: inline-block; padding-left: 1%;\"><b>NOTE</b>: Adding more dimensions could limit the accuracy of the data you obtain.  The Google API provides up to 5,000 results per request.<br>To illustrate this example, if you select <b>Queries</b> only lets say there were 10 queries for that day.  If you then add <b>Country</b> and each query was returned in 5 countries each, you now are receiving 50 results (10 queries X 5 countries).  Adding <b>Device Type</b>, assuming each query in each country was performed on each <b>Device Type</b> the number of results would grow to 150 results.  You can see how as you add additional dimensions the number of results can grow quickly.  Depending on frequently your site shows in the SERPs this may or may not impact your ability to capture all of the avaialbe data.</i>",
					"label" => "Search Console",
					"section" => array(
						"dimensions" => array(
							"description" => " section description",
							"heading" => "Dimensions",
							"heading_options" => "Enable/Disable",
							"options" => array(
								"query" => array(
									"label" => "Queries",
									"type" => "checkbox",
									"note" => ""
								),
								"page" => array(
									"label" => "Page (URL)",
									"type" => "checkbox",
									"note" => ""
								),
								"device" => array(
									"label" => "Device Type",
									"type" => "checkbox",
									"note" => "Will return <i>Desktop</i>, <i>Mobile</i>, or <i>Tablet</i>"
								),
								"country" => array(
									"label" => "Country",
									"type" => "checkbox",
									"note" => ""
								)
							)
						)
					)
				)
			)
		)
	);

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

		/* Loop through settings and update database where necessary */
		foreach( $settingsSetup as $searchEngine => $settingsGroup ) {
			foreach( $settingsGroup['products'] as $productName => $productGroup ) {
				foreach( $productGroup['section'] as $sectionName => $sectionGroup ) {
					foreach( $sectionGroup['options'] as $optionName => $optionGroup ) {
						$value = $searchEngine."_".$productName."_".$sectionName."_".$optionName;
						switch( $optionGroup['type'] ) {
							case "checkbox":
								if( isset( $_POST['settings'][$searchEngine][$productName][$sectionName][$optionName] ) ) { $data = "On"; } else { $data = "Off"; }
								break;
							default:
								$data = $_POST['settings'][$searchEngine][$productName][$sectionName][$optionName];
						}

						// Update the database
						if( !isset( $siteSettings['Settings'][$value] ) ) {
							$response = $mysql->qryDBinsert( $mysql::DB_TABLE_SETTINGS, "NULL, 'settings', '$value', '$data'" );
						} elseif ( isset( $siteSettings['Settings'][$value] ) && $siteSettings['Settings'][$value] != $data ) {
							$response = $mysql->qryDBupdate( $mysql::DB_TABLE_SETTINGS, array( 'type' => 'settings', 'value' => $value ), array( 'data' => $data ) );
						}

						// Update settings loaded ot page
						$siteSettings['Settings'][$value] = $data;
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
	<div class="settings-block">
		<p>Select the sites and Search Engine to capture data from.</p>
	</div>
	<h3>Not seeing the sites you expect?</h3>
	<h4>Google Search Console</h4>
	<div class="settings-block">
		<p>Ensure that you have enabled the <b>Google Search Console API</b> and added your Google API Service Account email address as a user to each of your sites in Google Search Console.<br><a href="http://promincproductions.com/blog/google-api-access-google-search-analytics-from-google-search-console/" target="blank">Instruction on how to configure Google Search Analytics for API Access</a></p>
	</div>


	<h4>Bing Webmaster Tools</h4>
	<div class="settings-block">
		<p>Ensure that you have configured your site with <b>Bing Wembaster Tools</b> and that the API key set in the configuration matches that of the <br><a href="https://www.bing.com/webmaster/home/api" target="blank">API key found in Bing Webmaster Tools</a>.</p>
	</div>

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

		<?php /* Settings options as defined in array above */ ?>
		<?php foreach( $settingsSetup as $searchEngine => $settingsGroup ) {?>
			<h2><?php echo $settingsGroup['label'] ?></h2>
			<?php if( strlen( $settingsGroup['description'] ) > 0 ) { ?><div><?php echo $settingsGroup['description'] ?></div><?php } ?>
			<?php foreach( $settingsGroup['products'] as $productName => $productGroup ) { ?>
				<div class="settings-block">
				<h3><?php echo $settingsGroup['label'] . " " . $productGroup['label'] ?> Options</h3>
					<?php if( strlen( $productGroup['description'] ) > 0 ) { ?><div><?php echo $productGroup['description'] ?></div><?php } ?>
					<?php foreach( $productGroup['section'] as $sectionName => $sectionGroup ) { ?>
						<div class="site_settings_heading">
							<div class="site_settings_label"><span><?php echo $sectionGroup['heading'] ?></span></div>
							<div class="site_settings_google"><span><?php echo $sectionGroup['heading_options'] ?></span></div>
						</div>
						<?php foreach( $sectionGroup['options'] as $optionName => $optionGroup ) { ?>
							<div class="site_settings_row">
								<div class="site_settings_label"><?php echo $optionGroup['label'] ?></div>
								<div>
								<?php
								switch( $optionGroup['type'] ) {
									case "checkbox":
										?><input type="checkbox" name="settings[<?php echo $searchEngine ?>][<?php echo $productName ?>][<?php echo $sectionName ?>][<?php echo $optionName ?>]" value="<?php echo $optionName ?>" <?php echo ( $siteSettings['Settings'][$searchEngine.'_'.$productName.'_'.$sectionName.'_'.$optionName] == "On" ? " checked" : "" ) ?> /><?php
										break;
									default:
								}
								?>
								</div>
								<div>
									<?php echo $optionGroup['note'] ?>
								</div>
							</div>
						<?php } ?>
					<?php } ?>
				</div>
			<?php } ?>
		<?php } ?>

		<input type="submit" name="save_settings" value="Save Settings" class="inputButton inputButtonSave">
	</form>
	<hr>

<?php include_once('inc/html/_foot.php'); ?>