<div id="saveReportForm" class="ajaxFormWrapper">
	<div id="saveReportCTA" class="button" onclick="showReportSave(this)">Save this Report to Quick Links >></div>
	<div class="ajaxFromContent" style="display:none;">
		<form id="save_report" class="ajaxForm" type="saveReport">
			<input type="hidden" name="reportParams" value="{{report_params}}">
			<p>
				<label for="reportName">Report Name:</label><br>
				<input type="text" id="reportName" name="reportName">
			</p>
			<p>
				<label for="reportCategory">Report Category:</label><br>
				<label for="reportCatExistingCheck">Existing</label>
				<input type="radio" name="reportCatType" id="reportCatType" value="existing" checked>
				{{report_categories}}
				<br>
				<label for="reportCatNew">New</label>
				<input type="radio" name="reportCatType" id="reportCatType" value="new">
				<input type="text" id="reportCatNew" name="reportCatNew">
			</p>
			<p>
				<input type="submit" class="button" id="reportSave" name="reportSave" value="Save Report to Quick Links">
			</p>
		</form>
	</div>
</div>