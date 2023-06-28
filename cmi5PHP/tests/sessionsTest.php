<?php
namespace cmi5Test;

use PHPUnit\Framework\TestCase;
use Session;

/**
 * Class sessionsTest.
 *
 * @copyright 2023 Megan Bohland
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \Au
 */
class sessionsTest extends TestCase
{
    private $sessionProperties, $emptyStatement, $mockStatementValues;

    protected function setUp(): void
    {

        //The properties of session class
        $this->sessionProperties = array(
            'id',
            'tenantname',
            'tenantId',
            'registrationsCoursesAusId',
            'lmsid',
            'firstlaunch',
            'lastlaunch',
            'progress',
            'auid',
            'aulaunchurl',
            'launchurl',
            'completed',
            'passed',
            'inprogress',
            'grade',
            'registrationid',
            'lrscode',
            'createdAt',
            'updatedAt',
            'registrationCourseAusId',
            'code',
            'lastRequestTime',
            'launchTokenId',
            'launchMode',
            'masteryScore',
            'contextTemplate',
            'isLaunched',
            'isInitialized',
            'initializedAt',
            'isCompleted',
            'isPassed',
            'isFailed',
            'isTerminated',
            'isAbandoned',
            'courseid'
        );

        $this->emptyStatement = array();

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


    public function testInstantiationWithEmpty()
    {
        $obj = new Session($this->emptyStatement);


        //Is a Session object
        $this->assertInstanceOf('Session', $obj);

        $expectedAmount = count($this->sessionProperties);
        //could typecasting the object as an array help? dirty fix
        $sessionArray = (array) $obj;
        $this->assertCount($expectedAmount, $sessionArray, "Session has $expectedAmount properties");

        //Properties exists and are empty
        foreach ($sessionArray as $property => $value) {

            $this->assertArrayHasKey($property, $sessionArray, "$property exists");
            $this->assertNull($value, "$property empty");
        }

    }

    public function testInstantiationWithValues()
    {
        $obj = new Session($this->mockStatementValues);

        //Is a Session object
        $this->assertInstanceOf('Session', $obj);
        $expectedAmount = count($this->sessionProperties);
        //could typecasting the object as an array help? dirty fix
        $sessionArray = (array) $obj;
        $this->assertCount($expectedAmount, $sessionArray, "Session has $expectedAmount properties");

        //Properties exists and are correct (value should equal name of property)
        foreach ($sessionArray as $property => $value) {

            $this->assertArrayHasKey($property, $sessionArray, "$property exists");
            $this->assertEquals($property, $value, "$value does not equal $property");
        }
    }
}