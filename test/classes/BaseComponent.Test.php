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
     * Store the component variable
     * @var BaseComponent
     */
    private $component;

    /**
     * setUp
     * @param void
     * @return void
     **/
    public function setUp() {
        defined('ROOT') || 
            define('ROOT', dirname(BASE_DIR));
        vfsStream::setup('componentTest');

        $this->component = new TestBaseComponent();
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
        $this->assertObjectHasAttribute('config', $this->component);
        $this->assertObjectNotHasAttribute(uniqid(), $this->component);
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

        // Must re-instantiate this class for this test
        $baseComponent = new TestBaseComponent();

        $newConsts = get_defined_constants(TRUE);
        $constants = array_diff_assoc($newConsts['user'], $prevConsts);


        $this->assertArrayHasKey($constName, $constants);
        $this->assertEquals($constValue, $constants[$constName]);

        // Let's not forget why we did all of this work.
        $this->assertTrue($baseComponent->eventExists($constValue));
    } // end function testRegisterEvents

    /**
     * Test the renderPartial
     * @param void
     * @return void
     * @group all
     * @covers BaseComponent::renderPartial
     **/
    public function testRenderPartial() {
        $value = uniqid('value_');

        $templateFile = $this->createTemplate($value);
        
        $output = $this->component->renderPartial($templateFile);

        $this->assertEquals($output, $value);
    } // end function testRenderPartial

    /**
     * Test the renderContent method
     * @param void
     * @return void
     * @group all
     * @covers BaseComponent::renderContent
     **/
    public function testRenderContent() {
        $this->markTestSkipped('Unable to test this method. It relies on Application::getResponse which is not able to be mocked or overloaded.');
        $value = uniqid('value_');

        $templateFile = $this->createTemplate($value);
        $this->component->render($templateFile);

        $mockResponse = $this->getMock('Response');
        $this->assertTrue(($mockResponse instanceOf Response) === TRUE);
        $response = Application::getResponse();
    } // end function testRenderContent

    /**
     * Create a template and store the contents
     * @param string $contents
     * @return string
     * @author Craig Gardner <craig@seabourneconsulting.com>
     **/
    public function createTemplate($contents, $fileName = NULL) {
        if (is_null($fileName)) {
            $fileName = 'componentTest/view.tpl.php';
        }

        $fileUrl = vfsStream::url($fileName);

        if (file_put_contents($fileUrl, $contents) === FALSE) {
            $this->fail(sprintf('Failed to write to %s', $fileName));
        }
        return $fileUrl;
    } // end function createTemplate
    
}

class TestBaseComponent extends BaseComponent {
    public function rootDirectory() {
        return vfsStream::url('componentTest');
    }
}
