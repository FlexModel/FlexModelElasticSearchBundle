<?php

namespace FlexModel\FlexModelElasticsearchBundle\Tests;

use FlexModel\FlexModelElasticsearchBundle\Elasticsearch\Model\IndexableObjectInterface;

/**
 * IndexObject.
 *
 * @author Niels Nijens <niels@connectholland.nl>
 */
class IndexObject implements IndexableObjectInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 1;
    }

    /**
     * Returns "bar".
     *
     * @return string
     */
    public function getFoo()
    {
        return 'bar';
    }
}
