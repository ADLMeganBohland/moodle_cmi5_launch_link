<?php
namespace cmi5Test;

use PHPUnit\Framework\TestCase;
use Au;

/**
 * Class AuTest.
 *
 * @copyright 2023 Megan Bohland
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \Au
 */
class AusTest extends TestCase
{
    private $auProperties, $emptyStatement, $mockStatementValues;

    protected function setUp(): void
    {
        $this->auProperties = array(
            'id',
            'url',
            'type',
            'lmsid',
            'grade',
            'scores',
            'title',
            'moveon',
            'auindex',
            'parents',
            'objectives',
            'description',
            'activitytype',
            'launchmethod',
            'masteryscore',
            'satisfied',
            'launchurl',
            'sessionid',
            'sessions',
            'progress',
            'noattempt',
            'completed',
            'passed',
            'inprogress',
        );

        $this->emptyStatement = array();

        //Perhaps a good test would be to test the constructor with a statement that has all the properties set.
        $this->mockStatementValues = array(
            'id' => 'id',
            'url' => 'url',
            'type' => 'type',
            'lmsId' => 'lmsId',
            'grade' => 'grade',
            'scores' => 'scores',
            'title' =>  'title',
            'moveOn' => 'moveOn',
            'auIndex' => 'auIndex',
            'parents' => 'parents',
            'objectives' => 'objectives',
            'description' =>  'description',
            'activityType' => 'activityType',
            'launchMethod' => 'launchMethod',
            'masteryScore' => 'masteryScore',
            'satisfied' => 'satisfied',
            'launchurl' => 'launchurl',
            'sessionid' => 'sessionid',
            'sessions' => 'sessions',
            'progress' => 'progress',
            'noattempt' => 'noattempt',
            'completed' => 'completed',
            'passed' => 'passed',
            'inprogress' => 'inprogress',
        );
    }

    protected function tearDown(): void
    {
        //  $this->example = null;
    }


    public function testInstantiationWithEmpty()
    {
        $obj = new Au($this->emptyStatement);

        //Is an AU object
        $this->assertInstanceOf('Au', $obj);
        //It is saying AU is not transversable
        //Implementing traversable in AU is breaking the code,
        //Make sure the AU object does not have any 'extra' properties, only the amount passed in
        $expectedAmount = count($this->auProperties);
        //could typecasting the object as an array help? dirty fix
        $auArray = (array) $obj;
        $this->assertCount($expectedAmount, $auArray, "AU has $expectedAmount properties");

        //Properties exists and are empty
        foreach ($auArray as $property => $value) {

            $this->assertArrayHasKey($property, $auArray, "$property exists");
            $this->assertNull($value, "$property empty");
        }

    }

    public function testInstantiationWithValues()
    {
        $obj = new Au($this->mockStatementValues);

        //Is an AU object
        $this->assertInstanceOf('Au', $obj);
        //It is saying AU is not transversable
        //Implementing traversable in AU is breaking the code,
        //Make sure the AU object does not have any 'extra' properties, only the amount passed in
        $expectedAmount = count($this->auProperties);
        //could typecasting the object as an array help? dirty fix
        $auArray = (array) $obj;
        $this->assertCount($expectedAmount, $auArray, "AU has $expectedAmount properties");

        //Properties exists and are correct (value should equal name of property)
        foreach ($auArray as $property => $value) {

            $this->assertArrayHasKey($property, $auArray, "$property exists");
            $this->assertEquals($property, $value, "$value does not equal $property");
        }
    }
}