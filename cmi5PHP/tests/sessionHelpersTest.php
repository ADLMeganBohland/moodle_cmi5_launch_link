<?php
namespace cmi5Test;

use PHPUnit\Framework\TestCase;
use Session;
use Session_Helpers;
use moodle_database;

/**
 * Tests for SessionHelpers class.
 *
 * @copyright 2023 Megan Bohland
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \auHelpers
 * @covers \Session_Helpers
 * @covers \SessionHelpers::updateSessions
 */
class SessionHelpersTest extends TestCase
{
    private $emptyStatement = array(), $mockStatementValues = array(), $mockStatementValues2 = array();

    protected function setUp(): void
    {
        $this->emptyStatement = array();

        //Perhaps a good test would be to test the constructor with a statement that has all the properties set.
         //Perhaps a good test would be to test the constructor with a statement that has all the properties set.
         $this->mockStatementValues = array(
            'id' => 'id',
            'tenantname' => 'tenantname',
            'tenantId' => 'tenantId',
            'registrationsCoursesAusId' => 'registrationsCoursesAusId',
            'lmsid' => 'lmsid',
            'firstlaunch' => 'firstlaunch',
            'lastlaunch' => 'lastlaunch',
            'progress' => 'progress',
            'auid' => 'auid',
            'aulaunchurl' => 'aulaunchurl',
            'launchurl' => 'launchurl',
            'completed' => 'completed',
            'passed' => 'passed',
            'inprogress' => 'inprogress',
            'grade' => 'grade',
            'registrationid' => 'registrationid',
            'lrscode' => 'lrscode',
            'createdAt' => 'createdAt',
            'updatedAt' => 'updatedAt',
            'registrationCourseAusId' => 'registrationCourseAusId',
            'code' => 'code',
            'lastRequestTime' => 'lastRequestTime',
            'launchTokenId' => 'launchTokenId',
            'launchMode' => 'launchMode',
            'masteryScore' => 'masteryScore',
            'contextTemplate' => 'contextTemplate',
            'isLaunched' => 'isLaunched',
            'isInitialized' => 'isInitialized',
            'initializedAt' => 'initializedAt',
            'isCompleted' => 'isCompleted',
            'isPassed' => 'isPassed',
            'isFailed' => 'isFailed',
            'isTerminated' => 'isTerminated',
            'isAbandoned' => 'isAbandoned',
            'courseid' => 'courseid'
        );
    }

    protected function tearDown(): void
    {
        //  $this->example = null;
    }

 

    //Tests for updateSessions
    //UpdateSessions retrieves session information from cmi5 player. 
    //using the getSessions func from cmi5connectors
    //Then updates the DB tables
    //IT then returns the session object
    public function testUpdateSessions()
    { 
        $helper = new Session_Helpers();
        //Mock ids to pass in
        $sessionId = 0;
        $cmi5Id = 0;


        $session = $helper->updateSessions($sessionId, $cmi5Id);

        echo"ok what is session";
        var_dump($session);
        $this->assertInstanceOf('Session', $session);
        }  
}