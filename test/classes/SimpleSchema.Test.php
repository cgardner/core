<?php

require_once 'base/Test.php';
require_once 'classes/SimpleSchema.class.php';

/**
 * Test of the SimpleSchema Class
 * @package Cumula
 * @subpackage Core
 **/
class Test_SimpleSchema extends Test_BaseTest {
    /**
     * Store the Schema object
     * @var SimpleSchema
     */
    private $schema;

    /**
     * Name used in the setUp of the SimpleSchema object
     * @var string
     */
    private $name;

    /**
     * setUp
     * @param void
     * @return void
     **/
    public function setUp() {
        $this->name = uniqid();
        $this->schema = new SimpleSchema($this->name);
    } // end function setUp

    /**
     * Test the Constructor of the SimpleSchema class
     * @param void
     * @return void
     * @group all
     * @covers SimpleSchema::__construct
     **/
    public function testConstructor() {
        $this->assertEquals($this->schema->getName(), $this->name);
        $this->assertNull($this->schema->getIdField());
        $this->assertNull($this->schema->getFields());
    } // end function testConstructor

    /**
     * Test the get and set methods for the name variable
     * @param void
     * @return void
     * @group all
     * @covers SimpleSchema::getName
     * @covers SimpleSchema::setName
     **/
    public function testGetName() {
        $name = uniqid('name_');
        $this->assertNull($this->schema->setName($name));
        $this->assertEquals($name, $this->schema->getName());
    } // end function testGetName

    /**
     * Test the get and set methods for the fields variable
     * @param void
     * @return void
     * @group all
     * @covers SimpleSchema::getFields
     * @covers SimpleSchema::setFields
     **/
    public function testGetFields() {
        $fields = uniqid('fields_');
        $this->assertNull($this->schema->setFields($fields));
        $this->assertEquals($fields, $this->schema->getFields());
    } // end function testGetFields

    /**
     * Test the get and set IdField methods
     * @param void
     * @return void
     * @group all
     * @covers SimpleSchema::getIdField
     * @covers SimpleSchema::setIdField
     **/
    public function testGetIdField() {
        $idField = uniqid('id_field_');
        $this->assertNull($this->schema->setIdField($idField));
        $this->assertEquals($idField, $this->schema->getIdField());
    } // end function testGetIdField
} // end class Test_SimpleSchema extends Test_BaseTest
