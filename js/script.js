/**
 *  Sends an AJAX request for data to capture
 *
 *  @param catId     String   Category ID for which data is being captured
 *  @param script     String   File name/path to run
 *  @param domain     String   Domain name to capture data for
 *  @param date     String   Date to search.  YYYY-MM-DD
 *  @param callback     String   Function to run upon success
 *
 *  @returns   Object   Database records.  MySQL object
 */
function ajaxScript(catId, script, domain, date, callback) {
	jQuery.ajax({
		type : "GET",
		url : script,
		data : {
			domain : domain,
			date : date
		},
		beforeSend: function( xhr ) {
			$("#"+catId+" li[domain='"+domain+"'][date='"+date+"']").append('<span style="margin-left: 5px;">Processing...</span>');
		},
		success: function(data) {
			if( callback ) {
				window[callback](catId, domain, date, data);
			}
		},
		error: function(xhr, status, errorThrown) {
			$("#"+catId+" li[domain='"+domain+"'][date='"+date+"']").append('<span style="margin-left: 5px;">Error, try again.</span>');
console.log( xhr );
console.log( status );
console.log( errorThrown );
		}
	});
}

function postGoogleSearchAnalyticsAjax(catId, domain, date, data) {
	$("#"+catId+" li[domain='"+domain+"'][date='"+date+"']").empty().append(data);

	/* If import all is set, continue processing */
	if( window.importAllProcessing == true ) {
		importAllRun(catId);
	}
}

/* Import All functionality | START */
importAllStop();
function importAllRun(catId) {
	window.importAllProcessing = true;

	$(".importAllButtons[category='"+catId+"'] .buttonImportAllRun").hide();
	$(".importAllButtons[category='"+catId+"'] .buttonImportAllStop").show();

	/* Trigger the first import button */
	if( $("#"+catId+" .buttonImport").eq(0) ) {
		$("#"+catId+" .buttonImport").eq(0).trigger("click");
	} else {
		console.log("No more data to import");
		window.importAllProcessing = false;
	}
}

function importAllStop() {
	window.importAllProcessing = false;
	$(".importAllButtons .buttonImportAllStop").hide();
	$(".importAllButtons .buttonImportAllRun").show();
}
/* Import All functionality | END */