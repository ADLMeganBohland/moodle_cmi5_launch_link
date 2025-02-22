<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 *Class to handle Assignable Units 
*
* @copyright  2023 Megan Bohland
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

class Au {
// Properties

	public $id, $url, $type, $lmsid, $grade, $scores, $title, $moveon, $auindex, $parents, $objectives, $description, $activitytype, $launchmethod, $masteryscore, $satisfied, $launchurl, $sessionid, $sessions, $progress, $noattempt, $completed, $passed, $inprogress;

	// Methods
	//Constructs AUs. Is fed array and where array key matches property, sets the property.  
	function __construct($statement){

		foreach($statement as $key => $value){

		$this->$key = ($value);
		}	
	}
}
?>