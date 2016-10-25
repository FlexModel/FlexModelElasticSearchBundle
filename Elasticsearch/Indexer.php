<?php

namespace FlexModel\FlexModelElasticsearchBundle\Elasticsearch;

use Elasticsearch\Client;
use FlexModel\FlexModelElasticsearchBundle\Elasticsearch\Model\IndexableObjectInterface;
use FlexModel\FlexModelElasticsearchBundle\Event\IndexObjectEvent;
use FlexModel\FlexModelElasticsearchBundle\FlexModelElasticsearchEvents;
use ReflectionClass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Handles indexing of objects to Elasticsearch through the Elasticsearch client.
 *
 * @author Niels Nijens <niels@connectholland.nl>
 */
class Indexer
{
    /**
     * The Elasticsearch client instance.
     *
     * @var Client
     */
    private $client;

    /**
     * The name of the Elasticsearch index.
     *
     * @var string
     */
    private $indexName;

    /**
     * The event dispatcher instance.
     *
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * Constructs a new Indexer instance.
     *
     * @param Client                   $client
     * @param string                   $indexName
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(Client $client, $indexName, EventDispatcherInterface $eventDispatcher)
    {
        $this->client = $client;
        $this->indexName = $indexName;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Adds an object to the Elasticsearch index.
     *
     * @param IndexableObjectInterface $object
     */
    public function indexObject(IndexableObjectInterface $object)
    {
        $reflectionClass = new ReflectionClass($object);
        $objectName = $reflectionClass->getShortName();

        $parameters = array(
            'index' => $this->indexName,
            'type' => $objectName,
            'id' => $object->getId(),
            'body' => array(),
        );

        $event = new IndexObjectEvent($object);

        $this->eventDispatcher->dispatch(FlexModelElasticsearchEvents::INDEX_OBJECT, $event);

        $parameters['body'] = $event->getBody();

        $this->client->index($parameters);
    }
}
