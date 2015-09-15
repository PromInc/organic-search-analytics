<?php

/**
 * Microsoft Bing Webmaster Tools API
 *
 * Reference:
 * https://msdn.microsoft.com/en-us/library/jj572365.aspx
 * https://msdn.microsoft.com/en-us/library/hh969359.aspx
 *
 * Copyright 2015 PromInc Productions. All Rights Reserved.
 *
 * @author: Brian Prom <prombrian@gmail.com>
 * @link:   http://promincproductions.com/blog/brian/
 */
 
class BingWebmasters
{

	const URL_BING_WEBMASTERS_JSON = 'https://ssl.bing.com/webmaster/api.svc/json/';

	/**
	 *  Get data from Bing Webmaster Tools API
	 *
	 *  List of avaiable methods:
	 *  https://msdn.microsoft.com/en-us/library/jj572365.aspx
	 *
	 *  @param $api_key     String   API Key
	 *  @param $method     String   API Method
	 *  @param $siteUrl     String   Website URL that correlates to Bing Webmaster Tools
	 *
	 *  @returns   Object   
	 */
	public function requestApi( $api_key, $method, $siteUrl = NULL ) {
		$url = self::URL_BING_WEBMASTERS_JSON.$method.'?apikey='.$api_key;

		if( $siteUrl ) {
			$url .= '&siteUrl='.$siteUrl;
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		if( ! $result = curl_exec($ch) ) {
			$alert = array("type"=>"error", "message"=>"Error communicating with the Bing API");
		}

		curl_close($ch);

		return $result;
	}

}
?>