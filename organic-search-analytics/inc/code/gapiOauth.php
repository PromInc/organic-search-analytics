<?php
	/**
	 *  PHP class for oAuth 2.0 connection to Google Search Console
	 *
	 *  Copyright 2017 PromInc Productions. All Rights Reserved.
	 *
	 *  Licensed under the Apache License, Version 2.0 (the "License");
	 *  you may not use this file except in compliance with the License.
	 *  You may obtain a copy of the License at
	 *
	 *     http://www.apache.org/licenses/LICENSE-2.0
	 *
	 *  Unless required by applicable law or agreed to in writing, software
	 *  distributed under the License is distributed on an "AS IS" BASIS,
	 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	 *  See the License for the specific language governing permissions and
	 *  limitations under the License.
	 *
	 *  @author: Brian Prom <prombrian@gmail.com>
	 *  @link:   http://promincproductions.com/blog/brian/
	 */

	class GAPIoAuth
	{

		/**
		 *  Connect to Google API via oAuth 2.0
		 *
		 *  @return Object  Returns oAuth response
		 */
		public function LogIn()
		{
			/* OAUTH 2.0 */
			$private_key = self::getPrivateKey();
			$scopes = array('https://www.googleapis.com/auth/webmasters.readonly');
			$credentials = new Google_Auth_AssertionCredentials(
					Config::OAUTH_CREDENTIALS_EMAIL,
					$scopes,
					$private_key
			);

			$client = new Google_Client();
			$client->setAssertionCredentials($credentials);
			if ($client->getAuth()->isAccessTokenExpired()) {
				$client->getAuth()->refreshTokenWithAssertion();
			}

			return $client;
		}
		
		
		public function getPrivateKey() {
			return file_get_contents( $GLOBALS['basedir'].'/config/'.Config::OAUTH_CREDENTIALS_PRIVATE_KEY_FILE_NAME );
		}

	}
?>