<?php

namespace FlexModel\FlexModelElasticsearchBundle;

/**
 * Contains all the events fired by this bundle.
 *
 * @author Niels Nijens <niels@connectholland.nl>
 */
final class FlexModelElasticsearchEvents
{
    /**
     * The INDEX_OBJECT event is dispatched by the Indexer to gather data for the index document body.
     *
     * The event listener receives an
     * FlexModel\FlexModelElasticsearchBundle\Event\IndexObjectEvent
     *
     * @var string
     */
    const INDEX_OBJECT = 'flexmodel.elasticsearch.index_object';
}
