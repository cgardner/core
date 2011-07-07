<?php

/**
 * Cumula
 *
 * Cumula - Framework for the cloud.
 *
 * @package     Cumula
 * @version     0.1.0
 * @author      Seabourne Consulting
 * @license     MIT LIcense
 * @copyrigt    2011 Seabourne Consulting
 * @link        http://cumula.org
 */

require_once 'base/Test.php';
require_once 'classes/Application.class.php';

/**
 * Tests for the Application Class
 * @package Cumula
 * @author Seabourne Consulting
 **/
class Test_Application extends Test_BaseTest {

    private $calls = 0;

    /**
     * Test the Constructor
     * @param void
     * @return void
     * @group all
     * @covers Application::__construct
     * @covers Application::_setupConstants
     * @covers Application::_setupBootstrap
     * @runInSeparateProcess
     * @dataProvider constructorDataProvider
     **/
    public function testConstructor($paths = NULL) {

        if (!is_null($paths)) {
            // The following paths don't get created in the Application class
            if (!file_exists($paths['core_path'])) mkdir($paths['core_path']);
            if (!file_exists($paths['core_component_path'])) mkdir($paths['core_component_path']);
            if (!file_exists($paths['template_path'])) mkdir($paths['template_path']);
            $this->files += $paths;
        }
        
        global $callbackExecuted;
        $callbackExecuted = FALSE;

        $application = new Application(function() {
            global $callbackExecuted;   
            $callbackExecuted = TRUE;
        }, $paths);

        $this->assertTrue($callbackExecuted, 'Callback was not executed during Application constructor');

        $checkConstants = array(
            'ROOT',
            'APPROOT',
            'COMPROOT',
            'CONTRIBCOMPROOT',
            'CONFIGROOT',
            'DATAROOT',
            'TEMPLATEROOT',
        );

        // Make sure the constants are all set
        foreach ($checkConstants as $const) {
            $constVal = constant($const);
            // Make sure the constant is longer than "/" and a file that exists
            $this->assertGreaterThan(2, strlen($constVal), sprintf('Make sure the value of %s is greater than 2', $const));
            $this->assertFileExists($constVal);
        }

        if (!is_null($paths)) {
            foreach ($paths as $name => $path) {
                $this->files[$name] = ROOT . DIRECTORY_SEPARATOR . $path;
            }
        }
    } // end function testConstructor

    /**
     * Test the boot method
     * @param void
     * @return void
     * @group all
     * @covers Application::boot
     **/
    public function testBoot() {
        $application = new Application(); 

        foreach ($application->bootProcess as $bootEvent) {
            $application->addEventListener($bootEvent, array($this, 'applicationCallback'));
        }

        $application->boot();
        $this->assertEquals(count($application->bootProcess), $this->calls);
    } // end function testBoot

    /**
     * Testing the magic __callStatic method
     * @param void
     * @return void
     * @group all
     * @covers Application::__callStatic
     **/
    public function testCallStatic() {
        // Need to instantiate the class before we can use the magic method
        // @TODO Make Application::__callStatic instantiate an object if it's not instantiated already
        $null = new ApplicationTestEvent();

        $this->assertInstanceOf('ApplicationTestEvent', Application::getApplicationTestEvent());

        $this->assertFalse(Application::getClassDoesNotExist());
    } // end function testCallStatic

    public function constructorDataProvider() {
        return array(
            'Custom Paths' => array(array(
                'core_path' => '/tmp/core',
                'core_component_path' => '/tmp/core_component_path',
                'contrib_component_path' => '/tmp/contrib_component_path',
                'config_path' => '/tmp/config_path',
                'data_path' => '/tmp/data_path',
                'template_path' => '/tmp/template_path',
            )),
            'No Paths' => array(NULL),
        );
    }

    /**
     * Application Callback method
     * @param void
     * @return void
     **/
    public function applicationCallback() {
        $this->calls++;
    } // end function applicationCallback
} // end class Test_Application extends Test_BaseTest

class ApplicationTestEvent extends EventDispatcher {}
