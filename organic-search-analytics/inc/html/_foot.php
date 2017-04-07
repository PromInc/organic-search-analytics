		</div>

		<?php if( isset( $GLOBALS['scriptName'] ) ) { ?>
			<?php if( $GLOBALS['scriptName'] == "report.php" ) { ?>
				<script type="text/javascript" src="js/report.js"></script>
			<?php } ?>
		<?php } ?>

		<footer>
			<div class="floatleft">
				Developed by <a href="http://www.promincproductions.com/blog" target="_blank">Brian Prom</a>
			</div>
			<div id="versionBlock" class="floatright">
				<?php $currerntVersion = trim( file_get_contents( ( isset( $GLOBALS['basedir'] ) ? $GLOBALS['basedir'] : '' ) . 'version.txt' ) ); ?>
				<span>ver <?php echo $currerntVersion; ?></span>
				<span id="upgradeVersion" class="floatright" style="display:none;">A newer version is available.  <a>Download <span id="upgradeVersionNumber"></span> now!</a></span>
				<script type="text/javascript">
				$.get('https://api.github.com/repos/prominc/organic-search-analytics/releases/latest', function (data) {
					var latestVersion = data.tag_name;
					if( latestVersion > '<?php echo trim( $currerntVersion ) ?>' ) {
						$('#upgradeVersion a').attr('href', data.zipball_url);
						$('#upgradeVersion a span#upgradeVersionNumber').text(latestVersion);
						$('#upgradeVersion').show();
					}
				});
				</script>
			</div>
			<div class="clear"></div>
		</footer>
	</body>
</html>