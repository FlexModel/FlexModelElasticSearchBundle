<?php

namespace FlexModel\FlexModelElasticsearchBundle\Tests\Event;

use FlexModel\FlexModelElasticsearchBundle\Elasticsearch\Model\IndexableObjectInterface;
use FlexModel\FlexModelElasticsearchBundle\Event\IndexObjectEvent;
use PHPUnit_Framework_TestCase;

/**
 * IndexObjectEventTest.
 *
 * @author Niels Nijens <niels@connectholland.nl>
 */
class IndexObjectEventTest extends PHPUnit_Framework_TestCase
{
    /**
     * The mock instance of an IndexableObjectInterface.
     *
     * @var IndexableObjectInterface
     */
    private $objectMock;

    /**
     * The IndexObjectEvent instance for testing.
     *
     * @var IndexObjectEvent
     */
    private $event;

    /**
     * Constructs the IndexObjectEvent and mock.
     */
    public function setUp()
    {
        $this->objectMock = $this->getMockBuilder(IndexableObjectInterface::class)
            ->getMock();

        $this->event = new IndexObjectEvent($this->objectMock);
    }

    /**
     * Tests if constructing a new IndexObjectEvent sets the instance properties.
     */
    public function testConstruct()
    {
        $this->assertAttributeSame($this->objectMock, 'object', $this->event);
    }

    /**
     * Tests if IndexObjectEvent::setBody sets the instance property.
     */
    public function testSetBody()
    {
        $this->event->setBody(array('foo' => 'bar'));

        $this->assertAttributeSame(array('foo' => 'bar'), 'body', $this->event);
    }

    /**
     * Tests if IndexObjectEvent::set sets a array key of the body instance property.
     */
    public function testSet()
    {
        $this->event->set('foo', 'bar');

        $this->assertAttributeSame(array('foo' => 'bar'), 'body', $this->event);
    }

    /**
     * Tests if IndexObjectEvent::hasBodyContent returns false when empty.
     */
    public function testHasBodyContentReturnsFalse()
    {
        $this->assertFalse($this->event->hasBodyContent());
    }

    /**
     * Tests if IndexObjectEvent::hasBodyContent returns true when the body contains content.
     *
     * @depends testSet
     */
    public function testHasBodyContentReturnsTrue()
    {
        $this->event->set('foo', 'bar');

        $this->assertTrue($this->event->hasBodyContent());
    }

    /**
     * Tests if IndexObjectEvent::getObject returns the IndexableObjectInterface object set during event construction.
     */
    public function testGetObject()
    {
        $this->assertSame($this->objectMock, $this->event->getObject());
    }

    /**
     * Tests if IndexObjectEvent::getBody returns an empty array.
     */
    public function testGetBody()
    {
        $this->assertInternalType('array', $this->event->getBody());
        $this->assertEmpty($this->event->getBody());
    }

    /**
     * Tests if IndexObjectEvent::getBody returns the array with content.
     *
     * @depends testSet
     */
    public function testGetBodyWithContent()
    {
        $this->event->set('foo', 'bar');

        $this->assertSame(array('foo' => 'bar'), $this->event->getBody());
    }
}
