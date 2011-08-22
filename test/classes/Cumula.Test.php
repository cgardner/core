<?php

require_once 'base/Test.php';
require_once 'classes/Cumula.class.php';

/**
 * Cumula Class Test Class
 * @package Cumula
 * @runTestsInSeparateProcesses
 **/
class Test_Cumula extends Test_BaseTest {
    /**
     * Test the setInstance method of the Cumula Utility class
     * @param void
     * @return void
     * @group all
     * @covers Cumula\Cumula::setInstance
     **/
    public function testSetInstance() {
        $application = $this->getMock('EventDispatcher');

        $this->assertTrue(Cumula::setInstance('EventDispatcher', $application)); 
        $this->assertFalse(Cumula::setInstance('SomeOtherClass', $application));
    } // end function testSetInstance

    /**
     * Test the getInstance method of the Cumula Utility Class
     * @param void
     * @return void
     * @group all
     * @covers Cumula\Cumula::getInstance
     **/
    public function testGetInstance() {
        $application = $this->getMock('EventDispatcher');
        Cumula::setInstance('EventDispatcher', $application);

        // Make sure the method returns what we expect
        $this->assertEquals($application, Cumula::getInstance('EventDispatcher'));
        $this->assertFalse(Cumula::getInstance('SomeOtherClass'));

        // Make sure a reference to the instance is stored rather than just a copy
        $application->myNewVariable = uniqid();
        $app2 = Cumula::getInstance('EventDispatcher');
        $this->assertEquals($application->myNewVariable, $app2->myNewVariable);
    } // end function testGetInstance
} // end class Test_Cumula extends Test_BaseTest
