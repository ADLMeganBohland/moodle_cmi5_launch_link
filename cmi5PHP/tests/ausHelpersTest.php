<?php
namespace cmi5Test;

use PHPUnit\Framework\TestCase;
use Au;
use Au_Helpers;
use moodle_database;

/**
 * Tests for AuHelpers class.
 *
 * @copyright 2023 Megan Bohland
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers \auHelpers
 * @covers \auHelpers::getAuProperties
 */
class AusHelpersTest extends TestCase
{
    private $emptyStatement = array(), $mockStatementValues = array(), $mockStatementValues2 = array();

    protected function setUp(): void
    {
        $this->emptyStatement = array();

        //Perhaps a good test would be to test the constructor with a statement that has all the properties set.
        $this->mockStatementValues = array(
            'id' => 0,
            'url' => 'url',
            'type' => 'type',
            'lmsId' => 'lmsid',
            'grade' => 'grade',
            'scores' => 'scores',
            'title' =>  array (
                array ("0"=> "en-Test",
                "text" => "Introduction to Testing"
                )
            ),
            'moveOn' => 'moveon',
            'auIndex' => 0,
            'parents' => 'parents',
            'objectives' => 'objectives',
            'description' =>  array (
                array ("0"=> "en-Test",
                "text" => "Testing Testing.")
            ),
            'activityType' => 'activitytype',
            'launchMethod' => 'Ownwindow',
            'masteryScore' => 0,
            'satisfied' => 0,
            'launchurl' => 'launchurl',
            'sessionid' => 'sessionid',
            'sessions' => 'sessions',
            'progress' => 'progress',
            'noattempt' => 0,
            'completed' => 0,
            'passed' => 0,
            'inprogress' => 0,
        );
    }

    protected function tearDown(): void
    {
        //  $this->example = null;
    }


    //Retrieve Aus parses and returns AUs from large statements from the CMI5 player
    //So to test, maybe make a statement and ensure the test value is returned? 
    //Arbitrarily pick a word and put in right place? See if it is returned?
    public function testRetrieveAus()
    {
    //It's not just returning it, it's splitting it into chuncks~!

        //Fake values to return
        $mockStatement = array(
            "createdAt" => "2023-06-26T18:36:15.000Z",
            "id"=> 000,
            "lmsId"=> "https://example",
            "metadata" => array( 
                "aus" => array  ( 
                    0 => array (
                        "activityType" => null,
                        "auIndex" => 0,
                        "description" => array ( 
                            "lang"=> "en-US",
                            "text"=> "Testing."
                        ),
                        "id"=> "https://au",
                        "launchMethod" => "AnyWindow",
                        "lmsId"  => "https://au/0",
                        "masteryScore" => null,
                        "moveOn"=> "CompletedOrPassed",
                        "objectives"=> null,
                        "parents"=> "",
                        "title"=> array (
                            "lang"=> "en-US",
                            "text" => "Introduction to Testing"
                        ),
                        "type" => "au",
                        "url" => "index.html?pages=1&complete=launch",
                    ),
                1 =>  array (
                    "activityType" => null,
                    "auIndex" => 1,
                    "description" => array(
                        "lang" => "en-US",
                        "text" => "Testing Testing."
                    ),
                    "id" => "https://example",
                    "launchMethod" => "AnyWindow",
                    "lmsId" => "https://example/au/1",
                    "masteryScore" => null,
                    "moveOn" => "CompletedOrPassed",
                    "objectives"=> null,
                    "parents" => "",
                    "title" => array(
                        "lang" => "en-US",
                        "text" => "Testing Materials"
                    ),
                    "type" => "au",
                    "url"=> "index.html?pages=2&complete=launch"
                    ),
                )
            )
        );
 
        //This is the value that should be returned, basically, an array holding all the aus separately
        $shouldBeReturned = array (
        //First au, nestled in array
        0 => array  ( 
            0 => array (
                "activityType" => null,
                "auIndex" => 0,
                "description" => array ( 
                    "lang"=> "en-US",
                    "text"=> "Testing."
                ),
                "id"=> "https://au",
                "launchMethod" => "AnyWindow",
                "lmsId"  => "https://au/0",
                "masteryScore" => null,
                "moveOn"=> "CompletedOrPassed",
                "objectives"=> null,
                "parents"=> "",
                "title"=> array (
                    "lang"=> "en-US",
                    "text" => "Introduction to Testing"
                ),
                "type" => "au",
                "url" => "index.html?pages=1&complete=launch",
            )
        ),
        //second au nestled in array    
        1 =>  array (
            0 => array (
                "activityType" => null,
                "auIndex" => 1,
                "description" => array(
                    "lang" => "en-US",
                    "text" => "Testing Testing."
                ),
                "id" => "https://example",
                "launchMethod" => "AnyWindow",
                "lmsId" => "https://example/au/1",
                "masteryScore" => null,
                "moveOn" => "CompletedOrPassed",
                "objectives"=> null,
                "parents" => "",
                "title" => array(
                    "lang" => "en-US",
                    "text" => "Testing Materials"
                ),
                "type" => "au",
                "url"=> "index.html?pages=2&complete=launch"
                ),
            )
        
        );

       $helper = new Au_Helpers();
        //So now with this fake 'statement', lets ensure it pulls the correct value which is "correct Retrieval"
        $retrieved = $helper->retrieveAus($mockStatement);

   
        //It should retrieve the mock aus
        $this->assertEquals($shouldBeReturned, $retrieved, "Expected retrieved statement to be equal to mock statement");
        //This is being flaged as risky?
        //Is there a different way to test this?
        //Maybe we 'expect' two properties since 2 aus  were passed in?
        //I mean we aren't testing 'chunked?' so....?

        //It DOES return as array
        $this->assertIsArray($retrieved, "Expected retrieved statement to be an array");
        //And it returns two in array? Since we passed in two?
        $this->assertCount(2, $retrieved, "Expected retrieved statement to have two aus");
        //TODO MB
        //Those seem to pass, so take away line 206?
    }

    //Test function that is fed an array of statments and returns an array of aus
    public function testCreateAus()
    {
        //Should be enough to pass the mock statement values here, make an array of them first
        $testStatements = array();
        
        //Lets create 4 aus statement
        for ($i = 0; $i < 4; $i++) {
            $testStatements[$i][] = $this->mockStatementValues;
        }
    
        $helper = new Au_Helpers();
        //So now with this fake 'statement', lets ensure it pulls the correct value which is "correct Retrieval"
        $auList = $helper->createAus($testStatements);
        
        //There should be a total of 4 Aus in this array
        $this->assertCount(4, $auList, "Expected retrieved statement to have four aus");
        //And they should all be au objects
        foreach ($auList as $au) {
            $this->assertInstanceOf(Au::class, $au, "Expected retrieved statement to be an array of aus");
        }
    }
    //To start toorrow
    //changed aushelpers backto have camel case in somecases. thats how it comes IN, so we just need to make a test
    //that takes that into account, not change for test!

    //Tests SaveAUs when one is passed. Saves to phpunit_test_db
    public function testSaveAusOneID()
    {  
        //Can we just make AUs and pass them in? 
        $helper = new Au_Helpers();
        //Lets create 1 au
        for ($i = 0; $i < 1; $i++) {
            $testStatements[$i][] = $this->mockStatementValues;
        }
        //Create array of AUs to send to saveAUs
        $auArray = $helper->createAus($testStatements);

        //Pass it to SaveAUs. SaveAUs returns an array of ids
        $auList = $helper->saveAUs($auArray);

        //It DOES return as array
        $this->assertIsArray($auList, "Expected retrieved statement to be an array");
        //And it returns one in array, since we passed in one?
        $this->assertCount(1, $auList, "Expected retrieved statement to have one id");
          
    }   

        //Tests SaveAUs when one is passed. Saves to phpunit_test_db
        public function testSaveAusMultipleID()
        {  
            //Can we just make AUs and pass them in? 
            $helper = new Au_Helpers();
            //Lets create 1 au
            for ($i = 0; $i < 4; $i++) {
                $testStatements[$i][] = $this->mockStatementValues;
            }
            //Create array of AUs to send to saveAUs
            $auArray = $helper->createAus($testStatements);
    
            //Pass it to SaveAUs. SaveAUs returns an array of ids
            $auList = $helper->saveAUs($auArray);
    
            //It DOES return as array
            $this->assertIsArray($auList, "Expected retrieved statement to be an array");
            //And it returns 4 in array, since we passed in 4?
            $this->assertCount(4, $auList, "Expected retrieved statement to have four ids");
              
        }  
}