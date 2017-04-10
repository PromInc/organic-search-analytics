<?php $titleTag = "Data Capture | Organic Search Analytics"; ?>

<?php include_once('inc/html/_head.php'); ?>

	<?php include_once('inc/html/_alert.php'); ?>
	<h1>Organic Search Analytics | Capture Data</h1>

	<div id="importAllButtons">
		<div>Import all non-imported data: 
			<span class="button buttonImportAllRun" onclick="importAllRun()">Run</span>
			<span class="button buttonImportAllStop" onclick="importAllStop()" style="display:none;">Stop</span>
		</div>
	</div>
	<hr>

	<h2>Google Search Console</h2>
	<hr>
	<h3>Search Analytics</h3>
	<ul>
		<li>Google updates API data every day.</li>
		<li>Google data is imported to the database on a daily basis.</li>
		<li>Any dates with data not in the database are listed below and ready for import.</li>
		<li>Google offers data for approximelty <?php echo $dataCapture::GOOGLE_SEARCH_ANALYTICS_MAX_DAYS ?> days</li>
		<li>The most recent data is <?php echo $dataCapture::GOOGLE_SEARCH_ANALYTICS_MAX_DATE_OFFSET ?> days from today.</li>
	</ul>

	<p>Domains:</p>
	<?php
		$domains = $mysql->getSettings("sites_google", "1");
		$catId = "googleSearchAnalytics";
	?>
	<div id="<?php echo $catId ?>">
	<?php
	if( $domains ) {
		foreach( $domains as $domain => $values ) {
			echo '<ul class="searchEngine">';
				echo '<li>'.$domain.'</li>';

				$googleSearchAnalyticsDates = $dataCapture->checkNeededDataGoogleSearchAnalytics($domain);

				if( count( $googleSearchAnalyticsDates['datesWithNoData'] ) > 0 ) {

					echo '<ul>';
						foreach( $googleSearchAnalyticsDates['datesWithNoData'] as $date ) {
							echo '<li domain="'.$domain.'" date="'.$date.'"><div class="button buttonImport" onclick="ajaxScript(\''.$catId.'\', \'data-capture-run.php\', \''.$domain.'\', \''.$date.'\', \'postGoogleSearchAnalyticsAjax\')">Import data for: ' . $date . ' ></div></li>'."\n";
						}
					echo "</ul>";
				} else {
					echo "<p>The database is up to date.</p>";
				}
			echo '</ul>';
		}
	} else {
		echo '<ul><li><i>No domains are configured.</i>  <a href="settings.php">Configure Domains for Data Capture.</a></li></ul>';
	}
	?>
	</div>
	<hr>


	<h2>Bing Webmaster Tools</h2>
	<hr>
	<h3>Search Keywords</h3>
	<ul>
		<li>Bing adds new data to their API feed every Saturday.</li>
		<li>Bing data is recorded on a weekly basis.</li>
		<li>Any dates with data not recorded in the database will be added when the import is run.</li>
	</ul>

	<p>Domains:</p>
	<?php
		$domains = $mysql->getSettings("sites_bing", "1");
		$catId = "bingSearchKeywords";
	?>
	<div id="<?php echo $catId ?>">
	<?php
	if( $domains ) {
		foreach( $domains as $domain => $values ) {
			echo '<ul class="searchEngine">';
				echo '<li>'.$domain.'</li>';
					$date = date( "Y-m-d", $now );

					echo '<ul>';
						echo '<li domain="'.$domain.'" date="'.$date.'"><div class="button buttonImport" onclick="ajaxScript(\''.$catId.'\', \'data-capture-run.php\', \''.$domain.'\', \''.$date.'\', \'postBingSearchKeywordsAjax\')">Import data ></div></li>'."\n";
					echo '</ul>';
			echo '</ul>';
		}
	} else {
		echo '<ul><li><i>No domains are configured.</i>  <a href="settings.php">Configure Domains for Data Capture.</a></li></ul>';
	}
	?>
	</div>
	<hr>

<?php include_once('inc/html/_foot.php'); ?>