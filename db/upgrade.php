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
 * This file keeps track of upgrades to the cmi5launch module
 *
 * Sometimes, changes between versions involve alterations to database
 * structures and other major things that may break installations. The upgrade
 * function in this file will attempt to perform all the necessary actions to
 * upgrade your older installation to the current version. If there's something
 * it cannot do itself, it will tell you what you need to do.  The commands in
 * here will all be database-neutral, using the functions defined in DLL libraries.
 *
 * @package mod_cmi5launch
 * @copyright  2013 Andrew Downes
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute cmi5launch upgrade from the given old version
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_cmi5launch_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();
    if ($oldversion < 2023121209) {

        
        // I need to remove 7 unused columns from the cmi5launch_aus table.
        // Define field auid to be dropped from cmi5launch_aus.
        $table = new xmldb_table('cmi5launch_aus');
        
        /*
        
             // drop index and add new one
             $indexOld = new xmldb_index('courseid', XMLDB_INDEX_NOTUNIQUE, ['courseid']);
             $indexNew = new xmldb_index('id', XMLDB_INDEX_NOTUNIQUE, ['courseid']);
                    
             // Conditionally launch drop index courseid.
             if ($dbman->index_exists($table, $indexOld)) {
                 $dbman->drop_index($table, $indexOld);
             }
     
             // Conditionally launch add index courseid.
             if (!$dbman->index_exists($table, $indexNew)) {
                 $dbman->add_index($table, $indexNew);
             }
     
        */
        
        $fieldauid = new xmldb_field('auid');
        //$fieldcourseid = new xmldb_field('courseid');
        $fielduserid = new xmldb_field('userid');
        $fieldtenantname = new xmldb_field('tenantname');
        $fieldcurrentgrade = new xmldb_field('currentgrade');
        $fieldregistrationid = new xmldb_field('registrationid');
        $fieldreturnurl = new xmldb_field('returnurl');

        $arraytoremove = array($fieldauid, /*$fieldcourseid*/ $fielduserid, $fieldtenantname, $fieldcurrentgrade, $fieldregistrationid, $fieldreturnurl);
       
        // Now cycle through array and remove fields.
        foreach ($arraytoremove as $field) {
            // Conditionally launch drop field auid.
            if ($dbman->field_exists($table, $field)) {
                $dbman->drop_field($table, $field);
            }
        }

   
        
        // Cmi5launch savepoint reached.
        upgrade_mod_savepoint(true, 2023121209, 'cmi5launch');
    }


    if ($oldversion < 2023112117) {

        // Changing type of field masteryscore on table cmi5launch_sessions to number.
        $table = new xmldb_table('cmi5launch_sessions');
        $field = new xmldb_field('masteryscore', XMLDB_TYPE_NUMBER, '10', null, null, null, null, 'launchmode');

        // Launch change of type for field masteryscore.
        $dbman->change_field_type($table, $field);

        // Cmi5launch savepoint reached.
        upgrade_mod_savepoint(true, 2023112117, 'cmi5launch');
    }
    if ($oldversion < 2023112113) {

        // Changing the default of field grade on table cmi5launch_aus to drop it.
        $table = new xmldb_table('cmi5launch_aus');
        $field = new xmldb_field('grade', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'scores');

        // Launch change of default for field grade.
        $dbman->change_field_default($table, $field);

        // Cmi5launch savepoint reached.
        upgrade_mod_savepoint(true, 2023112113, 'cmi5launch');
    }
    
    if ($oldversion < 2023111714) {

        // Changing type of field objectives on table cmi5launch_aus to text.
        $table = new xmldb_table('cmi5launch_aus');
        $objectives = new xmldb_field('objectives', XMLDB_TYPE_TEXT, null, null, null, null, null, 'parents');
        $description = new xmldb_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null, 'objectives');

        // Launch change of type for field objectives.
        $dbman->change_field_type($table, $objectives);


        // Launch change of type for field description.
        $dbman->change_field_type($table, $description);

        // Cmi5launch savepoint reached.
        upgrade_mod_savepoint(true, 2023111714, 'cmi5launch');

    }

    if ($oldversion < 2023101217) {

    
        // Define table cmi5launch to be created.
        $table = new xmldb_table('cmi5launch');

        // Adding fields to table cmi5launch.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('intro', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('introformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('cmi5launchurl', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null);
        $table->add_field('cmi5activityid', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('registrationid', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('returnurl', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('cmi5verbid', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('cmi5expiry', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '365');
        $table->add_field('overridedefaults', XMLDB_TYPE_INTEGER, '1', null, null, null, null);
        $table->add_field('cmi5multipleregs', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('courseinfo', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('aus', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('grade', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');

        // Adding keys to table cmi5launch.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table cmi5launch.
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, ['course']);

        // Conditionally launch create table for cmi5launch.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

            // Define table cmi5launch_course to be created.
            $table = new xmldb_table('cmi5launch_course');

            // Adding fields to table cmi5launch_course.
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
            $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
            $table->add_field('cmi5launchurl', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null);
            $table->add_field('cmi5activityid', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
            $table->add_field('registrationid', XMLDB_TYPE_CHAR, '50', null, null, null, null);
            $table->add_field('returnurl', XMLDB_TYPE_CHAR, '255', null, null, null, null);
            $table->add_field('aus', XMLDB_TYPE_TEXT, null, null, null, null, null);
            $table->add_field('ausgrades', XMLDB_TYPE_CHAR, '1000', null, null, null, '0');
            $table->add_field('grade', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
    
            // Adding keys to table cmi5launch_course.
            $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
    
            // Adding indexes to table cmi5launch_course.
            $table->add_index('courseid', XMLDB_INDEX_NOTUNIQUE, ['courseid']);
    
            // Conditionally launch create table for cmi5launch_course.
            if (!$dbman->table_exists($table)) {
                $dbman->create_table($table);
            }
            {
                //If the table already exists, we just need to add the new field
                       // Define field ausgrades to be added to cmi5launch_course.
                    $table = new xmldb_table('cmi5launch_course');
                     $field = new xmldb_field('ausgrades', XMLDB_TYPE_CHAR, '1000', null, null, null, '0', 'aus');

                     // Conditionally launch add field ausgrades.
                    if (!$dbman->field_exists($table, $field)) {
                        $dbman->add_field($table, $field);
                    }
            }

                    // Define table cmi5launch_lrs to be created.
        $table = new xmldb_table('cmi5launch_lrs');

        // Adding fields to table cmi5launch_lrs.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('cmi5launchid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lrsendpoint', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lrsauthentication', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lrslogin', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lrspass', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('customacchp', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('useactoremail', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('lrsduration', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('tenantname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('tenantpass', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('tenanttoken', XMLDB_TYPE_CHAR, '350', null, null, null, null);
        $table->add_field('playerport', XMLDB_TYPE_INTEGER, '5', null, null, null, '66398');
        $table->add_field('playerurl', XMLDB_TYPE_CHAR, '255', null, null, null, null);

        // Adding keys to table cmi5launch_lrs.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table cmi5launch_lrs.
        $table->add_index('cmi5launchid', XMLDB_INDEX_NOTUNIQUE, ['cmi5launchid']);

        // Conditionally launch create table for cmi5launch_lrs.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
      // Define table cmi5launch_player to be created.
      $table = new xmldb_table('cmi5launch_player');

      // Adding fields to table cmi5launch_player.
      $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
      $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
      $table->add_field('tenantid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
      $table->add_field('tenantname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
      $table->add_field('tenanttoken', XMLDB_TYPE_CHAR, '350', null, null, null, null);
      $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
      $table->add_field('launchmethod', XMLDB_TYPE_CHAR, '10', null, null, null, 'AnyWindow');
      $table->add_field('returnurl', XMLDB_TYPE_CHAR, '255', null, null, null, null);
      $table->add_field('homepage', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
      $table->add_field('registrationid', XMLDB_TYPE_CHAR, '50', null, null, null, null);
      $table->add_field('sessionid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
      $table->add_field('launchurl', XMLDB_TYPE_CHAR, '500', null, null, null, null);
      $table->add_field('cmi5playerurl', XMLDB_TYPE_CHAR, '255', null, null, null, null);
      $table->add_field('cmi5playerport', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
      $table->add_field('courseinfo', XMLDB_TYPE_TEXT, null, null, null, null, null);
      $table->add_field('firstlaunch', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
      $table->add_field('lastlaunch', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

      // Adding keys to table cmi5launch_player.
      $table->add_key('primary', XMLDB_KEY_PRIMARY, ['registrationid']);

      // Adding indexes to table cmi5launch_player.
      $table->add_index('name', XMLDB_INDEX_NOTUNIQUE, ['name']);

      // Conditionally launch create table for cmi5launch_player.
      if (!$dbman->table_exists($table)) {
          $dbman->create_table($table);
      }


          // Define table cmi5launch_sessions to be created.
          $table = new xmldb_table('cmi5launch_sessions');

          // Adding fields to table cmi5launch_sessions.
          $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, null, XMLDB_SEQUENCE, null);
          $table->add_field('sessionid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
          $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
          $table->add_field('registrationscoursesausid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
          $table->add_field('tenantname', XMLDB_TYPE_CHAR, '255', null, null, null, null);
          $table->add_field('lmsid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
          $table->add_field('createdat', XMLDB_TYPE_CHAR, '30', null, null, null, null);
          $table->add_field('updatedat', XMLDB_TYPE_CHAR, '30', null, null, null, null);
          $table->add_field('registrationcourseausid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
          $table->add_field('code', XMLDB_TYPE_CHAR, '550', null, null, null, null);
          $table->add_field('launchtokenid', XMLDB_TYPE_CHAR, '550', null, null, null, null);
          $table->add_field('lastrequesttime', XMLDB_TYPE_CHAR, '30', null, null, null, null);
          $table->add_field('launchmode', XMLDB_TYPE_CHAR, '25', null, null, null, null);
          $table->add_field('masteryscore', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
          $table->add_field('score', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
          $table->add_field('response', XMLDB_TYPE_TEXT, null, null, null, null, null);
          $table->add_field('islaunched', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
          $table->add_field('isinitialized', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
          $table->add_field('initializedat', XMLDB_TYPE_CHAR, '30', null, null, null, null);
          $table->add_field('duration', XMLDB_TYPE_CHAR, '30', null, null, null, null);
          $table->add_field('iscompleted', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
          $table->add_field('ispassed', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
          $table->add_field('isfailed', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
          $table->add_field('isterminated', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
          $table->add_field('isabandoned', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
          $table->add_field('progress', XMLDB_TYPE_TEXT, null, null, null, null, null);
          $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
          $table->add_field('launchmethod', XMLDB_TYPE_CHAR, '10', null, null, null, 'AnyWindow');
          $table->add_field('registrationid', XMLDB_TYPE_CHAR, '50', null, null, null, null);
          $table->add_field('lrscode', XMLDB_TYPE_CHAR, '50', null, null, null, null);
          $table->add_field('auid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
          $table->add_field('launchurl', XMLDB_TYPE_CHAR, '750', null, null, null, null);
    
          // Adding keys to table cmi5launch_sessions.
          $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
  
          // Adding indexes to table cmi5launch_sessions.
          $table->add_index('lmsid', XMLDB_INDEX_NOTUNIQUE, ['lmsid']);
  
          // Conditionally launch create table for cmi5launch_sessions.
          if (!$dbman->table_exists($table)) {
              $dbman->create_table($table);
          }else{
                // Define fields to be dropped from cmi5launch_sessions.
                $table = new xmldb_table('cmi5launch_sessions');
                $firstlaunch = new xmldb_field('firstlaunch');
                $lastlaunch = new xmldb_field('lastlaunch');
                $completed_passed = new xmldb_field('completed_passed');
                $contexttemplate = new xmldb_field('contexttemplate');

                // Conditionally launch drop field firstlaunch.
                if ($dbman->field_exists($table, $firstlaunch)) {
                        $dbman->drop_field($table, $firstlaunch);
                }
                // Conditionally launch drop field lastlaunch.
                if ($dbman->field_exists($table, $lastlaunch)) {
                        $dbman->drop_field($table, $lastlaunch);
                }
                // Conditionally launch drop field completed_passed.
                if ($dbman->field_exists($table, $completed_passed)) {
                        $dbman->drop_field($table, $completed_passed);
                }
                // Conditionally launch drop field contexttemplate.
                if ($dbman->field_exists($table, $contexttemplate)) {
                        $dbman->drop_field($table, $contexttemplate);
                }
            }

        // Define table cmi5launch_aus to be created.
        $table = new xmldb_table('cmi5launch_aus');

        // Adding fields to table cmi5launch_aus.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, null, XMLDB_SEQUENCE, null);
        $table->add_field('attempt', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('auid', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('tenantname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('currentgrade', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('launchmethod', XMLDB_TYPE_CHAR, '10', null, null, null, 'AnyWindow');
        $table->add_field('registrationid', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('sessionid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('returnurl', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('lmsid', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('url', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('type', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('title', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('moveon', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('auindex', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('parents', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('objectives', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('description', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('activitytype', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('masteryscore', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('completed', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $table->add_field('passed', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $table->add_field('inprogress', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $table->add_field('noattempt', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $table->add_field('satisfied', XMLDB_TYPE_CHAR, '5', null, null, null, '0');
        $table->add_field('sessions', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('scores', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('grade', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');

        // Adding keys to table cmi5launch_aus.
        $table->add_key('id', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table cmi5launch_aus.
        $table->add_index('courseid', XMLDB_INDEX_NOTUNIQUE, ['courseid']);

        // Conditionally launch create table for cmi5launch_aus.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }


        // Cmi5launch savepoint reached.
        upgrade_mod_savepoint(true, 2023101217, 'cmi5launch');
    }

    
    if ($oldversion < 2023081516) {

        // Define table cmi5launch to be created.
        $table = new xmldb_table('cmi5launch');

        // Adding fields to table cmi5launch.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('intro', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('introformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('cmi5launchurl', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null);
        $table->add_field('cmi5activityid', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('registrationid', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('returnurl', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('cmi5verbid', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('cmi5expiry', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '365');
        $table->add_field('cmi5multipleregs', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('courseinfo', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('aus', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('grade', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');

        // Adding keys to table cmi5launch.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table cmi5launch.
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, ['course']);

        // Conditionally launch create table for cmi5launch.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table cmi5launch_course to be created.
        $table = new xmldb_table('cmi5launch_course');

        // Adding fields to table cmi5launch_course.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('cmi5launchurl', XMLDB_TYPE_CHAR, '1333', null, XMLDB_NOTNULL, null, null);
        $table->add_field('cmi5activityid', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('registrationid', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('returnurl', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('aus', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('grade', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');

        // Adding keys to table cmi5launch_course.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table cmi5launch_course.
        $table->add_index('courseid', XMLDB_INDEX_NOTUNIQUE, ['courseid']);

        // Conditionally launch create table for cmi5launch_course.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table cmi5launch_lrs to be created.
        $table = new xmldb_table('cmi5launch_lrs');

        // Adding fields to table cmi5launch_lrs.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('cmi5launchid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lrsendpoint', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lrsauthentication', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lrslogin', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lrspass', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('customacchp', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('useactoremail', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('lrsduration', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('tenantname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('tenantpass', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('tenanttoken', XMLDB_TYPE_CHAR, '350', null, null, null, null);
        $table->add_field('playerport', XMLDB_TYPE_INTEGER, '5', null, null, null, '66398');
        $table->add_field('playerurl', XMLDB_TYPE_CHAR, '255', null, null, null, null);

        // Adding keys to table cmi5launch_lrs.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table cmi5launch_lrs.
        $table->add_index('cmi5launchid', XMLDB_INDEX_NOTUNIQUE, ['cmi5launchid']);

        // Conditionally launch create table for cmi5launch_lrs.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table cmi5launch_player to be created.
        $table = new xmldb_table('cmi5launch_player');

        // Adding fields to table cmi5launch_player.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('tenantid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('tenantname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('tenanttoken', XMLDB_TYPE_CHAR, '350', null, null, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('launchmethod', XMLDB_TYPE_CHAR, '10', null, null, null, 'AnyWindow');
        $table->add_field('returnurl', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('homepage', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('registrationid', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('sessionid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('launchurl', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('cmi5playerurl', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('cmi5playerport', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('courseinfo', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('firstlaunch', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('lastlaunch', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table cmi5launch_player.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['registrationid']);

        // Adding indexes to table cmi5launch_player.
        $table->add_index('name', XMLDB_INDEX_NOTUNIQUE, ['name']);

        // Conditionally launch create table for cmi5launch_player.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table cmi5launch_sessions to be created.
        $table = new xmldb_table('cmi5launch_sessions');

        // Adding fields to table cmi5launch_sessions.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, null, XMLDB_SEQUENCE, null);
        $table->add_field('sessionid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('registrationscoursesausid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('tenantname', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('lmsid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('createdat', XMLDB_TYPE_CHAR, '30', null, null, null, null);
        $table->add_field('updatedat', XMLDB_TYPE_CHAR, '30', null, null, null, null);
        $table->add_field('registrationcourseausid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('code', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('launchtokenid', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('lastrequesttime', XMLDB_TYPE_CHAR, '30', null, null, null, null);
        $table->add_field('launchmode', XMLDB_TYPE_CHAR, '25', null, null, null, null);
        $table->add_field('masteryscore', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('score', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('response', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('contexttemplate', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('islaunched', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('isinitialized', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('initializedat', XMLDB_TYPE_CHAR, '30', null, null, null, null);
        $table->add_field('duration', XMLDB_TYPE_CHAR, '30', null, null, null, null);
        $table->add_field('iscompleted', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('ispassed', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('isfailed', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('isterminated', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('isabandoned', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('progress', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('launchmethod', XMLDB_TYPE_CHAR, '10', null, null, null, 'AnyWindow');
        $table->add_field('registrationid', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('lrscode', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('auid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('launchurl', XMLDB_TYPE_CHAR, '750', null, null, null, null);
        $table->add_field('firstlaunch', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('lastlaunch', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('completed_passed', XMLDB_TYPE_CHAR, '10', null, null, null, null);

        // Adding keys to table cmi5launch_sessions.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table cmi5launch_sessions.
        $table->add_index('lmsid', XMLDB_INDEX_NOTUNIQUE, ['lmsid']);

        // Conditionally launch create table for cmi5launch_sessions.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table cmi5launch_aus to be created.
        $table = new xmldb_table('cmi5launch_aus');

        // Adding fields to table cmi5launch_aus.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, null, XMLDB_SEQUENCE, null);
        $table->add_field('auid', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('tenantname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('currentgrade', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('launchmethod', XMLDB_TYPE_CHAR, '10', null, null, null, 'AnyWindow');
        $table->add_field('registrationid', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('sessionid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('returnurl', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('lmsid', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('url', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('type', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('title', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('moveon', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('auindex', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('parents', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('objectives', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('description', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('activitytype', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('masteryscore', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('completed', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $table->add_field('passed', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $table->add_field('inprogress', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $table->add_field('noattempt', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $table->add_field('satisfied', XMLDB_TYPE_CHAR, '5', null, null, null, '0');
        $table->add_field('sessions', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('scores', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('grade', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');

        // Adding keys to table cmi5launch_aus.
        $table->add_key('id', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table cmi5launch_aus.
        $table->add_index('courseid', XMLDB_INDEX_NOTUNIQUE, ['courseid']);

        // Conditionally launch create table for cmi5launch_aus.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        upgrade_mod_savepoint(true, 2023081516, 'cmi5launch');

    }


    if ($oldversion < 2013083100) {
        // Define field cmi5activityid to be added to cmi5launch.
        $table = new xmldb_table('cmi5launch');
        $field = new xmldb_field('cmi5activityid', XMLDB_TYPE_TEXT, '1333', null, XMLDB_NOTNULL, null, null, 'cmi5launchurl');

        // Add field cmi5activityid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2013083100, 'cmi5launch');
    }

    if ($oldversion < 2013111600) {
        // Define field cmi5verbid to be added to cmi5launch.
        $table = new xmldb_table('cmi5launch');
        $field = new xmldb_field('cmi5verbid', XMLDB_TYPE_TEXT, '1333', null, XMLDB_NOTNULL, null, null, 'cmi5launchurl');

        // Add field cmi5activityid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2013111600, 'cmi5launch');
    }

    if ($oldversion < 2015032500) {

        // Define field overridedefaults to be added to cmi5launch.
        $table = new xmldb_table('cmi5launch');
        $field = new xmldb_field('overridedefaults', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'cmi5verbid');

        // Conditionally launch add field overridedefaults.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define table cmi5launch_lrs to be created.
        $table = new xmldb_table('cmi5launch_lrs');

        // Adding fields to table cmi5launch_lrs.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('cmi5launchid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lrsendpoint', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lrsauthentication', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lrslogin', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lrspass', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lrsduration', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table cmi5launch_lrs.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table cmi5launch_lrs.
        $table->add_index('cmi5launchid', XMLDB_INDEX_NOTUNIQUE, array('cmi5launchid'));

        // Conditionally launch create table for cmi5launch_lrs.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // CMI5launch savepoint reached.
        upgrade_mod_savepoint(true, 2015032500, 'cmi5launch');
    }

    if ($oldversion < 2015033100) {

        unset_config('cmi5launchlrsversion', 'cmi5launch');
        unset_config('cmi5launchlrauthentication', 'cmi5launch');

        upgrade_mod_savepoint(true, 2015033100, 'cmi5launch');
    }

    if ($oldversion < 2015112702) {
        // Define field cmi5activityid to be added to cmi5launch.
        $table = new xmldb_table('cmi5launch');
        $field = new xmldb_field('cmi5multipleregs', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'cmi5verbid');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('cmi5launch_lrs');
        $field = new xmldb_field('useactoremail', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table->add_field('customacchp', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2015112702, 'cmi5launch');
    }

    if ($oldversion < 2016121200) {
        $table = new xmldb_table('cmi5launch');
        $field = new xmldb_field('cmi5expiry', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 365);

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
    }

    if ($oldversion < 2018103000) {
        $table = new xmldb_table('cmi5launch_credentials');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table, $continue = true, $feedback = true);
        }

        $table = new xmldb_table('cmi5launch_lrs');
        $field = new xmldb_field('watershedlogin', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field, $continue = true, $feedback = true);
        }

        $field = new xmldb_field('watershedpass', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field, $continue = true, $feedback = true);
        }

        upgrade_mod_savepoint(true, 2018103000, 'cmi5launch');
    }

    // Final return of upgrade result (true, all went good) to Moodle.
    return true;
}
