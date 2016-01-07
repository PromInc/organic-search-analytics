<?php $titleTag = "Organic Search Analytics"; ?>

<?php include_once('inc/html/_head.php'); ?>

	<?php include_once('inc/html/_alert.php'); ?>
	<h1>Organic Search Analytics | Capture and Import</h1>
	<div>The below tools expedite grabbing data from Google Webmaster Tools</div>
	<ul>
		<li>
			<h2>Data Capture and Import</h2>
			<div>Capture and import data from Organic Search Analytics resources.</div>
			<ul>
				<li>Google Search Console - Search Analytics</li>
				<li>Bing Webmaster Tools - Search Keywords</li>
			</ul>
			<div><a href="data-capture.php">Data Capture and Import</a></div>
			<ul>
				<li>Delete Data</li>
				<ul>
					<li>On rare occasions you may need to delete data for a given day.  This utility will allow you to delete the data for a particular day for a domain.  You can then re-capture the data for that day.  Warning: This will remove data from your database - use at your own risk.</li>
					<li><a href="data-delete.php">Delete Data</a></li>
				</ul>
			</ul>
		</li>

		<li>
			<h2>Settings</h2>
			<div>Configure various settings for the Organic Search Analytics capture and import tool</div>
			<div><a href="settings.php">Configure Settings</a></div>
		</li>

		<li>
			<h2>Reports</h2>
			<div>View reports of imported Data.</div>
			<div><a href="report.php">View Reports</a></div>
		</li>

		<li>
			<h2>Upgrade</h2>
			<div>When updating between certain versions, database or other changes may have occured which will require an additional step to complete the upgrade process.</div>
			<div>After updating the codebase from <a href="https://github.com/PromInc/organic-search-analytics" target="_blank">Github</a>, check this upgrade page to see if the version upgrade you performed requires this additional step.</div>
			<div>If it does, it's as simple as clicking on the upgrade for your specific version to have the necessary updates implemented.</div>
			<div><a href="upgrade.php">Upgrade Scripts</a></div>
		</li>
	</ul>

<?php include_once('inc/html/_foot.php'); ?>