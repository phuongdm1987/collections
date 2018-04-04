<?php

declare(strict_types=1);

namespace CCT\Component\Collections;

use CCT\Component\Collections\Interactors\ArrayAggregator;
use CCT\Component\Collections\Interactors\ArrayInspector;
use CCT\Component\Collections\Interactors\ArraySegregator;
use CCT\Component\Collections\Interactors\ArraySorter;
use CCT\Component\Collections\Interactors\InteractorInterface;

class CollectionProxy
{
    /**
     * @var CollectionInterface
     */
    protected $collection;

    /**
     * @var object
     */
    protected $proxy;

    /**
     * @var array
     */
    public static $proxies = [
        'sorter' => ArraySorter::class,
        'inspector' => ArrayInspector::class,
        'aggregator' => ArrayAggregator::class,
        'segregator' => ArraySegregator::class,
    ];

    /**
     * ArrayProxy constructor.
     *
     * @param CollectionInterface   $collection
     * @param string                $proxy
     */
    public function __construct(CollectionInterface $collection, string $proxy)
    {
        $this->collection = $collection;
        $this->proxy = new static::$proxies[$proxy]($collection->all());
    }

    /**
     * Proxy a method call onto the specified Interactor.
     *
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $collectionClass = get_class($this->collection);
        $result = $this->proxy->{$method}(...$parameters);

        if (is_array($result)) {
            return new $collectionClass($result);
        }

        if ($result instanceof InteractorInterface || null === $result) {
            return new $collectionClass($this->proxy->all());
        }

        return $result;
    }
}
