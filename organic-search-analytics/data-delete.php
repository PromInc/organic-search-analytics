<?php $titleTag = "Data Delete | Organic Search Analytics"; ?>

<?php include_once('inc/html/_head.php'); ?>

	<?php include_once('inc/html/_alert.php'); ?>
	<h1>Organic Search Analytics | Delete Data</h1>

	<h2>Google Search Console</h2>
	<hr>
	<h3>Search Analytics</h3>
	<p>If you feel there is an issue with the data captured for a particular date, you can delete the data for that day from this page.  Once a date has been deleted, you will need to re-capture the data using the <a href="data-capture.php">data capture</a> page.</p>
	<p>Data deletion is not permitted outside the date range of when data is allowed from Google Search Console.</p>
	<ul>
		<li>Google offers data for approximelty <?php echo $dataCapture::GOOGLE_SEARCH_ANALYTICS_MAX_DAYS ?> days</li>
		<li>The most recent data is <?php echo $dataCapture::GOOGLE_SEARCH_ANALYTICS_MAX_DATE_OFFSET ?> days from today.</li>
	</ul>
	<?php $dateRange = $dataCapture->getGoogleAvailableDates(); ?>
	<p>Date Range Available for deletion: <?php echo $dateRange['start'] ?> to <?php echo $dateRange['end'] ?></p>


	<p>Domains:</p>
	<?php
		$domains = $mysql->getSettings("sites_google", "1");
		$catId = "googleSearchAnalytics";

		foreach( $domains as $domain => $values ) {
			echo '<ul class="searchEngine" id="'.$catId.'">';
				echo '<li>'.$domain.'</li>';

				$googleSearchAnalyticsDates = $dataCapture->getGoogleDatesWithData($domain, true);

				if( count( $googleSearchAnalyticsDates ) > 0 ) {

					echo '<ul>';
						foreach( $googleSearchAnalyticsDates as $date ) {
							?>
							<label for="<?php echo $date['date'] ?>"><?php echo $date['date'] ?></label>
							<input type="checkbox" name="<?php echo $domain ?>" value="<?php echo $date['date'] ?>" id="<?php echo $date['date'] ?>">
							<br/>
							<?php
						}
					echo "</ul>";
				} else {
					echo "<p>No dates have data that can be deleted.</p>";
				}
			echo '</ul>';
		}
	?>
	<hr>


<?php include_once('inc/html/_foot.php'); ?>