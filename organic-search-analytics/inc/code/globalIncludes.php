<?php
	$now = time();

	$GLOBALS['basedirWebServer'] = $_SERVER['DOCUMENT_ROOT']."/";
	$GLOBALS['basedir'] = preg_replace( '/\\\/', '/', realpath(dirname(__FILE__).'/../../').'/' );
	$GLOBALS['appInstallDir'] = str_replace( $GLOBALS['basedirWebServer'], "", $GLOBALS['basedir'] );
	$GLOBALS['file_name'] = basename( $_SERVER['SCRIPT_FILENAME'], ".php" );

	$isConfigured = file_exists($GLOBALS['basedir'].'config/config.php');

	include_once( $GLOBALS['basedir'].'inc/code/core.php' ); //Core functions
	$core = new Core(); //Load core

	include_once( $GLOBALS['basedir'].'inc/code/debugLogger.php' ); //Debug Logger

	if( $isConfigured ) {
		require_once( $GLOBALS['basedir'].'config/config.php' );  //Credentials & Configuration

		if( defined('config::DEBUG_LOGGER') ) {
			if( config::DEBUG_LOGGER == Core::ENABLED ) {
				$debug = new DebugLogger(); //Load Debugging Logger
			}
		} else {
			$alert = array("type"=>"warning", "message"=>"Go to the <a href=\"settings-configure.php\"><b>Settings Configuration</b></a> page and click the <b>Save</b> button.");
		}

		include_once( $GLOBALS['basedir'].'inc/code/mysql.php' ); //Database Connection
		include_once( $GLOBALS['basedir'].'inc/code/gapiOauth.php' ); //Google API Oauth
		include_once( $GLOBALS['basedir'].'inc/code/wmtimport.php' ); //WMT CSV import functions
		include_once( $GLOBALS['basedir'].'inc/code/dataCapture.php' ); //Data capturing functions
		include_once( $GLOBALS['basedir'].'inc/code/reports.php' ); //Reporting functions

		include_once( $GLOBALS['basedir'].'apis/Google/autoload.php' ); //Google API
		include_once( $GLOBALS['basedir'].'apis/Bing/Webmasters.php' ); //Bing Search API

		/* Load classes */
		$mysql = new MySQL(); //Load MySQL
		$dataCapture = new DataCapture(); //Load Data Capturing tools

		$GLOBALS['db'] = $core->mysql_connect_db(); // Connect to DB
	} else {
		$alert = array("type"=>"warning", "message"=>"Please set your configuration settings before procedding.  <a href=\"settings-configure.php\">Configure Now</a>");
	}
?>
