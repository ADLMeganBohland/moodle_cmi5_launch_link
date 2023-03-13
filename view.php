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
 * Prints a particular instance of cmi5launch
 *
 * @package mod_cmi5launch
 * @copyright  2013 Andrew Downes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require('header.php');

//Classes for connecting to Progress class - MB
require_once("$CFG->dirroot/mod/cmi5launch/cmi5PHP/src/Progress.php");

//Classes for connecting to CMI5 player
require_once("$CFG->dirroot/mod/cmi5launch/cmi5PHP/src/cmi5Connector.php");
require_once("$CFG->dirroot/mod/cmi5launch/cmi5PHP/src/cmi5_table_connectors.php");
require_once("$CFG->dirroot/mod/cmi5launch/cmi5PHP/src/ausHelpers.php");

//MB
    //bring in functions from classes cmi5Connector/Cmi5Tables
    $progress = new progress;
    $auHelper = new Au_Helpers;
    //bring in functions from class cmi5_table_connectors
    $getProgress = $progress->getRetrieveStatement();
    $createAUs = $auHelper->getCreateAUs();
    //Bring in functions
    //bring in functions from classes cmi5Connector/Cmi5Tables
    $connectors = new cmi5Connectors;
    $tables = new cmi5Tables;
    //bring in functions from class cmi5_table_connectors
    //$createCourse = $connectors->getCreateCourse();
//    $retrieveAus = $connectors-> getRetrieveAus();
//Why are we creating a record here? They are already made...
//
  //  $populateTable = $tables->getPopulateTable();

//MB
//Do we still need this?
// Trigger module viewed event.
$event = \mod_cmi5launch\event\course_module_viewed::create(array(
    'objectid' => $cmi5launch->id,
    'context' => $context,
));
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('cmi5launch', $cmi5launch);
$event->add_record_snapshot('course_modules', $cm);
$event->trigger();

// Print the page header.
//MB
//This seems ok, we still want the course name, we are going to have aus
//on the next page
$PAGE->set_url('/mod/cmi5launch/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($cmi5launch->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

$PAGE->requires->jquery();

// Output starts here.
echo $OUTPUT->header();

global $cmi5launch;
//Take the results of created course and save new course id to table
//Load a record and parse for aus
//MB
//Hmmmmm should this be it's own
// Reload cmi5 instance.
$record = $DB->get_record('cmi5launch', array('id' => $cmi5launch->id));
//Retrieve the saved course results
//Omg, here is the prob I am not actually accessing db!
//I am doing it backlwards! lol
//Retrieve saved AU
$auList = json_decode($record->aus, true);
/*
echo"<br>";
echo"What is auList being returned as?" ;
var_dump($auList);
echo"<br>";
*/
$aus = $createAUs($auList);

if ($cmi5launch->intro) { 
    // Conditions to show the intro can change to look for own settings or whatever.
    echo $OUTPUT->box(
        format_module_intro('cmi5launch', $cmi5launch, $cm->id),
        'generalbox mod_introbox',
        'cmi5launchintro'
    );
}

// TODO: Put all the php inserted data as parameters on the functions and put the functions in a separate JS file.
?>

    <script>
      
        function key_test(registration) {
        
            //Onclick calls this
            if (event.keyCode === 13 || event.keyCode === 32) {
                //MBMBMBMBMB
                //Ok, so we DON't want this right? This might be where
                //its good to put in our redirect!!
                mod_cmi5launch_launchexperience(registration);
          
            }
        }
        
        //function to be run on onclick

        // Function to run when the experience is launched.
        function mod_cmi5launch_launchexperience(registration) {
            // Set the form paramters.
            $('#launchform_registration').val(registration);
            // Post it.
            $('#launchform').submit();
            // Remove the launch links.
            $('#cmi5launch_newattempt').remove();
            $('#cmi5launch_attempttable').remove();
            //Add some new content.
            if (!$('#cmi5launch_status').length) {
                var message = "<? echo get_string('cmi5launch_progress', 'cmi5launch'); ?>";
                $('#region-main .card-body').append('\
                <div id="cmi5launch_status"> \
                    <span id="cmi5launch_completioncheck"></span> \
                    <p id="cmi5launch_attemptprogress">' + message + '</p> \
                    <p id="cmi5launch_exit"> \
                        <a href="complete.php?id=<?php echo $id ?>&n=<?php echo $n ?>" title="Return to course"> \
                            Return to course \
                        </a> \
                    </p> \
                </div>\
            ');
            }
            $('#cmi5launch_completioncheck').load('completion_check.php?id=<?php echo $id ?>&n=<?php echo $n ?>');
        }
        //*/

        // TODO: there may be a better way to check completion. Out of scope for current project.
        //MB
        //Someone elses TODO! But this IS in scope of THSI PromiseRejectionEvent//
        //Maybe a good place to put the red/green/yellow update stuff
        $(document).ready(function() {
            setInterval(function() {
                $('#cmi5launch_completioncheck').load('completion_check.php?id=<?php echo $id ?>&n=<?php echo $n ?>');
            }, 30000); // TODO: make this interval a configuration setting.
        });
    </script>
<?php

//Mb
//We shouldn't need any of this on this side. registrations being next page?
//TRIHT! BECAUSE you see, even though each section WILL have reg
//Thsoe are created on start new which is on new page

//Start at 1, if continuing old attempt it will draw previous regid from LRS
$registrationid = 1;

$getregistrationdatafromlrsstate = cmi5launch_get_global_parameters_and_get_state(
    "http://cmi5api.co.uk/stateapikeys/registrations"
);

$lrsrespond = $getregistrationdatafromlrsstate->httpResponse['status'];

if ($lrsrespond != 200 && $lrsrespond != 404) {
    // On clicking new attempt, save the registration details to the LRS State and launch a new attempt.
    echo "<div class='alert alert-error'>" . get_string('cmi5launch_notavailable', 'cmi5launch') . "</div>";

    if ($CFG->debug == 32767) {
        echo "<p>Error attempting to get registration data from State API.</p>";
        echo "<pre>";
       var_dump($getregistrationdatafromlrsstate);
        echo "</pre>";
    }
    die();
}
//MB
 
//////Ummmmmmmmmmmm, this is for AU right? oh, except the dang done or 
//not.....ugh
//  bring in functions from classes cmi5Connector/Cmi5Tables
    $progress = new progress;

    //bring in functions from class cmi5_table_connectors
    $getProgress = $progress->getRetrieveStatement();

    //IT helps to CALL the function sheik lol
   ////////////// $currentProgress = $getProgress($regId, $id);

//MB
//Ok, here is where I want to put progress in the tables here.
//so here is a good place to see what params are available to pass in,
//Hey! IS regid a tatmentid???
if ($lrsrespond == 200) {

    $registrationdatafromlrs = json_decode($getregistrationdatafromlrsstate->content->getContent(), true);

    //Array to hold verbs and be returned
    $progress = array();

    // Needs to come after previous attempts so a non-sighted user can hear launch options.
    if ($cmi5launch->cmi5multipleregs) {
        echo "<p id='cmi5launch_newattempt'><a tabindex=\"0\"
        onkeyup=\"key_test('".$registrationid ."')\" onclick=\"mod_cmi5launch_launchexperience('"
            . $registrationid 
            . "')\" style=\"cursor: pointer;\">"
            . get_string('cmi5launch_attempt', 'cmi5launch')
            . "</a></p>";
    }
    
} else {
    echo "<p tabindex=\"0\"
        onkeyup=\"key_test('".$registrationid."')\"
        id='cmi5launch_newattempt'><a onclick=\"mod_cmi5launch_launchexperience('"
        . $registrationid
        . "')\" style=\"cursor: pointer;\">"
        . get_string('cmi5launch_attempt', 'cmi5launch')
        . "</a></p>";
}
//*/

//Here is where the table is outlined
//Here is where I can change the headers
$table = new html_table();
//MB
//I think I will change the table id, doesn't seem to be defined elsewhere
$table->id = 'cmi5launch_autable';
$table->caption = get_string('AUtableheader', 'cmi5launch');
$table->head = array(
    get_string('cmi5launchviewAUname', 'cmi5launch'),
    get_string('cmi5launchviewstatus', 'cmi5launch'),
    get_string('cmi5launchviewregistrationheader', 'cmi5launch'),

);

echo"<br>";
    echo" ok but sighhhhh what is aus  here?    ";
    var_dump($aus);
    echo"<br>";

//What if we use a diff type array?
$length = count($aus);
//Should be an array of our table objects
$tableData = array();
$tableData2 = array();
//The problem is the table object is making these strings instead of arrays
//but its an OBJECt, so lets use its properties?
$tableObject = new stdClass();
$tableObject->au = array('title'=>'', 'progress'=>'');
$tableObject->link = '';

 //'au' = array ("title" , "progress"), 'link') );
//Ok, so we are now dumping a huge amount into table, lets refine:
//Do what THEY ARE doing!!! SORT BY KEY VALUE
/*
foreach ($aus as $key => $item) {

    echo"<br>";
    echo"I DONT UNDERSTNAD!~ IT SHOULD BE ARRAY???";
    var_dum((array)($aus[$key]) );
    echo"<br>";
    $au = (array)($aus[$key]);
    //OF COURSE!!! U=ITS MY OBJECT!!! lets try decodeing it

    if (!is_array($au)) {
        $reason = "Excepted array, found " . "";
        throw new moodle_exception($reason, 'cmi5launch', '', $warnings[$reason]);
    }
    array_push(
        //We need to feed this an array? Maybe a one level array wioth the script to
        //send it to new page? But that's what 'onlick' here does....
        //MB //weel it still need the link to the au page riht?
        //section one, section two, etc
        $tableData[$au['id'] ],
        "<a tabindex=\"0\" id='cmi5relaunch_attempt'
            onkeyup=\"key_test('". "view" ."')\" onclick=\"mod_cmi5launch_launchexperience('". "view ". "')\" style='cursor: pointer;'>"
            . get_string('cmi5launchviewlaunchlink', 'cmi5launch') . "</a>"
        );
    $au['created'] = date_format(
        date_create($registrationdatafromlrs[$key]['created']),
        'D, d M Y H:i:s'
    );
    $registrationdatafromlrs[$key]['lastlaunched'] = date_format(
        date_create($registrationdatafromlrs[$key]['lastlaunched']),
        'D, d M Y H:i:s'
    );

}*/
foreach ($aus as $key => $item) {

    $au = (array)($aus[$key]);
    //OF COURSE!!! U=ITS MY OBJECT!!! lets try decodeing it

    if (!is_array($au)) {
        $reason = "Excepted array, found " . "";
        throw new moodle_exception($reason, 'cmi5launch', '', $warnings[$reason]);
    }
    
           
    //$au['id']
    $registrationFromAu = array();
    $auInfo = array();
    $auInfo[] = "Trying hard!";
    $auInfo = $au['title'][0]['text'];    
    $registrationFromAu[] = $auInfo;
    
    $tableData2[] = $registrationFromAu;
    $tableData2[]= "<a tabindex=\"0\" id='cmi5relaunch_attempt'
    onkeyup=\"key_test('". "view" ."')\" onclick=\"mod_cmi5launch_launchexperience('". "view ". "')\" style='cursor: pointer;'>"
    . get_string('cmi5launchviewlaunchlink', 'cmi5launch') . "</a>"
; 
}

//}

echo"<br>";
    echo" ok but what is table au here?    ";
    var_dump($tableData2);
    echo"<br>";

//This feeds the table, note registrationdatafromlrs is anOBJECT, so maybe I can foreach loop through au objects
$table->data = $tableData2;
//Ok, this makes the table:
echo html_writer::table($table);

// Add a form to be posted based on the attempt selected.
//I don't think we need this, posting a form would be to activate launch.php and
//we are really just linking yeah? 
?>

 
    <form id="launchform" action="AUview.php" method="get">
        <input id="AU_view" name="AU_view" type="hidden" value="default">
        <input id="id" name="id" type="hidden" value="<?php echo $id ?>">
        <input id="n" name="n" type="hidden" value="<?php echo $n ?>">
    </form>

<?php

echo $OUTPUT->footer();


