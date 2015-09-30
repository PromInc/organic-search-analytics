<?php
	$now = time();

	$GLOBALS['basedirWebServer'] = $_SERVER['DOCUMENT_ROOT']."/";
	$GLOBALS['basedir'] = preg_replace( '/\\\/', '/', realpath(dirname(__FILE__).'/../../').'/' );
	$GLOBALS['appInstallDir'] = str_replace( $GLOBALS['basedirWebServer'], "", $GLOBALS['basedir'] );	

	$isConfigured = file_exists($GLOBALS['basedir'].'config/config.php');

	if( $isConfigured ) {
		require_once( $GLOBALS['basedir'].'config/config.php' );  //Credentials & Configuration

		include_once( $GLOBALS['basedir'].'inc/code/core.php' ); //Core functions
		include_once( $GLOBALS['basedir'].'inc/code/mysql.php' ); //Database Connection
		include_once( $GLOBALS['basedir'].'inc/code/gapiOauth.php' ); //Google API Oauth
		include_once( $GLOBALS['basedir'].'inc/code/wmtimport.php' ); //WMT CSV import functions
		include_once( $GLOBALS['basedir'].'inc/code/dataCapture.php' ); //Data capturing functions
		include_once( $GLOBALS['basedir'].'inc/code/reports.php' ); //Reporting functions

		include_once( $GLOBALS['basedir'].'apis/Google/autoload.php' ); //Google API
		include_once( $GLOBALS['basedir'].'apis/Bing/Webmasters.php' ); //Bing Search API

		/* Load classes */
		$core = new Core(); //Load core
		$mysql = new MySQL(); //Load MySQL
		$dataCapture = new DataCapture(); //Load Data Capturing tools

		$GLOBALS['db'] = $core->mysql_connect_db(); // Connect to DB
	} else {
		$alert = array("type"=>"warning", "message"=>"Please set your configuration settings before procedding.  <a href=\"settings-configure.php\">Configure Now</a>");
	}
?>