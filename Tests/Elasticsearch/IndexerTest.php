<?php

namespace FlexModel\FlexModelElasticsearchBundle\Tests\Elasticsearch;

use Elasticsearch\Client;
use FlexModel\FlexModelElasticsearchBundle\Elasticsearch\Indexer;
use FlexModel\FlexModelElasticsearchBundle\Elasticsearch\Model\IndexableObjectInterface;
use FlexModel\FlexModelElasticsearchBundle\Event\IndexObjectEvent;
use FlexModel\FlexModelElasticsearchBundle\FlexModelElasticsearchEvents;
use PHPUnit_Framework_TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * IndexerTest.
 *
 * @author Niels Nijens <niels@connectholland.nl>
 */
class IndexerTest extends PHPUnit_Framework_TestCase
{
    /**
     * The Indexer instance for testing.
     *
     * @var Indexer
     */
    private $indexer;

    /**
     * The mock instance of the Elasticsearch client.
     *
     * @var Client
     */
    private $clientMock;

    /**
     * The mock instance of the EventDispatcherInterface.
     *
     * @var EventDispatcherInterface
     */
    private $eventDispatcherMock;

    /**
     * Constructs the IndexObjectEvent and mocks.
     */
    public function setUp()
    {
        $this->clientMock = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventDispatcherMock = $this->getMockBuilder(EventDispatcherInterface::class)
            ->getMock();

        $this->indexer = new Indexer($this->clientMock, 'test_index', $this->eventDispatcherMock);
    }

    /**
     * Tests if constructing a new Indexer sets the instance properties.
     */
    public function testConstruct()
    {
        $this->assertAttributeSame($this->clientMock, 'client', $this->indexer);
        $this->assertAttributeSame('test_index', 'indexName', $this->indexer);
        $this->assertAttributeSame($this->eventDispatcherMock, 'eventDispatcher', $this->indexer);
    }

    /**
     * Tests if Indexer::indexObject dispatches FlexModelElasticsearchEvents::INDEX_OBJECT event and calls the Elasticsearch client.
     */
    public function testIndexObject()
    {
        $this->eventDispatcherMock->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->equalTo(FlexModelElasticsearchEvents::INDEX_OBJECT),
                $this->callback(
                    function($event) {
                        return $event instanceof IndexObjectEvent;
                    }
                )
            );

        $this->clientMock->expects($this->once())
            ->method('index')
            ->with(
                $this->equalTo(
                    array(
                        'index' => 'test_index',
                        'type' => 'ObjectMock',
                        'id' => 1,
                        'body' => array(),
                    )
                )
            );

        $objectMock = $this->getMockBuilder(IndexableObjectInterface::class)
            ->setMockClassName('ObjectMock')
            ->getMock();
        $objectMock->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $this->indexer->indexObject($objectMock);
    }
}
