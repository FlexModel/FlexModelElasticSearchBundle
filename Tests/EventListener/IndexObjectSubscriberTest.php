<?php

namespace FlexModel\FlexModelElasticsearchBundle\Tests\EventListener;

use FlexModel\FlexModel;
use FlexModel\FlexModelElasticsearchBundle\Event\IndexObjectEvent;
use FlexModel\FlexModelElasticsearchBundle\EventListener\IndexObjectSubscriber;
use FlexModel\FlexModelElasticsearchBundle\FlexModelElasticsearchEvents;
use FlexModel\FlexModelElasticsearchBundle\Tests\IndexObject;
use PHPUnit_Framework_TestCase;

/**
 * IndexObjectSubscriberTest.
 *
 * @author Niels Nijens <niels@connectholland.nl>
 */
class IndexObjectSubscriberTest extends PHPUnit_Framework_TestCase
{
    /**
     * The IndexObjectSubscriber instance for testing.
     *
     * @var IndexObjectSubscriber
     */
    private $subscriber;

    /**
     * The mock instance of the FlexModel configuration.
     *
     * @var FlexModel
     */
    private $flexModelMock;

    /**
     * Constructs the IndexObjectSubscriber and mock.
     */
    public function setUp()
    {
        $this->flexModelMock = $this->getMockBuilder(FlexModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->subscriber = new IndexObjectSubscriber($this->flexModelMock);
    }

    /**
     * Tests if IndexObjectSubscriber::getSubscribedEvents returns an array with a FlexModelElasticsearchEvents::INDEX_OBJECT key.
     */
    public function testGetSubscribedEvents()
    {
        $this->assertInternalType('array', IndexObjectSubscriber::getSubscribedEvents());
        $this->assertArrayHasKey(FlexModelElasticsearchEvents::INDEX_OBJECT, IndexObjectSubscriber::getSubscribedEvents());
    }

    /**
     * Tests if constructing a new IndexObjectSubscriber instance sets the instance properties.
     */
    public function testConstruct()
    {
        $this->assertAttributeSame($this->flexModelMock, 'flexModel', $this->subscriber);
    }

    /**
     * Tests if IndexObjectSubscriber::onIndexObject skips further execution when the event already has body content.
     */
    public function testOnIndexObjectSkipsOnBodyContent()
    {
        $eventMock = $this->getMockBuilder(IndexObjectEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock->expects($this->once())
            ->method('hasBodyContent')
            ->willReturn(true);
        $eventMock->expects($this->never())
            ->method('getObject');

        $this->flexModelMock->expects($this->never())
            ->method('getFieldNamesByView');

        $this->subscriber->onIndexObject($eventMock);
    }

    /**
     * Tests if IndexObjectSubscriber::onIndexObject reads the FlexModel 'searchindex' view and retrieves the data from the object.
     */
    public function testOnIndexObject()
    {
        $this->flexModelMock->expects($this->once())
            ->method('getFieldNamesByView')
            ->willReturn(array('foo'));

        $testObject = new IndexObject();

        $eventMock = $this->getMockBuilder(IndexObjectEvent::class)
            ->disableOriginalConstructor()
            ->getMock();
        $eventMock->expects($this->once())
            ->method('hasBodyContent')
            ->willReturn(false);
        $eventMock->expects($this->once())
            ->method('getObject')
            ->willReturn($testObject);
        $eventMock->expects($this->once())
            ->method('setBody')
            ->with($this->equalTo(array('foo' => 'bar')));

        $this->subscriber->onIndexObject($eventMock);
    }
}
