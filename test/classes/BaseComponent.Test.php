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
require_once 'vfsStream/vfsStream.php';
require_once 'classes/BaseComponent.class.php';

/**
 * Tests for the BaseComponent Class
 * @package Cumula
 * @author Seabourne Consulting
 */
class Test_BaseComponent extends Test_BaseTest {

    /**
     * setUp
     * @param void
     * @return void
     **/
    public function setUp() {
        defined('ROOT') || 
            define('ROOT', dirname(BASE_DIR));
        vfsStream::setup('componentTest');
    } // end function setUp

    /**
     * Test the constructor
     * @param void
     * @return void
     * @author Seabourne Consulting
     * @group all
     * @covers BaseComponent::__construct
     **/
    public function testConstructor() {
        $mock = new TestBaseComponent();

        $this->assertObjectHasAttribute('config', $mock);
        $this->assertObjectNotHasAttribute(uniqid(), $mock);
    } // end function testConstructor

    /**
     * Test the _registerEvents method
     * @param void
     * @return void
     * @group all
     * @covers BaseComponent::_registerEvents
     **/
    public function testRegisterEvents() {
        // Setup a fake events file
        $constName = uniqid('CONSTANT');
        $constValue = uniqid('VALUE');
        $phpString = "<?php\nconst {$constName} = '{$constValue}';";
        file_put_contents(vfsStream::url('componentTest/events.inc'), $phpString);

        $prevConsts = get_defined_constants(TRUE);
        $prevConsts = $prevConsts['user'];

        $baseComponent = new TestBaseComponent();

        $newConsts = get_defined_constants(TRUE);
        $constants = array_diff_assoc($newConsts['user'], $prevConsts);


        $this->assertArrayHasKey($constName, $constants);
        $this->assertEquals($constValue, $constants[$constName]);

        // Let's not forget why we did all of this work.
        $this->assertTrue($baseComponent->eventExists($constValue));
    } // end function testRegisterEvents
}

class TestBaseComponent extends BaseComponent {
    public function rootDirectory() {
        return vfsStream::url('componentTest');
    }
}
