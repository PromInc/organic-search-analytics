<?php
	/**
	 *  PHP class for debug logging functions
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

	class DebugLogger
	{


		const LOG_DIR_DEFAULT = "log";
		const LOG_FILE_DEFAULT = "general.log";


		/**
		 *  Log message to file
		 *
		 *  @param   message
		 *  @param   level
		 *  @param   dir
		 *  @param   file
		 */
		public function debugLog($message, $level, $file = self::LOG_FILE_DEFAULT, $dir = self::LOG_DIR_DEFAULT) {
			error_log(
				$this->makeLogMessage($message,$level=Core::DEBUG),
				3,
				$dir.Core::DS.$file
			);
		}


		/**
		 *  Generate log message
		 *
		 *  @param   message
		 *  @param   level
		 *
		 *  @returns   String   Date Level Message File Line,
		 */
		public function makeLogMessage($message = "", $level = Core::DEBUG) {
			$trace = debug_backtrace();
			return  date('c').
					Core::TAB.
					$level.
					Core::TAB.
					$message.
					Core::TAB.
					"in ".$trace[1]['file'].
					" line ".$trace[1]['line'].
					Core::NEWLINE
					;
		}


	}
?>