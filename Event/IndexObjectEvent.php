<?php

namespace FlexModel\FlexModelElasticsearchBundle\Event;

use FlexModel\FlexModelElasticsearchBundle\Elasticsearch\Model\IndexableObjectInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * The container for creating a index document body from an object.
 *
 * @author Niels Nijens <niels@connectholland.nl>
 */
class IndexObjectEvent extends Event
{
    /**
     * The object being indexed.
     *
     * @var IndexableObjectInterface
     */
    private $object;

    /**
     * The body for index document.
     *
     * @var array
     */
    private $body = array();

    /**
     * Constructs a new IndexObjectEvent.
     *
     * @param IndexableObjectInterface $object
     */
    public function __construct(IndexableObjectInterface $object)
    {
        $this->object = $object;
    }

    /**
     * Returns true if the body for the index document has content.
     *
     * @return bool
     */
    public function hasBodyContent()
    {
        return empty($this->body) === false;
    }

    /**
     * Returns the object being indexed.
     *
     * @return IndexableObjectInterface
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Returns the body for the index document.
     *
     * @return array
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Sets key to the body for the index document.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        $this->body[$key] = $value;
    }

    /**
     * Sets the body for the index document.
     *
     * @param array $body
     */
    public function setBody(array $body)
    {
        $this->body = $body;
    }
}
