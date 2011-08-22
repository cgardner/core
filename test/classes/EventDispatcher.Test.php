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
require_once 'classes/EventDispatcher.class.php';

/**
 * EventDispatcher Test Class
 * @package Cumula
 * @subpackage Core
 **/
class Test_EventDispatcher extends Test_BaseTest {
    /**
     * Store the number of calls to a method
     * @var integer
     */
    private $calls = 0;

    /**
     * setUp
     * @param void
     * @return void
     **/
    public function setUp() {
        $this->eventDispatcher = new EventDispatcherClass();
    } // end function setUp

    /**
     * Test the class Constructor
     * @param void
     * @return void
     * @group all
     * @covers Cumula\EventDispatcher::__construct
     **/
    public function testConstructor() {
        $dispatcher  = new EventDispatcherClass();

        // Make sure the EVENTDISPATCHER_EVENT_DISPATCHED event was registered
        // in the constructor
        $dispatcher->addEventListener(EVENTDISPATCHER_EVENT_DISPATCHED, array($this, 'eventDispatched'));
    } // end function testConstructor

    /**
     * Make sure an Exception is thrown when an event is added multiple times
     * @param void
     * @return void
     * @group all
     * @covers Cumula\EventDispatcher::addEvent
     * @expectedException Exception
     **/
    public function testAddEventException() {
        // Should throw an exception because the event was added
        // in the constructor already
        $this->eventDispatcher->addEvent(EVENTDISPATCHER_EVENT_DISPATCHED);
    } // end function testAddEventException

    /**
     * Test the addEvent and removeEvent methods
     * @param void
     * @return void
     * @group all
     * @covers Cumula\EventDispatcher::addEvent
     * @covers Cumula\EventDispatcher::removeEvent
     * @covers Cumula\EventDispatcher::eventExists
     **/
    public function testEventCreationAndRemoval() {
        $eventId = uniqid('event_');

        $this->eventDispatcher->addEvent($eventId);
        $this->assertTrue($this->eventDispatcher->eventExists($eventId));

        $this->eventDispatcher->removeEvent($eventId);
        $this->assertFalse($this->eventDispatcher->eventExists($eventId));
    } // end function testEventCreationAndRemoval


    /**
     * Test the static getInstance method
     * @param void
     * @return void
     * @group all
     * @covers Cumula\EventDispatcher::getInstance
     * @covers Cumula\EventDispatcher::setInstance
     **/
    public function testStaticGetInstance() {
        $eventId = uniqid('event_');
        $this->eventDispatcher->addEvent($eventId);
        $this->eventDispatcher->addEventListener($eventId, array($this, 'eventDispatched'));

        $instance = EventDispatcherClass::getInstance();
        $this->assertEquals($this->eventDispatcher, $instance);
    } // end function testStaticGetInstance

    /**
     * test the addEventListener and dispatch methods
     * @param void
     * @return void
     * @group all
     * @covers Cumula\EventDispatcher::addEventListener
     * @covers Cumula\EventDispatcher::dispatch
     **/
    public function testEventListenerAndDispatch() {
        $eventId = uniqid('event_');

        $this->eventDispatcher->addEvent($eventId);
        $this->eventDispatcher->addEventListener($eventId, array($this, 'eventDispatched'));    

        $this->assertTrue($this->eventDispatcher->dispatch($eventId));
        $this->assertFalse($this->eventDispatcher->dispatch(uniqid('event2_')));

        // Make sure the callback was called
        $this->assertEquals(1, $this->calls);
    } // end function testEventListenerAndDispatch

    /**
     * Test the removeEventListener method
     * @param void
     * @return void
     * @group all
     * @covers Cumula\EventDispatcher::removeEventListener
     **/
    public function testRemoveEventListener() {
        $eventId = uniqid('event_');
        $callback = array($this, 'eventDispatched');

        $this->eventDispatcher->addEvent($eventId);
        $this->eventDispatcher->addEventListener($eventId, $callback);

        // Dispatch the event and make sure it fired correctly
        $this->eventDispatcher->dispatch($eventId);
        $this->assertEquals(1, $this->calls);

        // Remove the event and dispatch the event again
        $this->eventDispatcher->removeEventListener($eventId, $callback);
        $this->eventDispatcher->dispatch($eventId);
        $this->assertEquals(1, $this->calls);
    } // end function testRemoveEventListener

    /**
     * Test the addEventListenerTo method
     * @param void
     * @return void
     * @group all
     * @covers Cumula\EventDispatcher::addEventListenerTo
     **/
    public function testAddEventListenerTo() {
        $eventId = uniqid('event_');

        $this->eventDispatcher->addEvent($eventId);

        $dispatcher = new EventDispatcherClass2();
        $dispatcher->addEventListenerTo('EventDispatcherClass', $eventId, array($this, 'eventDispatched'));

        $this->eventDispatcher->dispatch($eventId);
        $this->assertEquals(1, $this->calls);
    }

    /**
     * Test the addEventListenerTo method with a string callback
     * @param void
     * @return void
     * @group all
     * @covers Cumula\EventDispatcher::addEventListenerTo
     */
    public function testAddEventListenerToWithStringCallback() {
        $dispatcher = new EventDispatcherClass2();

        $eventId2 = uniqid('event2_');
        $this->eventDispatcher->addEvent($eventId2);
        $dispatcher->addEventListenerTo('EventDispatcherClass', $eventId2, 'eventCallback');
        $this->eventDispatcher->dispatch($eventId2);
        $this->assertEquals(1, $dispatcher->calls);
    } // end function testAddEventListenerTo

    /**
     * test the addEventListenerTo method on an uninstantiated class
     * @param void
     * @return void
     * @group all
     * @covers Cumula\EventDispatcher::addEventListenerTo
     * @expectedException Cumula\EventException
     **/
    public function testAddEventListenerToUninstantiatedClass() {
        $eventId = uniqid('event_');
        
        $this->eventDispatcher->addEventListenerTo('EventDispatcherClass4', $eventId, array($this, 'eventDispatched'));
        $this->fail('Expected an EventException');
    } // end function testAddEventListenerToUninstantiatedClass

    /**
     * HELPER METHODS |helpers
     */
    /**
     * Callback to test the EVENT_DISPATCHER_EVENT_DISPATCHED event
     * @param void
     * @return void
     * @author Craig Gardner <craig@seabourneconsulting.com>
     **/
    public function eventDispatched() {
        $this->calls++;
    } // end function eventDispatched
} // end class Test_EventDispatcher extends Test_BaseTest

class EventDispatcherClass extends Cumula\EventDispatcher {}

class EventDispatcherClass2 extends Cumula\EventDispatcher {
    public $calls = 0;
    
    public function eventCallback() {
        $this->calls++;
    }
}

class EventDispatcherClass3 extends Cumula\EventDispatcher {}
