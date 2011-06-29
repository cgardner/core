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
            mkdir($paths['core_path']);
            mkdir($paths['core_component_path']);
            mkdir($paths['template_path']);
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

            // Make sure the file is deleted after the tests are run
            $this->files[] = $constVal;
        }
    } // end function testConstructor


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
} // end class Test_Application extends Test_BaseTest
