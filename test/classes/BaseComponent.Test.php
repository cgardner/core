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
        define('ROOT', dirname(BASE_DIR));
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
        $testComponent = new TestComponent();
    } // end function testConstructor
}

class TestComponent extends BaseComponent {

}
