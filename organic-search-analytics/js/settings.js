$(document).ready(function(){

	$("#sites_google_new_text").on( 'input', function() {
		updateNewSiteText( this );
	});
	$("#sites_google_new_text").on( 'keyup', function() {
		updateNewSiteText( this );
	});

	function updateNewSiteText( object ) {
		$("#sites_google_new_check").val( object.value );
		if( object.value.length > 0 ) {
			$("#sites_google_new_check").prop('checked', true);
		} else {
			$("#sites_google_new_check").prop('checked', false);
		}
	}

});