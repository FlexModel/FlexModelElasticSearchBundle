<?php

namespace FlexModel\FlexModelElasticsearchBundle\EventListener;

use FlexModel\FlexModel;
use FlexModel\FlexModelElasticsearchBundle\Event\IndexObjectEvent;
use FlexModel\FlexModelElasticsearchBundle\FlexModelElasticsearchEvents;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * The event subscriber to add the index document body based on the searchindex view in FlexModel.
 *
 * @author Niels Nijens <niels@connectholland.nl>
 */
class IndexObjectSubscriber implements EventSubscriberInterface
{
    /**
     * The FlexModel instance.
     *
     * @var FlexModel
     */
    private $flexModel;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FlexModelElasticsearchEvents::INDEX_OBJECT => array(
                array('onIndexObject', 10),
            ),
        );
    }

    /**
     * Constructs a new IndexObjectSubscriber.
     *
     * @param FlexModel $flexModel
     */
    public function __construct(FlexModel $flexModel)
    {
        $this->flexModel = $flexModel;
    }

    /**
     * Reads the fields from the FlexModel configuration and adds the values of the fields to the index document body.
     *
     * @param IndexObjectEvent $event
     */
    public function onIndexObject(IndexObjectEvent $event)
    {
        if ($event->hasBodyContent()) {
            return;
        }

        $object = $event->getObject();

        $reflectionClass = new ReflectionClass($object);
        $objectName = $reflectionClass->getShortName();

        $body = array();

        $fieldNames = $this->flexModel->getFieldNamesByView($objectName, 'searchindex');
        foreach ($fieldNames as $fieldName) {
            $getter = 'get'.Container::camelize($fieldName);

            $body[$fieldName] = $object->$getter();
        }

        $event->setBody($body);
    }
}
